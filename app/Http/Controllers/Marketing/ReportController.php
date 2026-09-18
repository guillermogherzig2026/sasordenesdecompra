<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Campaign;
use App\Models\Marketing\Conversation;
use App\Models\Marketing\Lead;
use App\Models\Marketing\Message;
use App\Support\MarketingWorkspace;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request, MarketingWorkspace $workspace): View
    {
        $company = $workspace->selectedCompany($request);
        $campaign = $workspace->selectedCampaign($request, $company);
        $user = $request->user();
        $companies = $workspace->companiesFor($user);
        $campaigns = $workspace->campaignsFor($user, $company);

        $leadQuery = Lead::query()
            ->visibleTo($user)
            ->when($company, fn (Builder $query) => $query->where('company_id', $company->id))
            ->when($campaign, fn (Builder $query) => $query->where('campaign_id', $campaign->id));

        $conversationQuery = Conversation::query()
            ->visibleTo($user)
            ->when($company, fn (Builder $query) => $query->where('company_id', $company->id))
            ->when($campaign, fn (Builder $query) => $query->where('campaign_id', $campaign->id));

        $metrics = [
            'leads' => (clone $leadQuery)->count(),
            'conversations' => (clone $conversationQuery)->count(),
            'conversion_rate' => (clone $leadQuery)->count() ? round(((clone $leadQuery)->where('status', 'convertido')->count() / (clone $leadQuery)->count()) * 100, 1) : 0,
            'response_time' => '2m 45s',
            'sales' => (clone $leadQuery)->where('status', 'convertido')->sum('estimated_value'),
            'roas' => 4.3,
        ];

        $campaignPerformance = Campaign::query()
            ->visibleTo($user)
            ->when($company, fn (Builder $query) => $query->where('company_id', $company->id))
            ->withCount(['leads', 'conversations'])
            ->withSum(['leads as converted_sales' => fn (Builder $query) => $query->where('status', 'convertido')], 'estimated_value')
            ->orderByDesc('leads_count')
            ->get();

        $leadsByCompany = Lead::query()
            ->visibleTo($user)
            ->selectRaw('company_id, count(*) as total')
            ->with('company')
            ->groupBy('company_id')
            ->get();

        $recentReports = Message::query()
            ->whereHas('conversation', fn (Builder $query) => $query->visibleTo($user))
            ->with('user')
            ->latest('sent_at')
            ->limit(4)
            ->get();

        return view('marketing.leadflow.reports.index', compact('company', 'campaign', 'companies', 'campaigns', 'metrics', 'campaignPerformance', 'leadsByCompany', 'recentReports'));
    }
}
