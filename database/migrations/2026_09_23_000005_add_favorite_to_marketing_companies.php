<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketing_companies', function (Blueprint $table): void {
            $table->boolean('is_favorite')->default(false);
        });

        // Keep existing active companies in the upper selector after the upgrade.
        DB::table('marketing_companies')->where('active', true)->update(['is_favorite' => true]);
    }

    public function down(): void
    {
        Schema::table('marketing_companies', function (Blueprint $table): void {
            $table->dropColumn('is_favorite');
        });
    }
};
