<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('occasional_services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 30);
            $table->string('color', 20);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        $now = now();
        DB::table('occasional_services')->insert([
            ['name' => 'Tag', 'description' => null, 'icon' => 'tag', 'color' => 'blue', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Tiempo Aire', 'description' => null, 'icon' => 'phone', 'color' => 'blue', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Vale de Gasolina', 'description' => null, 'icon' => 'fuel', 'color' => 'rose', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Vale de despensa', 'description' => null, 'icon' => 'basket', 'color' => 'green', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('occasional_services');
    }
};
