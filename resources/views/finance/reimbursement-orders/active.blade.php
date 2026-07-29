@extends('layouts.app')

@section('body')
    <x-app-shell title="OR Vigentes">
        <section class="panel finance-active-panel">
            <div class="panel-header">
                <div>
                    <h2>OR Vigentes</h2>
                    <p class="fine-print">Reembolsos pendientes de autorizacion o pago.</p>
                </div>
                <form class="toolbar" method="GET" action="{{ route('finance.reimbursement-orders.active') }}">
                    <input name="q" value="{{ $query }}" placeholder="Buscar OR...">
                </form>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha de envio</th>
                            <th>Usuario</th>
                            <th>Empresa</th>
                            <th>Proveedor</th>
                            <th>Cotizacion</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Soporte del producto o servicio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td><strong>{{ $order->folio }}</strong></td>
                                <td>{{ $order->created_on?->format('d/m/Y') }}</td>
                                <td>{{ $order->requester->name }}</td>
                                <td>{{ $order->company->name }}</td>
                                <td>{{ $order->provider }}</td>
                                <td><a class="attachment-pill" href="{{ route('finance.reimbursement-orders.quote', $order) }}" target="_blank"><span>Ver</span>{{ $order->quote_original_name }}</a></td>
                                <td>${{ number_format((float) $order->amount, 2) }}</td>
                                <td>
                                    <details class="status-menu">
                                        <summary class="status {{ \App\Support\UiStatus::workflowClass($order->status) }}">{{ \App\Support\UiStatus::reimbursementOrder($order->status) }}</summary>
                                        <div class="status-menu-panel reimbursement-status-panel">
                                            @if ($order->status === 'sent')
                                                <form class="inline-form" method="POST" action="{{ route('finance.reimbursement-orders.approve', $order) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="button primary small" type="submit">Autorizada</button>
                                                </form>
                                                <form class="inline-form" method="POST" action="{{ route('finance.reimbursement-orders.reject', $order) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="button danger small" type="submit">Rechazada</button>
                                                </form>
                                            @elseif ($order->status === 'approved')
                                                <form class="stack" method="POST" action="{{ route('finance.reimbursement-orders.payment.store', $order) }}" enctype="multipart/form-data">
                                                    @csrf
                                                    <input name="paid_on" type="date" value="{{ now()->toDateString() }}" required>
                                                    <input name="payment_file" type="file" required>
                                                    <button class="button primary small" type="submit">Subir pago</button>
                                                </form>
                                                <form class="inline-form" method="POST" action="{{ route('finance.reimbursement-orders.reject', $order) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="button danger small" type="submit">Rechazar</button>
                                                </form>
                                            @endif
                                        </div>
                                    </details>
                                </td>
                                <td>
                                    @if ($order->support_file_path)
                                        <a class="attachment-pill" href="{{ route('finance.reimbursement-orders.support', $order) }}" target="_blank"><span>Soporte</span>{{ $order->support_original_name }}</a>
                                    @else
                                        <span class="status pending">Pendiente</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9">No hay OR vigentes.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
        <style>
            .reimbursement-status-panel { min-width: 230px; }
            .reimbursement-status-panel input[type=file] { max-width: 210px; font-size: .78rem; }
        </style>
    </x-app-shell>
@endsection
