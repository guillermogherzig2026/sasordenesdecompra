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
    $createNavigationPermissions = old(
        'menu_permissions',
        \App\Support\NavigationPermissionCatalog::defaultsForRole(old('role', 'buyer'), $createBuyerSubroles)
    );
    $openCreateDialog = request()->query('view') === 'create' || ($errors->any() && ! old('_authorization_edit_id'));
@endphp

@section('body')
    <x-app-shell title="Autorizaciones">
        <header class="authorization-directory-heading">
            <div>
                <h2>Autorizaciones</h2>
                <p>Administra usuarios y sus accesos al sistema.</p>
            </div>
            <button class="button primary" type="button" data-authorization-create-open aria-haspopup="dialog" aria-controls="authorization-create-dialog"><span aria-hidden="true">+</span> Nuevo usuario</button>
        </header>

        <dialog id="authorization-create-dialog" class="authorization-create-dialog" aria-labelledby="authorization-create-title" data-authorization-create-dialog @if ($openCreateDialog) data-auto-open @endif>
            <header class="authorization-dialog-header">
                <div>
                    <h2 id="authorization-create-title">Nuevo usuario</h2>
                    <p>Configura sus datos, accesos, empresas y almacenes.</p>
                </div>
                <button class="authorization-dialog-close" type="button" aria-label="Cerrar" title="Cerrar" data-authorization-create-close>×</button>
            </header>
            <form id="user-authorization-create-content" class="stack authorization-create-form" method="POST" action="{{ route('finance.admin.users.store') }}" data-credential-generator>
                @csrf
                <input type="hidden" name="_authorization_form" value="create">
                @if ($errors->any() && ! old('_authorization_edit_id'))
                    <div class="authorization-modal-errors" role="alert">
                        <strong>Revisa los datos capturados.</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <section class="authorization-step authorization-user-step">
                    <div class="authorization-step-header">
                        <div class="authorization-step-title">
                            <span class="authorization-step-number">0</span>
                            <div>
                                <h3>Datos del usuario</h3>
                                <p>Captura la información de acceso del nuevo usuario.</p>
                            </div>
                        </div>
                    </div>
                    <div class="authorization-user-fields">
                        <label>Nombre<input name="first_name" value="{{ old('first_name', old('name')) }}" autocomplete="given-name" required data-credential-first-name></label>
                        <label>Apellido paterno<input name="paternal_last_name" value="{{ old('paternal_last_name') }}" autocomplete="family-name" data-credential-paternal-name></label>
                        <label>Apellido materno<input name="maternal_last_name" value="{{ old('maternal_last_name') }}" data-credential-maternal-name></label>
                        <label>Nombre de usuario<input name="username" value="{{ old('username') }}" autocomplete="username" autocapitalize="none" spellcheck="false" required data-credential-username></label>
                        <label>Correo<input name="email" type="email" value="{{ old('email') }}" required></label>
                        <label>Contrasena inicial<input name="password" value="{{ old('password') }}" autocomplete="new-password" required data-credential-password></label>
                    </div>
                </section>
                <div class="authorization-configuration-stack">
                    <x-navigation-permission-selector
                        :catalog="$navigationCatalog"
                        :selected="$createNavigationPermissions"
                        :role-labels="$roleLabels"
                        :selected-role="old('role', 'buyer')"
                        :buyer-subrole-labels="$buyerSubroleLabels"
                        :selected-buyer-subroles="$createBuyerSubroles"
                        id-prefix="create-navigation"
                    />
                    <x-company-warehouse-selector :companies="$companies" :supply-warehouses="$supplyWarehouses" />
                    @if(false)
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
                    @endif
                </div>
                <div class="form-actions">
                    <span class="fine-print">Los usuarios nuevos podran iniciar sesion con el correo y contrasena indicados.</span>
                    <button class="button primary" type="submit">Crear usuario</button>
                </div>
            </form>
        </dialog>

        <section
            id="authorization-users-view"
            class="panel authorization-catalog"
            aria-label="Catálogo de usuarios"
        >
            <h2>Catálogo de usuarios</h2>
            <div class="authorization-filters" aria-label="Filtros de usuarios">
                <label class="authorization-filter-search">Buscar usuario
                    <input type="search" placeholder="Buscar por nombre, usuario o correo..." autocomplete="off" data-authorization-search>
                </label>
                <label>Empresa
                    <select data-authorization-company>
                        <option value="">Todas</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->name }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Módulo
                    <select data-authorization-module>
                        <option value="">Todos</option>
                        @foreach ($navigationCatalog as $categoryKey => $category)
                            <option value="{{ $categoryKey }}">{{ $category['label'] }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Estado
                    <select data-authorization-status>
                        <option value="">Todos</option>
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                    </select>
                </label>
            </div>
            <div id="authorized-users-content" class="table-scroll">
                <table class="authorization-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Módulos autorizados</th>
                            <th>Empresas</th>
                            <th>Almacenes</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $managedUser)
                            @php
                                $assignments = $managedUser->normalizedCompanyAssignments();
                                $authorizedModules = collect($navigationCatalog)
                                    ->filter(fn (array $category) => count(array_intersect(array_keys($category['items']), $managedUser->navigationPermissions())) > 0)
                                    ->keys()->all();
                                $warehouseCount = collect($assignments)->sum(fn (array $assignment) => count($assignment['warehouses']));
                                $allCompanies = ! in_array($managedUser->role, ['buyer', 'inventory'], true);
                                $initials = collect(preg_split('/\s+/', trim($managedUser->name)))
                                    ->filter()->take(2)->map(fn (string $part) => mb_substr($part, 0, 1))->implode('');
                            @endphp
                            <tr data-authorization-row data-editor-id="finance-editor-{{ $managedUser->id }}"
                                data-search="{{ $managedUser->name.' '.$managedUser->username.' '.$managedUser->email }}"
                                data-companies="{{ json_encode($managedUser->authorizedCompanyNames()) }}"
                                data-all-companies="{{ $allCompanies ? 'true' : 'false' }}"
                                data-modules="{{ json_encode($authorizedModules) }}"
                                data-status="{{ $managedUser->active ? 'active' : 'inactive' }}">
                                <td data-filter-value="{{ $managedUser->name }}">
                                    <div class="authorization-user-identity">
                                        <span class="authorization-user-avatar" aria-hidden="true">{{ $initials ?: '?' }}</span>
                                        <span><strong>{{ $managedUser->name }}</strong><small>{{ $managedUser->username ?: 'Sin usuario' }}</small></span>
                                    </div>
                                </td>
                                <td>{{ $managedUser->email }}</td>
                                <td><span class="authorization-count authorization-count-modules">{{ count($authorizedModules) }}</span> <span class="authorization-count-label">módulos</span></td>
                                <td><span class="authorization-count authorization-count-companies">{{ $allCompanies ? 'Todas' : count($assignments) }}</span></td>
                                <td><span class="authorization-count authorization-count-warehouses">{{ $allCompanies ? '—' : $warehouseCount }}</span></td>
                                <td>
                                    <span class="authorization-state {{ $managedUser->active ? 'is-active' : 'is-inactive' }}">{{ $managedUser->active ? 'Activo' : 'Inactivo' }}</span>
                                </td>
                                <td>
                                    <div class="authorization-row-actions">
                                        <button class="button ghost small editor-toggle" type="button" aria-controls="finance-editor-{{ $managedUser->id }}" aria-expanded="false" data-target="finance-editor-{{ $managedUser->id }}">Editar</button>
                                        <button class="button ghost small editor-toggle" type="button" aria-controls="finance-editor-{{ $managedUser->id }}" aria-expanded="false" data-target="finance-editor-{{ $managedUser->id }}" data-permission-editor>Permisos</button>
                                        <details class="authorization-more">
                                            <summary aria-label="Más acciones e información de {{ $managedUser->name }}" title="Más acciones e información">⋯</summary>
                                            <div class="authorization-more-content">
                                                <p><strong>Rol:</strong> {{ $roleLabels[$managedUser->role] ?? $managedUser->role }}@if ($managedUser->role === 'buyer') · {{ $managedUser->buyerSubroleLabel() }} @endif</p>
                                                <p><strong>Módulos:</strong> {{ implode(', ', $managedUser->authorizedNavigationCategoryLabels()) ?: 'Sin acceso' }}</p>
                                                <p><strong>Empresas:</strong> {{ $allCompanies ? 'Todas' : (collect($assignments)->pluck('name')->implode(', ') ?: 'Sin empresas asignadas') }}</p>
                                                <p><strong>Almacenes:</strong> {{ $allCompanies ? 'No aplica' : (collect($assignments)->flatMap(fn (array $assignment) => $assignment['warehouses'])->unique()->implode(', ') ?: 'Sin almacenes') }}</p>
                                                <form method="POST" action="{{ route('finance.admin.users.toggle', $managedUser) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="button {{ $managedUser->active ? 'danger' : 'primary' }} small" type="submit">{{ $managedUser->active ? 'Desactivar' : 'Activar' }}</button>
                                                </form>
                                            </div>
                                        </details>
                                    </div>
                                </td>
                            </tr>
                            <tr class="editor-row" id="finance-editor-{{ $managedUser->id }}" data-authorization-editor hidden>
                                <td colspan="7">
                                    <form class="stack" method="POST" action="{{ route('finance.admin.users.update', $managedUser) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="_authorization_edit_id" value="{{ $managedUser->id }}">
                                        <section class="authorization-step authorization-user-step">
                                            <div class="authorization-step-header">
                                                <div class="authorization-step-title">
                                                    <span class="authorization-step-number">0</span>
                                                    <div>
                                                        <h3>Datos del usuario</h3>
                                                        <p>Actualiza la información de acceso del usuario.</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="authorization-user-fields">
                                                <label>Nombre<input name="first_name" value="{{ $managedUser->first_name ?: $managedUser->name }}" autocomplete="given-name" required></label>
                                                <label>Apellido paterno<input name="paternal_last_name" value="{{ $managedUser->paternal_last_name }}" autocomplete="family-name"></label>
                                                <label>Apellido materno<input name="maternal_last_name" value="{{ $managedUser->maternal_last_name }}"></label>
                                                <label>Nombre de usuario<input name="username" value="{{ $managedUser->username }}" autocomplete="username" autocapitalize="none" spellcheck="false"></label>
                                                <label>Correo<input name="email" type="email" value="{{ $managedUser->email }}" required></label>
                                                <label>Nueva contrasena<input name="password" type="text" placeholder="Sin cambio"></label>
                                            </div>
                                        </section>
                                        <div class="authorization-configuration-stack">
                                            <x-navigation-permission-selector
                                                :catalog="$navigationCatalog"
                                                :selected="$managedUser->navigationPermissions()"
                                                :role-labels="$roleLabels"
                                                :selected-role="$managedUser->role"
                                                :buyer-subrole-labels="$buyerSubroleLabels"
                                                :selected-buyer-subroles="$managedUser->buyerSubroles()"
                                                id-prefix="edit-navigation-{{ $managedUser->id }}"
                                            />
                                            <x-company-warehouse-selector :companies="$companies" :supply-warehouses="$supplyWarehouses" :managed-user="$managedUser" />
                                            @if(false)
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
                                            @endif
                                        </div>
                                        <div class="form-actions">
                                            <span class="fine-print">Deja la contrasena vacia para conservar la actual.</span>
                                            <button class="button primary small" type="submit">Guardar cambios</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        <tr data-authorization-empty @if ($users->isNotEmpty()) hidden @endif>
                            <td colspan="7">No hay usuarios que coincidan con los filtros.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <footer class="authorization-catalog-footer">
                <span data-authorization-result-count>Mostrando {{ min($users->count(), 12) }} de {{ $users->count() }} usuarios</span>
                <nav class="authorization-pagination" aria-label="Páginas de usuarios">
                    <button class="button ghost small" type="button" data-authorization-previous aria-label="Página anterior" disabled>‹</button>
                    <span data-authorization-page>1 de {{ max(1, (int) ceil($users->count() / 12)) }}</span>
                    <button class="button ghost small" type="button" data-authorization-next aria-label="Página siguiente" @if ($users->count() <= 12) disabled @endif>›</button>
                </nav>
            </footer>
        </section>

        <style>
            .authorization-directory-heading { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
            .authorization-directory-heading h2 { margin: 0 0 4px; font-size: 1.5rem; }
            .authorization-directory-heading p { margin: 0; color: var(--muted); }
            .authorization-directory-heading .button { flex: 0 0 auto; }
            .authorization-directory-heading .button span { font-size: 1.25rem; line-height: 1; }
            .authorization-catalog { min-width: 0; max-height: none !important; overflow: visible !important; gap: 20px; padding: 22px; }
            .authorization-catalog h2 { margin: 0; font-size: 1.25rem; }
            .authorization-filters { display: grid; grid-template-columns: minmax(230px, 2fr) repeat(3, minmax(135px, 1fr)); align-items: end; gap: 12px; }
            .authorization-filters label { display: grid; gap: 5px; min-width: 0; color: #344054; font-size: .8rem; font-weight: 750; }
            .authorization-filters input, .authorization-filters select { min-width: 0; width: 100%; min-height: 40px; border: 1px solid var(--line); border-radius: 6px; background: #fff; padding: 8px 10px; color: var(--text); font: inherit; font-size: .88rem; }
            .authorization-filters input:focus-visible, .authorization-filters select:focus-visible { outline: 2px solid #6da8c0; outline-offset: 1px; }
            .authorization-catalog .table-scroll { min-width: 0; overflow-x: auto; border: 1px solid var(--line); border-radius: 6px; }
            .authorization-table { width: 100%; min-width: 1120px; table-layout: fixed; border-collapse: collapse; }
            .authorization-table th { background: #f5f9fd; color: #526077; font-size: .74rem; text-transform: uppercase; white-space: nowrap; }
            .authorization-table th, .authorization-table td { padding: 12px 14px; border-bottom: 1px solid var(--line); text-align: left; vertical-align: middle; }
            .authorization-table th:nth-child(1) { width: 19%; }
            .authorization-table th:nth-child(2) { width: 21%; }
            .authorization-table th:nth-child(3) { width: 15%; }
            .authorization-table th:nth-child(4), .authorization-table th:nth-child(5) { width: 8%; }
            .authorization-table th:nth-child(6) { width: 10%; }
            .authorization-table th:nth-child(7) { width: 19%; }
            .authorization-table td:nth-child(2) { overflow-wrap: anywhere; }
            .authorization-table tbody tr:last-child td { border-bottom: 0; }
            .authorization-table tr[hidden] { display: none; }
            .authorization-user-identity { display: flex; align-items: center; gap: 10px; min-width: 155px; }
            .authorization-user-identity strong, .authorization-user-identity small { display: block; line-height: 1.25; overflow-wrap: anywhere; }
            .authorization-user-identity strong { font-size: .88rem; }
            .authorization-user-identity small { margin-top: 4px; color: var(--muted); font-size: .78rem; }
            .authorization-user-avatar { display: grid; flex: 0 0 36px; width: 36px; height: 36px; place-items: center; border-radius: 50%; background: #e5effc; color: #1554a1; font-size: .76rem; font-weight: 850; }
            .authorization-table tbody tr:nth-child(4n+2) .authorization-user-avatar { background: #ddf6ea; color: #126d4b; }
            .authorization-table tbody tr:nth-child(4n+3) .authorization-user-avatar { background: #fae6ee; color: #a32957; }
            .authorization-table tbody tr:nth-child(4n+4) .authorization-user-avatar { background: #fff1d6; color: #8c5e13; }
            .authorization-count { display: inline-flex; min-width: 30px; min-height: 27px; align-items: center; justify-content: center; padding: 3px 7px; border-radius: 6px; font-size: .86rem; font-weight: 850; }
            .authorization-count-modules { color: #19477f; background: #e8f2ff; }
            .authorization-count-companies { color: #6135a0; background: #f1eaff; }
            .authorization-count-warehouses { color: #80571a; background: #fff3d1; }
            .authorization-count-label { color: var(--muted); font-size: .82rem; }
            .authorization-state { display: inline-flex; align-items: center; gap: 6px; border-radius: 6px; padding: 6px 9px; font-size: .82rem; font-weight: 800; white-space: nowrap; }
            .authorization-state::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
            .authorization-state.is-active { color: #08704b; background: #e6f8ee; }
            .authorization-state.is-inactive { color: #a52831; background: #fde8ea; }
            .authorization-row-actions { display: flex; align-items: center; flex-wrap: nowrap; gap: 6px; }
            .authorization-row-actions:has(.authorization-more[open]) { flex-wrap: wrap; }
            .authorization-row-actions .button { white-space: nowrap; }
            .authorization-more summary { width: 34px; height: 34px; display: grid; place-items: center; border: 1px solid var(--line); border-radius: 6px; background: #fff; font-size: 1.2rem; line-height: 1; cursor: pointer; list-style: none; }
            .authorization-more summary::-webkit-details-marker { display: none; }
            .authorization-more-content { min-width: min(280px, 75vw); padding: 10px 0; color: #344054; font-size: .8rem; }
            .authorization-more-content p { margin: 0 0 8px; line-height: 1.35; overflow-wrap: anywhere; }
            .authorization-more-content form { margin-top: 10px; }
            .authorization-more[open] { flex-basis: 100%; }
            .authorization-catalog-footer { display: flex; align-items: center; justify-content: space-between; gap: 12px; color: var(--muted); font-size: .84rem; }
            .authorization-pagination { display: flex; align-items: center; gap: 8px; color: var(--text); }
            .authorization-pagination button { width: 34px; padding: 0; font-size: 1.15rem; }
            .authorization-pagination button:disabled { opacity: .45; cursor: default; }
            .authorization-create-dialog { width: min(1050px, calc(100vw - 24px)); max-width: none; max-height: calc(100dvh - 24px); padding: 0; border: 1px solid var(--line); border-radius: 8px; background: #fff; color: var(--text); box-shadow: 0 24px 60px rgba(10, 31, 44, .3); overflow: hidden; }
            .authorization-create-dialog::backdrop { background: rgba(10, 31, 44, .62); }
            .authorization-dialog-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 18px 22px; border-bottom: 1px solid var(--line); }
            .authorization-dialog-header h2 { margin: 0 0 3px; font-size: 1.25rem; }
            .authorization-dialog-header p { margin: 0; color: var(--muted); font-size: .85rem; }
            .authorization-dialog-close { flex: 0 0 34px; width: 34px; height: 34px; border: 1px solid var(--line); border-radius: 6px; background: #fff; color: var(--text); font-size: 1.4rem; cursor: pointer; }
            .authorization-create-form { max-height: calc(100dvh - 100px); overflow-y: auto; padding: 18px 22px 22px; }
            .authorization-create-form .form-actions { position: sticky; bottom: -22px; z-index: 1; margin: 0 -22px -22px; padding: 12px 22px; border-top: 1px solid var(--line); background: #fff; }
            .authorization-modal-errors { padding: 10px 14px; border: 1px solid #e7b0ae; border-radius: 6px; background: #fff3f2; color: #8d241e; font-size: .85rem; }
            .authorization-modal-errors ul { margin: 6px 0 0; padding-left: 18px; }

            .authorization-configuration-stack {
                display: grid;
                gap: 16px;
            }

            .authorization-user-fields {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 10px;
                margin-top: 12px;
            }

            .authorization-user-fields label {
                gap: 3px;
                font-size: .84rem;
            }

            .authorization-user-fields input {
                min-height: 32px;
                height: 32px;
                border-radius: 6px;
                padding: 5px 9px;
                font-size: .9rem;
            }

            .navigation-permission-manager {
                display: grid;
                gap: 16px;
                min-width: 0;
            }

            .authorization-step {
                min-width: 0;
                border: 1px solid #cbd9e7;
                border-radius: 8px;
                background: #fff;
                padding: 16px;
            }

            .authorization-step-header,
            .navigation-permission-panel-header,
            .navigation-permission-actions {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }

            .authorization-step-title {
                display: flex;
                min-width: 0;
                align-items: flex-start;
                gap: 10px;
            }

            .authorization-step-number {
                display: inline-grid;
                flex: 0 0 30px;
                width: 30px;
                height: 30px;
                place-items: center;
                border-radius: 6px;
                background: #123247;
                color: #fff;
                font-size: .84rem;
                font-weight: 900;
            }

            .authorization-step-title h3,
            .authorization-step-title p {
                margin: 0;
            }

            .authorization-step-title h3 {
                font-size: 1rem;
            }

            .authorization-step-title p {
                margin-top: 3px;
                color: #617083;
                font-size: .83rem;
            }

            .authorization-total {
                flex: 0 0 auto;
                border: 1px solid #b9d9cf;
                border-radius: 6px;
                background: #edf8f4;
                padding: 6px 9px;
                color: #075d49;
                font-size: .78rem;
            }

            .navigation-permission-actions {
                flex-wrap: wrap;
                justify-content: flex-end;
            }

            .navigation-permission-panel-header p {
                margin: 3px 0 0;
            }

            .navigation-module-carousel {
                display: grid;
                grid-template-columns: 34px minmax(0, 1fr) 34px;
                min-width: 0;
                align-items: center;
                gap: 8px;
                margin-top: 15px;
            }

            .navigation-carousel-button {
                display: inline-grid;
                width: 34px;
                height: 34px;
                min-height: 34px;
                place-items: center;
                border: 1px solid #cbd9e7;
                border-radius: 50%;
                background: #fff;
                color: #123247;
                padding: 0;
                font: 800 1.25rem/1 sans-serif;
                cursor: pointer;
            }

            .navigation-carousel-button:disabled {
                opacity: .35;
                cursor: default;
            }

            .navigation-carousel-button:not(:disabled):hover,
            .navigation-carousel-button:not(:disabled):focus-visible {
                border-color: #14718b;
                color: #0d637a;
                outline: 0;
                box-shadow: 0 0 0 3px rgba(20, 113, 139, .12);
            }

            .navigation-module-track {
                display: flex;
                min-width: 0;
                gap: 10px;
                overflow-x: auto;
                overscroll-behavior-inline: contain;
                padding: 2px;
                scroll-behavior: smooth;
                scroll-snap-type: x proximity;
                scrollbar-width: none;
            }

            .navigation-module-track::-webkit-scrollbar {
                display: none;
            }

            .navigation-module-card {
                display: grid;
                flex: 0 0 166px;
                min-height: 126px;
                grid-template-rows: auto auto auto 1fr;
                align-content: start;
                gap: 5px;
                border: 1px solid #d5e0ea;
                border-radius: 8px;
                background: #fff;
                padding: 12px;
                color: #1f2d40;
                text-align: left;
                scroll-snap-align: start;
                cursor: pointer;
                font: inherit;
            }

            .navigation-module-card:hover,
            .navigation-module-card:focus-visible {
                border-color: var(--module-accent);
                outline: 0;
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--module-accent) 14%, transparent);
            }

            .navigation-module-card.is-active {
                border-color: var(--module-accent);
                background: color-mix(in srgb, var(--module-tint) 72%, #fff);
                box-shadow: inset 0 3px 0 var(--module-accent);
            }

            .navigation-module-icon {
                display: inline-grid;
                width: 34px;
                height: 34px;
                place-items: center;
                border-radius: 7px;
                background: var(--module-tint);
                color: var(--module-accent);
                font-size: .75rem;
                font-weight: 900;
            }

            .navigation-module-name {
                min-width: 0;
                overflow-wrap: anywhere;
                font-size: .84rem;
                font-weight: 850;
                line-height: 1.2;
            }

            .navigation-module-meta,
            .navigation-module-selected {
                color: #68778a;
                font-size: .72rem;
                line-height: 1.25;
            }

            .navigation-module-selected {
                align-self: end;
                color: var(--module-accent);
            }

            .navigation-permission-window {
                min-height: 190px;
                margin-top: 14px;
                border-top: 1px solid #e0e8ef;
                padding-top: 14px;
            }

            .navigation-permission-panel h4 {
                margin: 0;
                font-size: 1rem;
            }

            .navigation-permission-grid {
                display: flex;
                flex-wrap: wrap;
                align-items: stretch;
                gap: 6px;
            }

            .navigation-permission-subcategories {
                display: grid;
                gap: 18px;
                margin-top: 14px;
            }

            .navigation-permission-subcategory {
                min-width: 0;
                margin: 0;
                border: 0;
                padding: 0;
            }

            .navigation-permission-subcategory legend {
                margin-bottom: 8px;
                padding: 0;
                color: #0d637a;
                font-size: .76rem;
                font-weight: 800;
                text-transform: uppercase;
            }

            .navigation-permission-option {
                display: flex;
                align-items: center;
                flex: 0 1 170px;
                width: 170px;
                max-width: 100%;
                min-height: 28px;
                gap: 5px;
                border: 1px solid #d6e0e9;
                border-radius: 6px;
                background: #fff;
                padding: 3px 6px;
                font-size: .8rem;
                cursor: pointer;
            }

            .navigation-permission-option:has(input:checked) {
                border-color: #67b89d;
                background: #ecf9f4;
                color: #075d49;
            }

            .navigation-permission-option input[type="checkbox"] {
                flex: 0 0 auto;
                width: 12px;
                height: 12px;
                min-height: 12px;
                margin: 0;
                padding: 0;
                accent-color: #087f6b;
            }

            .navigation-permission-option span {
                min-width: 0;
                overflow-wrap: anywhere;
            }

            .authorization-scope-step .auth-split-selector {
                margin-top: 14px;
            }

            .navigation-category-chip {
                display: block;
                width: max-content;
                max-width: 220px;
                margin: 3px 0;
                border-radius: 5px;
                background: #edf4f8;
                padding: 4px 7px;
                font-size: .78rem;
            }

            @media (max-width: 980px) {
                .authorization-filters { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                .authorization-user-fields { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                .navigation-permission-option {
                    flex-basis: 160px;
                    width: 160px;
                }

                .authorization-step-header,
                .navigation-permission-panel-header {
                    align-items: flex-start;
                    flex-direction: column;
                }

            }

            @media (max-width: 640px) {
                .authorization-directory-heading { align-items: flex-start; flex-direction: column; }
                .authorization-directory-heading .button { width: 100%; }
                .authorization-catalog { padding: 14px; }
                .authorization-filters { grid-template-columns: minmax(0, 1fr); }
                .authorization-catalog-footer { align-items: flex-start; flex-direction: column; }
                .authorization-create-dialog { width: calc(100vw - 12px); max-height: calc(100dvh - 12px); }
                .authorization-dialog-header { padding: 12px 14px; }
                .authorization-create-form { padding: 12px 14px 18px; max-height: calc(100dvh - 82px); }
                .authorization-create-form .form-actions { margin: 0 -14px -18px; padding: 10px 14px; }
                .authorization-user-fields { grid-template-columns: minmax(0, 1fr); }

                .authorization-step {
                    padding: 13px;
                }

                .navigation-module-carousel {
                    grid-template-columns: 30px minmax(0, 1fr) 30px;
                    gap: 6px;
                }

                .navigation-carousel-button {
                    width: 30px;
                    height: 30px;
                    min-height: 30px;
                }

                .navigation-module-card {
                    flex-basis: 142px;
                    min-height: 120px;
                    padding: 10px;
                }

                .navigation-permission-option {
                    flex: 1 1 160px;
                    width: auto;
                }

                .navigation-permission-actions {
                    align-items: flex-start;
                    flex-wrap: wrap;
                }

                .auth-warehouse-scroll {
                    min-height: 320px;
                    max-height: 440px;
                    overflow-x: hidden;
                    overflow-y: auto;
                    border: 0;
                }

                .auth-warehouse-table {
                    display: block;
                    min-width: 0;
                }

                .auth-warehouse-table thead {
                    display: none;
                }

                .auth-warehouse-table tbody {
                    display: grid;
                    gap: 8px;
                }

                .auth-warehouse-table tr {
                    display: grid;
                    gap: 7px;
                    border: 1px solid #d6e0e9;
                    border-radius: 7px;
                    background: #fff;
                    padding: 10px;
                }

                .auth-warehouse-table tr.auth-supply-warehouse-row {
                    background: #f0f9ff;
                }

                .auth-warehouse-table th,
                .auth-warehouse-table td,
                .auth-warehouse-table .auth-supply-warehouse-row td {
                    display: grid;
                    grid-template-columns: minmax(86px, .75fr) minmax(0, 1.25fr);
                    gap: 8px;
                    border: 0;
                    background: transparent;
                    padding: 0;
                }

                .auth-warehouse-table td::before {
                    content: attr(data-label);
                    color: #617083;
                    font-size: .68rem;
                    font-weight: 800;
                    text-transform: uppercase;
                }

                .auth-warehouse-table tr[data-empty-warehouses] td {
                    grid-template-columns: 1fr;
                }
            }
        </style>

        <script>
            const existingUsernames = new Set(@json($existingUsernames ?? []));
            const credentialForm = document.querySelector('[data-credential-generator]');

            if (credentialForm) {
                const firstNameInput = credentialForm.querySelector('[data-credential-first-name]');
                const paternalNameInput = credentialForm.querySelector('[data-credential-paternal-name]');
                const maternalNameInput = credentialForm.querySelector('[data-credential-maternal-name]');
                const usernameInput = credentialForm.querySelector('[data-credential-username]');
                const passwordInput = credentialForm.querySelector('[data-credential-password]');
                const suggestedYear = 2024 + Math.floor(Math.random() * 3);
                let generatedUsername = '';
                let generatedPassword = '';

                const normalizePart = (value) => value
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase()
                    .replace(/[^a-z0-9]/g, '');

                const availableUsername = (base) => {
                    if (!base) return '';
                    let candidate = base.slice(0, 80);
                    let suffix = 2;

                    while (existingUsernames.has(candidate)) {
                        const suffixText = String(suffix++);
                        candidate = `${base.slice(0, 80 - suffixText.length)}${suffixText}`;
                    }

                    return candidate;
                };

                const proposeCredentials = () => {
                    const initials = firstNameInput.value
                        .trim()
                        .split(/\s+/)
                        .filter(Boolean)
                        .map((name) => normalizePart(name).charAt(0))
                        .join('');
                    const paternalName = normalizePart(paternalNameInput.value);
                    const maternalInitial = normalizePart(maternalNameInput.value).charAt(0);
                    const proposedUsername = availableUsername(`${initials}${paternalName}${maternalInitial}`);
                    const canReplaceUsername = !usernameInput.value || usernameInput.value === generatedUsername;
                    const canReplacePassword = !passwordInput.value || passwordInput.value === generatedPassword;

                    if (canReplaceUsername) usernameInput.value = proposedUsername;
                    generatedUsername = proposedUsername;

                    const proposedPassword = proposedUsername ? `${proposedUsername}${suggestedYear}` : '';
                    if (canReplacePassword) passwordInput.value = proposedPassword;
                    generatedPassword = proposedPassword;
                };

                [firstNameInput, paternalNameInput, maternalNameInput].forEach((input) => {
                    input.addEventListener('input', proposeCredentials);
                });
                usernameInput.addEventListener('input', () => {
                    if (!passwordInput.value || passwordInput.value === generatedPassword) {
                        generatedPassword = usernameInput.value ? `${normalizePart(usernameInput.value)}${suggestedYear}` : '';
                        passwordInput.value = generatedPassword;
                    }
                });

                proposeCredentials();
            }

            const createDialog = document.querySelector('[data-authorization-create-dialog]');
            const openCreateDialog = () => {
                if (!createDialog?.open) createDialog?.showModal();
                window.dispatchEvent(new Event('resize'));
            };
            document.querySelector('[data-authorization-create-open]')?.addEventListener('click', openCreateDialog);
            createDialog?.querySelector('[data-authorization-create-close]')?.addEventListener('click', () => createDialog.close());
            createDialog?.addEventListener('click', (event) => {
                if (event.target === createDialog) createDialog.close();
            });
            createDialog?.addEventListener('close', () => {
                const url = new URL(window.location.href);
                if (url.searchParams.get('view') === 'create') {
                    url.searchParams.delete('view');
                    window.history.replaceState({}, '', url);
                }
            });
            if (createDialog?.hasAttribute('data-auto-open')) openCreateDialog();

            const catalog = document.querySelector('.authorization-catalog');
            const userRows = Array.from(catalog?.querySelectorAll('[data-authorization-row]') || []);
            const emptyRow = catalog?.querySelector('[data-authorization-empty]');
            const searchInput = catalog?.querySelector('[data-authorization-search]');
            const companySelect = catalog?.querySelector('[data-authorization-company]');
            const moduleSelect = catalog?.querySelector('[data-authorization-module]');
            const statusSelect = catalog?.querySelector('[data-authorization-status]');
            const previousPage = catalog?.querySelector('[data-authorization-previous]');
            const nextPage = catalog?.querySelector('[data-authorization-next]');
            const pageLabel = catalog?.querySelector('[data-authorization-page]');
            const resultCount = catalog?.querySelector('[data-authorization-result-count]');
            const pageSize = 12;
            let currentPage = 1;

            const normalize = (value) => String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
            const closeEditor = (row) => {
                const editor = document.getElementById(row.dataset.editorId);
                if (editor) editor.hidden = true;
                row.querySelectorAll('.editor-toggle').forEach((button) => {
                    button.setAttribute('aria-expanded', 'false');
                    if (!button.hasAttribute('data-permission-editor')) button.textContent = 'Editar';
                });
            };
            const renderUsers = () => {
                const search = normalize(searchInput?.value.trim());
                const company = companySelect?.value || '';
                const module = moduleSelect?.value || '';
                const status = statusSelect?.value || '';
                const matching = userRows.filter((row) => {
                    const companies = JSON.parse(row.dataset.companies || '[]');
                    const modules = JSON.parse(row.dataset.modules || '[]');
                    return (!search || normalize(row.dataset.search).includes(search))
                        && (!company || row.dataset.allCompanies === 'true' || companies.includes(company))
                        && (!module || modules.includes(module))
                        && (!status || row.dataset.status === status);
                });
                const pages = Math.max(1, Math.ceil(matching.length / pageSize));
                currentPage = Math.max(1, Math.min(currentPage, pages));
                const visible = new Set(matching.slice((currentPage - 1) * pageSize, currentPage * pageSize));
                userRows.forEach((row) => {
                    row.hidden = !visible.has(row);
                    if (row.hidden) closeEditor(row);
                });
                if (emptyRow) emptyRow.hidden = matching.length > 0;
                if (previousPage) previousPage.disabled = currentPage <= 1;
                if (nextPage) nextPage.disabled = currentPage >= pages;
                if (pageLabel) pageLabel.textContent = `${currentPage} de ${pages}`;
                if (resultCount) {
                    const first = matching.length ? (currentPage - 1) * pageSize + 1 : 0;
                    const last = Math.min(currentPage * pageSize, matching.length);
                    resultCount.textContent = `Mostrando ${first}–${last} de ${matching.length} usuarios`;
                }
            };
            searchInput?.addEventListener('input', () => { currentPage = 1; renderUsers(); });
            [companySelect, moduleSelect, statusSelect].forEach((select) => select?.addEventListener('change', () => {
                currentPage = 1;
                renderUsers();
            }));
            previousPage?.addEventListener('click', () => { currentPage--; renderUsers(); });
            nextPage?.addEventListener('click', () => { currentPage++; renderUsers(); });
            renderUsers();

            document.querySelectorAll('.editor-toggle').forEach((button) => {
                button.addEventListener('click', () => {
                    const row = document.getElementById(button.dataset.target);
                    if (!row) return;
                    const shouldOpen = button.hasAttribute('data-permission-editor') || row.hidden;
                    row.hidden = !shouldOpen;
                    button.closest('[data-authorization-row]')?.querySelectorAll('.editor-toggle').forEach((toggle) => {
                        toggle.setAttribute('aria-expanded', String(shouldOpen));
                        if (!toggle.hasAttribute('data-permission-editor')) toggle.textContent = shouldOpen ? 'Cerrar' : 'Editar';
                    });
                    if (shouldOpen && button.hasAttribute('data-permission-editor')) {
                        row.querySelector('[data-navigation-permission-manager]')?.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    }
                    if (shouldOpen) window.dispatchEvent(new Event('resize'));
                });
            });
            const failedEditId = @json((int) old('_authorization_edit_id'));
            if (failedEditId) {
                const failedRowIndex = userRows.findIndex((row) => row.dataset.editorId === `finance-editor-${failedEditId}`);
                if (failedRowIndex >= 0) {
                    currentPage = Math.floor(failedRowIndex / pageSize) + 1;
                    renderUsers();
                }
                const editButton = document.querySelector(`[data-target="finance-editor-${failedEditId}"]:not([data-permission-editor])`);
                editButton?.click();
            }

            document.querySelectorAll('[data-navigation-permission-manager]').forEach((manager) => {
                const categoryInput = manager.querySelector('[data-navigation-category]');
                const categoryButtons = Array.from(manager.querySelectorAll('[data-navigation-category-button]'));
                const panels = Array.from(manager.querySelectorAll('[data-navigation-panel]'));
                const allCheckboxes = Array.from(manager.querySelectorAll('input[name="menu_permissions[]"]'));
                const total = manager.querySelector('[data-navigation-permission-total]');
                const activeCategoryLabel = manager.querySelector('[data-navigation-active-category-label]');
                const carousel = manager.querySelector('[data-navigation-carousel]');
                const carouselTrack = carousel?.querySelector('[data-navigation-carousel-track]');
                const previousButton = carousel?.querySelector('[data-navigation-carousel-prev]');
                const nextButton = carousel?.querySelector('[data-navigation-carousel-next]');

                const updateCounts = () => {
                    if (total) total.textContent = allCheckboxes.filter((checkbox) => checkbox.checked).length;
                    panels.forEach((panel) => {
                        const count = panel.querySelector('[data-navigation-category-count]');
                        const checked = panel.querySelectorAll('input[name="menu_permissions[]"]:checked').length;
                        if (count) count.textContent = checked;

                        const card = categoryButtons.find((button) => button.dataset.navigationCategoryButton === panel.dataset.navigationPanel);
                        const cardCount = card?.querySelector('[data-navigation-card-count]');
                        if (cardCount) cardCount.textContent = checked;
                    });
                };

                const updateCarouselButtons = () => {
                    if (!carouselTrack) return;
                    const maximum = Math.max(0, carouselTrack.scrollWidth - carouselTrack.clientWidth);
                    if (previousButton) previousButton.disabled = carouselTrack.scrollLeft <= 2;
                    if (nextButton) nextButton.disabled = carouselTrack.scrollLeft >= maximum - 2;
                };

                const showCategory = (categoryKey, shouldScroll = false) => {
                    const activePanel = panels.find((panel) => panel.dataset.navigationPanel === categoryKey) || panels[0];
                    if (!activePanel) return;

                    categoryInput.value = activePanel.dataset.navigationPanel;
                    panels.forEach((panel) => {
                        panel.hidden = panel !== activePanel;
                    });

                    const activeCard = categoryButtons.find((button) => button.dataset.navigationCategoryButton === activePanel.dataset.navigationPanel);
                    categoryButtons.forEach((button) => {
                        const isActive = button === activeCard;
                        button.classList.toggle('is-active', isActive);
                        button.setAttribute('aria-pressed', String(isActive));
                    });

                    if (activeCategoryLabel) {
                        activeCategoryLabel.textContent = activePanel.dataset.navigationLabel || '';
                    }

                    if (shouldScroll && activeCard && carouselTrack) {
                        const leftEdge = activeCard.offsetLeft - carouselTrack.offsetLeft;
                        const rightEdge = leftEdge + activeCard.offsetWidth;
                        if (leftEdge < carouselTrack.scrollLeft || rightEdge > carouselTrack.scrollLeft + carouselTrack.clientWidth) {
                            carouselTrack.scrollTo({ left: Math.max(0, leftEdge - 4), behavior: 'smooth' });
                        }
                    }
                };

                categoryButtons.forEach((button) => {
                    button.addEventListener('click', () => showCategory(button.dataset.navigationCategoryButton, true));
                });
                allCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', updateCounts));
                panels.forEach((panel) => {
                    panel.querySelector('[data-navigation-select-all]')?.addEventListener('click', () => {
                        panel.querySelectorAll('input[name="menu_permissions[]"]').forEach((checkbox) => checkbox.checked = true);
                        updateCounts();
                    });
                    panel.querySelector('[data-navigation-clear]')?.addEventListener('click', () => {
                        panel.querySelectorAll('input[name="menu_permissions[]"]').forEach((checkbox) => checkbox.checked = false);
                        updateCounts();
                    });
                });

                previousButton?.addEventListener('click', () => {
                    carouselTrack?.scrollBy({ left: -Math.max(180, carouselTrack.clientWidth * .75), behavior: 'smooth' });
                });
                nextButton?.addEventListener('click', () => {
                    carouselTrack?.scrollBy({ left: Math.max(180, carouselTrack.clientWidth * .75), behavior: 'smooth' });
                });
                carouselTrack?.addEventListener('scroll', updateCarouselButtons, { passive: true });
                window.addEventListener('resize', updateCarouselButtons);
                if (carouselTrack && 'ResizeObserver' in window) {
                    new ResizeObserver(updateCarouselButtons).observe(carouselTrack);
                }

                showCategory(categoryInput.value);
                updateCounts();
                window.requestAnimationFrame(updateCarouselButtons);
            });
        </script>
    </x-app-shell>
@endsection
