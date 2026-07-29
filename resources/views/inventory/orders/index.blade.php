@extends('layouts.app')

@section('body')
    <x-app-shell title="Recepcion de ordenes pagadas">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Ordenes pagadas pendientes de recepcion</h2>
                    <p class="fine-print">Solo se muestran OC pagadas pendientes o parciales. Al recibir todas las cantidades, pasan al historial.</p>
                </div>
                <form class="toolbar" method="GET" action="{{ route('inventory.orders.index') }}">
                    <input name="q" value="{{ $query }}" placeholder="Buscar orden...">
                    <a class="button ghost" href="{{ route('reports.download', 'inventory-paid') }}">Exportar CSV</a>
                </form>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th># OC</th>
                            <th>Fecha envio</th>
                            <th>Comprador</th>
                            <th>Proveedor</th>
                            <th>Monto</th>
                            <th>Pago</th>
                            <th>Estado recepcion</th>
                            <th>Cantidad recibida</th>
                            <th>Factura</th>
                            <th>Documento</th>
                            <th>Almacen receptor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            @php
                                $ordered = (float) $order->items->sum('quantity');
                                $received = (float) $order->items->sum(fn ($item) => $item->receiptItems->sum('received_quantity'));
                                $receivedLines = $order->items->filter(fn ($item) => (float) $item->receiptItems->sum('received_quantity') >= (float) $item->quantity)->count();
                                $lastReceipt = $order->receipts->sortByDesc('received_on')->first();
                            @endphp
                            <tr>
                                <td><strong>{{ $order->folio }}</strong></td>
                                <td>{{ ($order->created_on ?? $order->created_at)?->format('d/m/Y') ?? 'Sin fecha' }}</td>
                                <td>{{ $order->buyer->name }}</td>
                                <td>{{ $order->provider->business_name }}</td>
                                <td>${{ number_format((float) $order->total, 0) }}</td>
                                <td>
                                    @if ($order->payment?->original_name)
                                        <a class="attachment-pill" href="{{ route('inventory.orders.payment-receipt', $order) }}" target="_blank" rel="noopener"><span>Adjunto</span>{{ $order->payment->original_name }}</a>
                                    @else
                                        Sin pago
                                    @endif
                                </td>
                                <td>
                                    <details class="status-menu">
                                        <summary class="status {{ \App\Support\UiStatus::receiptClass($order->receipt_status, 'inventory') }}">{{ \App\Support\UiStatus::receipt($order->receipt_status, 'inventory') }}</summary>
                                        <div class="status-menu-panel">
                                            <a class="button ghost small" href="{{ route('inventory.orders.print', $order) }}" target="_blank">PDF</a>
                                            <a class="button primary small" href="{{ route('inventory.orders.receipt', $order) }}">Abrir copia</a>
                                        </div>
                                    </details>
                                </td>
                                <td>
                                    Recibido {{ number_format($received, 0) }} de {{ number_format($ordered, 0) }}
                                    <small class="fine-print">{{ $receivedLines }} de {{ $order->items->count() }} partidas completas</small>
                                </td>
                                <td>{{ $lastReceipt?->invoice_number ?? 'Pendiente' }}</td>
                                <td>
                                    @if ($lastReceipt)
                                        {{ $lastReceipt->original_name }}
                                        <small class="fine-print">{{ $lastReceipt->received_on?->format('d/m/Y') }}</small>
                                    @else
                                        Pendiente
                                    @endif
                                </td>
                                <td>{{ $order->warehouse ?: 'Sin almacen asignado' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11">No hay ordenes pagadas por recibir.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </x-app-shell>
@endsection

