<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('construction_payment_progress_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('construction_payment_order_id');
            $table->string('file_path');
            $table->string('original_name');
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign('construction_payment_order_id', 'payment_progress_photos_order_fk')
                ->references('id')
                ->on('construction_payment_orders')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('construction_payment_progress_photos');
    }
};
