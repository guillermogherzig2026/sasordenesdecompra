<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('occasional_service_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('occasional_service_id')->constrained('occasional_services')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('reference_number', 100);
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['occasional_service_id', 'reference_number'], 'occasional_entry_category_reference_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('occasional_service_entries');
    }
};
