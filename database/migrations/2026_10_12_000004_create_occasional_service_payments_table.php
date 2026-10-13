<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('occasional_service_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('occasional_service_entry_id')->constrained('occasional_service_entries')->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('due_date');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('occasional_service_payments');
    }
};
