<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Services\Marketing\ReceiveWhatsAppMessages;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request): Response
    {
        $verifyToken = (string) config('services.whatsapp.verify_token');

        if ($verifyToken === '') {
            return response('Webhook not configured', 503);
        }

        $providedToken = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($request->query('hub_mode') !== 'subscribe'
            || ! is_string($providedToken)
            || ! hash_equals($verifyToken, $providedToken)
            || ! is_string($challenge)
            || $challenge === '') {
            return response('Forbidden', 403);
        }

        return response($challenge, 200)->header('Content-Type', 'text/plain');
    }

    public function receive(Request $request, ReceiveWhatsAppMessages $receiver): Response
    {
        $appSecret = (string) config('services.whatsapp.app_secret');

        if ($appSecret === '') {
            return response('Webhook not configured', 503);
        }

        $rawBody = $request->getContent();

        if (strlen($rawBody) > 1024 * 1024) {
            return response('Payload too large', 413);
        }

        $signature = $request->header('X-Hub-Signature-256');
        $expected = 'sha256='.hash_hmac('sha256', $rawBody, $appSecret);

        if (! is_string($signature) || ! hash_equals($expected, $signature)) {
            return response('Forbidden', 403);
        }

        $payload = json_decode($rawBody, true);

        if (! is_array($payload) || ($payload['object'] ?? null) !== 'whatsapp_business_account') {
            return response('Invalid payload', 400);
        }

        $receiver->receive($payload);

        return response('EVENT_RECEIVED', 200);
    }
}
