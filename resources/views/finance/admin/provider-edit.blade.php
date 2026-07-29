@extends('layouts.app')

@section('body')
    <x-app-shell title="Editar proveedor">
        <form class="panel" method="POST" action="{{ route('finance.admin.providers.update', $provider) }}">
            @csrf
            @method('PUT')
            <div class="panel-header">
                <div>
                    <h2>Editar proveedor</h2>
                    <p class="fine-print">Proveedor dado de alta por {{ $provider->buyer?->name ?? 'Sin comprador' }}.</p>
                </div>
                <a class="button ghost" href="{{ route('finance.admin.providers') }}">Volver</a>
            </div>

            <div class="grid-3">
                <label>
                    Razon Social
                    <input name="business_name" value="{{ old('business_name', $provider->business_name) }}" required>
                </label>
                <label>
                    RFC
                    <input name="rfc" value="{{ old('rfc', $provider->rfc) }}" required>
                </label>
                <label>
                    Giro
                    <select name="business_line_id" required>
                        @foreach ($businessLines as $line)
                            <option value="{{ $line->id }}" @selected((int) old('business_line_id', $provider->provider_business_line_id) === $line->id || (! $provider->provider_business_line_id && $provider->business_line === $line->name))>{{ $line->name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="grid-3">
                <label>
                    Banco
                    <input name="bank" value="{{ old('bank', $provider->bank) }}" required>
                </label>
                <label>
                    Cuenta
                    <input name="account_number" value="{{ old('account_number', $provider->account_number) }}" required>
                </label>
                <label>
                    CLABE
                    <input name="clabe" value="{{ old('clabe', $provider->clabe) }}" maxlength="18" required>
                </label>
            </div>

            <label>
                Referencia
                <input name="reference" value="{{ old('reference', $provider->reference) }}" placeholder="Referencia bancaria o linea de captura">
            </label>

            <div class="form-actions">
                <span class="fine-print">Los cambios se reflejaran en OC vigentes e historial.</span>
                <button class="button primary" type="submit">Guardar cambios</button>
            </div>
        </form>
    </x-app-shell>
@endsection
