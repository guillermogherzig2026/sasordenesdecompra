<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('construction_projects', function (Blueprint $table) {
            $table->decimal('constructed_area', 15, 2)->default(0);
            $table->decimal('sellable_rentable_area', 15, 2)->default(0);
            $table->decimal('parking_area', 15, 2)->default(0);
            $table->unsignedSmallInteger('levels_count')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('construction_projects', function (Blueprint $table) {
            $table->dropColumn([
                'constructed_area',
                'sellable_rentable_area',
                'parking_area',
                'levels_count',
            ]);
        });
    }
};
