@extends('layouts.app')

@section('body')
    <x-app-shell title="OS por entregar">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>OS por Entregar</h2>
                    <p class="fine-print">Genera la remision, descuenta el inventario central y deja la OS pendiente de recepcion.</p>
                </div>
                <form class="toolbar" method="GET" action="{{ route('inventory.supply-orders.active') }}">
                    <input name="q" value="{{ $query }}" placeholder="Buscar OS...">
                </form>
            </div>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
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
                                    <strong>{{ $order->folio }}</strong>
                                    <small class="fine-print">{{ \App\Support\UiStatus::supplyOrder($order->status, 'inventory') }}</small>
                                </td>
                                <td>{{ $order->created_on?->format('d/m/Y') }}</td>
                                <td>{{ $order->requester->name }}</td>
                                <td>{{ $order->company->name }}</td>
                                <td>{{ number_format((float) $totalQuantity, 2) }}</td>
                                <td>{{ $unitLabel }}</td>
                                <td>
                                    <x-supply-order-items-dialog :order="$order" :dialog-id="'inventory-active-supply-detail-'.$order->id" />
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
                                        <a class="attachment-pill" href="{{ route('inventory.supply-orders.remission', $order) }}" target="_blank"><span>Remision</span>{{ $order->delivery_remission_number }}</a>
                                        <div class="fine-print">Pendiente de recibir</div>
                                    @else
                                        <form class="stack" method="POST" action="{{ route('inventory.supply-orders.deliver', $order) }}">
                                            @csrf
                                            <label>Fecha salida<input name="delivered_on" type="date" value="{{ now()->toDateString() }}" required></label>
                                            <label>PIN recepcion<input name="receiving_pin" type="password" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" placeholder="1234"></label>
                                            <button class="button primary small" type="submit">Generar remision</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10">No hay OS autorizadas por entregar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </x-app-shell>
@endsection
