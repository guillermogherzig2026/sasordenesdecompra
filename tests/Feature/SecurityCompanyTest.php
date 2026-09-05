<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\SecurityBranch;
use App\Models\SecurityCamera;
use App\Models\SecurityCompany;
use App\Models\User;
use App\Support\NavigationPermissionCatalog;
use Database\Seeders\SecurityDemoCameraSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityCompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_company_is_stored_independently_and_can_reference_a_finance_company(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $financeCompany = Company::create([
            'name' => 'Grupo Sentinel',
            'rfc' => 'GSE260831AA1',
            'address' => 'Monterrey, Nuevo León',
        ]);

        $catalog = $this->actingAs($user)->get(route('security.index'));

        $catalog->assertOk()
            ->assertSee('Selecciona una empresa o negocio')
            ->assertSee('Gestiona y supervisa las empresas y negocios registrados en Vigilancia.')
            ->assertSee('Empresas registradas')
            ->assertSee('Entradas y salidas')
            ->assertSee('Resumen por empresa')
            ->assertSee('Sin datos para el periodo seleccionado')
            ->assertSee('Nueva empresa o negocio')
            ->assertSee('Empresa relacionada en Finanzas (opcional)')
            ->assertSee('Grupo Sentinel');

        $response = $this->actingAs($user)->post(route('security.companies.store'), [
            'name' => 'Grupo Sentinel',
            'entity_type' => 'business',
            'legal_name' => 'Sentinel Operaciones, S.A. de C.V.',
            'rfc' => 'SEO260831BB2',
            'address' => 'San Pedro Garza García, Nuevo León',
            'contact_name' => 'Laura Méndez',
            'contact_phone' => '81 1234 5678',
            'contact_email' => 'laura@example.com',
            'finance_company_id' => $financeCompany->id,
        ]);

        $securityCompany = SecurityCompany::firstOrFail();

        $response->assertRedirect(route('security.index', ['company' => $securityCompany->id]));
        $this->assertDatabaseCount('companies', 1);
        $this->assertDatabaseHas('security_companies', [
            'name' => 'Grupo Sentinel',
            'entity_type' => 'business',
            'finance_company_id' => $financeCompany->id,
        ]);
        $this->assertTrue($securityCompany->financeCompany->is($financeCompany));

        $this->actingAs($user)
            ->get(route('security.index', ['company' => $securityCompany->id]))
            ->assertOk()
            ->assertSee('Relacionada con Finanzas')
            ->assertSee('Cerrar detalle')
            ->assertSee('Sentinel Operaciones, S.A. de C.V.');
    }

    public function test_security_company_store_requires_the_companies_navigation_permission(): void
    {
        $user = User::factory()->create([
            'role' => 'administrative_assistant',
            'active' => true,
            'menu_permissions' => [NavigationPermissionCatalog::HOME_DASHBOARD],
        ]);

        $this->actingAs($user)
            ->post(route('security.companies.store'), [
                'name' => 'Negocio restringido',
                'entity_type' => 'business',
            ])
            ->assertForbidden();

        $user->update([
            'menu_permissions' => [NavigationPermissionCatalog::HOME_DASHBOARD, 'security.dashboard'],
        ]);

        $this->actingAs($user->fresh())
            ->post(route('security.companies.store'), [
                'name' => 'Negocio autorizado',
                'entity_type' => 'business',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('security_companies', ['name' => 'Negocio autorizado']);
    }

    public function test_security_submenus_share_and_remember_the_selected_company(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        SecurityCompany::create([
            'name' => 'Empresa inicial',
            'entity_type' => 'company',
        ]);
        $selectedCompany = SecurityCompany::create([
            'name' => 'Empresa seleccionada para Vigilancia',
            'entity_type' => 'business',
        ]);

        $this->actingAs($user)
            ->get(route('security.index', [
                'section' => 'cameras',
                'company_id' => $selectedCompany->id,
            ]))
            ->assertOk()
            ->assertSee('data-security-section-company-selector', false)
            ->assertSee('Empresa seleccionada para Vigilancia');

        foreach (['branches', 'visualization', 'analytics', 'alerts', 'users', 'reports', 'configuration'] as $section) {
            $this->get(route('security.index', ['section' => $section]))
                ->assertOk()
                ->assertSee('data-security-section-company-selector', false)
                ->assertSee('Empresa seleccionada para Vigilancia');
        }
    }

    public function test_branches_section_filters_the_camera_view_by_branch(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $securityCompany = SecurityCompany::create([
            'name' => 'Empresa con sucursales',
            'entity_type' => 'company',
        ]);
        $securityBranch = SecurityBranch::create([
            'security_company_id' => $securityCompany->id,
            'name' => 'Sucursal Centro',
            'code' => 'SC-01',
        ]);
        $securityBranch->cameras()->createMany([
            [
                'name' => 'Entrada principal',
                'stream_url' => 'https://cameras.example.com/entrada.m3u8',
                'sort_order' => 0,
            ],
            [
                'name' => 'Acceso lateral',
                'stream_url' => 'rtsp://10.0.0.30/live',
                'sort_order' => 1,
            ],
        ]);
        $emptyBranch = SecurityBranch::create([
            'security_company_id' => $securityCompany->id,
            'name' => 'Sucursal sin cámaras',
        ]);

        $this->actingAs($user)
            ->get(route('security.index', [
                'section' => 'branches',
                'company_id' => $securityCompany->id,
            ]))
            ->assertOk()
            ->assertSee('data-security-branch-select', false)
            ->assertSee('value="'.$securityBranch->id.'" selected', false)
            ->assertSee('2 cámaras')
            ->assertSee('data-security-camera-preview-grid', false);

        $this
            ->get(route('security.index', [
                'section' => 'branches',
                'company_id' => $securityCompany->id,
                'branch_id' => $securityBranch->id,
            ]))
            ->assertOk()
            ->assertSee('data-security-branch-camera-filter', false)
            ->assertSee('Sucursal Centro')
            ->assertSee('SC-01')
            ->assertSee('2 cámaras')
            ->assertSee('data-security-camera-preview-grid', false)
            ->assertSee('Cámaras activas')
            ->assertSee('data-security-active-cameras="0/2"', false)
            ->assertSee('Entrada principal')
            ->assertSee('Acceso lateral')
            ->assertSee('Sin conexión')
            ->assertDontSee('CÁMARAS POR SUCURSAL')
            ->assertDontSee('VISTA PREVIA')
            ->assertDontSee('Cuadrícula de monitoreo de ejemplo')
            ->assertDontSee('DEMO-01')
            ->assertSee('data-security-branch-catalog-open', false)
            ->assertSee('security-branch-catalog-dialog', false)
            ->assertSee('Catálogo de sucursales')
            ->assertDontSee('security-branch-form', false);

        $this->get(route('security.index', ['company' => $securityCompany->id]))
            ->assertOk()
            ->assertSee('data-security-dashboard-camera-count', false)
            ->assertSee('data-security-company-camera-count="2"', false);

        $this->get(route('security.index', [
            'section' => 'analytics',
            'company_id' => $securityCompany->id,
            'branch_id' => $securityBranch->id,
        ]))
            ->assertOk()
            ->assertSee('data-security-analytics-camera-count', false)
            ->assertSee('2 cámaras registradas');

        $this->get(route('security.index', [
            'section' => 'branches',
            'company_id' => $securityCompany->id,
            'branch_id' => $emptyBranch->id,
        ]))
            ->assertOk()
            ->assertSee('0 cámaras')
            ->assertSee('Sucursal sin cámaras · 0 cámaras')
            ->assertSee('data-security-camera-preview-empty', false)
            ->assertSee('No hay cámaras de vigilancia registradas para esta sucursal.')
            ->assertDontSee('data-security-camera-preview-grid', false)
            ->assertDontSee('DEMO-01');
    }

    public function test_demo_camera_seeder_assigns_exactly_four_cameras_to_each_branch(): void
    {
        $securityCompany = SecurityCompany::create([
            'name' => 'Empresa para demo de cámaras',
            'entity_type' => 'company',
        ]);
        $branches = collect([
            SecurityBranch::create([
                'security_company_id' => $securityCompany->id,
                'name' => 'Sucursal Centro',
            ]),
            SecurityBranch::create([
                'security_company_id' => $securityCompany->id,
                'name' => 'Sucursal Norte',
            ]),
        ]);
        $branches->first()->cameras()->create([
            'name' => 'Registro anterior',
            'stream_url' => 'rtsp://old.example/camera',
            'sort_order' => 0,
        ]);

        $this->seed(SecurityDemoCameraSeeder::class);

        foreach ($branches as $branch) {
            $this->assertSame(4, $branch->cameras()->count());
            $this->assertSame(
                ['Entrada principal', 'Estacionamiento', 'Área de cajas', 'Almacén'],
                $branch->cameras()->pluck('name')->all(),
            );
        }

        $this->assertDatabaseMissing('security_cameras', ['name' => 'Registro anterior']);
    }

    public function test_branch_modal_stores_location_contact_and_configuration(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $securityCompany = SecurityCompany::create([
            'name' => 'Empresa para nueva sucursal',
            'entity_type' => 'company',
        ]);

        $catalog = $this->actingAs($user)->get(route('security.index', [
            'section' => 'branches',
            'company_id' => $securityCompany->id,
        ]));

        $catalog->assertOk()
            ->assertSee('Agregar sucursal')
            ->assertSee('security-branch-dialog', false)
            ->assertSee('type="hidden" name="security_company_id" value="'.$securityCompany->id.'"', false)
            ->assertDontSee('select name="security_company_id"', false)
            ->assertSee('Configuración avanzada')
            ->assertSee('URLs de cámaras de vigilancia')
            ->assertSee('data-security-camera-url-add', false)
            ->assertSee('data-security-camera-url-remove', false);

        $response = $this->post(route('security.branches.store'), [
            'security_company_id' => $securityCompany->id,
            'name' => 'Sucursal Monterrey',
            'code' => 'suc-001',
            'description' => 'Sucursal principal de vigilancia.',
            'address' => 'Av. Constitución 100, Centro',
            'country' => 'México',
            'state' => 'Nuevo León',
            'city' => 'Monterrey',
            'postal_code' => '64000',
            'phone' => '81 1234 5678',
            'email' => 'monterrey@example.com',
            'timezone' => 'America/Mexico_City',
            'status' => 'active',
            'analytics_enabled' => '1',
            'alerts_enabled' => '1',
            'camera_urls' => [
                ['name' => 'Entrada principal', 'url' => 'https://cameras.example.com/entrada.m3u8'],
                ['name' => '', 'url' => 'rtsp://10.0.0.25/live'],
                ['name' => 'Fila vacía', 'url' => ''],
            ],
        ]);

        $securityBranch = SecurityBranch::firstOrFail();

        $response->assertRedirect(route('security.index', [
            'section' => 'branches',
            'company_id' => $securityCompany->id,
            'branch_id' => $securityBranch->id,
        ]))->assertSessionMissing('status');
        $this->assertDatabaseHas('security_branches', [
            'security_company_id' => $securityCompany->id,
            'name' => 'Sucursal Monterrey',
            'code' => 'SUC-001',
            'country' => 'México',
            'state' => 'Nuevo León',
            'city' => 'Monterrey',
            'timezone' => 'America/Mexico_City',
            'status' => 'active',
            'analytics_enabled' => true,
            'alerts_enabled' => true,
        ]);
        $this->assertSame(3, SecurityCamera::query()->count());
        $this->assertSame(
            [
                ['name' => 'Entrada principal', 'url' => 'https://cameras.example.com/entrada.m3u8'],
                ['name' => 'Cámara 02', 'url' => 'rtsp://10.0.0.25/live'],
                ['name' => 'Fila vacía', 'url' => ''],
            ],
            $securityBranch->cameras()
                ->get()
                ->map(fn (SecurityCamera $camera) => ['name' => $camera->name, 'url' => $camera->stream_url])
                ->all(),
        );

        $this->get(route('security.index', [
            'section' => 'branches',
            'company_id' => $securityCompany->id,
        ]))
            ->assertOk()
            ->assertSee('Editar')
            ->assertSee('data-security-branch-edit=', false)
            ->assertSee(route('security.branches.update', $securityBranch), false);

        $updateResponse = $this->patch(route('security.branches.update', $securityBranch), [
            'security_company_id' => 999999,
            'name' => 'Sucursal Monterrey Norte',
            'code' => 'suc-002',
            'description' => 'Sucursal actualizada.',
            'address' => 'Av. Universidad 200, Centro',
            'country' => 'México',
            'state' => 'Nuevo León',
            'city' => 'San Nicolás',
            'postal_code' => '66400',
            'phone' => '81 8765 4321',
            'email' => 'norte@example.com',
            'timezone' => 'America/Mexico_City',
            'status' => 'inactive',
            'analytics_enabled' => '0',
            'alerts_enabled' => '1',
            'camera_urls' => [
                ['name' => 'Acceso norte', 'url' => 'rtsp'],
            ],
        ]);

        $updateResponse->assertRedirect(route('security.index', [
            'section' => 'branches',
            'company_id' => $securityCompany->id,
            'branch_id' => $securityBranch->id,
        ]))
            ->assertSessionMissing('status')
            ->assertSessionHas('security_branch_updated', true);
        $this->assertDatabaseHas('security_branches', [
            'id' => $securityBranch->id,
            'security_company_id' => $securityCompany->id,
            'name' => 'Sucursal Monterrey Norte',
            'code' => 'SUC-002',
            'city' => 'San Nicolás',
            'status' => 'inactive',
            'analytics_enabled' => false,
            'alerts_enabled' => true,
        ]);
        $this->assertSame(1, SecurityCamera::query()->count());
        $this->assertSame('Acceso norte', SecurityCamera::query()->firstOrFail()->name);
        $this->assertSame('rtsp', SecurityCamera::query()->firstOrFail()->stream_url);

        $this->get(route('security.index', [
            'section' => 'branches',
            'company_id' => $securityCompany->id,
            'branch_id' => $securityBranch->id,
        ]))
            ->assertOk()
            ->assertSee('security-branch-success-dialog', false)
            ->assertSee('Cambios guardados con exito')
            ->assertSee('data-security-branch-success-close', false);
    }
}
