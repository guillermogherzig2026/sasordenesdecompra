<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Campaign;
use App\Models\Marketing\Company;
use App\Models\Marketing\Lead;
use App\Models\Marketing\User;
use App\Support\MarketingWorkspace;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CampaignController extends Controller
{
    public function index(Request $request, MarketingWorkspace $workspace): View
    {
        $company = $workspace->selectedCompany($request);
        $campaign = $workspace->selectedCampaign($request, $company);
        $companies = $workspace->companiesFor($request->user());
        $campaigns = $workspace->campaignsFor($request->user(), $company);

        $summary = [
            'budget' => $campaign ? $campaign->budget : $campaigns->sum('budget'),
            'spent' => $campaign ? $campaign->spent : $campaigns->sum('spent'),
            'conversations' => $campaign ? $campaign->conversations_count : $campaigns->sum('conversations_count'),
            'leads' => $campaign ? $campaign->leads_count : $campaigns->sum('leads_count'),
            'sales' => Lead::query()->visibleTo($request->user())
                ->when($company, fn (Builder $query) => $query->where('company_id', $company->id))
                ->when($campaign, fn (Builder $query) => $query->where('campaign_id', $campaign->id))
                ->where('status', 'convertido')->sum('estimated_value'),
        ];

        return view('marketing.leadflow.campaigns.index', compact('company', 'campaign', 'companies', 'campaigns', 'summary'));
    }

    public function create(Request $request, MarketingWorkspace $workspace): View
    {
        $company = $workspace->selectedCompany($request);
        $companies = Company::query()->visibleTo($request->user())->orderBy('name')->get();
        $users = User::query()
            ->visibleTo($request->user())
            ->when($company, fn (Builder $query) => $query->where('company_id', $company->id))
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return view('marketing.leadflow.campaigns.form', ['campaign' => new Campaign(['company_id' => $company?->id]), 'companies' => $companies, 'users' => $users]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data = $this->withDefaults($data);
        $company = Company::query()->visibleTo($request->user())->findOrFail($data['company_id']);
        $this->ensureResponsibleBelongsToCompany($data, $company);

        $campaign = $company->campaigns()->create($data);

        return redirect()->route('marketing.campaigns.index', ['company_id' => $company->id, 'campaign_id' => $campaign->id])->with('status', 'Campana creada.');
    }

    public function edit(Request $request, Campaign $campaign): View
    {
        $companies = Company::query()->visibleTo($request->user())->orderBy('name')->get();
        $users = User::query()
            ->visibleTo($request->user())
            ->where('company_id', $campaign->company_id)
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return view('marketing.leadflow.campaigns.form', compact('campaign', 'companies', 'users'));
    }

    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data = $this->withDefaults($data);
        $company = Company::query()->visibleTo($request->user())->findOrFail($data['company_id']);
        $this->ensureResponsibleBelongsToCompany($data, $company);
        $campaign->update($data);

        return redirect()->route('marketing.campaigns.index', ['company_id' => $campaign->company_id, 'campaign_id' => $campaign->id])->with('status', 'Campana actualizada.');
    }

    public function toggle(Request $request, Campaign $campaign): RedirectResponse
    {
        $campaign->update(['status' => $campaign->status === 'active' ? 'paused' : 'active']);

        return back()->with('status', 'Estado de campana actualizado.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'company_id' => ['required', 'exists:marketing_companies,id'],
            'responsible_user_id' => ['nullable', 'exists:marketing_users,id'],
            'name' => ['required', 'string', 'max:160'],
            'status' => ['required', 'in:active,paused,ended'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'spent' => ['nullable', 'numeric', 'min:0'],
            'product' => ['nullable', 'string', 'max:160'],
            'source' => ['nullable', 'string', 'max:120'],
            'social_networks' => ['nullable', 'array'],
            'social_networks.*' => ['string', 'max:80'],
            'description' => ['nullable', 'string'],
        ]);
    }

    private function withDefaults(array $data): array
    {
        $data['budget'] = $data['budget'] ?? 0;
        $data['spent'] = $data['spent'] ?? 0;
        $data['social_networks'] = array_values(array_filter($data['social_networks'] ?? []));
        $data['source'] = $data['social_networks'] ? implode(', ', $data['social_networks']) : ($data['source'] ?? 'Redes sociales');

        return $data;
    }

    private function ensureResponsibleBelongsToCompany(array $data, Company $company): void
    {
        if (! empty($data['responsible_user_id'])) {
            User::query()
                ->where('company_id', $company->id)
                ->whereKey($data['responsible_user_id'])
                ->firstOrFail();
        }
    }
}
