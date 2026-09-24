<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\SecurityCompany;
use App\Models\SecurityBranch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceCompanyWarehouseTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_creation_requires_a_warehouse_with_name_and_address(): void
    {
        $this->actingAs($this->superAdmin());

        $company = [
            'name' => 'Empresa sin almacen capturado',
            'rfc' => 'ESA260820A11',
            'address' => 'Avenida Principal 100, Ciudad de Mexico',
        ];

        $this->post(route('finance.admin.companies.store'), $company)
            ->assertSessionHasErrors('warehouses');
        $this->post(route('finance.admin.companies.store'), $company + ['warehouses' => []])
            ->assertSessionHasErrors('warehouses');
        $this->post(route('finance.admin.companies.store'), $company + [
            'warehouses' => [['name' => '', 'address' => $company['address']]],
        ])->assertSessionHasErrors('warehouses.0.name');
        $this->post(route('finance.admin.companies.store'), $company + [
            'warehouses' => [['name' => 'Almacen principal', 'address' => '']],
        ])->assertSessionHasErrors('warehouses.0.address');

        $this->assertDatabaseMissing('companies', ['rfc' => $company['rfc']]);
    }

    public function test_company_creation_saves_the_required_warehouse_with_company_address(): void
    {
        $this->actingAs($this->superAdmin());

        $this->post(route('finance.admin.companies.store'), [
            'name' => 'Empresa con almacen principal',
            'rfc' => 'ESA260820A11',
            'address' => 'Avenida Principal 100, Ciudad de Mexico',
            'warehouses' => [[
                'name' => 'Almacen principal',
                'short_name' => 'Principal',
                'address' => 'Avenida Principal 100, Ciudad de Mexico',
            ]],
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

    public function test_company_directory_shows_all_companies_without_pagination(): void
    {
        $this->actingAs($this->superAdmin());

        $companies = collect(range(1, 15))->map(fn (int $number) => Company::create([
            'name' => sprintf('Empresa %02d', $number),
            'rfc' => sprintf('EMP%08d', $number),
            'address' => 'Ciudad de México',
        ]));

        foreach ([[], ['page' => 2]] as $query) {
            $response = $this->get(route('finance.admin.companies', $query))
                ->assertOk()
                ->assertSee('Mostrando 15 empresas')
                ->assertDontSee('aria-label="Páginas de empresas"', false);

            foreach ($companies as $company) {
                $response->assertSee('id="company-'.$company->id.'"', false);
            }
        }
    }

    public function test_location_carousel_filters_fiscal_addresses_and_branch_states_without_matching_street_names(): void
    {
        $this->actingAs($this->superAdmin());
        $capital = Company::create([
            'name' => 'Empresa Capital', 'rfc' => 'ECA260923A11', 'business_line' => 'Salud',
            'address' => 'Guanajuato #131, Roma Norte, Cuauhtémoc, CDMX, c.p. 06700',
        ]);
        $capitalPadded = Company::create([
            'name' => 'Empresa Capital Dos', 'rfc' => 'ECD260923D44', 'business_line' => 'Salud',
            'address' => 'Calle 2, Cuauhtémoc, CDMX , c.p. 03100',
        ]);
        $north = Company::create([
            'name' => 'Empresa Norte', 'rfc' => 'ENO260923B22', 'business_line' => 'Servicios',
            'address' => 'Fiscal 100, Monterrey',
        ]);
        $securityCompany = SecurityCompany::create([
            'name' => $capital->name, 'entity_type' => 'business',
            'rfc' => $capital->rfc, 'finance_company_id' => $capital->id,
        ]);
        SecurityBranch::create([
            'security_company_id' => $securityCompany->id, 'name' => 'Sucursal Guadalajara',
            'state' => 'Jalisco', 'city' => 'Guadalajara',
        ]);

        $this->get(route('finance.admin.companies', ['q' => 'Empresa', 'giro' => 'Salud']))
            ->assertOk()
            ->assertSee('aria-label="Ubicación de empresas"', false)
            ->assertSee(str_replace('&', '&amp;', route('finance.admin.companies', [
                'q' => 'Empresa', 'giro' => 'Salud', 'location' => 'CDMX',
            ])), false);

        $this->get(route('finance.admin.companies', ['location' => 'CDMX', 'giro' => 'Salud']))
            ->assertOk()
            ->assertSee('id="company-'.$capital->id.'"', false)
            ->assertSee('id="company-'.$capitalPadded->id.'"', false)
            ->assertDontSee('id="company-'.$north->id.'"', false)
            ->assertSee('name="location" value="CDMX"', false);

        $this->get(route('finance.admin.companies', ['location' => 'Monterrey']))
            ->assertOk()->assertSee('id="company-'.$north->id.'"', false)
            ->assertDontSee('id="company-'.$capital->id.'"', false);

        $this->get(route('finance.admin.companies', ['location' => 'Guadalajara']))
            ->assertOk()->assertSee('id="company-'.$capital->id.'"', false)
            ->assertDontSee('id="company-'.$north->id.'"', false);

        $this->get(route('finance.admin.companies', ['location' => 'Guanajuato']))
            ->assertOk()->assertDontSee('id="company-'.$capital->id.'"', false);
    }

    public function test_company_creation_can_register_branches_and_link_a_warehouse(): void
    {
        $this->actingAs($this->superAdmin());

        $this->post(route('finance.admin.companies.store'), [
            'name' => 'Operadora de Prueba',
            'rfc' => 'OPR260923A11',
            'address' => 'Fiscal 100, Monterrey',
            'primary_branch' => 1,
            'branches' => [
                0 => ['name' => 'Sucursal Norte', 'code' => 'NOR-01', 'address' => 'Norte 15', 'state' => 'Nuevo León', 'city' => 'Monterrey'],
                1 => ['name' => 'Sucursal Centro', 'code' => 'CEN-01', 'address' => 'Centro 20', 'state' => 'Nuevo León', 'city' => 'Monterrey', 'contact_name' => 'Ana López', 'phone' => '8112345678'],
            ],
            'warehouses' => [
                ['name' => 'Almacén Centro', 'short_name' => 'Principal', 'address' => 'Centro 20', 'branch_index' => 1],
            ],
        ])->assertRedirect(route('finance.admin.companies'));

        $company = Company::where('rfc', 'OPR260923A11')->firstOrFail();
        $securityCompany = SecurityCompany::where('finance_company_id', $company->id)->firstOrFail();
        $center = $securityCompany->branches()->where('code', 'CEN-01')->firstOrFail();

        $this->assertSame($company->rfc, $securityCompany->rfc);
        $this->assertSame(2, $securityCompany->branches()->count());
        $this->assertTrue($center->is_primary);
        $this->assertSame('Ana López', $center->contact_name);
        $this->assertSame('8112345678', $center->phone);
        $this->assertSame($center->id, $company->warehouseObjects()[0]['branch_id']);

        $this->get(route('finance.admin.companies'))->assertOk()
            ->assertSee('Sucursal Centro')
            ->assertSee('Sucursal: Sucursal Centro');
        $this->get(route('finance.admin.companies.edit', $company))
            ->assertOk()->assertSee('name="warehouses[0][branch_id]"', false);
        $this->put(route('finance.admin.companies.update', $company), [
            'name' => $company->name, 'rfc' => $company->rfc, 'address' => $company->address,
            'warehouses' => [['name' => 'Almacén Centro', 'short_name' => 'Principal', 'address' => 'Centro 20', 'branch_id' => $center->id]],
        ])->assertRedirect(route('finance.admin.companies'));
        $this->assertSame($center->id, $company->fresh()->warehouseObjects()[0]['branch_id']);
        $this->get(route('security.index', ['section' => 'branches', 'company_id' => $securityCompany->id]))
            ->assertOk()->assertSee('Sucursal Centro');
    }

    public function test_company_creation_reuses_an_unlinked_security_company_with_matching_rfc(): void
    {
        $this->actingAs($this->superAdmin());
        $securityCompany = SecurityCompany::create([
            'name' => 'Vigilancia Operadora', 'entity_type' => 'business', 'rfc' => 'VOP260923B22',
        ]);

        $this->post(route('finance.admin.companies.store'), [
            'name' => 'Operadora Vinculada', 'rfc' => 'VOP260923B22', 'address' => 'Centro 15',
            'warehouses' => [['name' => 'Almacen principal', 'address' => 'Centro 15']],
            'branches' => [0 => ['name' => 'Sucursal Sur', 'address' => 'Sur 50', 'state' => 'Jalisco', 'city' => 'Guadalajara']],
        ])->assertRedirect(route('finance.admin.companies'));

        $company = Company::where('rfc', 'VOP260923B22')->firstOrFail();
        $this->assertSame($company->id, $securityCompany->fresh()->finance_company_id);
        $this->assertSame(1, SecurityCompany::where('rfc', 'VOP260923B22')->count());
        $this->assertTrue($securityCompany->branches()->firstOrFail()->is_primary);
    }

    public function test_existing_company_can_add_a_branch_from_the_directory(): void
    {
        $this->actingAs($this->superAdmin());
        $company = Company::create([
            'name' => 'Empresa con nueva sucursal', 'rfc' => 'ENS260923A11', 'address' => 'Fiscal 100, Monterrey',
        ]);

        $this->get(route('finance.admin.companies', ['company_id' => $company->id]))
            ->assertOk()
            ->assertSee('Agregar Sucursal')
            ->assertSee('data-company-branch-action="'.route('finance.admin.companies.branches.store', $company).'"', false)
            ->assertSee('No hay sucursales vinculadas.');

        $this->post(route('finance.admin.companies.branches.store', $company), [
            '_branch_form' => '1', '_branch_company_id' => $company->id,
            'branch' => [
                'name' => 'Sucursal Norte', 'code' => 'NOR-01', 'address' => 'Norte 25, Monterrey',
                'state' => 'Nuevo León', 'city' => 'Monterrey', 'contact_name' => 'Ana López', 'phone' => '8112345678',
            ],
        ])->assertRedirect(route('finance.admin.companies', ['company_id' => $company->id]));

        $securityCompany = SecurityCompany::where('finance_company_id', $company->id)->firstOrFail();
        $branch = $securityCompany->branches()->firstOrFail();
        $this->assertSame('Sucursal Norte', $branch->name);
        $this->assertSame('NOR-01', $branch->code);
        $this->assertSame('Ana López', $branch->contact_name);
        $this->assertTrue($branch->is_primary);

        $this->get(route('finance.admin.companies', ['company_id' => $company->id]))
            ->assertOk()
            ->assertSee('Sucursal Norte')
            ->assertSee('Norte 25, Monterrey')
            ->assertSee('Ver / Editar')
            ->assertSee(str_replace('&', '&amp;', route('security.index', [
                'section' => 'branches', 'company_id' => $securityCompany->id,
                'branch_id' => $branch->id, 'edit_branch' => $branch->id,
            ])), false);
    }

    public function test_directory_branch_creation_reuses_matching_security_company_and_rejects_duplicates(): void
    {
        $this->actingAs($this->superAdmin());
        $company = Company::create(['name' => 'Operadora vinculada', 'rfc' => 'OVI260923B22', 'address' => 'Centro 15']);
        $securityCompany = SecurityCompany::create([
            'name' => 'Vigilancia Operadora', 'entity_type' => 'business', 'rfc' => 'OVI260923B22',
        ]);
        $payload = [
            '_branch_form' => '1', '_branch_company_id' => $company->id,
            'branch' => ['name' => 'Sucursal Centro', 'address' => 'Centro 15', 'state' => 'Jalisco', 'city' => 'Guadalajara'],
        ];

        $this->post(route('finance.admin.companies.branches.store', $company), $payload)
            ->assertRedirect(route('finance.admin.companies', ['company_id' => $company->id]));
        $this->assertSame($company->id, $securityCompany->fresh()->finance_company_id);
        $this->assertSame(1, SecurityCompany::where('rfc', $company->rfc)->count());

        $this->from(route('finance.admin.companies', ['company_id' => $company->id]))
            ->post(route('finance.admin.companies.branches.store', $company), $payload)
            ->assertSessionHasErrors('branch.name');
        $this->assertSame(1, $securityCompany->branches()->count());
    }

    public function test_warehouse_can_be_viewed_and_edited_in_a_dialog_without_changing_other_company_data(): void
    {
        $this->actingAs($this->superAdmin());
        $company = Company::create([
            'name' => 'Empresa con almacenes', 'rfc' => 'ECA260923E55', 'address' => 'Domicilio fiscal',
            'warehouses' => [
                ['name' => 'Almacén central', 'short_name' => 'Central', 'address' => 'Centro 10'],
                ['name' => 'Almacén norte', 'short_name' => 'Norte', 'address' => 'Norte 20'],
            ],
        ]);
        $securityCompany = SecurityCompany::create([
            'name' => 'Vigilancia almacenes', 'entity_type' => 'company', 'finance_company_id' => $company->id,
        ]);
        $branch = $securityCompany->branches()->create(['name' => 'Sucursal Centro']);
        $warehouses = $company->warehouseObjects();
        $warehouses[0]['branch_id'] = $branch->id;
        $company->update(['warehouses' => $warehouses]);
        $buyer = User::factory()->create([
            'role' => 'buyer', 'active' => true,
            'companies' => [['name' => $company->name, 'warehouses' => ['Almacén central']]],
        ]);

        $this->get(route('finance.admin.companies', ['company_id' => $company->id]))
            ->assertOk()
            ->assertSee('data-dialog-target="edit-warehouse-'.$company->id.'-0"', false)
            ->assertSee('id="edit-warehouse-'.$company->id.'-0"', false)
            ->assertSee('value="Almacén central"', false)
            ->assertSee('Sucursal vinculada:');

        $this->patch(route('finance.admin.companies.warehouses.update', [$company, 0]), [
            'warehouse' => ['name' => 'Almacén renovado', 'short_name' => 'Nuevo', 'address' => 'Centro 15'],
        ])->assertRedirect(route('finance.admin.companies', ['company_id' => $company->id]));

        $company->refresh();
        $this->assertSame('Empresa con almacenes', $company->name);
        $this->assertSame('Domicilio fiscal', $company->address);
        $this->assertSame(['Almacén renovado'], $buyer->fresh()->authorizedWarehousesFor($company->name));
        $this->assertSame([
            ['name' => 'Almacén renovado', 'short_name' => 'Nuevo', 'address' => 'Centro 15', 'branch_id' => $branch->id],
            ['name' => 'Almacén norte', 'short_name' => 'Norte', 'address' => 'Norte 20'],
        ], $company->warehouseObjects());

        $this->from(route('finance.admin.companies', ['company_id' => $company->id]))
            ->patch(route('finance.admin.companies.warehouses.update', [$company, 0]), [
                '_warehouse_form' => '1', '_warehouse_company_id' => $company->id, '_warehouse_index' => 0,
                'warehouse' => ['name' => 'Almacén norte', 'address' => 'Centro 15'],
            ])->assertSessionHasErrors('warehouse.name');
        $this->get(route('finance.admin.companies', ['company_id' => $company->id]))
            ->assertOk()->assertSee('data-auto-open', false);
        $this->assertSame('Almacén renovado', $company->fresh()->warehouseObjects()[0]['name']);
        $this->patch(route('finance.admin.companies.warehouses.update', [$company, 99]), [
            'warehouse' => ['name' => 'No existe', 'address' => 'Sin dirección'],
        ])->assertNotFound();
    }

    public function test_invalid_branch_does_not_create_a_company(): void
    {
        $this->actingAs($this->superAdmin());

        $this->post(route('finance.admin.companies.store'), [
            'name' => 'Empresa Incompleta', 'rfc' => 'EIN260923C33', 'address' => 'Centro 15',
            'warehouses' => [['name' => 'Almacen principal', 'address' => 'Centro 15']],
            'branches' => [0 => ['name' => 'Sucursal sin estado', 'address' => 'Centro 20', 'city' => 'Monterrey']],
        ])->assertSessionHasErrors('branches.0.state');

        $this->assertDatabaseMissing('companies', ['rfc' => 'EIN260923C33']);
        $this->assertDatabaseMissing('security_companies', ['rfc' => 'EIN260923C33']);
    }

    public function test_finance_only_user_cannot_create_security_branches_from_company_form(): void
    {
        $this->actingAs(User::factory()->create([
            'role' => 'finance', 'active' => true, 'menu_permissions' => ['finance.companies'],
        ]));

        $this->post(route('finance.admin.companies.store'), [
            'name' => 'Empresa sin Permiso', 'rfc' => 'ESP260923D44', 'address' => 'Centro 15',
            'warehouses' => [['name' => 'Almacen principal', 'address' => 'Centro 15']],
            'branches' => [0 => ['name' => 'Sucursal', 'address' => 'Centro 20', 'state' => 'Nuevo León', 'city' => 'Monterrey']],
        ])->assertForbidden();

        $this->assertDatabaseMissing('companies', ['rfc' => 'ESP260923D44']);
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

    public function test_directory_uses_existing_company_warehouse_and_security_branch_flows(): void
    {
        $this->actingAs($this->superAdmin());

        $company = Company::create([
            'name' => 'Empresa de salud',
            'business_line' => 'Salud',
            'rfc' => 'ESA260923A11',
            'address' => 'Centro, Monterrey',
            'warehouses' => [
                ['name' => 'Almacén central', 'short_name' => 'Principal', 'address' => 'Centro, Monterrey'],
                ['name' => 'Almacén norte', 'short_name' => 'Norte', 'address' => 'Norte, Monterrey'],
            ],
        ]);
        $securityCompany = SecurityCompany::create([
            'name' => 'Seguridad Empresa de salud',
            'entity_type' => 'company',
            'finance_company_id' => $company->id,
        ]);
        $branch = $securityCompany->branches()->create([
            'name' => 'Sucursal Centro', 'address' => 'Centro, Monterrey',
            'country' => 'México', 'state' => 'Nuevo León', 'city' => 'Monterrey',
            'timezone' => 'America/Mexico_City', 'status' => 'active',
        ]);
        User::factory()->create(['role' => 'buyer', 'active' => true]);

        $this->get(route('finance.admin.companies'))
            ->assertOk()
            ->assertSee('Empresas y Negocios')
            ->assertSee('id="company-'.$company->id.'"', false)
            ->assertSee('Salud')
            ->assertSee('Sucursal Centro')
            ->assertSee('Almacén norte')
            ->assertSee('2 almacenes')
            ->assertSee(route('finance.admin.companies.edit', $company))
            ->assertSee(route('finance.admin.companies.destroy', $company))
            ->assertSee(route('reports.download', 'companies'))
            ->assertSee(str_replace('&', '&amp;', route('security.index', [
                'section' => 'branches', 'company_id' => $securityCompany->id,
                'branch_id' => $branch->id, 'edit_branch' => $branch->id,
            ])), false)
            ->assertSee(route('finance.admin.companies.branches.store', $company))
            ->assertSee('name="purchase_order_notes"', false)
            ->assertSee('name="buyer_ids[]"', false);
    }

    public function test_directory_filters_by_name_business_line_and_company(): void
    {
        $this->actingAs($this->superAdmin());
        $health = Company::create([
            'name' => 'Clínica Azul', 'rfc' => 'CAZ260923A11',
            'address' => 'Monterrey', 'business_line' => 'Salud',
        ]);
        $logistics = Company::create([
            'name' => 'Logística Sur', 'rfc' => 'LSU260923B22',
            'address' => 'Guadalajara', 'business_line' => 'Logística',
        ]);

        $this->get(route('finance.admin.companies', ['q' => 'Clínica']))
            ->assertOk()
            ->assertSee('id="company-'.$health->id.'"', false)
            ->assertDontSee('id="company-'.$logistics->id.'"', false);

        $this->get(route('finance.admin.companies', ['giro' => 'Logística']))
            ->assertOk()
            ->assertSee('id="company-'.$logistics->id.'"', false)
            ->assertDontSee('id="company-'.$health->id.'"', false);

        $this->get(route('finance.admin.companies', ['company_id' => $health->id]))
            ->assertOk()
            ->assertSee('id="company-'.$health->id.'"', false)
            ->assertDontSee('id="company-'.$logistics->id.'"', false);
    }

    public function test_business_line_can_be_created_edited_and_preserved_by_legacy_updates(): void
    {
        $this->actingAs($this->superAdmin());

        $this->post(route('finance.admin.companies.store'), [
            'name' => 'Servicios Verdes', 'rfc' => 'SVE260923C33',
            'business_line' => 'Servicios ambientales', 'address' => 'Ciudad de México',
            'warehouses' => [['name' => 'Almacen principal', 'address' => 'Ciudad de México']],
        ])->assertRedirect(route('finance.admin.companies'));

        $company = Company::where('rfc', 'SVE260923C33')->firstOrFail();
        $this->assertSame('Servicios ambientales', $company->business_line);

        $this->put(route('finance.admin.companies.update', $company), [
            'name' => $company->name, 'rfc' => $company->rfc, 'address' => $company->address,
        ])->assertRedirect(route('finance.admin.companies'));
        $this->assertSame('Servicios ambientales', $company->fresh()->business_line);

        $this->put(route('finance.admin.companies.update', $company), [
            'name' => $company->name, 'rfc' => $company->rfc, 'address' => $company->address,
            'business_line' => 'Consultoría',
        ])->assertRedirect(route('finance.admin.companies'));
        $this->assertSame('Consultoría', $company->fresh()->business_line);

        $this->get(route('finance.admin.companies.edit', $company))
            ->assertOk()->assertSee('value="Consultoría"', false);
    }

    public function test_finance_only_user_does_not_receive_security_branch_links(): void
    {
        $user = User::factory()->create([
            'role' => 'finance', 'active' => true, 'menu_permissions' => ['finance.companies'],
        ]);
        $company = Company::create([
            'name' => 'Empresa privada', 'rfc' => 'EPR260923D44', 'address' => 'Monterrey',
        ]);
        $securityCompany = SecurityCompany::create([
            'name' => 'Vigilancia privada', 'entity_type' => 'company', 'finance_company_id' => $company->id,
        ]);
        $securityCompany->branches()->create([
            'name' => 'Sucursal restringida', 'address' => 'Monterrey',
            'country' => 'México', 'state' => 'Nuevo León', 'city' => 'Monterrey',
            'timezone' => 'America/Mexico_City', 'status' => 'active',
        ]);

        $this->actingAs($user)->get(route('finance.admin.companies'))
            ->assertOk()
            ->assertDontSee('Sucursal restringida')
            ->assertDontSee('data-company-branch-action=');

        $this->post(route('finance.admin.companies.branches.store', $company), [
            'branch' => ['name' => 'Nueva sucursal', 'address' => 'Monterrey', 'state' => 'Nuevo León', 'city' => 'Monterrey'],
        ])->assertForbidden();
    }

    public function test_existing_unlinked_security_company_with_matching_rfc_keeps_its_branches_visible(): void
    {
        $this->actingAs($this->superAdmin());
        $company = Company::create([
            'name' => 'Operadora Norte', 'rfc' => 'ONO260923E55', 'address' => 'Monterrey',
        ]);
        $securityCompany = SecurityCompany::create([
            'name' => 'Operadora Norte Vigilancia', 'entity_type' => 'business',
            'rfc' => $company->rfc,
        ]);
        $securityCompany->branches()->create([
            'name' => 'Sucursal existente', 'address' => 'Monterrey',
            'country' => 'México', 'state' => 'Nuevo León', 'city' => 'Monterrey',
            'timezone' => 'America/Mexico_City', 'status' => 'active',
        ]);

        $this->get(route('finance.admin.companies'))
            ->assertOk()
            ->assertSee('Sucursal existente')
            ->assertSee('1 sucursal')
            ->assertSee(route('finance.admin.companies.branches.store', $company))
            ->assertDontSee('Vincular sucursales');
    }

    private function superAdmin(): User
    {
        return User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
    }
}
