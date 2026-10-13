<?php

namespace Tests\Feature;

use App\Models\RecurringService;
use App\Models\RecurringServiceReceipt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
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
            ->assertSee('Servicio pagado en julio')
            ->assertDontSee('DEMO-SEP-2026');

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

    public function test_local_demo_payment_is_visible_in_september_without_creating_a_real_payment(): void
    {
        Carbon::setTestNow('2026-10-12 10:00:00');
        $this->app->detectEnvironment(fn () => 'local');

        $finance = User::factory()->create([
            'role' => 'finance',
            'active' => true,
        ]);

        $response = $this->actingAs($finance)->get(route('services.history'));

        $response
            ->assertOk()
            ->assertSee('data-month-key="2026-09"', false)
            ->assertSee('DEMO-SEP-2026')
            ->assertSee('Pagado (demo)')
            ->assertSee('aria-label="Ver factura de ejemplo"', false)
            ->assertSee('aria-label="Ver comprobante de pago de ejemplo"', false)
            ->assertSee('data-payment-preview', false)
            ->assertSee(route('services.demo-invoice-file'))
            ->assertSee(route('services.demo-payment-file'))
            ->assertSee('data-demo-row', false)
            ->assertSee('No es un movimiento real y no se incluye en los totales ni reportes.');

        preg_match('/<tr class="service-row-locked service-demo-payment-row"[^>]*>(.*?)<\/tr>/s', $response->getContent(), $demoRow);
        $this->assertNotEmpty($demoRow);
        $this->assertSame(2, preg_match_all('/>\s*Ver\s*<\/button>/', $demoRow[1]));

        $this->assertSame(0, RecurringService::count());
        $this->assertSame(0, RecurringServiceReceipt::count());
    }

    public function test_demo_receipt_opens_inline_and_downloads_as_pdf_only_locally(): void
    {
        $this->app->detectEnvironment(fn () => 'local');
        $finance = User::factory()->create(['role' => 'finance', 'active' => true]);

        $preview = $this->actingAs($finance)->get(route('services.demo-payment-file'));
        $preview->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('inline; filename="comprobante-demo-septiembre-2026.pdf"', $preview->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF-', $preview->getContent());

        $download = $this->actingAs($finance)->get(route('services.demo-payment-file', ['download' => 1]));
        $download->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('attachment; filename="comprobante-demo-septiembre-2026.pdf"', $download->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF-', $download->getContent());
    }

    public function test_demo_invoice_opens_inline_and_downloads_as_pdf_only_locally(): void
    {
        $this->app->detectEnvironment(fn () => 'local');
        $finance = User::factory()->create(['role' => 'finance', 'active' => true]);

        $preview = $this->actingAs($finance)->get(route('services.demo-invoice-file'));
        $preview->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('inline; filename="factura-demo-septiembre-2026.pdf"', $preview->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF-', $preview->getContent());

        $download = $this->actingAs($finance)->get(route('services.demo-invoice-file', ['download' => 1]));
        $download->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('attachment; filename="factura-demo-septiembre-2026.pdf"', $download->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF-', $download->getContent());
    }

    public function test_demo_receipt_is_not_available_outside_local_environment(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
        $finance = User::factory()->create(['role' => 'finance', 'active' => true]);

        $this->actingAs($finance)->get(route('services.demo-payment-file'))->assertNotFound();
        $this->actingAs($finance)->get(route('services.demo-invoice-file'))->assertNotFound();
    }

    public function test_attached_invoice_and_payment_open_separately_and_invoice_pdf_downloads_the_original(): void
    {
        Storage::fake('local');
        $finance = User::factory()->create(['role' => 'finance', 'active' => true]);
        $service = $this->service($finance, ['folio' => 'SRV-INVOICE-PDF']);
        $receipt = RecurringServiceReceipt::create([
            'recurring_service_id' => $service->id,
            'due_date' => '2026-09-18',
            'period_start' => '2026-08-01',
            'amount' => 1250,
            'support_file_path' => 'service-supports/invoice.pdf',
            'support_original_name' => 'invoice.pdf',
            'payment_file_path' => 'service-payments/payment.pdf',
            'payment_original_name' => 'payment.pdf',
            'payment_paid_on' => '2026-09-18',
            'paid_by' => $finance->id,
            'status' => 'paid',
        ]);
        Storage::disk('local')->put('service-supports/invoice.pdf', "%PDF-1.4\noriginal-invoice");
        Storage::disk('local')->put('service-payments/payment.pdf', "%PDF-1.4\noriginal-payment");

        $page = $this->actingAs($finance)->get(route('services.history'));
        $page->assertOk()
            ->assertSee(route('services.support-file', $receipt))
            ->assertSee(route('services.support-file.pdf', $receipt))
            ->assertSee(route('services.payment-file', $receipt))
            ->assertSee(route('services.payment-file.pdf', $receipt));
        $this->assertMatchesRegularExpression('/data-filter-value="invoice\.pdf"(?:(?!<\/td>).)*>Ver<\/button>/s', $page->getContent());
        $this->assertMatchesRegularExpression('/data-filter-value="payment\.pdf"(?:(?!<\/td>).)*>Ver<\/button>/s', $page->getContent());

        $invoice = $this->actingAs($finance)->get(route('services.support-file.pdf', $receipt));
        $invoice->assertOk();
        $this->assertStringContainsString('attachment', $invoice->headers->get('Content-Disposition'));
        $this->assertSame("%PDF-1.4\noriginal-invoice", $invoice->streamedContent());
    }

    public function test_attached_invoice_image_is_wrapped_in_a_downloadable_pdf(): void
    {
        Storage::fake('local');
        $finance = User::factory()->create(['role' => 'finance', 'active' => true]);
        $service = $this->service($finance, ['folio' => 'SRV-INVOICE-IMAGE']);
        $receipt = RecurringServiceReceipt::create([
            'recurring_service_id' => $service->id,
            'due_date' => '2026-09-18',
            'period_start' => '2026-08-01',
            'amount' => 1250,
            'support_file_path' => 'service-supports/invoice.png',
            'support_original_name' => 'invoice.png',
        ]);
        $image = imagecreatetruecolor(2, 2);
        ob_start();
        imagepng($image);
        $png = ob_get_clean();
        imagedestroy($image);
        Storage::disk('local')->put('service-supports/invoice.png', $png);

        $response = $this->actingAs($finance)->get(route('services.support-file.pdf', $receipt));
        $response->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('attachment; filename="factura-servicio-'.$receipt->id.'.pdf"', $response->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_paid_pdf_is_downloaded_as_the_original_file(): void
    {
        Storage::fake('local');
        $finance = User::factory()->create(['role' => 'finance', 'active' => true]);
        $service = $this->service($finance, ['folio' => 'SRV-PAID-PDF']);
        $receipt = RecurringServiceReceipt::create([
            'recurring_service_id' => $service->id,
            'due_date' => '2026-09-18',
            'period_start' => '2026-08-01',
            'amount' => 1250,
            'payment_file_path' => 'service-payments/paid.pdf',
            'payment_original_name' => 'paid.pdf',
            'payment_paid_on' => '2026-09-18',
            'paid_by' => $finance->id,
            'status' => 'paid',
        ]);
        Storage::disk('local')->put('service-payments/paid.pdf', "%PDF-1.4\noriginal-receipt");

        $page = $this->actingAs($finance)->get(route('services.history'));
        $page->assertOk()
            ->assertSee(route('services.payment-file.pdf', $receipt))
            ->assertSee('data-filter-value="Sin Archivo">', false)
            ->assertSee('Sin Archivo');

        $response = $this->actingAs($finance)->get(route('services.payment-file.pdf', $receipt));
        $response->assertOk();
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertSame("%PDF-1.4\noriginal-receipt", $response->streamedContent());
    }

    public function test_paid_image_is_wrapped_in_a_downloadable_pdf(): void
    {
        Storage::fake('local');
        $finance = User::factory()->create(['role' => 'finance', 'active' => true]);
        $service = $this->service($finance, ['folio' => 'SRV-PAID-IMAGE']);
        $receipt = RecurringServiceReceipt::create([
            'recurring_service_id' => $service->id,
            'due_date' => '2026-09-18',
            'period_start' => '2026-08-01',
            'amount' => 1250,
            'payment_file_path' => 'service-payments/paid.png',
            'payment_original_name' => 'paid.png',
            'payment_paid_on' => '2026-09-18',
            'paid_by' => $finance->id,
            'status' => 'paid',
        ]);
        $image = imagecreatetruecolor(2, 2);
        ob_start();
        imagepng($image);
        $png = ob_get_clean();
        imagedestroy($image);
        Storage::disk('local')->put('service-payments/paid.png', $png);

        $response = $this->actingAs($finance)->get(route('services.payment-file.pdf', $receipt));
        $response->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('attachment; filename="comprobante-servicio-'.$receipt->id.'.pdf"', $response->headers->get('Content-Disposition'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
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
