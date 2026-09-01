<?php

namespace Tests\Feature;

use App\Models\SecurityBranch;
use App\Models\SecurityCompany;
use App\Models\User;
use App\Support\NavigationPermissionCatalog;
use App\Support\SecurityAnalyticsCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_analytics_are_filtered_by_security_company_and_branch(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $company = SecurityCompany::create([
            'name' => 'Centro Comercial Norte',
            'entity_type' => 'company',
        ]);
        $branch = SecurityBranch::create([
            'security_company_id' => $company->id,
            'name' => 'Sucursal Monterrey',
            'code' => 'MTY-01',
        ]);

        $response = $this->actingAs($user)->get(route('security.index', [
            'section' => 'analytics',
            'show_analytics' => 1,
            'company_id' => $company->id,
            'branch_id' => $branch->id,
        ]));

        $response->assertOk()
            ->assertSee('VER Analíticas')
            ->assertSee('Analíticas - Resumen general')
            ->assertSee('Entradas y salidas')
            ->assertSee('Ocupación a lo largo del día')
            ->assertSee('Distribución por tipo de cámara')
            ->assertSee('Top 5 sucursales por entradas')
            ->assertSee('Sin datos para la fecha seleccionada')
            ->assertSee('data-security-analytics-results', false)
            ->assertSee('Centro Comercial Norte')
            ->assertSee('Sucursal Monterrey')
            ->assertSee('20 parámetros');

        foreach (SecurityAnalyticsCatalog::parameters() as $parameter) {
            $response->assertSee($parameter['name']);
            $response->assertSee($parameter['description']);
        }
    }

    public function test_a_branch_can_be_registered_for_a_security_company(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $company = SecurityCompany::create([
            'name' => 'Negocio Vigilado',
            'entity_type' => 'business',
        ]);

        $response = $this->actingAs($user)->post(route('security.branches.store'), [
            'security_company_id' => $company->id,
            'name' => 'Sucursal Centro',
            'code' => 'cen-01',
            'address' => 'Centro, Monterrey',
        ]);

        $response->assertRedirect(route('security.index', [
            'section' => 'branches',
            'company_id' => $company->id,
        ]));
        $this->assertDatabaseHas('security_branches', [
            'security_company_id' => $company->id,
            'name' => 'Sucursal Centro',
            'code' => 'CEN-01',
        ]);
    }

    public function test_branch_registration_requires_the_branches_navigation_permission(): void
    {
        $user = User::factory()->create([
            'role' => 'administrative_assistant',
            'active' => true,
            'menu_permissions' => [NavigationPermissionCatalog::HOME_DASHBOARD, 'security.analytics'],
        ]);
        $company = SecurityCompany::create([
            'name' => 'Empresa Restringida',
            'entity_type' => 'company',
        ]);

        $this->actingAs($user)
            ->post(route('security.branches.store'), [
                'security_company_id' => $company->id,
                'name' => 'Sucursal no autorizada',
            ])
            ->assertForbidden();
    }
}
