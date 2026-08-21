<?php

namespace Tests\Feature;

use App\Models\Provider;
use App\Models\ProviderBusinessLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConstructionProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_construction_provider_section_has_the_full_registration_form(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('construction.providers.index'));

        $response->assertOk();
        $response->assertSee('Alta de proveedor');
        $response->assertSee('Nuevo proveedor');
        $response->assertSee('Los proveedores dados de alta aqui quedan disponibles para las ordenes de compra de Administracion de obra.');
        $response->assertSeeInOrder([
            'Razon social',
            'RFC',
            'Giro de proveeduria',
            'Subcategoria',
            'Banco',
            'Cuenta',
            'CLABE',
            'Referencia',
            'Guardar proveedor',
        ]);
        $response->assertSee('Mis proveedores');
        $response->assertSee('Proveedores disponibles para las ordenes de compra de Administracion de obra.');
        $response->assertSee('action="'.route('construction.providers.store').'"', false);
    }

    public function test_superadmin_can_register_a_provider_from_construction_administration(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $businessLine = ProviderBusinessLine::query()->where('active', true)->firstOrFail();

        $response = $this->actingAs($user)->post(route('construction.providers.store'), [
            'business_name' => 'Materiales de Obra del Centro',
            'rfc' => 'MOC260817AB1',
            'business_line_id' => $businessLine->id,
            'business_subcategory_id' => null,
            'bank' => 'BANORTE',
            'account_number' => '1234567890',
            'clabe' => '072180012345678901',
            'reference' => 'Proveedor de obra',
        ]);

        $response->assertRedirect(route('construction.providers.index'));
        $response->assertSessionHas('status', 'Proveedor registrado.');
        $this->assertDatabaseHas('providers', [
            'buyer_id' => $user->id,
            'business_name' => 'Materiales de Obra del Centro',
            'rfc' => 'MOC260817AB1',
            'provider_business_line_id' => $businessLine->id,
            'bank' => 'BANORTE',
            'clabe' => '072180012345678901',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'provider_created',
            'description' => 'Proveedor Materiales de Obra del Centro dado de alta para Administracion de obra.',
        ]);
    }

    public function test_legacy_construction_provider_url_redirects_to_the_provider_panel(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('construction.placeholder', 'proveedores'))
            ->assertRedirect(route('construction.providers.index'));
    }

    public function test_superadmin_can_show_and_hide_the_provider_categories_catalog(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('superadmin.provider-lines.index'));

        $response->assertOk();
        $response->assertSee('Categorias de proveedores');
        $response->assertSee('data-provider-category-toggle', false);
        $response->assertSee('aria-controls="provider-category-management"', false);
        $response->assertSee('aria-expanded="true"', false);
        $response->assertSee('categoryContent.hidden = !willExpand;', false);
    }

    public function test_superadmin_provider_catalog_links_to_provider_registration(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('superadmin.provider-lines.index'))
            ->assertOk()
            ->assertSee('Alta de proveedor')
            ->assertSee('href="'.route('finance.admin.providers').'"', false)
            ->assertSee('data-provider-catalog-action', false);

        $this->actingAs($user)
            ->get(route('finance.admin.providers'))
            ->assertOk()
            ->assertSee('Nuevo proveedor');
    }

    public function test_provider_catalog_is_ordered_by_category_before_business_name(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $alphaCategory = ProviderBusinessLine::create([
            'name' => 'Alfa categoria',
            'active' => true,
        ]);
        $zetaCategory = ProviderBusinessLine::create([
            'name' => 'Zeta categoria',
            'active' => true,
        ]);

        Provider::create([
            'buyer_id' => $user->id,
            'business_name' => 'AAA Proveedor de Zeta',
            'rfc' => 'APZ260819AA1',
            'business_line' => $zetaCategory->name,
            'provider_business_line_id' => $zetaCategory->id,
            'bank' => 'BANORTE',
            'account_number' => '1000000001',
            'clabe' => '072180000000000001',
        ]);
        Provider::create([
            'buyer_id' => $user->id,
            'business_name' => 'ZZZ Proveedor de Alfa',
            'rfc' => 'ZPA260819AA2',
            'business_line' => $alphaCategory->name,
            'provider_business_line_id' => $alphaCategory->id,
            'bank' => 'BBVA',
            'account_number' => '1000000002',
            'clabe' => '012180000000000002',
        ]);

        $this->actingAs($user)
            ->get(route('superadmin.provider-lines.index'))
            ->assertOk()
            ->assertSeeInOrder([
                'ZZZ Proveedor de Alfa',
                'AAA Proveedor de Zeta',
            ]);
    }
}
