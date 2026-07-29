@extends('layouts.app')

@section('body')
    <x-app-shell title="Historial">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Historial</h2>
                    <p class="fine-print">Ordenes pagadas que ya fueron recibidas en su totalidad.</p>
                </div>
                <a class="button ghost" href="{{ route('reports.download', 'inventory-history') }}">Exportar CSV</a>
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
                            <th>Estado</th>
                            <th>Pago</th>
                            <th>Fecha pago</th>
                            <th>Recepcion</th>
                            <th>Almacen receptor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            @php
                                $lastReceipt = $order->receipts->sortByDesc('received_on')->first();
                                $ordered = (float) $order->items->sum('quantity');
                                $received = (float) $order->items->sum(fn ($item) => $item->receiptItems->sum('received_quantity'));
                            @endphp
                            <tr>
                                <td><strong>{{ $order->folio }}</strong></td>
                                <td>{{ ($order->created_on ?? $order->created_at)?->format('d/m/Y') ?? 'Sin fecha' }}</td>
                                <td>{{ $order->buyer->name }}</td>
                                <td>{{ $order->provider->business_name }}</td>
                                <td>${{ number_format((float) $order->total, 0) }}</td>
                                <td>
                                    <details class="status-menu">
                                        <summary class="status {{ \App\Support\UiStatus::receiptClass($order->receipt_status, 'inventory') }}">{{ \App\Support\UiStatus::receipt($order->receipt_status, 'inventory') }}</summary>
                                        <div class="status-menu-panel">
                                            <a class="button ghost small" href="{{ route('inventory.orders.print', $order) }}" target="_blank">PDF</a>
                                        </div>
                                    </details>
                                </td>
                                <td>
                                    @if ($order->payment?->original_name)
                                        <a class="attachment-pill" href="{{ route('inventory.orders.payment-receipt', $order) }}" target="_blank" rel="noopener"><span>Adjunto</span>{{ $order->payment->original_name }}</a>
                                    @else
                                        Sin pago
                                    @endif
                                </td>
                                <td>{{ $order->payment?->paid_on?->format('d/m/Y') ?? 'Sin pago' }}</td>
                                <td>
                                    {{ \App\Support\UiStatus::receipt($order->receipt_status, 'inventory') }}
                                    @if ($lastReceipt)
                                        <small class="fine-print">{{ $lastReceipt->invoice_number }} - {{ $lastReceipt->received_on?->format('d/m/Y') }} - Recibido {{ number_format($received, 0) }} de {{ number_format($ordered, 0) }}</small>
                                    @endif
                                </td>
                                <td>{{ $order->warehouse ?: 'Sin almacen asignado' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">No hay registros en historial.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </x-app-shell>
@endsection

