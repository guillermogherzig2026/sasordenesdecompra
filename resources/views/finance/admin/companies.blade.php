@extends('layouts.app')

@section('body')
    <x-app-shell title="Empresas y Negocios">
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

            $selectedBuyerIds = is_array(old('buyer_ids')) ? array_map('intval', old('buyer_ids')) : [];
            $branchRows = old('branches', []);
            $branchRows = is_array($branchRows) ? $branchRows : [];
            $primaryBranch = old('primary_branch', array_key_first($branchRows));
        @endphp

        <div class="company-directory">
            <header class="company-directory-heading">
                <div>
                    <h2>Empresas y Negocios</h2>
                </div>
                <button class="button primary" type="button" data-company-create-open>
                    <span aria-hidden="true">+</span> Nueva empresa o negocio
                </button>
            </header>

            <x-filter-carousel label="Ubicación de empresas"
                :items="$locationOptions" :selected="$location"
                route-name="finance.admin.companies" parameter="location" />

            <form class="company-directory-filters" method="GET" action="{{ route('finance.admin.companies') }}" data-company-filter-form>
                @if ($location !== '') <input type="hidden" name="location" value="{{ $location }}"> @endif
                <label class="company-directory-search">
                    <span>Buscar</span>
                    <input type="search" name="q" value="{{ $query }}" placeholder="Empresa, RFC o dirección" autocomplete="off">
                </label>
                <label>
                    Giro de negocio
                    <select name="giro" data-company-filter-select>
                        <option value="">Todos</option>
                        <option value="__none" @selected($businessLine === '__none')>Sin giro registrado</option>
                        @foreach ($businessLines as $line)
                            <option value="{{ $line }}" @selected($businessLine === $line)>{{ $line }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Empresa
                    <select name="company_id" data-company-filter-select>
                        <option value="">Todas</option>
                        @foreach ($companyOptions as $option)
                            <option value="{{ $option->id }}" @selected($companyId === $option->id)>{{ $option->name }}</option>
                        @endforeach
                    </select>
                </label>
                <button class="button ghost" type="submit">Buscar</button>
                <a class="button ghost" href="{{ route('finance.admin.companies') }}">Limpiar filtros</a>
                <a class="button ghost" href="{{ route('reports.download', 'companies') }}">Exportar Excel</a>
            </form>

            <section class="company-directory-list" aria-label="Catálogo de empresas">
                <div class="company-directory-table-scroll">
                    <table class="company-directory-table">
                        <thead>
                            <tr>
                                <th scope="col">Empresa</th>
                                <th scope="col">Giro de negocio</th>
                                <th scope="col">Ubicación</th>
                                <th scope="col">Sucursales</th>
                                <th scope="col">Almacenes</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($companies as $company)
                                @php
                                    $warehouses = $company->warehouseObjects();
                                    $securityEntities = $canViewSecurityBranches
                                        ? $company->securityCompanies->concat($unlinkedSecurityCompaniesByRfc->get($company->rfc, collect()))->unique('id')
                                        : collect();
                                    $branchCount = $securityEntities->sum(fn ($entity) => $entity->branches->count());
                                    $firstSecurityEntity = $securityEntities->first();
                                    $detailId = 'company-detail-'.$company->id;
                                @endphp
                                <tr class="company-directory-row" id="company-{{ $company->id }}">
                                    <td data-filter-value="{{ $company->name }}">
                                        <div class="company-directory-identity">
                                            <button class="company-expand" type="button" aria-label="Mostrar sucursales y almacenes de {{ $company->name }}" aria-controls="{{ $detailId }}" aria-expanded="false" data-company-expand data-company-name="{{ $company->name }}">
                                                <span aria-hidden="true">›</span>
                                            </button>
                                            @if ($company->logo_path)
                                                <img class="company-directory-logo" src="{{ route('companies.logo', $company) }}" alt="" loading="lazy">
                                            @else
                                                <span class="company-directory-logo company-directory-initials" aria-hidden="true">{{ $company->initials() }}</span>
                                            @endif
                                            <span class="company-directory-name">
                                                <strong>{{ $company->name }}</strong>
                                                <small>{{ $company->rfc }}</small>
                                            </span>
                                        </div>
                                    </td>
                                    <td>{{ $company->business_line ?: 'Sin giro registrado' }}</td>
                                    <td class="company-directory-location" title="{{ $company->address }}">{{ $company->address ?: 'Sin dirección' }}</td>
                                    <td><span class="company-directory-count">{{ $branchCount }} {{ $branchCount === 1 ? 'sucursal' : 'sucursales' }}</span></td>
                                    <td><span class="company-directory-count">{{ count($warehouses) }} {{ count($warehouses) === 1 ? 'almacén' : 'almacenes' }}</span></td>
                                    <td>
                                        <div class="company-directory-actions">
                                            <a class="button ghost small" href="{{ route('finance.admin.companies.edit', $company) }}">Editar</a>
                                            <button class="button ghost small company-delete-button" type="button" data-dialog-target="delete-company-{{ $company->id }}">Eliminar</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="company-directory-detail" id="{{ $detailId }}" hidden>
                                    <td colspan="6">
                                        <div class="company-directory-detail-grid">
                                            <section aria-label="Sucursales de {{ $company->name }}">
                                                <header>
                                                    <h3>Sucursales <span>({{ $branchCount }})</span></h3>
                                                    @if ($canViewSecurityBranches && ($firstSecurityEntity || $canCreateSecurityCompany))
                                                        <button class="button ghost small" type="button" data-company-branch-open data-company-branch-id="{{ $company->id }}" data-company-branch-name="{{ $company->name }}" data-company-branch-address="{{ $company->address }}" data-company-branch-action="{{ route('finance.admin.companies.branches.store', $company) }}">+ Agregar Sucursal</button>
                                                    @endif
                                                </header>
                                                @if (! $canViewSecurityBranches)
                                                    <p class="company-directory-empty">Consulta las sucursales en Seguridad y Vigilancia.</p>
                                                @elseif ($branchCount === 0)
                                                    <p class="company-directory-empty">No hay sucursales vinculadas.</p>
                                                @else
                                                    <div class="company-directory-subhead"><span>Nombre</span><span>Ubicación</span><span>Acciones</span></div>
                                                    @foreach ($securityEntities as $securityEntity)
                                                        @foreach ($securityEntity->branches as $branch)
                                                            <div class="company-directory-subrow">
                                                                <strong>{{ $branch->name }}@if ($branch->code) <small>{{ $branch->code }}</small>@endif @if ($branch->is_primary) <small>Principal</small>@endif</strong>
                                                                <span>{{ $branch->address ?: collect([$branch->city, $branch->state])->filter()->implode(', ') ?: 'Sin dirección' }}</span>
                                                                <span class="company-directory-subactions">
                                                                    <a href="{{ route('security.index', ['section' => 'branches', 'company_id' => $securityEntity->id, 'branch_id' => $branch->id, 'edit_branch' => $branch->id]) }}">Ver / Editar</a>
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    @endforeach
                                                @endif
                                            </section>
                                            <section aria-label="Almacenes de {{ $company->name }}">
                                                <header>
                                                    <h3>Almacenes <span>({{ count($warehouses) }})</span></h3>
                                                    <a class="button ghost small" href="{{ route('finance.admin.companies.edit', $company) }}#almacenes">+ Agregar almacén</a>
                                                </header>
                                                @if (count($warehouses) === 0)
                                                    <p class="company-directory-empty">No hay almacenes registrados.</p>
                                                @else
                                                    <div class="company-directory-subhead"><span>Nombre</span><span>Ubicación</span><span>Acciones</span></div>
                                                    @foreach ($warehouses as $warehouse)
                                                        @php
                                                            $linkedBranch = ! empty($warehouse['branch_id'])
                                                                ? $securityEntities->flatMap(fn ($entity) => $entity->branches)->firstWhere('id', $warehouse['branch_id'])
                                                                : null;
                                                        @endphp
                                                        <div class="company-directory-subrow">
                                                            <strong>{{ $warehouse['name'] }}@if ($warehouse['short_name']) <small>{{ $warehouse['short_name'] }}</small>@endif @if ($linkedBranch) <small>Sucursal: {{ $linkedBranch->name }}</small>@endif</strong>
                                                            <span>{{ $warehouse['address'] ?: 'Sin dirección' }}</span>
                                                            <span class="company-directory-subactions"><button type="button" data-dialog-target="edit-warehouse-{{ $company->id }}-{{ $loop->index }}">Ver / Editar</button></span>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </section>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="company-directory-no-results" colspan="6">No hay empresas que coincidan con los filtros.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <footer class="company-directory-footer">
                    <span>Mostrando {{ $companies->count() }} empresas</span>
                </footer>
            </section>
        </div>

        @if ($canViewSecurityBranches)
            <dialog class="company-create-dialog company-branch-dialog" id="company-branch-dialog" aria-labelledby="company-branch-dialog-title" @if (old('_branch_form') && $errors->any()) data-auto-open @endif>
                <form class="company-create-form" method="POST" action="" data-company-branch-form>
                    @csrf
                    <input type="hidden" name="_branch_form" value="1">
                    <input type="hidden" name="_branch_company_id" value="{{ old('_branch_company_id') }}" data-company-branch-company-id>
                    <header class="company-create-heading">
                        <div><h2 id="company-branch-dialog-title">Agregar Sucursal</h2><p data-company-branch-company-name></p></div>
                        <button type="button" class="company-create-close" data-company-branch-close aria-label="Cerrar formulario">×</button>
                    </header>
                    <div class="company-create-fields">
                        @if (old('_branch_form') && $errors->any())
                            <div class="company-create-errors" role="alert" data-company-branch-errors>
                                @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                            </div>
                        @endif
                        <div class="company-create-grid">
                            <label>Nombre de sucursal *<input name="branch[name]" value="{{ old('branch.name') }}" maxlength="255" required></label>
                            <label>Clave<input name="branch[code]" value="{{ old('branch.code') }}" maxlength="50"></label>
                            <label class="company-create-wide">Dirección *<textarea name="branch[address]" rows="2" maxlength="1000" data-company-branch-address required>{{ old('branch.address') }}</textarea></label>
                            <label>Estado *<input name="branch[state]" value="{{ old('branch.state') }}" maxlength="120" required></label>
                            <label>Ciudad *<input name="branch[city]" value="{{ old('branch.city') }}" maxlength="120" required></label>
                            <label>Responsable<input name="branch[contact_name]" value="{{ old('branch.contact_name') }}" maxlength="255"></label>
                            <label>Teléfono<input name="branch[phone]" value="{{ old('branch.phone') }}" maxlength="40" inputmode="tel"></label>
                        </div>
                    </div>
                    <footer class="company-create-actions">
                        <button class="button ghost" type="button" data-company-branch-close>Cancelar</button>
                        <button class="button primary" type="submit">Guardar sucursal</button>
                    </footer>
                </form>
            </dialog>
        @endif

        <dialog class="company-create-dialog" id="company-create-dialog" @if (($errors->any() && ! old('_branch_form') && ! old('_warehouse_form')) || request()->boolean('create')) data-auto-open @endif>
            <form class="company-create-form" method="POST" action="{{ route('finance.admin.companies.store') }}" enctype="multipart/form-data">
                @csrf
                <header class="company-create-heading">
                    <div>
                        <h2>Nueva empresa o negocio</h2>
                        <p>Registra la empresa y sus sucursales en un solo lugar.</p>
                    </div>
                    <button type="button" class="company-create-close" data-company-create-close aria-label="Cerrar formulario">×</button>
                </header>
                <div class="company-create-fields">
                    @if ($errors->any() && ! old('_branch_form'))
                        <div class="company-create-errors" role="alert">
                            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                        </div>
                    @endif
                    <section class="company-create-section" aria-labelledby="company-create-general-heading">
                        <header class="company-create-section-heading">
                            <span class="company-create-step" aria-hidden="true">1</span>
                            <h3 id="company-create-general-heading">Datos generales</h3>
                        </header>
                        <div class="company-create-grid">
                            <label>Razón social *<input name="name" value="{{ old('name') }}" maxlength="255" required></label>
                            <label>RFC *<input name="rfc" value="{{ old('rfc') }}" maxlength="20" required></label>
                            <label>Giro de negocio<input name="business_line" value="{{ old('business_line') }}" maxlength="120" list="company-business-lines" placeholder="Ej. Servicios empresariales"></label>
                            <datalist id="company-business-lines">
                                @foreach ($businessLines as $line)<option value="{{ $line }}"></option>@endforeach
                            </datalist>
                            <label>Logotipo<input name="logo" type="file" accept="image/*"></label>
                            <label class="company-create-wide">Dirección fiscal *<textarea id="company-address" name="address" rows="2" maxlength="1000" placeholder="Calle, número, colonia, ciudad y código postal" required>{{ $companyAddress }}</textarea></label>
                            <label class="company-create-wide">Observaciones para OC<textarea name="purchase_order_notes" rows="2" maxlength="3000" placeholder="Documentos requeridos, entrega o pago">{{ old('purchase_order_notes') }}</textarea></label>
                        </div>
                    </section>
                    @if ($canViewSecurityBranches)
                        <section class="company-create-section" aria-labelledby="company-create-branches-heading">
                            <header class="company-create-section-heading">
                                <span class="company-create-step" aria-hidden="true">2</span>
                                <h3 id="company-create-branches-heading">Sucursales</h3>
                                <button type="button" class="button primary small" data-branch-add>+ Agregar sucursal</button>
                            </header>
                            <div class="company-branch-list" data-branch-list data-next-index="{{ $branchRows ? max(array_keys($branchRows)) + 1 : 0 }}">
                                @foreach ($branchRows as $index => $branch)
                                    <article class="company-branch-card" data-branch-row data-branch-index="{{ $index }}">
                                        <header class="company-branch-card-heading">
                                            <label class="company-branch-primary"><input type="radio" name="primary_branch" value="{{ $index }}" @checked((string) $primaryBranch === (string) $index)><span>Principal</span></label>
                                            <span class="company-branch-summary"><strong data-branch-title>{{ $branch['name'] ?? 'Nueva sucursal' }}</strong><small data-branch-code>{{ $branch['code'] ?? '' }}</small></span>
                                            <button type="button" class="company-branch-icon" data-branch-toggle aria-expanded="true" aria-controls="company-branch-fields-{{ $index }}" aria-label="Contraer sucursal">⌃</button>
                                            <button type="button" class="company-branch-icon company-branch-remove" data-branch-remove aria-label="Quitar sucursal">×</button>
                                        </header>
                                        <div class="company-branch-fields company-create-grid" id="company-branch-fields-{{ $index }}" data-branch-fields>
                                            <label>Nombre de sucursal *<input name="branches[{{ $index }}][name]" value="{{ $branch['name'] ?? '' }}" maxlength="255" data-branch-name required></label>
                                            <label>Clave<input name="branches[{{ $index }}][code]" value="{{ $branch['code'] ?? '' }}" maxlength="50" data-branch-code-input></label>
                                            <label class="company-create-wide">Dirección *<textarea name="branches[{{ $index }}][address]" rows="2" maxlength="1000" data-branch-address required>{{ $branch['address'] ?? '' }}</textarea></label>
                                            <label class="company-branch-use-address"><input type="checkbox" data-branch-use-address @checked(($branch['address'] ?? '') !== '' && trim($branch['address']) === trim($companyAddress))> Usar dirección fiscal</label>
                                            <label>Estado *<input name="branches[{{ $index }}][state]" value="{{ $branch['state'] ?? '' }}" maxlength="120" required></label>
                                            <label>Ciudad *<input name="branches[{{ $index }}][city]" value="{{ $branch['city'] ?? '' }}" maxlength="120" required></label>
                                            <label>Responsable<input name="branches[{{ $index }}][contact_name]" value="{{ $branch['contact_name'] ?? '' }}" maxlength="255"></label>
                                            <label>Teléfono<input name="branches[{{ $index }}][phone]" value="{{ $branch['phone'] ?? '' }}" maxlength="40" inputmode="tel"></label>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                            <template id="company-branch-template">
                                <article class="company-branch-card" data-branch-row>
                                    <header class="company-branch-card-heading">
                                        <label class="company-branch-primary"><input type="radio" name="primary_branch" data-branch-primary><span>Principal</span></label>
                                        <span class="company-branch-summary"><strong data-branch-title>Nueva sucursal</strong><small data-branch-code></small></span>
                                        <button type="button" class="company-branch-icon" data-branch-toggle aria-expanded="true" aria-label="Contraer sucursal">⌃</button>
                                        <button type="button" class="company-branch-icon company-branch-remove" data-branch-remove aria-label="Quitar sucursal">×</button>
                                    </header>
                                    <div class="company-branch-fields company-create-grid" data-branch-fields>
                                        <label>Nombre de sucursal *<input data-branch-field="name" data-branch-name maxlength="255" required></label>
                                        <label>Clave<input data-branch-field="code" data-branch-code-input maxlength="50"></label>
                                        <label class="company-create-wide">Dirección *<textarea data-branch-field="address" data-branch-address rows="2" maxlength="1000" required></textarea></label>
                                        <label class="company-branch-use-address"><input type="checkbox" data-branch-use-address> Usar dirección fiscal</label>
                                        <label>Estado *<input data-branch-field="state" maxlength="120" required></label>
                                        <label>Ciudad *<input data-branch-field="city" maxlength="120" required></label>
                                        <label>Responsable<input data-branch-field="contact_name" maxlength="255"></label>
                                        <label>Teléfono<input data-branch-field="phone" maxlength="40" inputmode="tel"></label>
                                    </div>
                                </article>
                            </template>
                        </section>
                    @endif
                    <section class="company-create-warehouses" aria-label="Almacenes">
                        <header class="company-create-section-heading">
                            <span class="company-create-step" aria-hidden="true">{{ $canViewSecurityBranches ? 3 : 2 }}</span>
                            <h3>Almacenes *</h3>
                            <button type="button" class="button ghost small" data-warehouse-add>+ Agregar almacén</button>
                        </header>
                        <div id="warehouses-list" data-next-index="{{ $warehouseRows ? max(array_keys($warehouseRows)) + 1 : 0 }}">
                            @foreach ($warehouseRows as $warehouse)
                                @php
                                    $warehouseAddress = trim((string) ($warehouse['address'] ?? '')) ?: trim((string) $companyAddress);
                                    $followsCompanyAddress = blank($warehouse['address'] ?? null) || $warehouseAddress === trim((string) $companyAddress);
                                @endphp
                                <div class="warehouse-row">
                                    <label>Nombre *<input name="warehouses[{{ $loop->index }}][name]" value="{{ $warehouse['name'] ?? '' }}" maxlength="255" required></label>
                                    <label>Nombre corto<input name="warehouses[{{ $loop->index }}][short_name]" value="{{ $warehouse['short_name'] ?? '' }}" maxlength="50"></label>
                                    <label>Dirección *<input name="warehouses[{{ $loop->index }}][address]" value="{{ $warehouseAddress }}" maxlength="1000" data-warehouse-address data-follows-company-address="{{ $followsCompanyAddress ? 'true' : 'false' }}" required></label>
                                    @if ($canViewSecurityBranches)
                                        <label>Sucursal vinculada<select name="warehouses[{{ $loop->index }}][branch_index]" data-warehouse-branch data-selected-branch="{{ $warehouse['branch_index'] ?? '' }}"><option value="">Sin vincular</option></select></label>
                                    @endif
                                    <button type="button" class="company-warehouse-remove" data-warehouse-remove aria-label="Quitar almacén">×</button>
                                </div>
                            @endforeach
                        </div>
                        <template id="warehouse-row-template">
                            <div class="warehouse-row">
                                <label>Nombre *<input data-warehouse-field="name" maxlength="255" required></label>
                                <label>Nombre corto<input data-warehouse-field="short_name" maxlength="50"></label>
                                <label>Dirección *<input data-warehouse-field="address" data-warehouse-address data-follows-company-address="true" maxlength="1000" required></label>
                                @if ($canViewSecurityBranches)
                                    <label>Sucursal vinculada<select data-warehouse-field="branch_index" data-warehouse-branch><option value="">Sin vincular</option></select></label>
                                @endif
                                <button type="button" class="company-warehouse-remove" data-warehouse-remove aria-label="Quitar almacén">×</button>
                            </div>
                        </template>
                    </section>
                    <details class="company-create-buyers" @if (old('buyer_ids')) open @endif>
                        <summary class="company-create-section-heading">
                            <span class="company-create-step" aria-hidden="true">{{ $canViewSecurityBranches ? 4 : 3 }}</span>
                            <span>Compradores autorizados</span>
                        </summary>
                        <div class="company-create-buyer-list">
                            @forelse ($buyers as $buyer)
                                <label><input name="buyer_ids[]" type="checkbox" value="{{ $buyer->id }}" @checked(in_array($buyer->id, $selectedBuyerIds, true))>{{ $buyer->name }}</label>
                            @empty
                                <p class="fine-print">No hay compradores activos.</p>
                            @endforelse
                        </div>
                    </details>
                </div>
                <footer class="company-create-actions">
                    <span class="company-create-summary" data-company-create-summary></span>
                    <button class="button ghost" type="button" data-company-create-close>Cancelar</button>
                    <button class="button primary" type="submit">Guardar empresa y sucursales</button>
                </footer>
            </form>
        </dialog>

        @foreach ($companies as $company)
            <dialog class="confirm-dialog" id="delete-company-{{ $company->id }}">
                <form class="confirm-card" method="POST" action="{{ route('finance.admin.companies.destroy', $company) }}">
                    @csrf
                    @method('DELETE')
                    <h3>Eliminar empresa</h3>
                    <p>¿Estás seguro de que quieres eliminar {{ $company->name }}?</p>
                    <div class="form-actions">
                        <button class="button danger" type="submit">Sí, eliminar</button>
                        <button class="button ghost" type="button" data-dialog-close>Cancelar</button>
                    </div>
                </form>
            </dialog>
            @foreach ($company->warehouseObjects() as $warehouseIndex => $warehouse)
                @php
                    $warehouseForm = old('_warehouse_form')
                        && (string) old('_warehouse_company_id') === (string) $company->id
                        && (string) old('_warehouse_index') === (string) $warehouseIndex;
                    $linkedBranch = $canViewSecurityBranches && ! empty($warehouse['branch_id'])
                        ? $company->securityCompanies
                            ->concat($unlinkedSecurityCompaniesByRfc->get($company->rfc, collect()))
                            ->flatMap(fn ($entity) => $entity->branches)
                            ->firstWhere('id', $warehouse['branch_id'])
                        : null;
                @endphp
                <dialog class="company-create-dialog company-branch-dialog" id="edit-warehouse-{{ $company->id }}-{{ $warehouseIndex }}" aria-labelledby="edit-warehouse-title-{{ $company->id }}-{{ $warehouseIndex }}" @if ($warehouseForm && $errors->any()) data-auto-open @endif>
                    <form class="company-create-form" method="POST" action="{{ route('finance.admin.companies.warehouses.update', [$company, $warehouseIndex]) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="_warehouse_form" value="1">
                        <input type="hidden" name="_warehouse_company_id" value="{{ $company->id }}">
                        <input type="hidden" name="_warehouse_index" value="{{ $warehouseIndex }}">
                        <header class="company-create-heading">
                            <div><h2 id="edit-warehouse-title-{{ $company->id }}-{{ $warehouseIndex }}">Ver / Editar almacén</h2><p>{{ $company->name }}</p></div>
                            <button type="button" class="company-create-close" data-dialog-close aria-label="Cerrar">×</button>
                        </header>
                        <div class="company-create-fields">
                            @if ($warehouseForm && $errors->any())
                                <div class="company-create-errors" role="alert">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
                            @endif
                            <div class="company-create-grid">
                                <label>Nombre *<input name="warehouse[name]" value="{{ $warehouseForm ? old('warehouse.name') : $warehouse['name'] }}" maxlength="255" required></label>
                                <label>Nombre corto<input name="warehouse[short_name]" value="{{ $warehouseForm ? old('warehouse.short_name') : $warehouse['short_name'] }}" maxlength="50"></label>
                                <label class="company-create-wide">Dirección *<textarea name="warehouse[address]" rows="3" maxlength="1000" required>{{ $warehouseForm ? old('warehouse.address') : $warehouse['address'] }}</textarea></label>
                            </div>
                            @if ($linkedBranch)
                                <p class="company-warehouse-branch">Sucursal vinculada: <strong>{{ $linkedBranch->name }}</strong></p>
                            @endif
                        </div>
                        <footer class="company-create-actions">
                            <button class="button ghost" type="button" data-dialog-close>Cerrar</button>
                            <button class="button primary" type="submit">Guardar</button>
                        </footer>
                    </form>
                </dialog>
            @endforeach
        @endforeach

        <style>
            .company-directory { min-width: 0; display: grid; align-content: start; gap: 16px; }
            .company-directory-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; }
            .company-directory-heading h2 { margin: 0; font-size: 1.45rem; }
            .company-directory-heading .button { white-space: nowrap; }
            .company-directory-filters { min-width: 0; display: grid; grid-template-columns: minmax(180px, 1.4fr) minmax(160px, .9fr) minmax(160px, .9fr) auto auto auto; align-items: end; gap: 10px; padding: 16px; border: 1px solid var(--line); border-radius: 8px; background: #fff; }
            .company-directory-filters label { min-width: 0; gap: 5px; font-size: .85rem; }
            .company-directory-filters :is(input, select) { width: 100%; min-width: 0; height: 40px; min-height: 40px; font-weight: 500; }
            .company-directory-filters .button { min-height: 40px; white-space: nowrap; }
            .company-directory-list { min-width: 0; overflow: hidden; border: 1px solid var(--line); border-radius: 8px; background: #fff; }
            .company-directory-table-scroll { min-width: 0; overflow-x: auto; }
            .company-directory-table { width: 100%; min-width: 930px; border-collapse: collapse; table-layout: fixed; }
            .company-directory-table th { padding: 12px 14px; color: #344054; background: #f7fafc; text-align: left; font-size: .82rem; }
            .company-directory-table th:nth-child(1) { width: 22%; }
            .company-directory-table th:nth-child(2) { width: 14%; }
            .company-directory-table th:nth-child(3) { width: 20%; }
            .company-directory-table th:nth-child(4) { width: 12%; }
            .company-directory-table th:nth-child(5) { width: 14%; }
            .company-directory-table th:nth-child(6) { width: 18%; }
            .company-directory-table td { padding: 10px 14px; border-top: 1px solid var(--line); vertical-align: middle; font-size: .89rem; }
            .company-directory-row { position: relative; cursor: pointer; }
            .company-directory-row:hover td, .company-directory-row:focus-within td { background: #f6fbff; }
            .company-directory-row:has(+ .company-directory-detail:not([hidden])) td { background: #f6fbff; }
            .company-directory-identity { min-width: 0; display: flex; align-items: center; gap: 10px; }
            .company-expand { width: 26px; height: 28px; flex: 0 0 26px; border: 0; background: transparent; color: var(--text); font-size: 1.4rem; cursor: pointer; }
            .company-expand::after { content: ''; position: absolute; inset: 0; z-index: 1; }
            .company-expand:focus-visible::after { outline: 2px solid var(--primary-strong); outline-offset: -2px; }
            .company-expand span { display: block; line-height: 1; transform: rotate(0); transition: transform .15s ease; }
            .company-expand[aria-expanded="true"] span { transform: rotate(90deg); }
            .company-directory-logo { width: 42px; height: 42px; flex: 0 0 42px; border-radius: 6px; object-fit: contain; background: #e5f3f7; }
            .company-directory-initials { display: grid; place-items: center; color: #fff; background: #176b87; font-weight: 850; }
            .company-directory-name { min-width: 0; display: grid; gap: 3px; }
            .company-directory-name strong { overflow-wrap: anywhere; }
            .company-directory-name small { color: var(--muted); }
            .company-directory-location { overflow-wrap: anywhere; color: #475467; }
            .company-directory-count { display: inline-block; padding: 6px 9px; border: 1px solid #cfe0f1; border-radius: 6px; background: #f0f7fc; color: #145b7b; font-weight: 700; white-space: nowrap; }
            .company-directory-actions { position: relative; z-index: 2; display: flex; align-items: center; gap: 6px; white-space: nowrap; }
            .company-delete-button { color: var(--danger); }
            .company-directory-detail > td { padding: 0 10px 12px; background: #f6fbff; }
            .company-directory-detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); border: 1px solid #d9e8f5; border-radius: 6px; background: #fff; }
            .company-directory-detail-grid > section { min-width: 0; padding: 12px 14px; }
            .company-directory-detail-grid > section + section { border-left: 1px solid #d9e8f5; }
            .company-directory-detail-grid header { min-height: 36px; display: flex; align-items: center; justify-content: space-between; gap: 8px; }
            .company-directory-detail-grid h3 { margin: 0; font-size: 1rem; }
            .company-directory-detail-grid h3 span { color: var(--muted); font-weight: 600; }
            .company-directory-detail-grid header .button { white-space: nowrap; }
            .company-directory-subhead, .company-directory-subrow { display: grid; grid-template-columns: minmax(120px, 1fr) minmax(140px, 1.2fr) 88px; gap: 8px; align-items: center; }
            .company-directory-subhead { margin-top: 8px; padding: 8px; color: #475467; background: #f5f8fb; font-size: .79rem; font-weight: 800; }
            .company-directory-subrow { padding: 9px 8px; border-bottom: 1px solid #e5edf5; font-size: .82rem; }
            .company-directory-subrow:last-child { border-bottom: 0; }
            .company-directory-subrow > * { min-width: 0; overflow-wrap: anywhere; }
            .company-directory-subrow strong small { display: block; margin-top: 2px; color: var(--muted); font-weight: 500; }
            .company-directory-subactions { display: flex; gap: 7px; flex-wrap: wrap; }
            .company-directory-subactions a { color: var(--primary-strong); font-weight: 750; }
            .company-directory-subactions button { padding: 0; border: 0; background: none; color: var(--primary-strong); font: inherit; font-weight: 750; cursor: pointer; }
            .company-directory-subactions button:hover { text-decoration: underline; }
            .company-directory-empty { margin: 12px 8px 3px; color: var(--muted); font-size: .86rem; }
            .company-directory-no-results { padding: 36px !important; color: var(--muted); text-align: center; }
            .company-directory-footer { min-height: 51px; padding: 10px 16px; border-top: 1px solid var(--line); display: flex; align-items: center; justify-content: space-between; gap: 12px; color: var(--muted); font-size: .84rem; }
            .company-create-dialog { width: min(920px, calc(100vw - 24px)); max-height: calc(100vh - 24px); padding: 0; border: 0; border-radius: 8px; color: var(--text); box-shadow: 0 24px 65px rgba(16, 43, 58, .24); }
            .company-branch-dialog { width: min(640px, calc(100vw - 24px)); }
            .company-create-dialog::backdrop { background: rgba(16, 43, 58, .48); }
            .company-create-form { display: flex; flex-direction: column; max-height: calc(100vh - 24px); }
            .company-create-heading, .company-create-actions { flex: 0 0 auto; padding: 15px 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
            .company-create-heading { border-bottom: 1px solid var(--line); }
            .company-create-heading h2 { margin: 0; font-size: 1.35rem; }
            .company-create-heading p { margin: 3px 0 0; color: var(--muted); font-size: .88rem; }
            .company-create-close { width: 32px; height: 32px; border: 1px solid var(--line); border-radius: 6px; background: #fff; cursor: pointer; font-size: 1.35rem; }
            .company-create-fields { min-height: 0; overflow-y: auto; padding: 14px 20px; display: grid; gap: 14px; }
            .company-create-section, .company-create-warehouses { display: grid; gap: 10px; min-width: 0; }
            .company-create-section-heading { min-height: 42px; padding: 6px 9px; display: flex; align-items: center; gap: 11px; border-radius: 6px; background: #edf6fd; }
            .company-create-section-heading h3, .company-create-section-heading > span:not(.company-create-step) { margin: 0; font-size: 1rem; font-weight: 800; }
            .company-create-section-heading .button { margin-left: auto; white-space: nowrap; }
            .company-create-step { width: 27px; height: 27px; flex: 0 0 27px; display: grid; place-items: center; border-radius: 50%; color: #fff; background: #176b87; font-size: .85rem; font-weight: 800; }
            .company-create-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
            .company-create-grid label { min-width: 0; gap: 5px; font-size: .84rem; font-weight: 700; }
            .company-create-grid .company-create-wide { grid-column: 1 / -1; }
            .company-create-grid :is(input:not([type='checkbox']), textarea, select) { width: 100%; min-width: 0; font-weight: 500; }
            .company-create-grid textarea { resize: vertical; }
            .company-branch-list { display: grid; gap: 8px; }
            .company-branch-card { min-width: 0; overflow: hidden; border: 1px solid #cfe0f1; border-radius: 6px; }
            .company-branch-card-heading { min-height: 42px; padding: 7px 10px; display: flex; align-items: center; gap: 10px; }
            .company-branch-primary { display: flex; align-items: center; gap: 5px; color: #145b7b; font-size: .77rem; font-weight: 800; }
            .company-branch-primary input { width: 17px; height: 17px; min-height: 17px; margin: 0; accent-color: #176b87; }
            .company-branch-summary { min-width: 0; display: flex; align-items: baseline; gap: 8px; flex: 1; }
            .company-branch-summary strong { overflow-wrap: anywhere; font-size: .88rem; }
            .company-branch-summary small { color: var(--muted); font-size: .76rem; }
            .company-branch-icon, .company-warehouse-remove { width: 30px; height: 30px; flex: 0 0 30px; border: 1px solid #cfe0f1; border-radius: 6px; background: #fff; color: #145b7b; cursor: pointer; font-size: 1.1rem; }
            .company-branch-remove { color: var(--danger); }
            .company-branch-fields { padding: 0 12px 12px; }
            .company-branch-fields[hidden] { display: none; }
            .company-branch-use-address { grid-column: 1 / -1; display: flex !important; align-items: center; gap: 7px !important; font-size: .8rem !important; font-weight: 600 !important; }
            .company-branch-use-address input { width: 16px; height: 16px; min-height: 16px; margin: 0; }
            #warehouses-list { display: grid; gap: 8px; }
            .warehouse-row { display: grid; grid-template-columns: minmax(130px, 1.15fr) minmax(100px, .8fr) minmax(160px, 1.4fr) minmax(140px, 1fr) 30px; gap: 7px; align-items: end; }
            .warehouse-row label { min-width: 0; display: grid; gap: 5px; font-size: .78rem; font-weight: 700; }
            .warehouse-row :is(input, select) { width: 100%; min-width: 0; min-height: 36px; font-weight: 500; }
            .company-warehouse-remove:disabled { opacity: .45; cursor: not-allowed; }
            .company-create-buyers { min-width: 0; }
            .company-create-buyers summary { cursor: pointer; list-style: none; }
            .company-create-buyers summary::-webkit-details-marker { display: none; }
            .company-create-buyers summary::after { content: '⌄'; margin-left: auto; font-size: 1.2rem; }
            .company-create-buyers[open] summary::after { transform: rotate(180deg); }
            .company-create-buyer-list { display: flex; flex-wrap: wrap; gap: 8px 18px; padding: 12px 4px; }
            .company-create-buyers label { display: flex; align-items: center; gap: 6px; font-size: .84rem; }
            .company-create-buyers input { width: 17px; height: 17px; min-height: 17px; padding: 0; }
            .company-create-errors { padding: 10px 12px; border: 1px solid #efb3b0; border-radius: 6px; background: #fff3f2; color: #9c251d; }
            .company-create-errors p { margin: 2px 0; }
            .company-warehouse-branch { margin: 0; color: var(--muted); font-size: .84rem; }
            .company-create-actions { justify-content: flex-end; border-top: 1px solid var(--line); }
            .company-create-summary { flex: 1; color: #475467; font-size: .84rem; }
            @media (max-width: 1230px) {
                .company-directory-filters { grid-template-columns: repeat(3, minmax(0, 1fr)); }
                .company-directory-filters .button { justify-self: start; }
            }
            @media (max-width: 900px) {
                .company-directory-detail-grid { grid-template-columns: 1fr; }
                .company-directory-detail-grid > section + section { border-left: 0; border-top: 1px solid #d9e8f5; }
                .warehouse-row { grid-template-columns: repeat(2, minmax(0, 1fr)) 30px; }
                .warehouse-row label:nth-child(3) { grid-column: 1 / 3; }
            }
            @media (max-width: 700px) {
                .company-directory-heading { align-items: stretch; flex-direction: column; }
                .company-directory-heading .button { align-self: flex-start; }
                .company-directory-filters { grid-template-columns: 1fr 1fr; }
                .company-directory-search { grid-column: 1 / -1; }
                .company-create-grid { grid-template-columns: 1fr; }
                .company-create-grid .company-create-wide { grid-column: auto; }
                .company-create-actions { flex-wrap: wrap; }
                .company-create-summary { flex-basis: 100%; }
            }
            @media (max-width: 470px) {
                .company-directory-filters { grid-template-columns: 1fr; }
                .company-directory-search { grid-column: auto; }
                .warehouse-row { grid-template-columns: minmax(0, 1fr) 30px; }
                .warehouse-row label { grid-column: 1; }
                .warehouse-row label:nth-child(3) { grid-column: 1; }
                .warehouse-row .company-warehouse-remove { grid-column: 2; grid-row: 1; }
                .company-branch-summary { display: grid; gap: 0; }
                .company-branch-primary span { display: none; }
                .company-directory-footer { flex-wrap: wrap; }
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const createDialog = document.getElementById('company-create-dialog');
                const openCreate = () => {
                    if (!createDialog.open) createDialog.showModal();
                };
                document.querySelector('[data-company-create-open]')?.addEventListener('click', openCreate);
                createDialog.querySelectorAll('[data-company-create-close]').forEach((button) => {
                    button.addEventListener('click', () => createDialog.close());
                });
                createDialog.addEventListener('click', (event) => {
                    if (event.target === createDialog) createDialog.close();
                });
                if (createDialog.hasAttribute('data-auto-open')) openCreate();

                const branchDialog = document.getElementById('company-branch-dialog');
                const branchForm = branchDialog?.querySelector('[data-company-branch-form]');
                const branchButtons = [...document.querySelectorAll('[data-company-branch-open]')];
                const openBranch = (button, preserveValues = false) => {
                    if (!branchDialog || !branchForm) return;
                    branchForm.action = button.dataset.companyBranchAction;
                    branchForm.querySelector('[data-company-branch-company-id]').value = button.dataset.companyBranchId;
                    branchDialog.querySelector('[data-company-branch-company-name]').textContent = button.dataset.companyBranchName;
                    if (!preserveValues) {
                        branchForm.querySelectorAll('[name^="branch["]').forEach((field) => { field.value = ''; });
                        branchForm.querySelector('[data-company-branch-address]').value = button.dataset.companyBranchAddress;
                        branchDialog.querySelector('[data-company-branch-errors]')?.setAttribute('hidden', '');
                    }
                    if (!branchDialog.open) branchDialog.showModal();
                };
                branchButtons.forEach((button) => button.addEventListener('click', () => openBranch(button)));
                branchDialog?.querySelectorAll('[data-company-branch-close]').forEach((button) => {
                    button.addEventListener('click', () => branchDialog.close());
                });
                branchDialog?.addEventListener('click', (event) => {
                    if (event.target === branchDialog) branchDialog.close();
                });
                if (branchDialog?.hasAttribute('data-auto-open')) {
                    const source = branchButtons.find((button) => button.dataset.companyBranchId === branchForm.querySelector('[data-company-branch-company-id]').value);
                    if (source) openBranch(source, true);
                }

                const list = document.getElementById('warehouses-list');
                const branchList = createDialog.querySelector('[data-branch-list]');
                const companyAddress = document.getElementById('company-address');
                const refreshSummary = () => {
                    const branches = branchList?.querySelectorAll('[data-branch-row]').length || 0;
                    const warehouses = list.querySelectorAll('.warehouse-row').length;
                    createDialog.querySelector('[data-company-create-summary]').textContent =
                        `Se guardarán 1 empresa, ${branches} ${branches === 1 ? 'sucursal' : 'sucursales'} y ${warehouses} ${warehouses === 1 ? 'almacén' : 'almacenes'}.`;
                };
                const refreshBranchOptions = () => {
                    const branches = [...(branchList?.querySelectorAll('[data-branch-row]') || [])];
                    createDialog.querySelectorAll('[data-warehouse-branch]').forEach((select) => {
                        const selected = select.dataset.selectedBranch ?? select.value;
                        select.replaceChildren(new Option('Sin vincular', ''));
                        branches.forEach((row) => {
                            const name = row.querySelector('[data-branch-name]').value.trim() || 'Nueva sucursal';
                            select.add(new Option(name, row.dataset.branchIndex));
                        });
                        select.value = branches.some((row) => row.dataset.branchIndex === selected) ? selected : '';
                        delete select.dataset.selectedBranch;
                    });
                    refreshSummary();
                };
                const updateBranchSummary = (row) => {
                    row.querySelector('[data-branch-title]').textContent = row.querySelector('[data-branch-name]').value.trim() || 'Nueva sucursal';
                    row.querySelector('[data-branch-code]').textContent = row.querySelector('[data-branch-code-input]').value.trim();
                    refreshBranchOptions();
                };
                branchList?.addEventListener('click', (event) => {
                    const row = event.target.closest('[data-branch-row]');
                    if (!row) return;
                    if (event.target.closest('[data-branch-remove]')) {
                        const wasPrimary = row.querySelector('[data-branch-primary], input[name="primary_branch"]')?.checked;
                        row.remove();
                        if (wasPrimary) branchList.querySelector('input[name="primary_branch"]')?.click();
                        refreshBranchOptions();
                    } else if (event.target.closest('[data-branch-toggle]')) {
                        const fields = row.querySelector('[data-branch-fields]');
                        fields.hidden = !fields.hidden;
                        const button = row.querySelector('[data-branch-toggle]');
                        button.setAttribute('aria-expanded', String(!fields.hidden));
                        button.setAttribute('aria-label', fields.hidden ? 'Expandir sucursal' : 'Contraer sucursal');
                    }
                });
                branchList?.addEventListener('input', (event) => {
                    const row = event.target.closest('[data-branch-row]');
                    if (!row) return;
                    if (event.target.matches('[data-branch-name], [data-branch-code-input]')) updateBranchSummary(row);
                    if (event.target.matches('[data-branch-address]')) row.querySelector('[data-branch-use-address]').checked = false;
                });
                branchList?.addEventListener('change', (event) => {
                    if (!event.target.matches('[data-branch-use-address]')) return;
                    const address = event.target.closest('[data-branch-row]').querySelector('[data-branch-address]');
                    if (event.target.checked) address.value = companyAddress.value;
                    address.readOnly = event.target.checked;
                });
                document.querySelector('[data-branch-add]')?.addEventListener('click', () => {
                    if (branchList.querySelectorAll('[data-branch-row]').length >= 24) return;
                    const index = Number(branchList.dataset.nextIndex || 0);
                    const row = document.getElementById('company-branch-template').content.firstElementChild.cloneNode(true);
                    row.dataset.branchIndex = String(index);
                    row.querySelectorAll('[data-branch-field]').forEach((input) => {
                        input.name = `branches[${index}][${input.dataset.branchField}]`;
                    });
                    const primary = row.querySelector('[data-branch-primary]');
                    primary.value = String(index);
                    if (!branchList.querySelector('input[name="primary_branch"]:checked')) primary.checked = true;
                    const fields = row.querySelector('[data-branch-fields]');
                    fields.id = `company-branch-fields-${index}`;
                    row.querySelector('[data-branch-toggle]').setAttribute('aria-controls', fields.id);
                    branchList.appendChild(row);
                    branchList.dataset.nextIndex = String(index + 1);
                    refreshBranchOptions();
                    row.querySelector('[data-branch-name]').focus();
                });
                createDialog.querySelector('[data-branch-list]')?.querySelectorAll('[data-branch-row]').forEach((row) => {
                    const useAddress = row.querySelector('[data-branch-use-address]');
                    row.querySelector('[data-branch-address]').readOnly = useAddress.checked;
                });
                createDialog.querySelector('form').addEventListener('invalid', (event) => {
                    const row = event.target.closest('[data-branch-row]');
                    if (!row) return;
                    row.querySelector('[data-branch-fields]').hidden = false;
                    row.querySelector('[data-branch-toggle]').setAttribute('aria-expanded', 'true');
                }, true);
                const refreshWarehouseButtons = () => {
                    const rows = list.querySelectorAll('.warehouse-row');
                    rows.forEach((row) => {
                        row.querySelector('[data-warehouse-remove]').disabled = rows.length === 1;
                    });
                    refreshSummary();
                };
                document.querySelector('[data-warehouse-add]')?.addEventListener('click', () => {
                    const index = Number(list.dataset.nextIndex || 0);
                    const row = document.getElementById('warehouse-row-template').content.firstElementChild.cloneNode(true);
                    row.querySelectorAll('[data-warehouse-field]').forEach((input) => {
                        input.name = `warehouses[${index}][${input.dataset.warehouseField}]`;
                    });
                    row.querySelector('[data-warehouse-address]').value = companyAddress.value;
                    list.appendChild(row);
                    list.dataset.nextIndex = String(index + 1);
                    refreshBranchOptions();
                    refreshWarehouseButtons();
                    row.querySelector('input').focus();
                });
                list.addEventListener('click', (event) => {
                    if (!event.target.matches('[data-warehouse-remove]')) return;
                    if (list.querySelectorAll('.warehouse-row').length <= 1) return;
                    event.target.closest('.warehouse-row').remove();
                    refreshWarehouseButtons();
                });
                list.addEventListener('input', (event) => {
                    if (event.target.matches('[data-warehouse-address]')) event.target.dataset.followsCompanyAddress = 'false';
                });
                companyAddress?.addEventListener('input', (event) => {
                    list.querySelectorAll('[data-warehouse-address][data-follows-company-address="true"]')
                        .forEach((input) => input.value = event.target.value);
                    branchList?.querySelectorAll('[data-branch-row]').forEach((row) => {
                        if (row.querySelector('[data-branch-use-address]').checked) {
                            row.querySelector('[data-branch-address]').value = event.target.value;
                        }
                    });
                });
                refreshWarehouseButtons();
                refreshBranchOptions();

                document.querySelectorAll('.company-directory-row').forEach((row) => {
                    row.addEventListener('click', (event) => {
                        if (event.target.closest('.company-directory-actions')) return;
                        const button = row.querySelector('[data-company-expand]');
                        const detail = document.getElementById(button.getAttribute('aria-controls'));
                        detail.hidden = !detail.hidden;
                        button.setAttribute('aria-expanded', String(!detail.hidden));
                        button.setAttribute('aria-label', `${detail.hidden ? 'Mostrar' : 'Ocultar'} sucursales y almacenes de ${button.dataset.companyName}`);
                    });
                });
                document.querySelectorAll('[data-company-filter-select]').forEach((select) => {
                    select.addEventListener('change', () => select.form.requestSubmit());
                });
                document.querySelectorAll('[data-dialog-target]').forEach((button) => {
                    button.addEventListener('click', () => {
                        document.getElementById(button.dataset.dialogTarget)?.showModal();
                    });
                });
                document.querySelectorAll('[data-dialog-close]').forEach((button) => {
                    button.addEventListener('click', () => button.closest('dialog')?.close());
                });
                document.querySelector('[id^="edit-warehouse-"][data-auto-open]')?.showModal();
            });
        </script>
    </x-app-shell>
@endsection
