<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('construction_schedule_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('construction_project_id')->constrained('construction_projects')->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('contractor');
            $table->text('description')->nullable();
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->string('status')->default('Programado')->index();
            $table->timestamps();

            $table->index(['construction_project_id', 'start_date', 'end_date'], 'construction_schedule_project_dates_index');
        });

        $this->seedReferenceSchedule();
    }

    public function down(): void
    {
        Schema::dropIfExists('construction_schedule_items');
    }

    private function seedReferenceSchedule(): void
    {
        $projectId = DB::table('construction_projects')->where('project_key', 'OBR-001')->value('id');

        if (! $projectId) {
            return;
        }

        $userId = DB::table('users')->where('role', 'superadmin')->value('id')
            ?? DB::table('users')->value('id');
        $now = now();
        $items = [
            ['title' => 'Cimentacion y armado', 'contractor' => 'Constructora Alfa', 'description' => 'Armado de acero y preparacion para colado de zapatas.', 'start_date' => '2026-08-03', 'end_date' => '2026-08-07', 'progress' => 65, 'status' => 'En proceso'],
            ['title' => 'Instalacion electrica', 'contractor' => 'Instalaciones RM', 'description' => 'Canalizacion, cableado y colocacion de centros de carga.', 'start_date' => '2026-08-10', 'end_date' => '2026-08-14', 'progress' => 40, 'status' => 'En proceso'],
            ['title' => 'Colado de losa', 'contractor' => 'Acabados del Centro', 'description' => 'Suministro, bombeo y nivelacion de concreto.', 'start_date' => '2026-08-17', 'end_date' => '2026-08-20', 'progress' => 30, 'status' => 'Programado'],
            ['title' => 'Aplanados interiores', 'contractor' => 'Estructuras MX', 'description' => 'Aplicacion de mortero en muros del primer nivel.', 'start_date' => '2026-08-17', 'end_date' => '2026-08-21', 'progress' => 20, 'status' => 'Programado'],
            ['title' => 'Colocacion de canceleria', 'contractor' => 'Acabados del Centro', 'description' => 'Instalacion de marcos y canceleria de aluminio.', 'start_date' => '2026-08-24', 'end_date' => '2026-08-28', 'progress' => 10, 'status' => 'Programado'],
            ['title' => 'Pruebas hidraulicas', 'contractor' => 'Instalaciones RM', 'description' => 'Pruebas de presion en tuberias y verificacion de fugas.', 'start_date' => '2026-08-31', 'end_date' => '2026-09-02', 'progress' => 0, 'status' => 'Programado'],
        ];

        foreach ($items as $item) {
            DB::table('construction_schedule_items')->insert([
                ...$item,
                'construction_project_id' => $projectId,
                'created_by_user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
};
