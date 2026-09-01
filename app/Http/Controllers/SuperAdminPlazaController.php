<?php

namespace App\Http\Controllers;

use App\Support\PlazaNavigation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminPlazaController extends Controller
{
    public function index(): View
    {
        return $this->showSection('dashboard');
    }

    public function administration(): View
    {
        return $this->showSection('administration');
    }

    public function contracts(): View
    {
        return $this->showSection('contracts');
    }

    public function marketplace(): View
    {
        return $this->showSection('marketplace');
    }

    public function properties(): View
    {
        return $this->showSection('properties');
    }

    public function users(): View
    {
        return $this->showSection('users');
    }

    public function tenants(): View
    {
        return $this->showSection('tenants');
    }

    public function panel(Request $request): View
    {
        $section = $request->has('section')
            ? PlazaNavigation::normalizeSection($request->query('section'))
            : PlazaNavigation::sectionForLegacyTab($request->query('tab'));
        $item = PlazaNavigation::itemForSection($section);
        $this->authorizePermission($section);

        return view('superadmin.plazas.panel', [
            'defaultTab' => $item['tab'],
            'selectedSection' => $section,
        ]);
    }

    private function showSection(string $section): View
    {
        $section = PlazaNavigation::normalizeSection($section);
        $this->authorizePermission($section);

        return view('superadmin.plazas.index', [
            'selectedSection' => $section,
        ]);
    }

    private function authorizePermission(string $section): void
    {
        abort_unless(Auth::user()?->canNavigateTo(PlazaNavigation::permissionForSection($section)), 403);
    }
}
