<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('contract_number')->unique();
            $table->date('signed_at')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('total_value', 15, 2)->default(0);
            $table->decimal('retention_percentage', 5, 2)->default(0);
            $table->string('status')->default('Vigente')->index();
            $table->string('document_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('version')->default('1.0');
            $table->decimal('planned_total', 15, 2)->default(0);
            $table->date('approved_at')->nullable();
            $table->string('status')->default('Activo')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('budget_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('code')->index();
            $table->string('name');
            $table->string('level')->default('Categoria')->index();
            $table->integer('sort_order')->default(0);
            $table->string('status')->default('Activo')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['project_id', 'code']);
        });

        Schema::create('work_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('budget_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('work_items')->nullOnDelete();
            $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('code')->index();
            $table->text('description');
            $table->string('unit', 30)->nullable();
            $table->decimal('contracted_quantity', 15, 4)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('executed_quantity', 15, 4)->default(0);
            $table->decimal('progress_percent', 5, 2)->default(0);
            $table->decimal('scheduled_percent', 5, 2)->default(0);
            $table->decimal('estimated_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->date('estimated_end_date')->nullable();
            $table->string('status')->default('Sin iniciar')->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['project_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_items');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('contracts');
    }
};
