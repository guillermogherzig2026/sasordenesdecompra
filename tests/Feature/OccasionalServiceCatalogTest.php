<?php

namespace Tests\Feature;

use App\Models\OccasionalService;
use App\Models\OccasionalServiceEntry;
use App\Models\RecurringService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OccasionalServiceCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_shows_separate_occasional_carousel_with_initial_services(): void
    {
        $this->actingAs($this->superAdmin())->get(route('services.catalog'))
            ->assertOk()
            ->assertSee('Servicios recurrentes')
            ->assertSee('Servicios Ocasionales')
            ->assertSee('Tag')
            ->assertSee('Tiempo Aire')
            ->assertSee('Vale de Gasolina')
            ->assertSee('Vale de despensa')
            ->assertSee('data-occasional-create-open', false)
            ->assertSee(route('services.occasional.export'));

        $this->assertSame(4, OccasionalService::count());
    }

    public function test_services_sidebar_opens_occasional_payment_dialog_for_authorized_finance_user(): void
    {
        $user = $this->financeUser(['finance.services.catalog', 'finance.services.create', 'services.months']);
        $menuUrl = route('services.months', ['pay_occasional' => 1]);

        $this->actingAs($user)->get(route('services.catalog'))
            ->assertOk()
            ->assertSee('href="'.$menuUrl.'">Pagar servicio ocasional</a>', false);

        $response = $this->get($menuUrl)->assertOk();
        $this->assertMatchesRegularExpression('/<dialog[^>]+data-occasional-payment-dialog[^>]+data-auto-open/', $response->getContent());
        $this->assertMatchesRegularExpression('/<a class="button nav-button sub-nav-button active" href="'.preg_quote($menuUrl, '/').'">Pagar servicio ocasional<\/a>/', $response->getContent());
        $response->assertSee('Nuevo servicio')
            ->assertDontSee('data-occasional-create-dialog', false);
    }

    public function test_carousels_share_one_selection_and_show_only_its_information_below(): void
    {
        $this->actingAs($this->superAdmin());

        $recurring = $this->get(route('services.catalog', ['category' => 'Telefonia Fija']))->assertOk()
            ->assertSee('Telefonía fija')
            ->assertDontSee('<form class="service-directory-filters"', false)
            ->assertDontSee('aria-label="Servicios ocasionales seleccionados"', false);
        $this->assertSingleCarouselSelection($recurring->getContent());
        $recurring->assertSee('href="'.route('services.catalog', ['occasional' => 1]).'"', false);

        $allOccasional = $this->get(route('services.catalog', ['view' => 'occasional']))->assertOk()
            ->assertSee('aria-label="Servicios ocasionales seleccionados"', false)
            ->assertDontSee('<form class="service-directory-filters"', false)
            ->assertSee('<table class="occasional-directory-table">', false)
            ->assertDontSee('<table class="service-directory-table">', false);
        $this->assertSingleCarouselSelection($allOccasional->getContent());
        $this->assertSame(0, substr_count($allOccasional->getContent(), 'class="occasional-directory-row"'));

        $tag = $this->get(route('services.catalog', ['category' => 'Telefonia Fija', 'occasional' => 1]))->assertOk()
            ->assertSee('href="'.route('services.catalog', ['category' => 'Telefonia Fija']).'"', false)
            ->assertDontSee('<table class="service-directory-table">', false);
        $this->assertSingleCarouselSelection($tag->getContent());
        $this->assertSame(0, substr_count($tag->getContent(), 'class="occasional-directory-row"'));
    }

    public function test_authorized_finance_user_can_create_and_select_occasional_service_without_creating_recurring_payment(): void
    {
        $user = $this->financeUser(['finance.services.catalog', 'finance.services.create']);

        $this->actingAs($user)->post(route('services.occasional.store'), [
            '_service_form' => 'occasional',
            'name' => 'Estacionamiento',
            'reference_number' => 'EST-2026-001',
            'description' => 'Pago ocasional de estacionamiento.',
            'icon' => 'parking',
            'color' => 'cyan',
        ])->assertRedirect();

        $service = OccasionalService::where('name', 'Estacionamiento')->firstOrFail();
        $this->assertSame($user->id, $service->created_by);
        $entry = OccasionalServiceEntry::where('occasional_service_id', $service->id)->firstOrFail();
        $this->assertSame('EST-2026-001', $entry->reference_number);
        $this->assertSame(0, RecurringService::count());

        $this->actingAs($user)->get(route('services.catalog', ['occasional' => $service->id]))
            ->assertOk()
            ->assertSee('Estacionamiento')
            ->assertSee('EST-2026-001')
            ->assertSee('Pago ocasional de estacionamiento.')
            ->assertSee('aria-current="page"', false);
    }

    public function test_new_service_uses_selected_category_and_reference_in_table(): void
    {
        $this->actingAs($this->financeUser(['finance.services.catalog', 'finance.services.create']));
        $tag = OccasionalService::where('name', 'Tag')->firstOrFail();
        $gas = OccasionalService::where('name', 'Vale de Gasolina')->firstOrFail();

        $this->post(route('services.occasional.store'), [
            '_service_form' => 'occasional',
            'category_id' => $tag->id,
            'name' => 'Caseta norte',
            'reference_number' => 'REF-7788',
            'description' => 'Pago de peaje.',
        ])->assertRedirect(route('services.catalog', ['occasional' => $tag->id]));

        $this->assertSame(4, OccasionalService::count());
        $this->assertDatabaseHas('occasional_service_entries', [
            'occasional_service_id' => $tag->id,
            'name' => 'Caseta norte',
            'reference_number' => 'REF-7788',
        ]);

        $selected = $this->get(route('services.catalog', ['occasional' => $tag->id]))->assertOk()
            ->assertSee('Caseta norte')
            ->assertSee('REF-7788')
            ->assertSee('Pago de peaje.');
        $this->assertSame(1, substr_count($selected->getContent(), 'class="occasional-directory-row"'));

        $other = $this->get(route('services.catalog', ['occasional' => $gas->id]))->assertOk()
            ->assertDontSee('Caseta norte')
            ->assertDontSee('REF-7788');
        $this->assertSame(0, substr_count($other->getContent(), 'class="occasional-directory-row"'));
    }

    public function test_reference_is_required_and_unique_within_category(): void
    {
        $this->actingAs($this->financeUser(['finance.services.catalog', 'finance.services.create']));
        $tag = OccasionalService::where('name', 'Tag')->firstOrFail();
        $gas = OccasionalService::where('name', 'Vale de Gasolina')->firstOrFail();

        $this->post(route('services.occasional.store'), [
            '_service_form' => 'occasional', 'category_id' => $tag->id, 'name' => 'Sin referencia',
        ])->assertSessionHasErrors('reference_number');
        $this->assertSame(0, OccasionalServiceEntry::count());

        $this->post(route('services.occasional.store'), [
            '_service_form' => 'occasional', 'category_id' => $tag->id,
            'name' => 'Caseta norte', 'reference_number' => 'REF-7788',
        ])->assertRedirect();
        $this->post(route('services.occasional.store'), [
            '_service_form' => 'occasional', 'category_id' => $tag->id,
            'name' => 'Caseta sur', 'reference_number' => 'REF-7788',
        ])->assertSessionHasErrors('reference_number');
        $this->assertSame(1, OccasionalServiceEntry::count());

        $this->post(route('services.occasional.store'), [
            '_service_form' => 'occasional', 'category_id' => $gas->id,
            'name' => 'Vale semanal', 'reference_number' => 'REF-7788',
        ])->assertRedirect();
        $this->assertSame(2, OccasionalServiceEntry::count());
    }

    public function test_invalid_occasional_service_reopens_modal_and_does_not_create_record(): void
    {
        $catalog = route('services.catalog');

        $this->actingAs($this->superAdmin())->from($catalog)->post(route('services.occasional.store'), [
            '_service_form' => 'occasional',
            'name' => 'Tag',
            'reference_number' => 'TAG-1',
            'icon' => 'unknown',
            'color' => 'cyan',
        ])->assertRedirect($catalog)->assertSessionHasErrors(['name', 'icon']);

        $this->get($catalog)->assertOk()
            ->assertSee('id="occasional-create-dialog"', false)
            ->assertSee('data-auto-open', false);
        $this->assertSame(4, OccasionalService::count());
    }

    public function test_catalog_only_finance_user_cannot_create_occasional_service(): void
    {
        $user = $this->financeUser(['finance.services.catalog']);

        $this->actingAs($user)->get(route('services.catalog'))
            ->assertOk()
            ->assertDontSee('<button class="button primary" type="button" data-occasional-create-open>', false)
            ->assertDontSee('href="'.route('services.months', ['pay_occasional' => 1]).'">Pagar servicio ocasional</a>', false);

        $this->post(route('services.occasional.store'), [
            'name' => 'Estacionamiento', 'icon' => 'parking', 'color' => 'cyan',
        ])->assertForbidden();
    }

    public function test_occasional_export_is_available_to_catalog_viewers_and_escapes_spreadsheet_formulas(): void
    {
        $this->actingAs($this->superAdmin())->post(route('services.occasional.store'), [
            'name' => '=SUM(1+1)', 'reference_number' => '=REF-1', 'icon' => 'star', 'color' => 'purple',
        ])->assertRedirect();

        $response = $this->actingAs($this->financeUser(['finance.services.catalog']))
            ->get(route('services.occasional.export'));

        $response->assertOk()->assertDownload('servicios-ocasionales.csv');
        $content = $response->streamedContent();
        $this->assertStringContainsString("'=SUM(1+1)", $content);
        $this->assertStringContainsString("'=REF-1", $content);
        $this->assertStringContainsString('Número de referencia', $content);
    }

    private function superAdmin(): User
    {
        return User::factory()->create(['role' => 'superadmin', 'active' => true]);
    }

    private function assertSingleCarouselSelection(string $html): void
    {
        preg_match_all('/<nav class="service-categories"[^>]*>(.*?)<\/nav>/s', $html, $carousels);

        $this->assertCount(2, $carousels[1]);
        $this->assertSame(1, substr_count(implode('', $carousels[1]), 'aria-current="page"'));
    }

    private function financeUser(array $permissions): User
    {
        return User::factory()->create([
            'role' => 'finance', 'active' => true, 'menu_permissions' => $permissions,
        ]);
    }
}
