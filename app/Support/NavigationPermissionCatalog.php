<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class NavigationPermissionCatalog
{
    public const HOME_DASHBOARD = 'home.dashboard';

    public static function operationalRolesByCategory(): array
    {
        return [
            'procurement' => 'buyer',
            'inventory' => 'inventory',
            'services' => 'administrative_assistant',
        ];
    }

    public static function categoryForRole(string $role): ?string
    {
        $category = array_search($role, self::operationalRolesByCategory(), true);

        return $category === false ? null : $category;
    }

    public static function categories(): array
    {
        return [
            'home' => [
                'label' => 'Inicio',
                'description' => 'Acceso al panel de inicio y resumen operativo.',
                'items' => [
                    self::HOME_DASHBOARD => 'Panel de inicio',
                ],
            ],
            'finance' => [
                'label' => 'Finanzas',
                'description' => 'Ordenes por pagar, servicios, autorizaciones y catalogos financieros.',
                'items' => [
                    'finance.orders.active' => 'OC Vigentes',
                    'finance.orders.history' => 'OC Historial',
                    'finance.construction.active' => 'OP Pendientes',
                    'finance.construction.history' => 'OP Historial',
                    'finance.services.payments' => 'Pago Servicios',
                    'finance.services.history' => 'Historial de Servicios',
                    'finance.supply.active' => 'OS Vigentes',
                    'finance.supply.history' => 'OS Historial',
                    'finance.reimbursements.active' => 'OR Vigentes',
                    'finance.reimbursements.history' => 'OR Historial',
                    'finance.authorizations' => 'Autorizaciones',
                    'finance.providers' => 'Alta Proveedores',
                    'finance.companies' => 'Alta Empresas',
                    'finance.services.create' => 'Alta Servicio',
                    'finance.services.catalog' => 'Catalogo Servicios',
                ],
                'groups' => [
                    'purchase_orders' => [
                        'label' => 'Ordenes de compra',
                        'items' => ['finance.orders.active', 'finance.orders.history'],
                    ],
                    'construction_payment_orders' => [
                        'label' => 'Ordenes de Pago de Obra',
                        'items' => ['finance.construction.active', 'finance.construction.history'],
                    ],
                    'services' => [
                        'label' => 'Servicios',
                        'items' => ['finance.services.payments', 'finance.services.history'],
                    ],
                    'supply_orders' => [
                        'label' => 'Ordenes de suministro',
                        'items' => ['finance.supply.active', 'finance.supply.history'],
                    ],
                    'reimbursement_orders' => [
                        'label' => 'Ordenes de reembolso',
                        'items' => ['finance.reimbursements.active', 'finance.reimbursements.history'],
                    ],
                    'administration' => [
                        'label' => 'Administracion',
                        'items' => [
                            'finance.authorizations',
                            'finance.providers',
                            'finance.companies',
                            'finance.services.create',
                            'finance.services.catalog',
                        ],
                    ],
                ],
            ],
            'procurement' => [
                'label' => 'Compras y Suministros',
                'description' => 'Ordenes de compra, suministro, reembolso y proveedores.',
                'items' => [
                    'procurement.orders.create' => 'Nueva OC',
                    'procurement.orders.paid' => 'OC Pagadas',
                    'procurement.orders.pending_payment' => 'OC Pendientes de Pago',
                    'procurement.orders.mine' => 'Mis Ordenes',
                    'procurement.orders.rejected' => 'OC Rechazadas',
                    'procurement.supply.create' => 'Nueva OS',
                    'procurement.supply.pending' => 'OS Pendientes',
                    'procurement.supply.history' => 'OS Historial',
                    'procurement.reimbursements.create' => 'Nueva OR',
                    'procurement.reimbursements.pending' => 'OR Pendientes',
                    'procurement.reimbursements.history' => 'OR Historial',
                    'procurement.providers' => 'Alta de Proveedor',
                ],
                'groups' => [
                    'purchase_orders' => [
                        'label' => 'Ordenes de compra',
                        'items' => [
                            'procurement.orders.create',
                            'procurement.orders.paid',
                            'procurement.orders.pending_payment',
                            'procurement.orders.mine',
                            'procurement.orders.rejected',
                        ],
                    ],
                    'supply_orders' => [
                        'label' => 'Ordenes de suministro',
                        'items' => [
                            'procurement.supply.create',
                            'procurement.supply.pending',
                            'procurement.supply.history',
                        ],
                    ],
                    'reimbursement_orders' => [
                        'label' => 'Ordenes de reembolso',
                        'items' => [
                            'procurement.reimbursements.create',
                            'procurement.reimbursements.pending',
                            'procurement.reimbursements.history',
                        ],
                    ],
                    'providers' => [
                        'label' => 'Proveedores',
                        'items' => ['procurement.providers'],
                    ],
                ],
            ],
            'inventory' => [
                'label' => 'Almacenes e Inventarios',
                'description' => 'Recepciones, existencias, almacenes y ordenes por entregar.',
                'items' => [
                    'inventory.orders.paid' => 'OC Pagadas',
                    'inventory.orders.history' => 'Historial',
                    'inventory.supply.active' => 'OS por Entregar',
                    'inventory.stock' => 'Inventarios',
                    'inventory.warehouses' => 'Almacenes',
                ],
                'groups' => [
                    'purchase_orders' => [
                        'label' => 'Ordenes de compra',
                        'items' => ['inventory.orders.paid', 'inventory.orders.history'],
                    ],
                    'supply_orders' => [
                        'label' => 'Ordenes de suministro',
                        'items' => ['inventory.supply.active'],
                    ],
                    'inventory_management' => [
                        'label' => 'Inventarios y almacenes',
                        'items' => ['inventory.stock', 'inventory.warehouses'],
                    ],
                ],
            ],
            'services' => [
                'label' => 'Servicios',
                'description' => 'Alta, catalogo, calendario e historial de servicios.',
                'items' => [
                    'services.create' => 'Alta Servicio',
                    'services.catalog' => 'Catalogo Servicios',
                    'services.months' => 'Vista por Mes',
                    'services.history' => 'Historial de Servicios',
                ],
            ],
            'human_resources' => [
                'label' => 'Recursos Humanos',
                'description' => 'Acceso a la operacion y administracion de Recursos Humanos.',
                'items' => [
                    'human_resources.dashboard' => 'Inicio',
                    'human_resources.candidates' => 'Registro de candidatos',
                    'human_resources.contracts' => 'Contratos',
                    'human_resources.employees' => 'Empleados',
                    'human_resources.pending_approvals' => 'Pendientes de Aprobación',
                    'human_resources.payroll' => 'Nómina',
                    'human_resources.overtime' => 'Horas extras',
                    'human_resources.reports' => 'Reportes',
                    'human_resources.configuration' => 'Configuración',
                    'human_resources.managers_branches' => 'Gerentes y Sucursales',
                ],
            ],
            'construction' => [
                'label' => 'Administracion de obra',
                'description' => 'Obras, operacion, materiales, compras y costos unitarios.',
                'items' => [
                    'construction.dashboard' => 'Panel general',
                    'construction.generators' => 'Generadores de obra',
                    'construction.materials' => 'Materiales e insumos',
                    'construction.labor' => 'Mano de obra',
                    'construction.calendar' => 'Calendario',
                    'construction.payments' => 'Historial de pagos',
                    'construction.supply' => 'Ordenes de suministro',
                    'construction.warehouses' => 'Almacenes',
                    'construction.purchases' => 'Compras',
                    'construction.unit_prices' => 'Tabulador de precios unitarios',
                    'construction.providers' => 'Alta de proveedor',
                ],
                'groups' => [
                    'obras' => [
                        'label' => 'Obras',
                        'items' => [
                            'construction.dashboard',
                            'construction.generators',
                            'construction.materials',
                        ],
                    ],
                    'operation' => [
                        'label' => 'Operacion',
                        'items' => [
                            'construction.labor',
                            'construction.calendar',
                            'construction.payments',
                        ],
                    ],
                    'administration' => [
                        'label' => 'Administracion',
                        'items' => [
                            'construction.supply',
                            'construction.warehouses',
                            'construction.purchases',
                            'construction.unit_prices',
                            'construction.providers',
                        ],
                    ],
                ],
            ],
            'plazas' => [
                'label' => 'Administracion de Plazas',
                'description' => 'Catalogo y administracion operativa de las plazas.',
                'items' => PlazaNavigation::permissionLabels(),
            ],
            'government_contracts' => [
                'label' => 'Contratos Gobierno',
                'description' => 'Contratos, suministros, inventarios, catalogos y gobierno del modulo.',
                'items' => GovernmentContractNavigation::permissionLabels(),
            ],
            'security' => [
                'label' => 'Seguridad y Vigilancia',
                'description' => 'Empresas, sucursales, camaras, alertas y configuracion de seguridad.',
                'items' => [
                    'security.dashboard' => 'Empresas',
                    'security.branches' => 'Sucursales',
                    'security.analytics' => 'Analíticos',
                    'security.alerts' => 'Alertas',
                    'security.users' => 'Usuarios',
                    'security.reports' => 'Reportes',
                    'security.cameras' => 'Cámaras',
                    'security.configuration' => 'Configuración',
                ],
            ],
        ];
    }

    public static function allKeys(): array
    {
        return collect(self::categories())
            ->flatMap(fn (array $category) => array_keys($category['items']))
            ->values()
            ->all();
    }

    public static function categoryKeys(string $category): array
    {
        return array_keys(self::categories()[$category]['items'] ?? []);
    }

    public static function normalize(?array $permissions): array
    {
        $allowed = array_flip(self::allKeys());

        return collect($permissions ?? [])
            ->map(fn ($permission) => trim((string) $permission))
            ->filter(fn (string $permission) => isset($allowed[$permission]))
            ->unique()
            ->values()
            ->all();
    }

    public static function defaultsForRole(string $role, array $buyerSubroles = ['purchases']): array
    {
        if ($role === 'superadmin') {
            return self::allKeys();
        }

        $homePermissions = [self::HOME_DASHBOARD];

        if ($role === 'finance') {
            return array_merge($homePermissions, self::categoryKeys('finance'));
        }

        if ($role === 'inventory') {
            return array_merge($homePermissions, self::categoryKeys('inventory'));
        }

        if (in_array($role, ['services', 'administrative_assistant'], true)) {
            return array_merge($homePermissions, self::categoryKeys('services'));
        }

        if ($role !== 'buyer') {
            return $homePermissions;
        }

        $permissions = collect($homePermissions);
        if (in_array('purchases', $buyerSubroles, true)) {
            $permissions = $permissions->concat([
                'procurement.orders.create',
                'procurement.orders.paid',
                'procurement.orders.pending_payment',
                'procurement.orders.mine',
                'procurement.orders.rejected',
                'procurement.providers',
            ]);
        }
        if (in_array('supplies', $buyerSubroles, true)) {
            $permissions = $permissions->concat([
                'procurement.supply.create',
                'procurement.supply.pending',
                'procurement.supply.history',
            ]);
        }
        if (in_array('reimbursements', $buyerSubroles, true)) {
            $permissions = $permissions->concat([
                'procurement.reimbursements.create',
                'procurement.reimbursements.pending',
                'procurement.reimbursements.history',
            ]);
        }

        return self::normalize($permissions->all());
    }

    public static function categoryLabelsFor(array $permissions): array
    {
        $permissions = array_flip(self::normalize($permissions));

        return collect(self::categories())
            ->filter(fn (array $category) => collect(array_keys($category['items']))->contains(fn (string $key) => isset($permissions[$key])))
            ->pluck('label')
            ->values()
            ->all();
    }

    public static function permissionsForRequest(Request $request): array
    {
        $routeName = (string) $request->route()?->getName();

        if ($routeName === '') {
            return [];
        }

        if ($routeName === 'dashboard') {
            return [self::HOME_DASHBOARD];
        }

        if (Str::startsWith($routeName, 'finance.orders.')) {
            if ($routeName === 'finance.orders.history') {
                return ['finance.orders.history'];
            }
            if ($routeName === 'finance.orders.active') {
                return ['finance.orders.active'];
            }

            return ['finance.orders.active', 'finance.orders.history'];
        }
        if (Str::startsWith($routeName, 'finance.construction-payment-orders.')) {
            if ($routeName === 'finance.construction-payment-orders.history') {
                return ['finance.construction.history'];
            }
            if ($routeName === 'finance.construction-payment-orders.active') {
                return ['finance.construction.active'];
            }

            return ['finance.construction.active', 'finance.construction.history'];
        }
        if (Str::startsWith($routeName, 'finance.services.')) {
            return ['finance.services.payments'];
        }
        if (Str::startsWith($routeName, 'finance.supply-orders.')) {
            if ($routeName === 'finance.supply-orders.history') {
                return ['finance.supply.history'];
            }
            if ($routeName === 'finance.supply-orders.active') {
                return ['finance.supply.active'];
            }

            return ['finance.supply.active', 'finance.supply.history'];
        }
        if (Str::startsWith($routeName, 'finance.reimbursement-orders.')) {
            if ($routeName === 'finance.reimbursement-orders.history') {
                return ['finance.reimbursements.history'];
            }
            if ($routeName === 'finance.reimbursement-orders.active') {
                return ['finance.reimbursements.active'];
            }

            return ['finance.reimbursements.active', 'finance.reimbursements.history'];
        }
        if (Str::startsWith($routeName, 'finance.admin.users')) {
            return ['finance.authorizations'];
        }
        if (Str::startsWith($routeName, 'finance.admin.providers')) {
            return ['finance.providers'];
        }
        if (Str::startsWith($routeName, 'finance.admin.companies')) {
            return ['finance.companies'];
        }

        if (Str::startsWith($routeName, 'buyer.orders.')) {
            if ($request->input('context') === 'construction') {
                return ['construction.purchases'];
            }

            if (in_array($routeName, ['buyer.orders.create', 'buyer.orders.store'], true)) {
                return ['procurement.orders.create'];
            }

            if ($routeName === 'buyer.orders.index') {
                return [match ($request->query('panel')) {
                    'paid' => 'procurement.orders.paid',
                    'pending-payment' => 'procurement.orders.pending_payment',
                    'rejected' => 'procurement.orders.rejected',
                    default => 'procurement.orders.mine',
                }];
            }

            return [
                'procurement.orders.create',
                'procurement.orders.paid',
                'procurement.orders.pending_payment',
                'procurement.orders.mine',
                'procurement.orders.rejected',
            ];
        }
        if (Str::startsWith($routeName, 'buyer.providers.')) {
            return ['procurement.providers'];
        }
        if (Str::startsWith($routeName, 'buyer.supply-orders.')) {
            if (in_array($routeName, ['buyer.supply-orders.create', 'buyer.supply-orders.store'], true)) {
                return ['procurement.supply.create'];
            }

            if ($routeName === 'buyer.supply-orders.index') {
                return [$request->query('panel') === 'history' ? 'procurement.supply.history' : 'procurement.supply.pending'];
            }

            return ['procurement.supply.create', 'procurement.supply.pending', 'procurement.supply.history'];
        }
        if (Str::startsWith($routeName, 'buyer.reimbursement-orders.')) {
            if (in_array($routeName, ['buyer.reimbursement-orders.create', 'buyer.reimbursement-orders.store'], true)) {
                return ['procurement.reimbursements.create'];
            }

            if ($routeName === 'buyer.reimbursement-orders.index') {
                return [$request->query('panel') === 'history' ? 'procurement.reimbursements.history' : 'procurement.reimbursements.pending'];
            }

            return ['procurement.reimbursements.create', 'procurement.reimbursements.pending', 'procurement.reimbursements.history'];
        }

        if (Str::startsWith($routeName, 'inventory.orders.')) {
            if ($routeName === 'inventory.orders.history') {
                return ['inventory.orders.history'];
            }
            if ($routeName === 'inventory.orders.index') {
                return ['inventory.orders.paid'];
            }

            return ['inventory.orders.paid', 'inventory.orders.history'];
        }
        if (Str::startsWith($routeName, 'inventory.supply-orders.')) {
            return ['inventory.supply.active'];
        }
        if (Str::startsWith($routeName, 'inventory.stock.') || Str::startsWith($routeName, 'inventory.catalog.')) {
            return ['inventory.stock'];
        }
        if (Str::startsWith($routeName, 'inventory.warehouses.')) {
            return ['inventory.warehouses'];
        }

        if (in_array($routeName, ['services.create', 'services.store'], true)) {
            return ['services.create', 'finance.services.create'];
        }
        if (in_array($routeName, ['services.catalog', 'services.edit', 'services.update'], true)) {
            return ['services.catalog', 'finance.services.catalog'];
        }
        if ($routeName === 'services.history') {
            return ['services.history', 'finance.services.history'];
        }
        if (Str::startsWith($routeName, 'services.')) {
            return ['services.months'];
        }

        if (Str::startsWith($routeName, 'construction.')) {
            if (Str::startsWith($routeName, 'construction.providers.')) {
                return ['construction.providers'];
            }
            if (Str::startsWith($routeName, 'construction.payrolls.')) {
                return ['construction.labor'];
            }
            if (Str::startsWith($routeName, 'construction.estimates.')) {
                return ['construction.generators'];
            }
            if (Str::startsWith($routeName, 'construction.payment-orders.')) {
                return ['construction.payments'];
            }
            if ($routeName === 'construction.placeholder') {
                return [match ((string) $request->route('section')) {
                    'generadores-obra' => 'construction.generators',
                    'materiales-insumos' => 'construction.materials',
                    'mano-obra' => 'construction.labor',
                    'calendario' => 'construction.calendar',
                    'pagos' => 'construction.payments',
                    'ordenes-suministro' => 'construction.supply',
                    'almacenes' => 'construction.warehouses',
                    'compras' => 'construction.purchases',
                    'tabulador-precios-unitarios' => 'construction.unit_prices',
                    'proveedores' => 'construction.providers',
                    default => 'construction.dashboard',
                }];
            }

            return ['construction.dashboard'];
        }

        if ($routeName === 'superadmin.plazas.panel') {
            $section = $request->has('section')
                ? PlazaNavigation::normalizeSection($request->query('section'))
                : PlazaNavigation::sectionForLegacyTab($request->query('tab'));

            return [PlazaNavigation::permissionForSection($section)];
        }
        if (Str::startsWith($routeName, 'superadmin.plazas.')) {
            return [PlazaNavigation::permissionForSection(PlazaNavigation::sectionForRoute($routeName))];
        }
        if (Str::startsWith($routeName, 'superadmin.government-contracts.')) {
            return [GovernmentContractNavigation::permissionForSection($request->query('section'))];
        }
        if (Str::startsWith($routeName, 'security.companies.')) {
            return ['security.dashboard'];
        }
        if (Str::startsWith($routeName, 'security.branches.')) {
            return ['security.branches'];
        }
        if ($routeName === 'security.index') {
            return [match ((string) $request->query('section', 'companies')) {
                'branches' => 'security.branches',
                'cameras' => 'security.cameras',
                'analytics' => 'security.analytics',
                'alerts' => 'security.alerts',
                'users' => 'security.users',
                'reports' => 'security.reports',
                'configuration' => 'security.configuration',
                default => 'security.dashboard',
            }];
        }
        if ($routeName === 'human-resources.index') {
            return ['human_resources.dashboard'];
        }

        return [];
    }
}
