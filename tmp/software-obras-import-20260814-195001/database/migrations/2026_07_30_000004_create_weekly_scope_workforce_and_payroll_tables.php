<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('leader_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('specialty')->nullable()->index();
            $table->string('status')->default('Activa')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('crew_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('position')->nullable()->index();
            $table->string('specialty')->nullable();
            $table->decimal('daily_salary', 12, 2)->default(0);
            $table->decimal('weekly_payment', 12, 2)->default(0);
            $table->string('hiring_type')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('clabe', 18)->nullable();
            $table->json('documents')->nullable();
            $table->string('status')->default('Activo')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('weekly_scopes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('crew_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('week_number')->index();
            $table->unsignedSmallInteger('year')->index();
            $table->string('activity');
            $table->string('unit', 30)->nullable();
            $table->decimal('programmed_quantity', 15, 4)->default(0);
            $table->decimal('executed_quantity', 15, 4)->default(0);
            $table->decimal('fulfillment_percent', 5, 2)->default(0);
            $table->decimal('weekly_budget', 15, 2)->default(0);
            $table->decimal('actual_cost', 15, 2)->default(0);
            $table->date('scheduled_date')->nullable();
            $table->date('actual_date')->nullable();
            $table->text('observations')->nullable();
            $table->json('evidence')->nullable();
            $table->string('status')->default('Programado')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('week_number')->index();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('gross_amount', 15, 2)->default(0);
            $table->decimal('deductions', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->date('paid_at')->nullable();
            $table->string('receipt_path')->nullable();
            $table->string('status')->default('Capturada')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->string('worker_category')->nullable()->index();
            $table->decimal('days_worked', 6, 2)->default(0);
            $table->decimal('normal_hours', 8, 2)->default(0);
            $table->decimal('extra_hours', 8, 2)->default(0);
            $table->decimal('jornales', 8, 2)->default(0);
            $table->decimal('piecework_amount', 15, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('loan', 15, 2)->default(0);
            $table->decimal('gross_amount', 15, 2)->default(0);
            $table->decimal('deductions', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->string('status')->default('Capturada')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
        Schema::dropIfExists('payrolls');
        Schema::dropIfExists('weekly_scopes');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('crews');
    }
};
