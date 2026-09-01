<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\NavigationPermissionCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminPlazaTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_open_plaza_administration_from_the_menu(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('superadmin.plazas.index'));

        $response->assertOk();
        $response->assertSee('Administracion de Plazas');
        $response->assertSee('href="'.route('superadmin.plazas.index').'"', false);
        $response->assertSee('href="'.route('superadmin.plazas.administration').'"', false);
        $response->assertSee('href="'.route('superadmin.plazas.contracts').'"', false);
        $response->assertSee('href="'.route('superadmin.plazas.marketplace').'"', false);
        $response->assertSee('href="'.route('superadmin.plazas.properties').'"', false);
        $response->assertSee('href="'.route('superadmin.plazas.users').'"', false);
        $response->assertSee('href="'.route('superadmin.plazas.tenants').'"', false);
        $response->assertSeeInOrder([
            'Marketplace',
            'Arrendatarios',
            'Catalogo de unidades',
            'Alta de Usuarios',
        ]);
        $response->assertSee('src="'.route('superadmin.plazas.panel').'"', false);
        $response->assertSee('scrolling="no"', false);
        $response->assertSee('new ResizeObserver(resizeFrame)', false);

        $panelResponse = $this->actingAs($user)->get(route('superadmin.plazas.panel'));

        $panelResponse->assertOk();
        $panelResponse->assertSee('Secciones de Administracion de Plazas');
        $panelResponse->assertSee('display: none !important;', false);
        $panelResponse->assertSee('html[data-selected-section="tenants"] .module-chrome', false);
        $panelResponse->assertSee('html[data-selected-section="properties"] .module-chrome', false);
        $panelResponse->assertSee('aria-hidden="true"', false);
        $panelResponse->assertSee('data-selected-section="dashboard"', false);
        $panelResponse->assertSee('defaultTab: "superadmin_dashboard"', false);
        $panelResponse->assertSee('selectedSection: "dashboard"', false);
        $panelResponse->assertSee("classList.add('plazas-panel-embedded')", false);
        $panelResponse->assertSee('overflow-y: clip;', false);
        $panelResponse->assertSee('modules/rentas360-plazas/app.js', false);

        $moduleScript = file_get_contents(public_path('modules/rentas360-plazas/app.js'));

        $this->assertIsString($moduleScript);
        $this->assertStringContainsString('label: "Administracion y Cobranza"', $moduleScript);
        $this->assertStringContainsString('plaza-administration-selector-cards', $moduleScript);
        $this->assertStringContainsString('data-administration-property-id', $moduleScript);
        $this->assertStringContainsString('data-administration-carousel="previous"', $moduleScript);
        $this->assertStringContainsString('data-administration-carousel="next"', $moduleScript);
        $this->assertStringContainsString('renderPropertyDetailSection({ property, markupOnly: true })', $moduleScript);
        $this->assertStringContainsString('Listado de unidades', $moduleScript);
        $this->assertStringContainsString('property-administration-panel', $moduleScript);
        $this->assertStringContainsString('property-administration-toolbar', $moduleScript);
        $this->assertStringContainsString('data-property-administration-home', $moduleScript);
        $this->assertStringContainsString('data-property-administration-content', $moduleScript);
        $this->assertStringContainsString('data-property-administration-view="payments"', $moduleScript);
        $this->assertStringContainsString('data-property-administration-view="advances"', $moduleScript);
        $this->assertStringContainsString('data-property-administration-view="balances"', $moduleScript);
        $this->assertStringContainsString('data-administration-view-panel="advances"', $moduleScript);
        $this->assertStringContainsString('paymentMethodAdvancePanelMarkup(units)', $moduleScript);
        $this->assertStringNotContainsString('data-open-advance-payments="${property.id}"', $moduleScript);
        $this->assertStringContainsString('data-payment-month-toggle', $moduleScript);
        $this->assertStringContainsString('data-payment-month-content', $moduleScript);
        $this->assertStringContainsString('togglePaymentMethodMonthPanel', $moduleScript);
        $this->assertStringContainsString('switchPropertyAdministrationView', $moduleScript);
        $this->assertStringContainsString('propertyAdministrationViewMarkup', $moduleScript);
        $this->assertStringContainsString('scrollContainer.scrollLeft = 0;', $moduleScript);
        $this->assertStringContainsString('aria-current="page"', $moduleScript);
        $this->assertStringNotContainsString('property-detail-actions-only', $moduleScript);
        $this->assertStringNotContainsString('<p class="eyebrow">Propiedad</p>', $moduleScript);
        $this->assertStringNotContainsString('id="propertyInfo"', $moduleScript);
        $this->assertStringNotContainsString('data-view-users', $moduleScript);
        $this->assertStringNotContainsString('data-edit-units', $moduleScript);
        $this->assertStringContainsString('userPropertyAccessSectionMarkup(properties, selectedProperty)', $moduleScript);
        $this->assertStringContainsString('data-user-property-select', $moduleScript);
        $this->assertStringContainsString('propertyUnitCatalogSectionMarkup(selectedProperty)', $moduleScript);
        $this->assertStringContainsString('data-add-unit', $moduleScript);
        $this->assertStringContainsString('Selecciona una plaza', $moduleScript);
        $this->assertStringContainsString('data-plaza-dashboard-new', $moduleScript);
        $this->assertStringContainsString('plaza-dashboard-carousel is-compact', $moduleScript);
        $this->assertStringContainsString('modal-plaza-flow', $moduleScript);
        $this->assertStringContainsString('data-plaza-flow-tab', $moduleScript);
        $this->assertStringContainsString('Resumen de la plaza', $moduleScript);
        $this->assertStringContainsString('data-plaza-documents', $moduleScript);
        $this->assertStringContainsString('Guardar borrador', $moduleScript);
        $this->assertStringContainsString('Guardar plaza', $moduleScript);
        $this->assertStringContainsString('PLAZA_DRAFT_KEY', $moduleScript);
        $this->assertStringContainsString('data-plaza-dashboard-property-id', $moduleScript);
        $this->assertStringContainsString('data-plaza-dashboard-all', $moduleScript);
        $this->assertStringContainsString('plazaDashboardAllPropertiesTableMarkup', $moduleScript);
        $this->assertStringContainsString('Todas las plazas', $moduleScript);
        $this->assertStringContainsString('<th>Editar</th>', $moduleScript);
        $this->assertStringContainsString('data-plaza-dashboard-edit', $moduleScript);
        $this->assertStringContainsString('openPlazaCreationFlow(button.dataset.plazaDashboardEdit)', $moduleScript);
        $this->assertStringContainsString('Editar plaza: ${property.name}', $moduleScript);
        $this->assertStringContainsString('Guardar cambios', $moduleScript);
        $this->assertStringNotContainsString('Seleccionada', $moduleScript);
        $this->assertStringContainsString('plazaCatalogSelectorMarkup', $moduleScript);
        $this->assertStringContainsString('data-catalog-plaza-carousel="previous"', $moduleScript);
        $this->assertStringContainsString('data-catalog-plaza-carousel="next"', $moduleScript);
        $this->assertStringContainsString('Selector de plazas del catalogo de unidades', $moduleScript);
        $this->assertStringNotContainsString('Propiedades administradas', $moduleScript);
        $this->assertStringNotContainsString('data-action="new-property"', $moduleScript);
        $this->assertStringNotContainsString('propertyCardMarkup', $moduleScript);
        $this->assertStringContainsString('Selector de plazas del catalogo de arrendatarios', $moduleScript);
        $this->assertStringContainsString('<th>Datos de contacto</th>', $moduleScript);
        $this->assertStringContainsString('data-tenant-contact', $moduleScript);
        $this->assertStringContainsString('tenantLeaseContractStatus', $moduleScript);
        $this->assertStringContainsString('modal-compact', $moduleScript);
        $this->assertStringContainsString('Selector de plazas para contratos', $moduleScript);
        $this->assertStringContainsString('plaza_contracts: renderPlazaContracts', $moduleScript);
        $this->assertStringNotContainsString('Panel legal de ${escapeAttribute(property.name)}', $moduleScript);
        $this->assertStringNotContainsString('plazaContractsSummaryMarkup', $moduleScript);
        $this->assertStringContainsString('<h3>Panel legal por unidad</h3>', $moduleScript);
        $this->assertStringNotContainsString('data-go-legal-panel', $moduleScript);
        $this->assertStringContainsString('Selector de plazas para marketplace', $moduleScript);
        $this->assertStringContainsString('plaza_marketplace: renderPlazaMarketplace', $moduleScript);
        $this->assertStringContainsString('data-marketplace-toggle', $moduleScript);
        $this->assertStringContainsString('marketplace-inventory-section', $moduleScript);
        $this->assertStringContainsString('class="marketplace-section-heading"', $moduleScript);
        $this->assertStringContainsString('data-unit-marketplace-toggle', $moduleScript);
        $this->assertStringContainsString('marketplaceUnitsTableMarkup(units, property)', $moduleScript);
        $this->assertStringContainsString('isUnitMarketplaceEnabled(property, unit)', $moduleScript);
        $this->assertStringContainsString('typeof unit.marketplaceEnabled === "boolean" ? unit.marketplaceEnabled : true', $moduleScript);
        $this->assertStringContainsString('marketplaceEnabled', $moduleScript);
        $this->assertStringContainsString('role-badge is-marketplace', $moduleScript);
        $this->assertStringContainsString('Marketplace ${enabledMarketplaceUnits} de ${units.length}', $moduleScript);
        $this->assertStringNotContainsString('Resumen comercial de la plaza', $moduleScript);
        $this->assertStringContainsString('propertyLegalPanelTableMarkup(units)', $moduleScript);
        $this->assertStringContainsString('class="legal-contract-term-inline"', $moduleScript);
        $this->assertStringContainsString('Mantenimiento <span>Total</span>', $moduleScript);
        $this->assertStringContainsString('Machote de Contrato <span>en PDF</span>', $moduleScript);
        $this->assertStringContainsString('Contrato de Nuevo <span>Periodo</span>', $moduleScript);
        $this->assertStringContainsString('Regresar a administracion', $moduleScript);
        $this->assertStringNotContainsString('Regresar a propiedad', $moduleScript);
        $this->assertStringNotContainsString('plaza-dashboard-management', $moduleScript);
        $this->assertStringNotContainsString('data-view-property-tenant-directory', $moduleScript);
        $this->assertStringNotContainsString('data-plaza-dashboard-open', $moduleScript);
        $this->assertStringNotContainsString('class="property-header-inline-actions"', $moduleScript);
        $this->assertStringContainsString('superadmin_dashboard: renderPlazaGeneralDashboard', $moduleScript);
        $this->assertStringContainsString('plazaDashboardStatusTableMarkup(property, units)', $moduleScript);
        $this->assertStringContainsString('<th>Pago de renta</th>', $moduleScript);
        $this->assertStringContainsString('<th>Pago de mantenimiento</th>', $moduleScript);
        $this->assertStringContainsString('<th>Activo en Marketplace</th>', $moduleScript);
        $this->assertStringContainsString('paymentStatusChipMarkup(unit, "rent")', $moduleScript);
        $this->assertStringContainsString('paymentStatusChipMarkup(unit, "maintenance")', $moduleScript);
        $this->assertStringContainsString('daysRemaining < 60 ? "status-pending" : "status-paid"', $moduleScript);
        $this->assertStringContainsString('view.activeTab = PANEL_DEFAULT_TAB;', $moduleScript);
    }

    public function test_superadmin_can_open_the_administration_submenu_directly(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('superadmin.plazas.administration'));

        $response->assertOk();
        $response->assertSee('href="'.route('superadmin.plazas.index').'"', false);
        $response->assertSee('href="'.route('superadmin.plazas.administration').'"', false);
        $response->assertSee('src="'.route('superadmin.plazas.panel', ['section' => 'administration']).'"', false);

        $this->actingAs($user)
            ->get(route('superadmin.plazas.panel', ['section' => 'administration']))
            ->assertOk()
            ->assertSee('data-selected-section="administration"', false)
            ->assertSee('defaultTab: "administration"', false)
            ->assertSee('selectedSection: "administration"', false);
    }

    public function test_superadmin_can_open_each_moved_plaza_menu_directly(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $sections = [
            'contracts' => ['route' => 'superadmin.plazas.contracts', 'tab' => 'plaza_contracts'],
            'marketplace' => ['route' => 'superadmin.plazas.marketplace', 'tab' => 'plaza_marketplace'],
            'properties' => ['route' => 'superadmin.plazas.properties', 'tab' => 'properties'],
            'users' => ['route' => 'superadmin.plazas.users', 'tab' => 'user_new'],
            'tenants' => ['route' => 'superadmin.plazas.tenants', 'tab' => 'tenants'],
        ];

        foreach ($sections as $section => $item) {
            $response = $this->actingAs($user)->get(route($item['route']));

            $response->assertOk();
            $response->assertSee('src="'.route('superadmin.plazas.panel', ['section' => $section]).'"', false);

            $this->actingAs($user)
                ->get(route('superadmin.plazas.panel', ['section' => $section]))
                ->assertOk()
                ->assertSee('defaultTab: "'.$item['tab'].'"', false)
                ->assertSee('selectedSection: "'.$section.'"', false);
        }
    }

    public function test_plaza_menu_permissions_are_enforced_per_moved_option(): void
    {
        $user = User::factory()->create([
            'role' => 'administrative_assistant',
            'active' => true,
            'menu_permissions' => [NavigationPermissionCatalog::HOME_DASHBOARD, 'plazas.users'],
        ]);

        $dashboard = $this->actingAs($user)->get(route('dashboard'));

        $dashboard->assertOk();
        $dashboard->assertSee('href="'.route('superadmin.plazas.users').'"', false);
        $dashboard->assertDontSee('href="'.route('superadmin.plazas.properties').'"', false);
        $dashboard->assertDontSee('href="'.route('superadmin.plazas.contracts').'"', false);
        $dashboard->assertDontSee('href="'.route('superadmin.plazas.marketplace').'"', false);

        $this->actingAs($user)
            ->get(route('superadmin.plazas.users'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('superadmin.plazas.panel', ['section' => 'users']))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('superadmin.plazas.tenants'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('superadmin.plazas.contracts'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('superadmin.plazas.marketplace'))
            ->assertForbidden();
    }

    public function test_plaza_contracts_permission_grants_only_the_contracts_submenu(): void
    {
        $user = User::factory()->create([
            'role' => 'administrative_assistant',
            'active' => true,
            'menu_permissions' => [NavigationPermissionCatalog::HOME_DASHBOARD, 'plazas.contracts'],
        ]);

        $dashboard = $this->actingAs($user)->get(route('dashboard'));

        $dashboard->assertOk();
        $dashboard->assertSee('href="'.route('superadmin.plazas.contracts').'"', false);
        $dashboard->assertDontSee('href="'.route('superadmin.plazas.administration').'"', false);

        $this->actingAs($user)
            ->get(route('superadmin.plazas.contracts'))
            ->assertOk()
            ->assertSee('src="'.route('superadmin.plazas.panel', ['section' => 'contracts']).'"', false);

        $this->actingAs($user)
            ->get(route('superadmin.plazas.panel', ['section' => 'contracts']))
            ->assertOk()
            ->assertSee('defaultTab: "plaza_contracts"', false)
            ->assertSee('selectedSection: "contracts"', false);
    }

    public function test_plaza_marketplace_permission_grants_only_the_marketplace_submenu(): void
    {
        $user = User::factory()->create([
            'role' => 'administrative_assistant',
            'active' => true,
            'menu_permissions' => [NavigationPermissionCatalog::HOME_DASHBOARD, 'plazas.marketplace'],
        ]);

        $dashboard = $this->actingAs($user)->get(route('dashboard'));

        $dashboard->assertOk();
        $dashboard->assertSee('href="'.route('superadmin.plazas.marketplace').'"', false);
        $dashboard->assertDontSee('href="'.route('superadmin.plazas.administration').'"', false);

        $this->actingAs($user)
            ->get(route('superadmin.plazas.marketplace'))
            ->assertOk()
            ->assertSee('src="'.route('superadmin.plazas.panel', ['section' => 'marketplace']).'"', false);

        $this->actingAs($user)
            ->get(route('superadmin.plazas.panel', ['section' => 'marketplace']))
            ->assertOk()
            ->assertSee('defaultTab: "plaza_marketplace"', false)
            ->assertSee('selectedSection: "marketplace"', false);
    }

    public function test_plaza_administration_rejects_non_superadmin_users(): void
    {
        $user = User::factory()->create([
            'role' => 'finance',
            'active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('superadmin.plazas.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('superadmin.plazas.panel'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('superadmin.plazas.administration'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('superadmin.plazas.contracts'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('superadmin.plazas.marketplace'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('superadmin.plazas.users'))
            ->assertForbidden();
    }

    public function test_plaza_administration_requires_authentication(): void
    {
        $this->get(route('superadmin.plazas.index'))
            ->assertRedirect(route('login'));

        $this->get(route('superadmin.plazas.administration'))
            ->assertRedirect(route('login'));

        $this->get(route('superadmin.plazas.contracts'))
            ->assertRedirect(route('login'));

        $this->get(route('superadmin.plazas.marketplace'))
            ->assertRedirect(route('login'));

        $this->get(route('superadmin.plazas.properties'))
            ->assertRedirect(route('login'));

        $this->get(route('superadmin.plazas.users'))
            ->assertRedirect(route('login'));

        $this->get(route('superadmin.plazas.tenants'))
            ->assertRedirect(route('login'));
    }
}
