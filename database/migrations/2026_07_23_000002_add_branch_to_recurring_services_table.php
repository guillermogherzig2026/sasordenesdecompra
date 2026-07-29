<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recurring_services', function (Blueprint $table) {
            if (! Schema::hasColumn('recurring_services', 'branch')) {
                $table->string('branch')->nullable()->after('payer_account');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recurring_services', function (Blueprint $table) {
            if (Schema::hasColumn('recurring_services', 'branch')) {
                $table->dropColumn('branch');
            }
        });
    }
};
