<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerPurchaseOrderWarehouseTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_cannot_be_sent_without_a_warehouse(): void
    {
        $user = $this->createSuperAdmin();
        $company = $this->createCompany([]);
        $provider = $this->createProvider($user);
        $payload = $this->orderPayload($company, $provider);

        unset($payload['warehouse']);

        $this->actingAs($user)
            ->post(route('buyer.orders.store'), $payload)
            ->assertSessionHasErrors('warehouse');

        $this->assertDatabaseCount('purchase_orders', 0);
    }

    public function test_order_cannot_be_sent_when_the_company_has_no_warehouses(): void
    {
        $user = $this->createSuperAdmin();
        $company = $this->createCompany([]);
        $provider = $this->createProvider($user);

        $this->actingAs($user)
            ->post(route('buyer.orders.store'), $this->orderPayload($company, $provider, 'Almacen inventado'))
            ->assertSessionHasErrors('warehouse');

        $this->assertDatabaseCount('purchase_orders', 0);
    }

    public function test_order_cannot_be_sent_with_a_warehouse_from_another_company(): void
    {
        $user = $this->createSuperAdmin();
        $company = $this->createCompany(['Almacen Norte']);
        $provider = $this->createProvider($user);

        $this->actingAs($user)
            ->post(route('buyer.orders.store'), $this->orderPayload($company, $provider, 'Almacen Sur'))
            ->assertSessionHasErrors('warehouse');

        $this->assertDatabaseCount('purchase_orders', 0);
    }

    public function test_order_is_sent_with_an_authorized_warehouse(): void
    {
        $user = $this->createSuperAdmin();
        $company = $this->createCompany(['Almacen Norte']);
        $provider = $this->createProvider($user);

        $this->actingAs($user)
            ->post(route('buyer.orders.store'), $this->orderPayload($company, $provider, 'Almacen Norte'))
            ->assertRedirect(route('buyer.orders.index'));

        $this->assertDatabaseHas('purchase_orders', [
            'company_id' => $company->id,
            'provider_id' => $provider->id,
            'warehouse' => 'Almacen Norte',
            'status' => 'sent',
        ]);
    }

    private function createSuperAdmin(): User
    {
        return User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
    }

    private function createCompany(array $warehouses): Company
    {
        return Company::create([
            'name' => 'Empresa de prueba',
            'rfc' => 'EPR010101AAA',
            'warehouses' => $warehouses,
        ]);
    }

    private function createProvider(User $buyer): Provider
    {
        return Provider::create([
            'buyer_id' => $buyer->id,
            'business_name' => 'Proveedor de prueba',
            'rfc' => 'PPR010101AAA',
            'business_line' => 'Materiales',
            'bank' => 'Banco de prueba',
            'account_number' => '1234567890',
            'clabe' => '123456789012345678',
            'reference' => 'Referencia de prueba',
        ]);
    }

    private function orderPayload(Company $company, Provider $provider, ?string $warehouse = null): array
    {
        return [
            'company_id' => $company->id,
            'provider_id' => $provider->id,
            'warehouse' => $warehouse,
            'delivery_date' => '2026-09-10',
            'due_date' => '2026-09-15',
            'is_credit' => false,
            'reference' => '',
            'payment_concept' => 'Compra de prueba',
            'observations' => '',
            'items' => [
                [
                    'article' => 'Material de prueba',
                    'quantity' => 2,
                    'unit_price' => 100,
                ],
            ],
        ];
    }
}
