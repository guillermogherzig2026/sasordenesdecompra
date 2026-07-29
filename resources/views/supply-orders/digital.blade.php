@extends('layouts.app')

@section('body')
    <main class="view" style="max-width:1100px;margin:0 auto;width:100%">
        @if (session('status'))
            <div class="alert">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="error-list">
                <strong>Revisa la contrasena capturada.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Formato digital de solicitud</p>
                    <h1 style="margin:0">{{ $order->delivery_remission_number }}</h1>
                    <p class="fine-print">OS {{ $order->folio }} · {{ \App\Support\UiStatus::supplyOrder($order->status, 'buyer') }}</p>
                </div>
                @if ($order->status === 'delivered')
                    <span class="status paid">Mercancia recibida</span>
                @else
                    <button class="button primary" type="button" data-dialog-target="receive-supply-order">Recibir mercancia</button>
                @endif
            </div>

            <div class="grid-4">
                <article class="metric-card"><span>Fecha salida</span><strong style="font-size:1rem">{{ $order->delivered_on?->format('d/m/Y') ?: 'Pendiente' }}</strong></article>
                <article class="metric-card"><span>Fecha recepcion</span><strong style="font-size:1rem">{{ $order->received_on?->format('d/m/Y') ?: 'Pendiente' }}</strong></article>
                <article class="metric-card"><span>Origen</span><strong style="font-size:1rem">{{ $order->warehouse_from }}</strong></article>
                <article class="metric-card"><span>Destino</span><strong style="font-size:1rem">{{ $order->warehouse_to ?: 'Sin destino' }}</strong></article>
            </div>

            <div class="grid-2">
                <div class="panel">
                    <strong>Solicitante</strong>
                    <p>{{ $order->requester->name }}</p>
                    <p class="fine-print">{{ $order->requester->email }}</p>
                </div>
                <div class="panel">
                    <strong>Empresa receptora</strong>
                    <p>{{ $order->company->name }}</p>
                    <p class="fine-print">RFC: {{ $order->company->rfc ?: 'Sin RFC' }}</p>
                    <p class="fine-print">{{ $order->company->address ?: 'Sin direccion capturada' }}</p>
                </div>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Descripcion</th>
                            <th>Precio unitario</th>
                            <th>Precio total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>{{ number_format((float) $item->quantity, 2) }}</td>
                                <td>{{ $item->catalogItem?->unit ?: 'unidad' }}</td>
                                <td>
                                    <strong>{{ $item->article }}</strong>
                                    @if ($item->catalogItem?->description)
                                        <small class="fine-print">{{ $item->catalogItem->description }}</small>
                                    @endif
                                </td>
                                <td>${{ number_format((float) $item->unit_cost, 2) }}</td>
                                <td>${{ number_format((float) $item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <dialog class="confirm-dialog" id="receive-supply-order">
            <form class="confirm-card" method="POST" action="{{ route('supply-orders.digital.receive', $order->remission_token) }}">
                @csrf
                <h3>Recibir mercancia</h3>
                <p>Ingresa la contrasena de 4 digitos del almacen receptor para marcar esta OS como recibida.</p>
                <label>Contrasena
                    <input name="receiving_pin" type="password" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" required autofocus>
                </label>
                <div class="form-actions">
                    <button class="button ghost" type="button" data-dialog-close>Cancelar</button>
                    <button class="button primary" type="submit">Aceptar</button>
                </div>
            </form>
        </dialog>
    </main>

    <script>
        document.querySelectorAll('[data-dialog-target]').forEach((button) => {
            button.addEventListener('click', () => document.getElementById(button.dataset.dialogTarget)?.showModal());
        });

        document.querySelectorAll('[data-dialog-close]').forEach((button) => {
            button.addEventListener('click', () => button.closest('dialog')?.close());
        });
    </script>
@endsection
