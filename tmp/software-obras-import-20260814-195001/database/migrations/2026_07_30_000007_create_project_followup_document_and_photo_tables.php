<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('event_type')->index();
            $table->dateTime('starts_at')->index();
            $table->dateTime('ends_at')->nullable();
            $table->string('related_type')->nullable();
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('status')->default('Programado')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('log_date')->index();
            $table->string('weather')->nullable();
            $table->text('personnel')->nullable();
            $table->text('machinery')->nullable();
            $table->text('activities')->nullable();
            $table->text('received_material')->nullable();
            $table->text('progress')->nullable();
            $table->text('problems')->nullable();
            $table->text('instructions')->nullable();
            $table->text('observations')->nullable();
            $table->json('photos')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('folio')->unique();
            $table->date('incident_date')->index();
            $table->string('category')->index();
            $table->text('description');
            $table->string('priority')->default('Media')->index();
            $table->date('commitment_date')->nullable()->index();
            $table->string('evidence_path')->nullable();
            $table->text('solution')->nullable();
            $table->string('status')->default('Abierta')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('change_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requester_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('folio')->unique();
            $table->date('requested_at')->nullable()->index();
            $table->text('description');
            $table->text('reason')->nullable();
            $table->json('affected_items')->nullable();
            $table->decimal('cost_impact', 15, 2)->default(0);
            $table->integer('schedule_impact_days')->default(0);
            $table->decimal('new_contract_amount', 15, 2)->default(0);
            $table->date('new_end_date')->nullable();
            $table->json('authorizations')->nullable();
            $table->string('evidence_path')->nullable();
            $table->string('status')->default('Solicitada')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('document_type')->nullable()->index();
            $table->string('category')->index();
            $table->date('document_date')->nullable();
            $table->string('version')->default('1.0');
            $table->string('file_path')->nullable();
            $table->json('version_history')->nullable();
            $table->string('status')->default('Vigente')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('category')->nullable()->index();
            $table->date('taken_at')->nullable()->index();
            $table->string('file_path');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photos');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('change_orders');
        Schema::dropIfExists('incidents');
        Schema::dropIfExists('daily_logs');
        Schema::dropIfExists('project_events');
    }
};
