<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('construction_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rfc', 20)->nullable()->index();
            $table->string('contact_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('status')->default('Activo')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('construction_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('construction_clients')->nullOnDelete();
            $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('project_key')->unique();
            $table->string('name');
            $table->string('location')->nullable()->index();
            $table->string('project_type')->nullable();
            $table->string('modality')->index();
            $table->string('status')->default('En ejecucion')->index();
            $table->date('start_date')->nullable();
            $table->date('estimated_end_date')->nullable();
            $table->decimal('contracted_value', 15, 2)->default(0);
            $table->decimal('estimated_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('retention_amount', 15, 2)->default(0);
            $table->decimal('physical_progress', 5, 2)->default(0);
            $table->decimal('financial_progress', 5, 2)->default(0);
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('construction_project_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('construction_project_id')->constrained('construction_projects')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('can_view')->default(true);
            $table->boolean('can_edit')->default(false);
            $table->timestamps();
            $table->unique(['construction_project_id', 'user_id']);
        });

        Schema::create('construction_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('construction_project_id')->nullable()->constrained('construction_projects')->nullOnDelete();
            $table->timestamp('occurred_at')->useCurrent()->index();
            $table->string('module')->index();
            $table->string('action')->index();
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        $this->seedDemoData();
    }

    public function down(): void
    {
        Schema::dropIfExists('construction_audit_logs');
        Schema::dropIfExists('construction_project_user');
        Schema::dropIfExists('construction_projects');
        Schema::dropIfExists('construction_clients');
    }

    private function seedDemoData(): void
    {
        if (DB::table('construction_projects')->exists()) {
            return;
        }

        $now = now();
        $companyId = DB::table('companies')->value('id');
        $userId = DB::table('users')->where('role', 'superadmin')->value('id') ?? DB::table('users')->value('id');

        $clients = [
            ['name' => 'Inmobiliaria Los Pinos', 'rfc' => 'ILP260730A12', 'contact_name' => 'Mariana Fuentes', 'phone' => '55 1000 2000', 'email' => 'contacto@lospinos.local', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Corporativo Norte', 'rfc' => 'CNO260730B22', 'contact_name' => 'Ricardo Montes', 'phone' => '55 2000 3000', 'email' => 'obra@corporativonorte.local', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Desarrollos Industriales MX', 'rfc' => 'DIM260730C33', 'contact_name' => 'Laura Paredes', 'phone' => '55 3000 4000', 'email' => 'industrial@dimx.local', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('construction_clients')->insert($clients);
        $clientIds = DB::table('construction_clients')->pluck('id', 'name');

        $projects = [
            [
                'project_key' => 'OBR-001',
                'name' => 'Residencial Los Pinos',
                'client_id' => $clientIds['Inmobiliaria Los Pinos'] ?? null,
                'location' => 'Naucalpan, Estado de Mexico',
                'project_type' => 'Residencial',
                'modality' => 'Precio alzado',
                'status' => 'En ejecucion',
                'contracted_value' => 18500000,
                'estimated_amount' => 9200000,
                'paid_amount' => 7850000,
                'retention_amount' => 460000,
                'physical_progress' => 54,
                'financial_progress' => 42,
                'photo_path' => '/images/construction-projects/residencial-los-pinos.png',
                'start_date' => '2026-01-12',
                'estimated_end_date' => '2026-12-15',
            ],
            [
                'project_key' => 'OBR-002',
                'name' => 'Oficinas Corporativas',
                'client_id' => $clientIds['Corporativo Norte'] ?? null,
                'location' => 'Monterrey, Nuevo Leon',
                'project_type' => 'Oficinas',
                'modality' => 'Administracion',
                'status' => 'En ejecucion',
                'contracted_value' => 32400000,
                'estimated_amount' => 15100000,
                'paid_amount' => 12600000,
                'retention_amount' => 755000,
                'physical_progress' => 38,
                'financial_progress' => 33,
                'photo_path' => '/images/construction-projects/oficinas-corporativas.png',
                'start_date' => '2026-03-04',
                'estimated_end_date' => '2027-02-28',
            ],
            [
                'project_key' => 'OBR-003',
                'name' => 'Bodega Industrial',
                'client_id' => $clientIds['Desarrollos Industriales MX'] ?? null,
                'location' => 'Queretaro, Queretaro',
                'project_type' => 'Industrial',
                'modality' => 'Precio alzado',
                'status' => 'Por iniciar',
                'contracted_value' => 9800000,
                'estimated_amount' => 0,
                'paid_amount' => 0,
                'retention_amount' => 0,
                'physical_progress' => 0,
                'financial_progress' => 0,
                'photo_path' => '/images/construction-projects/bodega-industrial.png',
                'start_date' => '2026-09-01',
                'estimated_end_date' => '2027-03-15',
            ],
        ];

        foreach ($projects as $project) {
            DB::table('construction_projects')->insert([
                ...$project,
                'company_id' => $companyId,
                'responsible_user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('construction_audit_logs')->insert([
            [
                'user_id' => $userId,
                'occurred_at' => $now,
                'module' => 'Administracion de obra',
                'action' => 'Modulo importado',
                'description' => 'Se agrego el codigo base de Software Obras al sistema.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
};
