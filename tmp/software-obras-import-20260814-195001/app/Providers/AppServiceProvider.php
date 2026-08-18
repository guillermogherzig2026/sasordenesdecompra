<?php

namespace App\Providers;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('layouts.app', function ($view): void {
            $user = Auth::user();

            $activeCarouselProjects = collect();

            if ($user) {
                $activeCarouselProjects = Project::query()
                    ->visibleTo($user)
                    ->with('client')
                    ->whereIn('status', ['Por iniciar', 'En Proceso'])
                    ->orderBy('project_key')
                    ->get();
            }

            $view->with('activeCarouselProjects', $activeCarouselProjects);
        });
    }
}
