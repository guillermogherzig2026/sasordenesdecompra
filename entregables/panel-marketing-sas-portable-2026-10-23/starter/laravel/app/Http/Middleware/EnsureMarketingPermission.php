<?php

namespace App\Http\Middleware;

use App\Support\MarketingNavigation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMarketingPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $permission = MarketingNavigation::permissionForRoute($request->route()?->getName());

        abort_unless(
            $user && method_exists($user, 'canNavigateTo') && $user->canNavigateTo($permission),
            403
        );

        return $next($request);
    }
}
