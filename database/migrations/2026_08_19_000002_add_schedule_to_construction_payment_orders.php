<?php

use Carbon\CarbonImmutable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('construction_payment_orders', function (Blueprint $table) {
            $table->date('scheduled_for')->nullable()->after('construction_payroll_id');
            $table->index(
                'construction_payroll_id',
                'construction_payment_orders_payroll_schedule_idx'
            );
        });

        Schema::table('construction_payment_orders', function (Blueprint $table) {
            $table->dropUnique('construction_payment_orders_construction_payroll_id_unique');
        });

        DB::table('construction_payment_orders as orders')
            ->join('construction_payrolls as payrolls', 'payrolls.id', '=', 'orders.construction_payroll_id')
            ->where('payrolls.periodicity', 'Semanal')
            ->select(['orders.id', 'payrolls.id as payroll_id', 'payrolls.period_start'])
            ->orderBy('orders.id')
            ->get()
            ->each(function ($row): void {
                $periodStart = CarbonImmutable::parse($row->period_start);
                $sunday = $periodStart->addDays((7 - $periodStart->dayOfWeek) % 7);
                $friday = $sunday->addDays(5);

                DB::table('construction_payment_orders')
                    ->where('id', $row->id)
                    ->update([
                        'scheduled_for' => $sunday->toDateString(),
                        'payment_due_date' => $friday->toDateString(),
                    ]);

                DB::table('construction_payrolls')
                    ->where('id', $row->payroll_id)
                    ->update(['payment_due_date' => $friday->toDateString()]);
            });

        Schema::table('construction_payment_orders', function (Blueprint $table) {
            $table->unique(
                ['construction_payroll_id', 'scheduled_for'],
                'construction_payment_orders_payroll_scheduled_unique'
            );
        });
    }

    public function down(): void
    {
        DB::table('construction_payment_orders')
            ->whereNotNull('construction_payroll_id')
            ->orderBy('id')
            ->get()
            ->groupBy('construction_payroll_id')
            ->each(function ($orders): void {
                $keep = $orders->first();
                $deleteIds = $orders->skip(1)->pluck('id');

                if ($deleteIds->isNotEmpty()) {
                    DB::table('construction_payment_orders')->whereIn('id', $deleteIds)->delete();
                }

                DB::table('construction_payment_orders')
                    ->where('id', $keep->id)
                    ->update(['scheduled_for' => null]);
            });

        Schema::table('construction_payment_orders', function (Blueprint $table) {
            $table->unique('construction_payroll_id');
        });

        Schema::table('construction_payment_orders', function (Blueprint $table) {
            $table->dropUnique('construction_payment_orders_payroll_scheduled_unique');
            $table->dropIndex('construction_payment_orders_payroll_schedule_idx');
            $table->dropColumn('scheduled_for');
        });
    }
};
