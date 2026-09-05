<?php

namespace Tests\Feature;

use App\Models\RecurringService;
use App\Models\RecurringServiceReceipt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ServiceHistoryMonthlyTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_service_history_is_grouped_into_collapsible_months_from_newest_to_oldest(): void
    {
        Carbon::setTestNow('2026-09-04 10:00:00');

        $finance = User::factory()->create([
            'role' => 'finance',
            'active' => true,
        ]);

        $augustService = $this->service($finance, [
            'folio' => 'SRV-HISTORY-AUG',
            'service_name' => 'Servicio pagado en agosto',
            'cost' => 1250.50,
        ]);
        $julyService = $this->service($finance, [
            'folio' => 'SRV-HISTORY-JUL',
            'service_name' => 'Servicio pagado en julio',
            'cost' => 800,
            'status' => 'inactive',
        ]);

        $this->paidReceipt($augustService, $finance, '2026-08-10', 1250.50);
        $this->paidReceipt($julyService, $finance, '2026-07-12', 800);

        $response = $this->actingAs($finance)->get(route('services.history'));

        $response
            ->assertOk()
            ->assertSeeInOrder([
                'septiembre de 2026',
                'agosto de 2026',
                'julio de 2026',
            ])
            ->assertSee('data-month-key="2026-09"', false)
            ->assertSee('data-month-key="2026-08"', false)
            ->assertSee('data-month-key="2026-07"', false)
            ->assertSee('Servicio pagado en agosto')
            ->assertSee('Servicio pagado en julio');

        $html = $response->getContent();

        $this->assertSame(3, substr_count($html, 'data-month-key="'));
        preg_match_all('/<button class="month-toggle"[^>]+aria-expanded="(true|false)"/', $html, $matches);
        $this->assertSame(['true', 'false', 'false'], $matches[1]);

        $augustPanel = $this->panelHtml($html, '2026-08', '2026-07');
        $julyPanel = $this->panelHtml($html, '2026-07');

        $this->assertStringContainsString('Servicio pagado en agosto', $augustPanel);
        $this->assertStringContainsString('$1,250.50', $augustPanel);
        $this->assertStringContainsString('Servicio pagado en julio', $julyPanel);
        $this->assertStringContainsString('$800.00', $julyPanel);
    }

    private function service(User $user, array $attributes): RecurringService
    {
        return RecurringService::create([
            'folio' => 'SRV-HISTORY',
            'holder' => 'Empresa historica',
            'company_name' => 'Empresa historica',
            'bank' => 'Banco de prueba',
            'payer_account' => '0001',
            'service_name' => 'Servicio historico',
            'provider' => 'Proveedor historico',
            'service_number' => 'HIST-001',
            'category' => 'Otros',
            'cost' => 100,
            'validity' => 'Indefinido',
            'payment_interval_days' => 30,
            'start_date' => '2026-01-01',
            'status' => 'active',
            'is_domiciled' => false,
            'created_by' => $user->id,
            ...$attributes,
        ]);
    }

    private function paidReceipt(RecurringService $service, User $user, string $paidOn, float $amount): void
    {
        RecurringServiceReceipt::create([
            'recurring_service_id' => $service->id,
            'due_date' => $paidOn,
            'period_start' => Carbon::parse($paidOn)->subMonth()->toDateString(),
            'amount' => $amount,
            'payment_file_path' => 'service-payments/'.$service->folio.'.pdf',
            'payment_original_name' => $service->folio.'.pdf',
            'payment_paid_on' => $paidOn,
            'paid_by' => $user->id,
            'status' => 'paid',
        ]);
    }

    private function panelHtml(string $html, string $monthKey, ?string $nextMonthKey = null): string
    {
        $start = strpos($html, 'data-month-key="'.$monthKey.'"');
        $this->assertNotFalse($start);

        if (! $nextMonthKey) {
            return substr($html, $start);
        }

        $end = strpos($html, 'data-month-key="'.$nextMonthKey.'"', $start);
        $this->assertNotFalse($end);

        return substr($html, $start, $end - $start);
    }
}
