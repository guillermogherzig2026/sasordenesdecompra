<?php

namespace Tests\Feature;

use App\Models\Marketing\Campaign;
use App\Models\Marketing\Company as MarketingCompany;
use App\Models\Marketing\Conversation;
use App\Models\Marketing\Lead;
use App\Models\Marketing\Message;
use App\Models\Marketing\User as MarketingUser;
use App\Models\User;
use Database\Seeders\MarketingLeadFlowSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_open_marketing_from_the_sidebar(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $response = $this->actingAs($superAdmin)->get(route('marketing.index'));

        $response->assertOk();
        $response->assertSee('Marketing');
        $response->assertSee('href="'.route('marketing.index').'"', false);
        $response->assertSee('Empresas');
        $response->assertSee('Campañas');
        $response->assertSee('Conversaciones');
        $response->assertSeeInOrder([
            'Contratos Gobierno',
            'Marketing',
            'Seguridad y Vigilancia',
        ]);
    }

    public function test_marketing_access_respects_navigation_permissions(): void
    {
        $authorizedUser = User::factory()->create([
            'role' => 'buyer',
            'active' => true,
            'menu_permissions' => ['marketing.dashboard'],
        ]);
        $unauthorizedUser = User::factory()->create([
            'role' => 'buyer',
            'active' => true,
            'menu_permissions' => [],
        ]);

        $this->actingAs($authorizedUser)
            ->get(route('marketing.index'))
            ->assertOk()
            ->assertSee('Marketing');

        $this->actingAs($authorizedUser)
            ->get(route('marketing.companies.index'))
            ->assertForbidden();

        $this->actingAs($unauthorizedUser)
            ->get(route('marketing.index'))
            ->assertForbidden();
    }

    public function test_marketing_requires_authentication(): void
    {
        $this->get(route('marketing.index'))
            ->assertRedirect(route('login'));
    }

    public function test_superadmin_can_open_every_marketing_workspace_screen(): void
    {
        $this->seed(MarketingLeadFlowSeeder::class);
        $superAdmin = User::factory()->create(['role' => 'superadmin', 'active' => true]);

        $screens = [
            'marketing.index' => 'Empresa',
            'marketing.companies.index' => 'Empresas',
            'marketing.campaigns.index' => 'Campañas',
            'marketing.leads.index' => 'Leads',
            'marketing.conversations.index' => 'Conversaciones',
            'marketing.reports.index' => 'Reportes',
            'marketing.users.index' => 'Usuarios',
            'marketing.settings.index' => 'Configuración',
        ];

        foreach ($screens as $routeName => $heading) {
            $this->actingAs($superAdmin)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee($heading, false);
        }
    }

    public function test_primary_marketing_screens_share_the_company_and_campaign_selection_carousels(): void
    {
        $this->seed(MarketingLeadFlowSeeder::class);
        $superAdmin = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = MarketingCompany::query()->where('name', 'Zen Dragon Labs')->firstOrFail();
        $company->update(['is_favorite' => true]);
        $campaign = Campaign::query()->where('company_id', $company->id)->firstOrFail();

        foreach ([
            'marketing.index',
            'marketing.campaigns.index',
            'marketing.leads.index',
            'marketing.conversations.index',
        ] as $routeName) {
            $this->actingAs($superAdmin)
                ->get(route($routeName, [
                    'company_id' => $company->id,
                    'campaign_id' => $campaign->id,
                ]))
                ->assertOk()
                ->assertSeeInOrder([
                    'Empresas favoritas',
                    'data-marketing-favorite-id="'.$company->id.'"',
                    'Todas las empresas',
                    'Campañas de '.$company->name,
                    'Nueva campaña',
                    'Todas las campañas',
                    $campaign->name,
                ]);
        }

        $this->get(route('marketing.reports.index', ['company_id' => $company->id, 'campaign_id' => $campaign->id]))
            ->assertOk()
            ->assertSee('Empresa seleccionada');
    }

    public function test_application_seeder_populates_the_isolated_marketing_tables(): void
    {
        $this->seed();

        $this->assertDatabaseCount('marketing_companies', 6);
        $this->assertDatabaseHas('marketing_campaigns', ['name' => 'Promos Mayo']);
        $this->assertDatabaseHas('marketing_leads', ['source' => 'WhatsApp']);
    }

    public function test_authorized_user_can_create_a_lead_and_its_conversation(): void
    {
        $this->seed(MarketingLeadFlowSeeder::class);
        $company = MarketingCompany::query()->firstOrFail();
        $campaign = Campaign::query()->where('company_id', $company->id)->firstOrFail();
        $advisor = MarketingUser::query()->where('company_id', $company->id)->firstOrFail();
        $user = User::factory()->create([
            'role' => 'buyer',
            'active' => true,
            'menu_permissions' => ['marketing.leads'],
        ]);

        $this->actingAs($user)
            ->post(route('marketing.leads.store'), [
                'company_id' => $company->id,
                'campaign_id' => $campaign->id,
                'advisor_id' => $advisor->id,
                'name' => 'Lead de integracion',
                'phone' => '+52 55 1111 2222',
                'email' => 'lead.integracion@example.com',
                'status' => 'nuevo',
                'estimated_value' => 12500,
            ])
            ->assertRedirect(route('marketing.leads.index', [
                'company_id' => $company->id,
                'campaign_id' => $campaign->id,
            ]));

        $lead = Lead::query()->where('email', 'lead.integracion@example.com')->firstOrFail();
        $this->assertDatabaseHas('marketing_conversations', ['lead_id' => $lead->id]);
    }

    public function test_authorized_user_can_add_a_message_without_leaving_marketing(): void
    {
        $this->seed(MarketingLeadFlowSeeder::class);
        $conversation = Conversation::query()->firstOrFail();
        $user = User::factory()->create([
            'name' => 'Operador Comercial',
            'email' => 'operador@example.com',
            'role' => 'buyer',
            'active' => true,
            'menu_permissions' => ['marketing.conversations'],
        ]);

        $this->actingAs($user)
            ->post(route('marketing.conversations.messages.store', $conversation), [
                'body' => 'Mensaje de seguimiento desde el modulo integrado.',
            ])
            ->assertRedirect(route('marketing.conversations.index', ['conversation' => $conversation->id]));

        $this->assertTrue(Message::query()
            ->where('conversation_id', $conversation->id)
            ->where('body', 'Mensaje de seguimiento desde el modulo integrado.')
            ->exists());
        $this->assertDatabaseHas('marketing_users', [
            'name' => 'Operador Comercial',
            'company_id' => $conversation->company_id,
        ]);
    }
}
