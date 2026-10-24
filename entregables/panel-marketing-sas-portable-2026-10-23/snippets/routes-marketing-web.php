<?php

use App\Http\Controllers\Marketing\CampaignController as MarketingCampaignController;
use App\Http\Controllers\Marketing\ChannelConfigurationController as MarketingChannelConfigurationController;
use App\Http\Controllers\Marketing\CompanyController as MarketingCompanyController;
use App\Http\Controllers\Marketing\ConversationController as MarketingConversationController;
use App\Http\Controllers\Marketing\DashboardController as MarketingDashboardController;
use App\Http\Controllers\Marketing\LeadController as MarketingLeadController;
use App\Http\Controllers\Marketing\ReportController as MarketingReportController;
use App\Http\Controllers\Marketing\SettingsController as MarketingSettingsController;
use App\Http\Controllers\Marketing\UserController as MarketingUserController;
use App\Http\Controllers\Marketing\WhatsAppConnectionController as MarketingWhatsAppConnectionController;
use App\Http\Middleware\EnsureMarketingPermission;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', EnsureMarketingPermission::class])
    ->prefix('marketing')
    ->name('marketing.')
    ->group(function (): void {
        Route::get('/', [MarketingDashboardController::class, 'index'])->name('index');
        Route::resource('companies', MarketingCompanyController::class)->except(['show', 'destroy']);
        Route::patch('companies/{company}/estado', [MarketingCompanyController::class, 'toggle'])->name('companies.toggle');
        Route::patch('companies/{company}/favorita', [MarketingCompanyController::class, 'updateFavorite'])->name('companies.favorite');
        Route::resource('campaigns', MarketingCampaignController::class)->except(['show', 'destroy']);
        Route::post('campaigns/canales/vincular', [MarketingChannelConfigurationController::class, 'updateCampaign'])->name('campaigns.channels.update');
        Route::patch('campaigns/{campaign}/estado', [MarketingCampaignController::class, 'toggle'])->name('campaigns.toggle');
        Route::resource('leads', MarketingLeadController::class)->except(['show']);
        Route::get('conversaciones', [MarketingConversationController::class, 'index'])->name('conversations.index');
        Route::post('conversaciones/whatsapp', [MarketingWhatsAppConnectionController::class, 'store'])->name('conversations.whatsapp.store');
        Route::post('conversaciones/{conversation}/mensajes', [MarketingConversationController::class, 'storeMessage'])->name('conversations.messages.store');
        Route::post('conversaciones/{conversation}/notas', [MarketingConversationController::class, 'storeNote'])->name('conversations.notes.store');
        Route::patch('conversaciones/{conversation}/asignar', [MarketingConversationController::class, 'assign'])->name('conversations.assign');
        Route::get('reportes', [MarketingReportController::class, 'index'])->name('reports.index');
        Route::resource('users', MarketingUserController::class)->except(['show', 'destroy']);
        Route::patch('users/{user}/estado', [MarketingUserController::class, 'toggle'])->name('users.toggle');
        Route::get('configuracion', [MarketingSettingsController::class, 'index'])->name('settings.index');
        Route::post('configuracion/canales/{channel}', [MarketingChannelConfigurationController::class, 'update'])->name('settings.channels.update');
        Route::patch('configuracion/empresa', [MarketingSettingsController::class, 'updateCompany'])->name('settings.company.update');
        Route::post('configuracion/whatsapp', [MarketingSettingsController::class, 'storeWhatsAppNumber'])->name('settings.whatsapp.store');
        Route::post('configuracion/etiquetas', [MarketingSettingsController::class, 'storeTag'])->name('settings.tags.store');
        Route::post('configuracion/automatizaciones', [MarketingSettingsController::class, 'storeAutomation'])->name('settings.automations.store');
        Route::patch('configuracion/horarios', [MarketingSettingsController::class, 'updateOfficeHours'])->name('settings.hours.update');
    });
