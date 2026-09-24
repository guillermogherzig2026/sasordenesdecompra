<?php

namespace App\Support;

use App\Models\Marketing\Campaign;
use App\Models\Marketing\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class MarketingWorkspace
{
    public function companiesFor(User $user): Collection
    {
        return Company::query()
            ->visibleTo($user)
            ->withCount(['campaigns', 'leads', 'conversations'])
            ->orderByDesc('active')
            ->orderBy('name')
            ->get();
    }

    public function selectedCompany(Request $request): ?Company
    {
        /** @var User $user */
        $user = $request->user();
        $companyParam = $request->query('company_id', $request->query('company'));

        if ($companyParam === 'all') {
            $request->session()->put('marketing_company_scope', 'all');
            $request->session()->forget('marketing_campaign_id');

            return null;
        }

        if ($companyParam !== null && $companyParam !== '') {
            $request->session()->forget('marketing_company_scope');
        } elseif ($request->session()->get('marketing_company_scope') === 'all') {
            return null;
        }

        $companyId = $companyParam ?: $request->session()->get('marketing_company_id');

        $query = Company::query()->visibleTo($user)->withCount(['campaigns', 'leads', 'conversations']);
        $company = $companyId ? (clone $query)->find($companyId) : null;
        $company ??= $query->first();

        if ($company) {
            $request->session()->put('marketing_company_id', $company->id);
        }

        return $company;
    }

    public function campaignsFor(User $user, ?Company $company): Collection
    {
        return Campaign::query()
            ->visibleTo($user)
            ->when($company, fn (Builder $query) => $query->where('company_id', $company->id))
            ->withCount(['leads', 'conversations'])
            ->withSum('leads', 'estimated_value')
            ->with(['responsible', 'company'])
            ->orderByRaw("CASE status WHEN 'active' THEN 1 WHEN 'paused' THEN 2 ELSE 3 END")
            ->orderBy('name')
            ->get();
    }

    public function selectedCampaign(Request $request, ?Company $company): ?Campaign
    {
        if (! $company) {
            return null;
        }

        /** @var User $user */
        $user = $request->user();
        $campaignParam = $request->query('campaign_id') ?: $request->query('campaign');

        if ($campaignParam === 'all') {
            $request->session()->forget('marketing_campaign_id');

            return null;
        }

        $campaignId = $campaignParam ?: $request->session()->get('marketing_campaign_id');
        $query = Campaign::query()
            ->visibleTo($user)
            ->where('company_id', $company->id)
            ->withCount(['leads', 'conversations']);

        $campaign = $campaignId ? (clone $query)->find($campaignId) : null;

        if ($campaign) {
            $request->session()->put('marketing_campaign_id', $campaign->id);
        }

        return $campaign;
    }
}
