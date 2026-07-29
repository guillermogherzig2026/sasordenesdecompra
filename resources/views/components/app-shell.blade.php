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
        ->whereIn('status', ['sent', 'approved'])
        ->where(function ($query) {
            $query->where('is_credit', false)
                ->orWhereBetween('due_date', [now()->startOfWeek(), now()->copy()->addWeek()->endOfWeek()]);
        });
    $pendingOrderBadgeCount = auth()->user()->role === 'buyer'
        ? (clone $pendingOrdersQuery)->where('buyer_id', auth()->id())->count()
        : (clone $pendingOrdersQuery)->count();
    $pendingSupplyBadgeCount = auth()->user()->role === 'buyer'
        ? \App\Models\SupplyOrder::where('requester_id', auth()->id())->whereIn('status', ['sent', 'approved', 'remitted'])->count()
        : \App\Models\SupplyOrder::whereIn('status', ['sent', 'approved', 'remitted'])->count();
    $inventorySupplyBadgeCount = \App\Models\SupplyOrder::whereIn('status', ['approved', 'remitted'])->count();
    $pendingReimbursementBadgeCount = auth()->user()->role === 'buyer'
        ? \App\Models\ReimbursementOrder::where('requester_id', auth()->id())->whereIn('status', ['sent', 'approved'])->count()
        : \App\Models\ReimbursementOrder::whereIn('status', ['sent', 'approved'])->count();

    $purchaseNavItems = [
        ['label' => 'Nueva OC', 'url' => route('buyer.orders.create'), 'active' => request()->routeIs('buyer.orders.create'), 'menu_box' => 'purchases', 'menu_box_start' => true, 'menu_box_title' => 'Ordenes de compra'],
        ['label' => 'Pagadas', 'url' => route('buyer.orders.index', ['panel' => 'paid']), 'active' => request('panel') === 'paid' && request()->routeIs('buyer.orders.index'), 'menu_box' => 'purchases'],
        ['label' => 'Pendientes de Pago', 'url' => route('buyer.orders.index', ['panel' => 'pending-payment']), 'active' => request('panel') === 'pending-payment' && request()->routeIs('buyer.orders.index'), 'menu_box' => 'purchases'],
        ['label' => 'Mis Ordenes', 'url' => route('buyer.orders.index'), 'active' => request()->routeIs('buyer.orders.index') && ! request('panel'), 'pending_badge' => true, 'menu_box' => 'purchases'],
        ['label' => 'Rechazadas', 'url' => route('buyer.orders.index', ['panel' => 'rejected']), 'active' => request('panel') === 'rejected' && request()->routeIs('buyer.orders.index'), 'menu_box' => 'purchases', 'menu_box_end' => true],
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
        ['label' => 'Dashboard', 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
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

    $superAdminNavGroups = [
        'Administracion' => [
            ['label' => 'Dashboard', 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
            ['label' => 'Usuarios y Roles', 'url' => route('superadmin.users.index'), 'active' => request()->routeIs('superadmin.users.*')],
            ['label' => 'Giros Proveeduria', 'url' => route('superadmin.provider-lines.index'), 'active' => request()->routeIs('superadmin.provider-lines.*')],
        ],
        'Finanzas' => [
            ['label' => 'OC Vigentes', 'url' => route('finance.orders.active'), 'active' => request()->routeIs('finance.orders.active') || request()->routeIs('finance.orders.payment'), 'pending_badge' => true, 'menu_box' => 'oc', 'menu_box_start' => true, 'menu_box_title' => 'Ordenes de compra'],
            ['label' => 'OC Historial', 'url' => route('finance.orders.history'), 'active' => request()->routeIs('finance.orders.history'), 'menu_box' => 'oc', 'menu_box_end' => true],
            ['label' => 'Pago Servicios', 'url' => route('finance.services.index'), 'active' => request()->routeIs('finance.services.*'), 'menu_box' => 'services', 'menu_box_start' => true, 'menu_box_title' => 'Servicios'],
            ['label' => 'Alta Servicio', 'url' => route('services.create'), 'active' => request()->routeIs('services.create'), 'menu_box' => 'services'],
            ['label' => 'Catalogo Servicios', 'url' => route('services.catalog'), 'active' => request()->routeIs('services.catalog') || request()->routeIs('services.edit') || request()->routeIs('services.update'), 'menu_box' => 'services', 'menu_box_end' => true],
            ['label' => 'OS Vigentes', 'url' => route('finance.supply-orders.active'), 'active' => request()->routeIs('finance.supply-orders.active'), 'badge_count' => $pendingSupplyBadgeCount, 'menu_box' => 'os', 'menu_box_start' => true, 'menu_box_title' => 'Ordenes de suministro'],
            ['label' => 'OS Historial', 'url' => route('finance.supply-orders.history'), 'active' => request()->routeIs('finance.supply-orders.history'), 'menu_box' => 'os', 'menu_box_end' => true],
            ['label' => 'OR Vigentes', 'url' => route('finance.reimbursement-orders.active'), 'active' => request()->routeIs('finance.reimbursement-orders.active'), 'badge_count' => $pendingReimbursementBadgeCount, 'menu_box' => 'or', 'menu_box_start' => true, 'menu_box_title' => 'Ordenes de reembolso'],
            ['label' => 'OR Historial', 'url' => route('finance.reimbursement-orders.history'), 'active' => request()->routeIs('finance.reimbursement-orders.history'), 'menu_box' => 'or', 'menu_box_end' => true],
            ['label' => 'Autorizaciones', 'url' => route('finance.admin.users'), 'active' => request()->routeIs('finance.admin.users')],
            ['label' => 'Alta Proveedores', 'url' => route('finance.admin.providers'), 'active' => request()->routeIs('finance.admin.providers')],
            ['label' => 'Alta Empresas', 'url' => route('finance.admin.companies'), 'active' => request()->routeIs('finance.admin.companies')],
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
        ],
    ];

    $navItems = match (auth()->user()->role) {
        'superadmin' => [],
        'finance' => [
            ['label' => 'Dashboard', 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
            ['label' => 'OC Vigentes', 'url' => route('finance.orders.active'), 'active' => request()->routeIs('finance.orders.active') || request()->routeIs('finance.orders.payment'), 'pending_badge' => true, 'menu_box' => 'oc', 'menu_box_start' => true, 'menu_box_title' => 'Ordenes de compra'],
            ['label' => 'OC Historial', 'url' => route('finance.orders.history'), 'active' => request()->routeIs('finance.orders.history'), 'menu_box' => 'oc', 'menu_box_end' => true],
            ['label' => 'Pago Servicios', 'url' => route('finance.services.index'), 'active' => request()->routeIs('finance.services.*'), 'menu_box' => 'services', 'menu_box_start' => true, 'menu_box_title' => 'Servicios'],
            ['label' => 'Alta Servicio', 'url' => route('services.create'), 'active' => request()->routeIs('services.create'), 'menu_box' => 'services'],
            ['label' => 'Catalogo Servicios', 'url' => route('services.catalog'), 'active' => request()->routeIs('services.catalog') || request()->routeIs('services.edit') || request()->routeIs('services.update'), 'menu_box' => 'services', 'menu_box_end' => true],
            ['label' => 'OS Vigentes', 'url' => route('finance.supply-orders.active'), 'active' => request()->routeIs('finance.supply-orders.active'), 'badge_count' => $pendingSupplyBadgeCount, 'menu_box' => 'os', 'menu_box_start' => true, 'menu_box_title' => 'Ordenes de suministro'],
            ['label' => 'OS Historial', 'url' => route('finance.supply-orders.history'), 'active' => request()->routeIs('finance.supply-orders.history'), 'menu_box' => 'os', 'menu_box_end' => true],
            ['label' => 'OR Vigentes', 'url' => route('finance.reimbursement-orders.active'), 'active' => request()->routeIs('finance.reimbursement-orders.active'), 'badge_count' => $pendingReimbursementBadgeCount, 'menu_box' => 'or', 'menu_box_start' => true, 'menu_box_title' => 'Ordenes de reembolso'],
            ['label' => 'OR Historial', 'url' => route('finance.reimbursement-orders.history'), 'active' => request()->routeIs('finance.reimbursement-orders.history'), 'menu_box' => 'or', 'menu_box_end' => true],
            ['label' => 'Autorizaciones', 'url' => route('finance.admin.users'), 'active' => request()->routeIs('finance.admin.users')],
            ['label' => 'Alta Proveedores', 'url' => route('finance.admin.providers'), 'active' => request()->routeIs('finance.admin.providers')],
            ['label' => 'Alta Empresas', 'url' => route('finance.admin.companies'), 'active' => request()->routeIs('finance.admin.companies')],
        ],
        'buyer' => $buyerNavItems,
        'inventory' => [
            ['label' => 'Dashboard', 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
            ['label' => 'OC Pagadas', 'url' => route('inventory.orders.index'), 'active' => request()->routeIs('inventory.orders.index') || request()->routeIs('inventory.orders.receipt')],
            ['label' => 'Historial', 'url' => route('inventory.orders.history'), 'active' => request()->routeIs('inventory.orders.history')],
            ['label' => 'OS por Entregar', 'url' => route('inventory.supply-orders.active'), 'active' => request()->routeIs('inventory.supply-orders.*'), 'badge_count' => $inventorySupplyBadgeCount],
            ['label' => 'Inventarios', 'url' => route('inventory.stock.index'), 'active' => request()->routeIs('inventory.stock.*')],
            ['label' => 'Almacenes', 'url' => route('inventory.warehouses.index'), 'active' => request()->routeIs('inventory.warehouses.*')],
        ],
        'services' => [
            ['label' => 'Dashboard', 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
            ['label' => 'Alta Servicio', 'url' => route('services.create'), 'active' => request()->routeIs('services.create')],
            ['label' => 'Catalogo Servicios', 'url' => route('services.catalog'), 'active' => request()->routeIs('services.catalog')],
            ['label' => 'Vista por Meses', 'url' => route('services.months'), 'active' => request()->routeIs('services.months') || request()->routeIs('services.receipt')],
        ],
        'administrative_assistant' => [
            ['label' => 'Dashboard', 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
            ['label' => 'Alta Servicio', 'url' => route('services.create'), 'active' => request()->routeIs('services.create')],
            ['label' => 'Catalogo Servicios', 'url' => route('services.catalog'), 'active' => request()->routeIs('services.catalog')],
            ['label' => 'Vista por Mes', 'url' => route('services.months'), 'active' => request()->routeIs('services.months') || request()->routeIs('services.receipt')],
        ],
        default => [
            ['label' => 'Dashboard', 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
        ],
    };
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
            @if (auth()->user()->role === 'superadmin')
                @foreach ($superAdminNavGroups as $groupLabel => $items)
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
                                @if ($item['menu_box_end'] ?? false)
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </details>
                @endforeach
            @else
                @foreach ($navItems as $item)
                    @php $badgeCount = $item['badge_count'] ?? (($item['pending_badge'] ?? false) ? $pendingOrderBadgeCount : null); @endphp
                    @if ($item['menu_box_start'] ?? false)
                        <div class="nav-box nav-box-{{ $item['menu_box'] ?? 'default' }}">
                            @if ($item['menu_box_title'] ?? false)
                                <span class="nav-box-title">{{ $item['menu_box_title'] }}</span>
                            @endif
                    @endif
                    <a class="button nav-button {{ $item['active'] ? 'active' : '' }}" href="{{ $item['url'] }}">{{ $item['label'] }}@if ($badgeCount !== null)<span class="nav-pending-badge {{ $badgeCount ? '' : 'is-empty' }}">{{ $badgeCount }}</span>@endif</a>
                    @if ($item['menu_box_end'] ?? false)
                        </div>
                    @endif
                @endforeach
            @endif
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
            @if (session('status'))
                <div class="alert">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
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
