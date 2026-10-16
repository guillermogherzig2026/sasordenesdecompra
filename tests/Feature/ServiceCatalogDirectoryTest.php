<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\RecurringService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ServiceCatalogDirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_keeps_deep_link_filters_without_showing_the_removed_filter_panel(): void
    {
        $this->actingAs($this->superAdmin());
        $phone = $this->service('SRV-001', [
            'service_name' => 'Telefonía matriz', 'category' => 'Telefonia Fija',
            'company_name' => 'Empresa Norte', 'branch' => 'Matriz', 'status' => 'active',
        ]);
        $water = $this->service('SRV-002', [
            'service_name' => 'Agua norte', 'category' => 'Agua',
            'company_name' => 'Empresa Norte', 'branch' => 'Norte', 'status' => 'paused',
        ]);
        $other = $this->service('SRV-003', [
            'service_name' => 'Mantenimiento sur', 'category' => 'Otros',
            'company_name' => 'Empresa Sur', 'branch' => null, 'status' => 'inactive',
        ]);

        $this->get(route('services.catalog', [
            'category' => 'Agua', 'company' => 'Empresa Norte', 'branch' => 'Norte',
            'status' => 'paused', 'q' => 'Agua',
        ]))->assertOk()
            ->assertSee('id="service-'.$water->id.'"', false)
            ->assertDontSee('id="service-'.$phone->id.'"', false)
            ->assertDontSee('id="service-'.$other->id.'"', false)
            ->assertDontSee('class="service-directory-filters"', false)
            ->assertDontSee('data-service-filters', false)
            ->assertSee('href="'.route('services.catalog', ['category' => 'Agua']).'"', false)
            ->assertSee('aria-current="page"', false);

        $this->get(route('services.catalog', ['branch' => '__none']))
            ->assertOk()->assertSee('id="service-'.$other->id.'"', false)
            ->assertDontSee('id="service-'.$water->id.'"', false);
    }

    public function test_maintenance_carousel_labels_preserve_legacy_filter_and_include_condominium_fees(): void
    {
        $this->actingAs($this->superAdmin());
        $maintenance = $this->service('SRV-101', ['category' => 'Mantenimiento']);
        $condominium = $this->service('SRV-102', ['category' => 'Cuota de Mantenimiento de Condominio']);

        $this->get(route('services.catalog'))->assertOk()
            ->assertSee('<strong>Mantenimiento correctivo y preventivo</strong>', false)
            ->assertSee('<strong>Cuota de Mantenimiento de Condominio</strong>', false)
            ->assertSee('value="Mantenimiento"', false)
            ->assertSee('value="Cuota de Mantenimiento de Condominio"', false);

        $this->get(route('services.catalog', ['category' => 'Mantenimiento']))->assertOk()
            ->assertSee('id="service-'.$maintenance->id.'"', false)
            ->assertDontSee('id="service-'.$condominium->id.'"', false);

        $this->get(route('services.catalog', ['category' => 'Cuota de Mantenimiento de Condominio']))->assertOk()
            ->assertSee('id="service-'.$condominium->id.'"', false)
            ->assertDontSee('id="service-'.$maintenance->id.'"', false);
    }

    public function test_catalog_keeps_create_edit_export_and_status_routes(): void
    {
        $this->actingAs($this->superAdmin());
        $service = $this->service('SRV-004', [
            'bank' => 'Banco de prueba', 'payer_account' => '123456',
            'reference' => 'Referencia original', 'service_location' => 'Sucursal central',
        ]);

        $response = $this->get(route('services.catalog'))
            ->assertOk()
            ->assertSee(route('services.create'))
            ->assertSee(route('services.edit', $service))
            ->assertSee(route('reports.download', 'services-catalog'))
            ->assertSee(route('services.status', [$service, 'paused']))
            ->assertSee(route('services.status', [$service, 'inactive']))
            ->assertSee('Banco de prueba')
            ->assertSee('123456')
            ->assertSee('Referencia original')
            ->assertSee('Sucursal central');
        $this->assertMatchesRegularExpression('/<tr class="service-directory-row"[^>]*>.*?<td class="service-directory-banking">Banco de prueba<\/td>\s*<td class="service-directory-banking">123456<\/td>/s', $response->getContent());

        $this->patch(route('services.status', [$service, 'paused']))->assertRedirect();
        $this->assertSame('paused', $service->fresh()->status);
        $this->get(route('services.catalog', ['status' => 'paused']))
            ->assertOk()->assertSee('id="service-'.$service->id.'"', false)
            ->assertSee(route('services.status', [$service, 'active']));
    }

    public function test_catalog_is_the_only_service_entry_in_both_navigation_groups(): void
    {
        $response = $this->actingAs($this->superAdmin())->get(route('services.catalog'));

        $response->assertOk()
            ->assertSee('<h1>Servicios</h1>', false)
            ->assertSee('data-service-create-open', false);

        $html = $response->getContent();
        $this->assertSame(2, substr_count($html, 'href="'.route('services.catalog').'">Servicios</a>'));
        $this->assertStringNotContainsString('>Alta Servicio</a>', $html);
        $this->assertStringNotContainsString('>Catalogo Servicios</a>', $html);
    }

    public function test_finance_user_with_creation_permission_can_open_catalog_popup(): void
    {
        $user = User::factory()->create([
            'role' => 'finance', 'active' => true,
            'menu_permissions' => ['finance.services.catalog', 'finance.services.create'],
        ]);

        $this->actingAs($user)->get(route('services.catalog'))
            ->assertOk()
            ->assertSee('data-service-create-open', false)
            ->assertSee('id="service-create-dialog"', false);
    }

    public function test_edit_form_preserves_legacy_company_and_category_options(): void
    {
        $this->actingAs($this->superAdmin());
        Company::create(['name' => 'Empresa vigente', 'rfc' => 'EVI260923A11', 'address' => 'Monterrey']);
        $service = $this->service('SRV-005', [
            'company_name' => 'Empresa heredada', 'category' => 'Categoría heredada',
        ]);

        $this->get(route('services.edit', $service))
            ->assertOk()
            ->assertSee('Información del servicio')
            ->assertSee('Programación y fechas')
            ->assertSee('Pago y ubicación')
            ->assertSee('value="Empresa heredada" selected', false)
            ->assertSee('value="Categoría heredada" selected', false)
            ->assertSee('name="payer_account"', false)
            ->assertSee('name="cutoff_day"', false);
    }

    public function test_next_payment_uses_existing_cutoff_and_interval_rules(): void
    {
        $service = $this->service('SRV-006', [
            'start_date' => '2026-08-01', 'cutoff_day' => 31,
            'cutoff_month' => 8, 'cutoff_year' => 2026,
            'payment_interval_days' => 180,
        ]);

        $this->assertSame('2027-02-27', $service->nextScheduledPaymentDate(Carbon::parse('2026-09-01'))?->toDateString());
        $this->assertSame('2027-08-26', $service->nextScheduledPaymentDate(Carbon::parse('2027-03-01'))?->toDateString());

        $withoutCutoff = $this->service('SRV-007', [
            'start_date' => '2026-08-01', 'cutoff_day' => null,
            'cutoff_month' => null, 'cutoff_year' => null,
            'payment_interval_days' => 30, 'due_days_after_cutoff' => 5,
        ]);
        $this->assertSame('2026-10-05', $withoutCutoff->nextScheduledPaymentDate(Carbon::parse('2026-09-23'))?->toDateString());
    }

    public function test_new_service_keeps_valid_category_and_company_filter_context(): void
    {
        $this->actingAs($this->superAdmin());
        Company::create(['name' => 'Empresa Norte', 'rfc' => 'ENO260923A11', 'address' => 'Monterrey']);
        $catalog = route('services.catalog', ['category' => 'Agua', 'company' => 'Empresa Norte']);
        $create = route('services.create', ['category' => 'Agua', 'company' => 'Empresa Norte']);

        $this->get($catalog)->assertOk()
            ->assertSee(str_replace('&', '&amp;', $create), false)
            ->assertSee('id="service-create-dialog"', false)
            ->assertSee('data-service-create-open', false)
            ->assertSee('value="Empresa Norte" selected', false)
            ->assertSee('value="Agua" selected', false)
            ->assertSee('name="service_location"', false)
            ->assertSee('name="start_day"', false)
            ->assertSee('name="cutoff_day"', false)
            ->assertSee('name="is_domiciled"', false)
            ->assertSee('name="notes"', false);
        $this->get($create)->assertOk()
            ->assertSee('value="Empresa Norte" selected', false)
            ->assertSee('value="Agua" selected', false);

        $this->get(route('services.create', ['category' => 'No existe', 'company' => 'Otra empresa']))
            ->assertOk()->assertDontSee('value="Otra empresa" selected', false);

        $this->get(route('services.catalog'))->assertOk()
            ->assertSee('Recolección de basura')
            ->assertSee('Internet');
        $this->get(route('services.catalog', ['create' => 1]))->assertOk()
            ->assertSee('data-auto-open', false)
            ->assertDontSee('href="'.route('services.catalog', ['create' => 1]).'"', false);
        $this->get(route('services.create'))->assertOk()
            ->assertSee('value="Recoleccion de basura"', false)
            ->assertSee('value="Internet"', false);
    }

    public function test_invalid_catalog_creation_reopens_popup_with_entered_values(): void
    {
        $this->actingAs($this->superAdmin());
        Company::create(['name' => 'Empresa Norte', 'rfc' => 'ENO260923A11', 'address' => 'Monterrey']);
        $catalog = route('services.catalog', ['category' => 'Agua', 'company' => 'Empresa Norte']);

        $this->from($catalog)->post(route('services.store'), [
            '_service_form' => 'catalog',
            'service_name' => 'Agua de prueba',
            'company_name' => 'Empresa Norte',
            'category' => 'Agua',
        ])->assertRedirect($catalog)->assertSessionHasErrors();

        $this->get($catalog)->assertOk()
            ->assertSee('data-auto-open', false)
            ->assertSee('value="Agua de prueba"', false)
            ->assertSee('Revisa los siguientes datos:');
    }

    public function test_catalog_paginates_and_finance_catalog_only_user_does_not_get_create_link(): void
    {
        $user = User::factory()->create([
            'role' => 'finance', 'active' => true,
            'menu_permissions' => ['finance.services.catalog'],
        ]);
        foreach (range(1, 21) as $index) {
            $this->service('SRV-'.str_pad((string) $index, 3, '0', STR_PAD_LEFT));
        }

        $this->actingAs($user)->get(route('services.catalog'))
            ->assertOk()
            ->assertSee('Mostrando 1–20 de 21 servicios')
            ->assertDontSee('>Nuevo servicio</a>', false);

        $this->actingAs($user)->get(route('services.catalog', ['page' => 2]))
            ->assertOk()
            ->assertSee('Mostrando 21–21 de 21 servicios');
    }

    private function superAdmin(): User
    {
        return User::factory()->create(['role' => 'superadmin', 'active' => true]);
    }

    private function service(string $folio, array $attributes = []): RecurringService
    {
        return RecurringService::create(array_merge([
            'folio' => $folio,
            'holder' => 'Empresa Norte',
            'company_name' => 'Empresa Norte',
            'bank' => 'BB',
            'payer_account' => '0001',
            'branch' => 'Matriz',
            'service_name' => 'Servicio de prueba',
            'provider' => 'Proveedor de prueba',
            'service_number' => $folio,
            'category' => 'Otros',
            'cost' => 100,
            'validity' => 'Indefinido',
            'payment_interval_days' => 30,
            'start_date' => '2026-08-01',
            'cutoff_day' => 5,
            'cutoff_month' => 8,
            'cutoff_year' => 2026,
            'reference' => 'Referencia',
            'status' => 'active',
        ], $attributes));
    }
}
