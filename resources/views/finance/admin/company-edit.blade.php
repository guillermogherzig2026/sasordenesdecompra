@extends('layouts.app')

@section('body')
    <x-app-shell title="Editar empresa">
        @php
            $companyAddress = old('address', $company->address);
            $warehouseRows = old('warehouses', $company->warehouseObjects());

            if (! is_array($warehouseRows) || empty($warehouseRows)) {
                $warehouseRows = [[
                    'name' => 'Almacen principal',
                    'short_name' => 'Principal',
                    'address' => $companyAddress,
                ]];
            }
        @endphp

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
                <textarea id="company-address" name="address" required>{{ $companyAddress }}</textarea>
            </label>

            <div>
                <label>Almacenes</label>
                <p class="fine-print">Cada empresa debe conservar al menos un almacen. La direccion del almacen puede editarse de forma independiente.</p>
                <div id="warehouses-list" data-next-index="{{ count($warehouseRows) }}" style="display:flex;flex-direction:column;gap:8px;margin:6px 0">
                    @foreach ($warehouseRows as $warehouse)
                        @php
                            $warehouseAddress = trim((string) ($warehouse['address'] ?? '')) ?: trim((string) $companyAddress);
                            $followsCompanyAddress = blank($warehouse['address'] ?? null)
                                || $warehouseAddress === trim((string) $companyAddress);
                        @endphp
                        <div class="warehouse-row" style="display:grid;grid-template-columns:minmax(180px,2fr) minmax(120px,1fr) minmax(260px,3fr) auto;gap:6px;align-items:center">
                            <input name="warehouses[{{ $loop->index }}][name]" value="{{ $warehouse['name'] ?? '' }}" placeholder="Nombre del almacen" required>
                            <input name="warehouses[{{ $loop->index }}][short_name]" value="{{ $warehouse['short_name'] ?? '' }}" placeholder="Nombre corto">
                            <input
                                name="warehouses[{{ $loop->index }}][address]"
                                value="{{ $warehouseAddress }}"
                                placeholder="Direccion del almacen"
                                data-warehouse-address
                                data-follows-company-address="{{ $followsCompanyAddress ? 'true' : 'false' }}"
                                required
                            >
                            <button type="button" class="button ghost small warehouse-remove" onclick="removeWarehouse(this)">Quitar</button>
                        </div>
                    @endforeach
                </div>
                <button type="button" class="button ghost small" onclick="addWarehouse()">+ Agregar almacen</button>
            </div>

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
        function refreshWarehouseRemoveButtons() {
            const rows = document.querySelectorAll('#warehouses-list .warehouse-row');

            rows.forEach((row) => {
                const button = row.querySelector('.warehouse-remove');
                if (button) button.disabled = rows.length === 1;
            });
        }

        function removeWarehouse(button) {
            const rows = document.querySelectorAll('#warehouses-list .warehouse-row');
            if (rows.length <= 1) return;

            button.closest('.warehouse-row')?.remove();
            refreshWarehouseRemoveButtons();
        }

        function addWarehouse() {
            const list = document.getElementById('warehouses-list');
            const idx = Number(list.dataset.nextIndex || 0);
            const companyAddress = document.getElementById('company-address')?.value || '';
            const row = document.createElement('div');
            row.className = 'warehouse-row';
            row.style.cssText = 'display:grid;grid-template-columns:minmax(180px,2fr) minmax(120px,1fr) minmax(260px,3fr) auto;gap:6px;align-items:center';
            row.innerHTML = `
                <input name="warehouses[${idx}][name]" placeholder="Nombre del almacen" required>
                <input name="warehouses[${idx}][short_name]" placeholder="Nombre corto">
                <input name="warehouses[${idx}][address]" placeholder="Direccion del almacen" data-warehouse-address data-follows-company-address="true" required>
                <button type="button" class="button ghost small warehouse-remove" onclick="removeWarehouse(this)">Quitar</button>
            `;
            list.appendChild(row);
            list.dataset.nextIndex = String(idx + 1);

            const addressInput = row.querySelector('[data-warehouse-address]');
            addressInput.value = companyAddress;
            addressInput.addEventListener('input', (event) => {
                event.currentTarget.dataset.followsCompanyAddress = 'false';
            });

            refreshWarehouseRemoveButtons();
        }

        document.getElementById('company-address')?.addEventListener('input', (event) => {
            document.querySelectorAll('[data-warehouse-address][data-follows-company-address="true"]')
                .forEach((input) => input.value = event.currentTarget.value);
        });

        document.querySelectorAll('[data-warehouse-address]').forEach((input) => {
            input.addEventListener('input', () => input.dataset.followsCompanyAddress = 'false');
        });

        refreshWarehouseRemoveButtons();
    </script>
@endsection
