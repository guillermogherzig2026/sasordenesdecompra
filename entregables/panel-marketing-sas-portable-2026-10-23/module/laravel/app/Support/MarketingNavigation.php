<?php

namespace App\Support;

use Illuminate\Support\Str;

final class MarketingNavigation
{
    public static function items(): array
    {
        return [
            'dashboard' => [
                'label' => 'Panel general',
                'permission' => 'marketing.dashboard',
                'route' => 'marketing.index',
                'route_prefix' => 'marketing.index',
            ],
            'companies' => [
                'label' => 'Empresas',
                'permission' => 'marketing.companies',
                'route' => 'marketing.companies.index',
                'route_prefix' => 'marketing.companies.',
            ],
            'campaigns' => [
                'label' => 'Campañas',
                'permission' => 'marketing.campaigns',
                'route' => 'marketing.campaigns.index',
                'route_prefix' => 'marketing.campaigns.',
            ],
            'leads' => [
                'label' => 'Leads',
                'permission' => 'marketing.leads',
                'route' => 'marketing.leads.index',
                'route_prefix' => 'marketing.leads.',
            ],
            'conversations' => [
                'label' => 'Conversaciones',
                'permission' => 'marketing.conversations',
                'route' => 'marketing.conversations.index',
                'route_prefix' => 'marketing.conversations.',
            ],
            'reports' => [
                'label' => 'Reportes',
                'permission' => 'marketing.reports',
                'route' => 'marketing.reports.index',
                'route_prefix' => 'marketing.reports.',
            ],
            'users' => [
                'label' => 'Usuarios',
                'permission' => 'marketing.users',
                'route' => 'marketing.users.index',
                'route_prefix' => 'marketing.users.',
            ],
            'settings' => [
                'label' => 'Configuración',
                'permission' => 'marketing.settings',
                'route' => 'marketing.settings.index',
                'route_prefix' => 'marketing.settings.',
            ],
        ];
    }

    public static function permissionLabels(): array
    {
        return collect(self::items())
            ->mapWithKeys(fn (array $item) => [$item['permission'] => $item['label']])
            ->all();
    }

    public static function permissionsByLabel(): array
    {
        return collect(self::items())
            ->mapWithKeys(fn (array $item) => [$item['label'] => $item['permission']])
            ->all();
    }

    public static function permissionForRoute(?string $routeName): string
    {
        foreach (self::items() as $item) {
            if ($routeName === $item['route'] || Str::startsWith((string) $routeName, $item['route_prefix'])) {
                return $item['permission'];
            }
        }

        return self::items()['dashboard']['permission'];
    }
}
