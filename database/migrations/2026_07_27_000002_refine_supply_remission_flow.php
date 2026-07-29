<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('warehouse_catalog_items', 'unit_cost')) {
            Schema::table('warehouse_catalog_items', function (Blueprint $table) {
                $table->decimal('unit_cost', 14, 2)->default(0);
            });
        }

        Schema::table('supply_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('supply_orders', 'remission_token')) {
                $table->string('remission_token')->nullable();
            }

            if (! Schema::hasColumn('supply_orders', 'received_on')) {
                $table->date('received_on')->nullable();
            }

            if (! Schema::hasColumn('supply_orders', 'received_by_name')) {
                $table->string('received_by_name')->nullable();
            }

            if (! Schema::hasColumn('supply_orders', 'receiving_pin')) {
                $table->string('receiving_pin', 4)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('supply_orders', function (Blueprint $table) {
            foreach (['remission_token', 'received_on', 'received_by_name', 'receiving_pin'] as $column) {
                if (Schema::hasColumn('supply_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        if (Schema::hasColumn('warehouse_catalog_items', 'unit_cost')) {
            Schema::table('warehouse_catalog_items', function (Blueprint $table) {
                $table->dropColumn('unit_cost');
            });
        }
    }
};
