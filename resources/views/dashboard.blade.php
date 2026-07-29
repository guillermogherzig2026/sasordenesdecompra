@extends('layouts.app')

@section('body')
    <x-app-shell title="Resumen operativo">
        <div class="metrics-grid">
            @if ($user->role === 'superadmin')
                <article class="metric-card">
                    <span>Usuarios</span>
                    <strong>{{ $usersCount }}</strong>
                    <small>Cuentas registradas</small>
                </article>
                <article class="metric-card">
                    <span>Activos</span>
                    <strong>{{ $activeUsersCount }}</strong>
                    <small>Pueden iniciar sesion</small>
                </article>
                <article class="metric-card">
                    <span>Roles</span>
                    <strong>{{ $rolesCount }}</strong>
                    <small>Perfiles operativos usados</small>
                </article>
                <article class="metric-card">
                    <span>Empresas</span>
                    <strong>{{ $companiesCount }}</strong>
                    <small>Asignables a compradores</small>
                </article>
            @elseif ($user->role === 'finance')
                <article class="metric-card">
                    <span>OC pendientes</span>
                    <strong>{{ $financeSentCount }}</strong>
                    <small>Esperan revision de Finanzas</small>
                </article>
                <article class="metric-card">
                    <span>Aprobadas</span>
                    <strong>{{ $financeApprovedCount }}</strong>
                    <small>Pendientes de pago</small>
                </article>
                <article class="metric-card">
                    <span>Monto pendiente</span>
                    <strong>${{ number_format((float) $financePendingAmount, 0) }}</strong>
                    <small>Ordenado por vencimiento</small>
                </article>
                <article class="metric-card">
                    <span>Monto total</span>
                    <strong>${{ number_format((float) $financeCurrentMonthTotal, 0) }}</strong>
                    <small>Mes en curso: {{ $currentMonthLabel }}</small>
                </article>
            @elseif ($user->role === 'buyer')
                <article class="metric-card">
                    <span>Enviadas</span>
                    <strong>{{ $buyerSentCount }}</strong>
                    <small>Antes de aprobacion</small>
                </article>
                <article class="metric-card">
                    <span>Aprobadas</span>
                    <strong>{{ $buyerApprovedCount }}</strong>
                    <small>Listas para pago</small>
                </article>
                <article class="metric-card">
                    <span>Pagadas</span>
                    <strong>{{ $buyerPaidCount }}</strong>
                    <small>Con archivo de pago</small>
                </article>
                <article class="metric-card">
                    <span>Monto total</span>
                    <strong>${{ number_format((float) $buyerCurrentMonthTotal, 0) }}</strong>
                    <small>Mes en curso: {{ $currentMonthLabel }}</small>
                </article>
            @elseif ($user->role === 'inventory')
                <article class="metric-card">
                    <span>Pendientes</span>
                    <strong>{{ $inventoryPendingCount }}</strong>
                    <small>Sin comprobacion</small>
                </article>
                <article class="metric-card">
                    <span>Parciales</span>
                    <strong>{{ $inventoryPartialCount }}</strong>
                    <small>Recepcion incompleta</small>
                </article>
                <article class="metric-card">
                    <span>Completadas</span>
                    <strong>{{ $inventoryCompletedCount }}</strong>
                    <small>Cantidad recibida completa</small>
                </article>
                <article class="metric-card">
                    <span>Monto completado</span>
                    <strong>${{ number_format((float) $inventoryCompletedAmount, 0) }}</strong>
                    <small>Recepciones cerradas</small>
                </article>
            @elseif (in_array($user->role, ['services', 'administrative_assistant'], true))
                <article class="metric-card">
                    <span>Servicios activos</span>
                    <strong>{{ $servicesCount }}</strong>
                    <small>Generan pagos recurrentes</small>
                </article>
                <article class="metric-card">
                    <span>Por pagar este mes</span>
                    <strong>{{ $servicesDueThisMonthCount }}</strong>
                    <small>Fechas de corte del mes</small>
                </article>
                <article class="metric-card">
                    <span>Monto del mes</span>
                    <strong>${{ number_format((float) $servicesMonthAmount, 0) }}</strong>
                    <small>{{ $currentMonthLabel }}</small>
                </article>
                <article class="metric-card">
                    <span>Recibos cargados</span>
                    <strong>{{ $servicesReceiptsLoadedCount }}</strong>
                    <small>Soporte para Finanzas</small>
                </article>
            @else
                <article class="metric-card">
                    <span>Servicios activos</span>
                    <strong>{{ $servicesCount }}</strong>
                    <small>Generan pagos recurrentes</small>
                </article>
                <article class="metric-card">
                    <span>Total OC</span>
                    <strong>{{ $ordersCount }}</strong>
                    <small>Referencia general del sistema</small>
                </article>
            @endif

            @if (! in_array($user->role, ['superadmin', 'finance', 'buyer', 'inventory', 'services', 'administrative_assistant'], true))
                <article class="metric-card">
                    <span>Total OC</span>
                    <strong>{{ $ordersCount }}</strong>
                    <small>Ordenes registradas en el sistema</small>
                </article>
                <article class="metric-card">
                    <span>Auditoria</span>
                    <strong>{{ $auditLogs->count() }}</strong>
                    <small>Ultimos movimientos visibles</small>
                </article>
            @endif
        </div>

        @if ($user->role === 'superadmin')
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2>Gestion de accesos</h2>
                        <p class="fine-print">Crea usuarios, asigna roles, limita empresas para compradores y activa o desactiva cuentas.</p>
                    </div>
                    <a class="button primary" href="{{ route('superadmin.users.index') }}">Usuarios y Roles</a>
                </div>
            </section>

            <section class="panel">
                <h2>Auditoria reciente</h2>
                <ul class="audit-list">
                    @forelse ($auditLogs as $entry)
                        <li>
                            <strong>{{ $entry->action }}</strong>
                            {{ $entry->description }}
                            <small>{{ $entry->user?->name ?? 'Sistema' }} &middot; {{ $entry->created_at->format('d/m/Y H:i') }}</small>
                        </li>
                    @empty
                        <li>Sin movimientos registrados.</li>
                    @endforelse
                </ul>
            </section>
        @elseif ($user->role === 'finance')
        @elseif ($user->role === 'buyer')
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2>Nueva orden de compra</h2>
                        <p class="fine-print">Captura proveedor, fechas, empresa autorizada y articulos. La OC se envia a Finanzas automaticamente.</p>
                    </div>
                    <a class="button primary" href="{{ route('buyer.orders.create') }}">Crear OC</a>
                </div>
            </section>

        @elseif ($user->role === 'inventory')
        @elseif (in_array($user->role, ['services', 'administrative_assistant'], true))
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2>Seguimiento de servicios</h2>
                        <p class="fine-print">Da de alta servicios recurrentes, revisa fechas de corte y adjunta facturas por periodo.</p>
                    </div>
                    <a class="button primary" href="{{ route('services.create') }}">Alta servicio</a>
                </div>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <h2>Auditoria reciente</h2>
                    <p class="fine-print">Cada cambio genera una notificacion interna simulada.</p>
                </div>
                <ul class="audit-list">
                    @forelse ($orderAuditLogs as $entry)
                        <li>
                            <strong>{{ $entry->auditable?->folio }}</strong>
                            {{ $entry->description }}
                            <small>{{ $entry->user?->name ?? 'Sistema' }} &middot; {{ $entry->created_at->format('d/m/Y, h:i a') }}</small>
                        </li>
                    @empty
                        <li>Sin movimientos registrados.</li>
                    @endforelse
                </ul>
            </section>
        @else
            <section class="panel">
                <div>
                    <h2>Base Laravel lista</h2>
                    <p class="fine-print">
                        Esta pantalla ya usa autenticacion, roles y datos persistentes en MySQL.
                        El modulo Compras y Suministros ya tiene navegacion real para OC, OS, OR y proveedores.
                    </p>
                </div>
            </section>

            <section class="panel">
                <h2>Auditoria reciente</h2>
                <ul class="audit-list">
                    @forelse ($auditLogs as $entry)
                        <li>
                            <strong>{{ $entry->action }}</strong>
                            {{ $entry->description }}
                            <small>{{ $entry->user?->name ?? 'Sistema' }} &middot; {{ $entry->created_at->format('d/m/Y H:i') }}</small>
                        </li>
                    @empty
                        <li>Sin movimientos registrados.</li>
                    @endforelse
                </ul>
            </section>
        @endif
    </x-app-shell>
@endsection
