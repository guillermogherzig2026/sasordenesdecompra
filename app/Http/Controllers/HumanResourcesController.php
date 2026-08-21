<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class HumanResourcesController extends Controller
{
    private const SECTIONS = [
        'inicio' => ['title' => 'Recursos Humanos', 'route' => 'dashboard'],
        'candidatos' => ['title' => 'Registro de candidatos', 'route' => 'candidates'],
        'empleados' => ['title' => 'Empleados', 'route' => 'employees'],
        'contratos' => ['title' => 'Contratos', 'route' => 'contracts'],
        'aprobaciones' => ['title' => 'Pendientes de Aprobación', 'route' => 'pending-approvals'],
        'nomina' => ['title' => 'Nómina', 'route' => 'payroll'],
        'horas-extra' => ['title' => 'Horas extras', 'route' => 'overtime'],
        'reportes' => ['title' => 'Reportes', 'route' => 'reports'],
        'configuracion' => ['title' => 'Configuración', 'route' => 'config'],
        'gerentes' => ['title' => 'Gerentes', 'route' => 'managers'],
    ];

    public function show(?string $section = null): View
    {
        $this->ensureSuperAdmin();
        [$section, $screen] = $this->resolveSection($section);

        return view('human-resources.index', [
            'section' => $section,
            'title' => $screen['title'],
        ]);
    }

    public function embed(?string $section = null): View
    {
        $this->ensureSuperAdmin();
        [, $screen] = $this->resolveSection($section);

        return view('human-resources.embed', [
            'appRoute' => $screen['route'],
            'title' => $screen['title'],
            'registeredCompanies' => Company::query()
                ->orderBy('name')
                ->pluck('name')
                ->values()
                ->all(),
        ]);
    }

    private function resolveSection(?string $section): array
    {
        $section = $section ?: 'inicio';
        abort_unless(array_key_exists($section, self::SECTIONS), 404);

        return [$section, self::SECTIONS[$section]];
    }

    private function ensureSuperAdmin(): void
    {
        abort_unless(Auth::user()?->canAccessRole('superadmin'), 403);
    }
}
