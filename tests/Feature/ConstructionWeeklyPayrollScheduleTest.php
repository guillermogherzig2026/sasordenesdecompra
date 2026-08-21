<?php

namespace Tests\Feature;

use App\Models\ConstructionPayroll;
use App\Models\ConstructionProject;
use App\Models\User;
use App\Services\ConstructionPayrollScheduleService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConstructionWeeklyPayrollScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_weekly_order_enters_finance_on_sunday_and_is_due_on_friday(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::parse(
            '2026-08-19 12:00:00',
            ConstructionPayrollScheduleService::TIMEZONE,
        ));

        $user = $this->superadmin();
        $project = ConstructionProject::where('project_key', 'OBR-001')->firstOrFail();
        $payroll = $this->createWeeklyPayroll($user, $project);
        $payrollCode = $payroll->code;
        $order = $payroll->paymentOrders()->firstOrFail();

        $this->assertSame('2026-08-23', $order->scheduled_for?->format('Y-m-d'));
        $this->assertSame('2026-08-23', $order->period_start?->format('Y-m-d'));
        $this->assertSame('2026-08-28', $order->period_end?->format('Y-m-d'));
        $this->assertSame('2026-08-28', $order->payment_due_date?->format('Y-m-d'));

        $this->actingAs($user)
            ->get(route('finance.construction-payment-orders.active'))
            ->assertDontSee('data-payment-code="'.$payrollCode.'"', false);

        CarbonImmutable::setTestNow(CarbonImmutable::parse(
            '2026-08-23 00:05:00',
            ConstructionPayrollScheduleService::TIMEZONE,
        ));

        $this->artisan('construction:generate-weekly-payroll-orders', ['--date' => '2026-08-23'])
            ->expectsOutput('0 ordenes de pago semanales generadas.')
            ->assertSuccessful();

        $this->actingAs($user)
            ->get(route('finance.construction-payment-orders.active'))
            ->assertSee('data-payment-code="'.$payrollCode.'"', false)
            ->assertSee('28/08/2026');
    }

    public function test_weekly_generation_is_idempotent_and_continues_after_a_payment(): void
    {
        Storage::fake('local');
        CarbonImmutable::setTestNow(CarbonImmutable::parse(
            '2026-08-23 08:00:00',
            ConstructionPayrollScheduleService::TIMEZONE,
        ));

        $user = $this->superadmin();
        $project = ConstructionProject::where('project_key', 'OBR-001')->firstOrFail();
        $payroll = $this->createWeeklyPayroll($user, $project);
        $payrollCode = $payroll->code;
        $firstOrder = $payroll->paymentOrders()->whereDate('scheduled_for', '2026-08-23')->firstOrFail();

        $this->actingAs($user)->post(
            route('finance.construction-payment-orders.payment.store', $firstOrder),
            [
                'payment_file' => UploadedFile::fake()->create('pago-semanal.pdf', 80, 'application/pdf'),
                'paid_on' => '2026-08-28',
            ],
        )->assertRedirect(route('finance.construction-payment-orders.history'));

        $payroll->refresh();
        $this->assertSame('En revision', $payroll->status);
        $this->assertNull($payroll->payment_date);

        $service = app(ConstructionPayrollScheduleService::class);
        $this->assertSame(1, $service->generateWeeklyDueOccurrences('2026-08-30'));
        $this->assertSame(0, $service->generateWeeklyDueOccurrences('2026-08-30'));
        $this->assertSame(2, $payroll->paymentOrders()->count());

        $secondOrder = $payroll->paymentOrders()
            ->whereDate('scheduled_for', '2026-08-30')
            ->firstOrFail();

        $this->assertSame($payrollCode.'-20260830', $secondOrder->code);
        $this->assertSame('2026-09-04', $secondOrder->payment_due_date?->format('Y-m-d'));

        CarbonImmutable::setTestNow(CarbonImmutable::parse(
            '2026-08-30 08:00:00',
            ConstructionPayrollScheduleService::TIMEZONE,
        ));

        $this->actingAs($user)
            ->get(route('finance.construction-payment-orders.active'))
            ->assertDontSee('data-payment-code="'.$payrollCode.'"', false)
            ->assertSee('data-payment-code="'.$payrollCode.'-20260830"', false)
            ->assertSee('04/09/2026');
    }

    private function createWeeklyPayroll(
        User $user,
        ConstructionProject $project,
    ): ConstructionPayroll {
        $latestPayrollId = ConstructionPayroll::max('id') ?? 0;

        $this->actingAs($user)->post(route('construction.payrolls.store'), [
            'payroll_form' => 'create',
            'construction_project_id' => $project->id,
            'contractor' => 'Constructora semanal',
            'description' => 'Nomina semanal programada',
            'area' => 'Mano de obra',
            'periodicity' => 'Semanal',
            'period_start' => '2026-08-17',
            'period_end' => '2026-09-06',
            'progress' => 0,
            'amount' => 12500,
            'status' => 'En revision',
            'payment_due_date' => '2026-08-20',
        ])->assertSessionHasNoErrors();

        return ConstructionPayroll::query()
            ->where('id', '>', $latestPayrollId)
            ->latest('id')
            ->firstOrFail();
    }

    private function superadmin(): User
    {
        return User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
    }
}
