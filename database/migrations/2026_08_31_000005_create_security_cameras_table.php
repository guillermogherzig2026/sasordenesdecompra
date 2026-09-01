<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_cameras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('security_branch_id')->constrained('security_branches')->cascadeOnDelete();
            $table->string('name', 120);
            $table->text('stream_url');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['security_branch_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_cameras');
    }
};
