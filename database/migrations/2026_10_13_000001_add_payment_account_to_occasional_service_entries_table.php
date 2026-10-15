<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('occasional_service_entries', function (Blueprint $table) {
            $table->string('bank', 120)->nullable();
            $table->string('payer_account', 80)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('occasional_service_entries', function (Blueprint $table) {
            $table->dropColumn(['bank', 'payer_account']);
        });
    }
};
