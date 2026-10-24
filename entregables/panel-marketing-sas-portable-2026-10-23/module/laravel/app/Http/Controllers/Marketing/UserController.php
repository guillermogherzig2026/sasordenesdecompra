<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Campaign;
use App\Models\Marketing\Company;
use App\Models\Marketing\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->visibleTo($request->user())
            ->with(['company', 'campaigns'])
            ->withCount(['campaigns', 'assignedConversations'])
            ->when($request->filled('company_id'), fn (Builder $query) => $query->where('company_id', $request->integer('company_id')))
            ->when($request->filled('role'), fn (Builder $query) => $query->where('role', (string) $request->string('role')))
            ->orderByDesc('active')
            ->orderBy('name')
            ->paginate(8)
            ->withQueryString();

        $selected = $users->first();
        $companies = Company::query()->visibleTo($request->user())->orderBy('name')->get();
        $activeUsersCount = User::query()
            ->visibleTo($request->user())
            ->where('active', true)
            ->when($request->filled('company_id'), fn (Builder $query) => $query->where('company_id', $request->integer('company_id')))
            ->when($request->filled('role'), fn (Builder $query) => $query->where('role', (string) $request->string('role')))
            ->count();

        return view('marketing.leadflow.users.index', compact('users', 'selected', 'companies', 'activeUsersCount'));
    }

    public function create(Request $request): View
    {
        return view('marketing.leadflow.users.form', $this->formData($request) + ['userModel' => new User]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        unset($data['campaign_ids']);

        $data['password'] = Hash::make($data['password']);
        $data['timezone'] = $data['timezone'] ?: 'America/Mexico_City';
        $data['active'] = $request->boolean('active');

        $user = User::create($data);
        $this->syncCampaigns($request, $user);

        return redirect()->route('marketing.users.index')->with('status', 'Usuario creado.');
    }

    public function edit(Request $request, User $user): View
    {
        return view('marketing.leadflow.users.form', $this->formData($request, $user) + ['userModel' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validatedData($request, $user);
        unset($data['campaign_ids']);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $data['active'] = $request->boolean('active');
        $data['timezone'] = $data['timezone'] ?: 'America/Mexico_City';
        $user->update($data);
        $this->syncCampaigns($request, $user);

        return redirect()->route('marketing.users.index')->with('status', 'Usuario actualizado.');
    }

    public function toggle(Request $request, User $user): RedirectResponse
    {
        $user->update(['active' => ! $user->active]);

        return back()->with('status', 'Estado del usuario actualizado.');
    }

    private function formData(Request $request, ?User $editing = null): array
    {
        $companyId = $editing?->company_id ?: $request->session()->get('marketing_company_id');

        return [
            'companies' => Company::query()->visibleTo($request->user())->orderBy('name')->get(),
            'campaigns' => Campaign::query()->visibleTo($request->user())->when($companyId, fn (Builder $query) => $query->where('company_id', $companyId))->orderBy('name')->get(),
        ];
    }

    private function validatedData(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'company_id' => ['nullable', 'exists:marketing_companies,id'],
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:160', Rule::unique('marketing_users', 'email')->ignore($user)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'phone' => ['nullable', 'string', 'max:40'],
            'timezone' => ['nullable', 'string', 'max:80'],
            'active' => ['nullable', 'boolean'],
            'campaign_ids' => ['nullable', 'array'],
            'campaign_ids.*' => ['exists:marketing_campaigns,id'],
        ]);
    }

    private function syncCampaigns(Request $request, User $user): void
    {
        $campaignIds = collect($request->input('campaign_ids', []))->map(fn ($id) => (int) $id)->all();
        $campaignIds = Campaign::query()
            ->visibleTo($request->user())
            ->when($user->company_id, fn (Builder $query) => $query->where('company_id', $user->company_id))
            ->whereIn('id', $campaignIds)
            ->pluck('id')
            ->all();

        $user->campaigns()->sync($campaignIds);
    }
}
