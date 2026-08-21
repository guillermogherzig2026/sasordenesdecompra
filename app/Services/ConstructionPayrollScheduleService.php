<?php

namespace App\Services;

use App\Models\ConstructionPaymentOrder;
use App\Models\ConstructionPayroll;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class ConstructionPayrollScheduleService
{
    public const TIMEZONE = 'America/Mexico_City';

    public const RECURRING_PERIODICITIES = [
        'Semanal',
        'Quincenal',
    ];

    public const ACTIVE_STATUSES = [
        'Programada',
        'En revision',
        'Aprobada',
    ];

    public function synchronize(ConstructionPayroll $payroll): ConstructionPaymentOrder
    {
        return DB::transaction(function () use ($payroll): ConstructionPaymentOrder {
            $payroll = ConstructionPayroll::query()
                ->whereKey($payroll->id)
                ->lockForUpdate()
                ->firstOrFail();

            return match ($payroll->periodicity) {
                'Semanal' => $this->synchronizeWeekly($payroll),
                'Quincenal' => $this->synchronizeBiweekly($payroll),
                default => $this->synchronizeSingleOrder($payroll),
            };
        });
    }

    public function generateDueOccurrences(CarbonInterface|string|null $asOf = null): int
    {
        return $this->generateWeeklyDueOccurrences($asOf)
            + $this->generateBiweeklyDueOccurrences($asOf);
    }

    public function generateWeeklyDueOccurrences(CarbonInterface|string|null $asOf = null): int
    {
        $latestSunday = $this->previousOrSameSunday($this->date($asOf));
        $created = 0;

        ConstructionPayroll::query()
            ->where('periodicity', 'Semanal')
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->whereDate('period_start', '<=', $latestSunday->toDateString())
            ->orderBy('id')
            ->chunkById(100, function ($payrolls) use ($latestSunday, &$created): void {
                foreach ($payrolls as $payroll) {
                    $firstSunday = $this->firstScheduleDate($payroll->period_start);
                    $lastSunday = $latestSunday;

                    if ($payroll->period_end) {
                        $lastSunday = $this->previousOrSameSunday($this->date($payroll->period_end));

                        if ($lastSunday->isAfter($latestSunday)) {
                            $lastSunday = $latestSunday;
                        }
                    }

                    if ($firstSunday->isAfter($lastSunday)) {
                        continue;
                    }

                    for ($sunday = $firstSunday; ! $sunday->isAfter($lastSunday); $sunday = $sunday->addWeek()) {
                        DB::transaction(function () use ($payroll, $sunday, &$created): void {
                            $lockedPayroll = ConstructionPayroll::query()
                                ->whereKey($payroll->id)
                                ->lockForUpdate()
                                ->firstOrFail();
                            $exists = $lockedPayroll->paymentOrders()
                                ->whereDate('scheduled_for', $sunday->toDateString())
                                ->exists();

                            $this->ensureWeeklyOccurrence($lockedPayroll, $sunday);
                            $created += $exists ? 0 : 1;
                        });
                    }
                }
            });

        return $created;
    }

    public function generateBiweeklyDueOccurrences(CarbonInterface|string|null $asOf = null): int
    {
        $releaseLimit = $this->date($asOf);
        $created = 0;

        ConstructionPayroll::query()
            ->where('periodicity', 'Quincenal')
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->whereDate('period_start', '<=', $releaseLimit->toDateString())
            ->orderBy('id')
            ->chunkById(100, function ($payrolls) use ($releaseLimit, &$created): void {
                foreach ($payrolls as $payroll) {
                    $this->synchronize($payroll);

                    $scheduleDate = $this->firstBiweeklyScheduleDate($payroll->period_start);
                    $scheduleEnd = $releaseLimit;

                    if ($payroll->period_end) {
                        $periodEnd = $this->date($payroll->period_end);

                        if ($periodEnd->isBefore($scheduleEnd)) {
                            $scheduleEnd = $periodEnd;
                        }
                    }

                    while (! $scheduleDate->isAfter($scheduleEnd)) {
                        DB::transaction(function () use ($payroll, $scheduleDate, &$created): void {
                            $lockedPayroll = ConstructionPayroll::query()
                                ->whereKey($payroll->id)
                                ->lockForUpdate()
                                ->firstOrFail();
                            $exists = $lockedPayroll->paymentOrders()
                                ->whereDate('scheduled_for', $scheduleDate->toDateString())
                                ->exists();

                            $this->ensureBiweeklyOccurrence($lockedPayroll, $scheduleDate);
                            $created += $exists ? 0 : 1;
                        });

                        $scheduleDate = $this->nextBiweeklyScheduleDate($scheduleDate);
                    }
                }
            });

        return $created;
    }

    public function firstScheduleDate(CarbonInterface|string $periodStart): CarbonImmutable
    {
        $start = $this->date($periodStart);
        $daysUntilSunday = (7 - $start->dayOfWeek) % 7;

        return $start->addDays($daysUntilSunday);
    }

    public function firstPaymentDueDate(CarbonInterface|string $periodStart): CarbonImmutable
    {
        return $this->paymentDueDate($this->firstScheduleDate($periodStart));
    }

    public function firstPaymentDueDateFor(
        string $periodicity,
        CarbonInterface|string $periodStart
    ): CarbonImmutable {
        return match ($periodicity) {
            'Quincenal' => $this->biweeklyPaymentDueDate($periodStart),
            default => $this->firstPaymentDueDate($periodStart),
        };
    }

    public function paymentDueDate(CarbonInterface|string $sunday): CarbonImmutable
    {
        return $this->date($sunday)->addDays(5);
    }

    public function biweeklyPaymentDueDate(CarbonInterface|string $scheduleDate): CarbonImmutable
    {
        $date = $this->date($scheduleDate);
        $firstDueDate = $date->startOfMonth()->addDays(14);
        $secondDueDay = min(30, $date->daysInMonth);
        $secondDueDate = $date->startOfMonth()->addDays($secondDueDay - 1);

        if (! $date->isAfter($firstDueDate)) {
            return $firstDueDate;
        }

        if (! $date->isAfter($secondDueDate)) {
            return $secondDueDate;
        }

        return $date->startOfMonth()->addMonth()->addDays(14);
    }

    private function synchronizeWeekly(ConstructionPayroll $payroll): ConstructionPaymentOrder
    {
        $firstSunday = $this->firstScheduleDate($payroll->period_start);
        $this->movePrimaryOrderToSchedule($payroll, $firstSunday);

        $payroll->paymentOrders()
            ->whereNotNull('scheduled_for')
            ->whereNull('payment_file_path')
            ->get()
            ->each(function (ConstructionPaymentOrder $order) use ($payroll): void {
                $this->fillWeeklyOccurrence(
                    $order,
                    $payroll,
                    $this->date($order->scheduled_for)
                );
                $order->save();
            });

        return $this->ensureWeeklyOccurrence($payroll, $firstSunday);
    }

    private function synchronizeBiweekly(ConstructionPayroll $payroll): ConstructionPaymentOrder
    {
        $firstScheduleDate = $this->firstBiweeklyScheduleDate($payroll->period_start);
        $this->movePrimaryOrderToSchedule($payroll, $firstScheduleDate);

        $payroll->paymentOrders()
            ->whereNotNull('scheduled_for')
            ->whereNull('payment_file_path')
            ->get()
            ->each(function (ConstructionPaymentOrder $order) use ($payroll): void {
                $this->fillBiweeklyOccurrence(
                    $order,
                    $payroll,
                    $this->date($order->scheduled_for)
                );
                $order->save();
            });

        return $this->ensureBiweeklyOccurrence($payroll, $firstScheduleDate);
    }

    private function movePrimaryOrderToSchedule(
        ConstructionPayroll $payroll,
        CarbonImmutable $scheduleDate
    ): void {
        $primaryOrder = $payroll->paymentOrders()
            ->where('code', $payroll->code)
            ->first();

        if (! $primaryOrder || $primaryOrder->scheduled_for?->isSameDay($scheduleDate)) {
            return;
        }

        $conflictExists = $payroll->paymentOrders()
            ->whereKeyNot($primaryOrder->id)
            ->whereDate('scheduled_for', $scheduleDate->toDateString())
            ->exists();

        if (! $conflictExists) {
            $primaryOrder->scheduled_for = $scheduleDate;
            $primaryOrder->save();
        }
    }

    private function ensureWeeklyOccurrence(
        ConstructionPayroll $payroll,
        CarbonImmutable $sunday
    ): ConstructionPaymentOrder {
        $order = $payroll->paymentOrders()
            ->whereDate('scheduled_for', $sunday->toDateString())
            ->first();

        if (! $order) {
            $order = new ConstructionPaymentOrder([
                'construction_payroll_id' => $payroll->id,
                'scheduled_for' => $sunday,
                'code' => $this->occurrenceCode(
                    $payroll,
                    $sunday,
                    $this->firstScheduleDate($payroll->period_start)
                ),
            ]);
        }

        if (filled($order->payment_file_path)) {
            return $order;
        }

        $this->fillWeeklyOccurrence($order, $payroll, $sunday);
        $order->save();

        return $order;
    }

    private function fillWeeklyOccurrence(
        ConstructionPaymentOrder $order,
        ConstructionPayroll $payroll,
        CarbonImmutable $sunday
    ): void {
        $friday = $this->paymentDueDate($sunday);

        $order->fill([
            'construction_project_id' => $payroll->construction_project_id,
            'construction_payroll_id' => $payroll->id,
            'scheduled_for' => $sunday,
            'type' => 'Nomina',
            'description' => $payroll->description,
            'contractor' => $payroll->contractor,
            'area' => $payroll->area,
            'periodicity' => 'Semanal',
            'period_start' => $sunday,
            'period_end' => $friday,
            'period_reference' => null,
            'payment_due_date' => $friday,
            'progress' => $payroll->progress,
            'amount' => $payroll->amount,
            'status' => $payroll->status,
        ]);
    }

    private function ensureBiweeklyOccurrence(
        ConstructionPayroll $payroll,
        CarbonImmutable $scheduleDate
    ): ConstructionPaymentOrder {
        $order = $payroll->paymentOrders()
            ->whereDate('scheduled_for', $scheduleDate->toDateString())
            ->first();

        if (! $order) {
            $order = new ConstructionPaymentOrder([
                'construction_payroll_id' => $payroll->id,
                'scheduled_for' => $scheduleDate,
                'code' => $this->occurrenceCode(
                    $payroll,
                    $scheduleDate,
                    $this->firstBiweeklyScheduleDate($payroll->period_start)
                ),
            ]);
        }

        if (filled($order->payment_file_path)) {
            return $order;
        }

        $this->fillBiweeklyOccurrence($order, $payroll, $scheduleDate);
        $order->save();

        return $order;
    }

    private function fillBiweeklyOccurrence(
        ConstructionPaymentOrder $order,
        ConstructionPayroll $payroll,
        CarbonImmutable $scheduleDate
    ): void {
        $dueDate = $this->biweeklyPaymentDueDate($scheduleDate);

        $order->fill([
            'construction_project_id' => $payroll->construction_project_id,
            'construction_payroll_id' => $payroll->id,
            'scheduled_for' => $scheduleDate,
            'type' => 'Nomina',
            'description' => $payroll->description,
            'contractor' => $payroll->contractor,
            'area' => $payroll->area,
            'periodicity' => 'Quincenal',
            'period_start' => $scheduleDate,
            'period_end' => $dueDate,
            'period_reference' => null,
            'payment_due_date' => $dueDate,
            'progress' => $payroll->progress,
            'amount' => $payroll->amount,
            'status' => $payroll->status,
        ]);
    }

    private function synchronizeSingleOrder(ConstructionPayroll $payroll): ConstructionPaymentOrder
    {
        $order = $payroll->paymentOrders()
            ->whereNull('scheduled_for')
            ->first();

        if (! $order) {
            $order = $payroll->paymentOrders()
                ->where('code', $payroll->code)
                ->whereNull('payment_file_path')
                ->first() ?? new ConstructionPaymentOrder;
            $order->scheduled_for = null;
        }

        $wasPaid = filled($order->payment_file_path);
        $order->fill([
            'construction_project_id' => $payroll->construction_project_id,
            'construction_payroll_id' => $payroll->id,
            'type' => 'Nomina',
            'code' => $order->exists ? $order->code : $this->singleOrderCode($payroll),
            'description' => $payroll->description,
            'contractor' => $payroll->contractor,
            'area' => $payroll->area,
            'periodicity' => $payroll->periodicity,
            'period_start' => $payroll->period_start,
            'period_end' => $payroll->period_end,
            'period_reference' => null,
            'payment_due_date' => $payroll->payment_due_date,
            'progress' => $payroll->progress,
            'amount' => $payroll->amount,
            'status' => $wasPaid ? 'Pagado' : $payroll->status,
        ]);
        $order->save();

        return $order;
    }

    private function occurrenceCode(
        ConstructionPayroll $payroll,
        CarbonImmutable $scheduleDate,
        CarbonImmutable $firstScheduleDate
    ): string {
        $baseCodeAvailable = ! ConstructionPaymentOrder::query()
            ->where('code', $payroll->code)
            ->exists();

        if ($scheduleDate->isSameDay($firstScheduleDate) && $baseCodeAvailable) {
            return $payroll->code;
        }

        return $payroll->code.'-'.$scheduleDate->format('Ymd');
    }

    private function singleOrderCode(ConstructionPayroll $payroll): string
    {
        if (! ConstructionPaymentOrder::query()->where('code', $payroll->code)->exists()) {
            return $payroll->code;
        }

        return $payroll->code.'-'.CarbonImmutable::now(self::TIMEZONE)->format('YmdHis');
    }

    private function previousOrSameSunday(CarbonImmutable $date): CarbonImmutable
    {
        return $date->subDays($date->dayOfWeek);
    }

    private function firstBiweeklyScheduleDate(CarbonInterface|string $periodStart): CarbonImmutable
    {
        return $this->date($periodStart);
    }

    private function nextBiweeklyScheduleDate(CarbonImmutable $scheduleDate): CarbonImmutable
    {
        $dueDate = $this->biweeklyPaymentDueDate($scheduleDate);

        if ($dueDate->day === 15) {
            return $dueDate->addDay();
        }

        return $dueDate->startOfMonth()->addMonth();
    }

    private function date(CarbonInterface|string|null $value): CarbonImmutable
    {
        if ($value instanceof CarbonInterface) {
            $value = $value->format('Y-m-d');
        }

        return CarbonImmutable::parse($value ?? 'now', self::TIMEZONE)->startOfDay();
    }
}
