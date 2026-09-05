<?php

namespace Tests\Feature;

use App\Models\ConstructionPaymentOrder;
use App\Models\ConstructionProject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConstructionPaymentOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_payments_are_shared_with_finance_using_the_requested_columns(): void
    {
        $user = $this->superadmin();

        $finance = $this->actingAs($user)->get(route('finance.construction-payment-orders.active'));

        $finance->assertOk();
        $finance->assertSeeInOrder([
            'Tipo',
            'Codigo',
            'Descripcion',
            'Contratista',
            'Periodo',
            'Fecha limite de pago',
            'Monto',
            'Estado',
            'Factura',
            'Pago',
            'Fecha de Pago',
            'Descartar',
        ]);
        $finance->assertSee('NOM-S26');
        $finance->assertSee('PAQ-005');

        $labor = $this->actingAs($user)->get(route('construction.placeholder', 'mano-obra'));

        $labor->assertOk();
        $labor->assertSee('data-labor-all-projects', false);
        $labor->assertSee('data-project-id="all"', false);
        $labor->assertSee('Mostrar pagos de todas las obras');
        $labor->assertSee('<th>Obra</th>', false);
        $labor->assertSee('class="labor-project-cell"', false);
        $labor->assertSee('Fecha limite de pago');
        $labor->assertSee('03/07/2026');
    }

    public function test_superadmin_can_create_a_new_estimate_package_from_the_catalog(): void
    {
        $user = $this->superadmin();
        $project = ConstructionProject::where('project_key', 'OBR-001')->firstOrFail();

        $this->actingAs($user)
            ->get(route('construction.placeholder', 'mano-obra'))
            ->assertOk()
            ->assertSee('Nuevo paquete de estimaciones');

        $response = $this->actingAs($user)->post(route('construction.estimates.store'), [
            'estimate_form' => 'create',
            'construction_project_id' => $project->id,
            'code' => 'PAQ-TEST-009',
            'contractor' => 'Contratista de prueba',
            'description' => 'Paquete de estimaciones de prueba',
            'area' => 'Obra civil',
            'periodicity' => 'Quincenal',
            'period_reference' => '01/09 - 15/09/2026',
            'payment_due_date' => '2026-09-20',
            'progress' => 12.5,
            'amount' => 875000,
            'status' => 'Programado',
        ]);

        $response->assertRedirect(route('construction.placeholder', [
            'section' => 'mano-obra',
            'project' => $project->id,
            'open_estimates' => 1,
        ]));
        $response->assertSessionHas('status', 'Paquete de estimaciones PAQ-TEST-009 creado correctamente.');
        $this->assertDatabaseHas('construction_payment_orders', [
            'construction_project_id' => $project->id,
            'type' => 'Estimacion',
            'code' => 'PAQ-TEST-009',
            'description' => 'Paquete de estimaciones de prueba',
            'status' => 'Programado',
        ]);
        $this->assertDatabaseHas('construction_audit_logs', [
            'construction_project_id' => $project->id,
            'action' => 'Estimacion creada',
            'description' => 'Se creo el paquete de estimaciones PAQ-TEST-009.',
        ]);
        $this->actingAs($user)
            ->get(route('finance.construction-payment-orders.active'))
            ->assertSee('PAQ-TEST-009');
    }

    public function test_finance_payment_moves_a_weekly_occurrence_without_closing_the_schedule(): void
    {
        Storage::fake('local');
        $user = $this->superadmin();
        $order = ConstructionPaymentOrder::where('code', 'NOM-S26')->firstOrFail();

        $response = $this->actingAs($user)->post(
            route('finance.construction-payment-orders.payment.store', $order),
            [
                'payment_file' => UploadedFile::fake()->create('pago-nomina.pdf', 80, 'application/pdf'),
                'paid_on' => '2026-08-17',
            ]
        );

        $response->assertRedirect(route('finance.construction-payment-orders.history'));
        $order->refresh();
        $this->assertSame('Pagado', $order->status);
        $this->assertSame('2026-08-17', $order->paid_on?->format('Y-m-d'));
        $this->assertNotNull($order->payment_file_path);
        Storage::disk('local')->assertExists($order->payment_file_path);
        $this->assertDatabaseHas('construction_payrolls', [
            'id' => $order->construction_payroll_id,
            'status' => 'En revision',
            'payment_date' => null,
        ]);

        $this->actingAs($user)
            ->get(route('finance.construction-payment-orders.active'))
            ->assertDontSee('data-payment-code="NOM-S26"', false);
        $this->actingAs($user)
            ->get(route('construction.placeholder', 'mano-obra'))
            ->assertDontSee('data-labor-code="NOM-S26"', false)
            ->assertSee('data-disbursed-amount="236900.00"', false);
        $this->actingAs($user)
            ->get(route('finance.construction-payment-orders.history'))
            ->assertSee('NOM-S26')
            ->assertSee('17/08/2026');
        $this->actingAs($user)
            ->get(route('construction.placeholder', ['section' => 'pagos', 'project' => $order->construction_project_id]))
            ->assertSee('NOM-S26')
            ->assertSee('17/08/2026');
    }

    public function test_finance_can_discard_a_payment_occurrence_into_both_histories(): void
    {
        $user = $this->superadmin();
        $order = ConstructionPaymentOrder::where('code', 'NOM-S26')->firstOrFail();
        $payrollId = $order->construction_payroll_id;

        $response = $this->actingAs($user)->patch(
            route('finance.construction-payment-orders.discard', $order)
        );

        $response->assertRedirect(route('finance.construction-payment-orders.history'));
        $response->assertSessionHas('status', 'NOM-S26 descartada y enviada al historial.');

        $order->refresh();
        $this->assertSame('Descartada', $order->status);
        $this->assertSame('canceled', $order->statusClass());
        $this->assertNotNull($order->discarded_at);
        $this->assertSame($user->id, $order->discarded_by);
        $this->assertNull($order->payment_file_path);
        $this->assertDatabaseHas('construction_payrolls', [
            'id' => $payrollId,
            'code' => 'NOM-S26',
        ]);
        $this->assertDatabaseHas('construction_audit_logs', [
            'construction_project_id' => $order->construction_project_id,
            'action' => 'Orden de pago descartada',
        ]);

        $this->actingAs($user)
            ->get(route('finance.construction-payment-orders.active'))
            ->assertDontSee('data-payment-code="NOM-S26"', false);
        $this->actingAs($user)
            ->get(route('finance.construction-payment-orders.history'))
            ->assertSee('data-payment-code="NOM-S26"', false)
            ->assertSee('Descartada');
        $this->actingAs($user)
            ->get(route('construction.placeholder', [
                'section' => 'mano-obra',
                'project' => $order->construction_project_id,
            ]))
            ->assertDontSee('data-labor-code="NOM-S26"', false);
        $this->actingAs($user)
            ->get(route('construction.placeholder', [
                'section' => 'pagos',
                'project' => $order->construction_project_id,
            ]))
            ->assertSee('data-payment-code="NOM-S26"', false)
            ->assertSee('Descartada');
    }

    public function test_invoice_documents_uploaded_from_labor_are_available_from_the_modal(): void
    {
        Storage::fake('local');
        $user = $this->superadmin();
        $order = ConstructionPaymentOrder::where('code', 'NOM-S26')->firstOrFail();

        $laborUrl = route('construction.placeholder', [
            'section' => 'mano-obra',
            'project' => $order->construction_project_id,
        ]);

        $this->assertSame(0, $order->invoiceDocumentCount());
        $this->assertSame('empty', $order->invoiceDocumentStatus());
        $this->actingAs($user)
            ->get($laborUrl)
            ->assertSee('class="button ghost small invoice-upload-button invoice-upload-empty"', false)
            ->assertSee('data-invoice-document-count="0"', false);

        $this->actingAs($user)
            ->from($laborUrl)
            ->post(
                route('construction.payment-orders.invoice.store', $order),
                ['invoice_file' => UploadedFile::fake()->create('factura-nomina.pdf', 80, 'application/pdf')]
            )
            ->assertRedirect($laborUrl);

        $order->refresh();
        $this->assertSame(1, $order->invoiceDocumentCount());
        $this->assertSame('partial', $order->invoiceDocumentStatus());
        $this->actingAs($user)
            ->get($laborUrl)
            ->assertSee('class="button ghost small invoice-upload-button invoice-upload-partial"', false)
            ->assertSee('data-invoice-document-count="1"', false);

        $this->actingAs($user)
            ->from($laborUrl)
            ->post(
                route('construction.payment-orders.invoice.store', $order),
                ['invoice_xml_file' => UploadedFile::fake()->create('factura-nomina.xml', 20, 'application/xml')]
            )
            ->assertRedirect($laborUrl);

        $order->refresh();
        $this->assertSame(2, $order->invoiceDocumentCount());
        $this->assertSame('partial', $order->invoiceDocumentStatus());

        $this->actingAs($user)
            ->from($laborUrl)
            ->post(
                route('construction.payment-orders.invoice.store', $order),
                ['fiscal_verification_file' => UploadedFile::fake()->create('verificacion-fiscal.pdf', 60, 'application/pdf')]
            )
            ->assertRedirect($laborUrl);

        $order->refresh();
        $this->assertSame(3, $order->invoiceDocumentCount());
        $this->assertSame('complete', $order->invoiceDocumentStatus());
        $this->assertNotNull($order->invoice_file_path);
        $this->assertNotNull($order->invoice_xml_file_path);
        $this->assertNotNull($order->fiscal_verification_file_path);
        Storage::disk('local')->assertExists($order->invoice_file_path);
        Storage::disk('local')->assertExists($order->invoice_xml_file_path);
        Storage::disk('local')->assertExists($order->fiscal_verification_file_path);

        $labor = $this->actingAs($user)->get($laborUrl);
        $labor->assertOk();
        $labor->assertSee('class="button ghost small invoice-upload-button invoice-upload-complete"', false);
        $labor->assertSee('data-invoice-document-count="3"', false);
        $labor->assertSee('data-supply-detail-open="invoice-documents-dialog-'.$order->id.'"', false);
        $labor->assertSee('Subir factura PDF');
        $labor->assertSee('Subir XML');
        $labor->assertSee('Subir verificaci&oacute;n fiscal PDF', false);
        $labor->assertSee('name="invoice_file"', false);
        $labor->assertSee('name="invoice_xml_file"', false);
        $labor->assertSee('name="fiscal_verification_file"', false);

        $this->actingAs($user)
            ->get(route('construction.payment-orders.invoice', $order))
            ->assertOk();
        $this->actingAs($user)
            ->get(route('construction.payment-orders.invoice.xml', $order))
            ->assertOk();
        $this->actingAs($user)
            ->get(route('construction.payment-orders.invoice.fiscal-verification', $order))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('finance.construction-payment-orders.active'))
            ->assertSee(route('finance.construction-payment-orders.invoice', $order), false);
    }

    public function test_superadmin_can_delete_an_estimate_package_from_the_catalog(): void
    {
        $user = $this->superadmin();
        $order = ConstructionPaymentOrder::where('code', 'PAQ-005')->firstOrFail();
        $projectId = $order->construction_project_id;

        $response = $this->actingAs($user)->delete(route('construction.payment-orders.destroy', $order));

        $response->assertRedirect(route('construction.placeholder', [
            'section' => 'mano-obra',
            'project' => $projectId,
        ]));
        $response->assertSessionHas('status', 'Estimacion PAQ-005 eliminada correctamente.');
        $this->assertDatabaseMissing('construction_payment_orders', ['id' => $order->id]);
        $this->assertDatabaseHas('construction_audit_logs', [
            'construction_project_id' => $projectId,
            'action' => 'Estimacion eliminada',
            'description' => 'Se elimino la estimacion PAQ-005.',
        ]);
    }

    public function test_removing_a_current_payroll_payment_keeps_its_payroll_catalog_entry(): void
    {
        $user = $this->superadmin();
        $order = ConstructionPaymentOrder::where('code', 'NOM-S26')->firstOrFail();
        $payrollId = $order->construction_payroll_id;
        $laborUrl = route('construction.placeholder', [
            'section' => 'mano-obra',
            'project' => $order->construction_project_id,
        ]);

        $response = $this->actingAs($user)
            ->delete(route('construction.payment-orders.destroy', $order));

        $response->assertRedirect($laborUrl);
        $response->assertSessionHas('status', 'Pago vigente NOM-S26 enviado a los historiales correctamente.');
        $this->assertDatabaseHas('construction_payment_orders', [
            'id' => $order->id,
            'construction_payroll_id' => $payrollId,
            'status' => 'Descartada',
            'discarded_by' => $user->id,
        ]);
        $order->refresh();
        $this->assertNotNull($order->dismissed_at);
        $this->assertNotNull($order->discarded_at);
        $this->assertNull($order->payment_file_path);
        $this->assertSame('Descartada', $order->displayStatus());
        $this->assertSame('canceled', $order->statusClass());
        $this->assertDatabaseHas('construction_payrolls', [
            'id' => $payrollId,
            'code' => 'NOM-S26',
        ]);
        $this->assertDatabaseHas('construction_audit_logs', [
            'construction_project_id' => $order->construction_project_id,
            'action' => 'Pago vigente enviado a historial',
        ]);

        $this->actingAs($user)
            ->get($laborUrl)
            ->assertOk()
            ->assertDontSee('data-labor-code="NOM-S26"', false)
            ->assertSee('data-payroll-row', false)
            ->assertSee('NOM-S26');
        $this->actingAs($user)
            ->get(route('finance.construction-payment-orders.active'))
            ->assertDontSee('data-payment-code="NOM-S26"', false);
        $this->actingAs($user)
            ->get(route('finance.construction-payment-orders.history'))
            ->assertSee('data-payment-code="NOM-S26"', false)
            ->assertSee('Descartada');
        $this->actingAs($user)
            ->get(route('construction.placeholder', [
                'section' => 'pagos',
                'project' => $order->construction_project_id,
            ]))
            ->assertSee('data-payment-code="NOM-S26"', false)
            ->assertSee('Descartada');
    }

    public function test_finance_payment_moves_an_estimate_to_both_histories(): void
    {
        Storage::fake('local');
        $user = $this->superadmin();
        $order = ConstructionPaymentOrder::where('code', 'PAQ-005')->firstOrFail();

        $this->actingAs($user)->post(
            route('finance.construction-payment-orders.payment.store', $order),
            [
                'payment_file' => UploadedFile::fake()->create('pago-estimacion.pdf', 80, 'application/pdf'),
                'paid_on' => '2026-08-18',
            ]
        )->assertRedirect(route('finance.construction-payment-orders.history'));

        $this->actingAs($user)
            ->get(route('finance.construction-payment-orders.active'))
            ->assertDontSee('data-payment-code="PAQ-005"', false);
        $this->actingAs($user)
            ->get(route('construction.placeholder', 'mano-obra'))
            ->assertDontSee('data-labor-code="PAQ-005"', false);
        $this->actingAs($user)
            ->get(route('finance.construction-payment-orders.history'))
            ->assertSee('PAQ-005');
        $this->actingAs($user)
            ->get(route('construction.placeholder', ['section' => 'pagos', 'project' => $order->construction_project_id]))
            ->assertSee('PAQ-005');
    }

    private function superadmin(): User
    {
        return User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
    }
}
