@extends('layouts.app')

@section('body')
    @php($periodAmount = (float) ($receipt?->amount ?? $service->cost))

    <x-app-shell title="Registrar pago de servicio">
        <form class="panel" method="POST" action="{{ route('finance.services.payment.store', [$service, $occurrence['due_date']]) }}" enctype="multipart/form-data">
            @csrf
            <div class="panel-header">
                <div>
                    <h2>{{ $service->folio }} · {{ $service->service_name }}</h2>
                    <p class="fine-print">Adjunta el comprobante para marcar este periodo como pagado.</p>
                </div>
                <a class="button ghost" href="{{ route('finance.services.index') }}">Volver</a>
            </div>

            <section class="panel" style="box-shadow:none">
                <div class="grid-3">
                    <div><span class="fine-print">Proveedor</span><strong>{{ $service->provider }}</strong></div>
                    <div><span class="fine-print">Periodo</span><strong>{{ \Illuminate\Support\Carbon::parse($occurrence['period_start'])->format('d/m/Y') }} al {{ \Illuminate\Support\Carbon::parse($occurrence['due_date'])->format('d/m/Y') }}</strong></div>
                    <div><span class="fine-print">Monto</span><strong>${{ number_format($periodAmount, 2) }}</strong></div>
                </div>
                <p class="fine-print">Factura/recibo cargado por Asistente Administrativo: {{ $receipt?->support_original_name ?? 'Pendiente' }}</p>
            </section>

            <div class="grid-2">
                <label>
                    Comprobante de pago
                    <input name="payment_file" type="file" required>
                </label>
                <label>
                    Fecha de pago
                    <input name="payment_paid_on" type="date" value="{{ now()->toDateString() }}" required>
                </label>
            </div>

            <div class="form-actions">
                <span class="fine-print">Al guardar, el periodo quedara pagado y bloqueado para el Asistente Administrativo.</span>
                <button class="button primary" type="submit">Guardar pago</button>
            </div>
        </form>
    </x-app-shell>
@endsection
