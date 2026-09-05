<?php

namespace Tests\Feature;

use App\Models\ConstructionProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConstructionProjectAreaFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_projects_use_an_automatic_consecutive_key_and_start_without_progress(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $createForm = $this->actingAs($user)->get(route('construction.projects.create'));

        $createForm->assertOk();
        $createForm->assertSee('<input name="project_key" value="OBR-004" required readonly aria-readonly="true">', false);
        $createForm->assertSee('href="'.route('construction.dashboard').'"', false);
        $createForm->assertSee('<option value="Hibrida"', false);
        $createForm->assertDontSee('name="physical_progress"', false);
        $createForm->assertDontSee('name="financial_progress"', false);
        $createForm->assertDontSee('name="estimated_amount"', false);
        $createForm->assertDontSee('name="paid_amount"', false);
        $createForm->assertDontSee('name="retention_amount"', false);

        $response = $this->actingAs($user)->post(route('construction.projects.store'), [
            'project_key' => 'OBR-999',
            'name' => 'Nueva obra consecutiva',
            'modality' => 'Hibrida',
            'status' => 'Por iniciar',
            'physical_progress' => 75,
            'financial_progress' => 60,
            'estimated_amount' => 350000,
            'paid_amount' => 125000,
            'retention_amount' => 18000,
        ]);

        $project = ConstructionProject::where('name', 'Nueva obra consecutiva')->firstOrFail();

        $response->assertRedirect(route('construction.dashboard'));
        $response->assertSessionHas('construction_project_created', 'Obra creada correctamente.');
        $response->assertSessionMissing('status');
        $this->assertSame('OBR-004', $project->project_key);
        $this->assertSame('Hibrida', $project->modality);
        $this->assertSame('0.00', $project->physical_progress);
        $this->assertSame('0.00', $project->financial_progress);
        $this->assertSame('0.00', $project->estimated_amount);
        $this->assertSame('0.00', $project->paid_amount);
        $this->assertSame('0.00', $project->retention_amount);

        $confirmation = $this->actingAs($user)->get(route('construction.dashboard'));

        $confirmation->assertOk();
        $confirmation->assertSee('data-construction-created-dialog', false);
        $confirmation->assertSee('data-construction-created-close', false);
        $confirmation->assertSee('Obra creada correctamente.');
        $confirmation->assertDontSee('<div class="alert">Obra creada correctamente.</div>', false);

        $project->delete();

        $this->actingAs($user)->post(route('construction.projects.store'), [
            'name' => 'Siguiente obra consecutiva',
            'modality' => 'Precio alzado',
            'status' => 'Por iniciar',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('construction_projects', [
            'project_key' => 'OBR-005',
            'name' => 'Siguiente obra consecutiva',
            'physical_progress' => 0,
            'financial_progress' => 0,
        ]);
    }

    public function test_superadmin_can_update_and_view_project_area_and_level_fields(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $project = ConstructionProject::where('project_key', 'OBR-001')->firstOrFail();

        $response = $this->actingAs($user)->put(route('construction.projects.update', $project), [
            'project_key' => $project->project_key,
            'name' => $project->name,
            'company_id' => $project->company_id,
            'client_id' => $project->client_id,
            'responsible_user_id' => $project->responsible_user_id,
            'location' => $project->location,
            'project_type' => $project->project_type,
            'modality' => $project->modality,
            'status' => $project->status,
            'start_date' => $project->start_date?->format('Y-m-d'),
            'estimated_end_date' => $project->estimated_end_date?->format('Y-m-d'),
            'contracted_value' => $project->contracted_value,
            'estimated_amount' => $project->estimated_amount,
            'paid_amount' => $project->paid_amount,
            'retention_amount' => $project->retention_amount,
            'physical_progress' => $project->physical_progress,
            'financial_progress' => $project->financial_progress,
            'constructed_area' => 1248.50,
            'sellable_rentable_area' => 980.25,
            'parking_area' => 268.75,
            'levels_count' => 5,
            'photo_path' => $project->photo_path,
            'notes' => $project->notes,
        ]);

        $response->assertRedirect(route('construction.dashboard'));
        $this->assertDatabaseHas('construction_projects', [
            'id' => $project->id,
            'constructed_area' => 1248.50,
            'sellable_rentable_area' => 980.25,
            'parking_area' => 268.75,
            'levels_count' => 5,
        ]);

        $dashboard = $this->actingAs($user)->get(route('construction.dashboard'));

        $dashboard->assertOk();
        $dashboard->assertDontSee('id="panel-general-obras"', false);
        $dashboard->assertDontSee('data-project-filter-nav', false);
        $dashboard->assertDontSee('data-overview-project-id', false);
        $dashboard->assertDontSee('data-project-delete-open', false);
        $dashboard->assertSee('data-project-information', false);
        $dashboard->assertSee('data-project-information-card="'.$project->id.'"', false);
        $dashboard->assertSee('1,248.50 m2');
        $dashboard->assertSee('980.25 m2');
        $dashboard->assertSee('268.75 m2');

        $editForm = $this->actingAs($user)->get(route('construction.projects.edit', $project));

        $editForm->assertOk();
        $editForm->assertSee('Editar OBR-001');
        $editForm->assertSee('href="'.route('construction.dashboard').'"', false);
        $editForm->assertSee('name="constructed_area" value="1248.50"', false);
        $editForm->assertSee('name="sellable_rentable_area" value="980.25"', false);
        $editForm->assertSee('name="parking_area" value="268.75"', false);
        $editForm->assertSee('name="levels_count" value="5"', false);

        $legacyDetails = $this->actingAs($user)->get(route('construction.projects.show', $project));

        $legacyDetails->assertRedirect(route('construction.dashboard'));
    }

    public function test_project_status_controls_the_available_projects_carousel(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $project = ConstructionProject::where('project_key', 'OBR-003')->firstOrFail();

        $dashboardBefore = $this->actingAs($user)->get(route('construction.dashboard'));

        $dashboardBefore->assertOk();
        $dashboardBefore->assertSee('Obras activas y por iniciar');
        $dashboardBefore->assertSeeInOrder([
            'data-dashboard-all',
            'data-dashboard-project',
            'data-dashboard-create',
        ], false);
        $dashboardBefore->assertSee('data-carousel-project-id="'.$project->id.'"', false);
        $dashboardBefore->assertDontSee(route('construction.projects.status.update', $project), false);

        $activate = $this->actingAs($user)->patch(route('construction.projects.status.update', $project), [
            'status' => 'En ejecucion',
        ]);

        $activate->assertRedirect(route('construction.dashboard'));
        $activate->assertSessionHas('status', "Estatus de {$project->project_key} actualizado a En ejecucion.");
        $this->assertDatabaseHas('construction_projects', [
            'id' => $project->id,
            'status' => 'En ejecucion',
        ]);
        $this->assertDatabaseHas('construction_audit_logs', [
            'construction_project_id' => $project->id,
            'action' => 'Estatus de obra actualizado',
            'description' => "Se cambio el estatus de {$project->project_key} de Por iniciar a En ejecucion.",
        ]);

        $dashboardActive = $this->actingAs($user)->get(route('construction.dashboard'));
        $dashboardActive->assertSee('data-system-message-dialog', false);
        $dashboardActive->assertSee('data-system-message-close', false);
        $dashboardActive->assertSee("Estatus de {$project->project_key} actualizado a En ejecucion.");
        $dashboardActive->assertDontSee('<div class="alert">', false);
        $dashboardActive->assertSee('data-carousel-project-id="'.$project->id.'"', false);

        $this->actingAs($user)->patch(route('construction.projects.status.update', $project), [
            'status' => 'Concluida',
        ])->assertRedirect(route('construction.dashboard'));

        $this->assertDatabaseHas('construction_projects', [
            'id' => $project->id,
            'status' => 'Concluida',
        ]);
        $dashboardCompleted = $this->actingAs($user)->get(route('construction.dashboard'));
        $dashboardCompleted->assertDontSee('data-carousel-project-id="'.$project->id.'"', false);
        $dashboardCompleted->assertSee('data-project-information-card="'.$project->id.'"', false);
    }

    public function test_construction_carousels_include_running_and_not_started_projects_only(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $runningProject = ConstructionProject::where('status', 'En ejecucion')->firstOrFail();
        $notStartedProject = ConstructionProject::where('status', 'Por iniciar')->firstOrFail();
        $completedProject = ConstructionProject::where('status', 'Concluida')->firstOrFail();
        $carouselUrls = [
            route('construction.dashboard'),
            route('construction.placeholder', 'generadores-obra'),
            route('construction.placeholder', 'materiales-insumos'),
            route('construction.placeholder', 'mano-obra'),
            route('construction.placeholder', 'calendario'),
            route('construction.placeholder', 'pagos'),
            route('construction.placeholder', 'ordenes-suministro'),
            route('construction.placeholder', 'almacenes'),
        ];

        foreach ($carouselUrls as $url) {
            $response = $this->actingAs($user)->get($url);

            $response->assertOk();
            $response->assertSee('Obras activas y por iniciar');
            $response->assertSee('data-carousel-project-id="'.$runningProject->id.'"', false);
            $response->assertSee('data-carousel-project-id="'.$notStartedProject->id.'"', false);
            $response->assertDontSee('data-carousel-project-id="'.$completedProject->id.'"', false);
        }
    }

    public function test_dashboard_does_not_render_the_removed_project_overview_section(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $dashboard = $this->actingAs($user)->get(route('construction.dashboard', [
            'project_status' => 'completed',
        ]));

        $dashboard->assertOk();
        $dashboard->assertSee('Obras activas y por iniciar');
        $dashboard->assertSee('Bitacora reciente');
        $dashboard->assertSee('data-dashboard-create', false);
        $dashboard->assertSee('data-project-information', false);
        $dashboard->assertSee('data-project-information-card', false);
        $dashboard->assertDontSee('id="panel-general-obras"', false);
        $dashboard->assertDontSee('data-project-filter-nav', false);
        $dashboard->assertDontSee('data-overview-project-id', false);
        $dashboard->assertDontSee('data-project-delete-dialog', false);
    }

    public function test_dashboard_no_longer_shows_project_management_actions(): void
    {
        $user = User::factory()->create([
            'role' => 'buyer',
            'active' => true,
        ]);
        $project = ConstructionProject::where('project_key', 'OBR-001')->firstOrFail();
        $project->users()->syncWithoutDetaching([
            $user->id => ['can_view' => true, 'can_edit' => false],
        ]);

        $readOnlyDashboard = $this->actingAs($user)->get(route('construction.dashboard'));

        $readOnlyDashboard->assertOk();
        $readOnlyDashboard->assertDontSee(route('construction.projects.edit', $project), false);
        $readOnlyDashboard->assertDontSee(route('construction.projects.status.update', $project), false);
        $readOnlyDashboard->assertDontSee('data-project-delete-open', false);
        $this->actingAs($user)->get(route('construction.projects.edit', $project))->assertForbidden();
        $this->actingAs($user)->patch(route('construction.projects.status.update', $project), [
            'status' => 'Concluida',
        ])->assertForbidden();

        $project->users()->updateExistingPivot($user->id, ['can_edit' => true]);

        $editableDashboard = $this->actingAs($user)->get(route('construction.dashboard'));

        $editableDashboard->assertOk();
        $editableDashboard->assertDontSee(route('construction.projects.edit', $project), false);
        $editableDashboard->assertDontSee(route('construction.projects.status.update', $project), false);
        $editableDashboard->assertDontSee('data-project-delete-open', false);
        $editableDashboard->assertDontSee('data-project-delete-dialog', false);
        $this->actingAs($user)->get(route('construction.projects.edit', $project))->assertOk();
        $this->actingAs($user)->delete(route('construction.projects.destroy', $project))->assertForbidden();
    }

    public function test_superadmin_can_soft_delete_a_project_from_the_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $project = ConstructionProject::where('project_key', 'OBR-003')->firstOrFail();

        $response = $this->actingAs($user)->delete(route('construction.projects.destroy', $project));

        $response->assertRedirect(route('construction.dashboard'));
        $response->assertSessionHas('status', 'Obra eliminada correctamente.');
        $this->assertSoftDeleted('construction_projects', [
            'id' => $project->id,
        ]);
    }
}
