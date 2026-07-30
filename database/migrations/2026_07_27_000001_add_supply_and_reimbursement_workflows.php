<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'buyer_subrole')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('buyer_subrole')->nullable()->after('role');
            });
        }

        DB::table('users')
            ->where('role', 'buyer')
            ->whereNull('buyer_subrole')
            ->update(['buyer_subrole' => 'purchases']);

        Schema::create('warehouse_catalog_items', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->nullable();
            $table->string('name');
            $table->string('unit')->default('pieza');
            $table->text('description')->nullable();
            $table->boolean('authorized')->default(true);
            $table->timestamps();
        });

        Schema::create('warehouse_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_catalog_item_id')->constrained()->cascadeOnDelete();
            $table->string('warehouse')->default('San Francisco 516');
            $table->decimal('quantity', 12, 2)->default(0);
            $table->decimal('minimum_quantity', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['warehouse_catalog_item_id', 'warehouse'], 'warehouse_item_warehouse_unique');
        });

        Schema::create('supply_orders', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->foreignId('requester_id')->constrained('users');
            $table->foreignId('company_id')->constrained();
            $table->string('warehouse_from')->default('San Francisco 516');
            $table->string('warehouse_to')->nullable();
            $table->date('created_on');
            $table->date('delivery_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->default('sent');
            $table->decimal('total', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('delivery_remission_number')->nullable();
            $table->date('delivered_on')->nullable();
            $table->foreignId('delivered_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('supply_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_catalog_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('article');
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_cost', 14, 2)->default(0);
            $table->decimal('line_total', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('reimbursement_orders', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->foreignId('requester_id')->constrained('users');
            $table->foreignId('company_id')->constrained();
            $table->string('provider');
            $table->string('concept')->nullable();
            $table->date('created_on');
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('status')->default('sent');
            $table->string('quote_file_path')->nullable();
            $table->string('quote_original_name')->nullable();
            $table->string('support_file_path')->nullable();
            $table->string('support_original_name')->nullable();
            $table->date('support_on')->nullable();
            $table->string('payment_file_path')->nullable();
            $table->string('payment_original_name')->nullable();
            $table->date('paid_on')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reimbursement_orders');
        Schema::dropIfExists('supply_order_items');
        Schema::dropIfExists('supply_orders');
        Schema::dropIfExists('warehouse_inventory_items');
        Schema::dropIfExists('warehouse_catalog_items');

        if (Schema::hasColumn('users', 'buyer_subrole')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('buyer_subrole');
            });
        }
    }
};
