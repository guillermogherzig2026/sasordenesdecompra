<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::query()
            ->visibleTo($request->user())
            ->with(['client', 'responsible'])
            ->orderBy('project_key')
            ->get();

        $summary = [
            'projects' => $projects->count(),
            'active' => $projects->where('status', 'En ejecucion')->count(),
            'contracted' => $projects->sum('contracted_value'),
            'paid' => $projects->sum('paid_amount'),
            'pending' => $projects->sum(fn (Project $project): float => $project->balance_to_pay),
            'physical' => round($projects->avg('physical_progress') ?? 0, 2),
            'financial' => round($projects->avg('financial_progress') ?? 0, 2),
        ];

        $chart = [
            'labels' => $projects->pluck('project_key')->values(),
            'physical' => $projects->pluck('physical_progress')->map(fn ($value) => (float) $value)->values(),
            'financial' => $projects->pluck('financial_progress')->map(fn ($value) => (float) $value)->values(),
        ];

        return view('dashboard.index', compact('projects', 'summary', 'chart'));
    }
}
