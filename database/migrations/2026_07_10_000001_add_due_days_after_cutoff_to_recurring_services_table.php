<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recurring_services', function (Blueprint $table) {
            if (! Schema::hasColumn('recurring_services', 'due_days_after_cutoff')) {
                $table->unsignedSmallInteger('due_days_after_cutoff')->default(0)->after('payment_interval_days');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recurring_services', function (Blueprint $table) {
            if (Schema::hasColumn('recurring_services', 'due_days_after_cutoff')) {
                $table->dropColumn('due_days_after_cutoff');
            }
        });
    }
};
