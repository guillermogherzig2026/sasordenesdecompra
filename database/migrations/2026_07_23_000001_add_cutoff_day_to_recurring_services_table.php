<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recurring_services', function (Blueprint $table) {
            if (! Schema::hasColumn('recurring_services', 'cutoff_day')) {
                $table->unsignedSmallInteger('cutoff_day')->nullable()->after('start_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recurring_services', function (Blueprint $table) {
            if (Schema::hasColumn('recurring_services', 'cutoff_day')) {
                $table->dropColumn('cutoff_day');
            }
        });
    }
};
