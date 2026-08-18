<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserAccessController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::with(['role', 'projects'])->orderBy('name')->get(),
            'projects' => Project::with('client')->orderBy('project_key')->get(),
        ]);
    }

    public function updateProjectAccess(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'projects' => ['nullable', 'array'],
            'projects.*.can_view' => ['nullable', 'boolean'],
            'projects.*.can_edit' => ['nullable', 'boolean'],
        ]);

        $oldAccess = $user->projects()
            ->get()
            ->mapWithKeys(fn (Project $project) => [
                $project->project_key => [
                    'can_view' => (bool) $project->pivot->can_view,
                    'can_edit' => (bool) $project->pivot->can_edit,
                ],
            ])
            ->all();

        $sync = collect($data['projects'] ?? [])
            ->filter(fn (array $access): bool => isset($access['can_view']))
            ->mapWithKeys(function (array $access, string $projectId): array {
                return [
                    (int) $projectId => [
                        'can_view' => true,
                        'can_edit' => isset($access['can_edit']),
                    ],
                ];
            })
            ->all();

        $user->projects()->sync($sync);

        $newAccess = $user->projects()
            ->get()
            ->mapWithKeys(fn (Project $project) => [
                $project->project_key => [
                    'can_view' => (bool) $project->pivot->can_view,
                    'can_edit' => (bool) $project->pivot->can_edit,
                ],
            ])
            ->all();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'occurred_at' => now(),
            'module' => 'Usuarios y permisos',
            'record_type' => User::class,
            'record_id' => $user->id,
            'action' => 'Actualizo accesos de obra',
            'old_values' => $oldAccess,
            'new_values' => $newAccess,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('status', 'Permisos de obra actualizados.');
    }
}
