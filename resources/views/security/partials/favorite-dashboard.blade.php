@php
    $isDashboard = $securitySectionKey === 'companies';
    $favoriteBaseQuery = $isDashboard
        ? ['from_date' => $companyDashboardDateFrom, 'to_date' => $companyDashboardDateTo, 'compare' => $companyDashboardComparison]
        : ['section' => $securitySectionKey];
    if ($securitySectionKey === 'analytics') {
        $favoriteBaseQuery['analytics_date'] = $analyticsDate;
    }
    $favoriteSelection = $isDashboard ? $selectedSecurityCompany : $selectedSectionCompany;
    $favoriteCompanyParameter = $isDashboard ? 'company' : 'company_id';
    $favoriteAllQuery = $isDashboard ? $favoriteBaseQuery : array_merge($favoriteBaseQuery, ['company_id' => 'all']);
    $incidentScope = $selectedSecurityCompany?->name ?? 'todas las empresas';
    $pendingIncidents = $dashboardIncidents->where('status', 'pending')->count();
    $resolvedIncidents = $dashboardIncidents->where('status', 'resolved')->count();
    $criticalIncidents = $dashboardIncidents->where('status', 'pending')->where('priority', 'critical')->count();
    $priorityLabels = ['critical' => 'Crítica', 'medium' => 'Media', 'low' => 'Baja'];
    $favoriteFormIds = old('_favorites_form') ? old('favorite_ids', []) : $favoriteSecurityCompanies->pluck('id')->all();
    $favoriteFormIds = array_map('strval', is_array($favoriteFormIds) ? $favoriteFormIds : []);
@endphp

<section class="security-favorite-rail" data-security-carousel @if (!$isDashboard) data-security-section-company-selector @endif aria-label="Empresas favoritas">
    <header class="security-favorite-rail-heading">
        <h2>Empresas favoritas</h2>
        <p>Acceso rápido a favoritas</p>
    </header>
    <div class="security-favorite-carousel">
        <button class="security-favorite-arrow" type="button" data-security-carousel-prev aria-label="Ver favoritas anteriores">&lsaquo;</button>
        <div class="security-favorite-track" data-security-carousel-track>
            <a class="security-favorite-card is-all {{ $favoriteSelection ? '' : 'is-active' }}" href="{{ route('security.index', $favoriteAllQuery) }}" data-security-carousel-item @if (!$favoriteSelection) aria-current="page" @endif>
                <span class="security-favorite-avatar is-all" aria-hidden="true">▦</span>
                <span class="security-favorite-card-copy"><strong>Todas</strong><small>Resumen de empresas</small></span>
            </a>

            @foreach ($favoriteSecurityCompanies as $favoriteCompany)
                <div class="security-favorite-card-wrap {{ $favoriteSelection?->is($favoriteCompany) ? 'is-active' : '' }}" data-security-carousel-item data-security-favorite-company-id="{{ $favoriteCompany->id }}">
                    <a class="security-favorite-card" href="{{ route('security.index', array_merge($favoriteBaseQuery, [$favoriteCompanyParameter => $favoriteCompany->id])) }}" @if ($favoriteSelection?->is($favoriteCompany)) aria-current="page" @endif>
                        <span class="security-favorite-avatar is-tone-{{ ($loop->index % 5) + 1 }}" aria-hidden="true">{{ $favoriteCompany->initials() }}</span>
                        <span class="security-favorite-card-copy"><strong>{{ $favoriteCompany->name }}</strong><small>{{ $favoriteCompany->branches_count }} {{ $favoriteCompany->branches_count === 1 ? 'sucursal' : 'sucursales' }}</small></span>
                    </a>
                    <form method="POST" action="{{ route('security.companies.favorite', $favoriteCompany) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="favorite" value="0">
                        <button class="security-favorite-star is-on" type="submit" aria-label="Quitar {{ $favoriteCompany->name }} de favoritas" title="Quitar de favoritas">★</button>
                    </form>
                </div>
            @endforeach

            @if ($isDashboard)
                <button class="security-favorite-new" type="button" data-security-carousel-item data-security-company-open>
                    <span aria-hidden="true">+</span> Nueva empresa
                </button>
            @else
                <a class="security-favorite-new" href="{{ route('security.index', ['create_company' => 1]) }}" data-security-carousel-item>
                    <span aria-hidden="true">+</span> Nueva empresa
                </a>
            @endif
        </div>
        <button class="security-favorite-arrow" type="button" data-security-carousel-next aria-label="Ver favoritas siguientes">&rsaquo;</button>
    </div>
</section>

<div class="security-favorite-toolbar {{ $isDashboard ? '' : 'is-section' }}">
    <div class="security-favorite-company-filter">
        <label id="security-favorite-picker-label">Seleccionar empresa</label>
        <details class="security-favorite-picker" data-security-favorite-picker>
            <summary aria-labelledby="security-favorite-picker-label">
                <span>{{ $favoriteSelection?->name ?? 'Todas las empresas' }}</span>
                <span aria-hidden="true">⌄</span>
            </summary>
            <div class="security-favorite-picker-menu">
                <input type="search" placeholder="Buscar empresa..." aria-label="Buscar empresa" data-security-favorite-search>
                <div class="security-favorite-picker-list">
                    <a class="security-favorite-picker-all {{ $favoriteSelection ? '' : 'is-current' }}" href="{{ route('security.index', $favoriteAllQuery) }}">Todas las empresas <span aria-hidden="true">▦</span></a>
                    @foreach ($securityCompanies as $securityCompany)
                        <div class="security-favorite-picker-option" data-security-favorite-option data-search="{{ $securityCompany->name }}">
                            <a class="{{ $favoriteSelection?->is($securityCompany) ? 'is-current' : '' }}" href="{{ route('security.index', array_merge($favoriteBaseQuery, [$favoriteCompanyParameter => $securityCompany->id])) }}">{{ $securityCompany->name }}</a>
                            <form method="POST" action="{{ route('security.companies.favorite', $securityCompany) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="favorite" value="{{ $securityCompany->is_favorite ? 0 : 1 }}">
                                <button class="security-favorite-star {{ $securityCompany->is_favorite ? 'is-on' : '' }}" type="submit" aria-label="{{ $securityCompany->is_favorite ? 'Quitar' : 'Agregar' }} {{ $securityCompany->name }} {{ $securityCompany->is_favorite ? 'de' : 'a' }} favoritas" title="{{ $securityCompany->is_favorite ? 'Quitar de favoritas' : 'Agregar a favoritas' }}">{{ $securityCompany->is_favorite ? '★' : '☆' }}</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </details>
    </div>
    <button class="button ghost security-favorite-manage" type="button" aria-haspopup="dialog" aria-controls="security-favorites-dialog" data-security-favorites-manage><span aria-hidden="true">★</span> Administrar favoritas</button>
    @if ($isDashboard)
    <form class="security-favorite-date-filter" method="GET" action="{{ route('security.index') }}">
        @if ($selectedSecurityCompany)
            <input type="hidden" name="company" value="{{ $selectedSecurityCompany->id }}">
        @endif
        <label>Desde <input type="date" name="from_date" value="{{ $companyDashboardDateFrom }}"></label>
        <label>Hasta <input type="date" name="to_date" value="{{ $companyDashboardDateTo }}"></label>
        <label>Comparar con
            <select name="compare">
                @foreach ($comparisonLabels as $value => $label)
                    <option value="{{ $value }}" @selected($companyDashboardComparison === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <button class="button primary" type="submit">Actualizar</button>
    </form>
    @endif
</div>

<dialog class="security-favorites-dialog" id="security-favorites-dialog" aria-labelledby="security-favorites-title" data-security-favorites-dialog @if (old('_favorites_form') && $errors->any()) data-auto-open @endif>
    <form method="POST" action="{{ route('security.companies.favorites.update') }}" data-security-favorites-form>
        @csrf
        @method('PATCH')
        <input type="hidden" name="_favorites_form" value="1">
        <header>
            <div>
                <h2 id="security-favorites-title">Administrar favoritas</h2>
                <p>Selecciona las empresas que aparecerán en el carrusel.</p>
            </div>
            <button type="button" aria-label="Cerrar" data-security-favorites-close>&times;</button>
        </header>
        <div class="security-favorites-body">
            @if (old('_favorites_form') && $errors->any())
                <div class="security-favorites-errors" role="alert">{{ $errors->first() }}</div>
            @endif
            <label class="security-favorites-search">Buscar empresa
                <input type="search" placeholder="Nombre de la empresa" autocomplete="off" data-security-favorites-search>
            </label>
            <p class="security-favorites-count" aria-live="polite" data-security-favorites-count></p>
            <div class="security-favorites-list">
                @forelse ($securityCompanies as $securityCompany)
                    <label class="security-favorites-option" data-security-favorites-option data-search="{{ $securityCompany->name }}">
                        <input type="checkbox" name="favorite_ids[]" value="{{ $securityCompany->id }}" @checked(in_array((string) $securityCompany->id, $favoriteFormIds, true)) data-security-favorites-checkbox>
                        <span class="security-favorite-avatar is-tone-{{ ($loop->index % 5) + 1 }}" aria-hidden="true">{{ $securityCompany->initials() }}</span>
                        <span class="security-favorites-option-copy"><strong>{{ $securityCompany->name }}</strong><small>{{ $securityCompany->branches_count }} {{ $securityCompany->branches_count === 1 ? 'sucursal' : 'sucursales' }}</small></span>
                    </label>
                @empty
                    <p class="security-favorites-empty">No hay empresas registradas.</p>
                @endforelse
            </div>
            <p class="security-favorites-no-results" hidden data-security-favorites-no-results>Sin resultados para esta búsqueda.</p>
        </div>
        <footer>
            <button class="button ghost" type="button" data-security-favorites-close>Cancelar</button>
            <button class="button primary" type="submit">Guardar favoritas</button>
        </footer>
    </form>
</dialog>

@if ($isDashboard)
<section class="security-incident-dashboard" aria-labelledby="security-incident-dashboard-title">
    <header class="security-incident-heading">
        <div>
            <p class="eyebrow">SEGURIDAD Y VIGILANCIA</p>
            <h2 id="security-incident-dashboard-title">Incidencias de {{ $incidentScope }}</h2>
        </div>
        <button class="button primary" type="button" data-security-incident-create-open>+ Registrar incidencia</button>
    </header>

    <div class="security-incident-metrics" aria-label="Resumen de incidencias">
        <article class="security-incident-metric is-total"><span class="security-incident-metric-icon" aria-hidden="true">!</span><div><strong>{{ $dashboardIncidents->count() }}</strong><span>Incidencias</span><small>En el periodo seleccionado</small></div></article>
        <article class="security-incident-metric is-critical"><span class="security-incident-metric-icon" aria-hidden="true">▲</span><div><strong>{{ $criticalIncidents }}</strong><span>Críticas</span><small>Requieren atención</small></div></article>
        <article class="security-incident-metric is-pending"><span class="security-incident-metric-icon" aria-hidden="true">◷</span><div><strong>{{ $pendingIncidents }}</strong><span>Pendientes</span><small>En proceso de atención</small></div></article>
        <article class="security-incident-metric is-resolved"><span class="security-incident-metric-icon" aria-hidden="true">✓</span><div><strong>{{ $resolvedIncidents }}</strong><span>Resueltas</span><small>Atendidas y cerradas</small></div></article>
    </div>

    <div class="table-scroll security-incident-table-scroll">
        <table class="security-incident-table">
            <thead><tr><th>Prioridad</th><th>Empresa / sucursal</th><th>Incidencia</th><th>Cámara</th><th>Fecha y hora</th><th>Estado</th><th>Acción</th></tr></thead>
            <tbody>
                @forelse ($dashboardIncidents as $incident)
                    <tr>
                        <td><span class="security-incident-priority is-{{ $incident->priority }}">{{ $priorityLabels[$incident->priority] ?? 'Media' }}</span></td>
                        <td><strong>{{ $incident->securityCompany?->name }}</strong><small>{{ $incident->securityBranch?->name ?? 'Sin sucursal' }}</small></td>
                        <td>{{ $incident->title }}</td>
                        <td>{{ $incident->securityCamera?->name ?? 'Sin cámara' }}</td>
                        <td>{{ $incident->occurred_at->format('d/m/Y H:i') }}</td>
                        <td><span class="security-incident-status is-{{ $incident->status }}">{{ $incident->status === 'resolved' ? 'Resuelta' : 'Pendiente' }}</span></td>
                        <td><button class="button small ghost" type="button" data-security-incident-detail-open="{{ $incident->id }}" aria-controls="security-incident-detail-{{ $incident->id }}">Ver detalle</button></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="security-incident-empty">{{ $securityCompanies->isEmpty() ? 'No hay empresas registradas.' : 'Sin incidencias registradas en el periodo seleccionado.' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <footer>Mostrando {{ $dashboardIncidents->count() }} {{ $dashboardIncidents->count() === 1 ? 'incidencia' : 'incidencias' }} de {{ $selectedSecurityCompany ? '1 empresa' : $securityCompanies->count().' '.($securityCompanies->count() === 1 ? 'empresa' : 'empresas') }}.</footer>
</section>

<dialog class="security-incident-dialog" id="security-incident-create" data-security-incident-create @if ($errors->getBag('incident')->any()) data-auto-open @endif>
    <form method="POST" action="{{ route('security.incidents.store') }}" data-security-incident-form>
        @csrf
        <header><div><p class="eyebrow">SEGURIDAD Y VIGILANCIA</p><h2>Registrar incidencia</h2></div><button type="button" data-security-incident-close aria-label="Cerrar formulario">&times;</button></header>
        @if ($errors->getBag('incident')->any())
            <div class="security-incident-errors" role="alert">{{ $errors->getBag('incident')->first() }}</div>
        @endif
        <div class="security-incident-form-grid">
            <label>Empresa *
                <select name="security_company_id" required data-security-incident-company>
                    <option value="">Seleccionar empresa</option>
                    @foreach ($securityCompanies as $company)
                        <option value="{{ $company->id }}" @selected((string) old('security_company_id', $selectedSecurityCompany?->id) === (string) $company->id)>{{ $company->name }}</option>
                    @endforeach
                </select>
            </label>
            <label>Sucursal
                <select name="security_branch_id" data-security-incident-branch>
                    <option value="">Sin sucursal</option>
                    @foreach ($securityCompanies as $company)
                        @foreach ($company->branches as $branch)
                            <option value="{{ $branch->id }}" data-company-id="{{ $company->id }}" @selected((string) old('security_branch_id') === (string) $branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    @endforeach
                </select>
            </label>
            <label>Cámara
                <select name="security_camera_id" data-security-incident-camera>
                    <option value="">Sin cámara</option>
                    @foreach ($securityCompanies as $company)
                        @foreach ($company->branches as $branch)
                            @foreach ($branch->cameras as $camera)
                                <option value="{{ $camera->id }}" data-branch-id="{{ $branch->id }}" @selected((string) old('security_camera_id') === (string) $camera->id)>{{ $camera->name }}</option>
                            @endforeach
                        @endforeach
                    @endforeach
                </select>
            </label>
            <label>Prioridad *
                <select name="priority" required>
                    <option value="critical" @selected(old('priority') === 'critical')>Crítica</option>
                    <option value="medium" @selected(old('priority', 'medium') === 'medium')>Media</option>
                    <option value="low" @selected(old('priority') === 'low')>Baja</option>
                </select>
            </label>
            <label class="is-wide">Incidencia * <input name="title" value="{{ old('title') }}" maxlength="180" required></label>
            <label>Fecha y hora * <input type="datetime-local" name="occurred_at" value="{{ old('occurred_at', now()->format('Y-m-d\TH:i')) }}" required></label>
            <label class="is-wide">Descripción <textarea name="description" maxlength="3000" rows="3">{{ old('description') }}</textarea></label>
        </div>
        <footer><button class="button ghost" type="button" data-security-incident-close>Cancelar</button><button class="button primary" type="submit">Guardar incidencia</button></footer>
    </form>
</dialog>

@foreach ($dashboardIncidents as $incident)
    <dialog class="security-incident-dialog security-incident-detail" id="security-incident-detail-{{ $incident->id }}" data-security-incident-detail>
        <div class="security-incident-detail-content">
            <header><div><p class="eyebrow">INCIDENCIA #{{ $incident->id }}</p><h2>{{ $incident->title }}</h2></div><button type="button" data-security-incident-close aria-label="Cerrar detalle">&times;</button></header>
            <dl>
                <div><dt>Empresa</dt><dd>{{ $incident->securityCompany?->name }}</dd></div>
                <div><dt>Sucursal</dt><dd>{{ $incident->securityBranch?->name ?? 'Sin sucursal' }}</dd></div>
                <div><dt>Cámara</dt><dd>{{ $incident->securityCamera?->name ?? 'Sin cámara' }}</dd></div>
                <div><dt>Prioridad</dt><dd>{{ $priorityLabels[$incident->priority] ?? 'Media' }}</dd></div>
                <div><dt>Fecha y hora</dt><dd>{{ $incident->occurred_at->format('d/m/Y H:i') }}</dd></div>
                <div><dt>Estado</dt><dd>{{ $incident->status === 'resolved' ? 'Resuelta' : 'Pendiente' }}</dd></div>
            </dl>
            @if ($incident->description)<p>{{ $incident->description }}</p>@endif
            <footer>
                <button class="button ghost" type="button" data-security-incident-close>Cerrar</button>
                <form method="POST" action="{{ route('security.incidents.status.update', $incident) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ $incident->status === 'resolved' ? 'pending' : 'resolved' }}">
                    <button class="button primary" type="submit">{{ $incident->status === 'resolved' ? 'Reabrir incidencia' : 'Marcar resuelta' }}</button>
                </form>
            </footer>
        </div>
    </dialog>
@endforeach
@endif
