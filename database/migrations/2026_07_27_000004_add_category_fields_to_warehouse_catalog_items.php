<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('warehouse_catalog_items')) {
            return;
        }

        Schema::table('warehouse_catalog_items', function (Blueprint $table) {
            if (! Schema::hasColumn('warehouse_catalog_items', 'category')) {
                $table->string('category')->nullable()->after('sku');
            }

            if (! Schema::hasColumn('warehouse_catalog_items', 'subcategory')) {
                $table->string('subcategory')->nullable()->after('category');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('warehouse_catalog_items')) {
            return;
        }

        Schema::table('warehouse_catalog_items', function (Blueprint $table) {
            if (Schema::hasColumn('warehouse_catalog_items', 'subcategory')) {
                $table->dropColumn('subcategory');
            }

            if (Schema::hasColumn('warehouse_catalog_items', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};
