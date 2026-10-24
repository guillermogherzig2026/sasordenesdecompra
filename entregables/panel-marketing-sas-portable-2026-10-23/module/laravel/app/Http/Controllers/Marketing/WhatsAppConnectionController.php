<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WhatsAppConnectionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->role === 'superadmin', 403);

        $company = Company::query()->findOrFail($request->input('company_id'));
        $number = $company->whatsappNumbers()->whereNotNull('meta_phone_number_id')->first()
            ?? $company->whatsappNumbers()->where('status', 'pending_meta')->first();

        $data = $request->validate([
            'company_id' => ['required', 'exists:marketing_companies,id'],
            'display_name' => ['nullable', 'string', 'max:120'],
            'phone_number' => ['required', 'string', 'max:40'],
            'meta_phone_number_id' => [
                'required', 'regex:/^[0-9]{5,40}$/',
                Rule::unique('marketing_whatsapp_numbers', 'meta_phone_number_id')->ignore($number?->id),
            ],
            'meta_waba_id' => ['required', 'regex:/^[0-9]{5,40}$/'],
            'meta_access_token' => [$number?->meta_access_token ? 'nullable' : 'required', 'string', 'max:2048'],
        ]);

        $attributes = [
            'display_name' => ($data['display_name'] ?? null) ?: 'WhatsApp',
            'phone_number' => $data['phone_number'],
            'provider_phone_id' => $data['meta_phone_number_id'],
            'meta_phone_number_id' => $data['meta_phone_number_id'],
            'meta_waba_id' => $data['meta_waba_id'],
            'meta_configured_at' => now(),
            'status' => 'active',
        ];

        if (! empty($data['meta_access_token'])) {
            $attributes['meta_access_token'] = $data['meta_access_token'];
        }

        if ($number) {
            $number->update($attributes);
        } else {
            $company->whatsappNumbers()->create($attributes);
        }

        $destination = $request->input('return_to') === 'dashboard' ? 'marketing.index' : 'marketing.conversations.index';
        $parameters = ['company_id' => $company->id];
        if ($destination === 'marketing.index') {
            $parameters['view'] = 'channels';
            $campaignId = filter_var($request->input('campaign_id'), FILTER_VALIDATE_INT);
            $parameters['campaign_id'] = $company->campaigns()->whereKey($campaignId ?: null)->value('id') ?? 'all';
        }

        return redirect()->route($destination, $parameters)
            ->with('status', 'Numero configurado. Espera el primer webhook firmado para confirmar la recepcion.');
    }
}
