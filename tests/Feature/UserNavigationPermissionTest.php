<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Support\NavigationPermissionCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserNavigationPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorizations_page_shows_every_navigation_category_and_its_submenus(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $response = $this->actingAs($superAdmin)->get(route('finance.admin.users'));

        $response->assertOk();
        $response->assertSee('Selecciona un menú');
        $response->assertSee('Autoriza los submenús de');
        $response->assertSee('Define las empresas y los almacenes');
        $response->assertSee('data-authorization-view-tabs', false);
        $response->assertSee('data-initial-view="create"', false);
        $response->assertSee('data-authorization-view-target="create"', false);
        $response->assertSee('data-authorization-view-target="users"', false);
        $response->assertSee('Nuevo usuario');
        $response->assertSee('Ver usuarios');
        $response->assertSee('data-role-navigation-manager', false);
        $response->assertSee('aria-controls="user-authorization-create-content"', false);
        $response->assertSee('aria-controls="authorized-users-content"', false);
        $response->assertSee('data-collapsible-toggle', false);
        $response->assertSee('data-navigation-carousel', false);
        $response->assertSee('data-navigation-category-button="finance"', false);
        $response->assertSee('Inicio');
        $response->assertSee('value="'.NavigationPermissionCatalog::HOME_DASHBOARD.'"', false);
        $response->assertDontSee('name="role"', false);
        $response->assertDontSee('name="buyer_subroles[]"', false);
        $response->assertDontSee('Rol del usuario');
        $response->assertDontSee('Subcategoría operativa');
        $response->assertSee('data-navigation-subcategory="construction.obras"', false);
        $response->assertSee('data-navigation-subcategory="construction.operation"', false);
        $response->assertSee('data-navigation-subcategory="construction.administration"', false);
        $response->assertSee('Finanzas');
        $response->assertSee('Compras y Suministros');
        $response->assertSee('Almacenes e Inventarios');
        $response->assertSee('Servicios');
        $response->assertSee('Recursos Humanos');
        $this->assertSame([
            'human_resources.dashboard' => 'Inicio',
            'human_resources.candidates' => 'Registro de candidatos',
            'human_resources.contracts' => 'Contratos',
            'human_resources.employees' => 'Empleados',
            'human_resources.pending_approvals' => 'Pendientes de Aprobación',
            'human_resources.payroll' => 'Nómina',
            'human_resources.overtime' => 'Horas extras',
            'human_resources.reports' => 'Reportes',
            'human_resources.configuration' => 'Configuración',
            'human_resources.managers_branches' => 'Gerentes y Sucursales',
        ], NavigationPermissionCatalog::categories()['human_resources']['items']);
        $response->assertSee('value="human_resources.candidates"', false);
        $response->assertSee('value="human_resources.contracts"', false);
        $response->assertSee('value="human_resources.employees"', false);
        $response->assertSee('value="human_resources.pending_approvals"', false);
        $response->assertSee('value="human_resources.payroll"', false);
        $response->assertSee('value="human_resources.overtime"', false);
        $response->assertSee('value="human_resources.reports"', false);
        $response->assertSee('value="human_resources.configuration"', false);
        $response->assertSee('value="human_resources.managers_branches"', false);
        $response->assertSee('Administracion de obra');
        $response->assertSee('Administracion de Plazas');
        $response->assertSee('Contratos Gobierno');
        $response->assertSee('Seguridad y Vigilancia');
        $response->assertSee('value="plazas.administration"', false);
        $response->assertSee('value="plazas.contracts"', false);
        $response->assertSee('value="plazas.marketplace"', false);
        $response->assertSee('value="plazas.properties"', false);
        $response->assertSee('value="plazas.users"', false);
        $response->assertSee('value="plazas.tenants"', false);
        $response->assertSee('value="government_contracts.dashboard"', false);
        $response->assertSee('value="government_contracts.configuration"', false);
        $response->assertSee('value="security.dashboard"', false);
        $response->assertSee('value="security.branches"', false);
        $response->assertSee('value="security.cameras"', false);
        $response->assertDontSee('value="security.visualization"', false);
        $response->assertSee('value="security.analytics"', false);
        $response->assertSee('value="security.alerts"', false);
        $response->assertSee('value="security.users"', false);
        $response->assertSee('value="security.reports"', false);
        $response->assertSee('value="security.configuration"', false);
    }

    public function test_selected_navigation_permissions_are_saved_for_a_user(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $permissions = [
            'services.catalog',
            'plazas.administration',
            'human_resources.dashboard',
        ];

        $response = $this->actingAs($superAdmin)->post(route('finance.admin.users.store'), [
            'name' => 'Usuario con menus',
            'email' => 'menus@example.com',
            'password' => 'secret12',
            'menu_permissions_configured' => '1',
            'menu_permissions' => $permissions,
        ]);

        $response->assertRedirect(route('finance.admin.users'));

        $user = User::where('email', 'menus@example.com')->firstOrFail();
        $this->assertSame('administrative_assistant', $user->role);
        $this->assertSame($permissions, $user->menu_permissions);
        $this->assertSame($permissions, $user->navigationPermissions());
    }

    public function test_operational_profile_is_derived_from_selected_submenus(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $response = $this->actingAs($superAdmin)->post(route('finance.admin.users.store'), [
            'name' => 'Usuario de suministros y reembolsos',
            'email' => 'operativo@example.com',
            'password' => 'secret12',
            'menu_permissions_configured' => '1',
            'menu_permissions' => [
                'procurement.supply.pending',
                'procurement.reimbursements.history',
            ],
        ]);

        $response->assertRedirect(route('finance.admin.users'));

        $user = User::where('email', 'operativo@example.com')->firstOrFail();
        $this->assertSame('buyer', $user->role);
        $this->assertSame(['supplies', 'reimbursements'], $user->buyerSubroles());
    }

    public function test_selected_company_and_warehouse_scope_is_saved_for_an_administrative_user(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $selectedCompany = Company::create([
            'name' => 'Empresa autorizada',
            'rfc' => 'EAU260831001',
            'warehouses' => [
                ['name' => 'Almacén Norte', 'short_name' => 'Norte'],
                ['name' => 'Almacén Sur', 'short_name' => 'Sur'],
            ],
        ]);
        Company::create([
            'name' => 'Empresa restringida',
            'rfc' => 'ERE260831002',
            'warehouses' => [['name' => 'Almacén Central', 'short_name' => 'Central']],
        ]);

        $response = $this->actingAs($superAdmin)->post(route('finance.admin.users.store'), [
            'name' => 'Usuario con alcance',
            'email' => 'alcance@example.com',
            'password' => 'secret12',
            'menu_permissions_configured' => '1',
            'menu_permissions' => ['security.dashboard'],
            'companies' => [(string) $selectedCompany->id],
            'warehouses' => [
                (string) $selectedCompany->id => ['Almacén Norte'],
            ],
        ]);

        $response->assertRedirect(route('finance.admin.users'));

        $user = User::where('email', 'alcance@example.com')->firstOrFail();
        $this->assertSame([
            ['name' => 'Empresa autorizada', 'warehouses' => ['Almacén Norte']],
        ], $user->normalizedCompanyAssignments());
    }

    public function test_permissions_filter_the_sidebar_and_protect_direct_urls(): void
    {
        $user = User::factory()->create([
            'role' => 'administrative_assistant',
            'active' => true,
            'menu_permissions' => [NavigationPermissionCatalog::HOME_DASHBOARD, 'services.catalog', 'plazas.administration'],
        ]);

        $dashboard = $this->actingAs($user)->get(route('dashboard'));

        $dashboard->assertOk();
        $dashboard->assertSee('href="'.route('services.catalog').'"', false);
        $dashboard->assertSee('href="'.route('superadmin.plazas.administration').'"', false);
        $dashboard->assertDontSee('href="'.route('finance.orders.active').'"', false);
        $dashboard->assertDontSee('href="'.route('superadmin.plazas.index').'"', false);
        $dashboard->assertDontSee('href="'.route('security.index').'"', false);

        $this->actingAs($user)
            ->get(route('services.catalog'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('superadmin.plazas.administration'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('superadmin.plazas.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('finance.orders.active'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('security.index'))
            ->assertForbidden();
    }

    public function test_dashboard_requires_home_navigation_permission_for_explicitly_configured_users(): void
    {
        $withoutHome = User::factory()->create([
            'role' => 'administrative_assistant',
            'active' => true,
            'menu_permissions' => ['services.catalog'],
        ]);
        $withHome = User::factory()->create([
            'role' => 'administrative_assistant',
            'active' => true,
            'menu_permissions' => [NavigationPermissionCatalog::HOME_DASHBOARD],
        ]);

        $this->actingAs($withoutHome)
            ->get(route('dashboard'))
            ->assertForbidden();

        $this->actingAs($withHome)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Panel de inicio');
    }

    public function test_null_permissions_keep_legacy_role_defaults_but_an_explicit_empty_selection_denies_access(): void
    {
        $legacyFinance = User::factory()->create([
            'role' => 'finance',
            'active' => true,
            'menu_permissions' => null,
        ]);
        $restrictedFinance = User::factory()->create([
            'role' => 'finance',
            'active' => true,
            'menu_permissions' => [],
        ]);

        $this->actingAs($legacyFinance)
            ->get(route('finance.orders.active'))
            ->assertOk();

        $this->actingAs($restrictedFinance)
            ->get(route('finance.orders.active'))
            ->assertForbidden();
    }

    public function test_security_menu_is_visible_and_accessible_with_its_permission(): void
    {
        $user = User::factory()->create([
            'role' => 'administrative_assistant',
            'active' => true,
            'menu_permissions' => [NavigationPermissionCatalog::HOME_DASHBOARD, 'security.dashboard'],
        ]);

        $dashboard = $this->actingAs($user)->get(route('dashboard'));

        $dashboard->assertOk();
        $dashboard->assertSee('Seguridad y Vigilancia');
        $dashboard->assertSee('href="'.route('security.index').'"', false);

        $this->actingAs($user)
            ->get(route('security.index'))
            ->assertOk()
            ->assertSee('Sin incidencias registradas');
    }

    public function test_security_submenus_are_filtered_and_protected_by_their_permissions(): void
    {
        $user = User::factory()->create([
            'role' => 'administrative_assistant',
            'active' => true,
            'menu_permissions' => [NavigationPermissionCatalog::HOME_DASHBOARD, 'security.reports'],
        ]);

        $dashboard = $this->actingAs($user)->get(route('dashboard'));

        $dashboard->assertOk();
        $dashboard->assertSee('href="'.route('security.index', ['section' => 'reports']).'"', false);
        $dashboard->assertDontSee('href="'.route('security.index').'"', false);

        $this->actingAs($user)
            ->get(route('security.index', ['section' => 'reports']))
            ->assertOk()
            ->assertSee('Sin reportes disponibles');

        $this->actingAs($user)
            ->get(route('security.index'))
            ->assertForbidden();
    }

    public function test_unknown_navigation_permissions_are_rejected(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $response = $this->actingAs($superAdmin)
            ->from(route('finance.admin.users'))
            ->post(route('finance.admin.users.store'), [
                'name' => 'Usuario invalido',
                'email' => 'invalid-permission@example.com',
                'password' => 'secret12',
                'role' => 'administrative_assistant',
                'menu_permissions_configured' => '1',
                'menu_permissions' => ['system.root'],
            ]);

        $response->assertRedirect(route('finance.admin.users'));
        $response->assertSessionHasErrors('menu_permissions.0');
        $this->assertDatabaseMissing('users', ['email' => 'invalid-permission@example.com']);
    }
}
