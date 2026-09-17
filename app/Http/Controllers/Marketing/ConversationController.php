<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Conversation;
use App\Models\Marketing\User;
use App\Support\MarketingWorkspace;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(Request $request, MarketingWorkspace $workspace): View
    {
        $company = $workspace->selectedCompany($request);
        $campaign = $workspace->selectedCampaign($request, $company);
        $companies = $workspace->companiesFor($request->user());
        $campaigns = $workspace->campaignsFor($request->user(), $company);

        $baseQuery = Conversation::query()
            ->visibleTo($request->user())
            ->with(['lead', 'campaign', 'company', 'assignedUser', 'latestMessage'])
            ->when($company, fn (Builder $query) => $query->where('company_id', $company->id))
            ->when($campaign, fn (Builder $query) => $query->where('campaign_id', $campaign->id));

        $conversations = (clone $baseQuery)
            ->when($request->query('filter') === 'unread', fn (Builder $query) => $query->where('unread_count', '>', 0))
            ->when($request->query('filter') === 'unassigned', fn (Builder $query) => $query->whereNull('assigned_user_id'))
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = (string) $request->string('search');
                $query->whereHas('lead', function (Builder $leadQuery) use ($search): void {
                    $leadQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest('last_message_at')
            ->get();

        $selected = $request->integer('conversation')
            ? (clone $baseQuery)->with(['messages.user', 'lead.advisor', 'lead.campaign', 'company'])->find($request->integer('conversation'))
            : null;

        $selected ??= $conversations->first()?->load(['messages.user', 'lead.advisor', 'lead.campaign', 'company']);

        if ($selected) {
            $selected->update(['unread_count' => 0]);
        }

        $stats = [
            'active' => (clone $baseQuery)->where('status', 'open')->count(),
            'unassigned' => (clone $baseQuery)->whereNull('assigned_user_id')->count(),
            'waiting' => (clone $baseQuery)->where('unread_count', '>', 0)->count(),
            'response_time' => '2m 45s',
        ];

        $advisors = User::query()
            ->visibleTo($request->user())
            ->when($company, fn (Builder $query) => $query->where('company_id', $company->id))
            ->where('active', true)
            ->whereIn('role', ['administrador', 'supervisor', 'asesor'])
            ->orderBy('name')
            ->get();

        return view('marketing.leadflow.conversations.index', compact('company', 'campaign', 'companies', 'campaigns', 'conversations', 'selected', 'stats', 'advisors'));
    }

    public function storeMessage(Request $request, Conversation $conversation): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required_without:file', 'nullable', 'string'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        $filePath = null;
        $fileName = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('conversation-files', 'public');
            $fileName = $file->getClientOriginalName();
        }

        $author = $this->messageAuthor($request, $conversation);

        $conversation->messages()->create([
            'user_id' => $author->id,
            'sender_type' => 'user',
            'direction' => 'out',
            'message_type' => $filePath ? 'file' : 'text',
            'body' => $data['body'] ?? null,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'sent_at' => now(),
        ]);

        $conversation->update([
            'assigned_user_id' => $conversation->assigned_user_id ?: $author->id,
            'last_message_at' => now(),
        ]);

        $conversation->lead->update(['last_contact_at' => now()]);

        return redirect()->route('marketing.conversations.index', ['conversation' => $conversation->id])->with('status', 'Mensaje guardado.');
    }

    public function storeNote(Request $request, Conversation $conversation): RedirectResponse
    {
        $data = $request->validate(['note' => ['required', 'string', 'max:2000']]);
        $author = $this->messageAuthor($request, $conversation);

        $conversation->messages()->create([
            'user_id' => $author->id,
            'sender_type' => 'system',
            'direction' => 'internal',
            'message_type' => 'note',
            'body' => $data['note'],
            'sent_at' => now(),
        ]);

        return redirect()->route('marketing.conversations.index', ['conversation' => $conversation->id])->with('status', 'Nota agregada.');
    }

    public function assign(Request $request, Conversation $conversation): RedirectResponse
    {
        $data = $request->validate(['assigned_user_id' => ['nullable', 'exists:marketing_users,id']]);

        $advisorId = $data['assigned_user_id'] ?? null;

        if ($advisorId) {
            User::query()
                ->where('company_id', $conversation->company_id)
                ->findOrFail($advisorId);
        }

        $conversation->update(['assigned_user_id' => $advisorId]);
        $conversation->lead->update(['advisor_id' => $advisorId]);

        return back()->with('status', 'Conversacion asignada.');
    }

    private function messageAuthor(Request $request, Conversation $conversation): User
    {
        $systemUser = $request->user();
        $identity = sprintf('system-%d-company-%d@marketing.local', $systemUser->id, $conversation->company_id);

        return User::query()->firstOrCreate(
            ['email' => $identity],
            [
                'company_id' => $conversation->company_id,
                'name' => $systemUser->name,
                'password' => str()->password(32),
                'role' => 'supervisor',
                'timezone' => config('app.timezone', 'America/Mexico_City'),
                'active' => true,
            ]
        );
    }
}
