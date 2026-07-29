@extends('layouts.app')

@section('body')
    <x-app-shell title="Alta de empresas">
        <section class="panel">
            <div>
                <h2>Alta de empresas</h2>
            </div>

            <form class="stack" method="POST" action="{{ route('finance.admin.companies.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid-3">
                    <label>Razon social<input name="name" value="{{ old('name') }}" required></label>
                    <label>RFC<input name="rfc" value="{{ old('rfc') }}" required></label>
                    <label>Logotipo<input name="logo" type="file" accept="image/*"></label>
                </div>
                <label>Direccion<textarea name="address" required>{{ old('address') }}</textarea></label>
                <label>
                    Almacenes
                    <div id="warehouses-list" style="display:flex;flex-direction:column;gap:6px;margin-bottom:6px"></div>
                    <button type="button" class="button ghost small" onclick="addWarehouse()">+ Agregar almacen</button>
                </label>
                <label>
                    Observaciones para OC
                    <textarea name="purchase_order_notes" placeholder="Ej: caducidad minima, documentos requeridos, condiciones de entrega o pago.">{{ old('purchase_order_notes') }}</textarea>
                </label>
                <div>
                    <label>Compradores autorizados</label>
                    <div class="item-actions">
                        @forelse ($buyers as $buyer)
                            <label style="display:flex; gap:6px; align-items:center">
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
                            <th>Acciones</th>
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
                                            {{ $wh['name'] }}@if($wh['short_name']) <span class="fine-print">({{ $wh['short_name'] }})</span>@endif{{ $loop->last ? '' : ', ' }}
                                        @endforeach
                                    @else
                                        Sin almacenes
                                    @endif
                                </td>
                                <td>{{ $company->created_at?->format('d/m/Y') }}</td>
                                <td>
                                    <div class="item-actions">
                                        <a class="button ghost small" href="{{ route('finance.admin.companies.edit', $company) }}">Editar</a>
                                        <button class="button danger small" type="button" data-dialog-target="delete-company-{{ $company->id }}">Eliminar</button>
                                    </div>

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
                            <tr><td colspan="7">No hay empresas registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

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

