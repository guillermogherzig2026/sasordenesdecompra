<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('occasional_service_payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('occasional_service_payment_id');
            $table->foreignId('occasional_service_entry_id');
            $table->string('recipient_name', 100);
            $table->string('reference_number', 100);
            $table->decimal('amount', 12, 2);
            $table->timestamps();

            $table->foreign('occasional_service_payment_id', 'occasional_allocations_payment_fk')
                ->references('id')->on('occasional_service_payments')->cascadeOnDelete();
            $table->foreign('occasional_service_entry_id', 'occasional_allocations_entry_fk')
                ->references('id')->on('occasional_service_entries')->restrictOnDelete();
            $table->unique(['occasional_service_payment_id', 'occasional_service_entry_id'], 'occasional_allocations_payment_entry_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('occasional_service_payment_allocations');
    }
};
