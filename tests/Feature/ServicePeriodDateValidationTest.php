<?php

namespace Tests\Feature;

use App\Models\RecurringService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePeriodDateValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cutoff_date_cannot_be_before_period_start_date(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $service = $this->service($user);

        $response = $this->actingAs($user)
            ->from(route('services.edit', $service))
            ->put(route('services.update', $service), $this->payload([
                'cutoff_day' => 19,
            ]));

        $response->assertRedirect(route('services.edit', $service));
        $response->assertSessionHasErrors([
            'cutoff_day' => 'La fecha de corte debe ser igual o posterior a la fecha de inicio del periodo.',
        ]);
        $this->assertSame(25, $service->fresh()->cutoff_day);
    }

    public function test_cutoff_date_can_match_period_start_date(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $service = $this->service($user);

        $response = $this->actingAs($user)
            ->put(route('services.update', $service), $this->payload());

        $response->assertRedirect(route('services.catalog'));
        $service->refresh();

        $this->assertSame('2026-08-20', $service->start_date->toDateString());
        $this->assertSame(20, $service->cutoff_day);
        $this->assertSame(8, $service->cutoff_month);
        $this->assertSame(2026, $service->cutoff_year);
    }

    private function service(User $user): RecurringService
    {
        return RecurringService::create([
            'folio' => 'SRV-DATE-001',
            'holder' => 'Empresa de prueba',
            'company_name' => 'Empresa de prueba',
            'bank' => 'Banco de prueba',
            'payer_account' => '0001',
            'service_name' => 'Servicio de prueba',
            'provider' => 'Proveedor de prueba',
            'service_number' => '123456',
            'category' => 'Otros',
            'cost' => 850.50,
            'validity' => 'Indefinido',
            'payment_interval_days' => 30,
            'start_date' => '2026-08-20',
            'cutoff_day' => 25,
            'cutoff_month' => 8,
            'cutoff_year' => 2026,
            'reference' => 'Referencia',
            'status' => 'active',
            'created_by' => $user->id,
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'holder' => 'Empresa de prueba',
            'company_name' => 'Empresa de prueba',
            'bank' => 'Banco de prueba',
            'payer_account' => '0001',
            'branch' => 'Sucursal de prueba',
            'service_name' => 'Servicio de prueba',
            'provider' => 'Proveedor de prueba',
            'service_number' => '123456',
            'category' => 'Otros',
            'cost' => 850.50,
            'validity' => 'Indefinido',
            'payment_lapse' => 30,
            'start_date' => '2026-08-20',
            'start_day' => 20,
            'start_month' => 8,
            'start_year' => 2026,
            'cutoff_day' => 20,
            'cutoff_month' => 8,
            'cutoff_year' => 2026,
            'reference' => 'Referencia',
            'service_location' => 'Direccion de prueba',
            'notes' => 'Notas de prueba',
        ], $overrides);
    }
}
