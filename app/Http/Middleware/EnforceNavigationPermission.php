<?php

namespace App\Http\Middleware;

use App\Support\NavigationPermissionCatalog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceNavigationPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $requiredPermissions = NavigationPermissionCatalog::permissionsForRequest($request);

        if ($user && $user->role !== 'superadmin' && count($requiredPermissions)) {
            abort_unless(
                collect($requiredPermissions)->contains(fn (string $permission) => $user->canNavigateTo($permission)),
                403
            );
        }

        return $next($request);
    }
}
