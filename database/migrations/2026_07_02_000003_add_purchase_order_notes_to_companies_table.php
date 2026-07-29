<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('companies', 'purchase_order_notes')) {
            return;
        }

        Schema::table('companies', function (Blueprint $table) {
            $table->text('purchase_order_notes')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('companies', 'purchase_order_notes')) {
            return;
        }

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('purchase_order_notes');
        });
    }
};
