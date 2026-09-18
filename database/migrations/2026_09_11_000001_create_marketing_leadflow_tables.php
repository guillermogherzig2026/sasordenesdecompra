<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_companies', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('domain')->nullable();
            $table->string('industry')->nullable();
            $table->string('logo_initials', 8)->nullable();
            $table->string('brand_color', 20)->default('#0f766e');
            $table->boolean('active')->default(true);
            $table->string('status')->default('active');
            $table->string('api_token', 80)->unique();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->index(['active', 'status']);
        });

        Schema::create('marketing_users', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('marketing_companies')->nullOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('asesor');
            $table->string('phone')->nullable();
            $table->string('timezone')->default('America/Mexico_City');
            $table->boolean('active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->index(['company_id', 'role', 'active']);
        });

        Schema::create('marketing_campaigns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('marketing_companies')->cascadeOnDelete();
            $table->foreignId('responsible_user_id')->nullable()->constrained('marketing_users')->nullOnDelete();
            $table->string('name');
            $table->string('status')->default('active');
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->decimal('budget', 12, 2)->default(0);
            $table->decimal('spent', 12, 2)->default(0);
            $table->string('product')->nullable();
            $table->string('source')->default('Redes sociales');
            $table->json('social_networks')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'status']);
        });

        Schema::create('marketing_campaign_user', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->constrained('marketing_campaigns')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('marketing_users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['campaign_id', 'user_id']);
        });

        Schema::create('marketing_leads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('marketing_companies')->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('marketing_campaigns')->nullOnDelete();
            $table->foreignId('advisor_id')->nullable()->constrained('marketing_users')->nullOnDelete();
            $table->string('name');
            $table->string('phone', 40);
            $table->string('email')->nullable();
            $table->string('product')->nullable();
            $table->string('status')->default('nuevo');
            $table->decimal('estimated_value', 12, 2)->default(0);
            $table->timestamp('last_contact_at')->nullable();
            $table->timestamp('next_follow_up_at')->nullable();
            $table->string('source')->default('WhatsApp');
            $table->string('position')->nullable();
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'campaign_id', 'status']);
            $table->index(['advisor_id', 'last_contact_at']);
        });

        Schema::create('marketing_conversations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('marketing_companies')->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('marketing_campaigns')->nullOnDelete();
            $table->foreignId('lead_id')->constrained('marketing_leads')->cascadeOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('marketing_users')->nullOnDelete();
            $table->string('channel')->default('WhatsApp');
            $table->string('status')->default('open');
            $table->unsignedInteger('unread_count')->default(0);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'campaign_id', 'status']);
            $table->index(['assigned_user_id', 'last_message_at']);
        });

        Schema::create('marketing_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('conversation_id')->constrained('marketing_conversations')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('marketing_users')->nullOnDelete();
            $table->string('sender_type')->default('lead');
            $table->string('direction')->default('in');
            $table->string('message_type')->default('text');
            $table->text('body')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('external_id')->nullable()->index();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->index(['conversation_id', 'sent_at']);
        });

        Schema::create('marketing_whatsapp_numbers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('marketing_companies')->cascadeOnDelete();
            $table->string('display_name')->nullable();
            $table->string('phone_number', 40);
            $table->string('provider_phone_id')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            $table->index(['company_id', 'status']);
        });

        Schema::create('marketing_office_hours', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('marketing_companies')->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('marketing_campaigns')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            $table->index(['company_id', 'campaign_id', 'day_of_week']);
        });

        Schema::create('marketing_tags', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('marketing_companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('color', 20)->default('#14b8a6');
            $table->timestamps();
            $table->unique(['company_id', 'name']);
        });

        Schema::create('marketing_automation_rules', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('marketing_companies')->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('marketing_campaigns')->cascadeOnDelete();
            $table->string('name');
            $table->string('trigger');
            $table->string('action');
            $table->json('payload')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index(['company_id', 'campaign_id', 'active']);
        });

        Schema::create('marketing_webhook_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('marketing_companies')->nullOnDelete();
            $table->string('provider')->default('whatsapp');
            $table->string('event_type')->nullable();
            $table->json('payload');
            $table->string('status')->default('received');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->index(['provider', 'event_type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_webhook_events');
        Schema::dropIfExists('marketing_automation_rules');
        Schema::dropIfExists('marketing_tags');
        Schema::dropIfExists('marketing_office_hours');
        Schema::dropIfExists('marketing_whatsapp_numbers');
        Schema::dropIfExists('marketing_messages');
        Schema::dropIfExists('marketing_conversations');
        Schema::dropIfExists('marketing_leads');
        Schema::dropIfExists('marketing_campaign_user');
        Schema::dropIfExists('marketing_campaigns');
        Schema::dropIfExists('marketing_users');
        Schema::dropIfExists('marketing_companies');
    }
};
