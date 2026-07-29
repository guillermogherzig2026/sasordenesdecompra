<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_orders', 'quote_file_path')) {
                $table->string('quote_file_path')->nullable()->after('observations');
            }

            if (! Schema::hasColumn('purchase_orders', 'quote_original_name')) {
                $table->string('quote_original_name')->nullable()->after('quote_file_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_orders', 'quote_original_name')) {
                $table->dropColumn('quote_original_name');
            }

            if (Schema::hasColumn('purchase_orders', 'quote_file_path')) {
                $table->dropColumn('quote_file_path');
            }
        });
    }
};
