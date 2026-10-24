<?php

namespace Tests\Feature;

use App\Models\Marketing\Campaign;
use App\Models\Marketing\Company;
use App\Models\Marketing\Conversation;
use App\Models\Marketing\Lead;
use App\Models\User;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class MarketingDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_opens_with_channels_and_preserves_favorites_and_the_full_company_picker(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $favorite = Company::create(['name' => 'Empresa favorita', 'active' => true, 'is_favorite' => true]);
        $selected = Company::create(['name' => 'Empresa seleccionada', 'active' => true]);

        $response = $this->actingAs($admin)->get(route('marketing.index', ['company_id' => $selected->id]));

        $response->assertOk()
            ->assertViewHas('dashboardView', 'channels')
            ->assertViewHas('company', fn (?Company $company) => $company?->is($selected))
            ->assertSee('data-marketing-favorite-id="'.$favorite->id.'"', false)
            ->assertDontSee('data-marketing-favorite-id="'.$selected->id.'"', false)
            ->assertDontSee('Marketing de '.$selected->name)
            ->assertDontSee('Configura los canales de tu empresa y vincúlalos con tus campañas de marketing.');

        $xpath = $this->dashboardXPath($response);
        $this->assertSame(8, $xpath->query('//*[@data-marketing-dashboard]//*[@data-marketing-channel-card]')->length);
        $this->assertSame(1, $xpath->query('//*[@data-marketing-dashboard]//*[@data-marketing-channel-search]')->length);
        $this->assertSame(1, $xpath->query('//select[@id="marketing-company-select"]/option[@value="all"]')->length);
        $this->assertSame(1, $xpath->query('//select[@id="marketing-company-select"]/option[@value="'.$selected->id.'"][@selected]')->length);
        $this->assertSame(1, $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " marketing-dashboard-header ")]/*[contains(concat(" ", normalize-space(@class), " "), " marketing-dashboard-tabs ")]')->length);
        $this->assertSame(1, $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " marketing-dashboard-header ")]/*[contains(concat(" ", normalize-space(@class), " "), " marketing-dashboard-actions ")]')->length);
        $this->assertSame(0, $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " marketing-dashboard-tabs ")]//a[contains(normalize-space(.), "Campañas")]')->length);
        $this->assertSame(0, $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " marketing-dashboard-tabs ")]//a[contains(normalize-space(.), "Conversaciones")]')->length);
        $this->assertSame(0, $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " marketing-settings-nav ")]//a[contains(normalize-space(.), "Usuarios y permisos")]')->length);

        $summaryLinks = array_filter($this->dashboardLinks($xpath), function (array $link): bool {
            return $link['path'] === parse_url(route('marketing.index'), PHP_URL_PATH)
                && ($link['query']['view'] ?? null) === 'summary';
        });
        $this->assertNotEmpty($summaryLinks, 'El resumen debe ser accesible desde el panel.');
    }

    public function test_dashboard_links_respect_each_navigation_permission(): void
    {
        $company = Company::create(['name' => 'Empresa con permisos', 'active' => true]);
        $permissions = [
            'marketing.campaigns' => 'marketing.campaigns.index',
            'marketing.conversations' => 'marketing.conversations.index',
            'marketing.reports' => 'marketing.reports.index',
            'marketing.settings' => 'marketing.settings.index',
            'marketing.users' => 'marketing.users.index',
        ];

        foreach ([null, ...array_keys($permissions)] as $grantedPermission) {
            $user = User::factory()->create([
                'role' => 'buyer',
                'active' => true,
                'menu_permissions' => array_filter(['marketing.dashboard', $grantedPermission]),
            ]);

            $response = $this->actingAs($user)->get(route('marketing.index', ['company_id' => $company->id]));
            $response->assertOk();
            $links = $this->dashboardLinks($this->dashboardXPath($response));

            foreach ($permissions as $permission => $routeName) {
                $path = parse_url(route($routeName), PHP_URL_PATH);
                $matchingLinks = array_filter($links, fn (array $link): bool => $link['path'] === $path || str_starts_with($link['path'], $path.'/'));

                if ($grantedPermission === $permission) {
                    $this->assertNotEmpty($matchingLinks, 'Falta el acceso autorizado a '.$permission.'.');
                } else {
                    $this->assertEmpty($matchingLinks, 'El panel muestra un acceso sin permiso a '.$permission.'.');
                }
            }
        }
    }

    public function test_empty_and_all_company_scopes_do_not_offer_company_specific_connections(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $this->actingAs($admin);

        $this->assertNoCompanyConnection($this->get(route('marketing.index')));

        $company = Company::create(['name' => 'Empresa disponible', 'active' => true]);
        $this->get(route('marketing.index', ['company_id' => $company->id]))->assertOk();

        $response = $this->get(route('marketing.index', ['company_id' => 'all']));
        $response->assertSessionHas('marketing_company_scope', 'all');
        $this->assertNoCompanyConnection($response);

        $this->assertNoCompanyConnection($this->get(route('marketing.index')));
    }

    public function test_whatsapp_configuration_is_distinct_from_received_messages_and_never_exposes_credentials(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = Company::create(['name' => 'Empresa WhatsApp', 'active' => true]);
        $number = $company->whatsappNumbers()->create([
            'phone_number' => '+525500000001',
            'meta_phone_number_id' => '12345678901',
            'meta_waba_id' => '98765432101',
            'meta_access_token' => 'dashboard-private-meta-token',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('marketing.index', ['company_id' => $company->id]));
        $response->assertOk()
            ->assertViewHas('whatsappConfiguredCount', 1)
            ->assertViewHas('whatsappReceivedCount', 0)
            ->assertDontSee('dashboard-private-meta-token', false)
            ->assertDontSee($number->getRawOriginal('meta_access_token'), false);

        $xpath = $this->dashboardXPath($response);
        $cards = $xpath->query('//*[@data-marketing-dashboard]//*[@data-marketing-channel-card][contains(., "WhatsApp Business")]');
        $this->assertSame(1, $cards->length);
        $this->assertStringContainsString('Configurado', $cards->item(0)->textContent);
        $this->assertStringNotContainsString('Conectado', $cards->item(0)->textContent);

        $number->update(['last_webhook_at' => now()]);

        $this->get(route('marketing.index', ['company_id' => $company->id]))
            ->assertOk()
            ->assertViewHas('whatsappConfiguredCount', 1)
            ->assertViewHas('whatsappReceivedCount', 1)
            ->assertDontSee('dashboard-private-meta-token', false);
    }

    public function test_internal_whatsapp_numbers_do_not_count_as_cloud_connections(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = Company::create(['name' => 'Empresa sin conexión', 'active' => true]);
        $company->whatsappNumbers()->create([
            'phone_number' => '+525500000002',
            'provider_phone_id' => '12345678902',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('marketing.index', ['company_id' => $company->id]));
        $response->assertOk()
            ->assertViewHas('whatsappConfiguredCount', 0)
            ->assertViewHas('whatsappReceivedCount', 0);

        $xpath = $this->dashboardXPath($response);
        $card = $xpath->query('//*[@data-marketing-dashboard]//*[@data-marketing-channel-card][contains(., "WhatsApp Business")]')->item(0);
        $this->assertNotNull($card);
        $this->assertStringNotContainsString('Configurado', $card->textContent);
        $this->assertStringNotContainsString('Conectado', $card->textContent);
    }

    public function test_summary_uses_real_metrics_filtered_by_the_selected_company_and_campaign(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = Company::create(['name' => 'Empresa resumen', 'active' => true]);
        $otherCompany = Company::create(['name' => 'Otra empresa', 'active' => true]);
        $campaign = Campaign::create(['company_id' => $company->id, 'name' => 'Campaña seleccionada', 'status' => 'active']);
        $otherCampaign = Campaign::create(['company_id' => $company->id, 'name' => 'Otra campaña', 'status' => 'active']);
        $lead = Lead::create([
            'company_id' => $company->id, 'campaign_id' => $campaign->id,
            'name' => 'Lead convertido', 'phone' => '+525500000003', 'status' => 'convertido', 'estimated_value' => 1234.50,
        ]);
        Lead::create([
            'company_id' => $company->id, 'campaign_id' => $campaign->id,
            'name' => 'Lead nuevo', 'phone' => '+525500000004', 'status' => 'nuevo',
        ]);
        Lead::create([
            'company_id' => $company->id, 'campaign_id' => $otherCampaign->id,
            'name' => 'Lead otra campaña', 'phone' => '+525500000005', 'status' => 'convertido', 'estimated_value' => 9000,
        ]);
        Lead::create([
            'company_id' => $otherCompany->id,
            'name' => 'Lead otra empresa', 'phone' => '+525500000006', 'status' => 'convertido', 'estimated_value' => 8000,
        ]);
        Conversation::create([
            'company_id' => $company->id, 'campaign_id' => $campaign->id,
            'lead_id' => $lead->id, 'status' => 'open', 'last_message_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('marketing.index', [
            'company_id' => $company->id, 'campaign_id' => $campaign->id, 'view' => 'summary',
        ]));

        $response->assertOk()
            ->assertViewHas('dashboardView', 'summary')
            ->assertViewHas('metrics', fn (array $metrics): bool => $metrics['leads'] === 2
                && $metrics['active_conversations'] === 1
                && $metrics['unassigned'] === 1
                && $metrics['converted'] === 1
                && (float) $metrics['estimated_sales'] === 1234.50)
            ->assertSee('Campañas de '.$company->name)
            ->assertSee('Todas las campañas')
            ->assertSee('Campaña seleccionada')
            ->assertDontSee('2m 45s')
            ->assertDontSee('12.5%');

        $xpath = $this->dashboardXPath($response);
        $text = $xpath->query('//*[@data-marketing-dashboard]')->item(0)->textContent;
        $this->assertStringContainsString('Leads', $text);
        $this->assertStringContainsString('Convertidos', $text);
        $this->assertStringContainsString('Lead convertido', $text);
        $this->assertStringNotContainsString('Lead otra empresa', $text);
    }

    private function assertNoCompanyConnection(TestResponse $response): void
    {
        $response->assertOk()->assertViewHas('company', null);
        $xpath = $this->dashboardXPath($response);

        foreach ($this->dashboardLinks($xpath) as $link) {
            if (in_array($link['path'], [
                parse_url(route('marketing.conversations.index'), PHP_URL_PATH),
                parse_url(route('marketing.settings.index'), PHP_URL_PATH),
            ], true)) {
                $this->assertContains($link['query']['company_id'] ?? null, [null, 'all']);
            }
        }

        $connectionAction = route('marketing.conversations.whatsapp.store');
        $this->assertSame(0, $xpath->query('//*[@data-marketing-dashboard]//form[@action="'.$connectionAction.'"]')->length);
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

        $xpath = new DOMXPath($document);
        $this->assertSame(1, $xpath->query('//*[@data-marketing-dashboard]')->length);

        return $xpath;
    }

    private function dashboardLinks(DOMXPath $xpath): array
    {
        $links = [];

        foreach ($xpath->query('//*[@data-marketing-dashboard]//a[@href]') as $anchor) {
            /** @var DOMElement $anchor */
            $href = $anchor->getAttribute('href');
            $query = [];
            parse_str(parse_url($href, PHP_URL_QUERY) ?: '', $query);
            $links[] = ['path' => parse_url($href, PHP_URL_PATH) ?: '', 'query' => $query];
        }

        return $links;
    }
}
