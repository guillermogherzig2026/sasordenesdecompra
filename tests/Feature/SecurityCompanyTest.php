<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\SecurityBranch;
use App\Models\SecurityCamera;
use App\Models\SecurityCompany;
use App\Models\User;
use App\Support\CameraConnectionException;
use App\Support\LocalCameraProvisioner;
use App\Support\NavigationPermissionCatalog;
use App\Support\RtspCameraProbe;
use Database\Seeders\SecurityDemoCameraSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class SecurityCompanyTest extends TestCase
{
    use RefreshDatabase;

    private array $recorderConfigFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->recorderConfigFiles as $path) {
            @unlink($path);
            @unlink($path.'.lock');
        }

        parent::tearDown();
    }

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

    public function test_requested_submenus_share_the_favorites_filter_and_all_companies_scope(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $favorite = SecurityCompany::create(['name' => 'Plazas Mayoristas', 'entity_type' => 'company', 'is_favorite' => true]);
        $other = SecurityCompany::create(['name' => 'Pruebas', 'entity_type' => 'business']);
        $favorite->branches()->create(['name' => 'Sucursal favorita']);
        $other->branches()->create(['name' => 'Sucursal no favorita']);

        $this->actingAs($user);

        foreach (['branches', 'analytics', 'alerts', 'reports', 'cameras'] as $section) {
            $this->get(route('security.index', ['section' => $section, 'company_id' => $other->id]))
                ->assertOk()
                ->assertSee('data-security-section-company-selector', false)
                ->assertSee('data-security-favorite-company-id="'.$favorite->id.'"', false)
                ->assertDontSee('data-security-favorite-company-id="'.$other->id.'"', false)
                ->assertSee('data-security-favorite-picker', false)
                ->assertSee('Administrar favoritas')
                ->assertSee('id="security-favorites-dialog"', false)
                ->assertSee('create_company=1', false)
                ->assertSee('company_id=all', false)
                ->assertSee('company_id='.$other->id, false);

            $all = $this->get(route('security.index', ['section' => $section, 'company_id' => 'all']))
                ->assertOk()
                ->assertSee('security-favorite-card is-all is-active', false)
                ->assertSee('Todas las empresas');

            if ($section === 'branches') {
                $all->assertSee('name="company_id" value="all"', false)
                    ->assertSee('name="branch_id" required data-security-branch-select', false)
                    ->assertSee('data-security-branch-open disabled', false)
                    ->assertSee('<th>Empresa</th>', false)
                    ->assertSee('Plazas Mayoristas · Sucursal favorita')
                    ->assertSee('Pruebas · Sucursal no favorita');
            } elseif ($section === 'analytics') {
                $all->assertSee('Selecciona una empresa para consultar una sucursal')
                    ->assertDontSee('Sin sucursales registradas');
            }

            $this->get(route('security.index', ['section' => $section]))
                ->assertOk()
                ->assertSee('security-favorite-card is-all is-active', false);
        }

        $this->get(route('security.index', ['section' => 'users']))
            ->assertOk()
            ->assertSee('data-security-section-company-selector', false)
            ->assertSee('security-company-card is-tone-2 is-active', false);

        $this->from(route('security.index', ['section' => 'alerts', 'company_id' => 'all']))
            ->patch(route('security.companies.favorite', $other), ['favorite' => true])
            ->assertRedirect();
        $this->get(route('security.index', ['section' => 'alerts', 'company_id' => 'all']))
            ->assertSee('data-security-favorite-company-id="'.$other->id.'"', false);
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
            ->assertSee('Catálogo de cámaras')
            ->assertSee('data-security-camera-search', false)
            ->assertSee('data-security-camera-status-filter', false)
            ->assertSee('data-security-camera-view-switch="catalog"', false)
            ->assertSee('data-security-camera-view-switch="grid"', false)
            ->assertSee('data-security-camera-fullscreen-open', false)
            ->assertSee('id="security-camera-fullscreen"', false)
            ->assertSee('data-security-wall-count="2"', false)
            ->assertSee('data-security-wall-featured', false)
            ->assertSee('modules/security/camera-fullscreen.js', false)
            ->assertSee('data-security-camera-entry', false)
            ->assertSee('Configurar conexión')
            ->assertSee('Ver cámara')
            ->assertSee('data-security-camera-view-dialog', false)
            ->assertSee('name="rtsp_password" type="text"', false)
            ->assertSee('autocomplete="new-password" spellcheck="false" autocapitalize="off" data-security-camera-ip-field', false)
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
            ->assertSee('data-security-branch-form', false);

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
            ->assertDontSee('<div class="security-camera-preview-grid" data-security-camera-preview-grid', false)
            ->assertDontSee('DEMO-01');
    }

    public function test_fullscreen_camera_options_are_scoped_to_the_selected_branch(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = SecurityCompany::create(['name' => 'Grupo Centinela', 'entity_type' => 'company']);
        $firstBranch = $company->branches()->create(['name' => 'Sucursal Centro']);
        $otherBranch = $company->branches()->create(['name' => 'Sucursal Norte']);
        $firstCamera = $firstBranch->cameras()->create([
            'name' => 'Entrada Centro', 'stream_url' => 'https://cameras.example.com/centro.m3u8', 'sort_order' => 0,
        ]);
        $otherBranch->cameras()->create([
            'name' => 'Entrada Norte', 'stream_url' => 'https://cameras.example.com/norte.m3u8', 'sort_order' => 0,
        ]);

        $this->actingAs($user)->get(route('security.index', [
            'section' => 'branches', 'company_id' => $company->id, 'branch_id' => $firstBranch->id,
        ]))
            ->assertOk()
            ->assertSee('data-branch-id="'.$firstBranch->id.'"', false)
            ->assertSee('value="'.$firstCamera->id.'" data-security-wall-camera', false)
            ->assertSee('data-name="Entrada Centro"', false)
            ->assertDontSee('data-name="Entrada Norte"', false)
            ->assertSee('data-security-wall-count="2" aria-pressed="false" disabled', false);
    }

    public function test_local_recorder_camera_is_live_only_when_the_recording_path_is_ready(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $company = SecurityCompany::create([
            'name' => 'Empresa con grabador local',
            'entity_type' => 'company',
        ]);
        $branch = SecurityBranch::create([
            'security_company_id' => $company->id,
            'name' => 'Sucursal con grabador',
        ]);
        $branch->cameras()->create([
            'name' => 'Entrada principal',
            'stream_url' => 'http://127.0.0.1:8888/camara1',
            'sort_order' => 0,
        ]);
        $url = route('security.index', [
            'section' => 'branches',
            'company_id' => $company->id,
            'branch_id' => $branch->id,
        ]);

        Http::fake([
            'http://127.0.0.1:9997/v3/paths/list' => Http::sequence()
                ->push(['items' => [['name' => 'camara1', 'ready' => false]]])
                ->push(['items' => [['name' => 'camara1', 'ready' => true]]]),
        ]);

        $this->actingAs($user)->get($url)
            ->assertOk()
            ->assertSee('data-security-active-cameras="0/1"', false)
            ->assertSee('Sin conexión')
            ->assertDontSee('Video en vivo de Entrada principal');

        $this->get($url)
            ->assertOk()
            ->assertSee('data-security-active-cameras="1/1"', false)
            ->assertSee('src="http://127.0.0.1:8888/camara1"', false)
            ->assertSee('Video en vivo de Entrada principal')
            ->assertSee('EN VIVO');
    }

    public function test_external_camera_url_is_not_embedded_as_a_local_recorder_stream(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $company = SecurityCompany::create([
            'name' => 'Empresa externa',
            'entity_type' => 'company',
        ]);
        $branch = SecurityBranch::create([
            'security_company_id' => $company->id,
            'name' => 'Sucursal externa',
        ]);
        $branch->cameras()->create([
            'name' => 'Camara externa',
            'stream_url' => 'http://127.0.0.1:8888@cameras.example.com/camara1',
        ]);

        Http::fake();

        $this->actingAs($user)->get(route('security.index', [
            'section' => 'branches',
            'company_id' => $company->id,
            'branch_id' => $branch->id,
        ]))
            ->assertOk()
            ->assertSee('data-security-active-cameras="0/1"', false)
            ->assertDontSee('Video en vivo de Camara externa');

        Http::assertNothingSent();
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
            ->assertSee('URLs manuales de cámaras')
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

    public function test_ip_camera_registration_connects_a_new_recorder_path_without_storing_credentials_in_the_database(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = SecurityCompany::create(['name' => 'Operadora Centro', 'entity_type' => 'company']);
        $branch = $company->branches()->create([
            'name' => 'Sucursal Centro', 'address' => 'Centro', 'country' => 'México',
            'state' => 'Nuevo León', 'city' => 'Monterrey', 'timezone' => 'America/Mexico_City', 'status' => 'active',
        ]);
        $configPath = $this->useRecorderConfig();
        $probe = $this->mock(RtspCameraProbe::class);
        $probe->shouldReceive('assertAvailable')->once()->with('192.168.100.4', 554, 'Streaming/Channels/101', 'admin', 'test-password');
        Http::fake(function () use ($configPath) {
            $paths = array_keys(Yaml::parse(file_get_contents($configPath))['paths']);
            $newPath = end($paths);

            return Http::response(['items' => [['name' => $newPath, 'ready' => $newPath !== 'camara1']]]);
        });

        $response = $this->actingAs($user)->post(route('security.branches.cameras.store', $branch), [
            'name' => 'Acceso norte', 'ip' => '192.168.100.4', 'rtsp_port' => 554,
            'rtsp_path' => 'Streaming/Channels/101', 'rtsp_username' => 'admin', 'rtsp_password' => 'test-password',
        ]);

        $response->assertRedirect(route('security.index', [
            'section' => 'branches', 'company_id' => $company->id, 'branch_id' => $branch->id,
        ]))->assertSessionHas('camera_registered', true);
        $camera = SecurityCamera::query()->sole();
        $this->assertMatchesRegularExpression('/\Acamara-[a-f0-9]{12}\z/', $camera->recorder_path);
        $this->assertSame('http://127.0.0.1:8888/'.$camera->recorder_path, $camera->stream_url);
        $this->assertStringNotContainsString('test-password', $camera->stream_url);
        $this->assertSame(true, Yaml::parse(file_get_contents($configPath))['pathDefaults']['record']);
        $this->assertSame(
            'rtsp://admin:test-password@192.168.100.4:554/Streaming/Channels/101',
            Yaml::parse(file_get_contents($configPath))['paths'][$camera->recorder_path]['source'],
        );

        $this->get(route('security.index', ['section' => 'branches', 'company_id' => $company->id, 'branch_id' => $branch->id]))
            ->assertOk()
            ->assertSee('Agregar cámara IP')
            ->assertSee('Acceso norte')
            ->assertSee('name="rtsp_password" type="text" value="test-password"', false);
    }

    public function test_camera_registration_failure_preserves_configuration_and_never_flashes_password(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = SecurityCompany::create(['name' => 'Operadora Sur', 'entity_type' => 'company']);
        $branch = $company->branches()->create([
            'name' => 'Sucursal Sur', 'address' => 'Sur', 'country' => 'México',
            'state' => 'Nuevo León', 'city' => 'Monterrey', 'timezone' => 'America/Mexico_City', 'status' => 'active',
        ]);
        $configPath = $this->useRecorderConfig();
        $previous = file_get_contents($configPath);
        $probe = $this->mock(RtspCameraProbe::class);
        $probe->shouldReceive('assertAvailable')->once()->andThrow(new CameraConnectionException('La cámara rechazó el usuario o la contraseña RTSP.'));
        Http::fake(['*' => Http::response(['items' => []])]);

        $response = $this->actingAs($user)->post(route('security.branches.cameras.store', $branch), [
            'camera_form' => '1', 'name' => 'Acceso sur', 'ip' => '192.168.100.5', 'rtsp_port' => 554,
            'rtsp_path' => 'Streaming/Channels/101', 'rtsp_username' => 'admin', 'rtsp_password' => 'test-password',
        ]);

        $response->assertSessionHasErrorsIn('camera', ['connection']);
        $this->assertArrayNotHasKey('rtsp_password', session()->getOldInput());
        $this->assertSame($previous, file_get_contents($configPath));
        $this->assertSame(0, SecurityCamera::query()->count());
    }

    public function test_camera_registration_rolls_back_when_the_recorder_never_receives_video(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = SecurityCompany::create(['name' => 'Operadora Poniente', 'entity_type' => 'company']);
        $branch = $company->branches()->create([
            'name' => 'Sucursal Poniente', 'address' => 'Poniente', 'country' => 'México',
            'state' => 'Nuevo León', 'city' => 'Monterrey', 'timezone' => 'America/Mexico_City', 'status' => 'active',
        ]);
        $configPath = $this->useRecorderConfig();
        $previous = file_get_contents($configPath);
        $probe = $this->mock(RtspCameraProbe::class);
        $probe->shouldReceive('assertAvailable')->once();
        Http::fake(['*' => Http::response(['items' => []])]);

        $this->actingAs($user)->post(route('security.branches.cameras.store', $branch), [
            'camera_form' => '1', 'name' => 'Acceso poniente', 'ip' => '192.168.100.6', 'rtsp_port' => 554,
            'rtsp_path' => 'Streaming/Channels/101', 'rtsp_username' => 'admin', 'rtsp_password' => 'test-password',
        ])->assertSessionHasErrorsIn('camera', ['connection']);

        $this->assertSame($previous, file_get_contents($configPath));
        $this->assertSame(0, SecurityCamera::query()->count());
    }

    public function test_camera_registration_rejects_non_private_ips_without_flashing_password(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = SecurityCompany::create(['name' => 'Operadora Oriente', 'entity_type' => 'company']);
        $branch = $company->branches()->create([
            'name' => 'Sucursal Oriente', 'address' => 'Oriente', 'country' => 'México',
            'state' => 'Nuevo León', 'city' => 'Monterrey', 'timezone' => 'America/Mexico_City', 'status' => 'active',
        ]);

        $this->actingAs($user)->post(route('security.branches.cameras.store', $branch), [
            'camera_form' => '1', 'name' => 'Acceso oriente', 'ip' => '127.0.0.1', 'rtsp_port' => 554,
            'rtsp_path' => 'Streaming/Channels/101', 'rtsp_username' => 'admin', 'rtsp_password' => 'test-password',
        ])->assertSessionHasErrorsIn('camera', ['ip']);

        $this->assertArrayNotHasKey('rtsp_password', session()->getOldInput());
        $this->assertSame(0, SecurityCamera::query()->count());
    }

    public function test_branch_update_preserves_recorded_cameras(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = SecurityCompany::create(['name' => 'Operadora Norte', 'entity_type' => 'company']);
        $branch = $company->branches()->create([
            'name' => 'Sucursal Norte', 'address' => 'Norte', 'country' => 'México',
            'state' => 'Nuevo León', 'city' => 'Monterrey', 'timezone' => 'America/Mexico_City', 'status' => 'active',
        ]);
        $camera = $branch->cameras()->create([
            'name' => 'Entrada principal', 'stream_url' => 'http://127.0.0.1:8888/camara1',
            'recorder_path' => 'camara1', 'sort_order' => 0,
        ]);

        $this->actingAs($user)->patch(route('security.branches.update', $branch), [
            'name' => 'Sucursal Norte actualizada', 'address' => 'Norte', 'country' => 'México',
            'state' => 'Nuevo León', 'city' => 'Monterrey', 'timezone' => 'America/Mexico_City', 'status' => 'active',
            'camera_urls' => [['name' => 'URL manual', 'url' => 'https://example.com/camera.m3u8']],
        ])->assertRedirect();

        $this->assertSame('camara1', $camera->fresh()->recorder_path);
        $this->assertSame(2, $branch->cameras()->count());
    }

    public function test_each_manual_camera_can_be_edited_without_replacing_its_record(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = SecurityCompany::create(['name' => 'Operadora Centro', 'entity_type' => 'company']);
        $branch = $company->branches()->create([
            'name' => 'Sucursal Centro', 'address' => 'Centro', 'country' => 'México',
            'state' => 'Nuevo León', 'city' => 'Monterrey', 'timezone' => 'America/Mexico_City', 'status' => 'active',
        ]);
        $camera = $branch->cameras()->create([
            'name' => 'Entrada principal', 'stream_url' => 'https://cameras.example.com/entrada.m3u8', 'sort_order' => 0,
        ]);

        $this->actingAs($user)->get(route('security.index', [
            'section' => 'branches', 'company_id' => $company->id, 'branch_id' => $branch->id,
        ]))->assertOk()
            ->assertSee('aria-label="Editar cámara Entrada principal"', false)
            ->assertSee('id="security-camera-edit-'.$camera->id.'"', false);

        $this->actingAs($user)->patch(route('security.branches.cameras.update', [$branch, $camera]), [
            'editing_camera_id' => $camera->id,
            'name' => 'Acceso principal',
            'stream_url' => 'https://cameras.example.com/acceso.m3u8',
        ])->assertRedirect()->assertSessionHas('camera_updated', true);

        $this->assertSame('Acceso principal', $camera->fresh()->name);
        $this->assertSame('https://cameras.example.com/acceso.m3u8', $camera->fresh()->stream_url);
        $this->assertSame(1, $branch->cameras()->count());

        $otherBranch = $company->branches()->create([
            'name' => 'Otra sucursal', 'address' => 'Norte', 'country' => 'México',
            'state' => 'Nuevo León', 'city' => 'Monterrey', 'timezone' => 'America/Mexico_City', 'status' => 'active',
        ]);
        $this->actingAs($user)->patch(route('security.branches.cameras.update', [$otherBranch, $camera]), [
            'name' => 'Nombre indebido', 'stream_url' => 'https://example.com/otra.m3u8',
        ])->assertNotFound();
        $this->assertSame('Acceso principal', $camera->fresh()->name);
    }

    public function test_recorded_camera_details_edit_keeps_the_saved_connection(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = SecurityCompany::create(['name' => 'Operadora Centro', 'entity_type' => 'company']);
        $branch = $company->branches()->create([
            'name' => 'Sucursal Centro', 'address' => 'Centro', 'country' => 'México',
            'state' => 'Nuevo León', 'city' => 'Monterrey', 'timezone' => 'America/Mexico_City', 'status' => 'active',
        ]);
        $camera = $branch->cameras()->create([
            'name' => 'Acceso norte', 'stream_url' => 'http://127.0.0.1:8888/camara1',
            'recorder_path' => 'camara1', 'sort_order' => 0,
        ]);
        $configPath = $this->useRecorderConfig();
        file_put_contents($configPath, "paths:\n  camara1:\n    source: rtsp://admin:test-password@192.168.100.4:554/Streaming/Channels/101\n");
        $previous = file_get_contents($configPath);
        Http::fake(['*' => Http::response(['items' => []])]);

        $this->actingAs($user)->get(route('security.index', [
            'section' => 'branches', 'company_id' => $company->id, 'branch_id' => $branch->id,
        ]))->assertOk()
            ->assertSee('value="192.168.100.4"', false)
            ->assertSee('name="rtsp_password" type="text" value="test-password"', false)
            ->assertHeader('Cache-Control', 'no-store, private');

        $this->actingAs($user)->patch(route('security.branches.cameras.update', [$branch, $camera]), [
            'editing_camera_id' => $camera->id,
            'editing_camera_mode' => 'details',
            'name' => 'Acceso norte actualizado',
            'ip' => '192.168.100.4', 'rtsp_port' => 554,
            'rtsp_path' => 'Streaming/Channels/101', 'rtsp_username' => 'admin',
            'rtsp_password' => 'test-password',
        ])->assertSessionHas('camera_updated', true);

        $this->assertSame('Acceso norte actualizado', $camera->fresh()->name);
        $this->assertSame($previous, file_get_contents($configPath));
    }

    public function test_recorded_camera_connection_save_persists_even_when_values_are_unchanged(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = SecurityCompany::create(['name' => 'Operadora Centro', 'entity_type' => 'company']);
        $branch = $company->branches()->create([
            'name' => 'Sucursal Centro', 'address' => 'Centro', 'country' => 'México',
            'state' => 'Nuevo León', 'city' => 'Monterrey', 'timezone' => 'America/Mexico_City', 'status' => 'active',
        ]);
        $camera = $branch->cameras()->create([
            'name' => 'Acceso norte', 'stream_url' => 'http://127.0.0.1:8888/camara1',
            'recorder_path' => 'camara1', 'sort_order' => 0,
        ]);
        $configPath = $this->useRecorderConfig();
        file_put_contents($configPath, "# before save\npaths:\n  camara1:\n    source: rtsp://admin:test-password@192.168.100.4:554/Streaming/Channels/101\n");
        $probe = $this->mock(RtspCameraProbe::class);
        $probe->shouldNotReceive('assertAvailable');
        Http::fake(['*' => Http::response([], 503)]);

        $this->actingAs($user)->patch(route('security.branches.cameras.update', [$branch, $camera]), [
            'editing_camera_id' => $camera->id,
            'editing_camera_mode' => 'connection',
            'name' => 'Acceso norte',
            'ip' => '192.168.100.4', 'rtsp_port' => 554,
            'rtsp_path' => 'Streaming/Channels/101', 'rtsp_username' => 'admin',
            'rtsp_password' => 'test-password',
        ])->assertSessionHas('camera_updated', true)
            ->assertSessionHas('camera_connection_saved', true);

        $this->assertStringNotContainsString('# before save', file_get_contents($configPath));
        $this->assertSame(
            'rtsp://admin:test-password@192.168.100.4:554/Streaming/Channels/101',
            Yaml::parse(file_get_contents($configPath))['paths']['camara1']['source'],
        );
    }

    public function test_manual_camera_can_be_connected_as_an_ip_camera_without_creating_a_second_record(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = SecurityCompany::create(['name' => 'Operadora Centro', 'entity_type' => 'company']);
        $branch = $company->branches()->create([
            'name' => 'Sucursal Centro', 'address' => 'Centro', 'country' => 'México',
            'state' => 'Nuevo León', 'city' => 'Monterrey', 'timezone' => 'America/Mexico_City', 'status' => 'active',
        ]);
        $camera = $branch->cameras()->create([
            'name' => 'Entrada principal', 'stream_url' => 'rtsp', 'sort_order' => 0,
        ]);
        $configPath = $this->useRecorderConfig();
        $probe = $this->mock(RtspCameraProbe::class);
        $probe->shouldReceive('assertAvailable')->once()
            ->with('192.168.100.8', 554, 'Streaming/Channels/101', 'admin', 'test-password');
        Http::fake(function () use ($configPath) {
            $paths = array_keys(Yaml::parse(file_get_contents($configPath))['paths']);
            $newPath = end($paths);

            return Http::response(['items' => [['name' => $newPath, 'ready' => $newPath !== 'camara1']]]);
        });

        $this->actingAs($user)->patch(route('security.branches.cameras.update', [$branch, $camera]), [
            'editing_camera_id' => $camera->id,
            'editing_camera_mode' => 'connection',
            'connection_type' => 'ip',
            'name' => 'Entrada principal',
            'ip' => '192.168.100.8', 'rtsp_port' => 554,
            'rtsp_path' => 'Streaming/Channels/101', 'rtsp_username' => 'admin',
            'rtsp_password' => 'test-password',
        ])->assertSessionHas('camera_registered', true);

        $camera->refresh();
        $this->assertSame(1, $branch->cameras()->count());
        $this->assertMatchesRegularExpression('/\Acamara-[a-f0-9]{12}\z/', $camera->recorder_path);
        $this->assertSame('http://127.0.0.1:8888/'.$camera->recorder_path, $camera->stream_url);
        $this->assertSame(
            'rtsp://admin:test-password@192.168.100.8:554/Streaming/Channels/101',
            Yaml::parse(file_get_contents($configPath))['paths'][$camera->recorder_path]['source'],
        );
        $this->get(route('security.index', [
            'section' => 'branches', 'company_id' => $company->id, 'branch_id' => $branch->id,
        ]))->assertOk()->assertSee('IP 192.168.100.8')
            ->assertSee('name="rtsp_password" type="text" value="test-password"', false);
    }

    public function test_failed_ip_conversion_keeps_the_manual_camera_and_hides_the_password(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = SecurityCompany::create(['name' => 'Operadora Centro', 'entity_type' => 'company']);
        $branch = $company->branches()->create([
            'name' => 'Sucursal Centro', 'address' => 'Centro', 'country' => 'México',
            'state' => 'Nuevo León', 'city' => 'Monterrey', 'timezone' => 'America/Mexico_City', 'status' => 'active',
        ]);
        $camera = $branch->cameras()->create([
            'name' => 'Entrada principal', 'stream_url' => 'rtsp', 'sort_order' => 0,
        ]);
        $configPath = $this->useRecorderConfig();
        $previous = file_get_contents($configPath);
        $probe = $this->mock(RtspCameraProbe::class);
        $probe->shouldReceive('assertAvailable')->once()
            ->andThrow(new CameraConnectionException('La cámara rechazó el usuario o la contraseña RTSP.'));
        Http::fake(['*' => Http::response(['items' => []])]);

        $this->actingAs($user)->patch(route('security.branches.cameras.update', [$branch, $camera]), [
            'editing_camera_id' => $camera->id,
            'editing_camera_mode' => 'connection',
            'connection_type' => 'ip',
            'name' => 'Entrada modificada',
            'ip' => '192.168.100.8', 'rtsp_port' => 554,
            'rtsp_path' => 'Streaming/Channels/101', 'rtsp_username' => 'admin',
            'rtsp_password' => 'bad-password',
        ])->assertSessionHasErrorsIn('camera_edit', ['connection']);

        $this->assertSame('Entrada principal', $camera->fresh()->name);
        $this->assertSame('rtsp', $camera->fresh()->stream_url);
        $this->assertNull($camera->fresh()->recorder_path);
        $this->assertSame($previous, file_get_contents($configPath));
        $this->assertArrayNotHasKey('rtsp_password', session()->getOldInput());
        $this->assertSame('ip', session()->getOldInput()['connection_type']);
    }

    public function test_recorded_camera_connection_can_be_edited_with_the_existing_password(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = SecurityCompany::create(['name' => 'Operadora Centro', 'entity_type' => 'company']);
        $branch = $company->branches()->create([
            'name' => 'Sucursal Centro', 'address' => 'Centro', 'country' => 'México',
            'state' => 'Nuevo León', 'city' => 'Monterrey', 'timezone' => 'America/Mexico_City', 'status' => 'active',
        ]);
        $camera = $branch->cameras()->create([
            'name' => 'Acceso norte', 'stream_url' => 'http://127.0.0.1:8888/camara1',
            'recorder_path' => 'camara1', 'sort_order' => 0,
        ]);
        $configPath = $this->useRecorderConfig();
        file_put_contents($configPath, "paths:\n  camara1:\n    source: rtsp://admin:test-password@192.168.100.4:554/Streaming/Channels/101\n");
        $probe = $this->mock(RtspCameraProbe::class);
        $probe->shouldNotReceive('assertAvailable');
        Http::fake(['*' => Http::response([], 503)]);

        $this->actingAs($user)->patch(route('security.branches.cameras.update', [$branch, $camera]), [
            'editing_camera_id' => $camera->id,
            'name' => 'Acceso sur',
            'ip' => '192.168.100.5', 'rtsp_port' => 554,
            'rtsp_path' => 'Streaming/Channels/102', 'rtsp_username' => 'admin',
        ])->assertSessionHas('camera_updated', true)
            ->assertSessionHas('camera_connection_saved', true);

        $this->assertSame('Acceso sur', $camera->fresh()->name);
        $this->assertSame('camara1', $camera->fresh()->recorder_path);
        $this->assertSame(
            'rtsp://admin:test-password@192.168.100.5:554/Streaming/Channels/102',
            Yaml::parse(file_get_contents($configPath))['paths']['camara1']['source'],
        );
    }

    public function test_recorded_camera_keeps_the_latest_password_visible_when_signal_is_unavailable(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = SecurityCompany::create(['name' => 'Operadora Centro', 'entity_type' => 'company']);
        $branch = $company->branches()->create([
            'name' => 'Sucursal Centro', 'address' => 'Centro', 'country' => 'México',
            'state' => 'Nuevo León', 'city' => 'Monterrey', 'timezone' => 'America/Mexico_City', 'status' => 'active',
        ]);
        $camera = $branch->cameras()->create([
            'name' => 'Acceso norte', 'stream_url' => 'http://127.0.0.1:8888/camara1',
            'recorder_path' => 'camara1', 'sort_order' => 0,
        ]);
        $configPath = $this->useRecorderConfig();
        file_put_contents($configPath, "paths:\n  camara1:\n    source: rtsp://admin:old-password@192.168.100.4:554/Streaming/Channels/101\n");
        $probe = $this->mock(RtspCameraProbe::class);
        $probe->shouldNotReceive('assertAvailable');
        Http::fake(['*' => Http::response([], 503)]);

        $this->actingAs($user)->patch(route('security.branches.cameras.update', [$branch, $camera]), [
            'editing_camera_id' => $camera->id,
            'name' => 'Acceso norte',
            'ip' => '192.168.100.4', 'rtsp_port' => 554,
            'rtsp_path' => 'Streaming/Channels/101', 'rtsp_username' => 'admin',
            'rtsp_password' => 'new&pass#2026',
        ])->assertSessionHas('camera_updated', true)
            ->assertSessionHas('camera_connection_saved', true);

        $this->assertSame(
            'rtsp://admin:new%26pass%232026@192.168.100.4:554/Streaming/Channels/101',
            Yaml::parse(file_get_contents($configPath))['paths']['camara1']['source'],
        );
        $this->assertSame('new&pass#2026', app(LocalCameraProvisioner::class)->sourceDetails($camera)['password']);
        $this->get(route('security.index', [
            'section' => 'branches', 'company_id' => $company->id, 'branch_id' => $branch->id,
        ]))->assertOk()
            ->assertSee('name="rtsp_password" type="text" value="new&amp;pass#2026"', false)
            ->assertSee('Cambios guardados. La señal RTSP aún no se ha verificado.')
            ->assertDontSee('value="old-password"', false);
    }

    public function test_invalid_recorder_config_keeps_the_previous_connection_and_does_not_flash_the_password(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = SecurityCompany::create(['name' => 'Operadora Centro', 'entity_type' => 'company']);
        $branch = $company->branches()->create([
            'name' => 'Sucursal Centro', 'address' => 'Centro', 'country' => 'México',
            'state' => 'Nuevo León', 'city' => 'Monterrey', 'timezone' => 'America/Mexico_City', 'status' => 'active',
        ]);
        $camera = $branch->cameras()->create([
            'name' => 'Acceso norte', 'stream_url' => 'http://127.0.0.1:8888/camara1',
            'recorder_path' => 'camara1', 'sort_order' => 0,
        ]);
        $configPath = $this->useRecorderConfig();
        file_put_contents($configPath, "invalid: true\n");
        $previous = file_get_contents($configPath);
        $probe = $this->mock(RtspCameraProbe::class);
        $probe->shouldNotReceive('assertAvailable');
        Http::fake(['*' => Http::response(['items' => []])]);

        $this->actingAs($user)->patch(route('security.branches.cameras.update', [$branch, $camera]), [
            'editing_camera_id' => $camera->id,
            'name' => 'Acceso sur',
            'ip' => '192.168.100.5', 'rtsp_port' => 554,
            'rtsp_path' => 'Streaming/Channels/102', 'rtsp_username' => 'admin',
            'rtsp_password' => 'new-password',
        ])->assertSessionHasErrorsIn('camera_edit', ['connection']);

        $this->assertSame('Acceso norte', $camera->fresh()->name);
        $this->assertSame($previous, file_get_contents($configPath));
        $this->assertArrayNotHasKey('rtsp_password', session()->getOldInput());
        $this->assertSame($camera->id, (int) session()->getOldInput()['editing_camera_id']);
    }

    private function useRecorderConfig(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'camera-config-');
        $this->recorderConfigFiles[] = $path;
        file_put_contents($path, "pathDefaults:\n  record: true\npaths:\n  camara1:\n    source: publisher\n");
        config()->set('services.camera_recorder.config_path', $path);
        config()->set('services.camera_recorder.ready_attempts', 1);

        return $path;
    }
}
