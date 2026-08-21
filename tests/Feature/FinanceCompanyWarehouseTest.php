<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceCompanyWarehouseTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_creation_adds_a_default_warehouse_with_company_address(): void
    {
        $this->actingAs($this->superAdmin());

        $this->post(route('finance.admin.companies.store'), [
            'name' => 'Empresa sin almacen capturado',
            'rfc' => 'ESA260820A11',
            'address' => 'Avenida Principal 100, Ciudad de Mexico',
        ])->assertRedirect(route('finance.admin.companies'));

        $company = Company::query()->where('rfc', 'ESA260820A11')->firstOrFail();

        $this->assertSame([
            [
                'name' => 'Almacen principal',
                'short_name' => 'Principal',
                'address' => 'Avenida Principal 100, Ciudad de Mexico',
            ],
        ], $company->warehouseObjects());
    }

    public function test_company_creation_keeps_an_independent_warehouse_address(): void
    {
        $this->actingAs($this->superAdmin());

        $this->post(route('finance.admin.companies.store'), [
            'name' => 'Empresa con almacen',
            'rfc' => 'ECA260820B22',
            'address' => 'Direccion fiscal 200, Monterrey',
            'warehouses' => [
                [
                    'name' => 'Centro de recepcion norte',
                    'short_name' => 'Norte',
                    'address' => 'Bodega 15, Parque Industrial Norte',
                ],
            ],
        ])->assertRedirect(route('finance.admin.companies'));

        $company = Company::query()->where('rfc', 'ECA260820B22')->firstOrFail();

        $this->assertSame('Direccion fiscal 200, Monterrey', $company->address);
        $this->assertSame('Bodega 15, Parque Industrial Norte', $company->warehouseObjects()[0]['address']);
    }

    public function test_company_update_cannot_leave_company_without_a_warehouse(): void
    {
        $this->actingAs($this->superAdmin());

        $company = Company::query()->create([
            'name' => 'Empresa existente',
            'rfc' => 'EEX260820C33',
            'address' => 'Direccion anterior',
            'warehouses' => [],
        ]);

        $this->put(route('finance.admin.companies.update', $company), [
            'name' => $company->name,
            'rfc' => $company->rfc,
            'address' => 'Direccion actualizada 300, Guadalajara',
        ])->assertRedirect(route('finance.admin.companies'));

        $company->refresh();

        $this->assertSame([
            [
                'name' => 'Almacen principal',
                'short_name' => 'Principal',
                'address' => 'Direccion actualizada 300, Guadalajara',
            ],
        ], $company->warehouseObjects());
    }

    private function superAdmin(): User
    {
        return User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
    }
}
