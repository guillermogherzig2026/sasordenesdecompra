@extends('layouts.app')

@section('body')
    <x-app-shell title="Alta de proveedor">
        <section class="panel">
            <div>
                <h2>Nuevo proveedor</h2>
                <p class="fine-print">Los proveedores dados de alta aqui quedan disponibles para tus ordenes de compra.</p>
            </div>

            <form class="stack" method="POST" action="{{ route('buyer.providers.store') }}">
                @csrf
                <div class="grid-3">
                    <label>
                        Razon social
                        <input name="business_name" value="{{ old('business_name') }}" required>
                    </label>
                    <label>
                        RFC
                        <input name="rfc" value="{{ old('rfc') }}" required>
                    </label>
                    <label>
                        Giro de proveeduria
                        <select name="business_line_id" required>
                            @foreach ($businessLines as $line)
                                <option value="{{ $line->id }}" @selected((int) old('business_line_id') === $line->id)>{{ $line->name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                <div class="grid-3">
                    <label>
                        Banco
                        <input name="bank" value="{{ old('bank') }}" required>
                    </label>
                    <label>
                        Cuenta
                        <input name="account_number" value="{{ old('account_number') }}" required>
                    </label>
                    <label>
                        CLABE
                        <input name="clabe" value="{{ old('clabe') }}" maxlength="18" required>
                    </label>
                </div>
                <label>
                    Referencia
                    <input name="reference" value="{{ old('reference') }}" placeholder="Referencia bancaria o linea de captura">
                </label>
                <div class="form-actions">
                    <span class="fine-print">La CLABE debe tener 18 digitos.</span>
                    <button class="button primary" type="submit">Guardar proveedor</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Mis proveedores</h2>
                    <p class="fine-print">Catalogo propio del comprador autenticado.</p>
                </div>
                <form class="toolbar" method="GET" action="{{ route('buyer.providers.index') }}">
                    <input name="q" value="{{ $query }}" placeholder="Buscar proveedor">
                    <button class="button ghost" type="submit">Buscar</button>
                </form>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Razon social</th>
                            <th>RFC</th>
                            <th>Giro</th>
                            <th>Banco</th>
                            <th>Cuenta</th>
                            <th>CLABE</th>
                            <th>Referencia</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($providers as $provider)
                            <tr>
                                <td><strong>{{ $provider->business_name }}</strong></td>
                                <td>{{ $provider->rfc }}</td>
                                <td>{{ $provider->business_line }}</td>
                                <td>{{ $provider->bank }}</td>
                                <td>{{ $provider->account_number }}</td>
                                <td>{{ $provider->clabe }}</td>
                                <td>{{ $provider->reference ?: 'Sin referencia' }}</td>
                                <td>
                                    <button class="button ghost small editor-toggle" type="button" data-target="provider-editor-{{ $provider->id }}">Editar</button>
                                </td>
                            </tr>
                            <tr class="editor-row" id="provider-editor-{{ $provider->id }}" hidden>
                                <td colspan="8">
                                    <form class="stack" method="POST" action="{{ route('buyer.providers.update', $provider) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid-3">
                                            <label>Razon social<input name="business_name" value="{{ old('business_name', $provider->business_name) }}" required></label>
                                            <label>RFC<input name="rfc" value="{{ old('rfc', $provider->rfc) }}" required></label>
                                            <label>Giro de proveeduria
                                                <select name="business_line_id" required>
                                                    @foreach ($businessLines as $line)
                                                        <option value="{{ $line->id }}" @selected((int) old('business_line_id', $provider->provider_business_line_id) === $line->id || (! $provider->provider_business_line_id && $provider->business_line === $line->name))>{{ $line->name }}</option>
                                                    @endforeach
                                                </select>
                                            </label>
                                        </div>
                                        <div class="grid-3">
                                            <label>Banco<input name="bank" value="{{ old('bank', $provider->bank) }}" required></label>
                                            <label>Cuenta<input name="account_number" value="{{ old('account_number', $provider->account_number) }}" required></label>
                                            <label>CLABE<input name="clabe" value="{{ old('clabe', $provider->clabe) }}" maxlength="18" required></label>
                                        </div>
                                        <label>Referencia<input name="reference" value="{{ old('reference', $provider->reference) }}" placeholder="Referencia bancaria o linea de captura"></label>
                                        <div class="form-actions">
                                            <span class="fine-print">La CLABE debe tener 18 digitos.</span>
                                            <button class="button primary small" type="submit">Guardar cambios</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">No hay proveedores registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <script>
            document.querySelectorAll('.editor-toggle').forEach((button) => {
                button.addEventListener('click', () => {
                    const row = document.getElementById(button.dataset.target);
                    if (!row) return;

                    const isHidden = row.hasAttribute('hidden');
                    row.toggleAttribute('hidden', !isHidden);
                    button.textContent = isHidden ? 'Cerrar' : 'Editar';
                });
            });
        </script>
    </x-app-shell>
@endsection
