<?php

namespace Database\Seeders;

use App\Models\ConstructionPayroll;
use App\Models\ConstructionProject;
use App\Services\ConstructionPayrollScheduleService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConstructionDemoPayrollPaymentSeeder extends Seeder
{
    public function run(): void
    {
        $project = ConstructionProject::query()
            ->where('project_key', 'OBR-005')
            ->first();

        if (! $project) {
            return;
        }

        DB::transaction(function () use ($project): void {
            $payroll = ConstructionPayroll::query()->updateOrCreate(
                ['code' => 'NOM-DEMO'],
                [
                    'construction_project_id' => $project->id,
                    'contractor' => 'Cuadrilla Leoncio',
                    'description' => 'Nomina quincenal de demostracion',
                    'area' => 'Mano de obra',
                    'periodicity' => 'Quincenal',
                    'period_start' => '2026-09-01',
                    'period_end' => '2026-09-15',
                    'payment_due_date' => '2026-09-15',
                    'progress' => 20,
                    'amount' => 24850,
                    'status' => 'En revision',
                    'payment_date' => null,
                ],
            );

            app(ConstructionPayrollScheduleService::class)->synchronize($payroll);
        });
    }
}
