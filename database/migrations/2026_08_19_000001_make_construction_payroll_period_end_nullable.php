<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('construction_payrolls', function (Blueprint $table) {
            $table->date('period_end')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('construction_payrolls')
            ->whereNull('period_end')
            ->update(['period_end' => DB::raw('period_start')]);

        Schema::table('construction_payrolls', function (Blueprint $table) {
            $table->date('period_end')->nullable(false)->change();
        });
    }
};
