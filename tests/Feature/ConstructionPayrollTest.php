<?php

namespace Tests\Feature;

use App\Models\ConstructionPayroll;
use App\Models\ConstructionPaymentOrder;
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
            'code' => 'NOM-TEST-001',
            'contractor' => 'Constructora de prueba',
            'description' => 'Nomina semanal de prueba',
            'area' => 'Mano de obra',
            'periodicity' => 'Semanal',
            'period_start' => '2026-08-17',
            'period_end' => '2026-08-23',
            'progress' => 15,
            'amount' => 12500.50,
            'status' => 'Borrador',
            'payment_due_date' => '2026-08-28',
        ]);

        $response->assertRedirect(route('construction.placeholder', [
            'section' => 'mano-obra',
            'project' => $project->id,
            'open_payroll' => 1,
        ]));
        $response->assertSessionHas('status', 'Nomina NOM-TEST-001 creada correctamente.');
        $this->assertDatabaseHas('construction_payrolls', [
            'construction_project_id' => $project->id,
            'code' => 'NOM-TEST-001',
            'periodicity' => 'Semanal',
            'payment_due_date' => '2026-08-28 00:00:00',
            'status' => 'Borrador',
        ]);
        $this->assertDatabaseHas('construction_payment_orders', [
            'construction_project_id' => $project->id,
            'code' => 'NOM-TEST-001',
            'type' => 'Nomina',
            'payment_due_date' => '2026-08-28 00:00:00',
            'status' => 'Borrador',
        ]);
        $this->assertDatabaseHas('construction_audit_logs', [
            'construction_project_id' => $project->id,
            'action' => 'Nomina creada',
            'description' => 'Se creo la nomina periodica NOM-TEST-001.',
        ]);

        $payroll = ConstructionPayroll::where('code', 'NOM-TEST-001')->firstOrFail();
        $editResponse = $this->actingAs($user)->get(route('construction.payrolls.edit', $payroll));

        $editResponse->assertOk();
        $editResponse->assertSee('Editar NOM-TEST-001');
        $editResponse->assertSee('Guardar cambios');
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
