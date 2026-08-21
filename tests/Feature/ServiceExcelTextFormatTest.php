<?php

namespace Tests\Feature;

use App\Http\Controllers\ReportController;
use ReflectionMethod;
use Tests\TestCase;

class ServiceExcelTextFormatTest extends TestCase
{
    public function test_service_numbers_are_exported_as_text(): void
    {
        $method = new ReflectionMethod(ReportController::class, 'excel');
        $method->setAccessible(true);

        $response = $method->invoke(new ReportController(), 'services.xls', [[
            'Numero Servicio' => '014752100000',
            'Monto' => '100.00',
        ]]);

        ob_start();
        $response->sendContent();
        $content = (string) ob_get_clean();

        $this->assertStringContainsString(
            '<td style="mso-number-format:\'\\@\';">014752100000</td>',
            $content
        );
        $this->assertStringContainsString('<td>100.00</td>', $content);
    }
}
