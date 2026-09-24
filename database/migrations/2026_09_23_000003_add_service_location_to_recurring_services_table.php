<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('recurring_services', 'service_location')) {
            return;
        }

        Schema::table('recurring_services', function (Blueprint $table) {
            $table->text('service_location')->nullable()->after('branch');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('recurring_services', 'service_location')) {
            return;
        }

        Schema::table('recurring_services', function (Blueprint $table) {
            $table->dropColumn('service_location');
        });
    }
};
