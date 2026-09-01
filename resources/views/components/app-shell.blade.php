@php
    $roleLabels = [
        'finance' => 'Finanzas',
        'superadmin' => 'Super Administrador',
        'buyer' => 'Compras y Suministros',
        'inventory' => 'Control de inventarios',
        'services' => 'Servicios',
        'administrative_assistant' => 'Asistente Administrativo',
    ];

    $pendingOrdersQuery = \App\Models\PurchaseOrder::query()
        ->general()
        ->whereIn('status', ['sent', 'approved'])
        ->where(function ($query) {
            $query->where('is_credit', false)
                ->orWhereBetween('due_date', [now()->startOfWeek(), now()->copy()->addWeek()->endOfWeek()]);
        });
    $pendingOrderBadgeCount = auth()->user()->role === 'buyer'
        ? (clone $pendingOrdersQuery)->where('buyer_id', auth()->id())->count()
        : (clone $pendingOrdersQuery)->count();
    $constructionPendingOrderBadgeCount = \App\Models\PurchaseOrder::query()
        ->forConstruction()
        ->whereIn('status', ['sent', 'approved'])
        ->where(function ($query) {
            $query->where('is_credit', false)
                ->orWhereBetween('due_date', [now()->startOfWeek(), now()->copy()->addWeek()->endOfWeek()]);
        })
        ->count();
    $pendingSupplyBadgeCount = auth()->user()->role === 'buyer'
        ? \App\Models\SupplyOrder::where('requester_id', auth()->id())->whereIn('status', ['sent', 'approved', 'remitted'])->count()
        : \App\Models\SupplyOrder::whereIn('status', ['sent', 'approved', 'remitted'])->count();
    $inventorySupplyBadgeCount = \App\Models\SupplyOrder::whereIn('status', ['approved', 'remitted'])->count();
    $pendingReimbursementBadgeCount = auth()->user()->role === 'buyer'
        ? \App\Models\ReimbursementOrder::where('requester_id', auth()->id())->whereIn('status', ['sent', 'approved'])->count()
        : \App\Models\ReimbursementOrder::whereIn('status', ['sent', 'approved'])->count();
    $pendingConstructionPaymentOrderBadgeCount = \App\Models\ConstructionPaymentOrder::query()->pending()->count();
    $isConstructionPurchaseContext = auth()->user()->role === 'superadmin' && request('context') === 'construction';
    $homeDashboardPermission = \App\Support\NavigationPermissionCatalog::HOME_DASHBOARD;

    $purchaseNavItems = [
        ['label' => 'Nueva OC', 'url' => route('buyer.orders.create'), 'active' => ! $isConstructionPurchaseContext && request()->routeIs('buyer.orders.create'), 'menu_box' => 'purchases', 'menu_box_start' => true, 'menu_box_title' => 'Ordenes de compra'],
        ['label' => 'Pagadas', 'url' => route('buyer.orders.index', ['panel' => 'paid']), 'active' => ! $isConstructionPurchaseContext && request('panel') === 'paid' && request()->routeIs('buyer.orders.index'), 'menu_box' => 'purchases'],
        ['label' => 'Pendientes de Pago', 'url' => route('buyer.orders.index', ['panel' => 'pending-payment']), 'active' => ! $isConstructionPurchaseContext && request('panel') === 'pending-payment' && request()->routeIs('buyer.orders.index'), 'menu_box' => 'purchases'],
        ['label' => 'Mis Ordenes', 'url' => route('buyer.orders.index'), 'active' => ! $isConstructionPurchaseContext && request()->routeIs('buyer.orders.index') && ! request('panel'), 'pending_badge' => true, 'menu_box' => 'purchases'],
        ['label' => 'Rechazadas', 'url' => route('buyer.orders.index', ['panel' => 'rejected']), 'active' => ! $isConstructionPurchaseContext && request('panel') === 'rejected' && request()->routeIs('buyer.orders.index'), 'menu_box' => 'purchases', 'menu_box_end' => true],
    ];

    $constructionPurchaseNavItems = [
        ['label' => 'Nueva OC', 'url' => route('buyer.orders.create', ['context' => 'construction']), 'active' => $isConstructionPurchaseContext && request()->routeIs('buyer.orders.create')],
        ['label' => 'Pagadas', 'url' => route('buyer.orders.index', ['panel' => 'paid', 'context' => 'construction']), 'active' => $isConstructionPurchaseContext && request()->routeIs('buyer.orders.index') && request('panel') === 'paid'],
        ['label' => 'Pendientes de Pago', 'url' => route('buyer.orders.index', ['panel' => 'pending-payment', 'context' => 'construction']), 'active' => $isConstructionPurchaseContext && request()->routeIs('buyer.orders.index') && request('panel') === 'pending-payment'],
        ['label' => 'Mis Ordenes', 'url' => route('buyer.orders.index', ['context' => 'construction']), 'active' => $isConstructionPurchaseContext && request()->routeIs('buyer.orders.index') && ! request('panel'), 'badge_count' => $constructionPendingOrderBadgeCount],
        ['label' => 'Rechazadas', 'url' => route('buyer.orders.index', ['panel' => 'rejected', 'context' => 'construction']), 'active' => $isConstructionPurchaseContext && request()->routeIs('buyer.orders.index') && request('panel') === 'rejected'],
    ];

    $supplyNavItems = [
        ['label' => 'Nueva OS', 'url' => route('buyer.supply-orders.create'), 'active' => request()->routeIs('buyer.supply-orders.create'), 'menu_box' => 'supplies', 'menu_box_start' => true, 'menu_box_title' => 'Ordenes de suministro'],
        ['label' => 'OS Pendientes', 'url' => route('buyer.supply-orders.index'), 'active' => request()->routeIs('buyer.supply-orders.index') && request('panel') !== 'history', 'badge_count' => $pendingSupplyBadgeCount, 'menu_box' => 'supplies'],
        ['label' => 'OS Historial', 'url' => route('buyer.supply-orders.index', ['panel' => 'history']), 'active' => request()->routeIs('buyer.supply-orders.index') && request('panel') === 'history', 'menu_box' => 'supplies', 'menu_box_end' => true],
    ];

    $reimbursementNavItems = [
        ['label' => 'Nueva OR', 'url' => route('buyer.reimbursement-orders.create'), 'active' => request()->routeIs('buyer.reimbursement-orders.create'), 'menu_box' => 'reimbursements', 'menu_box_start' => true, 'menu_box_title' => 'Ordenes de reembolso'],
        ['label' => 'OR Pendientes', 'url' => route('buyer.reimbursement-orders.index'), 'active' => request()->routeIs('buyer.reimbursement-orders.index') && request('panel') !== 'history', 'badge_count' => $pendingReimbursementBadgeCount, 'menu_box' => 'reimbursements'],
        ['label' => 'OR Historial', 'url' => route('buyer.reimbursement-orders.index', ['panel' => 'history']), 'active' => request()->routeIs('buyer.reimbursement-orders.index') && request('panel') === 'history', 'menu_box' => 'reimbursements', 'menu_box_end' => true],
    ];

    $buyerSubroles = auth()->user()->role === 'buyer' ? auth()->user()->buyerSubroles() : [];
    $buyerNavItems = [
        ['label' => 'Panel de inicio', 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard'), 'permission' => $homeDashboardPermission],
    ];

    if (in_array('purchases', $buyerSubroles, true)) {
        $buyerNavItems = array_merge($buyerNavItems, $purchaseNavItems);
    }

    if (in_array('supplies', $buyerSubroles, true)) {
        $buyerNavItems = array_merge($buyerNavItems, $supplyNavItems);
    }

    if (in_array('reimbursements', $buyerSubroles, true)) {
        $buyerNavItems = array_merge($buyerNavItems, $reimbursementNavItems);
    }

    if (in_array('purchases', $buyerSubroles, true)) {
        $buyerNavItems[] = ['label' => 'Alta de Proveedor', 'url' => route('buyer.providers.index'), 'active' => request()->routeIs('buyer.providers.*')];
    }

    $governmentContractNavItems = collect(\App\Support\GovernmentContractNavigation::items())
        ->map(function (array $item, string $section) {
            $isDefault = $section === \App\Support\GovernmentContractNavigation::defaultSection();

            return [
                'label' => $item['label'],
                'url' => $isDefault
                    ? route('superadmin.government-contracts.index')
                    : route('superadmin.government-contracts.index', ['section' => $section]),
                'active' => request()->routeIs('superadmin.government-contracts.index')
                    && \App\Support\GovernmentContractNavigation::normalizeSection(request('section')) === $section,
                'permission' => $item['permission'],
            ];
        })
        ->values()
        ->all();

    $plazaNavItems = collect(\App\Support\PlazaNavigation::items())
        ->map(function (array $item) {
            return [
                'label' => $item['label'],
                'url' => route($item['route']),
                'active' => request()->routeIs($item['route']),
                'permission' => $item['permission'],
            ];
        })
        ->values()
        ->all();

    $superAdminNavGroups = [
        'Superadministrador' => [
            ['label' => 'Panel de inicio', 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard'), 'permission' => $homeDashboardPermission],
            ['label' => 'Usuarios y Roles', 'url' => route('superadmin.users.index'), 'active' => request()->routeIs('superadmin.users.*')],
            ['label' => 'Proveedores', 'url' => route('superadmin.provider-lines.index'), 'active' => request()->routeIs('superadmin.provider-lines.*')],
        ],
        'Finanzas' => [
            ['label' => 'OC Vigentes', 'url' => route('finance.orders.active'), 'active' => request()->routeIs('finance.orders.active') || request()->routeIs('finance.orders.payment'), 'pending_badge' => true, 'menu_box' => 'oc', 'menu_box_start' => true, 'menu_box_title' => 'Ordenes de compra'],
            ['label' => 'OC Historial', 'url' => route('finance.orders.history'), 'active' => request()->routeIs('finance.orders.history'), 'menu_box' => 'oc', 'menu_box_end' => true],
            ['label' => 'OP Pendientes', 'url' => route('finance.construction-payment-orders.active'), 'active' => request()->routeIs('finance.construction-payment-orders.active'), 'badge_count' => $pendingConstructionPaymentOrderBadgeCount, 'menu_box' => 'op', 'menu_box_start' => true, 'menu_box_title' => 'Ordenes de Pago de Obra'],
            ['label' => 'OP Historial', 'url' => route('finance.construction-payment-orders.history'), 'active' => request()->routeIs('finance.construction-payment-orders.history'), 'menu_box' => 'op', 'menu_box_end' => true],
            ['label' => 'Pago Servicios', 'url' => route('finance.services.index'), 'active' => request()->routeIs('finance.services.*'), 'menu_box' => 'services', 'menu_box_start' => true, 'menu_box_title' => 'Servicios'],
            ['label' => 'Historial de Servicios', 'url' => route('services.history'), 'active' => request()->routeIs('services.history'), 'menu_box' => 'services', 'menu_box_end' => true],
            ['label' => 'OS Vigentes', 'url' => route('finance.supply-orders.active'), 'active' => request()->routeIs('finance.supply-orders.active'), 'badge_count' => $pendingSupplyBadgeCount, 'menu_box' => 'os', 'menu_box_start' => true, 'menu_box_title' => 'Ordenes de suministro'],
            ['label' => 'OS Historial', 'url' => route('finance.supply-orders.history'), 'active' => request()->routeIs('finance.supply-orders.history'), 'menu_box' => 'os', 'menu_box_end' => true],
            ['label' => 'OR Vigentes', 'url' => route('finance.reimbursement-orders.active'), 'active' => request()->routeIs('finance.reimbursement-orders.active'), 'badge_count' => $pendingReimbursementBadgeCount, 'menu_box' => 'or', 'menu_box_start' => true, 'menu_box_title' => 'Ordenes de reembolso'],
            ['label' => 'OR Historial', 'url' => route('finance.reimbursement-orders.history'), 'active' => request()->routeIs('finance.reimbursement-orders.history'), 'menu_box' => 'or', 'menu_box_end' => true],
            ['label' => 'Autorizaciones', 'url' => route('finance.admin.users'), 'active' => request()->routeIs('finance.admin.users')],
            ['label' => 'Alta Proveedores', 'url' => route('finance.admin.providers'), 'active' => request()->routeIs('finance.admin.providers')],
            ['label' => 'Alta Empresas', 'url' => route('finance.admin.companies'), 'active' => request()->routeIs('finance.admin.companies')],
            ['label' => 'Alta Servicio', 'url' => route('services.create'), 'active' => request()->routeIs('services.create')],
            ['label' => 'Catalogo Servicios', 'url' => route('services.catalog'), 'active' => request()->routeIs('services.catalog') || request()->routeIs('services.edit') || request()->routeIs('services.update')],
        ],
        'Compras y Suministros' => array_merge(
            $purchaseNavItems,
            $supplyNavItems,
            $reimbursementNavItems,
            [['label' => 'Alta de Proveedor', 'url' => route('buyer.providers.index'), 'active' => request()->routeIs('buyer.providers.*')]]
        ),
        'Almacenes e Inventarios' => [
            ['label' => 'OC Pagadas', 'url' => route('inventory.orders.index'), 'active' => request()->routeIs('inventory.orders.index') || request()->routeIs('inventory.orders.receipt')],
            ['label' => 'Historial', 'url' => route('inventory.orders.history'), 'active' => request()->routeIs('inventory.orders.history')],
            ['label' => 'OS por Entregar', 'url' => route('inventory.supply-orders.active'), 'active' => request()->routeIs('inventory.supply-orders.*'), 'badge_count' => $inventorySupplyBadgeCount],
            ['label' => 'Inventarios', 'url' => route('inventory.stock.index'), 'active' => request()->routeIs('inventory.stock.*')],
            ['label' => 'Almacenes', 'url' => route('inventory.warehouses.index'), 'active' => request()->routeIs('inventory.warehouses.*')],
        ],
        'Servicios' => [
            ['label' => 'Alta Servicio', 'url' => route('services.create'), 'active' => request()->routeIs('services.create')],
            ['label' => 'Catalogo Servicios', 'url' => route('services.catalog'), 'active' => request()->routeIs('services.catalog')],
            ['label' => 'Vista por Mes', 'url' => route('services.months'), 'active' => request()->routeIs('services.months') || request()->routeIs('services.receipt')],
            ['label' => 'Historial de Servicios', 'url' => route('services.history'), 'active' => request()->routeIs('services.history')],
        ],
        'Recursos Humanos' => [
            ['label' => 'Panel general', 'url' => route('human-resources.index'), 'active' => request()->routeIs('human-resources.index')],
        ],
        'Administracion de obra' => [
            ['label' => 'Panel general', 'url' => route('construction.dashboard'), 'active' => request()->routeIs('construction.dashboard'), 'menu_box' => 'construction-obras', 'menu_box_start' => true, 'menu_box_title' => 'Obras'],
            ['label' => 'Generadores de obra', 'url' => route('construction.placeholder', 'generadores-obra'), 'active' => request()->routeIs('construction.placeholder') && request()->route('section') === 'generadores-obra', 'menu_box' => 'construction-obras'],
            ['label' => 'Materiales e insumos', 'url' => route('construction.placeholder', 'materiales-insumos'), 'active' => request()->routeIs('construction.placeholder') && request()->route('section') === 'materiales-insumos', 'menu_box' => 'construction-obras', 'menu_box_end' => true],

            ['label' => 'Mano de obra', 'url' => route('construction.placeholder', 'mano-obra'), 'active' => request()->routeIs('construction.placeholder') && request()->route('section') === 'mano-obra', 'menu_box' => 'construction-operacion', 'menu_box_start' => true, 'menu_box_title' => 'Operacion'],
            ['label' => 'Calendario', 'url' => route('construction.placeholder', 'calendario'), 'active' => request()->routeIs('construction.placeholder') && request()->route('section') === 'calendario', 'menu_box' => 'construction-operacion'],
            ['label' => 'Historial de pagos', 'url' => route('construction.placeholder', 'pagos'), 'active' => request()->routeIs('construction.placeholder') && request()->route('section') === 'pagos', 'menu_box' => 'construction-operacion', 'menu_box_end' => true],

            ['label' => 'Ordenes de suministro', 'url' => route('construction.placeholder', 'ordenes-suministro'), 'active' => request()->routeIs('construction.placeholder') && request()->route('section') === 'ordenes-suministro', 'menu_box' => 'construction-materiales', 'menu_box_start' => true, 'menu_box_title' => 'Administracion'],
            ['label' => 'Almacenes', 'url' => route('construction.placeholder', 'almacenes'), 'active' => request()->routeIs('construction.placeholder') && request()->route('section') === 'almacenes', 'menu_box' => 'construction-materiales'],
            ['label' => 'Compras', 'url' => route('construction.placeholder', 'compras'), 'active' => (request()->routeIs('construction.placeholder') && request()->route('section') === 'compras') || ($isConstructionPurchaseContext && request()->routeIs('buyer.orders.*')), 'menu_box' => 'construction-materiales', 'children_title' => 'Ordenes de compra', 'children' => $constructionPurchaseNavItems],
            ['label' => 'Tabulador de precios unitarios', 'url' => route('construction.placeholder', 'tabulador-precios-unitarios'), 'active' => request()->routeIs('construction.placeholder') && request()->route('section') === 'tabulador-precios-unitarios', 'menu_box' => 'construction-materiales'],
            ['label' => 'Alta de proveedor', 'url' => route('construction.providers.index'), 'active' => request()->routeIs('construction.providers.*') || (request()->routeIs('construction.placeholder') && request()->route('section') === 'proveedores'), 'menu_box' => 'construction-materiales', 'menu_box_end' => true],

        ],
        'Administracion de Plazas' => $plazaNavItems,
        'Contratos Gobierno' => $governmentContractNavItems,
        'Seguridad y Vigilancia' => [
            ['label' => 'Empresas', 'url' => route('security.index'), 'active' => request()->routeIs('security.index') && in_array(request()->query('section'), [null, '', 'companies'], true)],
            ['label' => 'Sucursales', 'url' => route('security.index', ['section' => 'branches']), 'active' => request()->routeIs('security.index') && request()->query('section') === 'branches'],
            ['label' => 'Analíticos', 'url' => route('security.index', ['section' => 'analytics']), 'active' => request()->routeIs('security.index') && request()->query('section') === 'analytics'],
            ['label' => 'Alertas', 'url' => route('security.index', ['section' => 'alerts']), 'active' => request()->routeIs('security.index') && request()->query('section') === 'alerts'],
            ['label' => 'Usuarios', 'url' => route('security.index', ['section' => 'users']), 'active' => request()->routeIs('security.index') && request()->query('section') === 'users'],
            ['label' => 'Reportes', 'url' => route('security.index', ['section' => 'reports']), 'active' => request()->routeIs('security.index') && request()->query('section') === 'reports'],
            ['label' => 'Cámaras', 'url' => route('security.index', ['section' => 'cameras']), 'active' => request()->routeIs('security.index') && request()->query('section') === 'cameras'],
            ['label' => 'Configuración', 'url' => route('security.index', ['section' => 'configuration']), 'active' => request()->routeIs('security.index') && request()->query('section') === 'configuration'],
        ],
    ];

    $navigationPermissionMap = [
        'Finanzas' => [
            'OC Vigentes' => 'finance.orders.active',
            'OC Historial' => 'finance.orders.history',
            'OP Pendientes' => 'finance.construction.active',
            'OP Historial' => 'finance.construction.history',
            'Pago Servicios' => 'finance.services.payments',
            'Historial de Servicios' => 'finance.services.history',
            'OS Vigentes' => 'finance.supply.active',
            'OS Historial' => 'finance.supply.history',
            'OR Vigentes' => 'finance.reimbursements.active',
            'OR Historial' => 'finance.reimbursements.history',
            'Autorizaciones' => 'finance.authorizations',
            'Alta Proveedores' => 'finance.providers',
            'Alta Empresas' => 'finance.companies',
            'Alta Servicio' => 'finance.services.create',
            'Catalogo Servicios' => 'finance.services.catalog',
        ],
        'Compras y Suministros' => [
            'Nueva OC' => 'procurement.orders.create',
            'Pagadas' => 'procurement.orders.paid',
            'Pendientes de Pago' => 'procurement.orders.pending_payment',
            'Mis Ordenes' => 'procurement.orders.mine',
            'Rechazadas' => 'procurement.orders.rejected',
            'Nueva OS' => 'procurement.supply.create',
            'OS Pendientes' => 'procurement.supply.pending',
            'OS Historial' => 'procurement.supply.history',
            'Nueva OR' => 'procurement.reimbursements.create',
            'OR Pendientes' => 'procurement.reimbursements.pending',
            'OR Historial' => 'procurement.reimbursements.history',
            'Alta de Proveedor' => 'procurement.providers',
        ],
        'Almacenes e Inventarios' => [
            'OC Pagadas' => 'inventory.orders.paid',
            'Historial' => 'inventory.orders.history',
            'OS por Entregar' => 'inventory.supply.active',
            'Inventarios' => 'inventory.stock',
            'Almacenes' => 'inventory.warehouses',
        ],
        'Servicios' => [
            'Alta Servicio' => 'services.create',
            'Catalogo Servicios' => 'services.catalog',
            'Vista por Mes' => 'services.months',
            'Historial de Servicios' => 'services.history',
        ],
        'Recursos Humanos' => ['Panel general' => 'human_resources.dashboard'],
        'Administracion de obra' => [
            'Panel general' => 'construction.dashboard',
            'Generadores de obra' => 'construction.generators',
            'Materiales e insumos' => 'construction.materials',
            'Mano de obra' => 'construction.labor',
            'Calendario' => 'construction.calendar',
            'Historial de pagos' => 'construction.payments',
            'Ordenes de suministro' => 'construction.supply',
            'Almacenes' => 'construction.warehouses',
            'Compras' => 'construction.purchases',
            'Tabulador de precios unitarios' => 'construction.unit_prices',
            'Alta de proveedor' => 'construction.providers',
        ],
        'Administracion de Plazas' => [
            'Panel general' => 'plazas.dashboard',
            'Administracion y Cobranza' => 'plazas.administration',
            'Contratos' => 'plazas.contracts',
            'Marketplace' => 'plazas.marketplace',
            'Arrendatarios' => 'plazas.tenants',
            'Catalogo de unidades' => 'plazas.properties',
            'Alta de Usuarios' => 'plazas.users',
        ],
        'Seguridad y Vigilancia' => [
            'Empresas' => 'security.dashboard',
            'Sucursales' => 'security.branches',
            'Analíticos' => 'security.analytics',
            'Alertas' => 'security.alerts',
            'Usuarios' => 'security.users',
            'Reportes' => 'security.reports',
            'Cámaras' => 'security.cameras',
            'Configuración' => 'security.configuration',
        ],
    ];

    $superAdminNavGroups = collect($superAdminNavGroups)
        ->map(fn (array $items, string $groupLabel) => collect($items)
            ->map(function (array $item) use ($navigationPermissionMap, $groupLabel) {
                $item['permission'] = $item['permission'] ?? ($navigationPermissionMap[$groupLabel][$item['label']] ?? null);

                return $item;
            })
            ->all())
        ->all();

    $currentUser = auth()->user();
    $visibleNavGroups = $currentUser->role === 'superadmin'
        ? $superAdminNavGroups
        : collect($superAdminNavGroups)
            ->except('Superadministrador')
            ->map(fn (array $items) => collect($items)
                ->filter(fn (array $item) => filled($item['permission']) && $currentUser->canNavigateTo($item['permission']))
                ->map(function (array $item) {
                    unset($item['menu_box'], $item['menu_box_start'], $item['menu_box_end'], $item['menu_box_title']);

                    return $item;
                })
                ->values()
                ->all())
            ->filter()
            ->all();
    $suppressGlobalMessages = request()->routeIs('security.index')
        && request()->query('section') === 'branches';
@endphp

<div class="app-shell">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-mark">OC</div>
            <div>
                <strong>OC System</strong>
                <span>{{ auth()->user()->role === 'buyer' ? auth()->user()->buyerSubroleLabel() : ($roleLabels[auth()->user()->role] ?? 'Usuario') }}</span>
            </div>
        </div>
        <nav class="nav-list" aria-label="Menu principal">
            @if ($currentUser->role !== 'superadmin' && $currentUser->canNavigateTo($homeDashboardPermission))
                <a class="button nav-button {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Panel de inicio</a>
            @endif
            @foreach ($visibleNavGroups as $groupLabel => $items)
                    @php
                        $groupIsActive = collect($items)->contains(fn ($item) => $item['active']);
                    @endphp
                    <details class="nav-group" {{ $groupIsActive ? 'open' : '' }}>
                        <summary>{{ $groupLabel }}</summary>
                        <div class="nav-sublist">
                            @foreach ($items as $item)
                                @php $badgeCount = $item['badge_count'] ?? (($item['pending_badge'] ?? false) ? $pendingOrderBadgeCount : null); @endphp
                                @if ($item['menu_box_start'] ?? false)
                                    <div class="nav-box nav-box-{{ $item['menu_box'] ?? 'default' }}">
                                        @if ($item['menu_box_title'] ?? false)
                                            <span class="nav-box-title">{{ $item['menu_box_title'] }}</span>
                                        @endif
                                @endif
                                <a class="button nav-button sub-nav-button {{ $item['active'] ? 'active' : '' }}" href="{{ $item['url'] }}">{{ $item['label'] }}@if ($badgeCount !== null)<span class="nav-pending-badge {{ $badgeCount ? '' : 'is-empty' }}">{{ $badgeCount }}</span>@endif</a>
                                @if (($item['children'] ?? []) && $item['active'])
                                    <div class="nav-inline-flow">
                                        <span class="nav-box-title">{{ $item['children_title'] ?? 'Opciones' }}</span>
                                        @foreach ($item['children'] as $child)
                                            @php $childBadgeCount = $child['badge_count'] ?? (($child['pending_badge'] ?? false) ? $pendingOrderBadgeCount : null); @endphp
                                            <a class="button nav-button sub-nav-button {{ $child['active'] ? 'active' : '' }}" href="{{ $child['url'] }}">{{ $child['label'] }}@if ($childBadgeCount !== null)<span class="nav-pending-badge {{ $childBadgeCount ? '' : 'is-empty' }}">{{ $childBadgeCount }}</span>@endif</a>
                                        @endforeach
                                    </div>
                                @endif
                                @if ($item['menu_box_end'] ?? false)
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </details>
            @endforeach
        </nav>
    </aside>

    <main class="content-shell">
        <header class="topbar">
            <div>
                <p class="eyebrow">{{ auth()->user()->role === 'buyer' ? auth()->user()->buyerSubroleLabel() : ($roleLabels[auth()->user()->role] ?? 'Usuario') }}</p>
                <h1>{{ $title }}</h1>
            </div>
            <div class="topbar-right">
                @isset($actions)
                    <div class="topbar-actions">{{ $actions }}</div>
                @endisset
                <div class="user-pill">
                    <span>{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="button ghost small" type="submit">Salir</button>
                    </form>
                </div>
            </div>
        </header>

        <section class="view">
            @if (! $suppressGlobalMessages && session('status'))
                <div class="alert">{{ session('status') }}</div>
            @endif

            @if (! $suppressGlobalMessages && $errors->any())
                <div class="error-list">
                    <strong>Revisa los datos capturados.</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </section>
    </main>
</div>
