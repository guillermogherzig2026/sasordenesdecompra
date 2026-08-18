@extends('layouts.app')

@section('body')
    <x-app-shell title="OP Historial">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>OP Historial</h2>
                    <p class="fine-print">Historial de ordenes de pago de obra autorizadas, pagadas, rechazadas o canceladas.</p>
                </div>
                <form class="toolbar" method="GET" action="{{ route('finance.construction-payment-orders.history') }}">
                    <input name="q" value="{{ $query }}" placeholder="Buscar OP...">
                </form>
            </div>

            @include('construction.partials.payment-order-table', [
                'paymentOrders' => $orders,
                'financeContext' => true,
                'allowPaymentUpload' => false,
                'emptyMessage' => 'No hay historial de OP registrado.',
            ])
        </section>
    </x-app-shell>
@endsection
