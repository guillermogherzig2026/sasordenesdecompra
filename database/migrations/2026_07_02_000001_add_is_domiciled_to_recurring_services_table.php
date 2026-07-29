<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('recurring_services', 'is_domiciled')) {
            return;
        }

        Schema::table('recurring_services', function (Blueprint $table) {
            $table->boolean('is_domiciled')->default(false)->after('payment_interval_days');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('recurring_services', 'is_domiciled')) {
            return;
        }

        Schema::table('recurring_services', function (Blueprint $table) {
            $table->dropColumn('is_domiciled');
        });
    }
};
