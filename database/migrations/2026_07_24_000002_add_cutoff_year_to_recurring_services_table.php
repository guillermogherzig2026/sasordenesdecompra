<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recurring_services', function (Blueprint $table) {
            if (! Schema::hasColumn('recurring_services', 'cutoff_year')) {
                $table->unsignedSmallInteger('cutoff_year')->nullable()->after('cutoff_month');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recurring_services', function (Blueprint $table) {
            if (Schema::hasColumn('recurring_services', 'cutoff_year')) {
                $table->dropColumn('cutoff_year');
            }
        });
    }
};
