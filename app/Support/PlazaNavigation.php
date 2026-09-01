<?php

namespace App\Support;

final class PlazaNavigation
{
    public static function items(): array
    {
        return [
            'dashboard' => [
                'label' => 'Panel general',
                'tab' => 'superadmin_dashboard',
                'permission' => 'plazas.dashboard',
                'route' => 'superadmin.plazas.index',
            ],
            'administration' => [
                'label' => 'Administracion y Cobranza',
                'tab' => 'administration',
                'permission' => 'plazas.administration',
                'route' => 'superadmin.plazas.administration',
            ],
            'contracts' => [
                'label' => 'Contratos',
                'tab' => 'plaza_contracts',
                'permission' => 'plazas.contracts',
                'route' => 'superadmin.plazas.contracts',
            ],
            'marketplace' => [
                'label' => 'Marketplace',
                'tab' => 'plaza_marketplace',
                'permission' => 'plazas.marketplace',
                'route' => 'superadmin.plazas.marketplace',
            ],
            'tenants' => [
                'label' => 'Arrendatarios',
                'tab' => 'tenants',
                'permission' => 'plazas.tenants',
                'route' => 'superadmin.plazas.tenants',
            ],
            'properties' => [
                'label' => 'Catalogo de unidades',
                'tab' => 'properties',
                'permission' => 'plazas.properties',
                'route' => 'superadmin.plazas.properties',
            ],
            'users' => [
                'label' => 'Alta de Usuarios',
                'tab' => 'user_new',
                'permission' => 'plazas.users',
                'route' => 'superadmin.plazas.users',
            ],
        ];
    }

    public static function defaultSection(): string
    {
        return 'dashboard';
    }

    public static function normalizeSection(?string $section): string
    {
        return array_key_exists((string) $section, self::items())
            ? (string) $section
            : self::defaultSection();
    }

    public static function itemForSection(?string $section): array
    {
        return self::items()[self::normalizeSection($section)];
    }

    public static function permissionForSection(?string $section): string
    {
        return self::itemForSection($section)['permission'];
    }

    public static function permissionLabels(): array
    {
        return collect(self::items())
            ->mapWithKeys(fn (array $item) => [$item['permission'] => $item['label']])
            ->all();
    }

    public static function sectionForRoute(?string $routeName): string
    {
        foreach (self::items() as $section => $item) {
            if ($item['route'] === $routeName) {
                return $section;
            }
        }

        return self::defaultSection();
    }

    public static function sectionForLegacyTab(?string $tab): string
    {
        return match ($tab) {
            'administration' => 'administration',
            'plaza_contracts' => 'contracts',
            'plaza_marketplace' => 'marketplace',
            'user_new' => 'users',
            'tenants' => 'tenants',
            default => self::defaultSection(),
        };
    }
}
