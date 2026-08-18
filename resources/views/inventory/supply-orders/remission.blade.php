@extends('layouts.app')

@php
    $digitalUrl = $order->remission_token ? route('supply-orders.digital.show', $order->remission_token) : null;
    $qrUrl = $digitalUrl ? 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data='.rawurlencode($digitalUrl) : null;
@endphp

@section('body')
    <x-app-shell title="Remision de entrega">
        <section class="panel remission-print">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Remision de entrega</p>
                    <h2>{{ $order->formatted_delivery_remission_number ?: 'Remision pendiente' }}</h2>
                    <p class="fine-print">ID OS {{ $order->supply_consecutive }}</p>
                    <p class="fine-print">OS {{ $order->folio }} · {{ \App\Support\UiStatus::supplyOrder($order->status, 'inventory') }}</p>
                </div>
                <button class="button ghost no-print" onclick="window.print()" type="button">Imprimir</button>
            </div>

            <div class="grid-4">
                <article class="metric-card"><span>Fecha salida</span><strong style="font-size:1rem">{{ $order->delivered_on?->format('d/m/Y') ?: 'Pendiente' }}</strong></article>
                <article class="metric-card"><span>Fecha recepcion</span><strong style="font-size:1rem">{{ $order->received_on?->format('d/m/Y') ?: 'Pendiente' }}</strong></article>
                <article class="metric-card"><span>Almacen origen</span><strong style="font-size:1rem">{{ $order->warehouse_from }}</strong></article>
                <article class="metric-card"><span>Almacen destino</span><strong style="font-size:1rem">{{ $order->warehouse_to ?: 'Sin destino capturado' }}</strong></article>
            </div>

            <div class="grid-3">
                <div class="panel">
                    <strong>Usuario solicitante</strong>
                    <p>{{ $order->requester->name }}</p>
                    <p class="fine-print">{{ $order->requester->email }}</p>
                    <p class="fine-print">Rol: {{ $order->requester->role === 'buyer' ? $order->requester->buyerSubroleLabel() : ucfirst($order->requester->role) }}</p>
                </div>
                <div class="panel">
                    <strong>Empresa receptora</strong>
                    <p>{{ $order->company->name }}</p>
                    <p class="fine-print">RFC: {{ $order->company->rfc ?: 'Sin RFC' }}</p>
                    <p class="fine-print">{{ $order->company->address ?: 'Sin direccion capturada' }}</p>
                </div>
                <div class="panel">
                    <strong>Confirmacion digital</strong>
                    @if ($qrUrl)
                        <img src="{{ $qrUrl }}" alt="QR de remision {{ $order->formatted_delivery_remission_number }}" width="180" height="180" style="width:180px;height:180px">
                        <p class="fine-print">Escanea para abrir el formato digital y recibir mercancia.</p>
                        <p class="fine-print" style="word-break:break-all">{{ $digitalUrl }}</p>
                    @else
                        <p class="fine-print">El QR se generara cuando exista una remision.</p>
                    @endif
                </div>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Descripcion</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Precio unitario</th>
                            <th>Precio total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->article }}</strong>
                                    @if ($item->catalogItem?->sku)
                                        <small class="fine-print">SKU {{ $item->catalogItem->sku }}</small>
                                    @endif
                                    @if ($item->catalogItem?->description)
                                        <small class="fine-print">{{ $item->catalogItem->description }}</small>
                                    @endif
                                </td>
                                <td>{{ number_format((float) $item->quantity, 2) }}</td>
                                <td>{{ $item->catalogItem?->unit ?: 'unidad' }}</td>
                                <td>${{ number_format((float) $item->unit_cost, 2) }}</td>
                                <td>${{ number_format((float) $item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" style="text-align:right">Total</th>
                            <th>${{ number_format((float) $order->total, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="grid-3">
                <div class="panel"><strong>Entrega</strong><p>{{ $order->deliveredBy?->name ?: 'Inventarios' }}</p></div>
                <div class="panel"><strong>Recibe</strong><p>{{ $order->received_by_name ?: ($order->warehouse_to ?: 'Pendiente de recepcion') }}</p></div>
                <div class="panel"><strong>Notas</strong><p>{{ $order->notes ?: $order->company->purchase_order_notes ?: 'Sin notas adicionales.' }}</p></div>
            </div>
        </section>

        <style>
            @media print {
                .sidebar, .topbar, .no-print, .table-export-actions, .column-sort-button, .column-filter { display: none !important; }
                .app-shell { display: block; }
                .content-shell, .view { min-height: auto; padding: 0; }
                .panel { box-shadow: none; }
                .remission-print { border: 0; }
                body { background: #fff; padding: 0; }
            }
        </style>
    </x-app-shell>
@endsection
