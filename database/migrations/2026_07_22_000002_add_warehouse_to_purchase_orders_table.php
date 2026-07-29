<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('purchase_orders', 'warehouse')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->string('warehouse')->nullable()->after('company_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('purchase_orders', 'warehouse')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->dropColumn('warehouse');
            });
        }
    }
};
