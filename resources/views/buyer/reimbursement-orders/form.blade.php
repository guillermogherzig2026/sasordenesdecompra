@extends('layouts.app')

@section('body')
    <x-app-shell title="Nueva orden de reembolso">
        <section class="panel reimbursement-panel">
            <div>
                <h2>Solicitud de reembolso</h2>
                <p class="fine-print">Captura proveedor, monto, cotizacion y soporte del producto o servicio si ya lo tienes.</p>
            </div>

            <form class="stack reimbursement-form" method="POST" action="{{ route('buyer.reimbursement-orders.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid-3">
                    <label>Empresa
                        <select name="company_id" required>
                            <option value="">Selecciona...</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Proveedor<input name="provider" value="{{ old('provider') }}" required></label>
                    <label>Monto<input name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount') }}" required></label>
                </div>
                <label class="reimbursement-concept">Concepto<input name="concept" value="{{ old('concept') }}" placeholder="Producto o servicio a reembolsar"></label>
                <div class="grid-2">
                    <label>Cotizacion<input name="quote_file" type="file" accept=".pdf,.jpg,.jpeg,.png" required></label>
                    <label>Soporte del producto o servicio<input name="support_file" type="file" accept=".pdf,.jpg,.jpeg,.png"></label>
                </div>
                <label>Notas<textarea name="notes" rows="3">{{ old('notes') }}</textarea></label>

                <div class="form-actions">
                    <a class="button ghost" href="{{ route('buyer.reimbursement-orders.index') }}">Cancelar</a>
                    <button class="button primary" type="submit">Enviar OR</button>
                </div>
            </form>
        </section>
    </x-app-shell>
@endsection
