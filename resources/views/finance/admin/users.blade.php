@extends('layouts.app')

@php
    $roleLabels = [
        'buyer' => 'Compras y Suministros',
        'inventory' => 'Control de inventarios',
        'administrative_assistant' => 'Asistente Administrativo',
    ];
    $buyerSubroleLabels = [
        'purchases' => 'Compras',
        'supplies' => 'Suministros',
        'reimbursements' => 'Reembolsos',
    ];
    $createBuyerSubroles = old('buyer_subroles', ['purchases']);
    $createBuyerSubroles = is_array($createBuyerSubroles) ? $createBuyerSubroles : [$createBuyerSubroles];
@endphp

@section('body')
    <x-app-shell title="Autorizaciones de usuarios">
        <section class="panel">
            <div>
                <h2>Alta de usuarios y autorizaciones</h2>
                <p class="fine-print">Finanzas puede crear usuarios de Compras y Suministros, inventarios o asistentes administrativos. Al elegir Compras y Suministros se habilita la subcategoria operativa.</p>
            </div>

            <form class="stack" method="POST" action="{{ route('finance.admin.users.store') }}">
                @csrf
                <div class="grid-3">
                    <label>Nombre<input name="name" value="{{ old('name') }}" required></label>
                    <label>Correo<input name="email" type="email" value="{{ old('email') }}" required></label>
                    <label>Contrasena inicial<input name="password" value="{{ old('password') }}" required></label>
                </div>
                <div class="grid-2">
                    <div class="role-subcategory-stack">
                        <label>Rol
                            <select name="role" class="role-select" required>
                                @foreach ($roleLabels as $role => $label)
                                    <option value="{{ $role }}" @selected(old('role', 'buyer') === $role)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <div class="buyer-subrole-box">
                            <span class="form-label">Subcategoria</span>
                            <div class="checkbox-grid">
                                @foreach ($buyerSubroleLabels as $subrole => $label)
                                    <label>
                                        <input name="buyer_subroles[]" type="checkbox" value="{{ $subrole }}" @checked(in_array($subrole, $createBuyerSubroles, true))>
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="companies-box">
                        <div class="company-selector" data-company-selector>
                            <div class="company-selector-header">
                                <label>Empresas y almacenes autorizados</label>
                                <span data-company-count></span>
                            </div>
                            <input class="company-selector-search" type="search" placeholder="Buscar empresa o almacen...">
                            <div class="company-selector-actions">
                                <button class="button ghost small" type="button" data-company-select-all>Todas</button>
                                <button class="button ghost small" type="button" data-company-clear>Limpiar</button>
                            </div>
                            <div class="company-selector-list">
                                @foreach ($supplyWarehouses as $supplyWarehouse)
                                    <div class="company-selector-option supply-warehouse-option with-warehouses" data-company-option style="display:block;height:auto;min-height:76px;overflow:visible;">
                                        <label class="company-selector-main">
                                            <input class="company-checkbox supply-warehouse-checkbox" name="supply_warehouses[]" type="checkbox" value="{{ $supplyWarehouse['key'] }}" checked>
                                            <span>{{ $supplyWarehouse['label'] }}</span>
                                        </label>
                                        <div class="warehouse-selector-list" style="display:flex;flex-wrap:wrap;gap:6px;padding-left:25px;padding-top:6px;min-height:30px;overflow:visible;">
                                            <small class="fine-print">
                                                Surte a:
                                                {{ collect($supplyWarehouse['companies'])->pluck('name')->implode(', ') ?: 'Sin empresas asignadas' }}
                                                @if (! empty($supplyWarehouse['address']))
                                                    · {{ $supplyWarehouse['address'] }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                @endforeach
                                @foreach ($companies as $company)
                                    @php $warehouseObjects = $company->warehouseObjects(); @endphp
                                    <div class="company-selector-option with-warehouses" data-company-option style="display:block;height:auto;min-height:76px;overflow:visible;">
                                        <label class="company-selector-main">
                                            <input class="company-checkbox" name="companies[]" type="checkbox" value="{{ $company->id }}" checked>
                                            <span>{{ $company->name }}</span>
                                        </label>
                                        <div class="warehouse-selector-list" style="display:flex;flex-wrap:wrap;gap:6px;padding-left:25px;padding-top:6px;min-height:30px;overflow:visible;">
                                            @forelse ($warehouseObjects as $warehouse)
                                                <label>
                                                    <input name="warehouses[{{ $company->id }}][]" type="checkbox" value="{{ $warehouse['name'] }}" checked>
                                                    {{ $warehouse['name'] }}@if($warehouse['short_name']) <span class="fine-print">({{ $warehouse['short_name'] }})</span>@endif
                                                </label>
                                            @empty
                                                <span class="fine-print">Sin almacenes registrados.</span>
                                            @endforelse
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <span class="fine-print">Los usuarios nuevos podran iniciar sesion con el correo y contrasena indicados.</span>
                    <button class="button primary" type="submit">Crear usuario</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <h2>Usuarios autorizados</h2>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Subcategoria</th>
                            <th>Empresas</th>
                            <th>Almacenes</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $managedUser)
                            @php $assignments = $managedUser->normalizedCompanyAssignments(); @endphp
                            <tr>
                                <td>{{ $managedUser->name }}</td>
                                <td>{{ $managedUser->email }}</td>
                                <td>{{ $roleLabels[$managedUser->role] ?? $managedUser->role }}</td>
                                <td>
                                    @if ($managedUser->role === 'buyer')
                                        {{ $managedUser->buyerSubroleLabel() }}
                                    @else
                                        <span class="fine-print">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if (in_array($managedUser->role, ['buyer', 'inventory'], true))
                                        @forelse ($assignments as $assignment)
                                            <div>{{ $assignment['name'] }}</div>
                                        @empty
                                            Sin empresas asignadas
                                        @endforelse
                                    @else
                                        Todas
                                    @endif
                                </td>
                                <td>
                                    @if (in_array($managedUser->role, ['buyer', 'inventory'], true))
                                        @forelse ($assignments as $assignment)
                                            <div>
                                                <strong>{{ $assignment['name'] }}:</strong>
                                                @if (count($assignment['warehouses']))
                                                    {{ implode(', ', $assignment['warehouses']) }}
                                                @else
                                                    <span class="fine-print">Sin almacenes</span>
                                                @endif
                                            </div>
                                        @empty
                                            <span class="fine-print">—</span>
                                        @endforelse
                                    @else
                                        <span class="fine-print">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status {{ $managedUser->active ? 'approved' : 'canceled' }}">{{ $managedUser->active ? 'Activo' : 'Inactivo' }}</span>
                                </td>
                                <td class="row-actions">
                                    <button class="button ghost small editor-toggle" type="button" data-target="finance-editor-{{ $managedUser->id }}">Editar</button>
                                    <form class="inline-form" method="POST" action="{{ route('finance.admin.users.toggle', $managedUser) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="button {{ $managedUser->active ? 'danger' : 'primary' }} small" type="submit">
                                            {{ $managedUser->active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="editor-row" id="finance-editor-{{ $managedUser->id }}" hidden>
                                <td colspan="8">
                                    <form class="stack" method="POST" action="{{ route('finance.admin.users.update', $managedUser) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid-3">
                                            <label>Nombre<input name="name" value="{{ $managedUser->name }}" required></label>
                                            <label>Correo<input name="email" type="email" value="{{ $managedUser->email }}" required></label>
                                            <label>Nueva contrasena<input name="password" type="text" placeholder="Sin cambio"></label>
                                        </div>
                                        <div class="grid-2">
                                            <div class="role-subcategory-stack">
                                                <label>Rol
                                                    <select name="role" class="role-select" required>
                                                        @foreach ($roleLabels as $role => $label)
                                                            <option value="{{ $role }}" @selected($managedUser->role === $role)>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </label>
                                                @php $selectedBuyerSubroles = $managedUser->buyerSubroles(); @endphp
                                                <div class="buyer-subrole-box">
                                                    <span class="form-label">Subcategoria</span>
                                                    <div class="checkbox-grid">
                                                        @foreach ($buyerSubroleLabels as $subrole => $label)
                                                            <label>
                                                                <input name="buyer_subroles[]" type="checkbox" value="{{ $subrole }}" @checked(in_array($subrole, $selectedBuyerSubroles, true))>
                                                                {{ $label }}
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="companies-box">
                                                <div class="company-selector" data-company-selector>
                                                    <div class="company-selector-header">
                                                        <label>Empresas y almacenes autorizados</label>
                                                        <span data-company-count></span>
                                                    </div>
                                                    <input class="company-selector-search" type="search" placeholder="Buscar empresa o almacen...">
                                                    <div class="company-selector-actions">
                                                        <button class="button ghost small" type="button" data-company-select-all>Todas</button>
                                                        <button class="button ghost small" type="button" data-company-clear>Limpiar</button>
                                                    </div>
                                                    <div class="company-selector-list">
                                                        @php
                                                            $selectedSupplyWarehouseKeys = collect($supplyWarehouses)
                                                                ->filter(fn (array $supplyWarehouse) => collect($supplyWarehouse['companies'])->contains(fn (array $company) => in_array($supplyWarehouse['label'], $managedUser->authorizedWarehousesFor($company['name']), true)))
                                                                ->pluck('key')
                                                                ->all();
                                                        @endphp
                                                        @foreach ($supplyWarehouses as $supplyWarehouse)
                                                            <div class="company-selector-option supply-warehouse-option with-warehouses" data-company-option style="display:block;height:auto;min-height:76px;overflow:visible;">
                                                                <label class="company-selector-main">
                                                                    <input class="company-checkbox supply-warehouse-checkbox" name="supply_warehouses[]" type="checkbox" value="{{ $supplyWarehouse['key'] }}" @checked(in_array($supplyWarehouse['key'], $selectedSupplyWarehouseKeys, true))>
                                                                    <span>{{ $supplyWarehouse['label'] }}</span>
                                                                </label>
                                                                <div class="warehouse-selector-list" style="display:flex;flex-wrap:wrap;gap:6px;padding-left:25px;padding-top:6px;min-height:30px;overflow:visible;">
                                                                    <small class="fine-print">
                                                                        Surte a:
                                                                        {{ collect($supplyWarehouse['companies'])->pluck('name')->implode(', ') ?: 'Sin empresas asignadas' }}
                                                                        @if (! empty($supplyWarehouse['address']))
                                                                            · {{ $supplyWarehouse['address'] }}
                                                                        @endif
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                        @foreach ($companies as $company)
                                                            @php
                                                                $selectedCompanies = $managedUser->authorizedCompanyNames();
                                                                $selectedWarehouses = $managedUser->authorizedWarehousesFor($company->name);
                                                                $warehouseObjects = $company->warehouseObjects();
                                                                $companySelected = in_array($company->name, $selectedCompanies, true);
                                                            @endphp
                                                            <div class="company-selector-option with-warehouses" data-company-option style="display:block;height:auto;min-height:76px;overflow:visible;">
                                                                <label class="company-selector-main">
                                                                    <input class="company-checkbox" name="companies[]" type="checkbox" value="{{ $company->id }}" @checked($companySelected)>
                                                                    <span>{{ $company->name }}</span>
                                                                </label>
                                                                <div class="warehouse-selector-list" style="display:flex;flex-wrap:wrap;gap:6px;padding-left:25px;padding-top:6px;min-height:30px;overflow:visible;">
                                                                    @forelse ($warehouseObjects as $warehouse)
                                                                        <label>
                                                                            <input name="warehouses[{{ $company->id }}][]" type="checkbox" value="{{ $warehouse['name'] }}" @checked(empty($selectedWarehouses) ? $companySelected : in_array($warehouse['name'], $selectedWarehouses, true))>
                                                                            {{ $warehouse['name'] }}@if($warehouse['short_name']) <span class="fine-print">({{ $warehouse['short_name'] }})</span>@endif
                                                                        </label>
                                                                    @empty
                                                                        <span class="fine-print">Sin almacenes registrados.</span>
                                                                    @endforelse
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <span class="fine-print">Deja la contrasena vacia para conservar la actual.</span>
                                            <button class="button primary small" type="submit">Guardar cambios</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">No hay usuarios autorizados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <script>
            document.querySelectorAll('.role-select').forEach((select) => {
                const scope = select.closest('form') || document;
                const box = scope.querySelector('.companies-box');
                const subroleBox = scope.querySelector('.buyer-subrole-box');
                const sync = () => {
                    if (box) box.style.display = ['buyer', 'inventory'].includes(select.value) ? 'block' : 'none';
                    if (subroleBox) subroleBox.style.display = select.value === 'buyer' ? 'grid' : 'none';
                };
                select.addEventListener('change', sync);
                sync();
            });

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
