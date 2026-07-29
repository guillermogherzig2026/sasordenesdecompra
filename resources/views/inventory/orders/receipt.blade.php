@extends('layouts.app')

@section('body')
    <x-app-shell title="Copia de orden original">
        <form class="panel" method="POST" action="{{ route('inventory.orders.receipt.store', $order) }}" enctype="multipart/form-data">
            @csrf
            <div class="panel-header">
                <div>
                    <h2>Copia de la orden original {{ $order->folio }}</h2>
                    <p class="fine-print">Las recepciones anteriores aparecen debajo de cada partida. Captura solo la cantidad recibida ahora.</p>
                </div>
                <a class="button ghost" href="{{ route('inventory.orders.index') }}">Volver</a>
            </div>

            <section class="panel" style="box-shadow:none">
                <div class="grid-3">
                    <div>
                        <span class="fine-print">Comprador</span>
                        <strong>{{ $order->buyer->name }}</strong>
                    </div>
                    <div>
                        <span class="fine-print">Empresa</span>
                        <strong>{{ $order->company->name }}</strong>
                    </div>
                    <div>
                        <span class="fine-print">Proveedor</span>
                        <strong>{{ $order->provider->business_name }}</strong>
                    </div>
                </div>

                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Articulo / registro</th>
                                <th>Cantidad OC</th>
                                <th>Recibido previo</th>
                                <th>Restante</th>
                                <th>Nueva recepcion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($progress as $index => $row)
                                <tr>
                                    <td><strong>{{ $row['item']->article }}</strong></td>
                                    <td>{{ number_format($row['ordered'], 2) }}</td>
                                    <td>{{ number_format($row['received'], 2) }}</td>
                                    <td>{{ number_format($row['remaining'], 2) }}</td>
                                    <td>
                                        <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $row['item']->id }}">
                                        <input name="items[{{ $index }}][received_quantity]" type="number" min="0" max="{{ $row['remaining'] }}" step="0.01" value="0" required>
                                    </td>
                                </tr>
                                @foreach ($row['item']->receiptItems as $receiptItem)
                                    <tr>
                                        <td class="fine-print">Recepcion previa: {{ $receiptItem->receipt?->invoice_number }}</td>
                                        <td></td>
                                        <td>{{ number_format((float) $receiptItem->received_quantity, 2) }}</td>
                                        <td colspan="2" class="fine-print">
                                            {{ $receiptItem->receipt?->received_on?->format('d/m/Y') }}
                                            · {{ $receiptItem->receipt?->original_name }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="grid-3">
                <label>
                    Documento de esta recepcion
                    <input name="receipt_file" type="file" required>
                </label>
                <label>
                    Numero de factura
                    <input name="invoice_number" placeholder="F-00000" required>
                </label>
                <label>
                    Fecha de recepcion
                    <input name="received_on" type="date" value="{{ now()->toDateString() }}" required>
                </label>
            </div>

            <div class="form-actions">
                <span class="fine-print">Si todas las cantidades quedan cubiertas, la OC pasara al historial de Inventarios.</span>
                <button class="button primary" type="submit">Guardar recepcion</button>
            </div>
        </form>
    </x-app-shell>
@endsection
