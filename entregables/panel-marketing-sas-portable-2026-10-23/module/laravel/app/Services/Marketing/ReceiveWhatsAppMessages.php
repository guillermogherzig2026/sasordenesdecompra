<?php

namespace App\Services\Marketing;

use App\Models\Marketing\Conversation;
use App\Models\Marketing\Lead;
use App\Models\Marketing\Message;
use App\Models\Marketing\WhatsAppNumber;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReceiveWhatsAppMessages
{
    public function receive(array $payload): void
    {
        $entries = $payload['entry'] ?? [];

        foreach (is_array($entries) ? $entries : [] as $entry) {
            if (! is_array($entry) || ! is_scalar($entry['id'] ?? null)) {
                continue;
            }

            $changes = $entry['changes'] ?? [];

            foreach (is_array($changes) ? $changes : [] as $change) {
                if (! is_array($change) || ($change['field'] ?? null) !== 'messages') {
                    continue;
                }

                $value = $change['value'] ?? null;

                if (! is_array($value)) {
                    continue;
                }

                $phoneId = data_get($value, 'metadata.phone_number_id');

                if (! is_scalar($phoneId)) {
                    continue;
                }

                $number = WhatsAppNumber::query()
                    ->where('meta_phone_number_id', (string) $phoneId)
                    ->where('meta_waba_id', (string) $entry['id'])
                    ->where('status', 'active')
                    ->whereHas('company', fn ($query) => $query->where('active', true))
                    ->first();

                if (! $number) {
                    continue;
                }

                $contacts = collect(is_array($value['contacts'] ?? null) ? $value['contacts'] : [])->keyBy('wa_id');
                $messages = $value['messages'] ?? [];

                foreach (is_array($messages) ? $messages : [] as $incoming) {
                    if (! is_array($incoming)) {
                        continue;
                    }

                    $this->storeMessage($number, $incoming, $contacts->get($incoming['from'] ?? ''));
                }
            }
        }
    }

    private function storeMessage(WhatsAppNumber $number, array $incoming, mixed $contact): void
    {
        $messageId = $incoming['id'] ?? null;
        $from = $incoming['from'] ?? null;

        if (! is_string($messageId) || $messageId === '' || strlen($messageId) > 255
            || ! is_string($from) || ! preg_match('/^[0-9]{7,40}$/', $from)) {
            return;
        }

        $name = is_array($contact) ? data_get($contact, 'profile.name') : null;
        $name = is_string($name) && trim($name) !== '' ? mb_substr(trim($name), 0, 160) : $from;
        $sentAt = is_numeric($incoming['timestamp'] ?? null)
            ? Carbon::createFromTimestampUTC((int) $incoming['timestamp'])
            : now();
        $type = is_string($incoming['type'] ?? null) ? $incoming['type'] : 'unknown';
        $type = mb_substr($type, 0, 40);
        $body = $type === 'text' && is_string(data_get($incoming, 'text.body'))
            ? data_get($incoming, 'text.body')
            : "[Mensaje de WhatsApp: {$type}]";

        DB::transaction(function () use ($number, $messageId, $from, $name, $sentAt, $type, $body): void {
            if (Message::query()->where('meta_message_id', $messageId)->exists()) {
                return;
            }

            $lead = Lead::query()->firstOrCreate(
                ['company_id' => $number->company_id, 'phone' => $from],
                ['name' => $name, 'status' => 'nuevo', 'source' => 'WhatsApp']
            );

            $conversation = Conversation::query()->firstOrCreate(
                ['whatsapp_number_id' => $number->id, 'contact_wa_id' => $from],
                [
                    'company_id' => $number->company_id,
                    'lead_id' => $lead->id,
                    'channel' => 'WhatsApp',
                    'status' => 'open',
                ]
            );

            $message = $conversation->messages()->firstOrCreate(
                ['meta_message_id' => $messageId],
                [
                    'sender_type' => 'lead',
                    'direction' => 'in',
                    'message_type' => $type,
                    'body' => $body,
                    'sent_at' => $sentAt,
                ]
            );

            if (! $message->wasRecentlyCreated) {
                return;
            }

            $conversation->increment('unread_count');
            $conversation->update([
                'last_inbound_at' => $conversation->last_inbound_at?->gt($sentAt)
                    ? $conversation->last_inbound_at
                    : $sentAt,
                'last_message_at' => $conversation->last_message_at?->gt($sentAt)
                    ? $conversation->last_message_at
                    : $sentAt,
            ]);
            $lead->update(['last_contact_at' => $sentAt]);
            $number->update(['last_webhook_at' => now()]);
        });
    }
}
