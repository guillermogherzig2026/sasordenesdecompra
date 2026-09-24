@extends('layouts.app')

@section('body')
    <x-app-shell title="Proveedores">
        <div class="provider-directory">
            <header class="provider-directory-heading">
                <div>
                    <h2>Catálogo de proveedores</h2>
                    <p>Consulta y administra la información de tus proveedores.</p>
                </div>
                <button class="button primary" type="button" data-provider-create-open><span aria-hidden="true">+</span> Alta de proveedor</button>
            </header>

            <x-filter-carousel label="Categorías de proveedores"
                :items="$lineOptions->map(fn ($line) => ['value' => $line, 'label' => $line])->all()"
                :selected="$businessLineFilter" route-name="finance.admin.providers" parameter="line" />

            <section class="provider-filter-panel" aria-label="Filtros de proveedores">
                <form class="provider-filters" method="GET" action="{{ route('finance.admin.providers') }}">
                    <label class="provider-filter-search">Buscar
                        <input type="search" name="q" value="{{ $query }}" placeholder="Razón social, RFC o contacto" autocomplete="off">
                    </label>
                    <label>Comprador
                        <select name="buyer">
                            <option value="">Todos</option>
                            @foreach ($filterBuyers as $filterBuyer)
                                <option value="{{ $filterBuyer->id }}" @selected($buyerId === $filterBuyer->id)>{{ $filterBuyer->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Giro de proveeduría
                        <select name="line">
                            <option value="">Todos</option>
                            @foreach ($lineOptions as $lineOption)
                                <option value="{{ $lineOption }}" @selected($businessLineFilter === $lineOption)>{{ $lineOption }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Subcategoría
                        <select name="subcategory">
                            <option value="">Todas</option>
                            @foreach ($subcategoryOptions as $subcategoryOption)
                                <option value="{{ $subcategoryOption }}" @selected($subcategoryFilter === $subcategoryOption)>{{ $subcategoryOption }}</option>
                            @endforeach
                        </select>
                    </label>
                    <div class="provider-filter-actions">
                        <button class="button ghost" type="submit">Buscar</button>
                        <a class="button ghost" href="{{ route('finance.admin.providers') }}">Limpiar</a>
                    </div>
                </form>
                <details class="provider-export-menu">
                    <summary class="button ghost">Exportar</summary>
                    <div class="provider-export-options">
                        <a href="{{ route('reports.download', 'providers-excel') }}">Excel</a>
                        <a href="{{ route('reports.download', 'providers') }}">CSV</a>
                    </div>
                </details>
            </section>

            <section class="provider-catalog" aria-label="Catálogo de proveedores">
                <div class="provider-catalog-head" aria-hidden="true">
                    <span></span>
                    <span>Proveedor</span>
                    <span class="provider-col-buyer">Comprador</span>
                    <span class="provider-col-line">Giro y subcategoría</span>
                    <span class="provider-col-contact">Contacto y teléfono</span>
                    <span class="provider-col-bank">Banco</span>
                    <span class="provider-col-date">Fecha de alta</span>
                    <span>Acciones</span>
                </div>

                @forelse ($providers as $provider)
                    @php
                        $detailId = 'provider-detail-'.$provider->id;
                        $subcategoryName = $provider->businessSubcategory?->name ?? $provider->provider_business_subcategory;
                    @endphp
                    <article class="provider-entry" data-provider-entry>
                        <div class="provider-row" data-provider-summary>
                            <button class="provider-expand" type="button" aria-label="Ver detalle de {{ $provider->business_name }}" aria-controls="{{ $detailId }}" aria-expanded="false" data-provider-toggle><span aria-hidden="true">›</span></button>
                            <div class="provider-cell provider-cell-name">
                                <strong>{{ $provider->business_name }}</strong>
                                <small>RFC: {{ $provider->rfc ?: 'Sin registrar' }}</small>
                            </div>
                            <div class="provider-cell provider-col-buyer">{{ $provider->buyer?->name ?? 'Sin comprador' }}</div>
                            <div class="provider-cell provider-col-line">
                                <span>{{ $provider->business_line ?: 'Sin giro' }}</span>
                                <small>{{ $subcategoryName ?: 'Sin subcategoría' }}</small>
                            </div>
                            <div class="provider-cell provider-col-contact">
                                <span>{{ $provider->contact_name ?: 'Sin registrar' }}</span>
                                <small>{{ $provider->phone ?: 'Sin teléfono' }}</small>
                            </div>
                            <div class="provider-cell provider-col-bank">
                                <span>{{ $provider->bank ?: 'Sin registrar' }}</span>
                                <small>Cuenta: {{ $provider->account_number ?: 'Sin registrar' }}</small>
                            </div>
                            <div class="provider-cell provider-col-date">{{ $provider->created_at?->format('d/m/Y') ?? 'Sin fecha' }}</div>
                            <div class="provider-cell provider-cell-actions">
                                <button class="button ghost small" type="button" aria-controls="{{ $detailId }}" aria-expanded="false" data-provider-toggle data-provider-detail-label>Ver detalle</button>
                                <a class="button ghost small" href="{{ route('finance.admin.providers.edit', $provider) }}" aria-controls="provider-edit-{{ $provider->id }}" data-provider-edit-open>Editar</a>
                            </div>
                        </div>

                        <div class="provider-details" id="{{ $detailId }}" role="region" aria-label="Información de {{ $provider->business_name }}" hidden>
                            <section class="provider-detail-section">
                                <h3>Datos generales</h3>
                                <dl>
                                    <div><dt>Razón social</dt><dd>{{ $provider->business_name }}</dd></div>
                                    <div><dt>RFC</dt><dd>{{ $provider->rfc ?: 'Sin registrar' }}</dd></div>
                                    <div><dt>Comprador</dt><dd>{{ $provider->buyer?->name ?? 'Sin comprador' }}</dd></div>
                                    <div><dt>Giro de proveeduría</dt><dd>{{ $provider->business_line ?: 'Sin registrar' }}</dd></div>
                                    <div><dt>Subcategoría</dt><dd>{{ $subcategoryName ?: 'Sin subcategoría' }}</dd></div>
                                    <div><dt>Fecha de alta</dt><dd>{{ $provider->created_at?->format('d/m/Y') ?? 'Sin fecha' }}</dd></div>
                                </dl>
                            </section>
                            <section class="provider-detail-section">
                                <h3>Contacto y ubicación</h3>
                                <dl>
                                    <div><dt>Contacto</dt><dd>{{ $provider->contact_name ?: 'Sin registrar' }}</dd></div>
                                    <div><dt>Teléfono</dt><dd>{{ $provider->phone ?: 'Sin registrar' }}</dd></div>
                                    <div class="provider-detail-wide"><dt>Dirección</dt><dd>{{ $provider->address ?: 'Sin registrar' }}</dd></div>
                                </dl>
                            </section>
                            <section class="provider-detail-section">
                                <h3>Información bancaria</h3>
                                <dl>
                                    <div><dt>Banco</dt><dd>{{ $provider->bank ?: 'Sin registrar' }}</dd></div>
                                    <div><dt>Cuenta</dt><dd>{{ $provider->account_number ?: 'Sin registrar' }}</dd></div>
                                    <div><dt>CLABE</dt><dd>{{ $provider->clabe ?: 'Sin registrar' }}</dd></div>
                                    <div><dt>Referencia</dt><dd>{{ $provider->reference ?: 'Sin referencia' }}</dd></div>
                                </dl>
                                <a class="button primary small provider-detail-edit" href="{{ route('finance.admin.providers.edit', $provider) }}" aria-controls="provider-edit-{{ $provider->id }}" data-provider-edit-open>Editar proveedor</a>
                            </section>
                        </div>

                        @php
                            $editingFailed = (int) old('_provider_edit_id') === $provider->id && $errors->any();
                            $editValue = fn ($field, $value) => $editingFailed ? old($field, $value) : $value;
                        @endphp
                        <dialog id="provider-edit-{{ $provider->id }}" class="provider-create-dialog provider-edit-dialog" aria-labelledby="provider-edit-title-{{ $provider->id }}" data-provider-edit-dialog @if ($editingFailed) data-auto-open @endif>
                            <form class="provider-create-form" method="POST" action="{{ route('finance.admin.providers.update', $provider) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="_provider_edit_id" value="{{ $provider->id }}">
                                <header class="provider-create-header">
                                    <div>
                                        <h2 id="provider-edit-title-{{ $provider->id }}">Editar proveedor</h2>
                                        <p>Proveedor dado de alta por {{ $provider->buyer?->name ?? 'Sin comprador' }}.</p>
                                    </div>
                                    <button class="provider-create-close" type="button" aria-label="Cerrar" data-provider-edit-close>&times;</button>
                                </header>
                                <div class="provider-create-body">
                                    @if ($editingFailed)
                                        <div class="provider-create-errors" role="alert">
                                            <strong>Revisa los siguientes datos:</strong>
                                            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                                        </div>
                                    @endif
                                    <div class="provider-form-grid">
                                        <label>Razón social<input name="business_name" value="{{ $editValue('business_name', $provider->business_name) }}" required></label>
                                        <label>RFC<input name="rfc" value="{{ $editValue('rfc', $provider->rfc) }}" required></label>
                                        <label>Giro de proveeduría
                                            <select name="business_line_id" data-provider-line-select required>
                                                @foreach ($businessLines as $line)
                                                    <option value="{{ $line->id }}" @selected((int) $editValue('business_line_id', $provider->provider_business_line_id) === $line->id || (! $editingFailed && ! $provider->provider_business_line_id && $provider->business_line === $line->name))>{{ $line->name }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                        <label>Subcategoría
                                            <select name="business_subcategory_id" data-provider-subcategory-select>
                                                <option value="">Sin subcategoría</option>
                                                @foreach ($businessLines as $line)
                                                    @foreach ($line->subcategories as $subcategory)
                                                        <option value="{{ $subcategory->id }}" data-line-id="{{ $line->id }}" @selected((int) $editValue('business_subcategory_id', $provider->provider_business_subcategory_id) === $subcategory->id)>{{ $subcategory->name }}</option>
                                                    @endforeach
                                                @endforeach
                                            </select>
                                        </label>
                                        <label>Contacto<input name="contact_name" value="{{ $editValue('contact_name', $provider->contact_name) }}"></label>
                                        <label>Teléfono<input name="phone" value="{{ $editValue('phone', $provider->phone) }}" inputmode="tel"></label>
                                        <label class="provider-form-wide">Dirección<input name="address" value="{{ $editValue('address', $provider->address) }}"></label>
                                        <label>Banco<input name="bank" value="{{ $editValue('bank', $provider->bank) }}" required></label>
                                        <label>Cuenta<input name="account_number" value="{{ $editValue('account_number', $provider->account_number) }}" required></label>
                                        <label>CLABE<input name="clabe" value="{{ $editValue('clabe', $provider->clabe) }}" inputmode="numeric" maxlength="18" required></label>
                                        <label class="provider-form-wide">Referencia<input name="reference" value="{{ $editValue('reference', $provider->reference) }}" placeholder="Referencia bancaria o línea de captura"></label>
                                    </div>
                                </div>
                                <footer class="provider-create-footer">
                                    <span>Los cambios se reflejarán en OC vigentes e historial.</span>
                                    <div>
                                        <button class="button ghost" type="button" data-provider-edit-close>Cancelar</button>
                                        <button class="button primary" type="submit">Guardar cambios</button>
                                    </div>
                                </footer>
                            </form>
                        </dialog>
                    </article>
                @empty
                    <div class="provider-empty">No hay proveedores que coincidan con los filtros.</div>
                @endforelse

                <footer class="provider-catalog-footer">
                    <span>Mostrando {{ $providers->firstItem() ?? 0 }}–{{ $providers->lastItem() ?? 0 }} de {{ $providers->total() }} proveedores</span>
                    @if ($providers->hasPages())
                        <nav class="provider-pagination" aria-label="Páginas de proveedores">
                            @if ($providers->previousPageUrl())
                                <a class="button ghost small" href="{{ $providers->previousPageUrl() }}">Anterior</a>
                            @else
                                <span class="button ghost small" aria-disabled="true">Anterior</span>
                            @endif
                            <span>{{ $providers->currentPage() }} de {{ $providers->lastPage() }}</span>
                            @if ($providers->nextPageUrl())
                                <a class="button ghost small" href="{{ $providers->nextPageUrl() }}">Siguiente</a>
                            @else
                                <span class="button ghost small" aria-disabled="true">Siguiente</span>
                            @endif
                        </nav>
                    @endif
                </footer>
            </section>
        </div>

        <dialog id="provider-create-dialog" class="provider-create-dialog" aria-labelledby="provider-create-title" data-provider-create-dialog @if ((old('_provider_form') === 'create' && $errors->any()) || request()->boolean('create')) data-auto-open @endif>
            <form class="provider-create-form" method="POST" action="{{ route('finance.admin.providers.store') }}">
                @csrf
                <input type="hidden" name="_provider_form" value="create">
                <header class="provider-create-header">
                    <div>
                        <h2 id="provider-create-title">Nuevo proveedor</h2>
                        <p>Los proveedores registrados estarán disponibles para las órdenes de compra del comprador asignado.</p>
                    </div>
                    <button class="provider-create-close" type="button" aria-label="Cerrar" data-provider-create-close>&times;</button>
                </header>
                <div class="provider-create-body">
                    @if (old('_provider_form') === 'create' && $errors->any())
                        <div class="provider-create-errors" role="alert">
                            <strong>Revisa los siguientes datos:</strong>
                            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif
                    <div class="provider-form-grid">
                        <label>Comprador
                            <select name="buyer_id" required>
                                <option value="">Seleccionar comprador...</option>
                                @foreach ($buyers as $buyer)
                                    <option value="{{ $buyer->id }}" @selected((int) old('buyer_id') === $buyer->id)>{{ $buyer->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>Razón social<input name="business_name" value="{{ old('business_name') }}" required></label>
                        <label>RFC<input name="rfc" value="{{ old('rfc') }}" required></label>
                        <label>Giro de proveeduría
                            <select name="business_line_id" data-provider-line-select required>
                                <option value="">Seleccionar giro...</option>
                                @foreach ($businessLines as $line)
                                    <option value="{{ $line->id }}" @selected((int) old('business_line_id') === $line->id)>{{ $line->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>Subcategoría
                            <select name="business_subcategory_id" data-provider-subcategory-select>
                                <option value="">Sin subcategoría</option>
                                @foreach ($businessLines as $line)
                                    @foreach ($line->subcategories as $subcategory)
                                        <option value="{{ $subcategory->id }}" data-line-id="{{ $line->id }}" @selected((int) old('business_subcategory_id') === $subcategory->id)>{{ $subcategory->name }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </label>
                        <label>Banco<input name="bank" value="{{ old('bank') }}" required></label>
                        <label>Cuenta<input name="account_number" value="{{ old('account_number') }}" required></label>
                        <label>CLABE<input name="clabe" value="{{ old('clabe') }}" inputmode="numeric" maxlength="18" required></label>
                        <label>Contacto<input name="contact_name" value="{{ old('contact_name') }}"></label>
                        <label>Teléfono<input name="phone" value="{{ old('phone') }}" inputmode="tel"></label>
                        <label class="provider-form-wide">Dirección<input name="address" value="{{ old('address') }}"></label>
                        <label class="provider-form-wide">Referencia<input name="reference" value="{{ old('reference') }}" placeholder="Referencia bancaria o línea de captura"></label>
                    </div>
                </div>
                <footer class="provider-create-footer">
                    <span>La CLABE debe tener 18 dígitos.</span>
                    <div>
                        <button class="button ghost" type="button" data-provider-create-close>Cancelar</button>
                        <button class="button primary" type="submit">Guardar proveedor</button>
                    </div>
                </footer>
            </form>
        </dialog>

        <style>
            .provider-directory { min-width: 0; display: grid; gap: 16px; align-content: start; }
            .provider-directory-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; }
            .provider-directory-heading h2 { margin: 0; font-size: 1.25rem; }
            .provider-directory-heading p { margin: 5px 0 0; color: var(--muted); }
            .provider-directory-heading > .button { flex: 0 0 auto; }
            .provider-filter-panel { display: grid; grid-template-columns: minmax(0, 1fr) auto; align-items: end; gap: 12px; padding: 16px; border: 1px solid var(--line); border-radius: 8px; background: #fff; }
            .provider-filters { min-width: 0; display: grid; grid-template-columns: minmax(190px, 1.8fr) repeat(3, minmax(125px, 1fr)) auto; gap: 10px; align-items: end; }
            .provider-filters label { min-width: 0; font-size: .82rem; }
            .provider-filters :is(input, select) { width: 100%; min-width: 0; min-height: 40px; }
            .provider-filter-actions { display: flex; gap: 6px; }
            .provider-filter-actions .button { min-height: 40px; white-space: nowrap; }
            .provider-export-menu { position: relative; }
            .provider-export-menu summary { list-style: none; white-space: nowrap; }
            .provider-export-menu summary::-webkit-details-marker { display: none; }
            .provider-export-menu summary::after { content: '⌄'; font-size: 1rem; }
            .provider-export-options { position: absolute; z-index: 5; top: calc(100% + 4px); right: 0; min-width: 145px; padding: 5px; display: grid; border: 1px solid var(--line); border-radius: 6px; background: #fff; box-shadow: 0 12px 28px rgba(14, 39, 56, .14); }
            .provider-export-options a { padding: 9px 11px; border-radius: 4px; text-decoration: none; }
            .provider-export-options a:hover { background: #eaf4f8; }
            .provider-catalog { min-width: 0; border: 1px solid var(--line); border-radius: 8px; background: #fff; overflow: hidden; }
            .provider-catalog-head, .provider-row { display: grid; grid-template-columns: 34px minmax(170px, 1.4fr) minmax(130px, 1fr) minmax(150px, 1fr) minmax(140px, 1fr) minmax(130px, .9fr) 95px minmax(150px, auto); align-items: center; gap: 10px; padding: 10px 14px; }
            .provider-catalog-head { color: #4a5a70; background: #f6f9fd; border-bottom: 1px solid var(--line); font-size: .75rem; font-weight: 800; text-transform: uppercase; }
            .provider-entry { border-bottom: 1px solid var(--line); }
            .provider-row { min-height: 74px; cursor: pointer; }
            .provider-row:hover, .provider-entry.is-expanded > .provider-row { background: #f0f7fb; }
            .provider-cell { min-width: 0; overflow-wrap: anywhere; font-size: .88rem; }
            .provider-cell :is(strong, span, small) { display: block; }
            .provider-cell-name strong { font-size: .94rem; }
            .provider-cell small { margin-top: 3px; color: var(--muted); font-size: .78rem; }
            .provider-expand { width: 30px; height: 30px; padding: 0; border: 0; border-radius: 50%; background: #e7f1f8; color: var(--primary-strong); font-size: 1.5rem; line-height: 1; cursor: pointer; }
            .provider-entry.is-expanded .provider-expand span { display: inline-block; transform: rotate(90deg); }
            .provider-cell-actions { display: flex; align-items: center; gap: 5px; flex-wrap: wrap; }
            .provider-cell-actions .button { white-space: nowrap; }
            .provider-details { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0; padding: 18px 22px; background: #f8fbfe; border-top: 1px solid var(--line); }
            .provider-detail-section { min-width: 0; padding: 0 20px; }
            .provider-detail-section:first-child { padding-left: 0; }
            .provider-detail-section + .provider-detail-section { border-left: 1px solid var(--line); }
            .provider-detail-section h3 { margin: 0 0 14px; color: var(--primary-strong); font-size: .9rem; }
            .provider-detail-section dl { margin: 0; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
            .provider-detail-section dt { color: #42536d; font-size: .78rem; font-weight: 800; }
            .provider-detail-section dd { margin: 4px 0 0; overflow-wrap: anywhere; font-size: .87rem; }
            .provider-detail-wide { grid-column: 1 / -1; }
            .provider-detail-edit { margin-top: 18px; }
            .provider-empty { padding: 40px 16px; text-align: center; color: var(--muted); }
            .provider-catalog-footer { min-height: 56px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; gap: 12px; color: var(--muted); font-size: .84rem; }
            .provider-pagination { display: flex; align-items: center; gap: 8px; }
            .provider-pagination [aria-disabled="true"] { opacity: .45; }
            .provider-create-dialog { width: min(950px, calc(100vw - 24px)); max-width: none; max-height: calc(100dvh - 24px); padding: 0; border: 1px solid var(--line); border-radius: 8px; background: #fff; color: var(--text); box-shadow: 0 22px 60px rgba(14, 39, 56, .25); }
            .provider-create-dialog::backdrop { background: rgba(14, 39, 56, .58); }
            .provider-create-form { max-height: calc(100dvh - 24px); display: flex; flex-direction: column; }
            .provider-create-header { padding: 18px 22px; display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; border-bottom: 1px solid var(--line); }
            .provider-create-header h2 { margin: 0; font-size: 1.18rem; }
            .provider-create-header p { margin: 5px 0 0; color: var(--muted); font-size: .84rem; }
            .provider-create-close { width: 32px; height: 32px; flex: 0 0 auto; border: 1px solid var(--line); border-radius: 5px; background: #fff; cursor: pointer; font-size: 1.3rem; }
            .provider-create-body { min-height: 0; overflow-y: auto; padding: 20px 22px; }
            .provider-create-errors { margin-bottom: 16px; padding: 12px; border: 1px solid #e5b5b5; border-radius: 5px; background: #fff3f3; color: #8c2929; }
            .provider-create-errors ul { margin: 6px 0 0; padding-left: 18px; }
            .provider-form-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
            .provider-form-grid label { min-width: 0; }
            .provider-form-grid :is(input, select) { width: 100%; min-width: 0; }
            .provider-form-wide { grid-column: span 2; }
            .provider-create-footer { padding: 12px 22px; display: flex; align-items: center; justify-content: space-between; gap: 12px; border-top: 1px solid var(--line); }
            .provider-create-footer > span { color: var(--muted); font-size: .8rem; }
            .provider-create-footer > div { display: flex; gap: 8px; }
            @media (max-width: 1450px) {
                .provider-catalog-head, .provider-row { grid-template-columns: 34px minmax(170px, 1.4fr) minmax(145px, 1fr) minmax(140px, 1fr) minmax(150px, auto); }
                .provider-col-buyer, .provider-col-bank, .provider-col-date { display: none; }
            }
            @media (max-width: 1200px) {
                .provider-filter-panel { grid-template-columns: 1fr; }
                .provider-export-menu { justify-self: end; }
            }
            @media (max-width: 1050px) {
                .provider-filters { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                .provider-filter-actions { align-self: end; }
                .provider-details { grid-template-columns: 1fr; gap: 18px; }
                .provider-detail-section { padding: 0; }
                .provider-detail-section + .provider-detail-section { border-left: 0; border-top: 1px solid var(--line); padding-top: 18px; }
            }
            @media (max-width: 700px) {
                .provider-directory-heading { flex-direction: column; }
                .provider-catalog-head, .provider-row { grid-template-columns: 30px minmax(0, 1fr) auto; gap: 8px; padding: 10px; }
                .provider-col-line, .provider-col-contact { display: none; }
                .provider-cell-actions [data-provider-detail-label] { display: none; }
                .provider-details { padding: 16px; }
                .provider-create-dialog { width: calc(100vw - 12px); max-height: calc(100dvh - 12px); }
                .provider-create-form { max-height: calc(100dvh - 12px); }
                .provider-form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            }
            @media (max-width: 520px) {
                .provider-filters, .provider-form-grid { grid-template-columns: 1fr; }
                .provider-form-wide { grid-column: auto; }
                .provider-filter-actions { flex-wrap: wrap; }
                .provider-catalog-footer { flex-direction: column; align-items: flex-start; }
                .provider-create-header, .provider-create-body, .provider-create-footer { padding-left: 14px; padding-right: 14px; }
                .provider-create-footer { align-items: stretch; flex-direction: column; }
                .provider-create-footer > div { justify-content: flex-end; }
            }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-provider-entry]').forEach((entry) => {
                    const details = entry.querySelector('.provider-details');
                    const buttons = entry.querySelectorAll('[data-provider-toggle]');
                    const setExpanded = (expanded) => {
                        details.hidden = !expanded;
                        entry.classList.toggle('is-expanded', expanded);
                        buttons.forEach((button) => button.setAttribute('aria-expanded', String(expanded)));
                        const label = entry.querySelector('[data-provider-detail-label]');
                        if (label) label.textContent = expanded ? 'Ocultar detalle' : 'Ver detalle';
                    };
                    buttons.forEach((button) => button.addEventListener('click', () => setExpanded(details.hidden)));
                    entry.querySelector('[data-provider-summary]').addEventListener('click', (event) => {
                        if (event.target.closest('button, a')) return;
                        setExpanded(details.hidden);
                    });
                });

                const dialog = document.querySelector('[data-provider-create-dialog]');
                const openDialog = () => {
                    if (typeof dialog.showModal === 'function') dialog.showModal();
                    else dialog.setAttribute('open', '');
                    dialog.querySelector('[name="buyer_id"]')?.focus({ preventScroll: true });
                };
                document.querySelector('[data-provider-create-open]')?.addEventListener('click', openDialog);
                dialog.querySelectorAll('[data-provider-create-close]').forEach((button) => {
                    button.addEventListener('click', () => dialog.close());
                });
                dialog.addEventListener('click', (event) => {
                    if (event.target === dialog) dialog.close();
                });
                dialog.addEventListener('close', () => {
                    const url = new URL(window.location.href);
                    if (!url.searchParams.has('create')) return;
                    url.searchParams.delete('create');
                    window.history.replaceState(null, '', url);
                });
                if (dialog.hasAttribute('data-auto-open')) openDialog();

                document.querySelectorAll('[data-provider-edit-dialog]').forEach((editDialog) => {
                    const openEditDialog = () => {
                        if (typeof editDialog.showModal === 'function') editDialog.showModal();
                        else editDialog.setAttribute('open', '');
                        editDialog.querySelector('[name="business_name"]')?.focus({ preventScroll: true });
                    };
                    editDialog.closest('[data-provider-entry]').querySelectorAll('[data-provider-edit-open]').forEach((link) => {
                        link.addEventListener('click', (event) => {
                            event.preventDefault();
                            openEditDialog();
                        });
                    });
                    editDialog.querySelectorAll('[data-provider-edit-close]').forEach((button) => {
                        button.addEventListener('click', () => editDialog.close());
                    });
                    editDialog.addEventListener('click', (event) => {
                        if (event.target === editDialog) editDialog.close();
                    });
                    if (editDialog.hasAttribute('data-auto-open')) openEditDialog();
                });
            });
        </script>
    </x-app-shell>
@endsection
