<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Control de Obras')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedheader/4.0.1/css/fixedHeader.bootstrap5.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div class="brand">
                <span class="brand-mark"><i data-lucide="hard-hat"></i></span>
                <span>Control de Obras</span>
            </div>

            <nav aria-label="Menu principal">
                <div class="sidebar-section">
                    <button class="sidebar-main" type="button" data-bs-toggle="collapse" data-bs-target="#menuObras" aria-expanded="true">
                        <i data-lucide="building-2"></i>
                        <span>Obras</span>
                    </button>
                    <div class="collapse show" id="menuObras">
                        <a class="sidebar-sub {{ request()->routeIs('obras.index') ? 'active' : '' }}" href="{{ route('obras.index') }}">Panel general</a>
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('administrador-obra'))
                            <a class="sidebar-sub {{ request()->routeIs('obras.create') ? 'active' : '' }}" href="{{ route('obras.create') }}">Nueva obra</a>
                        @endif
                        <a class="sidebar-sub disabled" href="#">Contratos</a>
                        <a class="sidebar-sub disabled" href="#">Presupuestos</a>
                        <a class="sidebar-sub disabled" href="#">Partidas y conceptos</a>
                        <a class="sidebar-sub disabled" href="#">Estimaciones</a>
                        <a class="sidebar-sub disabled" href="#">Avances</a>
                    </div>
                </div>

                <div class="sidebar-section">
                    <button class="sidebar-main" type="button" data-bs-toggle="collapse" data-bs-target="#menuOperacion">
                        <i data-lucide="clipboard-check"></i>
                        <span>Operacion</span>
                    </button>
                    <div class="collapse" id="menuOperacion">
                        <a class="sidebar-sub disabled" href="#">Alcances semanales</a>
                        <a class="sidebar-sub disabled" href="#">Mano de obra</a>
                        <a class="sidebar-sub disabled" href="#">Nomina</a>
                        <a class="sidebar-sub disabled" href="#">Calendario</a>
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole(['administrador-obra', 'supervisor']))
                            <a class="sidebar-sub {{ request()->routeIs('audit.index') ? 'active' : '' }}" href="{{ route('audit.index') }}">Bitacora</a>
                        @else
                            <a class="sidebar-sub disabled" href="#">Bitacora</a>
                        @endif
                        <a class="sidebar-sub disabled" href="#">Fotografias</a>
                        <a class="sidebar-sub disabled" href="#">Incidencias</a>
                        <a class="sidebar-sub disabled" href="#">Cambios</a>
                    </div>
                </div>

                <div class="sidebar-section">
                    <button class="sidebar-main" type="button" data-bs-toggle="collapse" data-bs-target="#menuMateriales">
                        <i data-lucide="package"></i>
                        <span>Materiales</span>
                    </button>
                    <div class="collapse" id="menuMateriales">
                        <a class="sidebar-sub disabled" href="#">Materiales e insumos</a>
                        <a class="sidebar-sub disabled" href="#">Requerimientos</a>
                        <a class="sidebar-sub disabled" href="#">Ordenes de suministro</a>
                        <a class="sidebar-sub disabled" href="#">Almacenes</a>
                        <a class="sidebar-sub disabled" href="#">Compras</a>
                        <a class="sidebar-sub disabled" href="#">Proveedores</a>
                    </div>
                </div>

                <div class="sidebar-section">
                    <button class="sidebar-main" type="button" data-bs-toggle="collapse" data-bs-target="#menuFinanzas">
                        <i data-lucide="wallet-cards"></i>
                        <span>Finanzas</span>
                    </button>
                    <div class="collapse" id="menuFinanzas">
                        <a class="sidebar-sub disabled" href="#">Pagos</a>
                        <a class="sidebar-sub disabled" href="#">Flujo de efectivo</a>
                        <a class="sidebar-sub disabled" href="#">Facturas</a>
                        <a class="sidebar-sub disabled" href="#">Retenciones</a>
                        <a class="sidebar-sub disabled" href="#">Reportes</a>
                    </div>
                </div>

                <div class="sidebar-section">
                    <button class="sidebar-main" type="button" data-bs-toggle="collapse" data-bs-target="#menuAdmin">
                        <i data-lucide="settings"></i>
                        <span>Administracion</span>
                    </button>
                    <div class="collapse" id="menuAdmin">
                        @if(auth()->user()->isSuperAdmin())
                            <a class="sidebar-sub {{ request()->routeIs('users.access.*') ? 'active' : '' }}" href="{{ route('users.access.index') }}">Usuarios y permisos</a>
                        @endif
                        <a class="sidebar-sub disabled" href="#">Documentos</a>
                        <a class="sidebar-sub disabled" href="#">Configuracion</a>
                    </div>
                </div>

                <div class="sidebar-section">
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole(['administrador-obra', 'supervisor']))
                        <a class="sidebar-link {{ request()->routeIs('audit.index') ? 'active' : '' }}" href="{{ route('audit.index') }}">
                            <i data-lucide="clipboard-list"></i>
                            <span>Auditoria</span>
                        </a>
                    @else
                        <span class="sidebar-link disabled">
                            <i data-lucide="clipboard-list"></i>
                            <span>Auditoria</span>
                        </span>
                    @endif
                </div>
            </nav>
        </aside>

        <main class="main-panel">
            <header class="topbar">
                <div class="topbar-heading">
                    <div class="topbar-title-block">
                        <h1>@yield('page-title', 'Control de Obras')</h1>
                        @hasSection('page-subtitle')
                            <p class="page-subtitle">@yield('page-subtitle')</p>
                        @endif
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="user-pill">
                        <i data-lucide="user-round"></i>
                        <span>{{ auth()->user()->name }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-soft" type="submit" title="Cerrar sesion">
                            <i data-lucide="log-out"></i>
                        </button>
                    </form>
                </div>
            </header>

            @if(session('status'))
                <div class="alert alert-success border-0 shadow-sm">{{ session('status') }}</div>
            @endif

            @include('partials.active-project-carousel')

            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.colVis.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/4.0.1/js/dataTables.fixedHeader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
