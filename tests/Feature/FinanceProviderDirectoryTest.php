<?php

namespace Tests\Feature;

use App\Models\Provider;
use App\Models\ProviderBusinessLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceProviderDirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_filters_providers_and_keeps_all_details_and_actions(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $buyerA = User::factory()->create(['role' => 'buyer', 'active' => true, 'name' => 'Comprador Norte']);
        $buyerB = User::factory()->create(['role' => 'buyer', 'active' => true, 'name' => 'Comprador Sur']);
        $matching = $this->provider($buyerA, 'Proveedor Norte', 'Materiales', 'Acero');
        $matching->update([
            'contact_name' => 'Ana Contacto',
            'phone' => '5512345678',
            'address' => 'Calle Norte 10',
            'reference' => 'REF-123',
        ]);
        $other = $this->provider($buyerB, 'Proveedor Sur', 'Servicios', 'Limpieza');

        $response = $this->actingAs($admin)->get(route('finance.admin.providers', [
            'q' => 'Ana Contacto',
            'buyer' => $buyerA->id,
            'line' => 'Materiales',
            'subcategory' => 'Acero',
        ]));

        $response->assertOk()
            ->assertSee('<h1>Proveedores</h1>', false)
            ->assertSee('Alta de proveedor')
            ->assertSee('Catálogo de proveedores')
            ->assertSee('id="provider-detail-'.$matching->id.'"', false)
            ->assertDontSee('id="provider-detail-'.$other->id.'"', false)
            ->assertSee('Ana Contacto')
            ->assertSee('5512345678')
            ->assertSee('Calle Norte 10')
            ->assertSee('REF-123')
            ->assertSee($matching->account_number)
            ->assertSee($matching->clabe)
            ->assertSee(route('finance.admin.providers.edit', $matching))
            ->assertSee(route('reports.download', 'providers'))
            ->assertSee(route('reports.download', 'providers-excel'));
    }

    public function test_catalog_paginates_and_preserves_filters(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $buyer = User::factory()->create(['role' => 'buyer', 'active' => true]);
        foreach (range(1, 16) as $number) {
            $this->provider($buyer, 'Proveedor '.str_pad((string) $number, 2, '0', STR_PAD_LEFT), 'Materiales', 'Acero');
        }

        $this->actingAs($admin)->get(route('finance.admin.providers', ['line' => 'Materiales']))
            ->assertOk()
            ->assertSee('Mostrando 1–15 de 16 proveedores')
            ->assertSee('line=Materiales&amp;page=2', false);

        $this->get(route('finance.admin.providers', ['line' => 'Materiales', 'page' => 2]))
            ->assertOk()
            ->assertSee('Mostrando 16–16 de 16 proveedores');
    }

    public function test_category_carousel_uses_the_existing_line_filter_and_keeps_search_context(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $buyer = User::factory()->create(['role' => 'buyer', 'active' => true]);
        $materials = $this->provider($buyer, 'Proveedor Acero', 'Materiales', 'Acero');
        $medicines = $this->provider($buyer, 'Proveedor Salud', 'Medicamentos', 'Farmacia');

        $this->actingAs($admin)->get(route('finance.admin.providers', ['q' => 'Proveedor', 'buyer' => $buyer->id]))
            ->assertOk()
            ->assertSee('aria-label="Categorías de proveedores"', false)
            ->assertSee(str_replace('&', '&amp;', route('finance.admin.providers', [
                'q' => 'Proveedor', 'buyer' => $buyer->id, 'line' => 'Materiales',
            ])), false);

        $this->get(route('finance.admin.providers', ['line' => 'Materiales']))
            ->assertOk()
            ->assertSee('id="provider-detail-'.$materials->id.'"', false)
            ->assertDontSee('id="provider-detail-'.$medicines->id.'"', false)
            ->assertSee('aria-current="page"', false);
    }

    public function test_provider_create_popup_preserves_validation_and_store_flow(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $buyer = User::factory()->create(['role' => 'buyer', 'active' => true]);
        $line = ProviderBusinessLine::create(['name' => 'Materiales', 'active' => true]);
        $catalog = route('finance.admin.providers');

        $this->actingAs($admin)->from($catalog)->post(route('finance.admin.providers.store'), [
            '_provider_form' => 'create',
            'buyer_id' => $buyer->id,
            'business_name' => 'Proveedor nuevo',
        ])->assertRedirect($catalog)->assertSessionHasErrors();

        $this->get($catalog)->assertOk()
            ->assertSee('data-auto-open', false)
            ->assertSee('value="Proveedor nuevo"', false);

        $this->post(route('finance.admin.providers.store'), [
            '_provider_form' => 'create',
            'buyer_id' => $buyer->id,
            'business_name' => 'Proveedor nuevo',
            'rfc' => 'PRN260923AB1',
            'business_line_id' => $line->id,
            'bank' => 'Banco Uno',
            'account_number' => '1234567890',
            'clabe' => '012345678901234567',
        ])->assertRedirect($catalog);

        $this->assertDatabaseHas('providers', [
            'buyer_id' => $buyer->id,
            'business_name' => 'Proveedor nuevo',
            'rfc' => 'PRN260923AB1',
        ]);
    }

    public function test_provider_edit_opens_from_catalog_and_preserves_validation_and_update_flow(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $buyer = User::factory()->create(['role' => 'buyer', 'active' => true]);
        $line = ProviderBusinessLine::create(['name' => 'Materiales', 'active' => true]);
        $provider = $this->provider($buyer, 'Proveedor original', $line->name, '');
        $catalog = route('finance.admin.providers');

        $this->actingAs($admin)->get($catalog)->assertOk()
            ->assertSee('id="provider-edit-'.$provider->id.'"', false)
            ->assertSee('data-provider-edit-open', false)
            ->assertSee('action="'.route('finance.admin.providers.update', $provider).'"', false)
            ->assertSee('value="Proveedor original"', false);

        $this->from($catalog)->put(route('finance.admin.providers.update', $provider), [
            '_provider_edit_id' => $provider->id,
            'business_name' => 'Proveedor corregido',
            'rfc' => $provider->rfc,
            'business_line_id' => $line->id,
            'bank' => 'Banco Uno',
            'account_number' => '1234567890',
            'clabe' => '123',
        ])->assertRedirect($catalog)->assertSessionHasErrors('clabe');

        $this->get($catalog)->assertOk()
            ->assertSee('data-auto-open', false)
            ->assertSee('value="Proveedor corregido"', false);

        $this->put(route('finance.admin.providers.update', $provider), [
            '_provider_edit_id' => $provider->id,
            'business_name' => 'Proveedor corregido',
            'rfc' => $provider->rfc,
            'business_line_id' => $line->id,
            'bank' => 'Banco Nuevo',
            'account_number' => '1234567890',
            'clabe' => '012345678901234567',
        ])->assertRedirect($catalog);

        $this->assertDatabaseHas('providers', [
            'id' => $provider->id,
            'business_name' => 'Proveedor corregido',
            'bank' => 'Banco Nuevo',
        ]);
    }

    private function provider(User $buyer, string $name, string $line, string $subcategory): Provider
    {
        return Provider::create([
            'buyer_id' => $buyer->id,
            'business_name' => $name,
            'rfc' => 'RFC'.substr(md5($name), 0, 10),
            'business_line' => $line,
            'provider_business_subcategory' => $subcategory,
            'bank' => 'Banco Uno',
            'account_number' => '1234567890',
            'clabe' => '012345678901234567',
        ]);
    }
}
