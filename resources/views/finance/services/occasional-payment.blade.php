@extends('layouts.app')

@section('body')
    <x-app-shell title="Registrar pago de servicio ocasional">
        <form class="panel" method="POST" action="{{ route('finance.services.occasional.payment.store', $payment) }}" enctype="multipart/form-data">
            @csrf
            <div class="panel-header">
                <div>
                    <h2>OCP-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }} · {{ $payment->entry->category->name }}</h2>
                    <p class="fine-print">Registra el comprobante de la transferencia para confirmar el pago.</p>
                </div>
                <a class="button ghost" href="{{ route('finance.services.index') }}">Volver</a>
            </div>

            <div class="grid-3" style="padding: 20px">
                <div><span class="fine-print">{{ $payment->allocations->isNotEmpty() ? 'Proveedor / cuenta concentradora' : 'Servicio / titular' }}</span><strong>{{ $payment->entry->name }}</strong></div>
                <div><span class="fine-print">Fecha prevista</span><strong>{{ $payment->due_date->format('d/m/Y') }}</strong></div>
                <div><span class="fine-print">Monto a transferir</span><strong>${{ number_format((float) $payment->amount, 2) }} MXN</strong></div>
            </div>

            @if ($payment->allocations->isNotEmpty())
                <div class="table-scroll" style="margin: 0 20px 20px">
                    <table>
                        <thead><tr><th>Referencia / tarjeta</th><th>Usuario</th><th>Monto asignado</th></tr></thead>
                        <tbody>
                            @foreach ($payment->allocations as $allocation)
                                <tr><td>{{ $allocation->reference_number }}</td><td>{{ $allocation->recipient_name }}</td><td>${{ number_format((float) $allocation->amount, 2) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if ($errors->any())
                <div class="error-list" role="alert" style="margin: 0 20px 20px">
                    <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="grid-2" style="padding: 0 20px 20px">
                <label>Comprobante de pago
                    <input name="payment_file" type="file" accept="application/pdf,image/jpeg,image/png,image/webp" required>
                </label>
                <label>Fecha de pago
                    <input name="payment_paid_on" type="date" max="{{ now()->toDateString() }}" value="{{ old('payment_paid_on', now()->toDateString()) }}" required>
                </label>
            </div>

            <div class="form-actions" style="padding: 0 20px 20px">
                <button class="button primary" type="submit">Guardar pago</button>
            </div>
        </form>
    </x-app-shell>
@endsection
