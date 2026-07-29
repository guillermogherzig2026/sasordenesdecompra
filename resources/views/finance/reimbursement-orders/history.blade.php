@extends('layouts.app')

@section('body')
    <x-app-shell title="OR Historial">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>OR Historial</h2>
                    <p class="fine-print">Reembolsos pagados, rechazados o cancelados.</p>
                </div>
                <form class="toolbar" method="GET" action="{{ route('finance.reimbursement-orders.history') }}">
                    <input name="q" value="{{ $query }}" placeholder="Buscar OR...">
                </form>
            </div>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha envio</th>
                            <th>Usuario</th>
                            <th>Empresa</th>
                            <th>Proveedor</th>
                            <th>Cotizacion</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Soporte producto/servicio</th>
                            <th>Pago</th>
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
                                <td><span class="status {{ \App\Support\UiStatus::workflowClass($order->status) }}">{{ \App\Support\UiStatus::reimbursementOrder($order->status) }}</span></td>
                                <td>
                                    @if ($order->support_file_path)
                                        <a class="attachment-pill" href="{{ route('finance.reimbursement-orders.support', $order) }}" target="_blank"><span>Soporte</span>{{ $order->support_original_name }}</a>
                                    @else
                                        <span class="status pending">Pendiente</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($order->payment_file_path)
                                        <a class="attachment-pill" href="{{ route('finance.reimbursement-orders.payment', $order) }}" target="_blank"><span>Pago</span>{{ $order->payment_original_name }}</a>
                                        <small class="fine-print">{{ $order->paid_on?->format('d/m/Y') }}</small>
                                    @else
                                        <span class="fine-print">Sin pago</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10">No hay historial de OR.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </x-app-shell>
@endsection
