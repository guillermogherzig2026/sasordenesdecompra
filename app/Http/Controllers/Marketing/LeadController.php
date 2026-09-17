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

class LeadController extends Controller
{
    public function index(Request $request, MarketingWorkspace $workspace): View
    {
        $company = $workspace->selectedCompany($request);
        $campaign = $workspace->selectedCampaign($request, $company);

        $leads = Lead::query()
            ->visibleTo($request->user())
            ->with(['company', 'campaign', 'advisor'])
            ->when($company, fn (Builder $query) => $query->where('company_id', $company->id))
            ->when($campaign, fn (Builder $query) => $query->where('campaign_id', $campaign->id))
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = (string) $request->string('search');
                $query->where(function (Builder $builder) use ($search): void {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', (string) $request->string('status')))
            ->when($request->filled('advisor_id'), fn (Builder $query) => $query->where('advisor_id', $request->integer('advisor_id')))
            ->when($request->filled('product'), fn (Builder $query) => $query->where('product', (string) $request->string('product')))
            ->latest('last_contact_at')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'new' => Lead::query()->visibleTo($request->user())->when($company, fn (Builder $query) => $query->where('company_id', $company->id))->where('status', 'nuevo')->count(),
            'qualified' => Lead::query()->visibleTo($request->user())->when($company, fn (Builder $query) => $query->where('company_id', $company->id))->where('status', 'calificado')->count(),
            'follow_up' => Lead::query()->visibleTo($request->user())->when($company, fn (Builder $query) => $query->where('company_id', $company->id))->where('status', 'seguimiento')->count(),
            'conversion' => Lead::query()->visibleTo($request->user())->when($company, fn (Builder $query) => $query->where('company_id', $company->id))->where('status', 'convertido')->count(),
        ];

        $selectedLead = $leads->first();
        $companies = $workspace->companiesFor($request->user());
        $campaigns = $workspace->campaignsFor($request->user(), $company);
        $advisors = User::query()
            ->visibleTo($request->user())
            ->when($company, fn (Builder $query) => $query->where('company_id', $company->id))
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return view('marketing.leadflow.leads.index', compact('leads', 'stats', 'selectedLead', 'company', 'campaign', 'companies', 'campaigns', 'advisors'));
    }

    public function create(Request $request): View
    {
        return view('marketing.leadflow.leads.form', $this->formData($request) + ['lead' => new Lead]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data = $this->withDefaults($data);
        $company = Company::query()->visibleTo($request->user())->findOrFail($data['company_id']);
        $this->ensureRelationsBelongToCompany($data, $company);

        $lead = Lead::create($data);
        $lead->conversations()->create([
            'company_id' => $lead->company_id,
            'campaign_id' => $lead->campaign_id,
            'assigned_user_id' => $lead->advisor_id,
            'last_message_at' => $lead->last_contact_at ?: now(),
        ]);

        return redirect()->route('marketing.leads.index', ['company_id' => $lead->company_id, 'campaign_id' => $lead->campaign_id])->with('status', 'Lead creado.');
    }

    public function edit(Request $request, Lead $lead): View
    {
        return view('marketing.leadflow.leads.form', $this->formData($request) + compact('lead'));
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $data = $this->withDefaults($this->validatedData($request));
        $company = Company::query()->visibleTo($request->user())->findOrFail($data['company_id']);
        $this->ensureRelationsBelongToCompany($data, $company);
        $lead->update($data);
        $lead->conversation?->update([
            'company_id' => $lead->company_id,
            'campaign_id' => $lead->campaign_id,
            'assigned_user_id' => $lead->advisor_id,
        ]);

        return redirect()->route('marketing.leads.index', ['company_id' => $lead->company_id, 'campaign_id' => $lead->campaign_id])->with('status', 'Lead actualizado.');
    }

    public function destroy(Request $request, Lead $lead): RedirectResponse
    {
        $lead->delete();

        return back()->with('status', 'Lead eliminado.');
    }

    private function formData(Request $request): array
    {
        $companyId = $request->session()->get('marketing_company_id');

        return [
            'companies' => Company::query()->visibleTo($request->user())->orderBy('name')->get(),
            'campaigns' => Campaign::query()->visibleTo($request->user())->when($companyId, fn (Builder $query) => $query->where('company_id', $companyId))->orderBy('name')->get(),
            'advisors' => User::query()
                ->visibleTo($request->user())
                ->when($companyId, fn (Builder $query) => $query->where('company_id', $companyId))
                ->where('active', true)
                ->orderBy('name')
                ->get(),
        ];
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'company_id' => ['required', 'exists:marketing_companies,id'],
            'campaign_id' => ['nullable', 'exists:marketing_campaigns,id'],
            'advisor_id' => ['nullable', 'exists:marketing_users,id'],
            'name' => ['required', 'string', 'max:160'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
            'product' => ['nullable', 'string', 'max:160'],
            'status' => ['required', 'in:'.implode(',', array_keys(Lead::STATUSES))],
            'estimated_value' => ['nullable', 'numeric', 'min:0'],
            'last_contact_at' => ['nullable', 'date'],
            'next_follow_up_at' => ['nullable', 'date'],
            'source' => ['nullable', 'string', 'max:120'],
            'position' => ['nullable', 'string', 'max:160'],
            'tags' => ['nullable', 'array'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function withDefaults(array $data): array
    {
        $data['estimated_value'] = $data['estimated_value'] ?? 0;
        $data['source'] = $data['source'] ?? 'WhatsApp';

        return $data;
    }

    private function ensureRelationsBelongToCompany(array $data, Company $company): void
    {
        if (! empty($data['campaign_id'])) {
            Campaign::query()
                ->where('company_id', $company->id)
                ->whereKey($data['campaign_id'])
                ->firstOrFail();
        }

        if (! empty($data['advisor_id'])) {
            User::query()
                ->where('company_id', $company->id)
                ->whereKey($data['advisor_id'])
                ->firstOrFail();
        }
    }
}
