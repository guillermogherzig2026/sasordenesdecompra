<?php

namespace Tests\Feature;

use App\Models\ConstructionPaymentOrder;
use App\Models\ConstructionPayroll;
use App\Models\ConstructionProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConstructionPayrollTest extends TestCase
{
    use RefreshDatabase;

    public function test_labor_tracking_has_a_toggleable_estimate_catalog_with_payroll_columns(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('construction.placeholder', 'mano-obra'));

        $response->assertOk();
        $response->assertSee('data-labor-catalog-toggle="estimates"', false);
        $response->assertSee('id="labor-estimate-catalog"', false);
        $response->assertSee('data-labor-catalog="estimates" hidden', false);
        $response->assertSee('Cat&aacute;logo de estimaciones', false);
        $response->assertSee('Periodicidad');
        $response->assertSee('Monto presupuestado');
        $response->assertSee('Monto erogado');
        $response->assertSee('data-disbursed-amount="0.00"', false);
        $response->assertSee('Fecha limite de pago');
        $response->assertSee('PAQ-005');
        $response->assertSee('data-estimate-row', false);
        $response->assertSee('No hay estimaciones registradas para esta obra.');
        $response->assertSee('data-labor-delete', false);
        $response->assertSee('data-labor-delete-dialog', false);
        $response->assertSeeInOrder(['NOM-S26', 'Editar', 'Eliminar']);
        $response->assertSeeInOrder(['PAQ-005', 'Editar', 'Eliminar']);
        $response->assertSee('Eliminar registro');
        $response->assertSee('Estas seguro que quieres eliminar?');
        $response->assertSee('>Si<', false);
        $response->assertSee('>No<', false);
        $response->assertSee('class="button ghost small labor-view-button" type="button" disabled', false);
        $response->assertSee('title="Sin archivo adjunto"', false);
        $response->assertSee('name="period_end_indefinite" value="1" checked', false);
        $response->assertSee('name="code" maxlength="9" value="NOM-00028" readonly aria-readonly="true" required', false);
        $response->assertSee("summary.className = 'column-filter-toggle-icon';", false);
        $response->assertDontSee("summary.textContent = 'Filtro';", false);
    }

    public function test_superadmin_can_register_a_periodic_payroll(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $project = ConstructionProject::where('project_key', 'OBR-001')->firstOrFail();

        $response = $this->actingAs($user)->post(route('construction.payrolls.store'), [
            'payroll_form' => 'create',
            'construction_project_id' => $project->id,
            'code' => 'NOM-99999',
            'contractor' => 'Constructora de prueba',
            'description' => 'Nomina semanal de prueba',
            'area' => 'Mano de obra',
            'periodicity' => 'Semanal',
            'period_start' => '2026-08-17',
            'period_end' => '2026-08-23',
            'progress' => 15,
            'amount' => '$12,500.50',
            'status' => 'Borrador',
            'payment_due_date' => '2026-08-28',
        ]);

        $response->assertRedirect(route('construction.placeholder', [
            'section' => 'mano-obra',
            'project' => $project->id,
            'open_payroll' => 1,
        ]));
        $response->assertSessionHas('status', 'Nomina NOM-00028 creada correctamente.');
        $this->assertDatabaseHas('construction_payrolls', [
            'construction_project_id' => $project->id,
            'code' => 'NOM-00028',
            'periodicity' => 'Semanal',
            'payment_due_date' => '2026-08-28 00:00:00',
            'status' => 'Borrador',
        ]);
        $this->assertDatabaseHas('construction_payment_orders', [
            'construction_project_id' => $project->id,
            'code' => 'NOM-00028',
            'type' => 'Nomina',
            'payment_due_date' => '2026-08-28 00:00:00',
            'status' => 'Borrador',
        ]);
        $this->assertDatabaseHas('construction_audit_logs', [
            'construction_project_id' => $project->id,
            'action' => 'Nomina creada',
            'description' => 'Se creo la nomina periodica NOM-00028.',
        ]);

        $payroll = ConstructionPayroll::where('code', 'NOM-00028')->firstOrFail();
        $paymentOrder = $payroll->paymentOrder()->firstOrFail();
        $this->assertSame('12500.50', $payroll->amount);
        $this->assertSame('12500.50', $paymentOrder->amount);
        $this->assertSame('2026-08-23', $paymentOrder->scheduled_for?->format('Y-m-d'));
        $editResponse = $this->actingAs($user)->get(route('construction.payrolls.edit', $payroll));

        $editResponse->assertOk();
        $editResponse->assertSee('Editar NOM-00028');
        $editResponse->assertSee('Guardar cambios');
        $editResponse->assertSee('class="payroll-currency-symbol" aria-hidden="true">$</span>', false);
        $editResponse->assertSee('value="12,500.50"', false);
        $editResponse->assertDontSee('Avance %');
        $editResponse->assertSee('type="hidden" name="progress"', false);
        $editResponse->assertSee('name="period_end_indefinite" value="1" checked', false);
        $editResponse->assertSee('Cada domingo se genera una OP en Finanzas con fecha limite de pago el viernes siguiente.');

        foreach (['Programada', 'Pausada', 'Cancelada', 'Concluida', 'Pagada'] as $status) {
            $editResponse->assertSee('<option value="'.$status.'"', false);
        }

        $nextResponse = $this->actingAs($user)->get(route('construction.placeholder', [
            'section' => 'mano-obra',
            'project' => $project->id,
        ]));

        $nextResponse->assertOk();
        $nextResponse->assertSee('name="code" maxlength="9" value="NOM-00029" readonly aria-readonly="true" required', false);
    }

    public function test_superadmin_can_register_a_payroll_with_an_indefinite_period_end(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $project = ConstructionProject::where('project_key', 'OBR-001')->firstOrFail();

        $response = $this->actingAs($user)->post(route('construction.payrolls.store'), [
            'payroll_form' => 'create',
            'construction_project_id' => $project->id,
            'code' => 'NOM-99999',
            'contractor' => 'Constructora de prueba',
            'description' => 'Nomina sin fecha final',
            'area' => 'Mano de obra',
            'periodicity' => 'Quincenal',
            'period_start' => '2026-08-17',
            'period_end_indefinite' => '1',
            'progress' => 10,
            'amount' => 18000,
            'status' => 'Borrador',
            'payment_due_date' => '2026-08-20',
        ]);

        $response->assertRedirect(route('construction.placeholder', [
            'section' => 'mano-obra',
            'project' => $project->id,
            'open_payroll' => 1,
        ]));
        $this->assertDatabaseHas('construction_payrolls', [
            'code' => 'NOM-00028',
            'period_end' => null,
        ]);
        $this->assertDatabaseHas('construction_payment_orders', [
            'code' => 'NOM-00028',
            'period_end' => null,
        ]);

        $payroll = ConstructionPayroll::where('code', 'NOM-00028')->firstOrFail();
        $paymentOrder = ConstructionPaymentOrder::where('code', 'NOM-00028')->firstOrFail();
        $this->assertSame('17/08/2026 - Indefinido', $paymentOrder->periodLabel());

        $editResponse = $this->actingAs($user)->get(route('construction.payrolls.edit', $payroll));
        $editResponse->assertOk();
        $editResponse->assertSee('Indefinido');
        $editResponse->assertSee('name="period_end_indefinite" value="1" checked', false);
    }

    public function test_superadmin_can_change_a_payroll_status_from_the_catalog(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $payroll = ConstructionPayroll::where('code', 'NOM-S26')->firstOrFail();
        $paymentOrder = $payroll->paymentOrder()->firstOrFail();

        $catalogResponse = $this->actingAs($user)->get(route('construction.placeholder', [
            'section' => 'mano-obra',
            'project' => $payroll->construction_project_id,
            'open_payroll' => 1,
        ]));

        $catalogResponse->assertOk();
        $catalogResponse->assertSee('aria-label="Cambiar estatus de NOM-S26"', false);
        $catalogResponse->assertSee(route('construction.payrolls.status.update', $payroll), false);

        foreach (ConstructionPayroll::CATALOG_STATUSES as $status) {
            $catalogResponse->assertSee('name="status" value="'.$status.'"', false);
        }

        foreach (array_diff(ConstructionPayroll::STATUSES, ConstructionPayroll::CATALOG_STATUSES) as $status) {
            $catalogResponse->assertDontSee('name="status" value="'.$status.'"', false);
        }

        $rejectedResponse = $this->actingAs($user)->patch(
            route('construction.payrolls.status.update', $payroll),
            ['status' => 'Pausada'],
        );

        $rejectedResponse->assertSessionHasErrors('status');
        $this->assertSame('En revision', $payroll->fresh()->status);

        $response = $this->actingAs($user)->patch(
            route('construction.payrolls.status.update', $payroll),
            ['status' => 'Programada'],
        );

        $response->assertRedirect(route('construction.placeholder', [
            'section' => 'mano-obra',
            'project' => $payroll->construction_project_id,
            'open_payroll' => 1,
        ]).'#payroll-row-'.$payroll->id);
        $response->assertSessionHas('status', 'Estatus de NOM-S26 actualizado a Programada.');
        $this->assertDatabaseHas('construction_payrolls', [
            'id' => $payroll->id,
            'status' => 'Programada',
        ]);
        $this->assertDatabaseHas('construction_payment_orders', [
            'id' => $paymentOrder->id,
            'status' => 'Programada',
        ]);
        $this->assertDatabaseHas('construction_audit_logs', [
            'construction_project_id' => $payroll->construction_project_id,
            'action' => 'Estatus de nomina actualizado',
            'description' => 'Se cambio el estatus de NOM-S26 de En revision a Programada.',
        ]);
    }

    public function test_superadmin_can_delete_a_periodic_payroll(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $project = ConstructionProject::where('project_key', 'OBR-001')->firstOrFail();
        $payroll = ConstructionPayroll::create([
            'construction_project_id' => $project->id,
            'code' => 'NOM-DELETE-001',
            'contractor' => 'Constructora de prueba',
            'description' => 'Nomina para eliminar',
            'area' => 'Mano de obra',
            'periodicity' => 'Quincenal',
            'period_start' => '2026-08-01',
            'period_end' => '2026-08-15',
            'payment_due_date' => '2026-08-20',
            'progress' => 10,
            'amount' => 9000,
            'status' => 'Borrador',
        ]);

        ConstructionPaymentOrder::create([
            'construction_project_id' => $project->id,
            'construction_payroll_id' => $payroll->id,
            'type' => 'Nomina',
            'code' => $payroll->code,
            'description' => $payroll->description,
            'contractor' => $payroll->contractor,
            'area' => $payroll->area,
            'periodicity' => $payroll->periodicity,
            'period_start' => $payroll->period_start,
            'period_end' => $payroll->period_end,
            'payment_due_date' => $payroll->payment_due_date,
            'progress' => $payroll->progress,
            'amount' => $payroll->amount,
            'status' => $payroll->status,
        ]);

        $response = $this->actingAs($user)->delete(route('construction.payrolls.destroy', $payroll));

        $response->assertRedirect(route('construction.placeholder', [
            'section' => 'mano-obra',
            'project' => $project->id,
        ]));
        $response->assertSessionHas('status', 'Nomina NOM-DELETE-001 eliminada correctamente.');
        $this->assertDatabaseMissing('construction_payrolls', [
            'id' => $payroll->id,
        ]);
        $this->assertDatabaseHas('construction_audit_logs', [
            'construction_project_id' => $project->id,
            'action' => 'Nomina eliminada',
            'description' => 'Se elimino la nomina periodica NOM-DELETE-001.',
        ]);
    }
}
