<?php

namespace Tests\Feature;

use App\Models\ConstructionProject;
use App\Models\ConstructionScheduleItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConstructionCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_calendar_renders_the_active_project_month_and_reference_controls(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $project = ConstructionProject::where('project_key', 'OBR-001')->firstOrFail();

        $response = $this->actingAs($user)->get(route('construction.placeholder', [
            'section' => 'calendario',
            'project' => $project->id,
            'month' => '2026-08',
        ]));

        $response->assertOk();
        $response->assertSee('Calendario de alcances');
        $response->assertSee('Agosto 2026');
        $response->assertSee('Todos los contratistas');
        $response->assertSee('Todos los alcances');
        $response->assertSee('Agregar alcance');
        $response->assertSeeInOrder([
            '<div class="schedule-month-heading">',
            '<h3>Agosto 2026</h3>',
            '<div class="schedule-month-nav"',
            '<div class="schedule-toolbar-actions">',
        ], false);
        $this->assertSame(1, substr_count($response->getContent(), 'class="schedule-month-nav"'));
        $response->assertSeeInOrder([
            '<span>Contratista</span>',
            '<span>Alcance</span>',
        ], false);
        $response->assertDontSee('<span>Avance %</span>', false);
        $this->assertSame(2, substr_count($response->getContent(), '<input type="hidden" name="progress"'));
        $response->assertDontSee('<span>Estado</span>', false);
        $this->assertSame(2, substr_count($response->getContent(), '<input type="hidden" name="status"'));
        $response->assertSee('data-schedule-create-dialog', false);
        $response->assertSee('data-calendar-contractor-filter', false);
        $response->assertSee('data-calendar-scope-filter', false);
        $response->assertSee('Cimentacion y armado');
        $response->assertSeeInOrder([
            '<span class="schedule-event-contractor">Constructora Alfa</span>',
            '<span class="schedule-event-title-separator" aria-hidden="true">-</span>',
            '<span class="schedule-event-scope">Cimentacion y armado</span>',
            '<span class="schedule-event-sequence">#001</span>',
        ], false);
        $response->assertSee('Instalacion electrica');
        $response->assertSee('Pruebas hidraulicas');
        $response->assertSee('Constructora Alfa');
        $response->assertSee('Programado');
        $response->assertSee('En proceso');
        $response->assertSee('Concluido');
        $response->assertSee(route('construction.schedule-items.store'), false);
        $this->assertDatabaseCount('construction_schedule_items', 6);
    }

    public function test_superadmin_can_create_update_and_delete_a_calendar_scope(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $project = ConstructionProject::where('project_key', 'OBR-001')->firstOrFail();

        $create = $this->actingAs($user)->post(route('construction.schedule-items.store'), [
            'construction_project_id' => $project->id,
            'title' => 'Revision de instalaciones',
            'contractor' => 'Instalaciones del Valle',
            'description' => 'Revision de canalizaciones del nivel uno.',
            'start_date' => '2026-08-05',
            'end_date' => '2026-08-08',
            'progress' => 15,
            'status' => 'En proceso',
        ]);

        $item = ConstructionScheduleItem::where('title', 'Revision de instalaciones')->firstOrFail();
        $create->assertRedirect(route('construction.placeholder', [
            'section' => 'calendario',
            'project' => $project->id,
            'month' => '2026-08',
        ]));
        $create->assertSessionHas('status', 'Alcance Revision de instalaciones agregado correctamente.');
        $this->assertDatabaseHas('construction_schedule_items', [
            'id' => $item->id,
            'construction_project_id' => $project->id,
            'created_by_user_id' => $user->id,
            'contractor_key' => 'instalaciones del valle',
            'contractor_sequence' => 1,
            'progress' => 15,
            'status' => 'En proceso',
        ]);
        $this->assertDatabaseHas('construction_audit_logs', [
            'construction_project_id' => $project->id,
            'action' => 'Alcance de calendario creado',
        ]);

        $update = $this->actingAs($user)->put(route('construction.schedule-items.update', $item), [
            'title' => 'Revision final de instalaciones',
            'contractor' => 'Instalaciones del Valle',
            'description' => 'Revision terminada.',
            'start_date' => '2026-08-06',
            'end_date' => '2026-08-09',
            'progress' => 100,
            'status' => 'Concluido',
        ]);

        $update->assertRedirect(route('construction.placeholder', [
            'section' => 'calendario',
            'project' => $project->id,
            'month' => '2026-08',
        ]));
        $this->assertDatabaseHas('construction_schedule_items', [
            'id' => $item->id,
            'title' => 'Revision final de instalaciones',
            'contractor_sequence' => 1,
            'progress' => 100,
            'status' => 'Concluido',
        ]);
        $this->assertDatabaseHas('construction_audit_logs', [
            'construction_project_id' => $project->id,
            'action' => 'Alcance de calendario actualizado',
        ]);

        $delete = $this->actingAs($user)->delete(route('construction.schedule-items.destroy', $item));

        $delete->assertRedirect(route('construction.placeholder', [
            'section' => 'calendario',
            'project' => $project->id,
            'month' => '2026-08',
        ]));
        $this->assertDatabaseMissing('construction_schedule_items', ['id' => $item->id]);
        $this->assertDatabaseHas('construction_audit_logs', [
            'construction_project_id' => $project->id,
            'action' => 'Alcance de calendario eliminado',
        ]);
    }

    public function test_scope_sequences_are_persisted_per_project_and_contractor(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $project = ConstructionProject::where('project_key', 'OBR-001')->firstOrFail();
        $otherProject = ConstructionProject::where('project_key', 'OBR-002')->firstOrFail();
        $contractor = 'Contratista Secuencial';
        $createScope = function (ConstructionProject $targetProject, string $title, string $startDate) use ($user, $contractor) {
            return $this->actingAs($user)->post(route('construction.schedule-items.store'), [
                'construction_project_id' => $targetProject->id,
                'title' => $title,
                'contractor' => $contractor,
                'description' => null,
                'start_date' => $startDate,
                'end_date' => $startDate,
                'progress' => 0,
                'status' => 'Programado',
            ]);
        };

        $createScope($project, 'Alcance secuencial uno', '2026-08-04')->assertSessionHasNoErrors();
        $createScope($project, 'Alcance secuencial dos', '2026-08-05')->assertSessionHasNoErrors();

        $first = ConstructionScheduleItem::where('title', 'Alcance secuencial uno')->firstOrFail();
        $second = ConstructionScheduleItem::where('title', 'Alcance secuencial dos')->firstOrFail();
        $this->assertSame(1, $first->contractor_sequence);
        $this->assertSame(2, $second->contractor_sequence);

        $this->actingAs($user)
            ->delete(route('construction.schedule-items.destroy', $second))
            ->assertSessionHasNoErrors();
        $createScope($project, 'Alcance secuencial tres', '2026-08-06')->assertSessionHasNoErrors();

        $third = ConstructionScheduleItem::where('title', 'Alcance secuencial tres')->firstOrFail();
        $this->assertSame(3, $third->contractor_sequence);
        $this->assertDatabaseHas('construction_schedule_contractor_counters', [
            'construction_project_id' => $project->id,
            'contractor_key' => 'contratista secuencial',
            'last_sequence' => 3,
        ]);

        $createScope($otherProject, 'Alcance de otra obra', '2026-08-07')->assertSessionHasNoErrors();
        $otherProjectItem = ConstructionScheduleItem::where('title', 'Alcance de otra obra')->firstOrFail();
        $this->assertSame(1, $otherProjectItem->contractor_sequence);

        $calendar = $this->actingAs($user)->get(route('construction.placeholder', [
            'section' => 'calendario',
            'project' => $project->id,
            'month' => '2026-08',
        ]));
        $calendar->assertSeeInOrder([
            '<span class="schedule-event-contractor">Contratista Secuencial</span>',
            '<span class="schedule-event-title-separator" aria-hidden="true">-</span>',
            'Alcance secuencial tres',
            '<span class="schedule-event-sequence">#003</span>',
        ], false);
    }

    public function test_calendar_data_and_last_context_survive_logout(): void
    {
        $password = 'calendar-password';
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
            'password' => $password,
        ]);
        $project = ConstructionProject::where('project_key', 'OBR-002')->firstOrFail();

        $this->actingAs($user)->post(route('construction.schedule-items.store'), [
            'construction_project_id' => $project->id,
            'title' => 'Alcance persistente',
            'contractor' => 'Contratista Persistente',
            'description' => 'Informacion capturada antes de cerrar sesion.',
            'start_date' => '2026-09-08',
            'end_date' => '2026-09-12',
            'progress' => 0,
            'status' => 'Programado',
        ])->assertSessionHasNoErrors();

        $projectCookie = "construction_calendar_project_{$user->id}";
        $monthCookie = "construction_calendar_month_{$user->id}";
        $calendar = $this->get(route('construction.placeholder', [
            'section' => 'calendario',
            'project' => $project->id,
            'month' => '2026-09',
        ]));

        $calendar->assertCookie($projectCookie, (string) $project->id);
        $calendar->assertCookie($monthCookie, '2026-09');

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertDatabaseHas('construction_schedule_items', [
            'construction_project_id' => $project->id,
            'title' => 'Alcance persistente',
            'contractor' => 'Contratista Persistente',
            'description' => 'Informacion capturada antes de cerrar sesion.',
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => $password,
        ])->assertRedirect(route('dashboard'));

        $restoredCalendar = $this
            ->withCookie($projectCookie, (string) $project->id)
            ->withCookie($monthCookie, '2026-09')
            ->get(route('construction.placeholder', ['section' => 'calendario']));

        $restoredCalendar->assertOk();
        $restoredCalendar->assertSee('Septiembre 2026');
        $restoredCalendar->assertSee($project->name);
        $restoredCalendar->assertSeeInOrder([
            '<span class="schedule-event-contractor">Contratista Persistente</span>',
            '<span class="schedule-event-title-separator" aria-hidden="true">-</span>',
            '<span class="schedule-event-scope">Alcance persistente</span>',
            '<span class="schedule-event-sequence">#001</span>',
        ], false);
        $restoredCalendar->assertSee('Informacion capturada antes de cerrar sesion.');
    }

    public function test_project_permissions_control_calendar_editing(): void
    {
        $user = User::factory()->create([
            'role' => 'buyer',
            'active' => true,
        ]);
        $project = ConstructionProject::where('project_key', 'OBR-001')->firstOrFail();
        $project->users()->syncWithoutDetaching([
            $user->id => ['can_view' => true, 'can_edit' => false],
        ]);
        $item = $project->scheduleItems()->firstOrFail();

        $page = $this->actingAs($user)->get(route('construction.placeholder', [
            'section' => 'calendario',
            'project' => $project->id,
            'month' => '2026-08',
        ]));

        $page->assertOk();
        $page->assertSee('Cimentacion y armado');
        $page->assertDontSee('<dialog class="schedule-dialog" data-schedule-create-dialog', false);
        $page->assertSee('data-schedule-edit="'.$item->id.'"', false);
        $page->assertSee('disabled', false);

        $this->actingAs($user)->put(route('construction.schedule-items.update', $item), [
            'title' => $item->title,
            'contractor' => $item->contractor,
            'description' => $item->description,
            'start_date' => $item->start_date->format('Y-m-d'),
            'end_date' => $item->end_date->format('Y-m-d'),
            'progress' => 50,
            'status' => 'En proceso',
        ])->assertForbidden();
    }
}
