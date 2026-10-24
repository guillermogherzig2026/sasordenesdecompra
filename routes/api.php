<?php

use App\Http\Controllers\Marketing\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('webhooks/whatsapp', [WhatsAppWebhookController::class, 'verify']);
Route::post('webhooks/whatsapp', [WhatsAppWebhookController::class, 'receive']);
