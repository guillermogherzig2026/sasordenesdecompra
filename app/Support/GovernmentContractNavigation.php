<?php

namespace App\Support;

final class GovernmentContractNavigation
{
    public static function items(): array
    {
        return [
            'contracts' => [
                'label' => 'Contratos',
                'module' => 'Contratos',
                'permission' => 'government_contracts.dashboard',
            ],
            'supply-pending' => [
                'label' => 'OS Pendientes',
                'module' => 'OS Pendientes',
                'permission' => 'government_contracts.supply_pending',
            ],
            'supply-billing' => [
                'label' => 'OS Facturacion',
                'module' => 'OS Facturacion',
                'permission' => 'government_contracts.supply_billing',
            ],
            'supply-history' => [
                'label' => 'OS Historial',
                'module' => 'OS Historial',
                'permission' => 'government_contracts.supply_history',
            ],
            'inventory' => [
                'label' => 'Almacenes e Inventarios',
                'module' => 'Almacenes e Inventarios',
                'permission' => 'government_contracts.inventory',
            ],
            'institutions' => [
                'label' => 'Instituciones y unidades',
                'module' => 'Instituciones y unidades',
                'permission' => 'government_contracts.institutions',
            ],
            'templates' => [
                'label' => 'Plantillas',
                'module' => 'Plantillas',
                'permission' => 'government_contracts.templates',
            ],
            'companies' => [
                'label' => 'Empresas',
                'module' => 'Empresas',
                'permission' => 'government_contracts.companies',
            ],
            'users' => [
                'label' => 'Usuarios y roles',
                'module' => 'Usuarios y roles',
                'permission' => 'government_contracts.users',
            ],
            'audit' => [
                'label' => 'Auditoria',
                'module' => 'Auditoria',
                'permission' => 'government_contracts.audit',
            ],
            'configuration' => [
                'label' => 'Configuracion',
                'module' => 'Configuracion',
                'permission' => 'government_contracts.configuration',
            ],
        ];
    }

    public static function defaultSection(): string
    {
        return 'contracts';
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
}
