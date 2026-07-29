<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('rfc', 20)->unique();
            $table->text('address')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });

        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users');
            $table->string('business_name');
            $table->string('rfc', 20);
            $table->string('business_line');
            $table->string('bank');
            $table->string('account_number');
            $table->string('clabe', 18);
            $table->timestamps();

            $table->unique(['buyer_id', 'rfc']);
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->foreignId('buyer_id')->constrained('users');
            $table->foreignId('company_id')->constrained();
            $table->foreignId('provider_id')->constrained();
            $table->date('created_on');
            $table->date('due_date');
            $table->date('delivery_date');
            $table->string('status')->default('sent');
            $table->string('receipt_status')->default('pending');
            $table->decimal('total', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->string('article');
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 14, 2);
            $table->decimal('line_total', 14, 2);
            $table->timestamps();
        });

        Schema::create('purchase_order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('paid_by')->constrained('users');
            $table->string('file_path');
            $table->string('original_name');
            $table->date('paid_on');
            $table->timestamps();
        });

        Schema::create('purchase_order_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('received_by')->constrained('users');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('invoice_number');
            $table->date('received_on');
            $table->timestamps();
        });

        Schema::create('purchase_order_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_receipt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('received_quantity', 12, 2);
            $table->timestamps();
        });

        Schema::create('recurring_services', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->string('holder');
            $table->string('company_name');
            $table->string('bank');
            $table->string('payer_account');
            $table->string('service_name');
            $table->string('provider');
            $table->string('service_number');
            $table->string('category');
            $table->decimal('cost', 14, 2);
            $table->string('validity');
            $table->unsignedInteger('payment_interval_days')->default(30);
            $table->date('start_date');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('recurring_service_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recurring_service_id')->constrained()->cascadeOnDelete();
            $table->date('due_date');
            $table->date('period_start')->nullable();
            $table->string('support_file_path')->nullable();
            $table->string('support_original_name')->nullable();
            $table->date('support_on')->nullable();
            $table->string('payment_file_path')->nullable();
            $table->string('payment_original_name')->nullable();
            $table->date('payment_paid_on')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users');
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->unique(['recurring_service_id', 'due_date']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->nullableMorphs('auditable');
            $table->string('action');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('recurring_service_receipts');
        Schema::dropIfExists('recurring_services');
        Schema::dropIfExists('purchase_order_receipt_items');
        Schema::dropIfExists('purchase_order_receipts');
        Schema::dropIfExists('purchase_order_payments');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('providers');
        Schema::dropIfExists('companies');
    }
};
