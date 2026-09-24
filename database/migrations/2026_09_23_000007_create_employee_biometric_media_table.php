<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_biometric_media', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('employee_number', 32);
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type', 64);
            $table->string('media_type', 8);
            $table->unsignedBigInteger('byte_size');
            $table->timestamps();

            $table->index(['user_id', 'employee_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_biometric_media');
    }
};
