<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('week_number')->nullable()->index();
            $table->date('record_date')->index();
            $table->decimal('programmed_percent', 5, 2)->default(0);
            $table->decimal('actual_percent', 5, 2)->default(0);
            $table->decimal('quantity_executed', 15, 4)->default(0);
            $table->text('observations')->nullable();
            $table->json('evidence')->nullable();
            $table->string('status')->default('Registrado')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('estimates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('estimate_number');
            $table->date('period_start');
            $table->date('period_end');
            $table->date('cutoff_date')->nullable();
            $table->date('prepared_at')->nullable();
            $table->date('authorized_at')->nullable();
            $table->date('scheduled_payment_date')->nullable()->index();
            $table->decimal('previous_progress', 5, 2)->default(0);
            $table->decimal('period_progress', 5, 2)->default(0);
            $table->decimal('cumulative_progress', 5, 2)->default(0);
            $table->decimal('gross_amount', 15, 2)->default(0);
            $table->decimal('amortization', 15, 2)->default(0);
            $table->decimal('retention', 15, 2)->default(0);
            $table->decimal('penalties', 15, 2)->default(0);
            $table->decimal('deductions', 15, 2)->default(0);
            $table->decimal('taxes', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('status')->default('Borrador')->index();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['project_id', 'estimate_number']);
        });

        Schema::create('estimate_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estimate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_item_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('previous_quantity', 15, 4)->default(0);
            $table->decimal('period_quantity', 15, 4)->default(0);
            $table->decimal('cumulative_quantity', 15, 4)->default(0);
            $table->decimal('programmed_percent', 5, 2)->default(0);
            $table->decimal('actual_percent', 5, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('real_amount', 15, 2)->default(0);
            $table->string('status')->default('En proceso')->index();
            $table->timestamps();
        });

        Schema::create('estimate_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estimate_id')->constrained()->cascadeOnDelete();
            $table->date('paid_at')->nullable()->index();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('reference')->nullable();
            $table->string('receipt_path')->nullable();
            $table->string('status')->default('Programado')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('retentions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('estimate_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('percentage', 5, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('released_amount', 15, 2)->default(0);
            $table->date('released_at')->nullable();
            $table->string('document_path')->nullable();
            $table->string('status')->default('Por liberar')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retentions');
        Schema::dropIfExists('estimate_payments');
        Schema::dropIfExists('estimate_items');
        Schema::dropIfExists('estimates');
        Schema::dropIfExists('progress_records');
    }
};
