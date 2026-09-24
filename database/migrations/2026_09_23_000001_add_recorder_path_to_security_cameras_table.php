<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('security_cameras', function (Blueprint $table): void {
            $table->string('recorder_path', 80)->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('security_cameras', function (Blueprint $table): void {
            $table->dropUnique(['recorder_path']);
            $table->dropColumn('recorder_path');
        });
    }
};
