<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('construction_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('construction_project_id')->constrained('construction_projects')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('contractor');
            $table->string('description');
            $table->string('area')->nullable();
            $table->string('periodicity');
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('progress', 5, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('status')->default('En revision')->index();
            $table->date('payment_date')->nullable();
            $table->timestamps();
        });

        $projectId = DB::table('construction_projects')->where('project_key', 'OBR-001')->value('id');

        if ($projectId) {
            $now = now();

            DB::table('construction_payrolls')->insert([
                [
                    'construction_project_id' => $projectId,
                    'code' => 'NOM-S26',
                    'contractor' => 'Constructora Norte',
                    'description' => 'Nomina quincenal S26',
                    'area' => 'Mano de obra',
                    'periodicity' => 'Semanal',
                    'period_start' => '2026-06-23',
                    'period_end' => '2026-06-29',
                    'progress' => 55,
                    'amount' => 236900,
                    'status' => 'En revision',
                    'payment_date' => '2026-06-30',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
                [
                    'construction_project_id' => $projectId,
                    'code' => 'NOM-S27',
                    'contractor' => 'Constructora Norte',
                    'description' => 'Nomina quincenal S27',
                    'area' => 'Albanileria / Obra civil',
                    'periodicity' => 'Quincenal',
                    'period_start' => '2026-08-16',
                    'period_end' => '2026-08-31',
                    'progress' => 55,
                    'amount' => 93300,
                    'status' => 'En revision',
                    'payment_date' => '2026-08-13',
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('construction_payrolls');
    }
};
