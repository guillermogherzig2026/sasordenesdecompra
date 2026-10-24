<?php

namespace Tests\Feature;

use App\Http\Middleware\EnforceNavigationPermission;
use App\Models\Marketing\Campaign;
use App\Models\Marketing\Company;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class MarketingChannelConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_accounts_are_saved_per_channel_without_overwriting_other_company_settings(): void
    {
        $this->actingAs($this->userWithPermissions(['marketing.settings']));
        $company = $this->company(['settings' => ['timezone' => 'America/Mexico_City', 'notifications' => ['email' => true]]]);
        $other = $this->company(['name' => 'Otra empresa', 'settings' => ['marketing_channels' => ['instagram' => ['account_name' => 'Cuenta ajena']]]]);
        $otherSettings = $other->settings;

        foreach (['meta' => 'Meta Ads', 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'chatgpt' => 'ChatGPT', 'x' => 'X', 'google' => 'Google Ads'] as $key => $network) {
            $this->configure($company, $key, [
                'account_name' => 'Cuenta '.$network,
                'account_identifier' => 'identificador-'.$key,
                'profile_url' => 'https://example.com/'.$key,
                'status' => 'connected',
                'access_token' => 'do-not-store-this-token',
            ])->assertRedirect()->assertSessionHasNoErrors();

            $saved = $company->fresh()->settings['marketing_channels'][$key];
            $this->assertSame('Cuenta '.$network, $saved['account_name']);
            $this->assertSame('identificador-'.$key, $saved['account_identifier']);
            $this->assertSame('https://example.com/'.$key, $saved['profile_url']);
            $this->assertSame($network, $saved['network']);
            $this->assertArrayNotHasKey('status', $saved);
            $this->assertArrayNotHasKey('access_token', $saved);
        }

        $this->configure($company, 'instagram', ['account_name' => 'Cuenta actualizada', 'profile_url' => null])
            ->assertRedirect()->assertSessionHasNoErrors();

        $settings = $company->fresh()->settings;
        $this->assertSame('America/Mexico_City', $settings['timezone']);
        $this->assertSame(['email' => true], $settings['notifications']);
        $this->assertCount(6, $settings['marketing_channels']);
        $this->assertSame('Cuenta actualizada', $settings['marketing_channels']['instagram']['account_name']);
        $this->assertNull($settings['marketing_channels']['instagram']['profile_url']);
        $this->assertSame('Cuenta Meta Ads', $settings['marketing_channels']['meta']['account_name']);
        $this->assertSame($otherSettings, $other->fresh()->settings);
    }

    public function test_other_channel_requires_one_of_the_supported_networks(): void
    {
        $this->actingAs($this->userWithPermissions(['marketing.settings']));
        $company = $this->company();

        foreach (['TikTok', 'LinkedIn', 'YouTube', 'Otros canales'] as $network) {
            $this->configure($company, 'other', ['network' => $network])->assertRedirect()->assertSessionHasNoErrors();
            $this->assertSame($network, $company->fresh()->settings['marketing_channels']['other']['network']);
        }

        $savedSettings = $company->fresh()->settings;
        foreach ([null, 'Instagram', 'Red desconocida'] as $network) {
            $this->configure($company, 'other', ['network' => $network])
                ->assertSessionHasErrorsIn('channelConfiguration', 'network');
            $this->assertSame($savedSettings, $company->fresh()->settings);
        }
    }

    public function test_missing_account_fields_and_non_web_profile_urls_are_rejected_in_the_channel_error_bag(): void
    {
        $this->actingAs($this->userWithPermissions(['marketing.settings']));
        $company = $this->company();

        $this->configure($company, 'instagram', ['account_name' => '', 'account_identifier' => ''])
            ->assertSessionHasErrorsIn('channelConfiguration', ['account_name', 'account_identifier']);

        foreach (['javascript:alert(1)', 'data:text/html,test', 'ftp://example.com/profile', 'no-es-una-url'] as $url) {
            $this->configure($company, 'instagram', ['profile_url' => $url])
                ->assertSessionHasErrorsIn('channelConfiguration', 'profile_url');
        }

        $this->assertEmpty($company->fresh()->settings['marketing_channels'] ?? []);
        $this->configure($company, 'instagram', ['profile_url' => 'http://example.com/profile'])
            ->assertRedirect()->assertSessionDoesntHaveErrors('profile_url', null, 'channelConfiguration');
        $this->assertSame('http://example.com/profile', $company->fresh()->settings['marketing_channels']['instagram']['profile_url']);
    }

    public function test_configuration_rejects_unknown_channels_and_keeps_whatsapp_on_its_existing_endpoint(): void
    {
        $this->actingAs($this->userWithPermissions(['marketing.settings']));
        $company = $this->company();

        foreach (['unknown', 'whatsapp'] as $key) {
            $response = $this->configure($company, $key);
            $this->assertContains($response->status(), [302, 404, 422]);
            if ($response->status() === 302) {
                $response->assertSessionHasErrorsIn('channelConfiguration');
            }
        }

        $this->assertEmpty($company->fresh()->settings['marketing_channels'] ?? []);
        $this->assertDatabaseCount('marketing_whatsapp_numbers', 0);
    }

    public function test_configuring_accounts_requires_settings_permission_even_without_navigation_middleware(): void
    {
        $company = $this->company();
        $this->actingAs($this->userWithPermissions(['marketing.dashboard', 'marketing.campaigns']));

        $this->configure($company, 'instagram')->assertForbidden();
        $this->withoutMiddleware(EnforceNavigationPermission::class);
        $this->configure($company, 'instagram')->assertForbidden();

        $this->assertEmpty($company->fresh()->settings['marketing_channels'] ?? []);
    }

    public function test_linking_channels_requires_campaign_permission_even_without_navigation_middleware(): void
    {
        $company = $this->company();
        $campaign = $this->campaign($company);
        $this->actingAs($this->userWithPermissions(['marketing.dashboard', 'marketing.settings']));
        $this->configure($company, 'instagram')->assertSessionHasNoErrors();

        $this->link($company, $campaign, ['instagram'])->assertForbidden();
        $this->withoutMiddleware(EnforceNavigationPermission::class);
        $this->link($company, $campaign, ['instagram'])->assertForbidden();

        $this->assertSame([], $campaign->fresh()->social_networks);
    }

    public function test_linking_configured_channels_appends_canonical_networks_without_duplicates_or_other_campaign_changes(): void
    {
        $company = $this->company();
        $campaign = $this->campaign($company, ['social_networks' => ['Instagram', 'LinkedIn'], 'source' => 'Instagram, LinkedIn']);
        $otherCampaign = $this->campaign($company, ['name' => 'Otra campaña', 'social_networks' => ['Facebook']]);
        $otherCompany = $this->company(['name' => 'Empresa ajena']);
        $externalCampaign = $this->campaign($otherCompany, ['social_networks' => ['YouTube']]);
        $this->actingAs($this->userWithPermissions(['marketing.settings']));

        foreach (['instagram', 'meta', 'google', 'other'] as $key) {
            $this->configure($company, $key, $key === 'other' ? ['network' => 'TikTok'] : [])->assertSessionHasNoErrors();
        }

        $this->actingAs($this->userWithPermissions(['marketing.campaigns']));
        foreach (range(1, 2) as $attempt) {
            $this->link($company, $campaign, ['instagram', 'meta', 'google', 'other'])
                ->assertRedirect(route('marketing.index', ['company_id' => $company->id, 'campaign_id' => $campaign->id, 'view' => 'channels']))
                ->assertSessionHasNoErrors();
        }

        $this->assertSame(['Instagram', 'LinkedIn', 'Meta Ads', 'Google Ads', 'TikTok'], $campaign->fresh()->social_networks);
        $this->assertSame('Instagram, LinkedIn, Meta Ads, Google Ads, TikTok', $campaign->fresh()->source);
        $this->assertSame(['Facebook'], $otherCampaign->fresh()->social_networks);
        $this->assertSame(['YouTube'], $externalCampaign->fresh()->social_networks);
    }

    public function test_channels_cannot_be_linked_to_a_campaign_from_a_different_company(): void
    {
        $company = $this->company();
        $otherCompany = $this->company(['name' => 'Otra empresa']);
        $foreignCampaign = $this->campaign($otherCompany);
        $this->actingAs($this->userWithPermissions(['marketing.settings', 'marketing.campaigns']));
        $this->configure($company, 'instagram')->assertSessionHasNoErrors();

        $this->link($company, $foreignCampaign, ['instagram'])
            ->assertSessionHasErrorsIn('campaignChannels', 'campaign_id');

        $this->assertSame([], $foreignCampaign->fresh()->social_networks);
    }

    public function test_unknown_unconfigured_and_other_company_channels_are_rejected_without_partial_updates(): void
    {
        $company = $this->company();
        $otherCompany = $this->company(['name' => 'Otra empresa']);
        $campaign = $this->campaign($company, ['social_networks' => ['LinkedIn']]);
        $this->actingAs($this->userWithPermissions(['marketing.settings', 'marketing.campaigns']));
        $this->configure($company, 'instagram')->assertSessionHasNoErrors();
        $this->configure($otherCompany, 'facebook')->assertSessionHasNoErrors();

        foreach ([['unknown'], ['instagram', 'meta'], ['instagram', 'facebook'], []] as $keys) {
            $this->link($company, $campaign, $keys)->assertSessionHasErrorsIn('campaignChannels');
            $this->assertSame(['LinkedIn'], $campaign->fresh()->social_networks);
        }
    }

    public function test_whatsapp_can_only_be_linked_when_an_active_cloud_configuration_exists(): void
    {
        $company = $this->company();
        $campaign = $this->campaign($company);
        $this->actingAs($this->userWithPermissions(['marketing.campaigns', 'marketing.dashboard']));

        $this->link($company, $campaign, ['whatsapp'])->assertSessionHasErrorsIn('campaignChannels');
        $number = $company->whatsappNumbers()->create(['phone_number' => '+525500000011', 'status' => 'active']);
        $this->link($company, $campaign, ['whatsapp'])->assertSessionHasErrorsIn('campaignChannels');
        $number->update(['meta_phone_number_id' => '12345678901', 'meta_waba_id' => '98765432101']);
        $this->link($company, $campaign, ['whatsapp'])->assertSessionHasErrorsIn('campaignChannels');
        $number->update(['meta_access_token' => 'private-whatsapp-channel-token', 'status' => 'inactive']);
        $this->link($company, $campaign, ['whatsapp'])->assertSessionHasErrorsIn('campaignChannels');
        $this->assertSame([], $campaign->fresh()->social_networks);

        $number->update(['status' => 'active']);
        $this->link($company, $campaign, ['whatsapp'])->assertRedirect()->assertSessionDoesntHaveErrors([], null, 'campaignChannels');
        $this->assertSame(['WhatsApp'], $campaign->fresh()->social_networks);
        $this->get(route('marketing.index', ['company_id' => $company->id]))
            ->assertOk()
            ->assertDontSee('private-whatsapp-channel-token', false)
            ->assertDontSee($number->fresh()->getRawOriginal('meta_access_token'), false)
            ->assertDontSee($company->api_token, false);
    }

    public function test_whatsapp_configuration_returns_to_the_dashboard_only_when_requested(): void
    {
        $company = $this->company();
        $campaign = $this->campaign($company);
        $admin = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $this->actingAs($admin);
        $attributes = [
            'company_id' => $company->id,
            'display_name' => 'Atención desde el panel',
            'phone_number' => '+525500000012',
            'meta_phone_number_id' => '12345678902',
            'meta_waba_id' => '98765432102',
            'meta_access_token' => 'private-dashboard-whatsapp-token',
        ];
        $dashboardUrl = route('marketing.index', ['company_id' => $company->id, 'view' => 'channels', 'campaign_id' => $campaign->id]);

        $this->post(route('marketing.conversations.whatsapp.store'), array_merge($attributes, [
            'configuration_channel' => 'whatsapp',
            'return_to' => 'dashboard',
            'campaign_id' => $campaign->id,
        ]))->assertRedirect($dashboardUrl)->assertSessionHasNoErrors();

        $number = $company->whatsappNumbers()->firstOrFail();
        $this->assertSame('private-dashboard-whatsapp-token', $number->meta_access_token);
        $this->get($dashboardUrl)->assertOk()
            ->assertSee('+525500000012')
            ->assertDontSee('private-dashboard-whatsapp-token', false)
            ->assertDontSee($number->getRawOriginal('meta_access_token'), false);

        $this->post(route('marketing.conversations.whatsapp.store'), array_merge($attributes, [
            'display_name' => 'Atención desde conversaciones',
            'meta_access_token' => '',
        ]))->assertRedirect(route('marketing.conversations.index', ['company_id' => $company->id]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('marketing_whatsapp_numbers', 1);
        $this->assertSame('Atención desde conversaciones', $number->fresh()->display_name);
        $this->assertSame('private-dashboard-whatsapp-token', $number->fresh()->meta_access_token);

        $otherCampaign = $this->campaign($this->company(['name' => 'Otra empresa']));
        $this->post(route('marketing.conversations.whatsapp.store'), array_merge($attributes, [
            'return_to' => 'dashboard',
            'campaign_id' => $otherCampaign->id,
            'meta_access_token' => '',
        ]))->assertRedirect(route('marketing.index', ['company_id' => $company->id, 'view' => 'channels', 'campaign_id' => 'all']))
            ->assertSessionHasNoErrors();
    }

    public function test_invalid_account_configuration_reopens_only_its_dialog_with_escaped_old_values(): void
    {
        $company = $this->company();
        $this->actingAs($this->userWithPermissions(['marketing.dashboard', 'marketing.settings']));
        $this->configure($company, 'facebook', ['account_name' => 'Cuenta Facebook guardada'])
            ->assertSessionHasNoErrors();
        $dashboardUrl = route('marketing.index', ['company_id' => $company->id, 'view' => 'channels']);
        $accountName = 'Cuenta "><script>alert("nombre")</script>';
        $accountIdentifier = '@cuenta" autofocus onfocus="alert(1)';
        $invalidUrl = 'javascript:alert("perfil")';

        $this->from($dashboardUrl)->configure($company, 'instagram', [
            'configuration_channel' => 'instagram',
            'account_name' => $accountName,
            'account_identifier' => $accountIdentifier,
            'profile_url' => $invalidUrl,
        ])->assertRedirect($dashboardUrl)
            ->assertSessionHasErrorsIn('channelConfiguration', 'profile_url')
            ->assertSessionHasInput('configuration_channel', 'instagram');

        $response = $this->get($dashboardUrl);
        $response->assertOk()->assertSee(e($accountName), false)->assertDontSee($accountName, false);
        $xpath = $this->dashboardXPath($response);
        $this->assertSame(1, $xpath->query('//dialog[@data-reopen-dialog]')->length);
        $this->assertSame('instagram', $xpath->query('//dialog[@data-reopen-dialog]')->item(0)->getAttribute('data-channel-dialog'));
        $this->assertSame(1, $xpath->query('//dialog[@data-channel-dialog="instagram"]//*[@role="alert"]')->length);

        foreach (['account_name' => $accountName, 'account_identifier' => $accountIdentifier, 'profile_url' => $invalidUrl] as $field => $value) {
            $input = $xpath->query('//dialog[@data-channel-dialog="instagram"]//input[@name="'.$field.'"]')->item(0);
            $this->assertNotNull($input);
            $this->assertSame($value, $input->getAttribute('value'));
            $this->assertFalse($input->hasAttribute('onfocus'));
            $this->assertFalse($input->hasAttribute('autofocus'));
        }

        $this->assertSame(0, $xpath->query('//dialog[@data-channel-dialog="instagram"]//script')->length);
        $this->assertSame('Cuenta Facebook guardada', $xpath->query('//dialog[@data-channel-dialog="facebook"]//input[@name="account_name"]')->item(0)->getAttribute('value'));
        $this->assertArrayNotHasKey('instagram', $company->fresh()->settings['marketing_channels']);
    }

    public function test_reloading_the_dashboard_shows_the_saved_account_and_real_campaign_networks(): void
    {
        $company = $this->company();
        $campaign = $this->campaign($company, ['name' => 'Campaña de captación']);
        $this->actingAs($this->userWithPermissions(['marketing.dashboard', 'marketing.settings', 'marketing.campaigns']));
        $this->configure($company, 'instagram', [
            'account_name' => 'Cuenta persistida de Instagram',
            'account_identifier' => '@empresa-persistida',
            'profile_url' => 'https://instagram.com/empresa-persistida',
        ])->assertSessionHasNoErrors();
        $this->link($company, $campaign, ['instagram'])->assertSessionHasNoErrors();

        $response = $this->get(route('marketing.index', ['company_id' => $company->id, 'campaign_id' => $campaign->id, 'view' => 'channels']));
        $response->assertOk()
            ->assertSee('Cuenta persistida de Instagram')
            ->assertSee('@empresa-persistida')
            ->assertSee('https://instagram.com/empresa-persistida')
            ->assertSee('Campaña de captación')
            ->assertViewHas('campaign', fn (?Campaign $selected): bool => $selected?->is($campaign) && $selected->social_networks === ['Instagram'])
            ->assertDontSee($company->api_token, false);

        $xpath = $this->dashboardXPath($response);
        $card = $xpath->query('//*[@data-marketing-channel-card][contains(., "Instagram")]')->item(0);
        $this->assertNotNull($card);
        $this->assertStringNotContainsString('Conectado', $card->textContent);
    }

    private function dashboardXPath(TestResponse $response): DOMXPath
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<?xml encoding="utf-8" ?>'.$response->getContent());
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
        return new DOMXPath($document);
    }

    private function company(array $attributes = []): Company
    {
        return Company::create(array_merge(['name' => 'Empresa de canales', 'active' => true], $attributes));
    }

    private function campaign(Company $company, array $attributes = []): Campaign
    {
        return Campaign::create(array_merge([
            'company_id' => $company->id,
            'name' => 'Campaña de prueba',
            'status' => 'active',
            'social_networks' => [],
        ], $attributes));
    }

    private function userWithPermissions(array $permissions): User
    {
        return User::factory()->create(['role' => 'buyer', 'active' => true, 'menu_permissions' => $permissions]);
    }

    private function configure(Company $company, string $channel, array $attributes = []): TestResponse
    {
        return $this->post(route('marketing.settings.channels.update', ['channel' => $channel]), array_merge([
            'company_id' => $company->id,
            'account_name' => 'Cuenta '.$channel,
            'account_identifier' => 'cuenta-'.$channel,
            'profile_url' => null,
        ], $attributes));
    }

    private function link(Company $company, Campaign $campaign, array $channels): TestResponse
    {
        return $this->post(route('marketing.campaigns.channels.update'), [
            'company_id' => $company->id,
            'campaign_id' => $campaign->id,
            'channels' => $channels,
        ]);
    }
}
