<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\RecurringService;
use App\Models\RecurringServiceReceipt;
use App\Support\StoredFileResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class FinanceServicePaymentController extends Controller
{
    public function index()
    {
        $this->ensureFinance();

        return view('finance.services.index', [
            'months' => $this->monthsPayload(),
        ]);
    }

    public function paymentForm(RecurringService $service, string $dueDate)
    {
        $this->ensureFinance();

        $occurrence = $this->occurrenceForDueDate($service, $dueDate);
        abort_unless($occurrence, 404);
        abort_if($service->is_domiciled, 403);
        $receipt = $this->receiptFor($service, $dueDate);
        abort_unless($service->status !== 'inactive' && filled($receipt?->support_file_path) && ! $receipt->isPaid(), 403);

        return view('finance.services.payment', [
            'service' => $service,
            'occurrence' => $occurrence,
            'receipt' => $receipt,
        ]);
    }

    public function storePayment(Request $request, RecurringService $service, string $dueDate)
    {
        $this->ensureFinance();

        $occurrence = $this->occurrenceForDueDate($service, $dueDate);
        abort_unless($occurrence, 404);
        abort_if($service->is_domiciled, 403);
        $existingReceipt = $this->receiptFor($service, $dueDate);
        abort_unless($service->status !== 'inactive' && filled($existingReceipt?->support_file_path) && ! $existingReceipt->isPaid(), 403);

        $validated = $request->validate([
            'payment_file' => ['required', 'file', 'max:10240'],
            'payment_paid_on' => ['required', 'date'],
        ]);

        $path = $request->file('payment_file')->store('service-payments');
        $receipt = $existingReceipt;

        $receipt->fill([
            'period_start' => $receipt->period_start ?: $occurrence['period_start'],
            'payment_file_path' => $path,
            'payment_original_name' => $request->file('payment_file')->getClientOriginalName(),
            'payment_paid_on' => $validated['payment_paid_on'],
            'paid_by' => Auth::id(),
            'status' => 'paid',
        ]);
        $receipt->save();

        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => RecurringService::class,
            'auditable_id' => $service->id,
            'action' => 'service_paid',
            'description' => "Pago registrado para {$service->folio} periodo {$dueDate}.",
        ]);

        return redirect()->route('finance.services.index')->with('status', 'Comprobante de pago registrado. El servicio quedo pagado.');
    }

    public function supportFile(RecurringServiceReceipt $receipt)
    {
        $this->ensureFinance();

        return StoredFileResponse::inline($receipt->support_file_path, $receipt->support_original_name);
    }

    public function paymentFile(RecurringServiceReceipt $receipt)
    {
        $this->ensureFinance();

        return StoredFileResponse::inline($receipt->payment_file_path, $receipt->payment_original_name);
    }

    public function updateStatus(RecurringService $service, string $status)
    {
        $this->ensureFinance();
        abort_unless(in_array($status, ['active', 'paused', 'inactive'], true), 404);

        $service->update(['status' => $status]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => RecurringService::class,
            'auditable_id' => $service->id,
            'action' => 'service_status_updated',
            'description' => "Estado del servicio {$service->folio} actualizado a {$status}.",
        ]);

        return redirect()->route('finance.services.index')->with('status', 'Estado del servicio actualizado.');
    }

    private function monthsPayload(): array
    {
        $start = now()->startOfMonth();

        return collect(range(0, 12))->map(function (int $offset) use ($start) {
            $month = $start->copy()->addMonths($offset);
            $items = RecurringService::with('receipts')
                ->where('status', '!=', 'inactive')
                ->get()
                ->flatMap(fn (RecurringService $service) => $this->occurrencesForMonth($service, $month))
                ->filter(fn (array $item) => ! $item['service']->is_domiciled && ! filled($item['receipt']?->payment_file_path))
                ->sortBy(fn (array $item) => $item['due_date'])
                ->values();

            return [
                'month_key' => $month->format('Y-m'),
                'label' => $this->monthLabel($month),
                'items' => $items,
                ...$this->monthTotals($items),
            ];
        })->all();
    }

    private function monthTotals($items): array
    {
        $amountFor = fn (array $item) => (float) ($item['receipt']?->amount ?? $item['service']->cost);
        $total = $items->sum($amountFor);
        $paid = $items->filter(fn (array $item) => $item['service']->is_domiciled || filled($item['receipt']?->payment_file_path))
            ->sum($amountFor);

        return [
            'total' => $total,
            'paid_total' => $paid,
            'pending_total' => max($total - $paid, 0),
        ];
    }

    private function monthLabel(Carbon $month): string
    {
        $months = [
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre',
        ];

        return ($months[(int) $month->month] ?? strtolower($month->format('F'))).' de '.$month->year;
    }

    private function occurrencesForMonth(RecurringService $service, Carbon $month)
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $interval = max($service->payment_interval_days, 1);

        if ($service->cutoff_day) {
            return $this->occurrencesForMonthWithCutoff($service, $month, $start, $end, $interval);
        }

        $due = $service->start_date->copy();
        $guard = 0;

        while ($due->lt($start) && $guard < 500) {
            $due->addDays($interval);
            $guard++;
        }

        $items = collect();
        while ($due->lte($end) && $guard < 600) {
            $dueDate = $due->toDateString();
            $items->push([
                'service' => $service,
                'due_date' => $dueDate,
                'period_start' => $due->copy()->subDays($interval)->toDateString(),
                'receipt' => $this->receiptFor($service, $dueDate),
            ]);
            $due->addDays($interval);
            $guard++;
        }

        return $items;
    }

    private function occurrencesForMonthWithCutoff(RecurringService $service, Carbon $month, Carbon $start, Carbon $end, int $interval): Collection
    {
        $cutoffDay = $service->cutoff_day;
        $startDate = $service->start_date->copy()->startOfDay();
        $cutoffMonth = (int) ($service->cutoff_month ?: $startDate->month);
        $cutoff = Carbon::create($service->cutoff_year ?: $startDate->year, $cutoffMonth, $cutoffDay)->startOfDay();
        if ($cutoff->lt($startDate)) {
            $cutoff->addYear();
        }

        $due = $cutoff->copy()->addDays($interval);
        $guard = 0;
        while ($due->lt($start) && $guard < 500) {
            $due->addDays($interval);
            $guard++;
        }

        $items = collect();
        while ($due->lte($end) && $guard < 600) {
            $dueDate = $due->toDateString();
            $items->push([
                'service' => $service,
                'due_date' => $dueDate,
                'period_start' => $due->copy()->subDays($interval)->toDateString(),
                'receipt' => $this->receiptFor($service, $dueDate),
            ]);
            $due->addDays($interval);
            $guard++;
        }

        return $items;
    }

    private function occurrenceForDueDate(RecurringService $service, string $dueDate): ?array
    {
        return $this->occurrencesForMonth($service, Carbon::parse($dueDate)->startOfMonth())
            ->firstWhere('due_date', Carbon::parse($dueDate)->toDateString());
    }

    private function receiptFor(RecurringService $service, string $dueDate): ?RecurringServiceReceipt
    {
        return $service->receipts->firstWhere('due_date', $dueDate)
            ?? RecurringServiceReceipt::where('recurring_service_id', $service->id)->whereDate('due_date', $dueDate)->first();
    }

    private function ensureFinance(): void
    {
        abort_unless(Auth::user()?->canAccessRole('finance'), 403);
    }
}
