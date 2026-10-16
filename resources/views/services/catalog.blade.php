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
        $occasionalIconChoices = [
            'parking' => 'Estacionamiento', 'car' => 'Automóvil', 'ticket' => 'Vale',
            'briefcase' => 'Trabajo', 'wrench' => 'Mantenimiento', 'star' => 'Otro',
        ];
        $previewIconKeys = array_unique([...array_keys($occasionalIconChoices), ...$occasionalServices->pluck('icon')->all()]);
        $selectedFormCategory = old('category_id', $selectedOccasionalService?->id ?? '');
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

            <nav class="service-categories" aria-label="Categorías de servicios recurrentes" data-service-carousel-root>
                <button class="service-carousel-arrow" type="button" title="Categorías anteriores" aria-label="Categorías anteriores" data-service-carousel="previous">‹</button>
                <div class="service-categories-track" data-service-categories-track>
                    <a class="service-category-card is-all {{ ! $isOccasionalView && $category === '' ? 'is-selected' : '' }}"
                       href="{{ route('services.catalog') }}"
                       @if (! $isOccasionalView && $category === '') aria-current="page" @endif>
                        <span class="service-category-symbol" aria-hidden="true">▦</span>
                        <strong>Todos</strong>
                    </a>
                    @foreach ($categories as $availableCategory)
                        @php $categoryStyle = $categoryStyles[$availableCategory] ?? ['label' => $availableCategory, 'symbol' => '•', 'tone' => 'slate']; @endphp
                        <a class="service-category-card tone-{{ $categoryStyle['tone'] }} {{ !empty($categoryStyle['wide']) ? 'is-wide' : '' }} {{ ! $isOccasionalView && $category === $availableCategory ? 'is-selected' : '' }}"
                           href="{{ route('services.catalog', ['category' => $availableCategory]) }}"
                           @if (! $isOccasionalView && $category === $availableCategory) aria-current="page" @endif>
                            <span class="service-category-symbol" aria-hidden="true">{{ $categoryStyle['symbol'] }}</span>
                            <strong>{{ $categoryStyle['label'] }}</strong>
                        </a>
                    @endforeach
                </div>
                <button class="service-carousel-arrow" type="button" title="Más categorías" aria-label="Más categorías" data-service-carousel="next">›</button>
            </nav>

            <header class="service-directory-heading occasional-heading">
                <h2>Servicios Ocasionales</h2>
                <div class="service-directory-heading-actions">
                    @if ($canCreateService)
                        <button class="button primary" type="button" data-occasional-create-open><span aria-hidden="true">+</span> Nuevo servicio</button>
                    @endif
                    <a class="button ghost" href="{{ route('services.occasional.export') }}">Exportar CSV</a>
                </div>
            </header>

            <nav class="service-categories" aria-label="Servicios ocasionales" data-service-carousel-root>
                <button class="service-carousel-arrow" type="button" title="Servicios anteriores" aria-label="Servicios anteriores" data-service-carousel="previous">‹</button>
                <div class="service-categories-track" data-service-categories-track>
                    <a class="service-category-card is-all {{ $isOccasionalView && ! $selectedOccasionalService ? 'is-selected' : '' }}"
                       href="{{ route('services.catalog', ['view' => 'occasional']) }}"
                       @if ($isOccasionalView && ! $selectedOccasionalService) aria-current="page" @endif>
                        <span class="service-category-symbol" aria-hidden="true">▦</span>
                        <strong>Todos</strong>
                    </a>
                    @foreach ($occasionalServices as $occasionalService)
                        <a class="service-category-card tone-{{ $occasionalService->color }} {{ $isOccasionalView && $selectedOccasionalService?->id === $occasionalService->id ? 'is-selected' : '' }}"
                           title="{{ $occasionalService->name }}"
                           href="{{ route('services.catalog', ['occasional' => $occasionalService->id]) }}"
                           @if ($isOccasionalView && $selectedOccasionalService?->id === $occasionalService->id) aria-current="page" @endif>
                            <span class="service-category-symbol" aria-hidden="true">@include('services.partials.occasional-icon', ['icon' => $occasionalService->icon])</span>
                            <strong>{{ $occasionalService->name }}</strong>
                        </a>
                    @endforeach
                </div>
                <button class="service-carousel-arrow" type="button" title="Más servicios" aria-label="Más servicios" data-service-carousel="next">›</button>
            </nav>

            @if ($isOccasionalView)
                <section class="service-directory-list occasional-directory-results" aria-label="Servicios ocasionales seleccionados">
                    <header class="service-results-heading">
                        <h3>{{ $selectedOccasionalService?->name ?? 'Todos los servicios ocasionales' }}</h3>
                    </header>
                    <div class="service-directory-table-scroll">
                        <table class="service-directory-table occasional-directory-table">
                            <thead>
                                <tr>
                                    <th scope="col">Servicio</th>
                                    <th scope="col">Empresa y sede</th>
                                    <th scope="col">Banco</th>
                                    <th scope="col">Cuenta pagadora</th>
                                    <th scope="col">Periodicidad</th>
                                    <th scope="col">Importe estimado</th>
                                    <th scope="col">Próximo pago</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col" data-no-filter>Ver información</th>
                                    <th scope="col" data-no-filter>Editar información</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($occasionalEntries as $entry)
                                    @php
                                        $nextOccasionalPayment = $entry->payments->first();
                                        $detailId = 'occasional-detail-'.$entry->id;
                                    @endphp
                                    <tr class="occasional-directory-row">
                                        <td data-filter-value="{{ $entry->name }}">
                                            <div class="service-directory-identity">
                                                <span class="service-list-symbol tone-{{ $entry->category->color }}" aria-hidden="true">@include('services.partials.occasional-icon', ['icon' => $entry->category->icon])</span>
                                                <span class="service-directory-name"><strong>{{ $entry->name }}</strong><small>{{ $entry->category->name }}</small></span>
                                            </div>
                                        </td>
                                        <td data-filter-value="Sin empresa ni sede">—</td>
                                        <td class="service-directory-banking">{{ $entry->bank ?: '—' }}</td>
                                        <td class="service-directory-banking">{{ $entry->payer_account ?: '—' }}</td>
                                        <td><span class="service-period">Pago único</span></td>
                                        <td class="service-directory-money" data-filter-value="{{ $nextOccasionalPayment?->amount ?? 'Sin importe' }}">{{ $nextOccasionalPayment ? '$'.number_format((float) $nextOccasionalPayment->amount, 2).' MXN' : '—' }}</td>
                                        <td data-filter-date="{{ $nextOccasionalPayment?->due_date?->format('Y-m-d') }}">{{ $nextOccasionalPayment?->due_date?->format('d/m/Y') ?? '—' }}</td>
                                        <td><span class="status {{ $nextOccasionalPayment ? 'partial' : '' }}">{{ $nextOccasionalPayment ? 'Pendiente' : 'Sin pago programado' }}</span></td>
                                        <td class="occasional-action-cell">
                                            <button class="button ghost small" type="button" title="Ver información" aria-label="Ver información de {{ $entry->name }}" aria-controls="{{ $detailId }}" aria-expanded="false" data-service-detail-toggle data-occasional-detail-toggle data-entry-name="{{ $entry->name }}">Ver</button>
                                        </td>
                                        <td class="occasional-action-cell">
                                            @if ($canEditOccasionalService)
                                                <button class="button ghost small" type="button" title="Editar información" aria-label="Editar información de {{ $entry->name }}"
                                                    data-occasional-edit-open data-edit-id="{{ $entry->id }}" data-edit-url="{{ route('services.occasional.update', $entry) }}"
                                                    data-edit-category="{{ $entry->occasional_service_id }}" data-edit-name="{{ $entry->name }}"
                                                    data-edit-reference="{{ $entry->reference_number }}" data-edit-bank="{{ $entry->bank }}"
                                                    data-edit-payer-account="{{ $entry->payer_account }}" data-edit-description="{{ $entry->description }}">Editar</button>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="service-directory-detail" id="{{ $detailId }}" hidden>
                                        <td colspan="10">
                                            <dl class="service-detail-facts">
                                                <div><dt>Categoría</dt><dd>{{ $entry->category->name }}</dd></div>
                                                <div><dt>Número de referencia</dt><dd class="occasional-reference">{{ $entry->reference_number }}</dd></div>
                                                <div><dt>Fecha de alta</dt><dd>{{ $entry->created_at->format('d/m/Y') }}</dd></div>
                                                <div class="service-detail-notes"><dt>Descripción</dt><dd>{{ $entry->description ?: '—' }}</dd></div>
                                            </dl>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td class="service-directory-empty" colspan="10">No hay servicios ocasionales registrados{{ $selectedOccasionalService ? ' en esta categoría' : '' }}.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <footer class="service-directory-footer">
                        <span>Mostrando {{ $occasionalEntries->firstItem() ?? 0 }}–{{ $occasionalEntries->lastItem() ?? 0 }} de {{ $occasionalEntries->total() }} servicios</span>
                        @if ($occasionalEntries->hasPages())
                            <nav class="service-directory-pages" aria-label="Páginas de servicios ocasionales">
                                @if ($occasionalEntries->onFirstPage()) <span aria-disabled="true">‹</span>
                                @else <a href="{{ $occasionalEntries->previousPageUrl() }}" aria-label="Página anterior">‹</a> @endif
                                <strong>{{ $occasionalEntries->currentPage() }} de {{ $occasionalEntries->lastPage() }}</strong>
                                @if ($occasionalEntries->hasMorePages()) <a href="{{ $occasionalEntries->nextPageUrl() }}" aria-label="Página siguiente">›</a>
                                @else <span aria-disabled="true">›</span> @endif
                            </nav>
                        @endif
                    </footer>
                </section>
            @else
            <div class="service-results-heading" role="status">
                <h3>{{ $category === '' ? 'Todos los servicios recurrentes' : ($categoryStyles[$category]['label'] ?? $category) }}</h3>
            </div>
            <section class="service-directory-list" aria-label="Servicios registrados">
                <div class="service-directory-table-scroll">
                    <table class="service-directory-table">
                        <thead>
                            <tr>
                                <th scope="col">Servicio</th>
                                <th scope="col">Empresa y sede</th>
                                <th scope="col">Banco</th>
                                <th scope="col">Cuenta pagadora</th>
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
                                    <td class="service-directory-banking">{{ $service->bank ?: '—' }}</td>
                                    <td class="service-directory-banking">{{ $service->payer_account ?: '—' }}</td>
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
                                    <td colspan="9">
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
                                <tr><td class="service-directory-empty" colspan="9">No hay servicios que coincidan con los filtros.</td></tr>
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
            @endif
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

        @if ($canCreateService)
            <dialog id="occasional-create-dialog" class="occasional-create-dialog" aria-labelledby="occasional-create-title" data-occasional-create-dialog @if ((old('_service_form') === 'occasional' && $errors->any()) || request()->boolean('create_occasional')) data-auto-open @endif>
                <form method="POST" action="{{ route('services.occasional.store') }}" data-occasional-form>
                    @csrf
                    <input type="hidden" name="_service_form" value="occasional">
                    <header class="service-create-header">
                        <div>
                            <h2 id="occasional-create-title">Nuevo servicio ocasional</h2>
                        </div>
                        <button class="service-create-close" type="button" aria-label="Cerrar" data-occasional-create-close>&times;</button>
                    </header>
                    <div class="occasional-create-body">
                        @if (old('_service_form') === 'occasional' && $errors->any())
                            <div class="service-create-errors occasional-errors" role="alert">
                                <strong>Revisa los siguientes datos:</strong>
                                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            </div>
                        @endif
                        <div class="occasional-create-fields">
                            <label for="occasional-category">Categoría
                                <select id="occasional-category" name="category_id" data-occasional-category>
                                    <option value="">Nueva categoría</option>
                                    @foreach ($occasionalServices as $occasionalService)
                                        <option value="{{ $occasionalService->id }}" data-icon="{{ $occasionalService->icon }}" data-color="{{ $occasionalService->color }}" @selected((string) $selectedFormCategory === (string) $occasionalService->id)>{{ $occasionalService->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label for="occasional-name"><span>Nombre del servicio/Titular <span class="required-mark">*</span></span>
                                <input id="occasional-name" name="name" value="{{ old('name', '') }}" maxlength="100" required data-occasional-name>
                            </label>
                            <label for="occasional-reference"><span>Número de referencia <span class="required-mark">*</span></span>
                                <input id="occasional-reference" name="reference_number" value="{{ old('reference_number', '') }}" maxlength="100" required data-occasional-reference>
                            </label>
                            <label for="occasional-bank">Banco (opcional)
                                <input id="occasional-bank" name="bank" value="{{ old('bank', '') }}" maxlength="120">
                            </label>
                            <label for="occasional-payer-account">Cuenta pagadora (opcional)
                                <input id="occasional-payer-account" name="payer_account" value="{{ old('payer_account', '') }}" maxlength="80">
                            </label>
                            <label for="occasional-description">Descripción (opcional)
                                <textarea id="occasional-description" name="description" maxlength="500" rows="3">{{ old('description', '') }}</textarea>
                            </label>
                            <fieldset class="occasional-icon-options" data-occasional-new-category @if (filled($selectedFormCategory)) hidden @endif>
                                <legend>Selecciona un icono</legend>
                                <div class="occasional-icon-grid">
                                    @foreach ($occasionalIconChoices as $iconKey => $iconLabel)
                                        <label class="occasional-icon-choice" title="{{ $iconLabel }}">
                                            <input type="radio" name="icon" value="{{ $iconKey }}" @checked(old('icon', 'parking') === $iconKey)>
                                            <span aria-hidden="true">@include('services.partials.occasional-icon', ['icon' => $iconKey])</span>
                                            <span class="sr-only">{{ $iconLabel }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                            <fieldset class="occasional-color-options" data-occasional-new-category @if (filled($selectedFormCategory)) hidden @endif>
                                <legend>Color del icono</legend>
                                <div class="occasional-color-grid">
                                    @foreach (['cyan' => 'Turquesa', 'blue' => 'Azul', 'amber' => 'Amarillo', 'rose' => 'Rojo', 'purple' => 'Morado'] as $colorKey => $colorLabel)
                                        <label class="occasional-color-choice tone-{{ $colorKey }}" title="{{ $colorLabel }}">
                                            <input type="radio" name="color" value="{{ $colorKey }}" @checked(old('color', 'cyan') === $colorKey)>
                                            <span class="sr-only">{{ $colorLabel }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        </div>
                        <aside class="occasional-preview" aria-label="Vista previa del servicio">
                            <strong>Vista previa</strong>
                            <div class="occasional-preview-card tone-{{ old('color', 'cyan') }}" data-occasional-preview>
                                <span class="service-category-symbol" aria-hidden="true">
                                    @foreach ($previewIconKeys as $iconKey)
                                        <span data-occasional-preview-icon="{{ $iconKey }}" @if (old('icon', 'parking') !== $iconKey) hidden @endif>@include('services.partials.occasional-icon', ['icon' => $iconKey])</span>
                                    @endforeach
                                </span>
                                <strong data-occasional-preview-name>{{ old('name') ?: 'Nombre del servicio' }}</strong>
                                <small data-occasional-preview-reference>{{ old('reference_number') ?: 'Número de referencia' }}</small>
                            </div>
                            <small data-occasional-preview-category>{{ $selectedOccasionalService?->name ?? 'Nueva categoría' }}</small>
                        </aside>
                    </div>
                    <footer class="occasional-create-footer">
                        <div class="service-directory-heading-actions">
                            <button class="button ghost" type="button" data-occasional-create-close>Cancelar</button>
                            <button class="button primary" type="submit"><span aria-hidden="true">+</span> Guardar servicio</button>
                        </div>
                    </footer>
                </form>
            </dialog>
        @endif

        @if ($isOccasionalView && $canEditOccasionalService)
            <dialog id="occasional-edit-dialog" class="occasional-create-dialog occasional-edit-dialog" aria-labelledby="occasional-edit-title"
                data-occasional-edit-dialog @if (old('_service_form') === 'occasional_edit' && $errors->any()) data-auto-open-entry="{{ old('entry_id') }}" @endif>
                <form method="POST" data-occasional-edit-form>
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_service_form" value="occasional_edit">
                    <input type="hidden" name="entry_id" value="{{ old('entry_id') }}">
                    <header class="service-create-header">
                        <h2 id="occasional-edit-title">Editar información</h2>
                        <button class="service-create-close" type="button" aria-label="Cerrar" data-occasional-edit-close>&times;</button>
                    </header>
                    <div class="occasional-create-fields occasional-edit-body">
                        @if (old('_service_form') === 'occasional_edit' && $errors->any())
                            <div class="service-create-errors occasional-errors" role="alert">
                                <strong>Revisa los siguientes datos:</strong>
                                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            </div>
                        @endif
                        <label for="occasional-edit-category">Categoría
                            <select id="occasional-edit-category" name="category_id" required>
                                @foreach ($occasionalServices as $occasionalService)
                                    <option value="{{ $occasionalService->id }}" @selected(old('_service_form') === 'occasional_edit' && (string) old('category_id') === (string) $occasionalService->id)>{{ $occasionalService->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label for="occasional-edit-name">Nombre del servicio/Titular
                            <input id="occasional-edit-name" name="name" maxlength="100" value="{{ old('_service_form') === 'occasional_edit' ? old('name') : '' }}" required>
                        </label>
                        <label for="occasional-edit-reference">Número de referencia
                            <input id="occasional-edit-reference" name="reference_number" maxlength="100" value="{{ old('_service_form') === 'occasional_edit' ? old('reference_number') : '' }}" required>
                        </label>
                        <label for="occasional-edit-bank">Banco (opcional)
                            <input id="occasional-edit-bank" name="bank" maxlength="120" value="{{ old('_service_form') === 'occasional_edit' ? old('bank') : '' }}">
                        </label>
                        <label for="occasional-edit-payer-account">Cuenta pagadora (opcional)
                            <input id="occasional-edit-payer-account" name="payer_account" maxlength="80" value="{{ old('_service_form') === 'occasional_edit' ? old('payer_account') : '' }}">
                        </label>
                        <label for="occasional-edit-description">Descripción (opcional)
                            <textarea id="occasional-edit-description" name="description" maxlength="500" rows="3">{{ old('_service_form') === 'occasional_edit' ? old('description') : '' }}</textarea>
                        </label>
                    </div>
                    <footer class="occasional-create-footer">
                        <button class="button ghost" type="button" data-occasional-edit-close>Cancelar</button>
                        <button class="button primary" type="submit">Guardar cambios</button>
                    </footer>
                </form>
            </dialog>
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
            .service-category-card strong { max-width: 100%; overflow-wrap: anywhere; font-size: .78rem; line-height: 1.15; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; overflow: hidden; }
            .service-category-symbol { color: #24669d; font-size: 1.43rem; line-height: 1; font-style: normal; font-weight: 850; }
            .service-category-symbol svg, .service-list-symbol svg { width: 22px; height: 22px; display: block; }
            .service-category-card.tone-cyan .service-category-symbol { color: #078fb3; }
            .service-category-card.tone-amber .service-category-symbol { color: #d79515; }
            .service-category-card.tone-indigo .service-category-symbol { color: #5168b9; }
            .service-category-card.tone-green .service-category-symbol { color: #149566; }
            .service-category-card.tone-rose .service-category-symbol { color: #c84d6e; }
            .service-category-card.tone-slate .service-category-symbol { color: #53687a; }
            .service-category-card.tone-blue .service-category-symbol { color: #2866c7; }
            .service-category-card.tone-purple .service-category-symbol { color: #873bcb; }
            .service-category-card.is-selected { border-color: #1b6d88; background: #1b6d88; color: #fff; }
            .service-category-card.is-selected .service-category-symbol { color: #fff; }
            .occasional-heading { margin-top: 2px; }
            .service-results-heading h3 { margin: 0; font-size: 1rem; line-height: 1.3; }
            .occasional-directory-results > .service-results-heading { padding: 13px 16px; border-bottom: 1px solid var(--line); background: #f5f9fc; }
            .occasional-reference { font-variant-numeric: tabular-nums; font-weight: 700; }
            .service-list-symbol.tone-blue { color: #2866c7; background: #e8f0ff; }
            .service-list-symbol.tone-purple { color: #873bcb; background: #f2e8ff; }
            .service-create-close:hover, .service-carousel-arrow:not(:disabled):hover { background: #f1f8fc; }
            .occasional-create-dialog { width: min(790px, calc(100vw - 24px)); max-width: none; max-height: calc(100dvh - 24px); padding: 0; border: 1px solid var(--line); border-radius: 8px; background: #fff; color: var(--text); box-shadow: 0 20px 60px rgba(14, 39, 56, .24); }
            .occasional-create-dialog::backdrop { background: rgba(14, 39, 56, .54); }
            .occasional-create-dialog form { max-height: calc(100dvh - 24px); display: flex; flex-direction: column; }
            .occasional-create-body { min-height: 0; overflow-y: auto; padding: 22px 24px; display: grid; grid-template-columns: minmax(0, 1.3fr) minmax(220px, .9fr); align-items: start; gap: 22px; }
            .occasional-errors { grid-column: 1 / -1; margin: 0; }
            .occasional-create-fields { min-width: 0; display: grid; align-content: start; gap: 16px; }
            .occasional-create-fields label { gap: 6px; }
            .occasional-create-dialog .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
            .occasional-create-fields :is(input, select, textarea) { width: 100%; }
            .occasional-create-fields textarea { min-height: 82px; padding: 10px 12px; border: 1px solid var(--line); border-radius: 6px; resize: vertical; font: inherit; }
            .required-mark { color: #c72d3b; }
            .occasional-create-fields fieldset { min-width: 0; margin: 0; padding: 0; border: 0; }
            .occasional-create-fields legend { margin-bottom: 8px; color: #344054; font-weight: 650; }
            .occasional-icon-grid, .occasional-color-grid { display: flex; flex-wrap: wrap; gap: 7px; }
            .occasional-icon-choice { position: relative; width: 57px; height: 54px; border: 1px solid var(--line); border-radius: 6px; display: grid; place-items: center; color: #1f5c86; cursor: pointer; }
            .occasional-icon-choice > span:first-of-type svg { width: 24px; height: 24px; display: block; }
            .occasional-icon-choice:has(input:checked) { border-color: #087ca1; background: #eaf7fb; box-shadow: inset 0 0 0 1px #087ca1; }
            .occasional-icon-choice:focus-within, .occasional-color-choice:focus-within { outline: 2px solid #087ca1; outline-offset: 2px; }
            .occasional-icon-choice input, .occasional-color-choice input { position: absolute; width: 1px; height: 1px; min-height: 0; opacity: 0; }
            .occasional-color-choice { --swatch: #0786a6; position: relative; width: 31px; height: 31px; border: 3px solid #fff; border-radius: 50%; background: var(--swatch); box-shadow: 0 0 0 1px transparent; cursor: pointer; }
            .occasional-color-choice:has(input:checked) { box-shadow: 0 0 0 2px var(--swatch); }
            .occasional-color-choice.tone-blue { --swatch: #3267e8; }
            .occasional-color-choice.tone-amber { --swatch: #f5a623; }
            .occasional-color-choice.tone-rose { --swatch: #ea3253; }
            .occasional-color-choice.tone-purple { --swatch: #873bcb; }
            .occasional-preview { min-width: 0; min-height: 250px; padding: 20px; border-radius: 6px; background: #ecf6fc; display: grid; align-content: center; justify-items: center; gap: 18px; text-align: center; }
            .occasional-preview > strong { justify-self: start; align-self: start; }
            .occasional-preview > small { color: var(--muted); }
            .occasional-preview-card { width: min(100%, 188px); min-height: 115px; padding: 15px; border: 1px solid var(--line); border-radius: 6px; background: #fff; display: grid; place-content: center; justify-items: center; gap: 8px; }
            .occasional-preview-card strong { max-width: 100%; overflow-wrap: anywhere; font-size: .9rem; }
            .occasional-preview-card small { max-width: 100%; color: var(--muted); overflow-wrap: anywhere; }
            .occasional-preview-card.tone-cyan .service-category-symbol { color: #0786a6; }
            .occasional-preview-card.tone-blue .service-category-symbol { color: #3267e8; }
            .occasional-preview-card.tone-amber .service-category-symbol { color: #d79515; }
            .occasional-preview-card.tone-rose .service-category-symbol { color: #ea3253; }
            .occasional-preview-card.tone-purple .service-category-symbol { color: #873bcb; }
            .occasional-preview-card.tone-green .service-category-symbol { color: #149566; }
            .occasional-create-footer { padding: 15px 24px; border-top: 1px solid var(--line); display: flex; align-items: center; justify-content: flex-end; gap: 12px; }
            .occasional-edit-dialog { width: min(520px, calc(100vw - 24px)); }
            .occasional-edit-body { max-height: calc(100dvh - 160px); overflow-y: auto; padding: 20px 24px; }
            .service-directory-list { min-width: 0; border: 1px solid var(--line); border-radius: 7px; overflow: hidden; background: #fff; }
            .service-directory-table-scroll { min-width: 0; overflow-x: auto; }
            .service-directory-table { width: 100%; min-width: 1500px; table-layout: fixed; border-collapse: collapse; }
            .service-directory-table th { padding: 10px 12px; background: #f5f9fc; color: #344c60; text-align: left; font-size: .77rem; }
            .service-directory-table th:nth-child(1) { width: 18%; }
            .service-directory-table th:nth-child(2) { width: 17%; }
            .service-directory-table th:nth-child(3) { width: 9%; }
            .service-directory-table th:nth-child(4) { width: 11%; }
            .service-directory-table th:nth-child(5) { width: 9%; }
            .service-directory-table th:nth-child(6) { width: 11%; }
            .service-directory-table th:nth-child(7) { width: 10%; }
            .service-directory-table th:nth-child(8) { width: 7%; }
            .service-directory-table th:nth-child(9) { width: 8%; }
            .occasional-directory-table { min-width: 1580px; }
            .occasional-directory-table th:nth-child(1) { width: 17%; }
            .occasional-directory-table th:nth-child(2) { width: 12%; }
            .occasional-directory-table th:nth-child(3) { width: 9%; }
            .occasional-directory-table th:nth-child(4) { width: 11%; }
            .occasional-directory-table th:nth-child(5) { width: 8%; }
            .occasional-directory-table th:nth-child(6) { width: 11%; }
            .occasional-directory-table th:nth-child(7) { width: 10%; }
            .occasional-directory-table th:nth-child(8) { width: 10%; }
            .occasional-directory-table th:nth-child(9), .occasional-directory-table th:nth-child(10) { width: 6%; }
            .occasional-directory-table :is(th, td):nth-child(n+9) { text-align: center; }
            .occasional-action-cell .button { min-width: 62px; justify-content: center; }
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
            .service-directory-banking { font-variant-numeric: tabular-nums; overflow-wrap: anywhere; }
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
                .occasional-create-body { grid-template-columns: 1fr; padding: 16px; }
                .occasional-edit-body { padding: 16px; }
                .occasional-preview { min-height: 190px; }
                .occasional-create-footer { padding: 14px 16px; flex-wrap: wrap; }
                .service-create-dialog { width: calc(100vw - 16px); max-height: calc(100dvh - 16px); }
                .service-create-dialog .service-form { height: calc(100dvh - 16px); max-height: calc(100dvh - 16px); }
                .service-create-dialog .grid-4 { grid-template-columns: minmax(0, 1fr); }
                .service-create-header, .service-create-footer { padding: 14px 16px; }
                .service-create-scroll { padding: 0 16px; }
                .service-directory-heading { align-items: flex-start; flex-direction: column; }
                .service-detail-facts { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            }
            @media (max-width: 470px) {
                .service-directory-heading-actions { flex-wrap: wrap; }
                .service-detail-facts { grid-template-columns: 1fr; }
                .service-directory-footer { flex-wrap: wrap; }
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-service-carousel-root]').forEach((carousel) => {
                    const track = carousel.querySelector('[data-service-categories-track]');
                    const previous = carousel.querySelector('[data-service-carousel="previous"]');
                    const next = carousel.querySelector('[data-service-carousel="next"]');
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
                });

                document.querySelectorAll('[data-service-detail-toggle]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const detail = document.getElementById(button.getAttribute('aria-controls'));
                        detail.hidden = !detail.hidden;
                        button.setAttribute('aria-expanded', String(!detail.hidden));
                        if (button.hasAttribute('data-occasional-detail-toggle')) {
                            const action = detail.hidden ? 'Ver' : 'Ocultar';
                            button.textContent = action;
                            button.title = `${action} información`;
                            button.setAttribute('aria-label', `${action} información de ${button.dataset.entryName}`);
                        }
                    });
                });

                const clearCreateQueryFlag = (flag) => {
                    const currentUrl = new URL(window.location.href);
                    if (!currentUrl.searchParams.has(flag)) return;
                    currentUrl.searchParams.delete(flag);
                    window.history.replaceState(null, '', currentUrl);
                    document.querySelectorAll('.sub-nav-button[href]').forEach((link) => {
                        const linkUrl = new URL(link.href);
                        if (linkUrl.pathname !== currentUrl.pathname) return;
                        link.classList.toggle('active', !linkUrl.searchParams.has('create') && !linkUrl.searchParams.has('create_occasional'));
                    });
                };

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
                    createDialog.addEventListener('close', () => clearCreateQueryFlag('create'));
                    if (createDialog.hasAttribute('data-auto-open')) openCreateDialog();
                }

                const occasionalDialog = document.querySelector('[data-occasional-create-dialog]');
                if (occasionalDialog) {
                    const openOccasionalDialog = () => {
                        occasionalDialog.showModal();
                        occasionalDialog.querySelector('[name="name"]')?.focus({ preventScroll: true });
                    };
                    document.querySelector('[data-occasional-create-open]')?.addEventListener('click', openOccasionalDialog);
                    occasionalDialog.querySelectorAll('[data-occasional-create-close]').forEach((button) => {
                        button.addEventListener('click', () => occasionalDialog.close());
                    });
                    occasionalDialog.addEventListener('click', (event) => {
                        if (event.target === occasionalDialog) occasionalDialog.close();
                    });
                    occasionalDialog.addEventListener('close', () => clearCreateQueryFlag('create_occasional'));
                    const form = occasionalDialog.querySelector('[data-occasional-form]');
                    const preview = form.querySelector('[data-occasional-preview]');
                    const category = form.querySelector('[data-occasional-category]');
                    const updatePreview = () => {
                        const isNewCategory = !category.value;
                        form.querySelectorAll('[data-occasional-new-category]').forEach((fieldset) => {
                            fieldset.hidden = !isNewCategory;
                        });
                        form.querySelector('[data-occasional-preview-name]').textContent = form.querySelector('[data-occasional-name]').value.trim() || 'Nombre del servicio';
                        form.querySelector('[data-occasional-preview-reference]').textContent = form.querySelector('[data-occasional-reference]').value.trim() || 'Número de referencia';
                        form.querySelector('[data-occasional-preview-category]').textContent = isNewCategory ? 'Nueva categoría' : category.selectedOptions[0].textContent.trim();
                        const selectedIcon = isNewCategory ? form.querySelector('[name="icon"]:checked')?.value || 'parking' : category.selectedOptions[0].dataset.icon;
                        const selectedColor = isNewCategory ? form.querySelector('[name="color"]:checked')?.value || 'cyan' : category.selectedOptions[0].dataset.color;
                        form.querySelectorAll('[data-occasional-preview-icon]').forEach((icon) => {
                            icon.hidden = icon.dataset.occasionalPreviewIcon !== selectedIcon;
                        });
                        preview.className = `occasional-preview-card tone-${selectedColor}`;
                    };
                    form.addEventListener('input', updatePreview);
                    form.addEventListener('change', updatePreview);
                    updatePreview();
                    if (occasionalDialog.hasAttribute('data-auto-open')) openOccasionalDialog();
                }

                const editDialog = document.querySelector('[data-occasional-edit-dialog]');
                if (editDialog) {
                    const editForm = editDialog.querySelector('[data-occasional-edit-form]');
                    const editButtons = [...document.querySelectorAll('[data-occasional-edit-open]')];
                    const openEditDialog = (button, preserveInput = false) => {
                        editForm.action = button.dataset.editUrl;
                        editForm.querySelector('[name="entry_id"]').value = button.dataset.editId;
                        if (!preserveInput) {
                            editForm.querySelector('[name="category_id"]').value = button.dataset.editCategory;
                            editForm.querySelector('[name="name"]').value = button.dataset.editName;
                            editForm.querySelector('[name="reference_number"]').value = button.dataset.editReference;
                            editForm.querySelector('[name="bank"]').value = button.dataset.editBank;
                            editForm.querySelector('[name="payer_account"]').value = button.dataset.editPayerAccount;
                            editForm.querySelector('[name="description"]').value = button.dataset.editDescription;
                        }
                        editDialog.showModal();
                        editForm.querySelector('[name="name"]').focus({ preventScroll: true });
                    };
                    editButtons.forEach((button) => button.addEventListener('click', () => openEditDialog(button)));
                    editDialog.querySelectorAll('[data-occasional-edit-close]').forEach((button) => {
                        button.addEventListener('click', () => editDialog.close());
                    });
                    editDialog.addEventListener('click', (event) => {
                        if (event.target === editDialog) editDialog.close();
                    });
                    const requestedEntry = editDialog.dataset.autoOpenEntry;
                    const requestedButton = editButtons.find((button) => button.dataset.editId === requestedEntry);
                    if (requestedButton) openEditDialog(requestedButton, true);
                }
            });
        </script>
    </x-app-shell>
@endsection
