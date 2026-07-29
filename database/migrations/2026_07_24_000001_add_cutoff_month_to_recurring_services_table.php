<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recurring_services', function (Blueprint $table) {
            if (! Schema::hasColumn('recurring_services', 'cutoff_month')) {
                $table->unsignedTinyInteger('cutoff_month')->nullable()->after('cutoff_day');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recurring_services', function (Blueprint $table) {
            if (Schema::hasColumn('recurring_services', 'cutoff_month')) {
                $table->dropColumn('cutoff_month');
            }
        });
    }
};
