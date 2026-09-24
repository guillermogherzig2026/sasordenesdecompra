@extends('layouts.app')

@section('body')
    @php
        $categoryStyles = [
            'Telefonia Fija' => ['label' => 'Telefonía fija', 'symbol' => '☎', 'tone' => 'blue'],
            'Telefonia Celular' => ['label' => 'Telefonía celular', 'symbol' => '▣', 'tone' => 'blue'],
            'Internet' => ['label' => 'Internet', 'symbol' => '⌁', 'tone' => 'blue'],
            'Predial' => ['label' => 'Predial', 'symbol' => '⌂', 'tone' => 'indigo'],
            'Mantenimiento' => ['label' => 'Mantenimiento correctivo y preventivo', 'symbol' => '⚙', 'tone' => 'slate', 'wide' => true],
            'Cuota de Mantenimiento de Condominio' => ['label' => 'Cuota de Mantenimiento de Condominio', 'symbol' => '▥', 'tone' => 'indigo', 'wide' => true],
            'Agua' => ['label' => 'Agua', 'symbol' => '◉', 'tone' => 'cyan'],
            'Luz' => ['label' => 'Electricidad', 'symbol' => 'ϟ', 'tone' => 'amber'],
            'Recoleccion de basura' => ['label' => 'Recolección de basura', 'symbol' => '♻', 'tone' => 'green'],
            'Tenencia' => ['label' => 'Tenencia', 'symbol' => '▤', 'tone' => 'rose'],
            'Renta' => ['label' => 'Renta', 'symbol' => '⌂', 'tone' => 'green'],
            'Otros' => ['label' => 'Otros', 'symbol' => '•••', 'tone' => 'slate'],
        ];
        $periodNames = [30 => 'Mensual', 60 => 'Bimestral', 90 => 'Trimestral', 180 => 'Semestral', 365 => 'Anual'];
    @endphp

    <x-app-shell title="Servicios">
        <div class="service-directory">
            <header class="service-directory-heading">
                <h2>Servicios recurrentes</h2>
                <div class="service-directory-heading-actions">
                    @if ($canCreateService)
                        <a class="button primary" href="{{ route('services.create', array_filter(['category' => $category, 'company' => $company])) }}" aria-haspopup="dialog" aria-controls="service-create-dialog" data-service-create-open><span aria-hidden="true">+</span> Nuevo servicio</a>
                    @endif
                    <a class="button ghost" href="{{ route('reports.download', 'services-catalog') }}">Exportar CSV</a>
                </div>
            </header>

            <nav class="service-categories" aria-label="Categorías de servicio">
                <button class="service-carousel-arrow" type="button" title="Categorías anteriores" aria-label="Categorías anteriores" data-service-carousel="previous">‹</button>
                <div class="service-categories-track" data-service-categories-track>
                    <a class="service-category-card is-all {{ $category === '' ? 'is-selected' : '' }}"
                       href="{{ route('services.catalog', request()->except(['category', 'page'])) }}"
                       @if ($category === '') aria-current="page" @endif>
                        <span class="service-category-symbol" aria-hidden="true">▦</span>
                        <strong>Todos</strong>
                    </a>
                    @foreach ($categories as $availableCategory)
                        @php $categoryStyle = $categoryStyles[$availableCategory] ?? ['label' => $availableCategory, 'symbol' => '•', 'tone' => 'slate']; @endphp
                        <a class="service-category-card tone-{{ $categoryStyle['tone'] }} {{ !empty($categoryStyle['wide']) ? 'is-wide' : '' }} {{ $category === $availableCategory ? 'is-selected' : '' }}"
                           href="{{ route('services.catalog', [...request()->except(['category', 'page']), 'category' => $availableCategory]) }}"
                           @if ($category === $availableCategory) aria-current="page" @endif>
                            <span class="service-category-symbol" aria-hidden="true">{{ $categoryStyle['symbol'] }}</span>
                            <strong>{{ $categoryStyle['label'] }}</strong>
                        </a>
                    @endforeach
                </div>
                <button class="service-carousel-arrow" type="button" title="Más categorías" aria-label="Más categorías" data-service-carousel="next">›</button>
            </nav>

            <form class="service-directory-filters" method="GET" action="{{ route('services.catalog') }}" data-service-filters>
                @if ($category !== '') <input type="hidden" name="category" value="{{ $category }}"> @endif
                <div class="service-directory-selects">
                    <label>Empresa
                        <select name="company" data-service-filter-select data-service-company>
                            <option value="">Todas</option>
                            @foreach ($companyOptions as $companyOption)
                                <option value="{{ $companyOption }}" @selected($company === $companyOption)>{{ $companyOption }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Sucursal / almacén
                        <select name="branch" data-service-filter-select data-service-branch>
                            <option value="">Todas las sedes</option>
                            <option value="__none" @selected($branch === '__none')>Sin sede registrada</option>
                            @foreach ($branchOptions as $branchOption)
                                <option value="{{ $branchOption }}" @selected($branch === $branchOption)>{{ $branchOption }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Estado
                        <select name="status" data-service-filter-select>
                            <option value="">Todos</option>
                            <option value="active" @selected($statusFilter === 'active')>Activo</option>
                            <option value="paused" @selected($statusFilter === 'paused')>Pausado</option>
                            <option value="inactive" @selected($statusFilter === 'inactive')>Inactivo</option>
                        </select>
                    </label>
                </div>
                <div class="service-directory-search-row">
                    <label class="service-directory-search">
                        <span class="sr-only">Buscar servicio, proveedor, folio o número de servicio</span>
                        <input type="search" name="q" value="{{ $query }}" placeholder="Buscar servicio, proveedor o folio..." autocomplete="off">
                    </label>
                    <button class="button ghost" type="submit">Buscar</button>
                    @if ($query !== '' || $category !== '' || $company !== '' || $branch !== '' || $statusFilter !== '')
                        <a class="button ghost" href="{{ route('services.catalog') }}">Limpiar filtros</a>
                    @endif
                </div>
            </form>

            <section class="service-directory-list" aria-label="Servicios registrados">
                <div class="service-directory-table-scroll">
                    <table class="service-directory-table">
                        <thead>
                            <tr>
                                <th scope="col">Servicio</th>
                                <th scope="col">Empresa y sede</th>
                                <th scope="col">Periodicidad</th>
                                <th scope="col">Importe estimado</th>
                                <th scope="col">Próximo pago</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($services as $service)
                                @php
                                    $nextPayment = $service->status === 'active' ? $service->nextScheduledPaymentDate() : null;
                                    $periodName = $periodNames[$service->payment_interval_days] ?? $service->payment_interval_days.' días';
                                    $detailId = 'service-detail-'.$service->id;
                                    $categoryStyle = $categoryStyles[$service->category] ?? ['label' => $service->category, 'symbol' => '•', 'tone' => 'slate'];
                                @endphp
                                <tr class="service-directory-row" id="service-{{ $service->id }}">
                                    <td data-filter-value="{{ $service->service_name }}">
                                        <div class="service-directory-identity">
                                            <span class="service-list-symbol tone-{{ $categoryStyle['tone'] }}" aria-hidden="true">{{ $categoryStyle['symbol'] }}</span>
                                            <span class="service-directory-name">
                                                <strong>{{ $service->service_name }}</strong>
                                                <small>{{ $service->provider }}</small>
                                                @if ($service->is_domiciled) <em>Domiciliado</em> @endif
                                            </span>
                                        </div>
                                    </td>
                                    <td data-filter-value="{{ $service->company_name }}">
                                        <span class="service-directory-company"><strong>{{ $service->company_name }}</strong><small>{{ $service->display_branch }}</small></span>
                                    </td>
                                    <td><span class="service-period">{{ $periodName }}</span></td>
                                    <td class="service-directory-money">${{ number_format((float) $service->cost, 2) }} MXN</td>
                                    <td data-filter-date="{{ $nextPayment?->format('Y-m-d') }}">{{ $nextPayment?->format('d/m/Y') ?? '—' }}</td>
                                    <td><span class="status {{ \App\Support\UiStatus::serviceClass($service->status) }}">{{ \App\Support\UiStatus::service($service->status, 'services') }}</span></td>
                                    <td>
                                        <div class="service-directory-actions">
                                            <a class="button ghost small" href="{{ route('services.edit', $service) }}">Editar</a>
                                            <button class="service-detail-toggle" type="button" title="Más detalles" aria-label="Ver detalles de {{ $service->service_name }}" aria-controls="{{ $detailId }}" aria-expanded="false" data-service-detail-toggle>⋯</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="service-directory-detail" id="{{ $detailId }}" hidden>
                                    <td colspan="7">
                                        <dl class="service-detail-facts">
                                            <div><dt>Folio</dt><dd>{{ $service->folio }}</dd></div>
                                            <div><dt>Titular</dt><dd>{{ $service->holder ?: $service->company_name }}</dd></div>
                                            <div><dt>Categoría</dt><dd>{{ $categoryStyle['label'] }}</dd></div>
                                            <div><dt>Número de servicio</dt><dd>{{ $service->service_number }}</dd></div>
                                            <div><dt>Banco</dt><dd>{{ $service->bank }}</dd></div>
                                            <div><dt>Cuenta pagadora</dt><dd>{{ $service->payer_account }}</dd></div>
                                            <div><dt>Ubicación</dt><dd>{{ $service->display_location }}</dd></div>
                                            <div><dt>Vigencia</dt><dd>{{ $service->validity }}</dd></div>
                                            <div><dt>Lapso de pago</dt><dd>{{ $service->payment_interval_days }} días</dd></div>
                                            <div><dt>Fecha de inicio</dt><dd>{{ $service->start_date?->format('d/m/Y') ?? '—' }}</dd></div>
                                            <div><dt>Fecha de corte</dt><dd>{{ $service->cutoff_day ? 'Día '.$service->cutoff_day : '—' }}</dd></div>
                                            <div><dt>Referencia</dt><dd>{{ $service->reference ?: '—' }}</dd></div>
                                            @if ($service->notes) <div class="service-detail-notes"><dt>Notas</dt><dd>{{ $service->notes }}</dd></div> @endif
                                        </dl>
                                        <div class="service-detail-actions">
                                            @if ($service->status === 'active')
                                                <form method="POST" action="{{ route('services.status', [$service, 'paused']) }}">
                                                    @csrf @method('PATCH')
                                                    <button class="button ghost small" type="submit">Pausar servicio</button>
                                                </form>
                                            @elseif ($service->status === 'paused')
                                                <form method="POST" action="{{ route('services.status', [$service, 'active']) }}">
                                                    @csrf @method('PATCH')
                                                    <button class="button ghost small" type="submit">Reactivar servicio</button>
                                                </form>
                                            @endif
                                            @if ($service->status !== 'inactive')
                                                <form method="POST" action="{{ route('services.status', [$service, 'inactive']) }}" onsubmit="return confirm('¿Dar de baja {{ $service->folio }}?')">
                                                    @csrf @method('PATCH')
                                                    <button class="button danger small" type="submit">Dar de baja</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="service-directory-empty" colspan="7">No hay servicios que coincidan con los filtros.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <footer class="service-directory-footer">
                    <span>Mostrando {{ $services->firstItem() ?? 0 }}–{{ $services->lastItem() ?? 0 }} de {{ $services->total() }} servicios</span>
                    @if ($services->hasPages())
                        <nav class="service-directory-pages" aria-label="Páginas de servicios">
                            @if ($services->onFirstPage()) <span aria-disabled="true">‹</span>
                            @else <a href="{{ $services->previousPageUrl() }}" aria-label="Página anterior">‹</a> @endif
                            <strong>{{ $services->currentPage() }} de {{ $services->lastPage() }}</strong>
                            @if ($services->hasMorePages()) <a href="{{ $services->nextPageUrl() }}" aria-label="Página siguiente">›</a>
                            @else <span aria-disabled="true">›</span> @endif
                        </nav>
                    @endif
                </footer>
            </section>
        </div>

        @if ($canCreateService)
            <dialog id="service-create-dialog" class="service-create-dialog" aria-labelledby="service-create-title" data-service-create-dialog @if ((old('_service_form') === 'catalog' && $errors->any()) || request()->boolean('create')) data-auto-open @endif>
                <form class="service-form" method="POST" action="{{ route('services.store') }}">
                    @csrf
                    <input type="hidden" name="_service_form" value="catalog">
                    <input type="hidden" name="start_date" value="{{ old('start_date', now()->toDateString()) }}">
                    <header class="service-create-header">
                        <div>
                            <h2 id="service-create-title">Nuevo servicio</h2>
                            <p>Alta de servicio recurrente</p>
                        </div>
                        <button class="service-create-close" type="button" aria-label="Cerrar" data-service-create-close>&times;</button>
                    </header>
                    <div class="service-create-scroll">
                        @if (old('_service_form') === 'catalog' && $errors->any())
                            <div class="service-create-errors" role="alert">
                                <strong>Revisa los siguientes datos:</strong>
                                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            </div>
                        @endif
                        @include('services.partials.fields', ['service' => null])
                    </div>
                    <footer class="form-actions service-create-footer">
                        <div class="actions">
                            <button class="button ghost" type="button" data-service-create-close>Cancelar</button>
                            <button class="button primary" type="submit">Guardar servicio</button>
                        </div>
                    </footer>
                </form>
            </dialog>
            @include('services.partials.assets')
        @endif

        <style>
            .service-create-dialog { width: min(1100px, calc(100vw - 32px)); max-width: none; max-height: calc(100dvh - 32px); padding: 0; border: 1px solid var(--line); border-radius: 8px; background: #fff; color: var(--text); box-shadow: 0 20px 60px rgba(14, 39, 56, .24); }
            .service-create-dialog::backdrop { background: rgba(14, 39, 56, .54); }
            .service-create-dialog .service-form { height: min(790px, calc(100dvh - 32px)); max-height: calc(100dvh - 32px); display: flex; flex-direction: column; }
            .service-create-header { padding: 18px 24px; display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; border-bottom: 1px solid var(--line); }
            .service-create-header h2 { margin: 0; font-size: 1.25rem; }
            .service-create-header p { margin: 4px 0 0; color: var(--muted); font-size: .83rem; }
            .service-create-close { width: 32px; height: 32px; flex: 0 0 32px; border: 1px solid var(--line); border-radius: 5px; background: #fff; color: var(--text); font-size: 1.25rem; line-height: 1; cursor: pointer; }
            .service-create-errors { margin: 12px 24px 0; padding: 10px 14px; border: 1px solid #e9b6b6; border-radius: 5px; background: #fff4f4; color: #8c2929; font-size: .82rem; }
            .service-create-errors ul { margin: 4px 0 0; padding-left: 20px; }
            .service-create-scroll { min-height: 0; flex: 1; overflow-y: auto; padding: 0 24px; }
            .service-create-dialog .grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .service-create-dialog .service-form-section:first-child { border-top: 0; }
            .service-create-dialog .service-form-section label { overflow-wrap: anywhere; }
            .service-create-dialog .service-form-section :is(input, select, textarea) { width: 100%; }
            .service-create-dialog .service-form-section label > div { min-width: 0; }
            .service-create-footer { padding: 14px 24px; background: #fff; }
            .service-directory { min-width: 0; display: grid; align-content: start; gap: 15px; }
            .service-directory-heading { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
            .service-directory-heading h2 { margin: 0; font-size: 1.4rem; }
            .service-directory-heading-actions { display: flex; align-items: center; gap: 8px; }
            .service-directory-heading-actions .button { min-height: 38px; white-space: nowrap; }
            .service-categories { min-width: 0; display: grid; grid-template-columns: 30px minmax(0, 1fr) 30px; align-items: center; gap: 9px; }
            .service-carousel-arrow { width: 30px; height: 30px; padding: 0; border: 1px solid var(--line); border-radius: 50%; background: #fff; color: var(--primary-strong); cursor: pointer; font-size: 1.25rem; line-height: 1; box-shadow: 0 2px 6px rgba(24, 44, 64, .1); }
            .service-carousel-arrow:disabled { opacity: .42; cursor: default; box-shadow: none; }
            .service-categories-track { min-width: 0; display: flex; gap: 8px; overflow-x: auto; scroll-behavior: smooth; scroll-snap-type: x mandatory; scrollbar-width: none; padding: 2px 1px 7px; }
            .service-categories-track::-webkit-scrollbar { display: none; }
            .service-category-card { width: 112px; height: 78px; flex: 0 0 112px; padding: 9px 7px; border: 1px solid #d6e3f1; border-radius: 7px; background: #fff; color: #17324a; display: grid; justify-items: center; align-content: center; gap: 5px; text-align: center; text-decoration: none; scroll-snap-align: start; box-shadow: 0 1px 3px rgba(23, 49, 74, .05); }
            .service-category-card.is-wide { width: 188px; flex-basis: 188px; }
            .service-category-card strong { max-width: 100%; overflow-wrap: anywhere; font-size: .78rem; line-height: 1.15; }
            .service-category-symbol { color: #24669d; font-size: 1.43rem; line-height: 1; font-style: normal; font-weight: 850; }
            .service-category-card.tone-cyan .service-category-symbol { color: #078fb3; }
            .service-category-card.tone-amber .service-category-symbol { color: #d79515; }
            .service-category-card.tone-indigo .service-category-symbol { color: #5168b9; }
            .service-category-card.tone-green .service-category-symbol { color: #149566; }
            .service-category-card.tone-rose .service-category-symbol { color: #c84d6e; }
            .service-category-card.tone-slate .service-category-symbol { color: #53687a; }
            .service-category-card.is-selected { border-color: #1b6d88; background: #1b6d88; color: #fff; }
            .service-category-card.is-selected .service-category-symbol { color: #fff; }
            .service-directory-filters { min-width: 0; padding: 12px; border: 1px solid var(--line); border-radius: 7px; background: #fff; display: grid; gap: 10px; }
            .service-directory-selects { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; }
            .service-directory-selects label { min-width: 0; gap: 4px; font-size: .81rem; }
            .service-directory-selects select { width: 100%; min-width: 0; min-height: 36px; }
            .service-directory-search-row { min-width: 0; display: grid; grid-template-columns: minmax(0, 1fr) auto auto; gap: 8px; }
            .service-directory-search-row .button { min-height: 36px; }
            .service-directory-search { min-width: 0; }
            .service-directory-search .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
            .service-directory-search input { width: 100%; min-height: 36px; }
            .service-directory-list { min-width: 0; border: 1px solid var(--line); border-radius: 7px; overflow: hidden; background: #fff; }
            .service-directory-table-scroll { min-width: 0; overflow-x: auto; }
            .service-directory-table { width: 100%; min-width: 1190px; table-layout: fixed; border-collapse: collapse; }
            .service-directory-table th { padding: 10px 12px; background: #f5f9fc; color: #344c60; text-align: left; font-size: .77rem; }
            .service-directory-table th:nth-child(1) { width: 20%; }
            .service-directory-table th:nth-child(2) { width: 20%; }
            .service-directory-table th:nth-child(3) { width: 14%; }
            .service-directory-table th:nth-child(4) { width: 15%; }
            .service-directory-table th:nth-child(5) { width: 12%; }
            .service-directory-table th:nth-child(6) { width: 10%; }
            .service-directory-table th:nth-child(7) { width: 9%; }
            .service-directory-table td { padding: 10px 12px; border-top: 1px solid var(--line); vertical-align: middle; font-size: .83rem; }
            .service-directory-row:has(+ .service-directory-detail:not([hidden])) td { background: #f7fbfd; }
            .service-directory-identity { min-width: 0; display: flex; align-items: center; gap: 9px; }
            .service-list-symbol { width: 35px; height: 35px; flex: 0 0 35px; border-radius: 50%; background: #e6f2fc; color: #24669d; display: grid; place-items: center; font-size: 1.13rem; font-weight: 850; }
            .service-list-symbol.tone-amber { color: #b77b00; background: #fff2d7; }
            .service-list-symbol.tone-cyan { color: #078fb3; background: #e2f8fb; }
            .service-list-symbol.tone-indigo { color: #5168b9; background: #e8edff; }
            .service-list-symbol.tone-green { color: #149566; background: #e0f6ec; }
            .service-list-symbol.tone-rose { color: #c84d6e; background: #ffebf0; }
            .service-list-symbol.tone-slate { color: #53687a; background: #edf2f5; }
            .service-directory-name, .service-directory-company { min-width: 0; display: grid; gap: 2px; }
            .service-directory-name strong, .service-directory-company strong { overflow-wrap: anywhere; line-height: 1.25; }
            .service-directory-name small, .service-directory-company small { color: var(--muted); }
            .service-directory-name em { width: fit-content; padding: 2px 6px; border-radius: 5px; background: #e1f7ec; color: #167954; font-size: .69rem; font-style: normal; font-weight: 700; }
            .service-period { padding: 4px 8px; border-radius: 5px; background: #e9f3fb; color: #195f85; font-size: .76rem; font-weight: 700; white-space: nowrap; }
            .service-directory-money { font-weight: 800; font-variant-numeric: tabular-nums; white-space: nowrap; }
            .service-directory-actions { display: flex; align-items: center; gap: 5px; white-space: nowrap; }
            .service-directory-actions .button { padding: 5px 7px; }
            .service-directory-table .status.approved { border-color: #9bd5b7; background: #e6f7ed; color: #187047; }
            .service-directory-table .status.partial { border-color: #ecd191; background: #fff5df; color: #8d6300; }
            .service-directory-table .status.canceled { border-color: #f0b9b9; background: #fff0f0; color: #a63838; }
            .service-detail-toggle { width: 29px; height: 29px; padding: 0; border: 1px solid var(--line); border-radius: 5px; background: #fff; color: var(--text); cursor: pointer; font-size: 1.1rem; line-height: 1; }
            .service-directory-detail > td { padding: 15px; background: #f7fbfd; }
            .service-detail-facts { margin: 0; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px 18px; }
            .service-detail-facts > div { min-width: 0; }
            .service-detail-facts dt { margin-bottom: 3px; color: #607085; font-size: .73rem; font-weight: 800; }
            .service-detail-facts dd { margin: 0; overflow-wrap: anywhere; font-size: .83rem; }
            .service-detail-notes { grid-column: 1 / -1; }
            .service-detail-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 14px; padding-top: 12px; border-top: 1px solid var(--line); }
            .service-detail-actions form { margin: 0; }
            .service-directory-empty { padding: 36px 12px !important; color: var(--muted); text-align: center; }
            .service-directory-footer { min-height: 48px; padding: 9px 12px; border-top: 1px solid var(--line); display: flex; align-items: center; justify-content: space-between; gap: 12px; color: var(--muted); font-size: .81rem; }
            .service-directory-pages { display: flex; align-items: center; gap: 5px; }
            .service-directory-pages :is(a, span, strong) { min-width: 29px; min-height: 29px; padding: 5px; border: 1px solid var(--line); border-radius: 5px; display: grid; place-items: center; text-decoration: none; }
            .service-directory-pages strong { padding-inline: 9px; background: #e6f3f8; color: var(--primary-strong); }
            .service-directory-pages span { opacity: .45; }
            @media (max-width: 1000px) {
                .service-detail-facts { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            }
            @media (max-width: 700px) {
                .service-create-dialog { width: calc(100vw - 16px); max-height: calc(100dvh - 16px); }
                .service-create-dialog .service-form { height: calc(100dvh - 16px); max-height: calc(100dvh - 16px); }
                .service-create-dialog .grid-4 { grid-template-columns: minmax(0, 1fr); }
                .service-create-header, .service-create-footer { padding: 14px 16px; }
                .service-create-scroll { padding: 0 16px; }
                .service-directory-heading { align-items: flex-start; flex-direction: column; }
                .service-directory-selects { grid-template-columns: 1fr; }
                .service-detail-facts { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            }
            @media (max-width: 470px) {
                .service-directory-heading-actions { flex-wrap: wrap; }
                .service-directory-search-row { grid-template-columns: 1fr 1fr; }
                .service-directory-search { grid-column: 1 / -1; }
                .service-detail-facts { grid-template-columns: 1fr; }
                .service-directory-footer { flex-wrap: wrap; }
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const track = document.querySelector('[data-service-categories-track]');
                const previous = document.querySelector('[data-service-carousel="previous"]');
                const next = document.querySelector('[data-service-carousel="next"]');
                const updateCarousel = () => {
                    previous.disabled = track.scrollLeft <= 1;
                    next.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 1;
                };
                previous.addEventListener('click', () => track.scrollBy({ left: -Math.max(track.clientWidth * .7, 230), behavior: 'smooth' }));
                next.addEventListener('click', () => track.scrollBy({ left: Math.max(track.clientWidth * .7, 230), behavior: 'smooth' }));
                track.addEventListener('scroll', updateCarousel, { passive: true });
                window.addEventListener('resize', updateCarousel);
                track.querySelector('[aria-current="page"]')?.scrollIntoView({ block: 'nearest', inline: 'nearest' });
                updateCarousel();

                document.querySelectorAll('[data-service-detail-toggle]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const detail = document.getElementById(button.getAttribute('aria-controls'));
                        detail.hidden = !detail.hidden;
                        button.setAttribute('aria-expanded', String(!detail.hidden));
                    });
                });

                const filters = document.querySelector('[data-service-filters]');
                const companySelect = filters.querySelector('[data-service-company]');
                companySelect.addEventListener('change', () => {
                    filters.querySelector('[data-service-branch]').value = '';
                    filters.requestSubmit();
                });
                filters.querySelectorAll('[data-service-filter-select]:not([data-service-company])').forEach((select) => {
                    select.addEventListener('change', () => filters.requestSubmit());
                });

                const createDialog = document.querySelector('[data-service-create-dialog]');
                if (createDialog) {
                    const openCreateDialog = () => {
                        createDialog.showModal();
                        createDialog.querySelector('[name="company_name"]')?.focus({ preventScroll: true });
                    };
                    document.querySelector('[data-service-create-open]')?.addEventListener('click', (event) => {
                        if (typeof createDialog.showModal !== 'function') return;
                        event.preventDefault();
                        openCreateDialog();
                    });
                    createDialog.querySelectorAll('[data-service-create-close]').forEach((button) => {
                        button.addEventListener('click', () => createDialog.close());
                    });
                    createDialog.addEventListener('click', (event) => {
                        if (event.target === createDialog) createDialog.close();
                    });
                    createDialog.addEventListener('close', () => {
                        const currentUrl = new URL(window.location.href);
                        if (!currentUrl.searchParams.has('create')) return;
                        currentUrl.searchParams.delete('create');
                        window.history.replaceState(null, '', currentUrl);
                        document.querySelectorAll('.sub-nav-button[href]').forEach((link) => {
                            const linkUrl = new URL(link.href);
                            if (linkUrl.pathname !== currentUrl.pathname) return;
                            link.classList.toggle('active', !linkUrl.searchParams.has('create'));
                        });
                    });
                    if (createDialog.hasAttribute('data-auto-open')) openCreateDialog();
                }
            });
        </script>
    </x-app-shell>
@endsection
