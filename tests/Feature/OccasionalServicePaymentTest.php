<?php

namespace Tests\Feature;

use App\Models\OccasionalService;
use App\Models\OccasionalServicePayment;
use App\Models\RecurringService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OccasionalServicePaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_dialog_creates_one_pending_charge_in_selected_month_only(): void
    {
        $this->travelTo(Carbon::parse('2026-10-12 10:00:00'));
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $entry = OccasionalService::where('name', 'Tag')->firstOrFail()->entries()->create([
            'name' => 'Caseta norte',
            'reference_number' => 'TAG-7788',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)->get(route('services.months', ['pay_occasional' => 1]))
            ->assertOk()
            ->assertSee('data-occasional-payment-dialog', false)
            ->assertSee('Caseta norte')
            ->assertSee('TAG-7788');

        $this->post(route('services.occasional.payments.store'), [
            'mode' => 'existing',
            'occasional_service_entry_id' => $entry->id,
            'amount' => '725.50',
            'due_date' => '2026-11-05',
        ])->assertRedirect(route('services.months', ['month' => '2026-11']));

        $this->assertDatabaseHas('occasional_service_payments', [
            'occasional_service_entry_id' => $entry->id,
            'amount' => 725.50,
            'created_by' => $user->id,
        ]);
        $this->assertSame(1, OccasionalServicePayment::count());
        $this->assertSame('2026-11-05', OccasionalServicePayment::firstOrFail()->due_date->toDateString());
        $this->assertSame(0, RecurringService::count());

        $html = $this->get(route('services.months', ['month' => '2026-11']))->assertOk()->getContent();
        preg_match('/<section[^>]+data-month-key="2026-11".*?<\/section>/s', $html, $november);
        preg_match('/<section[^>]+data-month-key="2026-12".*?<\/section>/s', $html, $december);
        $this->assertNotEmpty($november);
        $this->assertNotEmpty($december);
        $this->assertStringContainsString('aria-expanded="true"', $november[0]);
        $this->assertStringContainsString('OCP-00001', $november[0]);
        $this->assertStringContainsString('$725.50', $november[0]);
        $this->assertStringContainsString('TAG-7788', $november[0]);
        $this->assertStringContainsString('Pendiente', $november[0]);
        $this->assertStringNotContainsString('OCP-00001', $december[0]);
    }

    public function test_new_service_and_payment_can_be_created_together_from_empty_catalog(): void
    {
        $this->travelTo(Carbon::parse('2026-10-12 10:00:00'));
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $category = OccasionalService::where('name', 'Tag')->firstOrFail();

        $this->actingAs($user)->get(route('services.months', ['pay_occasional' => 1]))
            ->assertOk()
            ->assertSee('Nuevo servicio')
            ->assertSee('Nombre del servicio/Titular')
            ->assertSee('Número de referencia')
            ->assertSee('Agregar a Servicios Mensuales');

        $this->post(route('services.occasional.payments.store'), [
            'mode' => 'new',
            'category_id' => $category->id,
            'name' => 'Autopista norte',
            'reference_number' => 'TAG-NORTE-1',
            'amount' => '340.00',
            'due_date' => '2026-10-20',
        ])->assertRedirect(route('services.months', ['month' => '2026-10']));

        $this->assertDatabaseHas('occasional_service_entries', [
            'occasional_service_id' => $category->id,
            'name' => 'Autopista norte',
            'reference_number' => 'TAG-NORTE-1',
        ]);
        $this->assertSame(1, OccasionalServicePayment::count());
        $this->get(route('services.months', ['month' => '2026-10']))
            ->assertOk()
            ->assertSee('Autopista norte')
            ->assertSee('TAG-NORTE-1')
            ->assertSee('$340.00');

        $this->post(route('services.occasional.payments.store'), [
            'mode' => 'new',
            'category_id' => $category->id,
            'name' => 'Otra caseta',
            'reference_number' => 'TAG-NORTE-1',
            'amount' => '100.00',
            'due_date' => '2026-10-21',
        ])->assertSessionHasErrors('reference_number');
        $this->assertSame(1, OccasionalServicePayment::count());
    }

    public function test_invalid_or_out_of_range_payment_is_not_created_and_reopens_dialog(): void
    {
        $this->travelTo(Carbon::parse('2026-10-12 10:00:00'));
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $url = route('services.months', ['pay_occasional' => 1]);

        $this->actingAs($user)->from($url)->post(route('services.occasional.payments.store'), [
            'mode' => 'existing',
            'occasional_service_entry_id' => 999999,
            'amount' => '0',
            'due_date' => '2026-09-15',
        ])->assertRedirect($url)->assertSessionHasErrors(['occasional_service_entry_id', 'amount', 'due_date']);

        $this->assertSame(0, OccasionalServicePayment::count());
        $this->get($url)->assertOk()->assertSee('data-auto-open', false);
    }

    public function test_user_without_create_permission_cannot_schedule_payment(): void
    {
        $user = User::factory()->create([
            'role' => 'finance',
            'active' => true,
            'menu_permissions' => ['services.months', 'finance.services.catalog'],
        ]);

        $this->actingAs($user)->get(route('services.months'))
            ->assertOk()
            ->assertDontSee('Pagar servicio ocasional')
            ->assertDontSee('<dialog class="occasional-payment-dialog"', false);

        $this->post(route('services.occasional.payments.store'), [
            'occasional_service_entry_id' => 1,
            'amount' => '100',
            'due_date' => now()->toDateString(),
        ])->assertForbidden();
        $this->assertSame(0, OccasionalServicePayment::count());
    }
}
