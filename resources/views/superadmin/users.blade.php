@extends('layouts.app')

@php
    $roleLabels = [
        'superadmin' => 'Super Administrador',
        'finance' => 'Finanzas',
        'buyer' => 'Compras y Suministros',
        'inventory' => 'Control de inventarios',
        'services' => 'Servicios',
        'administrative_assistant' => 'Asistente Administrativo',
    ];
    $roleDescriptions = [
        'superadmin' => 'Acceso total al sistema: usuarios, roles, empresas, catalogos, finanzas, compras, inventarios, servicios y auditoria.',
        'finance' => 'Gestiona autorizaciones, empresas, proveedores, pagos, ordenes de compra, ordenes de suministro y ordenes de reembolso.',
        'buyer' => 'Crea y da seguimiento a solicitudes segun sus subcategorias asignadas: compras, suministros y/o reembolsos.',
        'inventory' => 'Administra almacenes e inventarios, registra recepciones, controla existencias y procesa entregas de suministros.',
        'services' => 'Registra servicios, consulta catalogos de servicios y da seguimiento a soportes operativos relacionados.',
        'administrative_assistant' => 'Apoya en actividades administrativas como alta y seguimiento de servicios, sin permisos financieros completos.',
    ];
    $buyerSubroleLabels = [
        'purchases' => 'Compras',
        'supplies' => 'Suministros',
        'reimbursements' => 'Reembolsos',
    ];

    $selectedRole = $filters['role'] ?? '';
    $selectedStatus = $filters['status'] ?? '';
    $query = $filters['q'] ?? '';
    $createBuyerSubroles = old('buyer_subroles', ['purchases']);
    $createBuyerSubroles = is_array($createBuyerSubroles) ? $createBuyerSubroles : [$createBuyerSubroles];
@endphp

@section('body')
    <x-app-shell title="Usuarios y roles">
        <div class="metrics-grid compact-metrics">
            <article class="metric-card">
                <span>Total usuarios</span>
                <strong>{{ $totalUsers }}</strong>
                <small>Cuentas registradas</small>
            </article>
            <article class="metric-card">
                <span>Activos</span>
                <strong>{{ $activeUsers }}</strong>
                <small>Con acceso permitido</small>
            </article>
            <article class="metric-card">
                <span>Inactivos</span>
                <strong>{{ $inactiveUsers }}</strong>
                <small>Acceso bloqueado</small>
            </article>
            <article class="metric-card">
                <span>Resultado actual</span>
                <strong>{{ $users->total() }}</strong>
                <small>Usuarios con los filtros</small>
            </article>
        </div>

        <details class="panel compact-create" @if ($errors->any()) open @endif>
            <summary>
                <span>
                    <strong>Crear usuario</strong>
                    <small>Crea la cuenta y asigna su rol inicial.</small>
                </span>
                <span class="button primary small">Nuevo usuario</span>
            </summary>

            <form class="stack" method="POST" action="{{ route('superadmin.users.store') }}">
                @csrf
                <div class="grid-3">
                    <label>Nombre<input name="name" value="{{ old('name') }}" required></label>
                    <label>Correo<input name="email" type="email" value="{{ old('email') }}" required></label>
                    <label>Contrasena inicial<input name="password" value="{{ old('password') }}" required></label>
                </div>
                <div class="role-subcategory-stack">
                    <label>Rol
                        <select name="role" class="role-select" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" @selected(old('role', 'buyer') === $role)>{{ $roleLabels[$role] }}</option>
                            @endforeach
                        </select>
                    </label>
                    <div class="role-capabilities" data-role-capabilities>
                        <span class="form-label">Guia rapida de capacidades por rol</span>
                        <div class="role-capability-list">
                            @foreach ($roleDescriptions as $role => $description)
                                <article class="role-capability-card" data-role-card="{{ $role }}">
                                    <strong>{{ $roleLabels[$role] }}</strong>
                                    <span>{{ $description }}</span>
                                </article>
                            @endforeach
                        </div>
                    </div>
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
                                            <small class="fine-print">Sin almacenes registrados</small>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <span class="fine-print">Compras y Suministros e inventarios pueden limitarse por empresa y almacen.</span>
                    <button class="button primary" type="submit">Crear usuario</button>
                </div>
            </form>
        </details>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Usuarios del sistema</h2>
                    <p class="fine-print">Busca, filtra y edita solo cuando lo necesites.</p>
                </div>
            </div>

            <form class="user-filters" method="GET" action="{{ route('superadmin.users.index') }}">
                <label>
                    Buscar usuario
                    <input name="q" value="{{ $query }}" placeholder="Nombre o correo">
                </label>
                <label>
                    Rol
                    <select name="role">
                        <option value="">Todos los roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected($selectedRole === $role)>{{ $roleLabels[$role] }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Estado
                    <select name="status">
                        <option value="">Todos</option>
                        <option value="active" @selected($selectedStatus === 'active')>Activos</option>
                        <option value="inactive" @selected($selectedStatus === 'inactive')>Inactivos</option>
                    </select>
                </label>
                <div class="filter-actions">
                    <button class="button primary" type="submit">Filtrar</button>
                    <a class="button ghost" href="{{ route('superadmin.users.index') }}">Limpiar</a>
                </div>
            </form>

            <div class="table-scroll">
                <table class="compact-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Contrasena</th>
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
                            @php
                                $passwordEditorOpen = $errors->has('password')
                                    && (int) old('password_user_id') === $managedUser->id;
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $managedUser->name }}</strong>
                                    <small>{{ $managedUser->email }}</small>
                                </td>
                                <td class="password-cell">
                                    <div
                                        class="password-inline-editor"
                                        data-password-editor
                                        data-original-password="{{ $managedUser->plain_password }}"
                                    >
                                        <div class="password-editor-display" data-password-display @if ($passwordEditorOpen) hidden @endif>
                                            <strong>{{ $managedUser->plain_password ?: 'No disponible' }}</strong>
                                            <button class="button ghost small" type="button" data-password-edit>Editar</button>
                                        </div>
                                        <form
                                            class="password-editor-form"
                                            method="POST"
                                            action="{{ route('superadmin.users.update', $managedUser) }}"
                                            data-password-form
                                            @if (! $passwordEditorOpen) hidden @endif
                                        >
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="password_only" value="1">
                                            <input type="hidden" name="password_user_id" value="{{ $managedUser->id }}">
                                            <input
                                                name="password"
                                                type="text"
                                                value="{{ $passwordEditorOpen ? old('password') : $managedUser->plain_password }}"
                                                minlength="6"
                                                maxlength="255"
                                                required
                                                aria-label="Contrasena de {{ $managedUser->name }}"
                                            >
                                            @if ($passwordEditorOpen)
                                                @error('password')
                                                    <small class="form-error">{{ $message }}</small>
                                                @enderror
                                            @endif
                                            <div class="password-editor-actions">
                                                <button class="button primary small" type="submit">Guardar</button>
                                                <button class="button ghost small" type="button" data-password-cancel>Cancelar</button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <span class="role-chip">{{ $roleLabels[$managedUser->role] ?? $managedUser->role }}</span>
                                </td>
                                <td>
                                    @if ($managedUser->role === 'buyer')
                                        {{ $managedUser->buyerSubroleLabel() }}
                                    @else
                                        <span class="fine-print">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if (in_array($managedUser->role, ['buyer', 'inventory'], true))
                                        @php $assignments = $managedUser->normalizedCompanyAssignments(); @endphp
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
                                        @php $assignments = $managedUser->normalizedCompanyAssignments(); @endphp
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
                                    <button class="button ghost small editor-toggle" type="button" data-target="editor-{{ $managedUser->id }}">Editar</button>

                                    @if ($managedUser->id !== auth()->id())
                                        <form class="inline-form" method="POST" action="{{ route('superadmin.users.toggle', $managedUser) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="button {{ $managedUser->active ? 'danger' : 'primary' }} small" type="submit">
                                                {{ $managedUser->active ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                    @else
                                        <small class="fine-print">Cuenta actual</small>
                                    @endif
                                </td>
                            </tr>
                            <tr class="editor-row" id="editor-{{ $managedUser->id }}" hidden>
                                <td colspan="8">
                                    <form class="stack" method="POST" action="{{ route('superadmin.users.update', $managedUser) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid-2">
                                            <label>Nombre<input name="name" value="{{ $managedUser->name }}" required></label>
                                            <label>Correo<input name="email" type="email" value="{{ $managedUser->email }}" required></label>
                                        </div>
                                        <div class="grid-2">
                                            <div class="role-subcategory-stack">
                                                <label>Rol
                                                    <select name="role" class="role-select" required>
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role }}" @selected($managedUser->role === $role)>{{ $roleLabels[$role] }}</option>
                                                        @endforeach
                                                    </select>
                                                </label>
                                                <div class="role-capabilities" data-role-capabilities>
                                                    <span class="form-label">Guia rapida de capacidades por rol</span>
                                                    <div class="role-capability-list">
                                                        @foreach ($roleDescriptions as $role => $description)
                                                            <article class="role-capability-card" data-role-card="{{ $role }}">
                                                                <strong>{{ $roleLabels[$role] }}</strong>
                                                                <span>{{ $description }}</span>
                                                            </article>
                                                        @endforeach
                                                    </div>
                                                </div>
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
                                                                        <small class="fine-print">Sin almacenes registrados</small>
                                                                    @endforelse
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <button class="button primary small" type="submit">Guardar cambios</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="empty-state">No hay usuarios con esos filtros.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="pagination-bar">
                    <span>Pagina {{ $users->currentPage() }} de {{ $users->lastPage() }}</span>
                    <div class="item-actions">
                        @if ($users->onFirstPage())
                            <span class="button ghost small disabled">Anterior</span>
                        @else
                            <a class="button ghost small" href="{{ $users->previousPageUrl() }}">Anterior</a>
                        @endif

                        @if ($users->hasMorePages())
                            <a class="button ghost small" href="{{ $users->nextPageUrl() }}">Siguiente</a>
                        @else
                            <span class="button ghost small disabled">Siguiente</span>
                        @endif
                    </div>
                </div>
            @endif
        </section>

        <style>
            .password-cell {
                min-width: 250px;
            }

            .password-inline-editor [hidden] {
                display: none !important;
            }

            .password-editor-display,
            .password-editor-actions {
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .password-editor-display {
                justify-content: space-between;
            }

            .password-editor-display strong {
                overflow-wrap: anywhere;
            }

            .password-editor-form {
                display: grid;
                gap: 6px;
            }

            .password-editor-form input[name="password"] {
                min-height: 34px;
                width: 100%;
                padding: 6px 8px;
            }

            .password-editor-actions {
                flex-wrap: wrap;
            }

            .form-error {
                color: #b42318;
            }
        </style>

        <script>
            document.querySelectorAll('.role-select').forEach((select) => {
                const scope = select.closest('form') || document;
                const box = scope.querySelector('.companies-box');
                const subroleBox = scope.querySelector('.buyer-subrole-box');
                const roleCards = scope.querySelectorAll('[data-role-card]');
                const sync = () => {
                    if (box) box.style.display = ['buyer', 'inventory'].includes(select.value) ? 'block' : 'none';
                    if (subroleBox) subroleBox.style.display = select.value === 'buyer' ? 'grid' : 'none';
                    roleCards.forEach((card) => card.classList.toggle('is-active', card.dataset.roleCard === select.value));
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

            document.querySelectorAll('[data-password-editor]').forEach((editor) => {
                const display = editor.querySelector('[data-password-display]');
                const form = editor.querySelector('[data-password-form]');
                const input = form?.querySelector('input[name="password"]');
                const editButton = editor.querySelector('[data-password-edit]');
                const cancelButton = editor.querySelector('[data-password-cancel]');

                if (!display || !form || !input) return;

                const originalPassword = editor.dataset.originalPassword || '';

                editButton?.addEventListener('click', () => {
                    display.hidden = true;
                    form.hidden = false;
                    input.focus();
                    input.select();
                });

                cancelButton?.addEventListener('click', () => {
                    input.value = originalPassword;
                    form.hidden = true;
                    display.hidden = false;
                });
            });
        </script>
    </x-app-shell>
@endsection
