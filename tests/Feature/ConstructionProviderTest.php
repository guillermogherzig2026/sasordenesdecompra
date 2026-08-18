<?php

namespace Tests\Feature;

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
}
