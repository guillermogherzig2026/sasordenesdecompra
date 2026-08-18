@extends('layouts.app')

@section('body')
    <x-app-shell title="OS Vigentes">
        <section class="panel finance-active-panel">
            <div class="panel-header">
                <div>
                    <h2>OS Vigentes</h2>
                    <p class="fine-print">Ordenes de suministro pendientes de autorizacion, remision o recepcion.</p>
                </div>
                <form class="toolbar" method="GET" action="{{ route('finance.supply-orders.active') }}">
                    <input name="q" value="{{ $query }}" placeholder="Buscar OS...">
                    <a class="button ghost" href="{{ route('reports.download', 'supply-orders-excel') }}">Exportar Excel</a>
                </form>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID consecutivo</th>
                            <th>OS</th>
                            <th>Fecha de envio</th>
                            <th>Usuario</th>
                            <th>Empresa</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Descripcion</th>
                            <th>Precio unitario</th>
                            <th>Precio total</th>
                            <th>Remision</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            @php
                                $itemCount = $order->items->count();
                                $totalQuantity = $order->items->sum(fn ($item) => (float) $item->quantity);
                                $units = $order->items->map(fn ($item) => $item->catalogItem?->unit ?: 'unidad')->unique()->values();
                                $unitLabel = $units->count() === 1 ? $units->first() : 'Varias';
                                $singleItem = $itemCount === 1 ? $order->items->first() : null;
                                $totalAmount = $order->items->sum(fn ($item) => (float) $item->line_total);
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $order->supply_consecutive }}</strong>
                                    <small class="fine-print">General</small>
                                </td>
                                <td>
                                    <strong>{{ $order->folio }}</strong>
                                    <small class="fine-print">{{ \App\Support\UiStatus::supplyOrder($order->status) }}</small>
                                </td>
                                <td>{{ $order->created_on?->format('d/m/Y') }}</td>
                                <td>{{ $order->requester->name }}</td>
                                <td>{{ $order->company->name }}</td>
                                <td>{{ number_format((float) $totalQuantity, 2) }}</td>
                                <td>{{ $unitLabel }}</td>
                                <td>
                                    <x-supply-order-items-dialog :order="$order" :dialog-id="'finance-active-supply-detail-'.$order->id" />
                                    <small class="fine-print">{{ $itemCount }} {{ $itemCount === 1 ? 'partida' : 'partidas' }}</small>
                                </td>
                                <td>
                                    @if ($singleItem)
                                        ${{ number_format((float) $singleItem->unit_cost, 2) }}
                                    @else
                                        <span class="fine-print">Ver detalle</span>
                                    @endif
                                </td>
                                <td>${{ number_format((float) $totalAmount, 2) }}</td>
                                <td>
                                    @if ($order->delivery_remission_number)
                                        <a class="attachment-pill" href="{{ route('finance.supply-orders.remission', $order) }}" target="_blank"><span>Remision</span>{{ $order->formatted_delivery_remission_number }}</a>
                                        <div class="fine-print">{{ $order->status === 'remitted' ? 'Pendiente de recibir' : 'Recibida' }}</div>
                                    @else
                                        <details class="status-menu">
                                            <summary class="status {{ \App\Support\UiStatus::workflowClass($order->status) }}">{{ \App\Support\UiStatus::supplyOrder($order->status) }}</summary>
                                            <div class="status-menu-panel">
                                                @if ($order->status === 'sent')
                                                    <form class="inline-form" method="POST" action="{{ route('finance.supply-orders.approve', $order) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="button primary small" type="submit">Autorizada</button>
                                                    </form>
                                                    <form class="inline-form" method="POST" action="{{ route('finance.supply-orders.reject', $order) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="reason" value="No cumple criterios de autorizacion">
                                                        <button class="button danger small" type="submit">Rechazada</button>
                                                    </form>
                                                @elseif ($order->status === 'approved')
                                                    <span class="fine-print">Inventarios genera la remision.</span>
                                                    <form class="inline-form" method="POST" action="{{ route('finance.supply-orders.reject', $order) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="reason" value="No cumple criterios de autorizacion">
                                                        <button class="button danger small" type="submit">Rechazar</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </details>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="11">No hay OS vigentes.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </x-app-shell>
@endsection
