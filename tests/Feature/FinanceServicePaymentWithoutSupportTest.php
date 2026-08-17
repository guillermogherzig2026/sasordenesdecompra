<?php

namespace Tests\Feature;

use App\Models\RecurringService;
use App\Models\RecurringServiceReceipt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FinanceServicePaymentWithoutSupportTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_can_register_a_service_payment_without_a_support_file(): void
    {
        Storage::fake('local');

        $finance = User::factory()->create([
            'role' => 'finance',
            'active' => true,
        ]);

        $service = RecurringService::create([
            'folio' => 'SRV-TEST-001',
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
            'start_date' => '2026-08-01',
            'status' => 'active',
            'is_domiciled' => false,
            'created_by' => $finance->id,
        ]);

        $response = $this->actingAs($finance)->post(
            route('finance.services.payment.store', [$service, '2026-08-01']),
            [
                'payment_file' => UploadedFile::fake()->create('comprobante.pdf', 100, 'application/pdf'),
                'payment_paid_on' => '2026-08-01',
            ]
        );

        $response->assertRedirect(route('finance.services.index'));

        $receipt = RecurringServiceReceipt::where('recurring_service_id', $service->id)->firstOrFail();

        $this->assertSame('2026-08-01', $receipt->due_date->toDateString());
        $this->assertSame('850.50', $receipt->amount);
        $this->assertNull($receipt->support_file_path);
        $this->assertSame('comprobante.pdf', $receipt->payment_original_name);
        $this->assertSame('paid', $receipt->status);
        $this->assertSame($finance->id, $receipt->paid_by);

        Storage::disk('local')->assertExists($receipt->payment_file_path);
    }
}
