@extends('layouts.app')

@php
    $isHistory = $panel === 'history';
@endphp

@section('body')
    <x-app-shell :title="$isHistory ? 'Historial de OR' : 'OR pendientes'">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>{{ $isHistory ? 'OR Historial' : 'OR Pendientes' }}</h2>
                    <p class="fine-print">{{ $isHistory ? 'Reembolsos pagados o rechazados.' : 'Reembolsos pendientes de autorizacion o pago.' }}</p>
                </div>
                <div class="item-actions">
                    <form class="toolbar" method="GET" action="{{ route('buyer.reimbursement-orders.index') }}">
                        @if ($isHistory)
                            <input type="hidden" name="panel" value="history">
                        @endif
                        <input name="q" value="{{ $query }}" placeholder="Buscar OR...">
                    </form>
                    @if (! $isHistory)
                        <a class="button primary" href="{{ route('buyer.reimbursement-orders.create') }}">Nueva OR</a>
                    @endif
                </div>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha envio</th>
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
                                <td>{{ $order->company->name }}</td>
                                <td>{{ $order->provider }}</td>
                                <td><a class="attachment-pill" href="{{ route('buyer.reimbursement-orders.quote', $order) }}" target="_blank"><span>Ver</span>{{ $order->quote_original_name }}</a></td>
                                <td>${{ number_format((float) $order->amount, 2) }}</td>
                                <td><span class="status {{ \App\Support\UiStatus::workflowClass($order->status) }}">{{ \App\Support\UiStatus::reimbursementOrder($order->status, 'buyer') }}</span></td>
                                <td>
                                    @if ($order->support_file_path)
                                        <a class="attachment-pill" href="{{ route('buyer.reimbursement-orders.support', $order) }}" target="_blank"><span>Soporte</span>{{ $order->support_original_name }}</a>
                                    @elseif (! $isHistory)
                                        <form class="stack" method="POST" action="{{ route('buyer.reimbursement-orders.support.store', $order) }}" enctype="multipart/form-data">
                                            @csrf
                                            <span class="status pending">Pendiente</span>
                                            <input name="support_file" type="file" accept=".pdf,.jpg,.jpeg,.png" required>
                                            <button class="button ghost small" type="submit">Subir soporte</button>
                                        </form>
                                    @else
                                        <span class="status pending">Pendiente</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($order->payment_file_path)
                                        <a class="attachment-pill" href="{{ route('buyer.reimbursement-orders.payment', $order) }}" target="_blank"><span>Pago</span>{{ $order->payment_original_name }}</a>
                                    @else
                                        <span class="fine-print">Sin pago</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9">No hay OR para mostrar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </x-app-shell>
@endsection
