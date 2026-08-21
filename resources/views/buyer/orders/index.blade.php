@extends('layouts.app')

@php
    $titles = [
        'all' => 'Mis ordenes de compra',
        'paid' => 'Ordenes pagadas',
        'pending-payment' => 'Pendientes de pago',
        'rejected' => 'Ordenes rechazadas',
    ];

    $descriptions = [
        'all' => 'Ordenes capturadas de la mas reciente a la mas antigua.',
        'paid' => 'Ordenes de compra pagadas, ordenadas por fecha de pago de mas reciente a mas antigua.',
        'pending-payment' => 'Ordenes que aun no cambian a estatus Pagada.',
        'rejected' => 'Ordenes de compra rechazadas por Finanzas.',
    ];

    $originalBuyerPanels = ['paid', 'pending-payment', 'rejected'];
    $emptyMessages = [
        'paid' => 'No tienes ordenes pagadas.',
        'pending-payment' => 'No tienes ordenes pendientes de pago.',
        'rejected' => 'No tienes ordenes rechazadas.',
    ];

    $constructionContext = $constructionContext ?? false;
    $routeContext = $constructionContext ? ['context' => 'construction'] : [];
    $orderRoute = fn (string $name, $order) => route($name, array_merge(['purchaseOrder' => $order], $routeContext));

    if ($constructionContext) {
        $titles = [
            'all' => 'Mis ordenes de compra de obra',
            'paid' => 'Ordenes de obra pagadas',
            'pending-payment' => 'Pendientes de pago de obra',
            'rejected' => 'Ordenes de obra rechazadas',
        ];
        $descriptions = [
            'all' => 'Ordenes de obra capturadas de la mas reciente a la mas antigua.',
            'paid' => 'Ordenes pagadas vinculadas a proyectos de obra.',
            'pending-payment' => 'Ordenes de obra que aun no cambian a estatus Pagada.',
            'rejected' => 'Ordenes de obra rechazadas.',
        ];
        $emptyMessages = [
            'paid' => 'No hay ordenes de obra pagadas.',
            'pending-payment' => 'No hay ordenes de obra pendientes de pago.',
            'rejected' => 'No hay ordenes de obra rechazadas.',
        ];
    }
@endphp

@section('body')
    <x-app-shell :title="$titles[$panel] ?? 'Mis ordenes de compra'">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>{{ $titles[$panel] ?? 'Mis ordenes de compra' }}</h2>
                    <p class="fine-print">{{ $descriptions[$panel] ?? $descriptions['all'] }}</p>
                </div>
                @if (in_array($panel, $originalBuyerPanels, true))
                    <form class="toolbar" method="GET" action="{{ route('buyer.orders.index', $routeContext) }}">
                        <input type="hidden" name="panel" value="{{ $panel }}">
                        @if ($constructionContext)<input type="hidden" name="context" value="construction">@endif
                        <input name="q" value="{{ $query }}" placeholder="Buscar orden...">
                    </form>
                @else
                    <div class="item-actions">
                        <a class="button ghost" href="{{ route('reports.download', array_merge(['type' => 'buyer-items-excel'], $routeContext)) }}">Exportar Excel</a>
                        <a class="button primary" href="{{ route('buyer.orders.create', $routeContext) }}">Nueva OC</a>
                    </div>
                @endif
            </div>

            @if (! in_array($panel, $originalBuyerPanels, true))
                <form class="toolbar" method="GET" action="{{ route('buyer.orders.index', $routeContext) }}">
                    @if ($panel !== 'all')
                        <input type="hidden" name="panel" value="{{ $panel }}">
                    @endif
                    @if ($constructionContext)<input type="hidden" name="context" value="construction">@endif
                    <input name="q" value="{{ $query }}" placeholder="Buscar por OC, empresa o proveedor">
                    <button class="button ghost" type="submit">Buscar</button>
                </form>
            @endif

            <div class="table-scroll">
                @if (in_array($panel, $originalBuyerPanels, true))
                    <table data-column-filter-table>
                        <thead>
                            <tr>
                                <th data-filter-column="0"><span># OC</span></th>
                                <th data-filter-column="1"><span>Empresa</span></th>
                                <th data-filter-column="2"><span>Proveedor</span></th>
                                <th data-filter-column="3"><span>Monto</span></th>
                                <th data-filter-column="4"><span>Estado</span></th>
                                <th data-filter-column="5"><span>Fecha envio</span></th>
                                <th data-filter-column="6"><span>Entrega</span></th>
                                <th data-filter-column="7"><span>Pago</span></th>
                                <th data-filter-column="8"><span>Almacen receptor</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                @php
                                    $buyerStatus = \App\Support\UiStatus::purchaseOrder($order->status, 'buyer');
                                    $buyerStatusClass = \App\Support\UiStatus::purchaseOrderClass($order->status, 'buyer');
                                    $paymentFilter = $order->payment ? ($order->payment->original_name . ' ' . $order->payment->paid_on?->format('d/m/Y')) : 'Sin pago';
                                @endphp
                                <tr data-filter-row>
                                    <td data-filter-value="{{ $order->folio }}">
                                        <strong>
                                            <a href="{{ $orderRoute('buyer.orders.print', $order) }}" target="_blank">{{ $order->folio }}</a>
                                        </strong>
                                        @if ($constructionContext && $order->constructionProject)
                                            <small class="fine-print">{{ $order->constructionProject->project_key }} - {{ $order->constructionProject->name }}</small>
                                        @endif
                                    </td>
                                    <td data-filter-value="{{ $order->company->name }}">{{ $order->company->name }}</td>
                                    <td data-filter-value="{{ $order->provider->business_name }}">{{ $order->provider->business_name }}</td>
                                    <td data-filter-value="${{ number_format((float) $order->total, 0) }}">${{ number_format((float) $order->total, 0) }}</td>
                                    <td data-filter-value="{{ $buyerStatus }}">
                                        @if ($panel === 'paid' && $order->payment)
                                            <a class="status {{ $buyerStatusClass }}" href="{{ $orderRoute('buyer.orders.payment-receipt', $order) }}" target="_blank" title="Descargar comprobante de pago">
                                                {{ $buyerStatus }}
                                            </a>
                                        @else
                                            <details class="status-menu">
                                                <summary class="status {{ $buyerStatusClass }}">{{ $buyerStatus }}</summary>
                                                <div class="status-menu-panel">
                                                    @if ($order->isEditableByBuyer())
                                                        <a class="button ghost small" href="{{ $orderRoute('buyer.orders.edit', $order) }}">Editar</a>
                                                        <form class="inline-form" method="POST" action="{{ $orderRoute('buyer.orders.cancel', $order) }}" onsubmit="return confirm('Cancelar {{ $order->folio }}?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button class="button danger small" type="submit">Cancelar</button>
                                                        </form>
                                                    @else
                                                        <span class="fine-print">Sin acciones</span>
                                                    @endif
                                                </div>
                                            </details>
                                        @endif
                                    </td>
                                    <td data-filter-value="{{ ($order->created_on ?? $order->created_at)?->format('d/m/Y') }}">{{ ($order->created_on ?? $order->created_at)?->format('d/m/Y') }}</td>
                                    <td data-filter-value="{{ $order->delivery_date?->format('d/m/Y') }}">{{ $order->delivery_date?->format('d/m/Y') }}</td>
                                    <td data-filter-value="{{ $paymentFilter }}">
                                        @if ($order->payment)
                                            <a class="attachment-pill" href="{{ $orderRoute('buyer.orders.payment-receipt', $order) }}" target="_blank">
                                                <span>Adjunto</span>{{ $order->payment->original_name }}
                                            </a>
                                            <div class="fine-print">{{ $order->payment->paid_on?->format('d/m/Y') }}</div>
                                        @else
                                            Sin pago
                                        @endif
                                    </td>
                                    <td data-filter-value="{{ $order->warehouse ?: 'Sin almacen asignado' }}">{{ $order->warehouse ?: 'Sin almacen asignado' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">{{ $emptyMessages[$panel] ?? 'No hay ordenes para mostrar.' }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @else
                    <table data-column-filter-table>
                        <thead>
                            <tr>
                                <th data-filter-column="0"><span>OC</span></th>
                                <th data-filter-column="1"><span>Fecha envio</span></th>
                                <th data-filter-column="2"><span>Empresa</span></th>
                                <th data-filter-column="3"><span>Proveedor</span></th>
                                <th data-filter-column="4"><span>Monto</span></th>
                                <th data-filter-column="5"><span>Estado</span></th>
                                <th data-filter-column="6"><span>Vencimiento</span></th>
                                <th data-filter-column="7"><span>Almacen receptor</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                @php
                                    $buyerStatus = \App\Support\UiStatus::purchaseOrder($order->status, 'buyer');
                                    $buyerStatusClass = \App\Support\UiStatus::purchaseOrderClass($order->status, 'buyer');
                                    $dueDate = $order->due_date?->copy()->startOfDay();
                                    $currentWeekEnd = now()->endOfWeek()->endOfDay();
                                    $nextWeekEnd = now()->copy()->addWeek()->endOfWeek()->endOfDay();
                                    $dueDateClass = '';

                                    if ($dueDate && $dueDate->lte($currentWeekEnd)) {
                                        $dueDateClass = 'due-date-danger';
                                    } elseif ($dueDate && $dueDate->lte($nextWeekEnd)) {
                                        $dueDateClass = 'due-date-warning';
                                    }
                                @endphp
                                <tr data-filter-row>
                                    <td data-filter-value="{{ $order->folio }} {{ $order->constructionProject?->project_key }} {{ $order->constructionProject?->name }}">
                                        <strong>
                                            <a href="{{ $orderRoute('buyer.orders.print', $order) }}" target="_blank">{{ $order->folio }}</a>
                                        </strong>
                                        @if ($constructionContext && $order->constructionProject)
                                            <small class="fine-print">{{ $order->constructionProject->project_key }} - {{ $order->constructionProject->name }}</small>
                                        @endif
                                    </td>
                                    <td data-filter-value="{{ ($order->created_on ?? $order->created_at)?->format('d/m/Y') ?? 'Sin fecha' }}">{{ ($order->created_on ?? $order->created_at)?->format('d/m/Y') ?? 'Sin fecha' }}</td>
                                    <td data-filter-value="{{ $order->company->name }}">{{ $order->company->name }}</td>
                                    <td data-filter-value="{{ $order->provider->business_name }} {{ $order->provider->business_line }}">
                                        {{ $order->provider->business_name }}
                                        <small class="fine-print">{{ $order->provider->business_line }}</small>
                                    </td>
                                    <td data-filter-value="${{ number_format((float) $order->total, 2) }}">${{ number_format((float) $order->total, 2) }}</td>
                                    <td data-filter-value="{{ $buyerStatus }}">
                                        <details class="status-menu">
                                            <summary class="status {{ $buyerStatusClass }}">{{ $buyerStatus }}</summary>
                                            <div class="status-menu-panel">
                                                @if ($order->isEditableByBuyer())
                                                    <a class="button ghost small" href="{{ $orderRoute('buyer.orders.edit', $order) }}">Editar</a>
                                                    <form class="inline-form" method="POST" action="{{ $orderRoute('buyer.orders.cancel', $order) }}" onsubmit="return confirm('Cancelar {{ $order->folio }}?')">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="button danger small" type="submit">Cancelar</button>
                                                    </form>
                                                @else
                                                    <span class="fine-print">Sin acciones</span>
                                                @endif
                                            </div>
                                        </details>
                                    </td>
                                    <td class="{{ $dueDateClass }}" data-filter-value="{{ $order->due_date->format('d/m/Y') }}"><span class="due-date-pill">{{ $order->due_date->format('d/m/Y') }}</span></td>
                                    <td data-filter-value="{{ $order->warehouse ?: 'Sin almacen asignado' }}">{{ $order->warehouse ?: 'Sin almacen asignado' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">No hay ordenes para mostrar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            </div>
        </section>
    </x-app-shell>

    <style>
        td .due-date-pill {
            border-radius: 8px;
            display: inline-flex;
            font-weight: 850;
            justify-content: center;
            min-width: 98px;
            padding: 7px 10px;
        }

        td.due-date-danger .due-date-pill {
            background: #fde8e8;
            border: 1px solid #d73b32;
            color: #a8201a;
        }

        td.due-date-warning .due-date-pill {
            background: #fff6d8;
            border: 1px solid #c79712;
            color: #8a6400;
        }
    </style>

    <script id="buyer-column-filters">
        document.addEventListener('DOMContentLoaded', () => {
            const normalize = (value) => (value || '').replace(/\s+/g, ' ').trim();
            const closeFilters = (except = null) => {
                document.querySelectorAll('.column-filter[open]').forEach((filter) => {
                    if (filter !== except) {
                        filter.removeAttribute('open');
                    }
                });
            };

            document.addEventListener('click', (event) => {
                if (!event.target.closest('.column-filter')) {
                    closeFilters();
                }
            });

            document.querySelectorAll('[data-column-filter-table]').forEach((table) => {
                const rows = Array.from(table.querySelectorAll('tbody tr[data-filter-row]'));
                const headers = Array.from(table.querySelectorAll('thead th[data-filter-column]'));
                const filters = new Map();

                const applyFilters = () => {
                    rows.forEach((row) => {
                        const visible = headers.every((header) => {
                            const column = Number(header.dataset.filterColumn);
                            const selected = filters.get(column);
                            if (!selected) return true;
                            const cell = row.cells[column];
                            const value = normalize(cell?.dataset.filterValue || cell?.textContent);
                            return selected.has(value);
                        });
                        row.hidden = !visible;
                    });
                };

                headers.forEach((header) => {
                    const column = Number(header.dataset.filterColumn);
                    const label = normalize(header.textContent);
                    const values = [...new Set(rows.map((row) => normalize(row.cells[column]?.dataset.filterValue || row.cells[column]?.textContent)).filter(Boolean))].sort((a, b) => a.localeCompare(b, 'es', { numeric: true }));

                    const wrapper = document.createElement('div');
                    wrapper.className = 'th-filter';
                    const title = document.createElement('span');
                    title.textContent = label;

                    const filter = document.createElement('details');
                    filter.className = 'column-filter';
                    const summary = document.createElement('summary');
                    summary.className = 'column-filter-toggle-icon';
                    summary.title = `Filtrar ${label}`;
                    summary.setAttribute('aria-label', `Filtrar ${label}`);
                    const panel = document.createElement('div');
                    panel.className = 'column-filter-panel';
                    const options = document.createElement('div');
                    options.className = 'column-filter-options';

                    values.forEach((value) => {
                        const option = document.createElement('label');
                        option.className = 'column-filter-option';
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.value = value;
                        checkbox.checked = true;
                        option.append(checkbox, document.createTextNode(value));
                        options.append(option);
                    });

                    const actions = document.createElement('div');
                    actions.className = 'column-filter-actions';
                    const all = document.createElement('button');
                    all.type = 'button';
                    all.className = 'button ghost small';
                    all.textContent = 'Todos';
                    const clear = document.createElement('button');
                    clear.type = 'button';
                    clear.className = 'button ghost small';
                    clear.textContent = 'Limpiar';
                    actions.append(all, clear);
                    panel.append(options, actions);
                    filter.append(summary, panel);
                    wrapper.append(title, filter);
                    header.textContent = '';
                    header.append(wrapper);

                    filter.addEventListener('toggle', () => {
                        if (filter.open) closeFilters(filter);
                    });
                    filter.addEventListener('click', (event) => event.stopPropagation());
                    options.addEventListener('change', () => {
                        const selected = new Set(Array.from(options.querySelectorAll('input:checked')).map((input) => input.value));
                        filters.set(column, selected);
                        if (selected.size === values.length) filters.delete(column);
                        applyFilters();
                    });
                    all.addEventListener('click', () => {
                        options.querySelectorAll('input').forEach((input) => input.checked = true);
                        filters.delete(column);
                        applyFilters();
                    });
                    clear.addEventListener('click', () => {
                        options.querySelectorAll('input').forEach((input) => input.checked = false);
                        filters.set(column, new Set());
                        applyFilters();
                    });
                });
            });
        });
    </script>
@endsection
