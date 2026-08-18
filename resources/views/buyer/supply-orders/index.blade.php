@extends('layouts.app')

@php
    $isHistory = $panel === 'history';
@endphp

@section('body')
    <x-app-shell :title="$isHistory ? 'Historial de OS' : 'OS pendientes'">
        <section class="panel supply-orders-panel">
            <div class="panel-header">
                <div>
                    <h2>{{ $isHistory ? 'OS Historial' : 'OS Pendientes' }}</h2>
                    <p class="fine-print">{{ $isHistory ? 'Ordenes recibidas, rechazadas o canceladas.' : 'Solicitudes enviadas, autorizadas o con remision pendiente de recepcion.' }}</p>
                </div>
                <div class="item-actions">
                    <form class="toolbar" method="GET" action="{{ route('buyer.supply-orders.index') }}">
                        @if ($isHistory)
                            <input type="hidden" name="panel" value="history">
                        @endif
                        <input name="q" value="{{ $query }}" placeholder="Buscar OS...">
                    </form>
                    <a class="button ghost" href="{{ route('reports.download', 'supply-orders-excel') }}">Exportar Excel</a>
                    @if (! $isHistory)
                        <a class="button primary" href="{{ route('buyer.supply-orders.create') }}">Nueva OS</a>
                    @endif
                </div>
            </div>

            <div class="table-scroll supply-orders-table-scroll">
                <table class="supply-orders-table">
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
                                    <small class="fine-print">{{ \App\Support\UiStatus::supplyOrder($order->status, 'buyer') }}</small>
                                </td>
                                <td>{{ $order->created_on?->format('d/m/Y') }}</td>
                                <td>{{ $order->requester?->name ?: 'Usuario' }}</td>
                                <td>{{ $order->company->name }}</td>
                                <td>{{ number_format((float) $totalQuantity, 2) }}</td>
                                <td>{{ $unitLabel }}</td>
                                <td>
                                    <x-supply-order-items-dialog :order="$order" :dialog-id="'buyer-supply-detail-'.$order->id" />
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
                                        <a class="button ghost small" href="{{ route('buyer.supply-orders.remission', $order) }}" target="_blank">Remision</a>
                                        <div class="fine-print">{{ $order->formatted_delivery_remission_number }}</div>
                                        @if ($order->status === 'remitted')
                                            <div class="fine-print">Pendiente de recibir</div>
                                        @endif
                                    @else
                                        <span class="button ghost small disabled" aria-disabled="true">Remision</span>
                                        <div class="fine-print">Pendiente</div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="11">No hay OS para mostrar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </x-app-shell>
@endsection
