<?php

namespace Tests\Feature;

use App\Models\SecurityCompany;
use App\Models\SecurityIncident;
use App\Models\User;
use App\Support\NavigationPermissionCatalog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityFavoriteDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_card_shows_all_companies_incidents_and_date_filter(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $favorite = SecurityCompany::create(['name' => 'Plazas Mayoristas', 'entity_type' => 'company', 'is_favorite' => true]);
        $other = SecurityCompany::create(['name' => 'Empresa no favorita', 'entity_type' => 'company']);
        SecurityIncident::create([
            'security_company_id' => $favorite->id, 'created_by' => $user->id,
            'title' => 'Acceso fuera de horario', 'priority' => 'critical',
            'status' => 'pending', 'occurred_at' => now(),
        ]);
        SecurityIncident::create([
            'security_company_id' => $other->id, 'created_by' => $user->id,
            'title' => 'Incidencia no favorita', 'priority' => 'low',
            'status' => 'pending', 'occurred_at' => now(),
        ]);

        $this->actingAs($user)->get(route('security.index'))
            ->assertOk()
            ->assertSee('Empresas favoritas')
            ->assertSee('data-security-favorite-company-id="'.$favorite->id.'"', false)
            ->assertDontSee('data-security-favorite-company-id="'.$other->id.'"', false)
            ->assertSee('Todas las empresas')
            ->assertSee('Resumen de empresas')
            ->assertSee('Acceso fuera de horario')
            ->assertSee('Incidencia no favorita')
            ->assertSee('Mostrando 2 incidencias de 2 empresas.', false);

        $this->get(route('security.index', ['company' => $other->id]))
            ->assertOk()
            ->assertSee('Incidencia no favorita')
            ->assertDontSee('Acceso fuera de horario');

        $this->get(route('security.index', [
            'from_date' => now()->subDay()->toDateString(),
            'to_date' => now()->subDay()->toDateString(),
        ]))
            ->assertOk()
            ->assertSee('Sin incidencias registradas en el periodo seleccionado.')
            ->assertDontSee('Acceso fuera de horario');
    }

    public function test_favorite_can_be_added_and_removed_without_losing_the_company_catalog(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = SecurityCompany::create(['name' => 'Pruebas', 'entity_type' => 'business']);

        $this->actingAs($user)->patch(route('security.companies.favorite', $company), ['favorite' => true])
            ->assertRedirect();
        $this->assertTrue($company->fresh()->is_favorite);
        $this->get(route('security.index'))->assertSee('data-security-favorite-company-id="'.$company->id.'"', false);

        $this->patch(route('security.companies.favorite', $company), ['favorite' => false])
            ->assertRedirect();
        $this->assertFalse($company->fresh()->is_favorite);
        $this->get(route('security.index'))
            ->assertDontSee('data-security-favorite-company-id="'.$company->id.'"', false)
            ->assertSee('Pruebas')
            ->assertSee('Todas las empresas')
            ->assertSee('Mostrando 0 incidencias de 1 empresa.', false);
    }

    public function test_favorites_dialog_lists_all_companies_and_saves_the_checked_selection(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $first = SecurityCompany::create(['name' => 'Plazas Mayoristas', 'entity_type' => 'company', 'is_favorite' => true]);
        $second = SecurityCompany::create(['name' => 'Pruebas', 'entity_type' => 'business']);

        $this->actingAs($user)->get(route('security.index'))->assertOk()
            ->assertSee('id="security-favorites-dialog"', false)
            ->assertSee('data-security-favorites-manage', false)
            ->assertSee('value="'.$first->id.'" checked', false)
            ->assertSee('value="'.$second->id.'"', false)
            ->assertDontSee('value="'.$second->id.'" checked', false);

        $this->patch(route('security.companies.favorites.update'), [
            '_favorites_form' => '1',
            'favorite_ids' => [$second->id],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertFalse($first->fresh()->is_favorite);
        $this->assertTrue($second->fresh()->is_favorite);
        $this->get(route('security.index'))
            ->assertDontSee('data-security-favorite-company-id="'.$first->id.'"', false)
            ->assertSee('data-security-favorite-company-id="'.$second->id.'"', false);

        $this->patch(route('security.companies.favorites.update'), ['_favorites_form' => '1'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $this->assertFalse($second->fresh()->is_favorite);
    }

    public function test_invalid_favorite_selection_does_not_change_companies_and_reopens_dialog(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = SecurityCompany::create(['name' => 'Plazas Mayoristas', 'entity_type' => 'company', 'is_favorite' => true]);
        $dashboard = route('security.index');

        $this->actingAs($user)->from($dashboard)->patch(route('security.companies.favorites.update'), [
            '_favorites_form' => '1',
            'favorite_ids' => [$company->id, 999999],
        ])->assertRedirect($dashboard)->assertSessionHasErrors(['favorite_ids.1']);

        $this->assertTrue($company->fresh()->is_favorite);
        $this->get($dashboard)->assertOk()->assertSee('data-auto-open', false);
    }

    public function test_incident_registration_validates_company_branch_camera_and_can_be_resolved(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $company = SecurityCompany::create(['name' => 'Plazas Mayoristas', 'entity_type' => 'company', 'is_favorite' => true]);
        $otherCompany = SecurityCompany::create(['name' => 'Otra empresa', 'entity_type' => 'company']);
        $branch = $company->branches()->create(['name' => 'Centro']);
        $otherBranch = $otherCompany->branches()->create(['name' => 'Norte']);
        $camera = $branch->cameras()->create(['name' => 'Entrada', 'stream_url' => 'https://example.com/live', 'sort_order' => 0]);
        $payload = [
            'security_company_id' => $company->id,
            'security_branch_id' => $branch->id,
            'security_camera_id' => $camera->id,
            'title' => 'Cámara sin conexión',
            'priority' => 'critical',
            'occurred_at' => now()->format('Y-m-d H:i:s'),
        ];

        $this->actingAs($user)->post(route('security.incidents.store'), [
            ...$payload, 'security_branch_id' => $otherBranch->id,
        ])->assertSessionHasErrorsIn('incident', ['security_branch_id']);
        $this->assertDatabaseCount('security_incidents', 0);

        $this->post(route('security.incidents.store'), $payload)->assertRedirect(route('security.index', [
            'company' => $company->id,
            'from_date' => now()->toDateString(),
            'to_date' => now()->toDateString(),
        ]));
        $incident = SecurityIncident::query()->sole();
        $this->assertSame($company->id, $incident->security_company_id);
        $this->assertSame($camera->id, $incident->security_camera_id);
        $this->assertSame('pending', $incident->status);
        $this->get(route('security.index'))->assertSee('Cámara sin conexión')->assertSee('Marcar resuelta');

        $this->patch(route('security.incidents.status.update', $incident), ['status' => 'resolved'])
            ->assertRedirect();
        $this->assertSame('resolved', $incident->fresh()->status);
        $this->assertNotNull($incident->fresh()->resolved_at);
        $this->get(route('security.index'))->assertSee('Reabrir incidencia');
    }

    public function test_favorite_and_incident_actions_require_security_permission(): void
    {
        $user = User::factory()->create([
            'role' => 'administrative_assistant', 'active' => true,
            'menu_permissions' => [NavigationPermissionCatalog::HOME_DASHBOARD],
        ]);
        $company = SecurityCompany::create(['name' => 'Restringida', 'entity_type' => 'company']);

        $this->actingAs($user)->patch(route('security.companies.favorite', $company), ['favorite' => true])
            ->assertForbidden();
        $this->patch(route('security.companies.favorites.update'), ['favorite_ids' => [$company->id]])
            ->assertForbidden();
        $this->post(route('security.incidents.store'), [
            'security_company_id' => $company->id, 'title' => 'Prueba',
            'priority' => 'low', 'occurred_at' => now()->format('Y-m-d H:i:s'),
        ])->assertForbidden();
    }
}
