<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Models\Client;
use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $baseQuery = Project::query()->visibleTo($user);

        $projects = (clone $baseQuery)
            ->with(['company', 'client', 'responsible'])
            ->when($request->filled('company_id'), fn (Builder $query) => $query->where('company_id', $request->integer('company_id')))
            ->when($request->filled('client_id'), fn (Builder $query) => $query->where('client_id', $request->integer('client_id')))
            ->when($request->filled('responsible_user_id'), fn (Builder $query) => $query->where('responsible_user_id', $request->integer('responsible_user_id')))
            ->when($request->filled('modality'), fn (Builder $query) => $query->where('modality', $request->input('modality')))
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->input('status')))
            ->when($request->filled('location'), fn (Builder $query) => $query->where('location', 'like', '%'.$request->input('location').'%'))
            ->when($request->filled('date_from'), fn (Builder $query) => $query->whereDate('start_date', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn (Builder $query) => $query->whereDate('start_date', '<=', $request->date('date_to')))
            ->when($request->filled('state_group'), fn (Builder $query) => $this->applyStateGroupFilter($query, $request->input('state_group')))
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = '%'.$request->input('search').'%';
                $query->where(function (Builder $builder) use ($search): void {
                    $builder
                        ->where('project_key', 'like', $search)
                        ->orWhere('name', 'like', $search)
                        ->orWhere('location', 'like', $search)
                        ->orWhereHas('client', fn (Builder $client) => $client->where('name', 'like', $search));
                });
            })
            ->orderByDesc('start_date')
            ->get();

        $counts = [
            'Todas' => (clone $baseQuery)->count(),
            'Por iniciar' => $this->applyStateGroupFilter((clone $baseQuery), 'Por iniciar')->count(),
            'En Proceso' => $this->applyStateGroupFilter((clone $baseQuery), 'En Proceso')->count(),
            'Terminada' => $this->applyStateGroupFilter((clone $baseQuery), 'Terminada')->count(),
        ];

        $filterOptions = [
            'companies' => Company::orderBy('name')->get(),
            'clients' => Client::orderBy('name')->get(),
            'responsibles' => User::orderBy('name')->get(),
            'modalities' => ['Precio alzado', 'Administracion'],
            'statuses' => ['Por iniciar', 'En Proceso', 'Terminada'],
        ];

        return view('projects.index', [
            'projects' => $projects,
            'counts' => $counts,
            'filterOptions' => $filterOptions,
            'canCreate' => $this->canCreate($user),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($this->canCreate($request->user()), 403);

        return view('projects.create', [
            'project' => new Project([
                'status' => 'Por iniciar',
                'modality' => 'Precio alzado',
                'physical_progress' => 0,
                'financial_progress' => 0,
            ]),
            'companies' => Company::orderBy('name')->get(),
            'clients' => Client::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        abort_unless($this->canCreate($request->user()), 403);

        $data = $this->validatedProjectData($request);
        $project = Project::create($data);

        $this->syncDefaultAccess($project, $request->user());

        return redirect()
            ->route('obras.show', $project)
            ->with('status', 'Obra creada correctamente.');
    }

    public function show(Request $request, Project $project): View
    {
        $this->authorizeProject($request, $project);

        $project->load([
            'company',
            'client',
            'responsible',
            'contracts',
            'budgets',
            'categories.workItems',
            'workItems.category',
            'progressRecords.workItem',
            'estimates.items.workItem',
            'estimates.payments',
            'retentions',
            'payments',
            'weeklyScopes.crew',
            'payrolls.items',
            'materialRequests.items',
            'supplyOrders.items',
            'warehouses',
            'dailyLogs',
            'photos',
            'incidents.responsible',
            'changeOrders',
            'documents',
            'events',
            'users.role',
        ]);

        return view('projects.show', [
            'project' => $project,
            'canEdit' => $this->canEditProject($request->user(), $project),
        ]);
    }

    public function edit(Request $request, Project $project): View
    {
        abort_unless($this->canEditProject($request->user(), $project), 403);

        return view('projects.edit', [
            'project' => $project,
            'companies' => Company::orderBy('name')->get(),
            'clients' => Client::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        abort_unless($this->canEditProject($request->user(), $project), 403);

        $project->update($this->validatedProjectData($request, $project));

        return redirect()
            ->route('obras.show', $project)
            ->with('status', 'Obra actualizada correctamente.');
    }

    public function destroy(Request $request, Project $project): RedirectResponse
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        $project->delete();

        return redirect()
            ->route('obras.index')
            ->with('status', 'Obra enviada a borrado logico.');
    }

    private function validatedProjectData(ProjectRequest $request, ?Project $project = null): array
    {
        $data = $request->validated();
        $data['estimated_amount'] = $data['estimated_amount'] ?? 0;
        $data['paid_amount'] = $data['paid_amount'] ?? 0;
        $data['retention_amount'] = $data['retention_amount'] ?? 0;
        $data['physical_progress'] = $data['physical_progress'] ?? 0;
        $data['financial_progress'] = $data['financial_progress'] ?? 0;

        if ($request->hasFile('photo')) {
            File::ensureDirectoryExists(public_path('uploads/projects'));
            $fileName = Str::slug($data['project_key']).'-'.time().'.'.$request->file('photo')->extension();
            $request->file('photo')->move(public_path('uploads/projects'), $fileName);
            $data['photo_path'] = '/uploads/projects/'.$fileName;
        } elseif ($project && empty($data['photo_path'])) {
            $data['photo_path'] = $project->photo_path;
        }

        unset($data['photo']);

        return $data;
    }

    private function canCreate(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasRole(['administrador-obra']);
    }

    private function applyStateGroupFilter(Builder $query, string $state): Builder
    {
        return match ($state) {
            'Por iniciar' => $query
                ->where('status', '!=', 'Terminada')
                ->where('physical_progress', '<=', 0),
            'En Proceso' => $query
                ->where('status', '!=', 'Terminada')
                ->where('physical_progress', '>', 0)
                ->where('physical_progress', '<', 100),
            'Terminada' => $query
                ->where(function (Builder $builder): void {
                    $builder->where('status', 'Terminada')
                        ->orWhere('physical_progress', '>=', 100);
                }),
            default => $query,
        };
    }

    private function authorizeProject(Request $request, Project $project): void
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            return;
        }

        $hasAccess = $project->users()
            ->where('users.id', $user->id)
            ->where('project_users.can_view', true)
            ->exists();

        abort_unless($hasAccess, 403);
    }

    private function canEditProject(User $user, Project $project): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $project->users()
            ->where('users.id', $user->id)
            ->where('project_users.can_edit', true)
            ->exists();
    }

    private function syncDefaultAccess(Project $project, User $user): void
    {
        $access = [
            $user->id => ['can_view' => true, 'can_edit' => true],
        ];

        if ($project->responsible_user_id) {
            $access[$project->responsible_user_id] = ['can_view' => true, 'can_edit' => true];
        }

        $project->users()->syncWithoutDetaching($access);
    }
}
