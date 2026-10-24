<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Campaign;
use App\Models\Marketing\Conversation;
use App\Models\Marketing\Lead;
use App\Models\Marketing\Message;
use App\Support\MarketingChannelCatalog;
use App\Support\MarketingWorkspace;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, MarketingWorkspace $workspace): View
    {
        $user = $request->user();
        $company = $workspace->selectedCompany($request);
        $campaign = $workspace->selectedCampaign($request, $company);
        $companies = $workspace->companiesFor($user);
        $campaigns = $workspace->campaignsFor($user, $company);
        $dashboardView = $request->query('view') === 'summary' ? 'summary' : 'channels';
        $scopeParameters = ['company_id' => $company?->id ?? 'all', 'campaign_id' => $campaign?->id ?? 'all'];
        $whatsappNumbers = $company?->whatsappNumbers ?? collect();
        $configuredNumbers = $whatsappNumbers->filter(fn ($number) => $number->status === 'active' && $number->hasCloudConfiguration());
        $whatsappConfiguredCount = $configuredNumbers->count();
        $whatsappReceivedCount = $configuredNumbers->whereNotNull('last_webhook_at')->count();
        $channels = MarketingChannelCatalog::all();
        $channelConfigurations = MarketingChannelCatalog::configurationsFor($company);
        $configuredChannelKeys = MarketingChannelCatalog::configuredKeysFor($company);
        $configuredChannelCount = count($configuredChannelKeys);
        $configuredNetworks = collect($channels)
            ->whereIn('key', $configuredChannelKeys)
            ->map(fn (array $channel) => $channel['key'] === 'other' ? $channelConfigurations['other']['network'] : $channel['network'])
            ->all();
        $linkedCampaignCount = $campaigns->filter(fn (Campaign $item) => count(array_intersect($item->social_networks ?? [], $configuredNetworks)) > 0)->count();
        $storedCloudNumber = $whatsappNumbers->first(fn ($number) => $number->hasCloudConfiguration())
            ?? $whatsappNumbers->firstWhere('status', 'pending_meta');
        $whatsappHasSavedToken = filled($storedCloudNumber?->meta_access_token);
        $cloudNumber = $storedCloudNumber ? clone $storedCloudNumber : null;
        if ($cloudNumber) {
            unset($cloudNumber->meta_access_token);
        }

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
            'active_conversations' => (clone $conversationQuery)->where('status', 'open')->count(),
            'unassigned' => (clone $conversationQuery)->whereNull('assigned_user_id')->count(),
            'converted' => (clone $leadQuery)->where('status', 'convertido')->count(),
            'estimated_sales' => (clone $leadQuery)->where('status', 'convertido')->sum('estimated_value'),
        ];

        $leadStatusCounts = (clone $leadQuery)->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        $recentConversations = (clone $conversationQuery)
            ->with(['lead', 'campaign', 'company', 'assignedUser', 'latestMessage'])
            ->latest('last_message_at')
            ->limit(6)
            ->get();

        $topCampaigns = Campaign::query()
            ->visibleTo($user)
            ->when($company, fn (Builder $query) => $query->where('company_id', $company->id))
            ->when($campaign, fn (Builder $query) => $query->whereKey($campaign->id))
            ->withCount('leads')
            ->withSum('leads', 'estimated_value')
            ->orderByDesc('leads_count')
            ->limit(5)
            ->get();

        $activity = Message::query()
            ->whereHas('conversation', function (Builder $query) use ($user, $company, $campaign): void {
                $query->visibleTo($user)
                    ->when($company, fn (Builder $builder) => $builder->where('company_id', $company->id))
                    ->when($campaign, fn (Builder $builder) => $builder->where('campaign_id', $campaign->id));
            })
            ->with(['conversation.lead', 'user'])
            ->latest('sent_at')
            ->limit(5)
            ->get();

        return view('marketing.leadflow.dashboard', compact(
            'company',
            'campaign',
            'companies',
            'campaigns',
            'metrics',
            'recentConversations',
            'topCampaigns',
            'activity',
            'dashboardView',
            'scopeParameters',
            'whatsappConfiguredCount',
            'whatsappReceivedCount',
            'whatsappHasSavedToken',
            'channels',
            'channelConfigurations',
            'configuredChannelKeys',
            'configuredChannelCount',
            'linkedCampaignCount',
            'cloudNumber',
            'leadStatusCounts'
        ));
    }
}
