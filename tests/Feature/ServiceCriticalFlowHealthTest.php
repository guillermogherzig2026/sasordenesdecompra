<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\RecurringService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceCriticalFlowHealthTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrative_assistant_can_pause_and_reactivate_a_service(): void
    {
        $assistant = User::factory()->create([
            'role' => 'administrative_assistant',
            'active' => true,
        ]);
        $service = $this->service($assistant);

        $this->actingAs($assistant)
            ->get(route('services.catalog'))
            ->assertOk()
            ->assertSee('Pausar');

        $this->actingAs($assistant)
            ->patch(route('services.status', [$service, 'paused']))
            ->assertRedirect();
        $this->assertSame('paused', $service->fresh()->status);

        $this->actingAs($assistant)
            ->patch(route('services.status', [$service, 'active']))
            ->assertRedirect();
        $this->assertSame('active', $service->fresh()->status);
    }

    public function test_service_form_offers_day_31_and_a_180_day_payment_interval(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        Company::create(['name' => 'Empresa de prueba', 'rfc' => 'EPR260901AA1']);

        $this->actingAs($user)
            ->get(route('services.create'))
            ->assertOk()
            ->assertSee('<select name="cutoff_day"', false)
            ->assertSee('<option value="31"', false)
            ->assertSee('<option value="180"', false);
    }

    public function test_service_can_be_saved_with_a_month_end_cutoff_and_180_day_interval(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        Company::create(['name' => 'Empresa de prueba', 'rfc' => 'EPR260901AA1']);

        $this->actingAs($user)
            ->post(route('services.store'), $this->payload())
            ->assertRedirect(route('services.catalog'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('recurring_services', [
            'company_name' => 'Empresa de prueba',
            'payment_interval_days' => 180,
            'cutoff_day' => 31,
            'cutoff_month' => 8,
            'cutoff_year' => 2026,
        ]);

        $service = RecurringService::where('service_number', 'HEALTH-180')->firstOrFail();
        $response = $this->actingAs($user)->get(route('services.months'));
        $response->assertOk()->assertSee('27/02/2027');
        $this->assertSame(180, $service->payment_interval_days);
    }

    public function test_user_without_service_permissions_cannot_pause_a_service(): void
    {
        $owner = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $buyer = User::factory()->create([
            'role' => 'buyer',
            'active' => true,
            'menu_permissions' => [],
        ]);
        $service = $this->service($owner);

        $this->actingAs($buyer)
            ->patch(route('services.status', [$service, 'paused']))
            ->assertForbidden();

        $this->assertSame('active', $service->fresh()->status);
    }

    private function service(User $user): RecurringService
    {
        return RecurringService::create([
            'folio' => 'SRV-HEALTH-001',
            'holder' => 'Empresa de prueba',
            'company_name' => 'Empresa de prueba',
            'bank' => 'BB',
            'payer_account' => '0001',
            'service_name' => 'Servicio de salud',
            'provider' => 'Proveedor de prueba',
            'service_number' => 'HEALTH-001',
            'category' => 'Otros',
            'cost' => 100,
            'validity' => 'Indefinido',
            'payment_interval_days' => 30,
            'start_date' => '2026-08-01',
            'cutoff_day' => 31,
            'cutoff_month' => 8,
            'cutoff_year' => 2026,
            'reference' => 'Referencia',
            'status' => 'active',
            'created_by' => $user->id,
        ]);
    }

    private function payload(): array
    {
        return [
            'holder' => 'Empresa de prueba',
            'company_name' => 'Empresa de prueba',
            'bank' => 'BB',
            'payer_account' => '0001',
            'branch' => 'Matriz',
            'service_name' => 'Servicio semestral',
            'provider' => 'Proveedor de prueba',
            'service_number' => 'HEALTH-180',
            'category' => 'Otros',
            'cost' => 1800,
            'validity' => 'Indefinido',
            'payment_lapse' => 180,
            'start_date' => '2026-08-01',
            'start_day' => 1,
            'start_month' => 8,
            'start_year' => 2026,
            'cutoff_day' => 31,
            'cutoff_month' => 8,
            'cutoff_year' => 2026,
            'reference' => 'Referencia 180',
            'service_location' => 'Direccion de prueba',
            'notes' => 'Prueba automatizada de salud',
        ];
    }
}
