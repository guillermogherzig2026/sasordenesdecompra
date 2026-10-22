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
        $response->assertSee('href="'.route('superadmin.plazas.agents.index').'"', false);
        $response->assertSee('Centro de agentes');
        $response->assertSee('href="'.route('superadmin.plazas.index').'"', false);
        $response->assertSee('href="'.route('superadmin.plazas.administration').'"', false);
        $response->assertSee('href="'.route('superadmin.plazas.contracts').'"', false);
        $response->assertSee('href="'.route('superadmin.plazas.marketplace').'"', false);
        $response->assertSee('href="'.route('superadmin.plazas.properties').'"', false);
        $response->assertSee('href="'.route('superadmin.plazas.local-marketing').'"', false);
        $response->assertSee('href="'.route('superadmin.plazas.users').'"', false);
        $response->assertSee('href="'.route('superadmin.plazas.tenants').'"', false);
        $response->assertSee('class="nav-box nav-box-plazas"', false);
        $this->assertSame(1, substr_count($response->getContent(), 'class="nav-box nav-box-plazas"'));
        $response->assertSee('class="nav-box nav-box-plazas-marketplace"', false);
        $this->assertSame(1, substr_count($response->getContent(), 'class="nav-box nav-box-plazas-marketplace"'));
        $response->assertSeeInOrder([
            'Contratos',
            'Arrendatarios',
            'Catalogo de locales',
            'Marketplace',
            'Marketing local',
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
        $panelResponse->assertSee('html[data-selected-section="local_marketing"] .module-chrome', false);
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
        $this->assertStringContainsString('data-administration-all', $moduleScript);
        $this->assertStringContainsString('const carouselSelections = ["all", ...properties.map((property) => property.id)];', $moduleScript);
        $this->assertStringContainsString('data-administration-property-id', $moduleScript);
        $this->assertStringContainsString('data-administration-carousel="previous"', $moduleScript);
        $this->assertStringContainsString('data-administration-carousel="next"', $moduleScript);
        $this->assertStringContainsString('renderPropertyDetailSection({ property, markupOnly: true })', $moduleScript);
        $this->assertStringContainsString('Listado de unidades', $moduleScript);
        foreach (['Todos', 'Al corriente', 'Con Adeudo', 'Contrato Vigente', 'Contrato Vencido'] as $filterLabel) {
            $this->assertStringContainsString('label: "'.$filterLabel.'"', $moduleScript);
        }
        $this->assertStringContainsString('data-unit-list-filter="${option.key}"', $moduleScript);
        $this->assertStringContainsString('propertyPaymentColumns.some((concept) => isPaymentConceptDue(unit, monthKey, concept.key))', $moduleScript);
        $this->assertStringNotContainsString('Cada unidad puede abrirse para revisar su informacion especifica.', $moduleScript);
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
        $this->assertStringContainsString('data-plaza-dashboard-general-information', $moduleScript);
        $this->assertStringContainsString('plazaDashboardGeneralInformationTableMarkup', $moduleScript);
        $this->assertStringContainsString('<th>Nombre</th>', $moduleScript);
        $this->assertStringContainsString('<th>Dirección</th>', $moduleScript);
        $this->assertStringContainsString('<th>Ubicación de Google Maps</th>', $moduleScript);
        $this->assertStringContainsString('<th>Coordenadas</th>', $moduleScript);
        $this->assertStringContainsString('<th>Número de niveles</th>', $moduleScript);
        $this->assertStringContainsString('<th>Número de locales</th>', $moduleScript);
        $this->assertStringContainsString('<th>Horario de servicio</th>', $moduleScript);
        $this->assertStringContainsString('<th>Ingreso total estimado (mensual)</th>', $moduleScript);
        $this->assertStringContainsString('<th>Ingreso total programado (mensual)</th>', $moduleScript);
        $this->assertStringContainsString('plazaDashboardProgrammedMonthlyIncome(property, units)', $moduleScript);
        $this->assertStringContainsString('<small>Total programado mensual</small>', $moduleScript);
        $this->assertStringContainsString('<small>Total programado de rentas</small>', $moduleScript);
        $this->assertStringContainsString('const totalProgrammedRent = properties.reduce(', $moduleScript);
        $this->assertStringContainsString('https://www.google.com/maps/search/?api=1&query=', $moduleScript);
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
        $this->assertStringContainsString('Selector de plazas del catalogo de locales', $moduleScript);
        $this->assertStringContainsString('Catalogo de locales de ${property.name}', $moduleScript);
        $this->assertStringNotContainsString('Propiedades administradas', $moduleScript);
        $this->assertStringNotContainsString('data-action="new-property"', $moduleScript);
        $this->assertStringNotContainsString('propertyCardMarkup', $moduleScript);
        $this->assertStringContainsString('Selector de plazas del catalogo de arrendatarios', $moduleScript);
        $this->assertStringContainsString('<th>Datos de contacto</th>', $moduleScript);
        $this->assertStringContainsString('data-tenant-contact', $moduleScript);
        $this->assertStringContainsString('tenantLeaseContractStatus', $moduleScript);
        $this->assertStringContainsString('modal-compact', $moduleScript);
        $this->assertStringContainsString('Selector de plazas para contratos', $moduleScript);
        $this->assertStringContainsString('contractsPropertyId: loadSelectedPropertyId()', $moduleScript);
        $this->assertStringContainsString('marketplacePropertyId: loadSelectedPropertyId()', $moduleScript);
        $this->assertStringContainsString('propertiesCatalogPropertyId: loadSelectedPropertyId()', $moduleScript);
        $this->assertStringContainsString('localMarketingPropertyId: "all"', $moduleScript);
        $this->assertStringContainsString('usersPropertyId: loadSelectedPropertyId()', $moduleScript);
        $this->assertStringContainsString('tenantCatalogPropertyId: loadSelectedPropertyId()', $moduleScript);
        $this->assertStringContainsString('data-catalog-plaza-all', $moduleScript);
        $this->assertStringContainsString('plaza-contracts-selector-cards', $moduleScript);
        $this->assertStringContainsString('plaza-left-aligned-selector-cards', $moduleScript);
        $this->assertStringContainsString('includeAll: true', $moduleScript);
        $this->assertStringContainsString('plaza_contracts: renderPlazaContracts', $moduleScript);
        $this->assertStringNotContainsString('Panel legal de ${escapeAttribute(property.name)}', $moduleScript);
        $this->assertStringNotContainsString('plazaContractsSummaryMarkup', $moduleScript);
        $this->assertStringContainsString('<h3>Panel legal por unidad</h3>', $moduleScript);
        $this->assertStringNotContainsString('data-go-legal-panel', $moduleScript);
        $this->assertStringContainsString('Selector de plazas para marketplace', $moduleScript);
        $this->assertStringContainsString('Espacios de todas las plazas', $moduleScript);
        $this->assertStringContainsString('plaza_marketplace: renderPlazaMarketplace', $moduleScript);
        $this->assertStringContainsString('local_marketing: renderLocalMarketing', $moduleScript);
        $this->assertStringContainsString('Selector de plazas para marketing local', $moduleScript);
        $this->assertStringContainsString('localMarketingView: PANEL_LOCAL_MARKETING_VIEW', $moduleScript);
        $this->assertStringContainsString('localMarketingScope: "all"', $moduleScript);
        $this->assertStringContainsString('localMarketingCampaignSection: "playlists"', $moduleScript);
        $this->assertStringContainsString('const isCampaignView = view.localMarketingView === "campaigns";', $moduleScript);
        $this->assertStringContainsString('const sectionLabel = isCampaignView ? "Campañas" : "Pantallas";', $moduleScript);
        $this->assertStringContainsString('data-local-marketing-panel', $moduleScript);
        $this->assertStringContainsString('data-local-marketing-scope="${option.id}"', $moduleScript);
        $this->assertStringContainsString('{ id: "all", label: "Todas" }', $moduleScript);
        $this->assertStringContainsString('{ id: "level", label: "Por Nivel" }', $moduleScript);
        $this->assertStringContainsString('{ id: "group", label: "Por grupo" }', $moduleScript);
        $this->assertStringContainsString('const campaignScopeOptions = [', $moduleScript);
        $this->assertStringContainsString('{ id: "generic", label: "Genericas" }', $moduleScript);
        $this->assertStringContainsString('{ id: "category", label: "Por categoria" }', $moduleScript);
        $this->assertStringContainsString('{ id: "season", label: "Por Temporada" }', $moduleScript);
        $this->assertStringContainsString('{ id: "promotions", label: "Promociones" }', $moduleScript);
        $this->assertStringContainsString('{ id: "drafts", label: "Borradores" }', $moduleScript);
        $this->assertStringContainsString('const scopeOptions = isCampaignView ? campaignScopeOptions : screenScopeOptions;', $moduleScript);
        $this->assertStringContainsString('localMarketingCampaignMiniCarouselMarkup', $moduleScript);
        $this->assertStringContainsString('Listas de reproducción', $moduleScript);
        $this->assertStringContainsString('{ id: "library", label: "Biblioteca", icon: "layoutDashboard" }', $moduleScript);
        $this->assertStringContainsString('data-local-marketing-campaign-carousel', $moduleScript);
        $this->assertStringContainsString('data-local-marketing-campaign-section', $moduleScript);
        $this->assertStringContainsString('${isCampaignView ? localMarketingCampaignMiniCarouselMarkup() : ""}', $moduleScript);
        $this->assertStringContainsString('localMarketingPlaylists: []', $moduleScript);
        $this->assertStringContainsString('localMarketingPlaylists: mergeById(seed.localMarketingPlaylists, parsed.localMarketingPlaylists)', $moduleScript);
        $this->assertStringContainsString('data-local-marketing-playlist-new', $moduleScript);
        $this->assertStringContainsString('Nueva lista de reproducción', $moduleScript);
        $this->assertStringContainsString('openLocalMarketingPlaylistForm', $moduleScript);
        $this->assertStringContainsString('data-local-marketing-playlist-edit', $moduleScript);
        $this->assertStringContainsString('localMarketingPlaylistsMarkup(properties, property, allSelected, activeScope, scopeCopy.empty)', $moduleScript);
        $this->assertStringContainsString('localMarketingScreens: [', $moduleScript);
        $this->assertStringContainsString('localMarketingScreens: mergeById(seed.localMarketingScreens, parsed.localMarketingScreens)', $moduleScript);
        $this->assertStringContainsString('localMarketingMaintenanceTickets: []', $moduleScript);
        $this->assertStringContainsString('localMarketingScreensWorkspaceMarkup', $moduleScript);
        $this->assertStringNotContainsString('local-marketing-screen-heading', $moduleScript);
        $this->assertStringNotContainsString('local-marketing-screen-count', $moduleScript);
        $this->assertStringContainsString('Buscar por ID, nombre o ubicacion...', $moduleScript);
        $this->assertStringContainsString('data-local-marketing-screen-new', $moduleScript);
        $this->assertStringContainsString('data-local-marketing-screen-filter="connection"', $moduleScript);
        $this->assertStringContainsString('data-local-marketing-screen-toggle', $moduleScript);
        $this->assertStringContainsString('data-local-marketing-screen-edit', $moduleScript);
        $this->assertStringContainsString('data-local-marketing-screen-copy', $moduleScript);
        $this->assertStringContainsString('data-local-marketing-screen-delete', $moduleScript);
        $this->assertStringContainsString('localMarketingScreenTableColumnCount = localMarketingScreenColumns.length + 3', $moduleScript);
        $this->assertStringContainsString('<th class="local-marketing-action-header"><span>Editar</span></th>', $moduleScript);
        $this->assertStringContainsString('<th class="local-marketing-action-header"><span>Duplicar</span></th>', $moduleScript);
        $this->assertStringContainsString('<th class="local-marketing-action-header"><span>Eliminar</span></th>', $moduleScript);
        $this->assertStringContainsString('class="local-marketing-action-cell"', $moduleScript);
        $this->assertStringContainsString('localMarketingScreenColumns', $moduleScript);
        $this->assertStringContainsString('{ key: "programming", label: "Programacion" }', $moduleScript);
        $this->assertStringContainsString('programming: screen.active ? "Activa" : "Inactiva"', $moduleScript);
        $this->assertStringContainsString('class="local-marketing-programming ${screen.active ? "is-active" : "is-inactive"}"', $moduleScript);
        $this->assertStringContainsString('{ key: "maintenance", label: "Mantenimiento" }', $moduleScript);
        $this->assertStringContainsString('localMarketingMaintenanceMeta(screen.connection)', $moduleScript);
        $this->assertStringContainsString('class="local-marketing-maintenance ${maintenance.className}"', $moduleScript);
        $this->assertStringContainsString('data-local-marketing-maintenance-menu', $moduleScript);
        $this->assertStringContainsString('data-local-marketing-maintenance-request', $moduleScript);
        $this->assertStringContainsString('Solicitar mantenimiento', $moduleScript);
        $this->assertStringContainsString('openLocalMarketingMaintenanceRequest', $moduleScript);
        $this->assertStringContainsString('Levantar ticket', $moduleScript);
        $this->assertStringContainsString('state.localMarketingMaintenanceTickets.unshift(ticket)', $moduleScript);
        $this->assertStringContainsString('screen.connection = "maintenance"', $moduleScript);
        $this->assertStringContainsString('data-local-marketing-column-filter', $moduleScript);
        $this->assertStringContainsString('data-local-marketing-column-sort', $moduleScript);
        $this->assertStringContainsString('data-local-marketing-column-filter-value', $moduleScript);
        $this->assertStringContainsString('placeholder="Buscar (Todos)"', $moduleScript);
        $this->assertStringContainsString('<span>(Todos)</span>', $moduleScript);
        $this->assertStringContainsString('data-local-marketing-column-filter-apply', $moduleScript);
        $this->assertStringContainsString('data-local-marketing-column-filter-cancel', $moduleScript);
        $this->assertStringContainsString('aria-sort="${ariaSort}"', $moduleScript);
        $this->assertStringContainsString('localMarketingScreenRowsMarkup(orderedScreens', $moduleScript);
        $this->assertStringContainsString('<tfoot class="local-marketing-table-footer">', $moduleScript);
        $this->assertStringNotContainsString('data-local-marketing-screen-page', $moduleScript);
        $this->assertStringNotContainsString('table-panel local-marketing-screen-table-panel', $moduleScript);
        $this->assertStringContainsString('Nueva pantalla', $moduleScript);
        $this->assertStringContainsString('localMarketingScreenGroupValues', $moduleScript);
        $this->assertStringContainsString('return [...groupValues, "Sin grupo"]', $moduleScript);
        $this->assertStringContainsString('class="local-marketing-group-row is-ungrouped"', $moduleScript);
        $this->assertStringContainsString('activeScope === "group" && !screens.some(isLocalMarketingScreenUngrouped)', $moduleScript);
        $this->assertStringContainsString('<select id="localMarketingScreenGroup" name="group" required>', $moduleScript);
        $this->assertStringContainsString('propertyInput.addEventListener("change"', $moduleScript);
        $this->assertStringContainsString('group: String(formData.get("group") || "").trim()', $moduleScript);
        $this->assertStringContainsString('allPropertiesUnitCatalogSectionMarkup', $moduleScript);
        $this->assertStringContainsString('renderTenantCatalogForAllProperties', $moduleScript);
        $this->assertStringContainsString('Selector de plazas para usuarios', $moduleScript);
        $this->assertStringContainsString('Usuarios vinculados a todas las plazas', $moduleScript);
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
        $this->assertStringContainsString('class="property-legal-table contract-validation-table"', $moduleScript);
        $this->assertStringNotContainsString('contract-validation-edit-column', $moduleScript);
        $this->assertStringContainsString('<th class="contract-validation-status-column">Validacion</th>', $moduleScript);
        $this->assertStringNotContainsString('legal-term-action-column', $moduleScript);
        $this->assertStringNotContainsString('data-edit-unit-contract-validation-row=', $moduleScript);
        $this->assertStringContainsString('data-edit-unit-contract-validation', $moduleScript);
        $this->assertSame(1, substr_count($moduleScript, '${propertyUnitContractValidationHeaderMarkup('));
        $this->assertSame(1, substr_count($moduleScript, '${propertyUnitContractValidationCellMarkup('));
        $this->assertSame(1, substr_count($moduleScript, '${propertyUnitContractValidationActionsMarkup('));
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
        $this->assertStringContainsString('plazaDashboardAllPropertiesTableMarkup(properties, { includeDelete: true })', $moduleScript);
        $this->assertStringContainsString('data-plaza-dashboard-delete=', $moduleScript);
        $this->assertStringContainsString('Confirmacion ${step} de 3', $moduleScript);
        $this->assertStringContainsString('nameInput.value.trim() !== property.name', $moduleScript);
        $this->assertStringContainsString('nextState.deletedPropertyIds.forEach((propertyId) => removePropertyData(nextState, propertyId));', $moduleScript);
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
            ->assertSee('selectedSection: "administration"', false)
            ->assertSee('canManageValidation: true', false);
    }

    public function test_only_superadmin_can_manage_plaza_payment_validation(): void
    {
        $user = User::factory()->create([
            'role' => 'administrative_assistant',
            'active' => true,
            'menu_permissions' => [NavigationPermissionCatalog::HOME_DASHBOARD, 'plazas.administration'],
        ]);

        $this->actingAs($user)
            ->get(route('superadmin.plazas.panel', ['section' => 'administration']))
            ->assertOk()
            ->assertSee('canManageValidation: false', false);
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
            'local_marketing' => ['route' => 'superadmin.plazas.local-marketing', 'tab' => 'local_marketing'],
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
        $dashboard->assertDontSee('href="'.route('superadmin.plazas.local-marketing').'"', false);
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

        $this->actingAs($user)
            ->get(route('superadmin.plazas.local-marketing'))
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

    public function test_plaza_local_marketing_permission_grants_only_the_local_marketing_submenu(): void
    {
        $user = User::factory()->create([
            'role' => 'administrative_assistant',
            'active' => true,
            'menu_permissions' => [NavigationPermissionCatalog::HOME_DASHBOARD, 'plazas.local_marketing'],
        ]);

        $dashboard = $this->actingAs($user)->get(route('dashboard'));

        $dashboard->assertOk();
        $dashboard->assertSee('href="'.route('superadmin.plazas.local-marketing').'"', false);
        $dashboard->assertDontSee('href="'.route('superadmin.plazas.properties').'"', false);
        $dashboard->assertDontSee('href="'.route('superadmin.plazas.users').'"', false);

        $this->actingAs($user)
            ->get(route('superadmin.plazas.local-marketing'))
            ->assertOk()
            ->assertSee('Pantallas')
            ->assertSee('Campañas')
            ->assertSee('href="'.route('superadmin.plazas.local-marketing', ['view' => 'campaigns']).'"', false)
            ->assertSeeInOrder(['Marketing local', 'Pantallas', 'Campañas'])
            ->assertDontSee('>Secciones<', false)
            ->assertSee('src="'.route('superadmin.plazas.panel', ['section' => 'local_marketing']).'"', false);

        $this->actingAs($user)
            ->get(route('superadmin.plazas.panel', ['section' => 'local_marketing']))
            ->assertOk()
            ->assertSee('defaultTab: "local_marketing"', false)
            ->assertSee('selectedSection: "local_marketing"', false)
            ->assertSee('localMarketingView: "screens"', false);

        $campaignsResponse = $this->actingAs($user)
            ->get(route('superadmin.plazas.local-marketing', ['view' => 'campaigns']));

        $campaignsResponse->assertOk();
        $campaignsResponse->assertSee('view=campaigns', false);

        $this->actingAs($user)
            ->get(route('superadmin.plazas.panel', ['section' => 'local_marketing', 'view' => 'campaigns']))
            ->assertOk()
            ->assertSee('localMarketingView: "campaigns"', false);

        $this->actingAs($user)
            ->get(route('superadmin.plazas.users'))
            ->assertForbidden();
    }

    public function test_plaza_administration_rejects_users_without_permission(): void
    {
        $user = User::factory()->create([
            'role' => 'finance',
            'active' => true,
            'menu_permissions' => [],
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
            ->get(route('superadmin.plazas.local-marketing'))
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

        $this->get(route('superadmin.plazas.local-marketing'))
            ->assertRedirect(route('login'));

        $this->get(route('superadmin.plazas.users'))
            ->assertRedirect(route('login'));

        $this->get(route('superadmin.plazas.tenants'))
            ->assertRedirect(route('login'));
    }
}
