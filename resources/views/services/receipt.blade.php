@extends('layouts.app')

@section('body')
    @php($periodAmount = (float) ($receipt?->amount ?? $service->cost))

    <x-app-shell title="Cargar factura de servicio">
        <form class="panel" method="POST" action="{{ route('services.receipt.store', [$service, $occurrence['due_date']]) }}" enctype="multipart/form-data">
            @csrf
            <div class="panel-header">
                <div>
                    <h2>{{ $service->folio }} · {{ $service->service_name }}</h2>
                    <p class="fine-print">Este archivo sera el soporte para que Finanzas realice el pago. No cambia el estatus a pagado.</p>
                </div>
                <a class="button ghost" href="{{ route('services.months') }}">Volver</a>
            </div>

            <section class="panel" style="box-shadow:none">
                <div class="grid-3">
                    <div><span class="fine-print">Proveedor</span><strong>{{ $service->provider }}</strong></div>
                    <div><span class="fine-print">Periodo</span><strong>{{ \Illuminate\Support\Carbon::parse($occurrence['period_start'])->format('d/m/Y') }} al {{ \Illuminate\Support\Carbon::parse($occurrence['due_date'])->format('d/m/Y') }}</strong></div>
                    <div><span class="fine-print">Monto</span><strong>${{ number_format($periodAmount, 2) }}</strong></div>
                </div>
            </section>

            <div class="grid-2">
                <label>
                    Factura
                    <input name="support_file" type="file" required>
                </label>
                <label>
                    Fecha de factura
                    <input name="support_on" type="date" value="{{ now()->toDateString() }}" required>
                </label>
            </div>

            @if ($receipt?->support_original_name)
                <p class="fine-print">Archivo actual: {{ $receipt->support_original_name }}</p>
            @endif

            <div class="form-actions">
                <span class="fine-print">Finanzas vera el soporte en Pago Servicios.</span>
                <button class="button primary" type="submit">Guardar factura</button>
            </div>
        </form>
    </x-app-shell>
@endsection
