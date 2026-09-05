<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('construction_payment_orders', function (Blueprint $table) {
            $table->string('invoice_xml_file_path')->nullable()->after('invoice_original_name');
            $table->string('invoice_xml_original_name')->nullable()->after('invoice_xml_file_path');
            $table->string('fiscal_verification_file_path')->nullable()->after('invoice_xml_original_name');
            $table->string('fiscal_verification_original_name')->nullable()->after('fiscal_verification_file_path');
        });
    }

    public function down(): void
    {
        Schema::table('construction_payment_orders', function (Blueprint $table) {
            $table->dropColumn([
                'invoice_xml_file_path',
                'invoice_xml_original_name',
                'fiscal_verification_file_path',
                'fiscal_verification_original_name',
            ]);
        });
    }
};
