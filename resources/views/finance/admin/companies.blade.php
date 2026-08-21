@extends('layouts.app')

@section('body')
    <x-app-shell title="Alta de empresas">
        @php
            $companyAddress = old('address', '');
            $warehouseRows = old('warehouses');

            if (! is_array($warehouseRows) || empty($warehouseRows)) {
                $warehouseRows = [[
                    'name' => 'Almacen principal',
                    'short_name' => 'Principal',
                    'address' => $companyAddress,
                ]];
            }
        @endphp

        <section class="panel company-create-panel">
            <div class="company-create-heading">
                <h2>Alta de empresas</h2>
            </div>

            <form class="stack company-create-form" method="POST" action="{{ route('finance.admin.companies.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid-3">
                    <label>Razon social<input name="name" value="{{ old('name') }}" required></label>
                    <label>RFC<input name="rfc" value="{{ old('rfc') }}" required></label>
                    <label>Logotipo<input name="logo" type="file" accept="image/*"></label>
                </div>

                <div class="company-create-details">
                    <label>Direccion<textarea id="company-address" name="address" rows="2" required>{{ $companyAddress }}</textarea></label>
                    <label>
                        Observaciones para OC
                        <textarea name="purchase_order_notes" rows="2" placeholder="Ej: caducidad minima, documentos requeridos, condiciones de entrega o pago.">{{ old('purchase_order_notes') }}</textarea>
                    </label>
                </div>

                <div>
                    <div class="company-create-section-heading">
                        <div>
                            <label>Almacenes</label>
                            <p class="fine-print">Cada empresa debe conservar al menos un almacen. La direccion puede editarse de forma independiente.</p>
                        </div>
                        <button type="button" class="button ghost small" onclick="addWarehouse()">+ Agregar almacen</button>
                    </div>
                    <div id="warehouses-list" data-next-index="{{ count($warehouseRows) }}">
                        @foreach ($warehouseRows as $warehouse)
                            @php
                                $warehouseAddress = trim((string) ($warehouse['address'] ?? '')) ?: trim((string) $companyAddress);
                                $followsCompanyAddress = blank($warehouse['address'] ?? null)
                                    || $warehouseAddress === trim((string) $companyAddress);
                            @endphp
                            <div class="warehouse-row">
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
                </div>

                <div class="company-create-buyers">
                    <label>Compradores autorizados</label>
                    <div class="item-actions">
                        @forelse ($buyers as $buyer)
                            <label>
                                <input name="buyer_ids[]" type="checkbox" value="{{ $buyer->id }}">
                                {{ $buyer->name }}
                            </label>
                        @empty
                            <span class="fine-print">No hay compradores activos.</span>
                        @endforelse
                    </div>
                </div>
                <div class="form-actions">
                    <span class="fine-print">Despues de guardar la empresa, aparecera en Autorizaciones para asignarla a compradores.</span>
                    <button class="button primary" type="submit">Guardar empresa</button>
                </div>
            </form>
        </section>

        <style>
            .company-create-panel {
                width: min(100%, 1280px);
                margin-inline: auto;
                padding: 14px 16px;
                gap: 8px;
            }

            .company-create-heading h2 {
                margin: 0;
                font-size: 1.05rem;
            }

            .company-create-form {
                gap: 8px;
            }

            .company-create-form label {
                gap: 3px;
            }

            .company-create-form :is(input, select, textarea) {
                min-height: 34px;
                padding: 7px 9px;
                font-size: .88rem;
            }

            .company-create-form input[type="file"] {
                padding: 5px 7px;
            }

            .company-create-form textarea {
                min-height: 54px;
                max-height: 90px;
                resize: vertical;
            }

            .company-create-form .grid-3,
            .company-create-details {
                gap: 8px;
            }

            .company-create-details {
                display: grid;
                grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
            }

            .company-create-section-heading {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 10px;
            }

            .company-create-section-heading p {
                margin: 2px 0 0;
            }

            .company-create-form #warehouses-list {
                display: flex;
                flex-direction: column;
                gap: 6px;
                margin: 4px 0 0;
            }

            .company-create-form .warehouse-row {
                display: grid;
                grid-template-columns: minmax(180px, 1.35fr) minmax(110px, .7fr) minmax(260px, 2fr) auto;
                gap: 6px;
                align-items: center;
            }

            .company-create-form .warehouse-remove:disabled {
                background: #f1f5f9;
                border-color: #d7e0ea;
                color: #94a3b8;
                box-shadow: none;
                cursor: not-allowed;
                opacity: 1;
            }

            .company-create-buyers {
                display: grid;
                grid-template-columns: auto minmax(0, 1fr);
                align-items: center;
                gap: 8px 14px;
            }

            .company-create-buyers > label {
                margin: 0;
            }

            .company-create-buyers .item-actions {
                gap: 6px 18px;
            }

            .company-create-buyers .item-actions label {
                display: flex;
                align-items: center;
                gap: 5px;
                white-space: nowrap;
            }

            .company-create-buyers input[type="checkbox"] {
                width: 18px;
                height: 18px;
                min-height: 18px;
                padding: 0;
            }

            .company-create-form .form-actions {
                min-height: 34px;
            }

            @media (max-width: 900px) {
                .company-create-details {
                    grid-template-columns: 1fr;
                }

                .company-create-form .warehouse-row {
                    grid-template-columns: 1fr 1fr;
                }

                .company-create-form .warehouse-row [data-warehouse-address] {
                    grid-column: 1 / -1;
                }

                .company-create-buyers {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 620px) {
                .company-create-form .warehouse-row {
                    grid-template-columns: 1fr;
                }

                .company-create-form .warehouse-row [data-warehouse-address] {
                    grid-column: auto;
                }

                .company-create-section-heading {
                    align-items: flex-start;
                    flex-direction: column;
                }
            }
        </style>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Empresas registradas</h2>
                    <p class="fine-print">Catalogo usado en ordenes de compra y autorizaciones.</p>
                </div>
                <form class="toolbar" method="GET" action="{{ route('finance.admin.companies') }}">
                    <input name="q" value="{{ $query }}" placeholder="Buscar empresa...">
                    <a class="button ghost" href="{{ route('reports.download', 'companies') }}">Exportar Excel</a>
                </form>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Razon Social</th>
                            <th>RFC</th>
                            <th>Direccion</th>
                            <th>Almacenes</th>
                            <th>Fecha alta</th>
                            <th>Editar</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($companies as $company)
                            <tr>
                                <td><span class="company-logo-thumb">{{ $company->initials() }}</span></td>
                                <td>{{ $company->name }}</td>
                                <td>{{ $company->rfc }}</td>
                                <td>{{ $company->address }}</td>
                                <td>
                                    @php $objects = $company->warehouseObjects(); @endphp
                                    @if (count($objects))
                                        @foreach ($objects as $wh)
                                            <div @if (! $loop->last) style="margin-bottom:6px" @endif>
                                                <strong>{{ $wh['name'] }}</strong>
                                                @if($wh['short_name']) <span class="fine-print">({{ $wh['short_name'] }})</span>@endif
                                                <div class="fine-print">{{ $wh['address'] }}</div>
                                            </div>
                                        @endforeach
                                    @else
                                        Sin almacenes
                                    @endif
                                </td>
                                <td>{{ $company->created_at?->format('d/m/Y') }}</td>
                                <td>
                                    <a class="button ghost small" href="{{ route('finance.admin.companies.edit', $company) }}">Editar</a>
                                </td>
                                <td>
                                    <button class="button danger small" type="button" data-dialog-target="delete-company-{{ $company->id }}">Eliminar</button>
                                    <dialog class="confirm-dialog" id="delete-company-{{ $company->id }}">
                                        <form class="confirm-card" method="POST" action="{{ route('finance.admin.companies.destroy', $company) }}">
                                            @csrf
                                            @method('DELETE')
                                            <h3>Eliminar empresa</h3>
                                            <p>Estas seguro que quieres eliminar {{ $company->name }}.</p>
                                            <div class="form-actions">
                                                <button class="button danger" type="submit">Si eliminar</button>
                                                <button class="button ghost" type="button" data-dialog-close>Cancelar</button>
                                            </div>
                                        </form>
                                    </dialog>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8">No hay empresas registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

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

            document.querySelectorAll('[data-dialog-target]').forEach((button) => {
                button.addEventListener('click', () => {
                    const dialog = document.getElementById(button.dataset.dialogTarget);
                    if (dialog) dialog.showModal();
                });
            });

            document.querySelectorAll('[data-dialog-close]').forEach((button) => {
                button.addEventListener('click', () => {
                    button.closest('dialog')?.close();
                });
            });
        </script>
    </x-app-shell>
@endsection
