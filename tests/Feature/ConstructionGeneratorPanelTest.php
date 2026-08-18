<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConstructionGeneratorPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_unit_price_menu_opens_its_own_construction_panel(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('construction.placeholder', 'tabulador-precios-unitarios'));

        $response->assertOk();
        $response->assertSee('Tabulador de precios unitarios');
        $response->assertSee('href="'.route('construction.placeholder', 'tabulador-precios-unitarios').'"', false);
        $response->assertSee('Tabulador general de precios unitarios');
        $response->assertSee('P.U. mano de obra');
        $response->assertSee('P.U. materiales');
        $response->assertSee('P.U. total');
        $response->assertSee('data-filter-search', false);
        $response->assertDontSee('class="unit-price-table" data-no-column-tools', false);
        $response->assertSee('AB12BB');
        $response->assertSee('$212.38');
        $response->assertSee('No publicado');
        $this->assertSame(500, $response->viewData('unitPrices')->perPage());
        $this->assertSame(12, $response->viewData('unitPrices')->lastPage());
        $this->assertDatabaseCount('construction_unit_prices', 5674);
        $response->assertSeeInOrder([
            'Compras',
            'Tabulador de precios unitarios',
            'Alta de proveedor',
        ]);
    }

    public function test_unit_price_catalog_can_be_searched_and_filtered_by_chapter(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $searchResponse = $this->actingAs($user)->get(route('construction.placeholder', [
            'section' => 'tabulador-precios-unitarios',
            'q' => 'FG12BB',
        ]));

        $searchResponse->assertOk();
        $searchResponse->assertSee('FG12BB');
        $searchResponse->assertSee('$3,829.62');
        $this->assertSame(1, $searchResponse->viewData('unitPrices')->total());

        $chapterResponse = $this->actingAs($user)->get(route('construction.placeholder', [
            'section' => 'tabulador-precios-unitarios',
            'chapter' => 'F',
        ]));

        $chapterResponse->assertOk();
        $this->assertSame(117, $chapterResponse->viewData('unitPrices')->total());
        $chapterResponse->assertSee('Concreto hidraulico');
    }

    public function test_materials_carousel_starts_with_the_supply_explosion_catalog_button(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('construction.placeholder', 'materiales-insumos'));

        $response->assertOk();
        $response->assertSee('data-materials-catalog-select', false);
        $response->assertSee('construction-project-tile-catalog', false);
        $response->assertSee('href="#materials-explosion-catalog"', false);
        $response->assertSee('Catalogo de explosion de insumos');
        $response->assertSee('Catalogo general');
        $response->assertSee('Informacion general');
        $response->assertSeeInOrder([
            'Catalogo de explosion de insumos',
            'OBR-001',
        ]);
        $response->assertSee('id="materials-explosion-catalog"', false);
        $response->assertSee('data-materials-explosion-panel', false);
        $response->assertSee('aria-hidden="true"', false);
        $response->assertSee('Detalle de explosion de insumos');
        $response->assertSee('Insumo requerido = Cantidad de obra x Factor de consumo + Merma');
        $response->assertSee('Pintura');
        $response->assertSee('Concreto');
        $response->assertSee('Cemento CPC 30R');
        $this->assertSame(4, substr_count($response->getContent(), 'bultos/m3'));
        $this->assertSame(4, substr_count($response->getContent(), '>bulto de 50 kg<'));
        $this->assertSame(8, substr_count($response->getContent(), '>bultos<'));
        $response->assertSee('Muro de block 12 x 12 x 20 cm');
        $response->assertSee('Block hueco 12 x 12 x 20 cm');
        $response->assertSee('5 conceptos');
        $response->assertSee('data-materials-category-toggle', false);
        $response->assertSee('data-materials-concept-toggle', false);
        $response->assertSee('data-materials-download', false);
    }

    public function test_superadmin_can_view_the_construction_generator_panel(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('construction.placeholder', 'generadores-obra'));

        $response->assertOk();
        $response->assertSee('Obras activas');
        $response->assertSee('Niveles de la obra');
        $response->assertSee('Detalle de cuantificacion');
        $response->assertSee('data-generator-level="nivel-02"', false);
        $response->assertSee('Muros de block');
        $response->assertSee('Descargar Excel');
        $response->assertSee('data-generator-concept aria-label="Concepto" readonly', false);
        $response->assertSee('data-generator-unit aria-label="Unidad" disabled', false);
        $response->assertSee('data-generator-edit-row aria-pressed="false"', false);
        $response->assertDontSee('Nueva obra');
        $response->assertDontSee(route('construction.projects.create'), false);
        $response->assertDontSee('Resumen por nivel');
        $response->assertDontSee('data-generator-view="summary"', false);
    }
}
