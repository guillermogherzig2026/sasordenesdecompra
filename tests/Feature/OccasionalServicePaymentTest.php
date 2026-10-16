<?php

namespace Tests\Feature;

use App\Models\OccasionalService;
use App\Models\OccasionalServicePayment;
use App\Models\OccasionalServicePaymentAllocation;
use App\Models\RecurringService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OccasionalServicePaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_registered_payment_dialog_exposes_catalog_categories_and_tagged_service_options(): void
    {
        $user = User::factory()->create(['role' => 'finance', 'active' => true]);
        $tag = OccasionalService::where('name', 'Tag')->firstOrFail();
        $fuel = OccasionalService::where('name', 'Vale de Gasolina')->firstOrFail();
        $tagEntry = $tag->entries()->create([
            'name' => 'Caseta norte',
            'reference_number' => 'TAG-7788',
            'created_by' => $user->id,
        ]);
        $fuelEntry = $fuel->entries()->create([
            'name' => 'Gasolina central',
            'reference_number' => 'GAS-1133',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)->get(route('services.months', ['pay_occasional' => 1]))
            ->assertOk()
            ->assertSee('aria-label="Filtrar servicios ocasionales"', false)
            ->assertSee('data-payment-category="'.$tag->id.'"', false)
            ->assertSee('data-payment-category="'.$fuel->id.'"', false)
            ->assertSee('value="'.$tagEntry->id.'" data-payment-entry-category="'.$tag->id.'"', false)
            ->assertDontSee('value="'.$fuelEntry->id.'" data-payment-entry-category="'.$fuel->id.'"', false)
            ->assertSee('Efectivale')
            ->assertSee('data-payment-carousel-direction="next"', false);
    }

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

    public function test_fuel_payment_requires_a_balanced_allocation_and_saves_each_card(): void
    {
        $this->travelTo(Carbon::parse('2026-10-12 10:00:00'));
        $user = User::factory()->create(['role' => 'finance', 'active' => true]);
        $fuel = OccasionalService::where('name', 'Vale de Gasolina')->firstOrFail();
        $provider = $fuel->entries()->where('reference_number', 'EFECTIVALE-CONCENTRADORA')->firstOrFail();
        $firstCard = $fuel->entries()->create(['name' => 'Ana Lopez', 'reference_number' => 'GAS-1024']);
        $secondCard = $fuel->entries()->create(['name' => 'Carlos Perez', 'reference_number' => 'GAS-2058']);

        $this->actingAs($user)->get(route('services.months', ['pay_occasional' => 1]))
            ->assertOk()
            ->assertSee('data-fuel-allocation', false)
            ->assertSee('Asignar monto a tarjetas')
            ->assertSee('data-recipient-name="Ana Lopez"', false);

        $this->post(route('services.occasional.payments.store'), [
            'mode' => 'existing',
            'occasional_service_entry_id' => $provider->id,
            'amount' => '10000.00',
            'due_date' => '2026-10-20',
            'allocations' => [
                ['entry_id' => $firstCard->id, 'amount' => '3000.00'],
                ['entry_id' => $secondCard->id, 'amount' => '7000.00'],
            ],
        ])->assertRedirect(route('services.months', ['month' => '2026-10']));

        $payment = OccasionalServicePayment::with('allocations')->firstOrFail();
        $this->assertCount(2, $payment->allocations);
        $this->assertDatabaseHas('occasional_service_payment_allocations', [
            'occasional_service_payment_id' => $payment->id,
            'occasional_service_entry_id' => $firstCard->id,
            'recipient_name' => 'Ana Lopez',
            'reference_number' => 'GAS-1024',
            'amount' => 3000.00,
        ]);
        $this->assertSame('10000.00', $payment->amount);
        $this->assertEqualsWithDelta(10000.00, $payment->allocations->sum('amount'), 0.001);
    }

    public function test_fuel_payment_rejects_missing_unbalanced_duplicate_or_wrong_category_allocations(): void
    {
        $this->travelTo(Carbon::parse('2026-10-12 10:00:00'));
        $user = User::factory()->create(['role' => 'finance', 'active' => true]);
        $fuel = OccasionalService::where('name', 'Vale de Gasolina')->firstOrFail();
        $provider = $fuel->entries()->where('reference_number', 'EFECTIVALE-CONCENTRADORA')->firstOrFail();
        $otherProvider = $fuel->entries()->create(['name' => 'Otro proveedor', 'reference_number' => 'GAS-CON-1']);
        $card = $fuel->entries()->create(['name' => 'Ana Lopez', 'reference_number' => 'GAS-1024']);
        $tag = OccasionalService::where('name', 'Tag')->firstOrFail()->entries()->create(['name' => 'Caseta', 'reference_number' => 'TAG-001']);
        $url = route('services.months', ['pay_occasional' => 1]);
        $base = [
            'mode' => 'existing',
            'occasional_service_entry_id' => $provider->id,
            'amount' => '100.00',
            'due_date' => '2026-10-20',
        ];

        $this->actingAs($user)->from($url)->post(route('services.occasional.payments.store'), $base)
            ->assertRedirect($url)->assertSessionHasErrors('allocations');
        $this->from($url)->post(route('services.occasional.payments.store'), $base + [
            'allocations' => [['entry_id' => $card->id, 'amount' => '99.99']],
        ])->assertSessionHasErrors('allocations');
        $this->from($url)->post(route('services.occasional.payments.store'), $base + [
            'allocations' => [
                ['entry_id' => $card->id, 'amount' => '50.00'],
                ['entry_id' => $card->id, 'amount' => '50.00'],
            ],
        ])->assertSessionHasErrors('allocations.0.entry_id');
        $this->from($url)->post(route('services.occasional.payments.store'), $base + [
            'allocations' => [['entry_id' => $tag->id, 'amount' => '100.00']],
        ])->assertSessionHasErrors('allocations.0.entry_id');
        $this->from($url)->post(route('services.occasional.payments.store'), $base + [
            'allocations' => [['entry_id' => $provider->id, 'amount' => '100.00']],
        ])->assertSessionHasErrors('allocations.0.entry_id');
        $this->from($url)->post(route('services.occasional.payments.store'), array_replace($base, [
            'occasional_service_entry_id' => $otherProvider->id,
            'allocations' => [['entry_id' => $card->id, 'amount' => '100.00']],
        ]))->assertSessionHasErrors('occasional_service_entry_id');

        $this->assertSame(0, OccasionalServicePayment::count());
        $this->assertSame(0, OccasionalServicePaymentAllocation::count());
    }

    public function test_new_fuel_provider_cannot_be_created_from_payment_dialog(): void
    {
        $this->travelTo(Carbon::parse('2026-10-12 10:00:00'));
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $fuel = OccasionalService::where('name', 'Vale de Gasolina')->firstOrFail();
        $card = $fuel->entries()->create(['name' => 'Luis Garcia', 'reference_number' => 'GAS-3096']);

        $this->actingAs($user)->post(route('services.occasional.payments.store'), [
            'mode' => 'new',
            'category_id' => $fuel->id,
            'name' => 'Cuenta concentradora del proveedor',
            'reference_number' => 'CON-001',
            'amount' => '2500.00',
            'due_date' => '2026-10-20',
            'allocations' => [['entry_id' => $card->id, 'amount' => '2500.00']],
        ])->assertSessionHasErrors('occasional_service_entry_id');

        $this->assertSame(0, OccasionalServicePayment::count());
        $this->assertSame(2, $fuel->entries()->count());
    }

    public function test_fuel_payment_shows_varios_then_finance_payment_creates_filterable_history_rows(): void
    {
        Storage::fake('local');
        $this->travelTo(Carbon::parse('2026-10-12 10:00:00'));
        $finance = User::factory()->create(['role' => 'finance', 'active' => true]);
        $fuel = OccasionalService::where('name', 'Vale de Gasolina')->firstOrFail();
        $provider = $fuel->entries()->where('reference_number', 'EFECTIVALE-CONCENTRADORA')->firstOrFail();
        $ana = $fuel->entries()->create(['name' => 'Ana Lopez', 'reference_number' => 'GAS-1024']);
        $carlos = $fuel->entries()->create(['name' => 'Carlos Perez', 'reference_number' => 'GAS-2058']);

        $this->actingAs($finance)->post(route('services.occasional.payments.store'), [
            'mode' => 'existing',
            'occasional_service_entry_id' => $provider->id,
            'amount' => '100.00',
            'due_date' => '2026-10-20',
            'allocations' => [
                ['entry_id' => $ana->id, 'amount' => '30.00'],
                ['entry_id' => $carlos->id, 'amount' => '70.00'],
            ],
        ])->assertRedirect();

        $payment = OccasionalServicePayment::firstOrFail();
        foreach (['services.months', 'finance.services.index'] as $route) {
            $this->get(route($route))->assertOk()
                ->assertSee('data-fuel-breakdown="'.$payment->id.'"', false)
                ->assertSee('Varios')
                ->assertSee('GAS-1024')
                ->assertSee('GAS-2058')
                ->assertSee('Ana Lopez')
                ->assertSee('Carlos Perez');
        }
        $this->get(route('services.history'))->assertOk()->assertDontSee('GAS-1024');
        $this->get(route('services.history.general'))->assertOk()->assertDontSee('GAS-2058');
        $this->get(route('finance.services.occasional.payment', $payment))->assertOk()
            ->assertSee('Efectivale')->assertSee('$100.00');

        $this->travelTo(Carbon::parse('2026-11-02 10:00:00'));
        $this->get(route('finance.services.index'))->assertOk()
            ->assertSee('OCP-00001')
            ->assertSee(route('finance.services.occasional.payment', $payment));
        $this->post(route('finance.services.occasional.payment.store', $payment), [
            'payment_file' => UploadedFile::fake()->create('transferencia.pdf', 12, 'application/pdf'),
            'payment_paid_on' => '2026-11-02',
        ])->assertRedirect(route('finance.services.index'));

        $this->assertDatabaseHas('occasional_service_payments', [
            'id' => $payment->id,
            'paid_by' => $finance->id,
        ]);
        $this->assertSame('2026-11-02', $payment->fresh()->payment_paid_on->toDateString());
        Storage::disk('local')->assertExists($payment->fresh()->payment_file_path);
        $this->get(route('finance.services.occasional.payment', $payment))->assertForbidden();
        $this->post(route('finance.services.occasional.payment.store', $payment), [
            'payment_file' => UploadedFile::fake()->create('again.pdf', 12, 'application/pdf'),
            'payment_paid_on' => '2026-11-02',
        ])->assertStatus(409);

        foreach (['services.history', 'services.history.general'] as $route) {
            $html = $this->get(route($route))->assertOk()->getContent();
            $this->assertStringContainsString('GAS-1024', $html);
            $this->assertStringContainsString('GAS-2058', $html);
            $this->assertStringContainsString('Ana Lopez', $html);
            $this->assertStringContainsString('Carlos Perez', $html);
            $this->assertStringContainsString('$30.00', $html);
            $this->assertStringContainsString('$70.00', $html);
            $this->assertSame(2, substr_count($html, 'data-filter-value="OCP-'.$payment->id.'"'));
            $this->assertStringContainsString(route('services.occasional.payments.file', $payment), $html);
        }

        $historyHtml = $this->get(route('services.history'))->getContent();
        preg_match('/<section[^>]+data-month-key="2026-11".*?<\/section>/s', $historyHtml, $november);
        $this->assertNotEmpty($november);
        $this->assertStringContainsString('Monto pagado del mes</span>', $november[0]);
        $this->assertStringContainsString('<strong>$100.00</strong>', $november[0]);
        $this->assertStringNotContainsString('$100.00</td>', $november[0]);

        $this->get(route('services.months'))->assertOk()->assertDontSee('data-fuel-breakdown="'.$payment->id.'"', false);
        $this->get(route('finance.services.index'))->assertOk()->assertDontSee('data-fuel-breakdown="'.$payment->id.'"', false);
        $this->get(route('services.occasional.payments.file', $payment))->assertOk();
        $this->get(route('services.occasional.payments.file.pdf', $payment))
            ->assertOk()->assertHeader('Content-Type', 'application/pdf');
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
            'bank' => 'BBVA',
            'payer_account' => '00112233',
            'amount' => '340.00',
            'due_date' => '2026-10-20',
        ])->assertRedirect(route('services.months', ['month' => '2026-10']));

        $this->assertDatabaseHas('occasional_service_entries', [
            'occasional_service_id' => $category->id,
            'name' => 'Autopista norte',
            'reference_number' => 'TAG-NORTE-1',
            'bank' => 'BBVA',
            'payer_account' => '00112233',
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

    public function test_payment_created_by_superadmin_appears_in_finance_monthly_panels(): void
    {
        $this->travelTo(Carbon::parse('2026-10-12 10:00:00'));
        $superAdmin = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $finance = User::factory()->create([
            'role' => 'finance',
            'active' => true,
            'menu_permissions' => ['finance.services.payments', 'services.months'],
        ]);
        $entry = OccasionalService::where('name', 'Tag')->firstOrFail()->entries()->create([
            'name' => 'Caseta norte',
            'reference_number' => 'TAG-FIN-7788',
            'created_by' => $superAdmin->id,
        ]);

        $this->actingAs($superAdmin)->post(route('services.occasional.payments.store'), [
            'mode' => 'existing',
            'occasional_service_entry_id' => $entry->id,
            'amount' => '725.50',
            'due_date' => '2026-10-20',
        ])->assertRedirect(route('services.months', ['month' => '2026-10']));

        foreach (['finance.services.index', 'services.months'] as $route) {
            $html = $this->actingAs($finance)->get(route($route))->assertOk()->getContent();
            preg_match('/<section[^>]+data-month-key="2026-10".*?<\/section>/s', $html, $october);
            preg_match('/<section[^>]+data-month-key="2026-11".*?<\/section>/s', $html, $november);

            $this->assertNotEmpty($october, $route);
            $this->assertNotEmpty($november, $route);
            $this->assertStringContainsString('OCP-00001', $october[0]);
            $this->assertStringContainsString('Caseta norte', $october[0]);
            $this->assertStringContainsString('TAG-FIN-7788', $october[0]);
            $this->assertStringContainsString('$725.50', $october[0]);
            $this->assertStringContainsString('Pendiente', $october[0]);
            $this->assertMatchesRegularExpression('/Monto total mensual<\/span>\s*<strong>\$725\.50<\/strong>/s', $october[0]);
            $this->assertMatchesRegularExpression('/Monto pendiente por pagar<\/span>\s*<strong>\$725\.50<\/strong>/s', $october[0]);
            $this->assertStringNotContainsString('No hay servicios programados para este mes.', $october[0]);
            $this->assertStringNotContainsString('OCP-00001', $november[0]);
        }
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

    public function test_only_authorized_finance_user_can_confirm_an_occasional_payment(): void
    {
        Storage::fake('local');
        $entry = OccasionalService::where('name', 'Tag')->firstOrFail()->entries()->create([
            'name' => 'Caseta norte',
            'reference_number' => 'TAG-FIN-001',
        ]);
        $payment = $entry->payments()->create([
            'amount' => '125.00',
            'due_date' => now()->toDateString(),
        ]);
        $restricted = User::factory()->create([
            'role' => 'finance',
            'active' => true,
            'menu_permissions' => ['services.months', 'finance.services.history'],
        ]);
        $payload = [
            'payment_file' => UploadedFile::fake()->create('transferencia.pdf', 12, 'application/pdf'),
            'payment_paid_on' => now()->toDateString(),
        ];

        $this->actingAs($restricted)->get(route('finance.services.occasional.payment', $payment))->assertForbidden();
        $this->post(route('finance.services.occasional.payment.store', $payment), $payload)->assertForbidden();
        $this->assertNull($payment->fresh()->payment_paid_on);
        $this->get(route('services.occasional.payments.file', $payment))->assertNotFound();

        $finance = User::factory()->create(['role' => 'finance', 'active' => true]);
        $this->actingAs($finance)->post(route('finance.services.occasional.payment.store', $payment), [
            'payment_file' => UploadedFile::fake()->create('not-a-receipt.txt', 1, 'text/plain'),
            'payment_paid_on' => now()->addDay()->toDateString(),
        ])->assertSessionHasErrors(['payment_file', 'payment_paid_on']);
        $this->assertNull($payment->fresh()->payment_paid_on);
    }
}
