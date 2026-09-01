<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('security_company_id')->constrained('security_companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 50)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();

            $table->unique(['security_company_id', 'name']);
            $table->unique(['security_company_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_branches');
    }
};
