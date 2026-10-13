<?php

namespace Tests\Feature;

use App\Models\AiAgent;
use App\Models\Company;
use App\Models\ConstructionProject;
use App\Models\Provider;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Support\NavigationPermissionCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceExpandedNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_menu_matches_the_administrator_groups_without_superadministrator_tools(): void
    {
        $user = User::factory()->create([
            'role' => 'finance',
            'active' => true,
            'menu_permissions' => null,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertSeeInOrder([
                '<summary>Finanzas</summary>',
                '<summary>Compras y Suministros</summary>',
                '<summary>Almacenes e Inventarios</summary>',
                '<summary>Servicios</summary>',
                '<summary>Recursos Humanos</summary>',
                '<summary>Administracion de obra</summary>',
                '<summary>Administracion de Plazas</summary>',
                '<summary>Contratos Gobierno</summary>',
                '<summary>Marketing</summary>',
                '<summary>Seguridad y Vigilancia</summary>',
            ], false)
            ->assertSee('class="nav-box nav-box-oc"', false)
            ->assertSee('href="'.route('finance.agents.index').'"', false)
            ->assertSee('href="'.route('superadmin.plazas.agents.index').'"', false)
            ->assertDontSee('href="'.route('superadmin.users.index').'"', false);

        $this->assertEqualsCanonicalizing(
            NavigationPermissionCatalog::financeRolePermissions(),
            $user->navigationPermissions()
        );
    }

    public function test_finance_can_open_the_requested_modules_and_their_embedded_views(): void
    {
        $user = User::factory()->create([
            'role' => 'finance',
            'active' => true,
            'menu_permissions' => null,
        ]);

        $routes = [
            route('finance.orders.active'),
            route('buyer.orders.create'),
            route('inventory.orders.index'),
            route('services.catalog'),
            route('human-resources.show', 'empleados'),
            route('human-resources.embed', 'empleados'),
            route('construction.dashboard'),
            route('construction.projects.create'),
            route('superadmin.plazas.index'),
            route('superadmin.government-contracts.index'),
            route('marketing.index'),
            route('security.index'),
            route('finance.agents.index'),
            route('superadmin.plazas.agents.index'),
        ];

        foreach ($routes as $url) {
            $this->actingAs($user)->get($url)->assertOk();
        }

        $this->actingAs($user)->get(route('superadmin.users.index'))->assertForbidden();
    }

    public function test_existing_finance_accounts_receive_the_expanded_permissions_without_changing_other_roles(): void
    {
        $finance = User::factory()->create([
            'role' => 'finance',
            'menu_permissions' => ['finance.orders.active'],
        ]);
        $assistant = User::factory()->create([
            'role' => 'administrative_assistant',
            'menu_permissions' => ['services.catalog'],
        ]);

        $migration = require database_path('migrations/2026_10_12_000001_expand_finance_navigation_permissions.php');
        $migration->up();

        $this->assertEqualsCanonicalizing(
            NavigationPermissionCatalog::financeRolePermissions(),
            $finance->fresh()->menu_permissions
        );
        $this->assertSame(['services.catalog'], $assistant->fresh()->menu_permissions);
    }

    public function test_finance_can_see_all_projects_and_companies_in_the_operational_modules(): void
    {
        $finance = User::factory()->create([
            'role' => 'finance', 'active' => true, 'menu_permissions' => null,
        ]);
        $company = Company::query()->create([
            'name' => 'Empresa para compras',
            'rfc' => 'EPC261012001',
        ]);

        $this->assertSame(
            ConstructionProject::query()->count(),
            ConstructionProject::query()->visibleTo($finance)->count()
        );
        $this->assertTrue($finance->canManageAllRecordsIn('procurement'));
        $this->assertTrue($finance->canManageAllRecordsIn('inventory'));

        $this->actingAs($finance)->get(route('buyer.orders.create'))
            ->assertOk()
            ->assertSee($company->name);
    }

    public function test_finance_can_create_an_agent_in_its_own_center_but_not_access_superadministrator_tools(): void
    {
        $finance = User::factory()->create([
            'role' => 'finance', 'active' => true, 'menu_permissions' => null,
        ]);

        $page = $this->actingAs($finance)->get(route('finance.agents.index'));
        $page->assertOk()
            ->assertSee('action="'.route('finance.agents.store').'"', false)
            ->assertDontSee('action="'.route('superadmin.agents.store').'"', false);

        $this->actingAs($finance)->post(route('finance.agents.store'), [
            'context' => 'finanzas',
            'agent_form' => '1',
            'name' => 'Agente de Finanzas',
        ])->assertRedirect();

        $agent = AiAgent::query()->sole();
        $this->assertSame($finance->id, $agent->created_by);

        $this->actingAs($finance)->patch(route('finance.agents.toggle', $agent), [
            'context' => 'finanzas',
            'is_active' => '1',
        ])->assertRedirect();
        $this->assertTrue($agent->fresh()->is_active);

        $this->actingAs($finance)->get(route('superadmin.agents.index'))->assertForbidden();
        $this->actingAs($finance)->post(route('finance.agents.store'), [
            'context' => 'superadmin',
            'agent_form' => '1',
            'name' => 'Agente no autorizado',
        ])->assertForbidden();
    }

    public function test_finance_can_view_and_edit_purchase_orders_created_by_another_buyer(): void
    {
        $finance = User::factory()->create([
            'role' => 'finance', 'active' => true, 'menu_permissions' => null,
        ]);
        $buyer = User::factory()->create(['role' => 'buyer', 'active' => true]);
        $company = Company::query()->create([
            'name' => 'Empresa compartida', 'rfc' => 'ECO261012001',
        ]);
        $provider = Provider::query()->create([
            'buyer_id' => $buyer->id,
            'business_name' => 'Proveedor compartido',
            'rfc' => 'PCO261012001',
            'business_line' => 'Materiales',
            'bank' => 'Banco',
            'account_number' => '1234567890',
            'clabe' => '123456789012345678',
        ]);
        $order = PurchaseOrder::query()->create([
            'folio' => 'OC-901',
            'buyer_id' => $buyer->id,
            'company_id' => $company->id,
            'provider_id' => $provider->id,
            'created_on' => '2026-10-12',
            'due_date' => '2026-10-20',
            'delivery_date' => '2026-10-19',
            'status' => 'draft',
        ]);

        $this->actingAs($finance)->get(route('buyer.orders.index'))
            ->assertOk()
            ->assertSee($order->folio);
        $this->actingAs($finance)->get(route('buyer.orders.edit', $order))
            ->assertOk()
            ->assertSee($provider->business_name);

        $this->actingAs($buyer)->get(route('buyer.orders.edit', $order))->assertOk();
    }

    public function test_employee_biometric_media_requires_the_employee_permission(): void
    {
        $finance = User::factory()->create([
            'role' => 'finance', 'active' => true, 'menu_permissions' => null,
        ]);
        $limited = User::factory()->create([
            'role' => 'administrative_assistant',
            'active' => true,
            'menu_permissions' => ['human_resources.dashboard'],
        ]);

        $this->actingAs($finance)->getJson(route('human-resources.biometric-media.index'))
            ->assertOk();
        $this->actingAs($limited)->getJson(route('human-resources.biometric-media.index'))
            ->assertForbidden();
    }

    public function test_a_restricted_finance_menu_keeps_its_visible_box_well_formed(): void
    {
        $finance = User::factory()->create([
            'role' => 'finance',
            'active' => true,
            'menu_permissions' => [NavigationPermissionCatalog::HOME_DASHBOARD, 'finance.orders.history'],
        ]);

        $response = $this->actingAs($finance)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('class="nav-box nav-box-oc"', false)
            ->assertSee('Ordenes de compra')
            ->assertSee('href="'.route('finance.orders.history').'"', false)
            ->assertDontSee('href="'.route('finance.orders.active').'"', false);
    }
}
