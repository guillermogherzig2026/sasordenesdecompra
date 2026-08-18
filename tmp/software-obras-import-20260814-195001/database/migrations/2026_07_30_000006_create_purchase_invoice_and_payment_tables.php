<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('rfc', 20)->nullable()->index();
            $table->string('contact_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('specialty')->nullable()->index();
            $table->text('materials_supplied')->nullable();
            $table->string('payment_terms')->nullable();
            $table->string('bank_account')->nullable();
            $table->json('documentation')->nullable();
            $table->string('status')->default('Activo')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('supply_orders', function (Blueprint $table) {
            $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
        });

        Schema::create('supplier_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('price_score', 5, 2)->default(0);
            $table->decimal('delivery_score', 5, 2)->default(0);
            $table->decimal('quality_score', 5, 2)->default(0);
            $table->decimal('warranty_score', 5, 2)->default(0);
            $table->text('comments')->nullable();
            $table->date('evaluated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('material_request_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supply_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('folio')->unique();
            $table->date('ordered_at')->nullable();
            $table->date('expected_delivery_at')->nullable()->index();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('taxes', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('status')->default('Borrador')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_catalog_id')->nullable()->constrained('material_catalog')->nullOnDelete();
            $table->string('description');
            $table->string('unit', 30);
            $table->decimal('quantity', 15, 4)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('folio')->index();
            $table->string('uuid')->nullable()->unique();
            $table->date('issued_at')->nullable()->index();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('taxes', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('pdf_path')->nullable();
            $table->string('xml_path')->nullable();
            $table->string('status')->default('Recibida')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('estimate_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payroll_id')->nullable()->constrained()->nullOnDelete();
            $table->string('payment_type')->index();
            $table->string('beneficiary');
            $table->string('concept');
            $table->string('related_item')->nullable();
            $table->date('requested_at')->nullable();
            $table->date('scheduled_at')->nullable()->index();
            $table->date('paid_at')->nullable()->index();
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('retention', 15, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('reference')->nullable();
            $table->string('receipt_path')->nullable();
            $table->string('status')->default('Pendiente')->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('supplier_evaluations');
        Schema::table('supply_orders', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
        });
        Schema::dropIfExists('suppliers');
    }
};
