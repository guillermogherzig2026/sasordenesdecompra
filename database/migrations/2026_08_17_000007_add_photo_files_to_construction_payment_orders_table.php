<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('construction_payment_orders', function (Blueprint $table) {
            $table->json('photo_files')->nullable()->after('invoice_original_name');
        });
    }

    public function down(): void
    {
        Schema::table('construction_payment_orders', function (Blueprint $table) {
            $table->dropColumn('photo_files');
        });
    }
};
