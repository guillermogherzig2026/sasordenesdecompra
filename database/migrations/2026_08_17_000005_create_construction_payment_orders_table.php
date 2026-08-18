<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('construction_payrolls', function (Blueprint $table) {
            $table->date('payment_due_date')->nullable()->after('period_end');
        });

        Schema::create('construction_payment_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('construction_project_id')->constrained('construction_projects')->cascadeOnDelete();
            $table->foreignId('construction_payroll_id')->nullable()->unique()->constrained('construction_payrolls')->cascadeOnDelete();
            $table->string('type')->index();
            $table->string('code')->unique();
            $table->string('description');
            $table->string('contractor')->nullable();
            $table->string('area')->nullable();
            $table->string('periodicity')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('period_reference')->nullable();
            $table->date('payment_due_date')->nullable()->index();
            $table->decimal('progress', 5, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('status')->default('En revision')->index();
            $table->string('invoice_file_path')->nullable();
            $table->string('invoice_original_name')->nullable();
            $table->string('payment_file_path')->nullable();
            $table->string('payment_original_name')->nullable();
            $table->date('paid_on')->nullable()->index();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        $now = now();

        DB::table('construction_payrolls')->orderBy('id')->get()->each(function ($payroll) use ($now): void {
            $dueDate = $payroll->payment_date
                ?: Carbon::parse($payroll->period_end)->addDays(5)->toDateString();

            DB::table('construction_payrolls')->where('id', $payroll->id)->update([
                'payment_due_date' => $dueDate,
                'payment_date' => in_array($payroll->status, ['Pagada', 'Pagado'], true) ? $payroll->payment_date : null,
            ]);

            DB::table('construction_payment_orders')->insert([
                'construction_project_id' => $payroll->construction_project_id,
                'construction_payroll_id' => $payroll->id,
                'type' => 'Nomina',
                'code' => $payroll->code,
                'description' => $payroll->description,
                'contractor' => $payroll->contractor,
                'area' => $payroll->area,
                'periodicity' => $payroll->periodicity,
                'period_start' => $payroll->period_start,
                'period_end' => $payroll->period_end,
                'payment_due_date' => $dueDate,
                'progress' => $payroll->progress,
                'amount' => $payroll->amount,
                'status' => $payroll->status,
                'paid_on' => in_array($payroll->status, ['Pagada', 'Pagado'], true) ? $payroll->payment_date : null,
                'created_at' => $payroll->created_at ?: $now,
                'updated_at' => $now,
            ]);
        });

        $projectId = DB::table('construction_projects')->where('project_key', 'OBR-001')->value('id');

        if ($projectId) {
            DB::table('construction_payment_orders')->insert([
                [
                    'construction_project_id' => $projectId,
                    'type' => 'Estimacion',
                    'code' => 'PAQ-005',
                    'description' => 'Castillos y dalas Planta alta',
                    'contractor' => null,
                    'area' => 'Obra civil',
                    'periodicity' => 'Quincenal',
                    'period_reference' => 'Pendiente',
                    'payment_due_date' => '2026-08-20',
                    'progress' => 0,
                    'amount' => 1250000,
                    'status' => 'Sin asignar',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'construction_project_id' => $projectId,
                    'type' => 'Estimacion',
                    'code' => 'PAQ-006',
                    'description' => 'Instalacion electrica Planta baja',
                    'contractor' => 'Electricidad SA de CV',
                    'area' => 'Instalaciones',
                    'periodicity' => 'Quincenal',
                    'period_reference' => 'Pendiente',
                    'payment_due_date' => '2026-08-25',
                    'progress' => 40,
                    'amount' => 1320000,
                    'status' => 'En ejecucion',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'construction_project_id' => $projectId,
                    'type' => 'Estimacion',
                    'code' => 'PAQ-007',
                    'description' => 'Instalacion hidraulica Planta baja',
                    'contractor' => 'Hidraulica del Norte',
                    'area' => 'Instalaciones',
                    'periodicity' => 'Quincenal',
                    'period_reference' => 'Pendiente',
                    'payment_due_date' => '2026-08-31',
                    'progress' => 20,
                    'amount' => 1580000,
                    'status' => 'En ejecucion',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'construction_project_id' => $projectId,
                    'type' => 'Estimacion',
                    'code' => 'PAQ-008',
                    'description' => 'Acabado interior Muros PB',
                    'contractor' => null,
                    'area' => 'Albanileria',
                    'periodicity' => 'Quincenal',
                    'period_reference' => 'Pendiente',
                    'payment_due_date' => '2026-09-05',
                    'progress' => 0,
                    'amount' => 2350000,
                    'status' => 'Programado',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('construction_payment_orders');

        Schema::table('construction_payrolls', function (Blueprint $table) {
            $table->dropColumn('payment_due_date');
        });
    }
};
