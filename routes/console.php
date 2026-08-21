<?php

use App\Services\ConstructionPayrollScheduleService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('construction:generate-weekly-payroll-orders {--date=}', function () {
    $date = $this->option('date')
        ? CarbonImmutable::parse($this->option('date'), ConstructionPayrollScheduleService::TIMEZONE)
        : CarbonImmutable::now(ConstructionPayrollScheduleService::TIMEZONE);
    $created = app(ConstructionPayrollScheduleService::class)->generateWeeklyDueOccurrences($date);

    $this->info("{$created} ordenes de pago semanales generadas.");
})->purpose('Genera las OP semanales disponibles cada domingo y con vencimiento el viernes.');

Artisan::command('construction:generate-payroll-orders {--date=}', function () {
    $date = $this->option('date')
        ? CarbonImmutable::parse($this->option('date'), ConstructionPayrollScheduleService::TIMEZONE)
        : CarbonImmutable::now(ConstructionPayrollScheduleService::TIMEZONE);
    $created = app(ConstructionPayrollScheduleService::class)->generateDueOccurrences($date);

    $this->info("{$created} ordenes de pago recurrentes generadas.");
})->purpose('Genera las OP recurrentes que deben incorporarse a pagos vigentes.');

Schedule::command('construction:generate-payroll-orders')
    ->daily()
    ->at('00:05')
    ->timezone(ConstructionPayrollScheduleService::TIMEZONE)
    ->withoutOverlapping();
