@extends('layouts.app')

@section('body')
    <x-app-shell title="Editar empresa">
        <form class="panel" method="POST" action="{{ route('finance.admin.companies.update', $company) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="panel-header">
                <div>
                    <h2>Editar empresa</h2>
                    <p class="fine-print">Actualiza datos fiscales, logotipo y compradores autorizados.</p>
                </div>
                <a class="button ghost" href="{{ route('finance.admin.companies') }}">Volver</a>
            </div>

            <div class="grid-3">
                <label>
                    Razon Social
                    <input name="name" value="{{ old('name', $company->name) }}" required>
                </label>
                <label>
                    RFC
                    <input name="rfc" value="{{ old('rfc', $company->rfc) }}" required>
                </label>
                <label>
                    Logotipo
                    <input name="logo" type="file" accept="image/*">
                    @if ($company->logo_path)
                        <small class="fine-print">
                            Logo actual:
                            <a href="{{ route('companies.logo', $company) }}" target="_blank">Ver logotipo cargado</a>
                        </small>
                    @else
                        <small class="fine-print">Sin logotipo cargado.</small>
                    @endif
                </label>
            </div>

            <label>
                Direccion
                <textarea name="address" required>{{ old('address', $company->address) }}</textarea>
            </label>

            <label>
                Almacenes
                <div id="warehouses-list" style="display:flex;flex-direction:column;gap:6px;margin-bottom:6px">
                    @foreach ($company->warehouseObjects() as $warehouse)
                        <div style="display:flex;gap:6px;align-items:center">
                            <input name="warehouses[{{ $loop->index }}][name]" value="{{ $warehouse['name'] }}" placeholder="Nombre del almacen" required style="flex:3">
                            <input name="warehouses[{{ $loop->index }}][short_name]" value="{{ $warehouse['short_name'] }}" placeholder="Nombre corto (ej: AC)" style="flex:1">
                            <button type="button" class="button ghost small" onclick="this.parentElement.remove()">✕</button>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="button ghost small" onclick="addWarehouse()">+ Agregar almacen</button>
            </label>

            <label>
                Observaciones para OC
                <textarea name="purchase_order_notes" placeholder="Ej: caducidad minima, documentos requeridos, condiciones de entrega o pago.">{{ old('purchase_order_notes', $company->purchase_order_notes) }}</textarea>
            </label>

            <div>
                <label>Compradores autorizados</label>
                <div class="item-actions">
                    @foreach ($buyers as $buyer)
                        <label style="display:flex; gap:6px; align-items:center">
                            <input name="buyer_ids[]" type="checkbox" value="{{ $buyer->id }}" @checked(in_array($company->name, $buyer->authorizedCompanyNames(), true))>
                            {{ $buyer->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-actions">
                <span class="fine-print">Los cambios se reflejaran en nuevas ordenes y autorizaciones.</span>
                <button class="button primary" type="submit">Guardar cambios</button>
            </div>
        </form>
    </x-app-shell>

    <script>
        function addWarehouse(name = '', shortName = '') {
            const list = document.getElementById('warehouses-list');
            const idx = list.children.length;
            const row = document.createElement('div');
            row.style.cssText = 'display:flex;gap:6px;align-items:center';
            row.innerHTML = `
                <input name="warehouses[${idx}][name]" value="${name}" placeholder="Nombre del almacen" required style="flex:3">
                <input name="warehouses[${idx}][short_name]" value="${shortName}" placeholder="Nombre corto (ej: AC)" style="flex:1">
                <button type="button" class="button ghost small" onclick="this.parentElement.remove()">✕</button>
            `;
            list.appendChild(row);
        }
    </script>
@endsection


