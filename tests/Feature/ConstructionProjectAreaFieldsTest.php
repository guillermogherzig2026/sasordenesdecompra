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
        $createForm->assertSee('href="'.route('construction.dashboard').'#panel-general-obras"', false);
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

        $response->assertRedirect(route('construction.dashboard').'#project-row-'.$project->id);
        $this->assertSame('OBR-004', $project->project_key);
        $this->assertSame('Hibrida', $project->modality);
        $this->assertSame('0.00', $project->physical_progress);
        $this->assertSame('0.00', $project->financial_progress);
        $this->assertSame('0.00', $project->estimated_amount);
        $this->assertSame('0.00', $project->paid_amount);
        $this->assertSame('0.00', $project->retention_amount);

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

        $response->assertRedirect(route('construction.dashboard').'#project-row-'.$project->id);
        $this->assertDatabaseHas('construction_projects', [
            'id' => $project->id,
            'constructed_area' => 1248.50,
            'sellable_rentable_area' => 980.25,
            'parking_area' => 268.75,
            'levels_count' => 5,
        ]);

        $dashboard = $this->actingAs($user)->get(route('construction.dashboard'));

        $dashboard->assertOk();
        $dashboard->assertSee('Metros cuadrados construidos');
        $dashboard->assertSee('Metros cuadrados vendibles o rentables');
        $dashboard->assertSee('Metros cuadrados de estacionamientos');
        $dashboard->assertSee('1,248.50 m2');
        $dashboard->assertSee('980.25 m2');
        $dashboard->assertSee('268.75 m2');
        $dashboard->assertDontSee('Ver obras');
        $dashboard->assertSee('href="#project-row-'.$project->id.'"', false);
        $dashboard->assertSee('Acciones');
        $dashboard->assertSee(route('construction.projects.edit', $project), false);

        $editForm = $this->actingAs($user)->get(route('construction.projects.edit', $project));

        $editForm->assertOk();
        $editForm->assertSee('Editar OBR-001');
        $editForm->assertSee('href="'.route('construction.dashboard').'#panel-general-obras"', false);

        $legacyDetails = $this->actingAs($user)->get(route('construction.projects.show', $project));

        $legacyDetails->assertRedirect(route('construction.dashboard').'#project-row-'.$project->id);
    }

    public function test_dashboard_only_shows_edit_actions_for_projects_with_edit_permission(): void
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
        $readOnlyDashboard->assertSee('Solo lectura');
        $readOnlyDashboard->assertDontSee(route('construction.projects.edit', $project), false);

        $project->users()->updateExistingPivot($user->id, ['can_edit' => true]);

        $editableDashboard = $this->actingAs($user)->get(route('construction.dashboard'));

        $editableDashboard->assertOk();
        $editableDashboard->assertSee(route('construction.projects.edit', $project), false);
    }
}
