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
    $authorizationView = request()->query('view') === 'users' ? 'users' : 'create';
@endphp

@section('body')
    <x-app-shell title="Autorizaciones de usuarios">
        <div class="authorization-view-tabs" role="tablist" aria-label="Vistas de autorizaciones" data-authorization-view-tabs data-initial-view="{{ $authorizationView }}">
            <button
                id="authorization-create-tab"
                class="authorization-view-tab {{ $authorizationView === 'create' ? 'is-active' : '' }}"
                type="button"
                role="tab"
                aria-controls="authorization-create-view"
                aria-selected="{{ $authorizationView === 'create' ? 'true' : 'false' }}"
                data-authorization-view-target="create"
            >Nuevo usuario</button>
            <button
                id="authorization-users-tab"
                class="authorization-view-tab {{ $authorizationView === 'users' ? 'is-active' : '' }}"
                type="button"
                role="tab"
                aria-controls="authorization-users-view"
                aria-selected="{{ $authorizationView === 'users' ? 'true' : 'false' }}"
                data-authorization-view-target="users"
            >Ver usuarios</button>
        </div>

        <section
            id="authorization-create-view"
            class="panel collapsible-panel authorization-view-panel"
            role="tabpanel"
            aria-labelledby="authorization-create-tab"
            data-authorization-view-panel="create"
            data-collapsible-panel
            data-collapsible-name="alta de usuarios y autorizaciones"
            @if ($authorizationView !== 'create') hidden @endif
        >
            <div class="collapsible-panel-header">
                <div>
                    <h2>Alta de usuarios y autorizaciones</h2>
                    <p class="fine-print">Finanzas puede crear usuarios y definir sus accesos por menú, empresa y almacén.</p>
                </div>
                <button
                    class="collapsible-panel-toggle"
                    type="button"
                    aria-expanded="true"
                    aria-controls="user-authorization-create-content"
                    aria-label="Ocultar alta de usuarios y autorizaciones"
                    title="Ocultar alta de usuarios y autorizaciones"
                    data-collapsible-toggle
                ><span aria-hidden="true" data-collapsible-symbol>-</span></button>
            </div>

            <form id="user-authorization-create-content" class="stack" method="POST" action="{{ route('finance.admin.users.store') }}" data-collapsible-content data-credential-generator>
                @csrf
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
        </section>

        <section
            id="authorization-users-view"
            class="panel collapsible-panel authorization-view-panel"
            role="tabpanel"
            aria-labelledby="authorization-users-tab"
            data-authorization-view-panel="users"
            data-collapsible-panel
            data-collapsible-name="usuarios autorizados"
            @if ($authorizationView !== 'users') hidden @endif
        >
            <div class="collapsible-panel-header">
                <h2>Usuarios autorizados</h2>
                <button
                    class="collapsible-panel-toggle"
                    type="button"
                    aria-expanded="true"
                    aria-controls="authorized-users-content"
                    aria-label="Ocultar usuarios autorizados"
                    title="Ocultar usuarios autorizados"
                    data-collapsible-toggle
                ><span aria-hidden="true" data-collapsible-symbol>-</span></button>
            </div>
            <div id="authorized-users-content" class="table-scroll" data-collapsible-content>
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Subcategoria</th>
                            <th>Menus</th>
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
                                <td>{{ $managedUser->username ?: 'Sin usuario' }}</td>
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
                                    @forelse ($managedUser->authorizedNavigationCategoryLabels() as $categoryLabel)
                                        <span class="navigation-category-chip">{{ $categoryLabel }}</span>
                                    @empty
                                        <span class="fine-print">Sin acceso</span>
                                    @endforelse
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
                                <td colspan="10">
                                    <form class="stack" method="POST" action="{{ route('finance.admin.users.update', $managedUser) }}">
                                        @csrf
                                        @method('PUT')
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
                        @empty
                            <tr>
                                <td colspan="10">No hay usuarios autorizados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <style>
            .authorization-view-tabs {
                width: max-content;
                max-width: 100%;
                padding: 4px;
                border: 1px solid #cbd9e7;
                border-radius: 8px;
                background: #eef3f8;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .authorization-view-tab {
                min-height: 34px;
                padding: 7px 13px;
                border: 1px solid transparent;
                border-radius: 6px;
                background: transparent;
                color: #425368;
                font: inherit;
                font-size: .84rem;
                font-weight: 800;
                white-space: nowrap;
                cursor: pointer;
            }

            .authorization-view-tab:hover,
            .authorization-view-tab:focus-visible {
                color: #0d637a;
                outline: none;
            }

            .authorization-view-tab:focus-visible {
                box-shadow: 0 0 0 3px rgba(20, 113, 139, .16);
            }

            .authorization-view-tab.is-active {
                border-color: #14718b;
                background: #14718b;
                color: #fff;
                box-shadow: 0 2px 5px rgba(20, 113, 139, .18);
            }

            .authorization-view-panel[hidden] {
                display: none !important;
            }

            .collapsible-panel-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 16px;
            }

            .collapsible-panel-header h2 {
                margin: 0;
            }

            .collapsible-panel-toggle {
                display: inline-grid;
                flex: 0 0 36px;
                width: 36px;
                height: 36px;
                place-items: center;
                border: 1px solid #cbd9e7;
                border-radius: 6px;
                background: #fff;
                color: #123247;
                padding: 0;
                font: 700 1.25rem/1 sans-serif;
                cursor: pointer;
            }

            .collapsible-panel-toggle:hover,
            .collapsible-panel-toggle:focus-visible {
                border-color: #14718b;
                color: #0d637a;
                outline: 2px solid transparent;
                box-shadow: 0 0 0 3px rgba(20, 113, 139, .14);
            }

            .authorization-configuration-stack {
                display: grid;
                gap: 16px;
            }

            .authorization-user-fields {
                display: grid;
                grid-template-columns: minmax(0, 420px);
                gap: 8px;
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
                .authorization-view-tabs {
                    width: 100%;
                }

                .authorization-view-tab {
                    flex: 1 1 0;
                }

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

            const authorizationViewTabs = document.querySelector('[data-authorization-view-tabs]');

            if (authorizationViewTabs) {
                const viewButtons = Array.from(authorizationViewTabs.querySelectorAll('[data-authorization-view-target]'));
                const viewPanels = Array.from(document.querySelectorAll('[data-authorization-view-panel]'));

                const showAuthorizationView = (view, updateUrl = true) => {
                    const activeButton = viewButtons.find((button) => button.dataset.authorizationViewTarget === view) || viewButtons[0];
                    if (!activeButton) return;

                    const activeView = activeButton.dataset.authorizationViewTarget;
                    viewButtons.forEach((button) => {
                        const isActive = button === activeButton;
                        button.classList.toggle('is-active', isActive);
                        button.setAttribute('aria-selected', String(isActive));
                        button.tabIndex = isActive ? 0 : -1;
                    });
                    viewPanels.forEach((panel) => {
                        panel.hidden = panel.dataset.authorizationViewPanel !== activeView;
                    });

                    if (updateUrl) {
                        const url = new URL(window.location.href);
                        if (activeView === 'create') url.searchParams.delete('view');
                        else url.searchParams.set('view', activeView);
                        window.history.replaceState({}, '', url);
                    }

                    window.dispatchEvent(new Event('resize'));
                };

                viewButtons.forEach((button, index) => {
                    button.addEventListener('click', () => showAuthorizationView(button.dataset.authorizationViewTarget));
                    button.addEventListener('keydown', (event) => {
                        if (!['ArrowLeft', 'ArrowRight'].includes(event.key)) return;
                        event.preventDefault();
                        const direction = event.key === 'ArrowRight' ? 1 : -1;
                        const nextButton = viewButtons[(index + direction + viewButtons.length) % viewButtons.length];
                        showAuthorizationView(nextButton.dataset.authorizationViewTarget);
                        nextButton.focus();
                    });
                });

                showAuthorizationView(authorizationViewTabs.dataset.initialView || 'create', false);
            }

            document.querySelectorAll('[data-collapsible-panel]').forEach((panel) => {
                const button = panel.querySelector('[data-collapsible-toggle]');
                const content = panel.querySelector('[data-collapsible-content]');
                const symbol = button?.querySelector('[data-collapsible-symbol]');
                const name = panel.dataset.collapsibleName || 'seccion';

                if (!button || !content || !symbol) return;

                button.addEventListener('click', () => {
                    const isExpanded = button.getAttribute('aria-expanded') === 'true';
                    const nextExpanded = !isExpanded;
                    const action = nextExpanded ? 'Ocultar' : 'Mostrar';

                    content.hidden = !nextExpanded;
                    button.setAttribute('aria-expanded', String(nextExpanded));
                    button.setAttribute('aria-label', `${action} ${name}`);
                    button.title = `${action} ${name}`;
                    symbol.textContent = nextExpanded ? '-' : '+';
                });
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
