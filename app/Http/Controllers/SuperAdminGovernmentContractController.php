<?php

namespace App\Http\Controllers;

use App\Support\GovernmentContractNavigation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminGovernmentContractController extends Controller
{
    public function index(Request $request): View
    {
        $section = GovernmentContractNavigation::normalizeSection($request->query('section'));
        $this->authorizePermission($section);

        return view('superadmin.government-contracts.index', [
            'selectedSection' => $section,
        ]);
    }

    public function panel(Request $request): View
    {
        $section = GovernmentContractNavigation::normalizeSection($request->query('section'));
        $item = GovernmentContractNavigation::itemForSection($section);
        $this->authorizePermission($section);

        return view('superadmin.government-contracts.panel', [
            'selectedModule' => $item['module'],
        ]);
    }

    private function authorizePermission(string $section): void
    {
        abort_unless(Auth::user()?->canNavigateTo(GovernmentContractNavigation::permissionForSection($section)), 403);
    }
}
