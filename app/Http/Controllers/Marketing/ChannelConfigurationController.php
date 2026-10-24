<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Campaign;
use App\Models\Marketing\Company;
use App\Support\MarketingChannelCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ChannelConfigurationController extends Controller
{
    public function update(Request $request, string $channel): RedirectResponse
    {
        abort_unless($request->user()?->canNavigateTo('marketing.settings'), 403);

        $catalog = collect(MarketingChannelCatalog::all())->keyBy('key');
        $request->merge(['channel' => $channel]);
        $data = $request->validateWithBag('channelConfiguration', [
            'channel' => ['required', Rule::in($catalog->keys()->reject(fn (string $key) => $key === 'whatsapp')->all())],
            'company_id' => ['required', 'integer', 'exists:marketing_companies,id'],
            'account_name' => ['required', 'string', 'max:160'],
            'account_identifier' => ['required', 'string', 'max:255'],
            'profile_url' => ['nullable', 'string', 'max:2048', 'url:http,https'],
            'network' => $channel === 'other' ? ['required', Rule::in(MarketingChannelCatalog::OTHER_NETWORKS)] : ['nullable', 'string'],
        ]);

        $company = DB::transaction(function () use ($request, $data, $channel, $catalog): Company {
            $company = Company::query()->visibleTo($request->user())->lockForUpdate()->findOrFail($data['company_id']);
            $settings = is_array($company->settings) ? $company->settings : [];
            $configurations = is_array($settings['marketing_channels'] ?? null) ? $settings['marketing_channels'] : [];
            $configurations[$channel] = [
                'account_name' => $data['account_name'],
                'account_identifier' => $data['account_identifier'],
                'profile_url' => $data['profile_url'] ?? null,
                'network' => $channel === 'other' ? $data['network'] : $catalog[$channel]['network'],
            ];
            $settings['marketing_channels'] = $configurations;
            $company->update(['settings' => $settings]);

            return $company;
        });

        $requestedCampaignId = filter_var($request->input('campaign_id'), FILTER_VALIDATE_INT);
        $campaignId = Campaign::query()->visibleTo($request->user())
            ->where('company_id', $company->id)
            ->whereKey($requestedCampaignId ?: null)
            ->value('id');

        return redirect()->route('marketing.index', [
            'company_id' => $company->id,
            'campaign_id' => $campaignId ?? 'all',
            'view' => 'channels',
        ])->with('status', 'Cuenta registrada. Ya puedes asignar este canal a las campañas de la empresa.');
    }

    public function updateCampaign(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canNavigateTo('marketing.campaigns'), 403);

        $catalog = collect(MarketingChannelCatalog::all())->keyBy('key');
        $data = $request->validateWithBag('campaignChannels', [
            'company_id' => ['required', 'integer', 'exists:marketing_companies,id'],
            'campaign_id' => ['required', 'integer', Rule::exists('marketing_campaigns', 'id')->where('company_id', $request->input('company_id'))],
            'channels' => ['required', 'array', 'min:1', 'max:8'],
            'channels.*' => ['required', 'string', 'distinct', Rule::in($catalog->keys()->all())],
        ]);

        DB::transaction(function () use ($request, $data, $catalog): void {
            $company = Company::query()->visibleTo($request->user())->lockForUpdate()->findOrFail($data['company_id']);
            $campaign = Campaign::query()->visibleTo($request->user())
                ->where('company_id', $company->id)
                ->lockForUpdate()->findOrFail($data['campaign_id']);
            $configuredKeys = MarketingChannelCatalog::configuredKeysFor($company);

            if (array_diff($data['channels'], $configuredKeys)) {
                throw ValidationException::withMessages([
                    'channels' => 'Configura cada canal en esta empresa antes de vincularlo a una campaña.',
                ])->errorBag('campaignChannels');
            }

            $configurations = MarketingChannelCatalog::configurationsFor($company);
            $networks = collect($campaign->social_networks ?? [])
                ->filter(fn ($network) => is_string($network) && filled($network))
                ->map(fn (string $network) => trim($network));

            foreach ($data['channels'] as $channel) {
                $networks->push($channel === 'other' ? $configurations[$channel]['network'] : $catalog[$channel]['network']);
            }

            $networks = $networks->unique()->values()->all();
            $campaign->update(['social_networks' => $networks, 'source' => implode(', ', $networks)]);
        });

        return redirect()->route('marketing.index', [
            'company_id' => $data['company_id'],
            'campaign_id' => $data['campaign_id'],
            'view' => 'channels',
        ])->with('status', 'Canales vinculados a la campaña.');
    }
}
