<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_companies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('entity_type', 20);
            $table->string('legal_name')->nullable();
            $table->string('rfc', 20)->nullable()->unique();
            $table->text('address')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone', 40)->nullable();
            $table->string('contact_email')->nullable();
            $table->foreignId('finance_company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_companies');
    }
};
