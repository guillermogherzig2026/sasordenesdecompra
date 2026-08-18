<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('unit', 30);
            $table->string('category')->nullable()->index();
            $table->decimal('minimum_stock', 15, 4)->default(0);
            $table->decimal('standard_cost', 15, 2)->default(0);
            $table->string('status')->default('Activo')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('type')->default('Obra')->index();
            $table->string('location')->nullable();
            $table->string('status')->default('Activo')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_catalog_id')->constrained('material_catalog')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('work_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('movement_date')->index();
            $table->string('folio')->index();
            $table->string('movement_type')->index();
            $table->decimal('quantity_in', 15, 4)->default(0);
            $table->decimal('quantity_out', 15, 4)->default(0);
            $table->decimal('balance', 15, 4)->default(0);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('movement_value', 15, 2)->default(0);
            $table->decimal('accumulated_value', 15, 2)->default(0);
            $table->string('reference')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requester_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->string('folio')->unique();
            $table->string('area')->nullable();
            $table->date('requested_at')->nullable();
            $table->date('required_at')->nullable()->index();
            $table->string('priority')->default('Media')->index();
            $table->string('related_activity')->nullable();
            $table->text('observations')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('status')->default('Borrador')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('material_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_catalog_id')->nullable()->constrained('material_catalog')->nullOnDelete();
            $table->string('material_name');
            $table->string('unit', 30);
            $table->decimal('requested_quantity', 15, 4)->default(0);
            $table->decimal('available_stock', 15, 4)->default(0);
            $table->decimal('authorized_quantity', 15, 4)->default(0);
            $table->decimal('supplied_quantity', 15, 4)->default(0);
            $table->decimal('pending_quantity', 15, 4)->default(0);
            $table->string('usage_destination')->nullable();
            $table->string('status')->default('Pendiente')->index();
            $table->timestamps();
        });

        Schema::create('supply_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_request_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('origin_warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->foreignId('destination_warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable();
            $table->foreignId('shipping_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('receiving_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('folio')->unique();
            $table->date('ordered_at')->nullable();
            $table->date('commitment_date')->nullable()->index();
            $table->string('status')->default('Generada')->index();
            $table->text('observations')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('supply_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_catalog_id')->nullable()->constrained('material_catalog')->nullOnDelete();
            $table->string('material_name');
            $table->string('unit', 30);
            $table->decimal('requested_quantity', 15, 4)->default(0);
            $table->decimal('authorized_quantity', 15, 4)->default(0);
            $table->decimal('sent_quantity', 15, 4)->default(0);
            $table->decimal('received_quantity', 15, 4)->default(0);
            $table->decimal('rejected_quantity', 15, 4)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('lot')->nullable();
            $table->string('evidence_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_order_items');
        Schema::dropIfExists('supply_orders');
        Schema::dropIfExists('material_request_items');
        Schema::dropIfExists('material_requests');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('material_catalog');
    }
};
