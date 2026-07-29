@extends('layouts.app')

@section('body')
    <x-app-shell title="Registrar pago">
        <form class="panel" method="POST" action="{{ route('finance.orders.payment.store', $order) }}" enctype="multipart/form-data">
            @csrf
            <div class="panel-header">
                <div>
                    <h2>Registrar pago de {{ $order->folio }}</h2>
                    <p class="fine-print">Al adjuntar el comprobante, la OC cambia automaticamente a pagada y aparece en Inventarios.</p>
                </div>
                <a class="button ghost" href="{{ route('finance.orders.active') }}">Volver</a>
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
                                <th>Articulo</th>
                                <th>Cantidad</th>
                                <th>Precio unit.</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $item->article }}</td>
                                    <td>{{ number_format((float) $item->quantity, 2) }}</td>
                                    <td>${{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td>${{ number_format((float) $item->line_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <strong>Total: ${{ number_format((float) $order->total, 2) }}</strong>
            </section>

            <div class="grid-2">
                <label>
                    Archivo de pago
                    <input name="payment_file" type="file" required>
                </label>
                <label>
                    Fecha de pago
                    <input name="paid_on" type="date" value="{{ now()->toDateString() }}" required>
                </label>
            </div>

            <div class="form-actions">
                <span class="fine-print">El comprobante queda almacenado y visible en historial.</span>
                <button class="button primary" type="submit">Guardar pago</button>
            </div>
        </form>
    </x-app-shell>
@endsection
