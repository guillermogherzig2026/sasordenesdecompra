@extends('layouts.app')

@section('body')
    <x-app-shell title="Alta de proveedores">
        <section class="panel">
            <div>
                <h2>Nuevo proveedor</h2>
                <p class="fine-print">Los proveedores dados de alta aqui quedan disponibles para las ordenes de compra del comprador asignado.</p>
            </div>

            <form class="stack" method="POST" action="{{ route('finance.admin.providers.store') }}">
                @csrf
                <div class="grid-3">
                    <label>
                        Comprador
                        <select name="buyer_id" required>
                            <option value="">Seleccionar comprador...</option>
                            @foreach ($buyers as $buyer)
                                <option value="{{ $buyer->id }}" @selected((int) old('buyer_id') === $buyer->id)>{{ $buyer->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        Razon social
                        <input name="business_name" value="{{ old('business_name') }}" required>
                    </label>
                    <label>
                        RFC
                        <input name="rfc" value="{{ old('rfc') }}" required>
                    </label>
                </div>

                <div class="grid-4">
                    <label>
                        Giro de proveeduria
                        <select name="business_line_id" data-provider-line-select required>
                            @foreach ($businessLines as $line)
                                <option value="{{ $line->id }}" @selected((int) old('business_line_id') === $line->id)>{{ $line->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        Subcategoria
                        <select name="business_subcategory_id" data-provider-subcategory-select>
                            <option value="">Sin subcategoria</option>
                            @foreach ($businessLines as $line)
                                @foreach ($line->subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}" data-line-id="{{ $line->id }}" @selected((int) old('business_subcategory_id') === $subcategory->id)>{{ $subcategory->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </label>
                    <label>
                        Banco
                        <input name="bank" value="{{ old('bank') }}" required>
                    </label>
                    <label>
                        Cuenta
                        <input name="account_number" value="{{ old('account_number') }}" required>
                    </label>
                </div>

                <div class="grid-3">
                    <label>Contacto<input name="contact_name" value="{{ old('contact_name') }}"></label>
                    <label>Teléfono<input name="phone" value="{{ old('phone') }}"></label>
                    <label>Dirección<input name="address" value="{{ old('address') }}"></label>
                </div>

                <div class="grid-2">
                    <label>
                        CLABE
                        <input name="clabe" value="{{ old('clabe') }}" maxlength="18" required>
                    </label>
                    <label>
                        Referencia
                        <input name="reference" value="{{ old('reference') }}" placeholder="Referencia bancaria o linea de captura">
                    </label>
                </div>

                <div class="form-actions">
                    <span class="fine-print">La CLABE debe tener 18 digitos.</span>
                    <button class="button primary" type="submit">Guardar proveedor</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Alta de proveedores</h2>
                    <p class="fine-print">Vista consolidada de todos los proveedores dados de alta por compradores.</p>
                </div>
                <form class="toolbar" method="GET" action="{{ route('finance.admin.providers') }}">
                    <input name="q" value="{{ $query }}" placeholder="Buscar proveedor...">
                    <a class="button ghost" href="{{ route('reports.download', 'providers') }}">Exportar CSV</a>
                    <a class="button ghost" href="{{ route('reports.download', 'providers-excel') }}">Exportar Excel</a>
                </form>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Acciones</th>
                            <th>Comprador</th>
                            <th>Razon Social</th>
                            <th>RFC</th>
                            <th>Contacto</th>
                            <th>Teléfono</th>
                            <th>Dirección</th>
                            <th>Giro</th>
                            <th>Subcategoria</th>
                            <th>Banco</th>
                            <th>Cuenta</th>
                            <th>CLABE</th>
                            <th>Referencia</th>
                            <th>Fecha alta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($providers as $provider)
                            <tr>
                                <td><a class="button ghost small" href="{{ route('finance.admin.providers.edit', $provider) }}">Editar</a></td>
                                <td>{{ $provider->buyer?->name ?? 'Sin comprador' }}</td>
                                <td>{{ $provider->business_name }}</td>
                                <td>{{ $provider->rfc }}</td>
                                <td>{{ $provider->contact_name ?: 'N/A' }}</td>
                                <td>{{ $provider->phone ?: 'N/A' }}</td>
                                <td>{{ $provider->address ?: 'N/A' }}</td>
                                <td>{{ $provider->business_line }}</td>
                                <td>{{ $provider->businessSubcategory?->name ?? $provider->provider_business_subcategory ?? 'Sin subcategoria' }}</td>
                                <td>{{ $provider->bank }}</td>
                                <td>{{ $provider->account_number }}</td>
                                <td>{{ $provider->clabe }}</td>
                                <td>{{ $provider->reference ?: 'Sin referencia' }}</td>
                                <td>{{ $provider->created_at?->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="14">Aun no hay proveedores registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </x-app-shell>
@endsection
