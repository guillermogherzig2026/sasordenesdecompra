<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketing_whatsapp_numbers', function (Blueprint $table): void {
            $table->string('meta_phone_number_id')->nullable()->unique();
            $table->string('meta_waba_id')->nullable();
            $table->text('meta_access_token')->nullable();
            $table->timestamp('meta_configured_at')->nullable();
            $table->timestamp('last_webhook_at')->nullable();
        });

        Schema::table('marketing_conversations', function (Blueprint $table): void {
            $table->foreignId('whatsapp_number_id')->nullable()->constrained('marketing_whatsapp_numbers')->nullOnDelete();
            $table->string('contact_wa_id', 40)->nullable();
            $table->timestamp('last_inbound_at')->nullable();
            $table->unique(['whatsapp_number_id', 'contact_wa_id'], 'marketing_conversations_whatsapp_contact_unique');
        });

        Schema::table('marketing_messages', function (Blueprint $table): void {
            $table->string('meta_message_id')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('marketing_messages', function (Blueprint $table): void {
            $table->dropUnique(['meta_message_id']);
            $table->dropColumn('meta_message_id');
        });

        Schema::table('marketing_conversations', function (Blueprint $table): void {
            $table->dropUnique('marketing_conversations_whatsapp_contact_unique');
            $table->dropConstrainedForeignId('whatsapp_number_id');
            $table->dropColumn(['contact_wa_id', 'last_inbound_at']);
        });

        Schema::table('marketing_whatsapp_numbers', function (Blueprint $table): void {
            $table->dropUnique(['meta_phone_number_id']);
            $table->dropColumn(['meta_phone_number_id', 'meta_waba_id', 'meta_access_token', 'meta_configured_at', 'last_webhook_at']);
        });
    }
};
