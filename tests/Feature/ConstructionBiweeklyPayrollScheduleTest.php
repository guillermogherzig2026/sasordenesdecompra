<?php

namespace Tests\Feature;

use App\Models\ConstructionPayroll;
use App\Models\ConstructionProject;
use App\Models\User;
use App\Services\ConstructionPayrollScheduleService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConstructionBiweeklyPayrollScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_biweekly_payroll_adds_the_august_30_occurrence_when_labor_screen_loads(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse(
            '2026-08-21 12:00:00',
            ConstructionPayrollScheduleService::TIMEZONE,
        ));

        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $project = ConstructionProject::where('project_key', 'OBR-001')->firstOrFail();
        $latestPayrollId = ConstructionPayroll::max('id') ?? 0;

        $this->actingAs($user)
            ->post(route('construction.payrolls.store'), [
                'payroll_form' => 'create',
                'construction_project_id' => $project->id,
                'contractor' => 'Leoncio',
                'description' => 'Nomina Leoncio',
                'area_category' => 'Mano de obra',
                'periodicity' => 'Quincenal',
                'period_start' => '2026-08-01',
                'period_end_indefinite' => '1',
                'progress_percent' => '0',
                'amount' => '102040',
                'status' => 'Programada',
            ])
            ->assertSessionHasNoErrors();

        $payroll = ConstructionPayroll::where('id', '>', $latestPayrollId)
            ->latest('id')
            ->firstOrFail();

        $this->assertDatabaseHas('construction_payment_orders', [
            'construction_payroll_id' => $payroll->id,
            'code' => $payroll->code,
            'period_start' => '2026-08-01 00:00:00',
            'period_end' => '2026-08-15 00:00:00',
            'payment_due_date' => '2026-08-15 00:00:00',
            'scheduled_for' => '2026-08-01 00:00:00',
        ]);
        $this->assertCount(1, $payroll->paymentOrders()->get());

        $response = $this->actingAs($user)->get(
            '/administracion-obra/secciones/mano-obra?project='.$project->id,
        );

        $response->assertOk();

        $secondCode = $payroll->code.'-20260816';

        $this->assertDatabaseHas('construction_payment_orders', [
            'construction_payroll_id' => $payroll->id,
            'code' => $secondCode,
            'period_start' => '2026-08-16 00:00:00',
            'period_end' => '2026-08-30 00:00:00',
            'payment_due_date' => '2026-08-30 00:00:00',
            'scheduled_for' => '2026-08-16 00:00:00',
            'amount' => '102040.00',
            'status' => 'Programada',
        ]);
        $this->assertCount(2, $payroll->paymentOrders()->get());

        $this->actingAs($user)
            ->get('/administracion-obra/secciones/mano-obra?project='.$project->id)
            ->assertOk();

        $this->assertCount(2, $payroll->paymentOrders()->get());

        $this->actingAs($user)
            ->get(route('finance.construction-payment-orders.active'))
            ->assertOk()
            ->assertSee('data-payment-code="'.$secondCode.'"', false)
            ->assertSee('30/08/2026');
    }
}
