@extends('layouts.app')

@section('body')
    <x-app-shell title="OP Pendientes">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>OP Pendientes</h2>
                    <p class="fine-print">Ordenes de pago de obra pendientes de autorizacion o pago.</p>
                </div>
                <form class="toolbar" method="GET" action="{{ route('finance.construction-payment-orders.active') }}">
                    <input name="q" value="{{ $query }}" placeholder="Buscar OP...">
                </form>
            </div>

            @include('construction.partials.payment-order-table', [
                'paymentOrders' => $orders,
                'financeContext' => true,
                'allowPaymentUpload' => true,
                'emptyMessage' => 'No hay OP pendientes registradas.',
            ])
        </section>
    </x-app-shell>
@endsection
