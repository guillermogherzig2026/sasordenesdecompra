<?php

namespace Tests\Feature;

use App\Models\Marketing\Company;
use App\Models\Marketing\Conversation;
use App\Models\Marketing\Message;
use App\Models\Marketing\WhatsAppNumber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class MarketingWhatsAppCloudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.whatsapp.verify_token', 'test-verify-token');
        config()->set('services.whatsapp.app_secret', 'test-app-secret');
    }

    public function test_superadmin_can_configure_one_cloud_number_per_company_without_exposing_the_token(): void
    {
        $company = Company::create(['name' => 'Empresa Uno']);
        $admin = User::factory()->create(['role' => 'superadmin', 'active' => true]);

        $this->actingAs($admin)->post(route('marketing.conversations.whatsapp.store'), [
            'company_id' => $company->id,
            'display_name' => 'Ventas',
            'phone_number' => '+52 55 9000 1234',
            'meta_phone_number_id' => '12345678901',
            'meta_waba_id' => '98765432101',
            'meta_access_token' => 'secret-system-user-token',
        ])->assertRedirect(route('marketing.conversations.index', ['company_id' => $company->id]));

        $number = WhatsAppNumber::query()->where('company_id', $company->id)->firstOrFail();
        $this->assertSame('secret-system-user-token', $number->meta_access_token);
        $this->assertNotSame('secret-system-user-token', $number->getRawOriginal('meta_access_token'));
        $this->assertArrayNotHasKey('meta_access_token', $number->toArray());

        $this->actingAs($admin)->post(route('marketing.conversations.whatsapp.store'), [
            'company_id' => $company->id,
            'display_name' => 'Ventas central',
            'phone_number' => '+52 55 9000 1234',
            'meta_phone_number_id' => '12345678901',
            'meta_waba_id' => '98765432101',
            'meta_access_token' => '',
        ])->assertRedirect();

        $this->assertDatabaseCount('marketing_whatsapp_numbers', 1);
        $this->assertSame('secret-system-user-token', $number->fresh()->meta_access_token);

        $this->actingAs($admin)
            ->get(route('marketing.conversations.index', ['company_id' => $company->id]))
            ->assertOk()
            ->assertSee('Esperando webhook')
            ->assertDontSee('secret-system-user-token');
    }

    public function test_pending_official_number_is_displayed_and_reused_when_meta_ids_arrive(): void
    {
        $company = Company::create(['name' => 'Empresa Uno']);
        $number = $company->whatsappNumbers()->create([
            'display_name' => 'WhatsApp oficial',
            'phone_number' => '+525500000001',
            'status' => 'pending_meta',
        ]);
        $admin = User::factory()->create(['role' => 'superadmin', 'active' => true]);

        $this->actingAs($admin)
            ->get(route('marketing.conversations.index', ['company_id' => $company->id]))
            ->assertOk()
            ->assertSee('Pendiente de Meta')
            ->assertSee('+525500000001');

        $this->actingAs($admin)->post(route('marketing.conversations.whatsapp.store'), [
            'company_id' => $company->id,
            'phone_number' => '+525500000001',
            'meta_phone_number_id' => '12345678901',
            'meta_waba_id' => '98765432101',
            'meta_access_token' => 'secret-system-user-token',
        ])->assertRedirect();

        $this->assertDatabaseCount('marketing_whatsapp_numbers', 1);
        $this->assertSame($number->id, WhatsAppNumber::query()->where('meta_phone_number_id', '12345678901')->firstOrFail()->id);
        $this->assertSame('active', $number->fresh()->status);
    }

    public function test_only_superadmin_can_set_cloud_credentials(): void
    {
        $company = Company::create(['name' => 'Empresa Uno']);
        $user = User::factory()->create([
            'role' => 'buyer',
            'active' => true,
            'menu_permissions' => ['marketing.conversations'],
        ]);

        $this->actingAs($user)->post(route('marketing.conversations.whatsapp.store'), [
            'company_id' => $company->id,
            'phone_number' => '+52 55 9000 1234',
            'meta_phone_number_id' => '12345678901',
            'meta_waba_id' => '98765432101',
            'meta_access_token' => 'secret',
        ])->assertForbidden();

        $this->assertDatabaseCount('marketing_whatsapp_numbers', 0);
    }

    public function test_failed_connection_validation_does_not_flash_the_access_token(): void
    {
        $company = Company::create(['name' => 'Empresa Uno']);
        $admin = User::factory()->create(['role' => 'superadmin', 'active' => true]);

        $this->actingAs($admin)->post(route('marketing.conversations.whatsapp.store'), [
            'company_id' => $company->id,
            'phone_number' => '+52 55 9000 1234',
            'meta_phone_number_id' => 'invalid-id',
            'meta_waba_id' => '98765432101',
            'meta_access_token' => 'never-flash-this-token',
        ])->assertSessionHasErrors('meta_phone_number_id');

        $this->assertArrayNotHasKey('meta_access_token', session()->getOldInput());
        $this->assertDatabaseCount('marketing_whatsapp_numbers', 0);
    }

    public function test_webhook_challenge_requires_the_correct_verify_token(): void
    {
        $this->get('/api/webhooks/whatsapp?hub.mode=subscribe&hub.verify_token=test-verify-token&hub.challenge=abc123')
            ->assertOk()
            ->assertSeeText('abc123');

        $this->get('/api/webhooks/whatsapp?hub.mode=subscribe&hub.verify_token=wrong&hub.challenge=abc123')
            ->assertForbidden();
    }

    public function test_signed_inbound_message_is_isolated_and_idempotent(): void
    {
        $first = Company::create(['name' => 'Empresa Uno']);
        $second = Company::create(['name' => 'Empresa Dos']);
        $number = $this->cloudNumber($first, '12345678901', '98765432101');
        $this->cloudNumber($second, '12345678902', '98765432102');
        $payload = $this->messagePayload('12345678901', '98765432101');

        $this->signedWebhook($payload)->assertOk();
        $this->signedWebhook($payload)->assertOk();

        $this->assertDatabaseCount('marketing_leads', 1);
        $this->assertDatabaseCount('marketing_conversations', 1);
        $this->assertDatabaseCount('marketing_messages', 1);
        $conversation = Conversation::query()->firstOrFail();
        $this->assertSame($first->id, $conversation->company_id);
        $this->assertSame($number->id, $conversation->whatsapp_number_id);
        $this->assertSame('5215512345678', $conversation->contact_wa_id);
        $this->assertSame(1, $conversation->unread_count);
        $this->assertSame('Hola desde Meta', $conversation->messages()->firstOrFail()->body);
        $this->assertNotNull($number->fresh()->last_webhook_at);

        $wrongWaba = $this->messagePayload('12345678901', '98765432102');
        $wrongWaba['entry'][0]['changes'][0]['value']['messages'][0]['id'] = 'wamid.wrong';
        $this->signedWebhook($wrongWaba)->assertOk();
        $this->assertDatabaseCount('marketing_messages', 1);

        $admin = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $this->actingAs($admin)
            ->post(route('marketing.conversations.messages.store', $conversation), ['body' => 'No debe salir'])
            ->assertSessionHasErrors('body');
        $this->assertFalse(Message::query()->where('body', 'No debe salir')->exists());
    }

    public function test_same_customer_can_write_to_two_companies_without_crossing_conversations(): void
    {
        $first = Company::create(['name' => 'Empresa Uno']);
        $second = Company::create(['name' => 'Empresa Dos']);
        $this->cloudNumber($first, '12345678901', '98765432101');
        $this->cloudNumber($second, '12345678902', '98765432102');

        $this->signedWebhook($this->messagePayload('12345678901', '98765432101'))->assertOk();
        $secondPayload = $this->messagePayload('12345678902', '98765432102');
        $secondPayload['entry'][0]['changes'][0]['value']['messages'][0]['id'] = 'wamid.second-company';
        $this->signedWebhook($secondPayload)->assertOk();

        $this->assertDatabaseCount('marketing_leads', 2);
        $this->assertDatabaseCount('marketing_conversations', 2);
        $this->assertSame(1, Conversation::query()->where('company_id', $first->id)->count());
        $this->assertSame(1, Conversation::query()->where('company_id', $second->id)->count());
    }

    public function test_invalid_signature_and_missing_secret_fail_closed(): void
    {
        $payload = $this->messagePayload('12345678901', '98765432101');
        $raw = json_encode($payload, JSON_THROW_ON_ERROR);

        $this->call('POST', '/api/webhooks/whatsapp', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_HUB_SIGNATURE_256' => 'sha256=invalid',
        ], $raw)->assertForbidden();

        config()->set('services.whatsapp.app_secret', null);
        $this->signedWebhook($payload)->assertStatus(503);
        $this->assertDatabaseCount('marketing_messages', 0);
    }

    private function cloudNumber(Company $company, string $phoneId, string $wabaId): WhatsAppNumber
    {
        return $company->whatsappNumbers()->create([
            'display_name' => 'Ventas',
            'phone_number' => '+52 55 9000 1234',
            'provider_phone_id' => $phoneId,
            'meta_phone_number_id' => $phoneId,
            'meta_waba_id' => $wabaId,
            'meta_access_token' => 'secret-system-user-token',
            'status' => 'active',
        ]);
    }

    private function messagePayload(string $phoneId, string $wabaId): array
    {
        return [
            'object' => 'whatsapp_business_account',
            'entry' => [[
                'id' => $wabaId,
                'changes' => [[
                    'field' => 'messages',
                    'value' => [
                        'metadata' => ['phone_number_id' => $phoneId],
                        'contacts' => [['wa_id' => '5215512345678', 'profile' => ['name' => 'Cliente Uno']]],
                        'messages' => [[
                            'id' => 'wamid.test-message',
                            'from' => '5215512345678',
                            'timestamp' => '1792627200',
                            'type' => 'text',
                            'text' => ['body' => 'Hola desde Meta'],
                        ]],
                    ],
                ]],
            ]],
        ];
    }

    private function signedWebhook(array $payload): TestResponse
    {
        $raw = json_encode($payload, JSON_THROW_ON_ERROR);

        return $this->call('POST', '/api/webhooks/whatsapp', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X_HUB_SIGNATURE_256' => 'sha256='.hash_hmac('sha256', $raw, 'test-app-secret'),
        ], $raw);
    }
}
