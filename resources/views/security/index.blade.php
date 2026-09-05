@extends('layouts.app')

@section('body')
    @php
        $securitySections = [
            'companies' => ['label' => 'Empresas', 'empty' => 'Sin incidencias registradas'],
            'branches' => ['label' => 'Sucursales', 'empty' => 'Sin sucursales registradas'],
            'cameras' => ['label' => 'Cámaras', 'empty' => 'Sin cámaras registradas'],
            'visualization' => ['label' => 'Visualización', 'empty' => 'Sin visualizaciones disponibles'],
            'analytics' => ['label' => 'Analíticos', 'empty' => 'Sin información analítica disponible'],
            'alerts' => ['label' => 'Alertas', 'empty' => 'Sin alertas registradas'],
            'users' => ['label' => 'Usuarios', 'empty' => 'Sin usuarios registrados'],
            'reports' => ['label' => 'Reportes', 'empty' => 'Sin reportes disponibles'],
            'configuration' => ['label' => 'Configuración', 'empty' => 'Sin configuración registrada'],
        ];
        $entityTypeLabels = ['company' => 'Empresa', 'business' => 'Negocio'];
        $securitySectionKey = isset($securitySectionKey) && array_key_exists($securitySectionKey, $securitySections)
            ? $securitySectionKey
            : (string) request()->query('section', 'companies');
        $securitySectionKey = array_key_exists($securitySectionKey, $securitySections) ? $securitySectionKey : 'companies';
        $securitySection = $securitySections[$securitySectionKey] ?? $securitySections['companies'];
    @endphp

    <x-app-shell title="Seguridad y Vigilancia">
        @if ($securitySectionKey !== 'companies')
            @include('security.partials.company-selector')
        @endif

        @if ($securitySectionKey === 'companies')
            @php
                $dashboardCompanies = $selectedSecurityCompany
                    ? $securityCompanies->filter(fn ($company) => $company->is($selectedSecurityCompany))
                    : $securityCompanies;
                $dashboardBranchCount = $dashboardCompanies->sum('branches_count');
                $dashboardCameraCount = $dashboardCompanies->sum('cameras_count');
                $dashboardScopeLabel = $selectedSecurityCompany?->name ?? 'Todas las empresas';
                $comparisonLabels = [
                    'previous_day' => 'Ayer',
                    'previous_week' => 'Semana anterior',
                    'previous_period' => 'Periodo anterior',
                ];
            @endphp

            <section class="security-companies-dashboard" data-security-carousel>
                <div class="security-carousel-shell">
                    <button class="security-carousel-nav" type="button" data-security-carousel-prev aria-label="Ver registros anteriores">&lsaquo;</button>

                    <div class="security-carousel-track" data-security-carousel-track>
                        <a
                            class="security-company-card security-company-card-all {{ $selectedSecurityCompany ? '' : 'is-active' }}"
                            href="{{ route('security.index', ['from_date' => $companyDashboardDateFrom, 'to_date' => $companyDashboardDateTo, 'compare' => $companyDashboardComparison]) }}"
                            data-security-carousel-item
                            @if (!$selectedSecurityCompany) aria-current="true" @endif
                        >
                            <span class="security-company-avatar" aria-hidden="true">&#9638;</span>
                            <span class="security-company-card-copy">
                                <strong>Todas</strong>
                                <small>Ver todas las empresas</small>
                            </span>
                        </a>

                        @foreach ($securityCompanies as $securityCompany)
                            <a
                                class="security-company-card is-tone-{{ ($loop->index % 5) + 1 }} {{ $selectedSecurityCompany?->is($securityCompany) ? 'is-active' : '' }}"
                                href="{{ route('security.index', ['company' => $securityCompany->id, 'from_date' => $companyDashboardDateFrom, 'to_date' => $companyDashboardDateTo, 'compare' => $companyDashboardComparison]) }}"
                                data-security-carousel-item
                                @if ($selectedSecurityCompany?->is($securityCompany)) aria-current="true" @endif
                            >
                                <span class="security-company-avatar" aria-hidden="true">{{ $securityCompany->initials() }}</span>
                                <span class="security-company-card-copy">
                                    <strong>{{ $securityCompany->name }}</strong>
                                    <small>{{ $securityCompany->branches_count }} {{ $securityCompany->branches_count === 1 ? 'sucursal' : 'sucursales' }}</small>
                                </span>
                            </a>
                        @endforeach

                        <button
                            class="security-company-card security-company-card-new"
                            type="button"
                            data-security-carousel-item
                            data-security-company-open
                        >
                            <span class="security-company-avatar" aria-hidden="true">+</span>
                            <span class="security-company-card-copy">
                                <strong>Nueva empresa</strong>
                                <small>Alta de Vigilancia</small>
                            </span>
                        </button>
                    </div>

                    <button class="security-carousel-nav" type="button" data-security-carousel-next aria-label="Ver registros siguientes">&rsaquo;</button>
                </div>

                <form class="security-company-dashboard-filters" method="GET" action="{{ route('security.index') }}">
                    @if ($selectedSecurityCompany)
                        <input type="hidden" name="company" value="{{ $selectedSecurityCompany->id }}">
                    @endif
                    <fieldset>
                        <legend>Periodo de consulta</legend>
                        <div class="security-company-date-range">
                            <label>
                                <span>Desde</span>
                                <input type="date" name="from_date" value="{{ $companyDashboardDateFrom }}">
                            </label>
                            <span aria-hidden="true">a</span>
                            <label>
                                <span>Hasta</span>
                                <input type="date" name="to_date" value="{{ $companyDashboardDateTo }}">
                            </label>
                        </div>
                    </fieldset>
                    <label>
                        Comparar con
                        <select name="compare">
                            @foreach ($comparisonLabels as $value => $label)
                                <option value="{{ $value }}" @selected($companyDashboardComparison === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button class="button ghost security-company-refresh" type="submit">Actualizar datos</button>
                </form>

                <div class="security-company-metrics" aria-label="Indicadores generales">
                    <article class="security-company-metric-card">
                        <span>Empresas registradas</span>
                        <div><strong>{{ $dashboardCompanies->count() }}</strong><i class="is-blue" aria-hidden="true">E</i></div>
                        <small>Datos del catálogo</small>
                    </article>
                    <article class="security-company-metric-card">
                        <span>Sucursales totales</span>
                        <div><strong>{{ $dashboardBranchCount }}</strong><i class="is-green" aria-hidden="true">S</i></div>
                        <small>Datos del catálogo</small>
                    </article>
                    <article class="security-company-metric-card">
                        <span>Cámaras totales</span>
                        <div><strong data-security-dashboard-camera-count>{{ $dashboardCameraCount }}</strong><i class="is-violet" aria-hidden="true">C</i></div>
                        <small>{{ $dashboardCameraCount > 0 ? 'Datos del catálogo' : 'Sin cámaras registradas' }}</small>
                    </article>
                    <article class="security-company-metric-card">
                        <span>Personas hoy</span>
                        <div><strong>0</strong><i class="is-amber" aria-hidden="true">P</i></div>
                        <small>Sin telemetría disponible</small>
                    </article>
                    <article class="security-company-metric-card">
                        <span>Alertas activas</span>
                        <div><strong>0</strong><i class="is-red" aria-hidden="true">A</i></div>
                        <small>Sin alertas registradas</small>
                    </article>
                </div>

                <div class="security-company-insights">
                    <article class="panel security-company-insight security-company-flow-widget">
                        <header>
                            <div>
                                <p class="eyebrow">FLUJO DE PERSONAS</p>
                                <h3>Entradas y salidas</h3>
                                <small>{{ $dashboardScopeLabel }}</small>
                            </div>
                            <div class="security-analytics-legend" aria-label="Leyenda">
                                <span class="is-blue">Entradas</span>
                                <span class="is-green">Salidas</span>
                            </div>
                        </header>
                        <div class="security-company-flow-chart" role="img" aria-label="Gráfica de entradas y salidas sin datos">
                            <svg viewBox="0 0 620 230" aria-hidden="true" focusable="false">
                                <g class="security-chart-grid">
                                    <line x1="42" y1="24" x2="602" y2="24" />
                                    <line x1="42" y1="72" x2="602" y2="72" />
                                    <line x1="42" y1="120" x2="602" y2="120" />
                                    <line x1="42" y1="168" x2="602" y2="168" />
                                    <line x1="42" y1="168" x2="602" y2="168" class="security-chart-axis" />
                                </g>
                                <g class="security-chart-labels">
                                    <text x="42" y="205">00:00</text>
                                    <text x="154" y="205">04:00</text>
                                    <text x="266" y="205">08:00</text>
                                    <text x="378" y="205">12:00</text>
                                    <text x="490" y="205">16:00</text>
                                    <text x="602" y="205" text-anchor="end">20:00</text>
                                </g>
                            </svg>
                            <span>Sin datos para el periodo seleccionado</span>
                        </div>
                    </article>

                    <article class="panel security-company-insight security-company-occupancy-widget">
                        <header>
                            <div>
                                <p class="eyebrow">OCUPACIÓN</p>
                                <h3>Ocupación actual</h3>
                                <small>{{ $dashboardScopeLabel }}</small>
                            </div>
                        </header>
                        <div class="security-company-gauge" role="img" aria-label="Ocupación actual sin datos">
                            <svg viewBox="0 0 220 126" aria-hidden="true" focusable="false">
                                <path d="M 24 108 A 86 86 0 0 1 196 108" />
                            </svg>
                            <span><strong>0</strong>Personas</span>
                        </div>
                        <p>Sin datos de capacidad u ocupación.</p>
                    </article>

                    <article class="panel security-company-insight security-company-ranking-widget">
                        <header>
                            <div>
                                <p class="eyebrow">COMPARATIVO</p>
                                <h3>Top 5 empresas por entradas</h3>
                                <small>{{ $comparisonLabels[$companyDashboardComparison] }}</small>
                            </div>
                        </header>
                        @if ($dashboardCompanies->isNotEmpty())
                            <ol class="security-company-ranking">
                                @foreach ($dashboardCompanies->take(5) as $securityCompany)
                                    <li>
                                        <span>{{ $loop->iteration }}</span>
                                        <strong>{{ $securityCompany->name }}</strong>
                                        <i><b style="width: 0%"></b></i>
                                        <small>Sin datos</small>
                                    </li>
                                @endforeach
                            </ol>
                        @else
                            <div class="security-company-insight-empty">
                                <strong>Sin empresas registradas</strong>
                            </div>
                        @endif
                    </article>
                </div>

                <section class="panel security-company-summary-panel">
                    <header>
                        <div>
                            <p class="eyebrow">RESUMEN OPERATIVO</p>
                            <h3>Resumen por empresa</h3>
                        </div>
                        <span>{{ $dashboardScopeLabel }}</span>
                    </header>
                    <div class="table-scroll security-company-summary-scroll">
                        <table class="security-company-summary-table" data-no-column-tools>
                            <thead>
                                <tr>
                                    <th>Empresa o negocio</th>
                                    <th>Sucursales</th>
                                    <th>Cámaras</th>
                                    <th>Entradas hoy</th>
                                    <th>Salidas hoy</th>
                                    <th>Ocupación actual</th>
                                    <th>Alertas activas</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dashboardCompanies as $securityCompany)
                                    <tr>
                                        <td><strong>{{ $securityCompany->name }}</strong></td>
                                        <td>{{ $securityCompany->branches_count }}</td>
                                        <td data-security-company-camera-count="{{ $securityCompany->cameras_count }}">{{ $securityCompany->cameras_count }}</td>
                                        <td><span class="security-company-data-empty">Sin datos</span></td>
                                        <td><span class="security-company-data-empty">Sin datos</span></td>
                                        <td><span class="security-company-data-empty">Sin datos</span></td>
                                        <td>0</td>
                                        <td><span class="status success">Registrada</span></td>
                                        <td>
                                            <a
                                                class="button small ghost"
                                                href="{{ route('security.index', ['company' => $securityCompany->id, 'from_date' => $companyDashboardDateFrom, 'to_date' => $companyDashboardDateTo, 'compare' => $companyDashboardComparison]) }}"
                                            >Ver detalle</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="security-company-summary-empty">Sin empresas o negocios registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <footer>
                        Mostrando {{ $dashboardCompanies->count() }} de {{ $securityCompanies->count() }} empresas o negocios
                    </footer>
                </section>
            </section>

            @if ($selectedSecurityCompany)
                <section class="panel security-detail-panel">
                    <header class="security-detail-heading">
                        <div>
                            <p class="eyebrow">EMPRESA O NEGOCIO SELECCIONADO</p>
                            <h2>{{ $selectedSecurityCompany->name }}</h2>
                            <p>{{ $selectedSecurityCompany->legal_name ?: 'Sin razón social capturada' }}</p>
                        </div>
                        <span class="status {{ $selectedSecurityCompany->financeCompany ? 'success' : 'primary' }}">
                            {{ $selectedSecurityCompany->financeCompany ? 'Relacionada con Finanzas' : 'Registro independiente' }}
                        </span>
                        <a
                            class="button small ghost security-detail-close"
                            href="{{ route('security.index', ['from_date' => $companyDashboardDateFrom, 'to_date' => $companyDashboardDateTo, 'compare' => $companyDashboardComparison]) }}"
                        >Cerrar detalle</a>
                    </header>

                    <dl class="security-company-detail-grid">
                        <div>
                            <dt>Tipo</dt>
                            <dd>{{ $entityTypeLabels[$selectedSecurityCompany->entity_type] ?? 'Empresa' }}</dd>
                        </div>
                        <div>
                            <dt>RFC</dt>
                            <dd>{{ $selectedSecurityCompany->rfc ?: 'Pendiente' }}</dd>
                        </div>
                        <div>
                            <dt>Responsable de vigilancia</dt>
                            <dd>{{ $selectedSecurityCompany->contact_name ?: 'Pendiente' }}</dd>
                        </div>
                        <div>
                            <dt>Teléfono</dt>
                            <dd>{{ $selectedSecurityCompany->contact_phone ?: 'Pendiente' }}</dd>
                        </div>
                        <div>
                            <dt>Correo</dt>
                            <dd>{{ $selectedSecurityCompany->contact_email ?: 'Pendiente' }}</dd>
                        </div>
                        <div>
                            <dt>Empresa relacionada en Finanzas</dt>
                            <dd>{{ $selectedSecurityCompany->financeCompany?->name ?: 'Sin relación' }}</dd>
                        </div>
                        <div class="security-company-address">
                            <dt>Dirección</dt>
                            <dd>{{ $selectedSecurityCompany->address ?: 'Pendiente' }}</dd>
                        </div>
                    </dl>
                </section>
            @endif

            <dialog class="security-company-dialog" id="security-company-dialog" @if ($errors->any()) data-auto-open @endif>
                <form class="security-company-form" method="POST" action="{{ route('security.companies.store') }}">
                    @csrf
                    <header class="security-company-form-heading">
                        <div>
                            <p class="eyebrow">ALTA DE VIGILANCIA</p>
                            <h2>Nueva empresa o negocio</h2>
                        </div>
                        <button class="security-dialog-close" type="button" data-security-company-close aria-label="Cerrar formulario">&times;</button>
                    </header>

                    <div class="security-company-form-grid">
                        <label>
                            Nombre comercial *
                            <input name="name" value="{{ old('name') }}" maxlength="255" required autofocus>
                        </label>
                        <label>
                            Tipo de registro *
                            <select name="entity_type" required>
                                <option value="company" @selected(old('entity_type', 'company') === 'company')>Empresa</option>
                                <option value="business" @selected(old('entity_type') === 'business')>Negocio</option>
                            </select>
                        </label>
                        <label>
                            Razón social
                            <input name="legal_name" value="{{ old('legal_name') }}" maxlength="255">
                        </label>
                        <label>
                            RFC
                            <input name="rfc" value="{{ old('rfc') }}" maxlength="20" autocomplete="off">
                        </label>
                        <label class="security-company-form-wide">
                            Dirección
                            <textarea name="address" maxlength="1000" rows="2">{{ old('address') }}</textarea>
                        </label>
                        <label>
                            Responsable de vigilancia
                            <input name="contact_name" value="{{ old('contact_name') }}" maxlength="255">
                        </label>
                        <label>
                            Teléfono
                            <input name="contact_phone" value="{{ old('contact_phone') }}" maxlength="40" inputmode="tel">
                        </label>
                        <label>
                            Correo electrónico
                            <input name="contact_email" value="{{ old('contact_email') }}" maxlength="255" type="email">
                        </label>
                        <label>
                            Empresa relacionada en Finanzas (opcional)
                            <select name="finance_company_id">
                                <option value="">Sin relación</option>
                                @foreach ($financeCompanies as $financeCompany)
                                    <option value="{{ $financeCompany->id }}" @selected((string) old('finance_company_id') === (string) $financeCompany->id)>
                                        {{ $financeCompany->name }} · {{ $financeCompany->rfc }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <div class="security-company-form-actions">
                        <button class="button ghost" type="button" data-security-company-close>Cancelar</button>
                        <button class="button primary" type="submit">Guardar empresa o negocio</button>
                    </div>
                </form>
            </dialog>
        @elseif ($securitySectionKey === 'branches')
            <section class="security-branch-camera-view">
                <div class="security-branch-toolbar">
                    <form class="security-branch-camera-filter" method="GET" action="{{ route('security.index') }}" data-security-branch-camera-filter>
                        <input type="hidden" name="section" value="branches">
                        <input type="hidden" name="company_id" value="{{ $selectedSectionCompany?->id }}">

                        <label>
                            Filtrar por sucursal
                            <select name="branch_id" required data-security-branch-select @disabled(!$selectedSectionCompany || $securityBranches->isEmpty())>
                                @if ($securityBranches->isEmpty())
                                    <option value="">Sin sucursales disponibles</option>
                                @endif
                                @foreach ($securityBranches as $securityBranch)
                                    <option value="{{ $securityBranch->id }}" @selected($selectedCameraBranch?->is($securityBranch))>
                                        {{ $securityBranch->name }}{{ $securityBranch->code ? ' · '.$securityBranch->code : '' }} · {{ (int) $securityBranch->cameras_count }} {{ (int) $securityBranch->cameras_count === 1 ? 'cámara' : 'cámaras' }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </form>

                    <div class="security-branch-toolbar-actions">
                        <button class="button ghost security-branch-catalog-button" type="button" data-security-branch-catalog-open @disabled(!$selectedSectionCompany)>
                            Catálogo de sucursales
                        </button>
                        <button class="button primary security-branch-add-button" type="button" data-security-branch-open @disabled($securityCompanies->isEmpty())>
                            <span aria-hidden="true">+</span>
                            Agregar sucursal
                        </button>
                    </div>
                </div>

                @if ($selectedCameraBranch)
                    <div class="security-branch-camera-results" data-security-branch-camera-results>
                        @php
                            $cameraPreviewScenes = ['entrance', 'parking', 'checkout', 'warehouse'];
                            $cameraPreviewTiles = $selectedCameraBranch->cameras
                                ->values()
                                ->map(fn ($camera, $cameraIndex) => [
                                    'code' => 'CAM-'.str_pad((string) ($cameraIndex + 1), 2, '0', STR_PAD_LEFT),
                                    'name' => $camera->name,
                                    'zone' => parse_url($camera->stream_url, PHP_URL_HOST) ?: 'URL configurada',
                                    'scene' => $cameraPreviewScenes[$cameraIndex % count($cameraPreviewScenes)],
                                ])
                                ->all();
                            $cameraPreviewCount = count($cameraPreviewTiles);
                        @endphp

                        <div class="security-camera-preview" data-security-camera-preview>
                            <div class="security-camera-preview-summary">
                                <span class="security-carousel-total">
                                    {{ $cameraPreviewCount }}
                                    {{ $cameraPreviewCount === 1 ? 'cámara' : 'cámaras' }}
                                </span>
                            </div>

                            @if ($cameraPreviewCount > 0)
                                <div class="security-camera-preview-grid" data-security-camera-preview-grid>
                                    @foreach ($cameraPreviewTiles as $cameraPreview)
                                        <article class="security-camera-tile">
                                            <div class="security-camera-screen is-{{ $cameraPreview['scene'] }} is-offline">
                                                <div class="security-camera-screen-top">
                                                    <span class="security-camera-live-state">
                                                        <i aria-hidden="true"></i>
                                                        DESCONECTADA
                                                    </span>
                                                    <span>{{ $cameraPreview['code'] }}</span>
                                                </div>

                                                <div class="security-camera-scene" aria-hidden="true">
                                                    <span class="is-one"></span>
                                                    <span class="is-two"></span>
                                                    <span class="is-three"></span>
                                                    <span class="is-four"></span>
                                                </div>

                                                <div class="security-camera-no-signal">
                                                    <span aria-hidden="true">!</span>
                                                    <strong>Sin conexión</strong>
                                                </div>

                                                <time datetime="{{ now()->toIso8601String() }}">{{ now()->format('d/m/Y H:i:s') }}</time>
                                            </div>

                                            <footer>
                                                <div>
                                                    <strong>{{ $cameraPreview['name'] }}</strong>
                                                    <small>{{ $cameraPreview['zone'] }}</small>
                                                </div>
                                                <span>Sin conexión</span>
                                            </footer>
                                        </article>
                                    @endforeach
                                </div>
                            @else
                                <p class="security-camera-preview-empty" data-security-camera-preview-empty>
                                    No hay cámaras de vigilancia registradas para esta sucursal.
                                </p>
                            @endif
                        </div>
                    </div>
                @elseif ($securityBranches->isNotEmpty())
                    <p class="security-branch-camera-hint">Selecciona una sucursal para consultar sus cámaras.</p>
                @elseif ($selectedSectionCompany)
                    <p class="security-branch-camera-hint">No hay sucursales disponibles para {{ $selectedSectionCompany->name }}.</p>
                @else
                    <p class="security-branch-camera-hint">Selecciona primero una empresa o negocio.</p>
                @endif

                @php
                    $branchFormEditing = old('branch_form_mode') === 'edit'
                        ? $securityBranches->firstWhere('id', (int) old('editing_branch_id'))
                        : null;
                    $branchFormIsEditing = $branchFormEditing !== null;
                    $branchCameraUrlRows = old('camera_urls_submitted')
                        ? old('camera_urls', [])
                        : ($branchFormEditing?->cameras
                            ->map(fn ($camera) => ['name' => $camera->name, 'url' => $camera->stream_url])
                            ->values()
                            ->all() ?? []);
                    $branchCameraUrlRows = $branchCameraUrlRows ?: [['name' => '', 'url' => '']];
                @endphp

                <dialog class="security-branch-dialog" id="security-branch-dialog" @if ($errors->any()) data-auto-open @endif>
                    <form
                        class="security-branch-create-form"
                        method="POST"
                        action="{{ $branchFormIsEditing ? route('security.branches.update', $branchFormEditing) : route('security.branches.store') }}"
                        data-security-branch-form
                        data-security-branch-create-action="{{ route('security.branches.store') }}"
                    >
                        @csrf
                        <input type="hidden" name="_method" value="PATCH" data-security-branch-method @disabled(!$branchFormIsEditing)>
                        <input type="hidden" name="branch_form_mode" value="{{ $branchFormIsEditing ? 'edit' : 'create' }}" data-security-branch-form-mode>
                        <input type="hidden" name="editing_branch_id" value="{{ $branchFormEditing?->id }}" data-security-branch-editing-id @disabled(!$branchFormIsEditing)>
                        <input type="hidden" name="camera_urls_submitted" value="1">
                        <header class="security-branch-dialog-heading">
                            <span class="security-branch-dialog-icon" aria-hidden="true" data-security-branch-dialog-icon>{{ $branchFormIsEditing ? 'E' : '+' }}</span>
                            <div>
                                <h2 data-security-branch-dialog-title>{{ $branchFormIsEditing ? 'Editar sucursal' : 'Nueva sucursal' }}</h2>
                                <p data-security-branch-dialog-description>
                                    {{ $branchFormIsEditing ? 'Actualiza la información de la sucursal seleccionada.' : 'Completa la información para registrar una nueva sucursal.' }}
                                </p>
                            </div>
                            <button class="security-dialog-close" type="button" data-security-branch-close aria-label="Cerrar formulario">&times;</button>
                        </header>

                        <div class="security-branch-dialog-content">
                            <div class="security-branch-primary-grid">
                                <section class="security-branch-form-section">
                                    <header>
                                        <span class="security-branch-step-number">1</span>
                                        <h3>Información general</h3>
                                    </header>
                                    <label>
                                        Nombre de la sucursal *
                                        <input name="name" value="{{ old('name', $branchFormEditing?->name) }}" placeholder="Ej. Sucursal Centro" maxlength="255" required autofocus>
                                    </label>
                                    <label>
                                        Empresa *
                                        <input type="hidden" name="security_company_id" value="{{ $selectedSectionCompany?->id }}">
                                        <span class="security-branch-selected-company" aria-readonly="true">
                                            {{ $selectedSectionCompany?->name ?? 'Sin empresa seleccionada' }}
                                        </span>
                                    </label>
                                    <label>
                                        Código / Clave
                                        <input name="code" value="{{ old('code', $branchFormEditing?->code) }}" placeholder="Ej. SUC-001 (opcional)" maxlength="50">
                                    </label>
                                    <label>
                                        Descripción
                                        <textarea name="description" placeholder="Describe la sucursal (opcional)" maxlength="1500">{{ old('description', $branchFormEditing?->description) }}</textarea>
                                    </label>
                                </section>

                                <section class="security-branch-form-section security-branch-location-section">
                                    <header>
                                        <span class="security-branch-step-number">2</span>
                                        <h3>Ubicación</h3>
                                    </header>
                                    <label class="security-branch-field-wide">
                                        Dirección *
                                        <input name="address" value="{{ old('address', $branchFormEditing?->address) }}" placeholder="Ej. Av. Reforma 123, Col. Centro" maxlength="1000" required>
                                    </label>
                                    <label>
                                        País *
                                        <select name="country" required>
                                            <option value="">Selecciona un país</option>
                                            @foreach (['México', 'Estados Unidos', 'Canadá', 'Otro'] as $country)
                                                <option value="{{ $country }}" @selected(old('country', $branchFormEditing?->country ?? 'México') === $country)>{{ $country }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <label>
                                        Estado / Provincia *
                                        <input name="state" value="{{ old('state', $branchFormEditing?->state) }}" placeholder="Ej. Nuevo León" maxlength="120" required>
                                    </label>
                                    <label>
                                        Ciudad *
                                        <input name="city" value="{{ old('city', $branchFormEditing?->city) }}" placeholder="Ej. Monterrey" maxlength="120" required>
                                    </label>
                                    <label>
                                        Código postal
                                        <input name="postal_code" value="{{ old('postal_code', $branchFormEditing?->postal_code) }}" placeholder="Ej. 64000" maxlength="20" inputmode="numeric">
                                    </label>
                                    <div class="security-branch-map security-branch-field-wide" data-security-branch-map aria-label="Vista de ubicación">
                                        <input type="search" placeholder="Buscar en el mapa" aria-label="Buscar en el mapa" data-security-branch-map-search>
                                        <span class="security-branch-map-marker" aria-hidden="true"></span>
                                        <div class="security-branch-map-controls" aria-label="Controles del mapa">
                                            <button type="button" aria-label="Acercar mapa" data-security-branch-map-zoom="in">+</button>
                                            <button type="button" aria-label="Alejar mapa" data-security-branch-map-zoom="out">&minus;</button>
                                        </div>
                                    </div>
                                </section>
                            </div>

                            <div class="security-branch-secondary-grid">
                                <section class="security-branch-form-section">
                                    <header>
                                        <span class="security-branch-step-number is-green">3</span>
                                        <h3>Información de contacto</h3>
                                    </header>
                                    <label>
                                        Teléfono
                                        <input name="phone" value="{{ old('phone', $branchFormEditing?->phone) }}" placeholder="Ej. 81 1234 5678" maxlength="40" inputmode="tel">
                                    </label>
                                    <label>
                                        Correo electrónico
                                        <input name="email" type="email" value="{{ old('email', $branchFormEditing?->email) }}" placeholder="Ej. contacto@sucursal.com" maxlength="255">
                                    </label>
                                </section>

                                <section class="security-branch-form-section">
                                    <header>
                                        <span class="security-branch-step-number is-amber">4</span>
                                        <h3>Configuración</h3>
                                    </header>
                                    <label>
                                        Zona horaria *
                                        <select name="timezone" required>
                                            <option value="America/Mexico_City" @selected(old('timezone', $branchFormEditing?->timezone ?? 'America/Mexico_City') === 'America/Mexico_City')>(GMT-06:00) Ciudad de México</option>
                                            <option value="America/Cancun" @selected(old('timezone', $branchFormEditing?->timezone) === 'America/Cancun')>(GMT-05:00) Cancún</option>
                                            <option value="America/Chihuahua" @selected(old('timezone', $branchFormEditing?->timezone) === 'America/Chihuahua')>(GMT-07:00) Chihuahua</option>
                                            <option value="America/Tijuana" @selected(old('timezone', $branchFormEditing?->timezone) === 'America/Tijuana')>(GMT-08:00) Tijuana</option>
                                        </select>
                                    </label>
                                    <label>
                                        Estatus *
                                        <select name="status" required>
                                            <option value="active" @selected(old('status', $branchFormEditing?->status ?? 'active') === 'active')>Activa</option>
                                            <option value="inactive" @selected(old('status', $branchFormEditing?->status) === 'inactive')>Inactiva</option>
                                        </select>
                                    </label>
                                </section>

                                <section class="security-branch-form-section security-branch-advanced-section">
                                    <header>
                                        <span class="security-branch-step-number is-violet">5</span>
                                        <h3>Configuración avanzada</h3>
                                    </header>
                                    <input type="hidden" name="analytics_enabled" value="0">
                                    <label class="security-branch-toggle-option">
                                        <input name="analytics_enabled" type="checkbox" value="1" @checked(old('analytics_enabled', $branchFormEditing?->analytics_enabled ?? false))>
                                        <span>
                                            <strong>Habilitar analíticos por defecto</strong>
                                            <small>Activa los analíticos en las cámaras nuevas.</small>
                                        </span>
                                    </label>
                                    <input type="hidden" name="alerts_enabled" value="0">
                                    <label class="security-branch-toggle-option">
                                        <input name="alerts_enabled" type="checkbox" value="1" @checked(old('alerts_enabled', $branchFormEditing?->alerts_enabled ?? false))>
                                        <span>
                                            <strong>Habilitar alertas por defecto</strong>
                                            <small>Activa las alertas en las cámaras nuevas.</small>
                                        </span>
                                    </label>
                                </section>
                            </div>

                            <section class="security-branch-camera-urls-section">
                                <header>
                                    <div class="security-branch-camera-urls-title">
                                        <span class="security-branch-step-number is-cyan">6</span>
                                        <div>
                                            <h3>URLs de cámaras de vigilancia</h3>
                                            <p>Agrega enlaces HTTPS, RTSP o RTSPS para esta sucursal.</p>
                                        </div>
                                    </div>
                                    <button class="button ghost" type="button" data-security-camera-url-add>
                                        <span aria-hidden="true">+</span>
                                        Agregar cámara
                                    </button>
                                </header>

                                <div class="security-camera-url-list" data-security-camera-url-list>
                                    @foreach ($branchCameraUrlRows as $cameraUrlIndex => $cameraUrlRow)
                                        <div class="security-camera-url-row" data-security-camera-url-row>
                                            <label>
                                                <span data-security-camera-url-number>Cámara {{ $cameraUrlIndex + 1 }}</span>
                                                <input
                                                    name="camera_urls[{{ $cameraUrlIndex }}][name]"
                                                    value="{{ $cameraUrlRow['name'] ?? '' }}"
                                                    placeholder="Ej. Entrada principal"
                                                    maxlength="120"
                                                >
                                            </label>
                                            <label>
                                                URL de transmisión
                                                <input
                                                    name="camera_urls[{{ $cameraUrlIndex }}][url]"
                                                    value="{{ $cameraUrlRow['url'] ?? '' }}"
                                                    placeholder="https://... o rtsp://..."
                                                    maxlength="2048"
                                                    inputmode="url"
                                                    autocomplete="off"
                                                >
                                            </label>
                                            <button
                                                class="security-camera-url-remove"
                                                type="button"
                                                data-security-camera-url-remove
                                                aria-label="Eliminar URL de cámara"
                                                title="Eliminar URL"
                                            >&times;</button>
                                        </div>
                                    @endforeach
                                </div>

                                <template data-security-camera-url-template>
                                    <div class="security-camera-url-row" data-security-camera-url-row>
                                        <label>
                                            <span data-security-camera-url-number>Cámara</span>
                                            <input
                                                name="camera_urls[__INDEX__][name]"
                                                placeholder="Ej. Entrada principal"
                                                maxlength="120"
                                            >
                                        </label>
                                        <label>
                                            URL de transmisión
                                            <input
                                                name="camera_urls[__INDEX__][url]"
                                                placeholder="https://... o rtsp://..."
                                                maxlength="2048"
                                                inputmode="url"
                                                autocomplete="off"
                                            >
                                        </label>
                                        <button
                                            class="security-camera-url-remove"
                                            type="button"
                                            data-security-camera-url-remove
                                            aria-label="Eliminar URL de cámara"
                                            title="Eliminar URL"
                                        >&times;</button>
                                    </div>
                                </template>
                            </section>
                        </div>

                        <footer class="security-branch-form-actions">
                            <button class="button ghost" type="button" data-security-branch-close>Cancelar</button>
                            <button class="button primary" type="submit" data-security-branch-submit>
                                {{ $branchFormIsEditing ? 'Guardar cambios' : 'Guardar sucursal' }}
                            </button>
                        </footer>
                    </form>
                </dialog>

                @if (session('security_branch_updated'))
                    <dialog
                        class="security-branch-success-dialog"
                        id="security-branch-success-dialog"
                        aria-labelledby="security-branch-success-title"
                        data-auto-open
                    >
                        <div class="security-branch-success-content">
                            <span class="security-branch-success-icon" aria-hidden="true">✓</span>
                            <h2 id="security-branch-success-title">Cambios guardados con exito</h2>
                            <button
                                class="security-dialog-close"
                                type="button"
                                data-security-branch-success-close
                                aria-label="Cerrar confirmación"
                            >&times;</button>
                        </div>
                    </dialog>
                @endif

                <dialog class="security-branch-catalog-dialog" id="security-branch-catalog-dialog">
                    <div class="security-branch-catalog-shell">
                        <header class="security-branch-dialog-heading">
                            <span class="security-branch-dialog-icon" aria-hidden="true">S</span>
                            <div>
                                <h2>Catálogo de sucursales</h2>
                                <p>{{ $selectedSectionCompany?->name ?? 'Sin empresa seleccionada' }}</p>
                            </div>
                            <button class="security-dialog-close" type="button" data-security-branch-catalog-close aria-label="Cerrar catálogo">&times;</button>
                        </header>

                        <div class="security-branch-catalog-content">
                            @if ($securityBranches->isNotEmpty())
                                <div class="table-scroll security-branch-catalog-table-scroll">
                                    <table class="security-branch-catalog-table" data-no-column-tools>
                                        <thead>
                                            <tr>
                                                <th>Sucursal</th>
                                                <th>Clave</th>
                                                <th>Ubicación</th>
                                                <th>Contacto</th>
                                                <th>Estatus</th>
                                                <th>Cámaras activas</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($securityBranches as $securityBranch)
                                                @php
                                                    $securityBranchEditPayload = [
                                                        'id' => $securityBranch->id,
                                                        'name' => $securityBranch->name,
                                                        'code' => $securityBranch->code,
                                                        'description' => $securityBranch->description,
                                                        'address' => $securityBranch->address,
                                                        'country' => $securityBranch->country,
                                                        'state' => $securityBranch->state,
                                                        'city' => $securityBranch->city,
                                                        'postal_code' => $securityBranch->postal_code,
                                                        'phone' => $securityBranch->phone,
                                                        'email' => $securityBranch->email,
                                                        'timezone' => $securityBranch->timezone,
                                                        'status' => $securityBranch->status,
                                                        'analytics_enabled' => (bool) $securityBranch->analytics_enabled,
                                                        'alerts_enabled' => (bool) $securityBranch->alerts_enabled,
                                                        'cameras' => $securityBranch->cameras
                                                            ->map(fn ($camera) => ['name' => $camera->name, 'url' => $camera->stream_url])
                                                            ->values()
                                                            ->all(),
                                                    ];
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong>{{ $securityBranch->name }}</strong>
                                                        @if ($securityBranch->description)
                                                            <small>{{ $securityBranch->description }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $securityBranch->code ?: 'Sin clave' }}</td>
                                                    <td>
                                                        {{ collect([$securityBranch->city, $securityBranch->state])->filter()->implode(', ') ?: ($securityBranch->address ?: 'Sin ubicación') }}
                                                    </td>
                                                    <td>
                                                        {{ $securityBranch->email ?: ($securityBranch->phone ?: 'Sin contacto') }}
                                                    </td>
                                                    <td>
                                                        <span class="status {{ $securityBranch->status === 'inactive' ? 'canceled' : 'approved' }}">
                                                            {{ $securityBranch->status === 'inactive' ? 'Inactiva' : 'Activa' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="security-branch-active-cameras"
                                                            data-security-active-cameras="0/{{ $securityBranch->cameras_count }}"
                                                            aria-label="0 de {{ $securityBranch->cameras_count }} cámaras activas"
                                                        >
                                                            <i aria-hidden="true"></i>
                                                            <strong>0</strong>
                                                            <small>de {{ $securityBranch->cameras_count }}</small>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="security-branch-row-actions">
                                                            <a class="button ghost small" href="{{ route('security.index', ['section' => 'branches', 'company_id' => $selectedSectionCompany?->id, 'branch_id' => $securityBranch->id]) }}">
                                                                Ver cámaras
                                                            </a>
                                                            <button
                                                                class="button ghost small"
                                                                type="button"
                                                                data-security-branch-edit="{{ json_encode($securityBranchEditPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}"
                                                                data-security-branch-update-action="{{ route('security.branches.update', $securityBranch) }}"
                                                            >
                                                                Editar
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="empty-state security-company-empty">
                                    <strong>Sin sucursales registradas</strong>
                                    <p>Agrega la primera sucursal para esta empresa.</p>
                                </div>
                            @endif
                        </div>

                        <footer class="security-branch-form-actions">
                            <button class="button ghost" type="button" data-security-branch-catalog-close>Cerrar</button>
                        </footer>
                    </div>
                </dialog>
            </section>
        @elseif ($securitySectionKey === 'analytics')
            @php
                $analyticsCameraCount = $selectedAnalyticsBranch
                    ? (int) $selectedAnalyticsBranch->cameras_count
                    : (int) ($selectedAnalyticsCompany?->branches->sum('cameras_count') ?? 0);
            @endphp

            <section class="panel security-analytics-overview" data-security-analytics-filter data-analytics-today="{{ now()->toDateString() }}">
                <header class="security-analytics-overview-heading">
                    <div>
                        <p class="eyebrow">SEGURIDAD Y VIGILANCIA</p>
                        <h2>Analíticas - Resumen general</h2>
                        <p class="security-analytics-breadcrumb">
                            Empresas
                            <span aria-hidden="true">&rsaquo;</span>
                            <strong>{{ $selectedAnalyticsCompany?->name ?? 'Sin selección' }}</strong>
                        </p>
                    </div>
                    <div class="security-analytics-date-controls">
                        <button class="button small" type="button" data-security-analytics-today>Hoy</button>
                        <label>
                            <span>Fecha de consulta</span>
                            <input
                                type="date"
                                name="analytics_date"
                                value="{{ $analyticsDate }}"
                                form="security-analytics-filter-form"
                                data-security-analytics-date
                            >
                        </label>
                    </div>
                </header>

                <form id="security-analytics-filter-form" class="security-analytics-filters" method="GET" action="{{ route('security.index') }}">
                    <input type="hidden" name="section" value="analytics">
                    <input type="hidden" name="show_analytics" value="1">
                    <input type="hidden" name="company_id" value="{{ $selectedAnalyticsCompany?->id }}">
                    <label>
                        Empresa o negocio
                        <span class="security-analytics-selected-company">
                            {{ $selectedAnalyticsCompany?->name ?? 'Sin empresas registradas' }}
                        </span>
                    </label>
                    <label>
                        Sucursal
                        <select name="branch_id" data-security-analytics-branch required @disabled(!$selectedAnalyticsCompany || $selectedAnalyticsCompany->branches->isEmpty())>
                            <option value="">Selecciona una sucursal</option>
                            @foreach ($selectedAnalyticsCompany?->branches ?? collect() as $securityBranch)
                                <option value="{{ $securityBranch->id }}" @selected($selectedAnalyticsBranch?->is($securityBranch))>
                                    {{ $securityBranch->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <button class="button primary security-analytics-submit" type="submit" @disabled(!$selectedAnalyticsCompany || $selectedAnalyticsCompany->branches->isEmpty())>
                        VER Analíticas
                    </button>
                </form>
            </section>

            <section class="security-analytics-dashboard" aria-label="Resumen visual de analíticas">
                <article class="panel security-analytics-widget security-analytics-line-widget">
                    <header class="security-analytics-widget-heading">
                        <div>
                            <p class="eyebrow">FLUJO DE PERSONAS</p>
                            <h3>Entradas y salidas</h3>
                        </div>
                        <div class="security-analytics-legend" aria-label="Leyenda">
                            <span class="is-blue">Entradas</span>
                            <span class="is-green">Salidas</span>
                        </div>
                    </header>
                    <div class="security-analytics-chart" role="img" aria-label="Gráfica de entradas y salidas sin datos">
                        <svg viewBox="0 0 520 210" aria-hidden="true" focusable="false">
                            <g class="security-chart-grid">
                                <line x1="42" y1="24" x2="502" y2="24" />
                                <line x1="42" y1="70" x2="502" y2="70" />
                                <line x1="42" y1="116" x2="502" y2="116" />
                                <line x1="42" y1="162" x2="502" y2="162" />
                                <line x1="42" y1="162" x2="502" y2="162" class="security-chart-axis" />
                            </g>
                            <g class="security-chart-labels">
                                <text x="42" y="192">00:00</text>
                                <text x="134" y="192">04:00</text>
                                <text x="226" y="192">08:00</text>
                                <text x="318" y="192">12:00</text>
                                <text x="410" y="192">16:00</text>
                                <text x="502" y="192" text-anchor="end">20:00</text>
                            </g>
                        </svg>
                        <span class="security-analytics-no-data">Sin datos para la fecha seleccionada</span>
                    </div>
                </article>

                <article class="panel security-analytics-widget security-analytics-line-widget">
                    <header class="security-analytics-widget-heading">
                        <div>
                            <p class="eyebrow">OCUPACIÓN</p>
                            <h3>Ocupación a lo largo del día</h3>
                        </div>
                        <div class="security-analytics-legend" aria-label="Leyenda">
                            <span class="is-violet">Ocupación</span>
                            <span class="is-amber">Capacidad</span>
                        </div>
                    </header>
                    <div class="security-analytics-chart" role="img" aria-label="Gráfica de ocupación sin datos">
                        <svg viewBox="0 0 520 210" aria-hidden="true" focusable="false">
                            <g class="security-chart-grid">
                                <line x1="42" y1="24" x2="502" y2="24" />
                                <line x1="42" y1="70" x2="502" y2="70" />
                                <line x1="42" y1="116" x2="502" y2="116" />
                                <line x1="42" y1="162" x2="502" y2="162" />
                                <line x1="42" y1="162" x2="502" y2="162" class="security-chart-axis" />
                            </g>
                            <g class="security-chart-labels">
                                <text x="42" y="192">00:00</text>
                                <text x="134" y="192">04:00</text>
                                <text x="226" y="192">08:00</text>
                                <text x="318" y="192">12:00</text>
                                <text x="410" y="192">16:00</text>
                                <text x="502" y="192" text-anchor="end">20:00</text>
                            </g>
                        </svg>
                        <span class="security-analytics-no-data">Sin datos para la fecha seleccionada</span>
                    </div>
                </article>

                <article class="panel security-analytics-widget security-analytics-distribution-widget">
                    <header class="security-analytics-widget-heading">
                        <div>
                            <p class="eyebrow">DISTRIBUCIÓN</p>
                            <h3>Distribución por tipo de cámara</h3>
                        </div>
                    </header>
                    <div class="security-analytics-distribution">
                        <div class="security-analytics-donut" role="img" aria-label="{{ $analyticsCameraCount }} cámaras registradas">
                            <span><strong data-security-analytics-camera-count>{{ $analyticsCameraCount }}</strong>Total</span>
                        </div>
                        <ul class="security-analytics-distribution-list">
                            <li><span class="security-distribution-dot is-blue"></span>Entradas <strong>0%</strong></li>
                            <li><span class="security-distribution-dot is-green"></span>Salidas <strong>0%</strong></li>
                            <li><span class="security-distribution-dot is-amber"></span>Áreas comunes <strong>0%</strong></li>
                        </ul>
                    </div>
                </article>

                <article class="panel security-analytics-widget security-analytics-ranking-widget">
                    <header class="security-analytics-widget-heading">
                        <div>
                            <p class="eyebrow">COMPARATIVO</p>
                            <h3>Top 5 sucursales por entradas</h3>
                        </div>
                        <span class="security-analytics-widget-date">{{ $analyticsDate }}</span>
                    </header>
                    @if ($selectedAnalyticsCompany && $selectedAnalyticsCompany->branches->isNotEmpty())
                        <ol class="security-analytics-ranking">
                            @foreach ($selectedAnalyticsCompany->branches->take(5) as $securityBranch)
                                <li>
                                    <span class="security-ranking-position">{{ $loop->iteration }}</span>
                                    <strong>{{ $securityBranch->name }}</strong>
                                    <span class="security-ranking-bar"><i style="width: 0%"></i></span>
                                    <span class="security-ranking-value">Sin datos</span>
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <div class="security-analytics-widget-empty">
                            <strong>Sin sucursales registradas</strong>
                            <p>Registra sucursales para habilitar el comparativo.</p>
                        </div>
                    @endif
                </article>
            </section>

            @if ($analyticsRequested && $selectedAnalyticsCompany && $selectedAnalyticsBranch)
                <section class="panel security-analytics-results" data-security-analytics-results>
                    <header class="security-analytics-results-heading">
                        <div>
                            <p class="eyebrow">ANALÍTICAS POR SUCURSAL</p>
                            <h2>{{ $selectedAnalyticsBranch->name }}</h2>
                            <p>{{ $selectedAnalyticsCompany->name }}</p>
                        </div>
                        <span class="security-analytics-count">{{ count($analyticsParameters) }} parámetros</span>
                    </header>

                    <div class="table-scroll security-analytics-table-scroll">
                        <table class="security-analytics-table" data-no-column-tools>
                            <colgroup>
                                <col class="security-analytics-number-column">
                                <col class="security-analytics-name-column">
                                <col>
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Parámetro analítico</th>
                                    <th>Qué mide</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($analyticsParameters as $parameter)
                                    <tr>
                                        <td><span class="security-analytics-number">{{ $parameter['number'] }}</span></td>
                                        <td><strong>{{ $parameter['name'] }}</strong></td>
                                        <td>{{ $parameter['description'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @else
                <section class="panel security-analytics-empty-panel">
                    <div class="empty-state security-company-empty">
                        @if ($securityCompanies->isEmpty())
                            <strong>Sin empresas o negocios registrados</strong>
                            <p>Registra una empresa en el catálogo de Vigilancia.</p>
                        @elseif (!$selectedAnalyticsCompany || $selectedAnalyticsCompany->branches->isEmpty())
                            <strong>Sin sucursales registradas</strong>
                            <p>Registra las sucursales que se utilizarán en el filtro.</p>
                        @elseif ($analyticsRequested)
                            <strong>Selecciona una empresa o negocio y una sucursal válidos</strong>
                        @else
                            <strong>Selecciona una empresa o negocio y una sucursal</strong>
                            <p>Presiona VER Analíticas para consultar sus parámetros.</p>
                        @endif
                    </div>
                </section>
            @endif
        @else
            <section class="panel stack">
                <div>
                    <p class="eyebrow">SEGURIDAD Y VIGILANCIA</p>
                    <h2>{{ $securitySection['label'] }}</h2>
                    <p class="security-section-company-context">
                        {{ $selectedSectionCompany?->name ?? 'Sin empresa o negocio seleccionado' }}
                    </p>
                </div>

                <div class="metrics-grid">
                    <article class="metric-card"><span>Personal activo</span><strong>0</strong></article>
                    <article class="metric-card"><span>Incidencias abiertas</span><strong>0</strong></article>
                    <article class="metric-card"><span>Recorridos del día</span><strong>0</strong></article>
                </div>

                <div class="empty-state">
                    <strong>{{ $securitySection['empty'] }}</strong>
                    @if ($selectedSectionCompany)
                        <p>Consulta correspondiente a {{ $selectedSectionCompany->name }}.</p>
                    @endif
                </div>
            </section>
        @endif

        <style>
            .security-companies-dashboard { min-width: 0; display: grid; gap: 18px; }
            .security-section-company-selector { min-width: 0; display: grid; gap: 13px; }
            .security-company-selector-empty { border-style: dashed; }
            .security-carousel-total { flex: 0 0 auto; min-height: 30px; padding: 6px 10px; border: 1px solid #b7dfca; border-radius: 999px; background: #e8f6ee; color: var(--success); font-size: .82rem; font-weight: 850; }
            .security-carousel-shell { min-width: 0; display: grid; grid-template-columns: 32px minmax(0, 1fr) 32px; align-items: center; gap: 9px; }
            .security-carousel-track { min-width: 0; display: flex; gap: 9px; padding: 1px; overflow-x: auto; overscroll-behavior-x: contain; scroll-behavior: smooth; scroll-snap-type: x mandatory; scrollbar-width: none; }
            .security-carousel-track::-webkit-scrollbar { display: none; }
            .security-carousel-nav { width: 32px; height: 32px; min-height: 32px; padding: 0; border: 1px solid var(--line); border-radius: 999px; background: #fff; color: var(--primary-strong); box-shadow: 0 5px 14px rgba(35, 48, 73, .09); font-size: 1.2rem; font-weight: 850; cursor: pointer; }
            .security-carousel-nav:hover:not(:disabled) { border-color: var(--primary); background: #eef8fb; }
            .security-carousel-nav:disabled { cursor: default; opacity: .35; }
            .security-company-card { flex: 0 0 205px; min-height: 86px; padding: 11px 12px; border: 1px solid var(--line); border-radius: 7px; background: #fff; color: var(--text); display: flex; align-items: center; gap: 10px; text-align: left; text-decoration: none; scroll-snap-align: start; cursor: pointer; }
            .security-company-card:hover,
            .security-company-card.is-active { border-color: #246de0; background: #f6f9ff; box-shadow: inset 0 0 0 1px rgba(36, 109, 224, .2); }
            .security-company-card-new { appearance: none; border-style: dashed; color: var(--primary-strong); font: inherit; }
            .security-company-card-new .security-company-avatar { border: 1px solid #246de0; background: #fff; font-size: 1.2rem; font-weight: 500; }
            .security-company-avatar { width: 42px; height: 42px; flex: 0 0 42px; border-radius: 8px; background: #e5efff; color: #246de0; display: grid; place-items: center; font-size: .78rem; font-weight: 900; }
            .security-company-card.is-tone-1 .security-company-avatar { background: #e1f5ec; color: #178b5b; }
            .security-company-card.is-tone-2 .security-company-avatar { background: #efe8ff; color: #6f3ed6; }
            .security-company-card.is-tone-3 .security-company-avatar { background: #fff0d9; color: #bd7400; }
            .security-company-card.is-tone-4 .security-company-avatar { background: #fde8e8; color: #c53d3d; }
            .security-company-card.is-tone-5 .security-company-avatar { background: #e4efff; color: #2568c9; }
            .security-company-card-copy { min-width: 0; display: grid; gap: 5px; }
            .security-company-card-copy strong { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: .84rem; line-height: 1.2; }
            .security-company-card-copy small { overflow: hidden; color: var(--muted); text-overflow: ellipsis; white-space: nowrap; font-size: .7rem; }
            .security-company-dashboard-filters { padding: 12px 0; border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); display: grid; grid-template-columns: minmax(330px, auto) minmax(190px, 240px) 150px; align-items: end; justify-content: start; gap: 14px; }
            .security-company-dashboard-filters fieldset { min-width: 0; margin: 0; padding: 0; border: 0; }
            .security-company-dashboard-filters legend,
            .security-company-dashboard-filters > label { color: var(--text); font-size: .76rem; font-weight: 850; }
            .security-company-date-range { display: flex; align-items: flex-end; gap: 8px; }
            .security-company-date-range > label { min-width: 145px; gap: 4px; }
            .security-company-date-range > label > span { color: var(--muted); font-size: .66rem; font-weight: 900; text-transform: uppercase; }
            .security-company-date-range > span { padding-bottom: 10px; color: var(--muted); font-size: .76rem; }
            .security-company-dashboard-filters input,
            .security-company-dashboard-filters select { min-height: 38px; padding: 7px 9px; }
            .security-company-refresh { min-height: 38px; }
            .security-company-metrics { min-width: 0; display: grid; grid-template-columns: repeat(5, minmax(140px, 1fr)); gap: 12px; }
            .security-company-metric-card { min-width: 0; min-height: 118px; padding: 13px; border: 1px solid var(--line); border-radius: 8px; background: #fff; display: grid; align-content: space-between; gap: 7px; }
            .security-company-metric-card > span { color: var(--muted); font-size: .74rem; font-weight: 800; }
            .security-company-metric-card > div { min-width: 0; display: flex; align-items: center; justify-content: space-between; gap: 10px; }
            .security-company-metric-card strong { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 1.5rem; }
            .security-company-metric-card i { width: 36px; height: 36px; flex: 0 0 36px; border-radius: 8px; display: grid; place-items: center; font-size: .76rem; font-style: normal; font-weight: 900; }
            .security-company-metric-card i.is-blue { background: #e5efff; color: #246de0; }
            .security-company-metric-card i.is-green { background: #e1f5ec; color: #178b5b; }
            .security-company-metric-card i.is-violet { background: #efe8ff; color: #6f3ed6; }
            .security-company-metric-card i.is-amber { background: #fff0d9; color: #bd7400; }
            .security-company-metric-card i.is-red { background: #fde8e8; color: #c53d3d; }
            .security-company-metric-card small { color: var(--muted); font-size: .67rem; }
            .security-company-insights { min-width: 0; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
            .security-company-insight { min-width: 0; min-height: 286px !important; padding: 16px; gap: 12px; overflow: hidden; }
            .security-company-flow-widget { grid-column: 1 / -1; }
            .security-company-insight > header,
            .security-company-summary-panel > header { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; }
            .security-company-insight h3,
            .security-company-insight p,
            .security-company-summary-panel h3,
            .security-company-summary-panel p { margin-top: 0; margin-bottom: 0; }
            .security-company-insight h3,
            .security-company-summary-panel h3 { margin-top: 2px; font-size: 1rem; }
            .security-company-insight header small { margin-top: 4px; display: block; color: var(--muted); font-size: .7rem; }
            .security-company-flow-chart { min-width: 0; min-height: 214px; position: relative; display: grid; place-items: center; }
            .security-company-flow-chart svg { width: 100%; height: 100%; min-height: 214px; }
            .security-company-flow-chart > span { position: absolute; top: 45%; left: 50%; max-width: 250px; padding: 7px 10px; border: 1px solid #dce4ed; border-radius: 6px; background: rgba(255, 255, 255, .94); color: var(--muted); transform: translate(-50%, -50%); text-align: center; font-size: .74rem; font-weight: 800; }
            .security-company-gauge { width: min(250px, 100%); min-height: 148px; margin: 8px auto 0; position: relative; display: grid; place-items: end center; }
            .security-company-gauge svg { width: 100%; position: absolute; inset: 0; }
            .security-company-gauge path { fill: none; stroke: #e3e8ef; stroke-linecap: round; stroke-width: 18; }
            .security-company-gauge > span { padding-bottom: 10px; display: grid; justify-items: center; color: var(--muted); font-size: .75rem; font-weight: 800; }
            .security-company-gauge strong { color: var(--text); font-size: 1.7rem; }
            .security-company-occupancy-widget > p { color: var(--muted); text-align: center; font-size: .72rem; }
            .security-company-ranking { margin: 0; padding: 5px 0 0; display: grid; gap: 12px; list-style: none; }
            .security-company-ranking li { min-width: 0; display: grid; grid-template-columns: 23px minmax(110px, .9fr) minmax(80px, 1fr) 58px; align-items: center; gap: 8px; }
            .security-company-ranking li > span { width: 23px; height: 23px; border-radius: 6px; background: #eef2f6; color: var(--muted); display: grid; place-items: center; font-size: .68rem; font-weight: 900; }
            .security-company-ranking strong { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: .76rem; }
            .security-company-ranking li > i { height: 7px; border-radius: 999px; background: #e4e9f0; overflow: hidden; }
            .security-company-ranking li > i > b { height: 100%; display: block; border-radius: inherit; background: #246de0; }
            .security-company-ranking li > small { color: var(--muted); text-align: right; font-size: .65rem; font-weight: 800; }
            .security-company-insight-empty { min-height: 190px; border: 1px dashed var(--line); border-radius: 7px; display: grid; place-items: center; color: var(--muted); }
            .security-company-summary-panel { min-height: 0 !important; padding: 16px; gap: 12px; }
            .security-company-summary-panel > header { align-items: center; }
            .security-company-summary-panel > header > span { max-width: 280px; padding: 5px 8px; border-radius: 999px; background: #eef2f6; color: var(--muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: .7rem; font-weight: 850; }
            .security-company-summary-scroll { border: 1px solid var(--line); border-radius: 8px; }
            .security-company-summary-table { min-width: 1100px; }
            .security-company-summary-table th { background: #f7f9fc; }
            .security-company-summary-table th,
            .security-company-summary-table td { padding: 9px 10px; }
            .security-company-data-empty { color: var(--muted); font-size: .72rem; font-weight: 750; }
            .security-company-summary-empty { padding: 28px !important; color: var(--muted); text-align: center; }
            .security-company-summary-panel > footer { color: var(--muted); font-size: .72rem; }
            .security-detail-panel { min-height: 0 !important; }
            .security-detail-heading,
            .security-company-form-heading,
            .security-company-form-actions { display: flex; align-items: center; justify-content: space-between; gap: 14px; }
            .security-detail-heading { align-items: flex-start; display: grid; grid-template-columns: minmax(0, 1fr) auto auto; }
            .security-detail-heading h2,
            .security-detail-heading p,
            .security-company-form-heading h2,
            .security-company-form-heading p { margin-top: 0; margin-bottom: 0; }
            .security-detail-heading h2 { font-size: 1.35rem; }
            .security-detail-heading > div > p:last-child { margin-top: 5px; color: var(--muted); }
            .security-detail-close { white-space: nowrap; }
            .security-company-detail-grid { margin: 0; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); border: 1px solid var(--line); border-radius: 8px; overflow: hidden; }
            .security-company-detail-grid > div { min-width: 0; padding: 12px 14px; border-right: 1px solid var(--line); border-bottom: 1px solid var(--line); }
            .security-company-detail-grid > div:nth-child(3n) { border-right: 0; }
            .security-company-detail-grid dt { margin-bottom: 5px; color: var(--muted); font-size: .72rem; font-weight: 900; text-transform: uppercase; }
            .security-company-detail-grid dd { margin: 0; overflow-wrap: anywhere; font-weight: 750; }
            .security-company-address { grid-column: 1 / -1; border-bottom: 0 !important; border-right: 0 !important; }
            .security-company-empty { border: 1px dashed var(--line); border-radius: 8px; background: #f8fbff; }
            .security-company-empty p { margin: 0; }
            .security-company-dialog { width: min(860px, calc(100vw - 32px)); max-height: calc(100vh - 32px); padding: 0; border: 0; border-radius: 8px; color: var(--text); box-shadow: 0 24px 70px rgba(24, 34, 53, .25); }
            .security-company-dialog::backdrop { background: rgba(16, 43, 58, .38); }
            .security-company-form { padding: 20px; display: grid; gap: 18px; }
            .security-company-form-heading { align-items: flex-start; padding-right: 42px; position: relative; }
            .security-company-form-heading h2 { font-size: 1.35rem; }
            .security-dialog-close { position: absolute; top: 0; right: 0; width: 34px; height: 34px; padding: 0; border: 1px solid var(--line); border-radius: 8px; background: #fff; color: var(--text); font-size: 1.25rem; line-height: 1; cursor: pointer; }
            .security-dialog-close:hover { border-color: #f1b8b4; background: #fdecec; color: var(--danger); }
            .security-company-form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
            .security-company-form-wide { grid-column: 1 / -1; }
            .security-company-form textarea { min-height: 72px; resize: vertical; }
            .security-company-form-actions { justify-content: flex-end; padding-top: 16px; border-top: 1px solid var(--line); }
            .security-analytics-overview,
            .security-analytics-results,
            .security-analytics-empty-panel { min-height: 0 !important; }
            .security-section-heading,
            .security-analytics-results-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; }
            .security-section-heading h2,
            .security-section-heading p,
            .security-analytics-results-heading h2,
            .security-analytics-results-heading p { margin-top: 0; margin-bottom: 0; }
            .security-section-heading h2,
            .security-analytics-results-heading h2 { font-size: 1.35rem; }
            .security-branch-camera-view { min-width: 0; display: grid; gap: 18px; }
            .security-branch-toolbar { min-width: 0; display: flex; align-items: end; justify-content: space-between; gap: 18px; }
            .security-branch-camera-filter { width: min(520px, 100%); display: grid; grid-template-columns: minmax(260px, 1fr); align-items: end; gap: 12px; }
            .security-branch-toolbar-actions { flex: 0 0 auto; display: flex; align-items: center; justify-content: flex-end; gap: 8px; }
            .security-branch-catalog-button { min-width: 176px; }
            .security-branch-add-button { flex: 0 0 auto; min-width: 154px; }
            .security-branch-add-button > span { font-size: 1.12rem; line-height: 1; }
            .security-branch-camera-results { min-width: 0; }
            .security-branch-camera-hint { margin: 0; color: var(--muted); font-size: .82rem; font-weight: 750; }
            .security-camera-preview { min-width: 0; display: grid; gap: 12px; }
            .security-camera-preview-summary { min-width: 0; display: flex; justify-content: flex-end; }
            .security-camera-preview-empty { min-height: 92px; margin: 0; padding: 20px; border: 1px dashed #cbd9e7; border-radius: 8px; color: var(--muted); display: grid; place-items: center; text-align: center; font-size: .8rem; font-weight: 750; }
            .security-camera-preview-grid { min-width: 0; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
            .security-camera-tile { min-width: 0; border: 1px solid #cbd9e7; border-radius: 8px; background: #fff; overflow: hidden; box-shadow: 0 5px 16px rgba(18, 46, 61, .06); }
            .security-camera-screen { aspect-ratio: 16 / 9; min-height: 128px; position: relative; overflow: hidden; background: #1b303a; color: #f4fafb; isolation: isolate; }
            .security-camera-screen::before { content: ''; position: absolute; z-index: 3; inset: 0; pointer-events: none; opacity: .11; background: repeating-linear-gradient(0deg, transparent 0 3px, #fff 4px); }
            .security-camera-screen::after { content: ''; position: absolute; z-index: 1; right: -8%; bottom: -32%; left: -8%; height: 62%; border-top: 1px solid rgba(188, 222, 229, .55); background: #29444d; transform: perspective(180px) rotateX(58deg); transform-origin: bottom; }
            .security-camera-screen-top { position: absolute; z-index: 5; top: 9px; right: 10px; left: 10px; display: flex; align-items: center; justify-content: space-between; gap: 8px; font-size: .62rem; font-weight: 900; }
            .security-camera-live-state { display: inline-flex; align-items: center; gap: 5px; }
            .security-camera-live-state i { width: 6px; height: 6px; border-radius: 999px; background: #ff5a5a; box-shadow: 0 0 0 3px rgba(255, 90, 90, .14); }
            .security-camera-screen.is-offline .security-camera-live-state i { background: #aeb9bd; box-shadow: none; }
            .security-camera-screen.is-configured .security-camera-live-state i { background: #58a6ff; box-shadow: 0 0 0 3px rgba(88, 166, 255, .14); }
            .security-camera-screen time { position: absolute; z-index: 5; right: 10px; bottom: 8px; font-size: .58rem; font-variant-numeric: tabular-nums; font-weight: 800; }
            .security-camera-scene { position: absolute; z-index: 2; inset: 24% 8% 19%; overflow: hidden; }
            .security-camera-scene span { position: absolute; display: block; border: 1px solid rgba(188, 222, 229, .62); background: rgba(109, 157, 166, .2); }
            .security-camera-screen.is-entrance { background: #18313b; }
            .security-camera-screen.is-entrance .is-one { width: 29%; height: 88%; left: 19%; bottom: 0; }
            .security-camera-screen.is-entrance .is-two { width: 29%; height: 88%; right: 19%; bottom: 0; }
            .security-camera-screen.is-entrance .is-three { width: 1px; height: 76%; top: 12%; left: 50%; background: rgba(188, 222, 229, .62); }
            .security-camera-screen.is-entrance .is-four { width: 72%; height: 9px; right: 14%; bottom: 0; background: #6b858a; }
            .security-camera-screen.is-parking { background: #20343d; }
            .security-camera-screen.is-parking .is-one { width: 28%; height: 36%; left: 5%; bottom: 8%; border-radius: 12px 12px 3px 3px; background: #668087; }
            .security-camera-screen.is-parking .is-two { width: 28%; height: 36%; right: 5%; bottom: 8%; border-radius: 12px 12px 3px 3px; background: #566d76; }
            .security-camera-screen.is-parking .is-three { width: 2px; height: 96%; top: 30%; left: 50%; border: 0; background: #e7d188; transform: rotate(62deg); }
            .security-camera-screen.is-parking .is-four { width: 2px; height: 75%; top: 44%; left: 29%; border: 0; background: rgba(231, 209, 136, .78); transform: rotate(62deg); }
            .security-camera-screen.is-checkout { background: #20383a; }
            .security-camera-screen.is-checkout .is-one { width: 90%; height: 22%; left: 5%; bottom: 4%; background: #567278; }
            .security-camera-screen.is-checkout .is-two { width: 20%; height: 52%; left: 13%; bottom: 23%; background: #738b8e; }
            .security-camera-screen.is-checkout .is-three { width: 20%; height: 52%; left: 40%; bottom: 23%; background: #668184; }
            .security-camera-screen.is-checkout .is-four { width: 20%; height: 52%; right: 13%; bottom: 23%; background: #738b8e; }
            .security-camera-screen.is-warehouse { background: #26343a; }
            .security-camera-screen.is-warehouse .is-one,
            .security-camera-screen.is-warehouse .is-two,
            .security-camera-screen.is-warehouse .is-three { width: 22%; height: 84%; bottom: 0; background: repeating-linear-gradient(0deg, rgba(109, 130, 136, .62) 0 14%, rgba(37, 54, 60, .72) 15% 21%); }
            .security-camera-screen.is-warehouse .is-one { left: 4%; }
            .security-camera-screen.is-warehouse .is-two { left: 39%; }
            .security-camera-screen.is-warehouse .is-three { right: 4%; }
            .security-camera-screen.is-warehouse .is-four { width: 72%; height: 2px; right: 14%; bottom: 2%; border: 0; background: #c1a55e; }
            .security-camera-screen.is-offline .security-camera-scene { opacity: .28; }
            .security-camera-no-signal { position: absolute; z-index: 4; inset: 0; display: grid; place-content: center; justify-items: center; gap: 6px; background: rgba(17, 28, 33, .72); }
            .security-camera-no-signal span { width: 26px; height: 26px; display: grid; place-items: center; border: 1px solid rgba(255, 255, 255, .45); border-radius: 999px; font-size: .82rem; font-weight: 900; }
            .security-camera-no-signal strong { font-size: .72rem; }
            .security-camera-tile footer { min-width: 0; min-height: 58px; padding: 10px 11px; display: flex; align-items: center; justify-content: space-between; gap: 8px; }
            .security-camera-tile footer > div { min-width: 0; display: grid; gap: 2px; }
            .security-camera-tile footer strong,
            .security-camera-tile footer small { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .security-camera-tile footer strong { font-size: .78rem; }
            .security-camera-tile footer small { color: var(--muted); font-size: .65rem; font-weight: 700; }
            .security-camera-tile footer > span { flex: 0 0 auto; color: var(--muted); font-size: .62rem; font-weight: 800; white-space: nowrap; }
            .security-branch-dialog { width: min(1180px, calc(100vw - 32px)); max-height: calc(100vh - 28px); padding: 0; border: 0; border-radius: 8px; color: var(--text); box-shadow: 0 24px 70px rgba(24, 34, 53, .28); overflow: hidden; }
            .security-branch-dialog::backdrop { background: rgba(16, 43, 58, .58); }
            .security-branch-success-dialog { width: min(470px, calc(100vw - 32px)); padding: 0; border: 0; border-radius: 8px; color: var(--text); box-shadow: 0 24px 70px rgba(24, 34, 53, .3); }
            .security-branch-success-dialog::backdrop { background: rgba(16, 43, 58, .5); }
            .security-branch-success-content { min-height: 104px; padding: 24px 58px 24px 24px; position: relative; display: flex; align-items: center; gap: 14px; background: #fff; }
            .security-branch-success-content h2 { margin: 0; font-size: 1.08rem; }
            .security-branch-success-icon { width: 40px; height: 40px; flex: 0 0 40px; border-radius: 999px; background: #e3f5ee; color: #14845a; display: grid; place-items: center; font-size: 1.15rem; font-weight: 900; }
            .security-branch-success-content .security-dialog-close { top: 12px; right: 12px; }
            .security-branch-create-form { max-height: calc(100vh - 28px); display: grid; grid-template-rows: auto minmax(0, 1fr) auto; background: #fff; }
            .security-branch-dialog-heading { min-width: 0; padding: 18px 58px 18px 24px; border-bottom: 1px solid var(--line); position: relative; display: flex; align-items: center; gap: 14px; }
            .security-branch-dialog-heading h2,
            .security-branch-dialog-heading p { margin: 0; }
            .security-branch-dialog-heading h2 { font-size: 1.42rem; }
            .security-branch-dialog-heading p { margin-top: 4px; color: var(--muted); font-size: .82rem; }
            .security-branch-dialog-icon { width: 44px; height: 44px; flex: 0 0 44px; border-radius: 8px; background: #e5efff; color: #246de0; display: grid; place-items: center; font-size: 1.45rem; font-weight: 500; }
            .security-branch-dialog-heading .security-dialog-close { top: 4px; }
            .security-branch-dialog-content { min-height: 0; padding: 20px 26px; display: grid; gap: 22px; overflow-y: auto; }
            .security-branch-primary-grid { min-width: 0; display: grid; grid-template-columns: minmax(300px, .8fr) minmax(520px, 1.2fr); gap: 24px; }
            .security-branch-secondary-grid { min-width: 0; padding-top: 20px; border-top: 1px solid var(--line); display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0; }
            .security-branch-form-section { min-width: 0; padding: 0 24px; display: grid; align-content: start; gap: 12px; }
            .security-branch-primary-grid > .security-branch-form-section:first-child,
            .security-branch-secondary-grid > .security-branch-form-section:first-child { padding-left: 0; }
            .security-branch-primary-grid > .security-branch-form-section + .security-branch-form-section,
            .security-branch-secondary-grid > .security-branch-form-section + .security-branch-form-section { border-left: 1px solid var(--line); }
            .security-branch-primary-grid > .security-branch-form-section:last-child,
            .security-branch-secondary-grid > .security-branch-form-section:last-child { padding-right: 0; }
            .security-branch-form-section > header { min-height: 30px; margin-bottom: 4px; display: flex; align-items: center; gap: 9px; }
            .security-branch-form-section > header h3 { margin: 0; font-size: .94rem; }
            .security-branch-step-number { width: 26px; height: 26px; flex: 0 0 26px; border-radius: 7px; background: #e5efff; color: #246de0; display: grid; place-items: center; font-size: .76rem; font-weight: 900; }
            .security-branch-step-number.is-green { background: #e1f5ec; color: #178b5b; }
            .security-branch-step-number.is-amber { background: #fff0d9; color: #bd7400; }
            .security-branch-step-number.is-violet { background: #efe8ff; color: #6f3ed6; }
            .security-branch-step-number.is-cyan { background: #dff3f6; color: #14788a; }
            .security-branch-form-section label { min-width: 0; gap: 5px; font-size: .79rem; }
            .security-branch-form-section input,
            .security-branch-form-section select,
            .security-branch-form-section textarea { width: 100%; }
            .security-branch-selected-company { min-height: 40px; padding: 9px 11px; border: 1px solid #cbd9e7; border-radius: 8px; background: #f5f8fb; color: var(--text); display: flex; align-items: center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 800; }
            .security-branch-form-section textarea { min-height: 92px; resize: vertical; }
            .security-branch-location-section { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .security-branch-location-section > header,
            .security-branch-field-wide { grid-column: 1 / -1; }
            .security-branch-map { min-height: 154px; border: 1px solid #cbd9e7; border-radius: 8px; background-color: #eef2f5; background-image: linear-gradient(rgba(255, 255, 255, .7) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, .7) 1px, transparent 1px); background-size: 34px 34px; position: relative; overflow: hidden; }
            .security-branch-map::before,
            .security-branch-map::after { content: ''; position: absolute; height: 12px; border-radius: 999px; background: rgba(255, 255, 255, .92); box-shadow: 0 0 0 1px rgba(203, 217, 231, .65); transform: rotate(-18deg); }
            .security-branch-map::before { width: 72%; top: 52%; left: -8%; }
            .security-branch-map::after { width: 62%; top: 30%; right: -12%; transform: rotate(24deg); }
            .security-branch-map > input { width: min(180px, calc(100% - 76px)); min-height: 36px; position: absolute; z-index: 2; top: 10px; left: 10px; background: rgba(255, 255, 255, .96); }
            .security-branch-map-marker { width: 24px; height: 24px; position: absolute; z-index: 2; top: 53%; left: 50%; border: 5px solid #fff; border-radius: 999px 999px 999px 2px; background: #246de0; box-shadow: 0 4px 12px rgba(36, 109, 224, .28); transform: translate(-50%, -50%) rotate(-45deg); }
            .security-branch-map-controls { position: absolute; z-index: 2; right: 10px; bottom: 10px; border: 1px solid #cbd9e7; border-radius: 7px; background: #fff; display: grid; overflow: hidden; box-shadow: 0 3px 8px rgba(24, 34, 53, .1); }
            .security-branch-map-controls button { width: 32px; height: 30px; padding: 0; border: 0; background: #fff; color: var(--text); font-size: 1rem; cursor: pointer; }
            .security-branch-map-controls button + button { border-top: 1px solid var(--line); }
            .security-branch-map-controls button:hover { background: #eef8fb; color: var(--primary-strong); }
            .security-branch-toggle-option { grid-template-columns: 18px minmax(0, 1fr); align-items: start; gap: 9px !important; padding: 3px 0; }
            .security-branch-toggle-option input { width: 16px; height: 16px; margin-top: 2px; }
            .security-branch-toggle-option span { min-width: 0; display: grid; gap: 3px; }
            .security-branch-toggle-option strong { font-size: .79rem; }
            .security-branch-toggle-option small { color: var(--muted); font-size: .7rem; line-height: 1.35; }
            .security-branch-camera-urls-section { min-width: 0; padding-top: 20px; border-top: 1px solid var(--line); display: grid; gap: 12px; }
            .security-branch-camera-urls-section > header { min-width: 0; display: flex; align-items: center; justify-content: space-between; gap: 14px; }
            .security-branch-camera-urls-title { min-width: 0; display: flex; align-items: flex-start; gap: 9px; }
            .security-branch-camera-urls-title h3,
            .security-branch-camera-urls-title p { margin: 0; }
            .security-branch-camera-urls-title h3 { font-size: .94rem; }
            .security-branch-camera-urls-title p { margin-top: 3px; color: var(--muted); font-size: .72rem; font-weight: 700; }
            .security-branch-camera-urls-section > header .button { flex: 0 0 auto; display: inline-flex; align-items: center; gap: 7px; }
            .security-camera-url-list { min-width: 0; display: grid; gap: 8px; }
            .security-camera-url-row { min-width: 0; padding: 9px; border: 1px solid #d4e0ec; border-radius: 8px; background: #f8fbfd; display: grid; grid-template-columns: minmax(170px, .55fr) minmax(320px, 1.45fr) 38px; align-items: end; gap: 9px; }
            .security-camera-url-row label { min-width: 0; display: grid; gap: 5px; font-size: .75rem; }
            .security-camera-url-row input { width: 100%; min-width: 0; }
            .security-camera-url-remove { width: 38px; height: 40px; min-height: 40px; padding: 0; border: 1px solid #e8caca; border-radius: 7px; background: #fff; color: #b72e2e; display: grid; place-items: center; font-size: 1.18rem; line-height: 1; cursor: pointer; }
            .security-camera-url-remove:hover { border-color: #d84c4c; background: #fff1f1; }
            .security-branch-form-actions { padding: 14px 24px; border-top: 1px solid var(--line); background: #fff; display: flex; justify-content: flex-end; gap: 10px; }
            .security-branch-catalog-dialog { width: min(1120px, calc(100vw - 32px)); max-height: calc(100vh - 32px); padding: 0; border: 0; border-radius: 8px; color: var(--text); box-shadow: 0 24px 70px rgba(24, 34, 53, .28); overflow: hidden; }
            .security-branch-catalog-dialog::backdrop { background: rgba(16, 43, 58, .58); }
            .security-branch-catalog-shell { max-height: calc(100vh - 32px); display: grid; grid-template-rows: auto minmax(0, 1fr) auto; background: #fff; }
            .security-branch-catalog-content { min-height: 180px; padding: 18px 22px; overflow: auto; }
            .security-branch-catalog-table-scroll { max-height: min(62vh, 560px); border: 1px solid var(--line); border-radius: 8px; }
            .security-branch-catalog-table { min-width: 960px; }
            .security-branch-catalog-table th { position: sticky; top: 0; z-index: 1; background: #f8fbff; }
            .security-branch-catalog-table td { vertical-align: middle; }
            .security-branch-catalog-table td:first-child strong,
            .security-branch-catalog-table td:first-child small { display: block; }
            .security-branch-catalog-table td:first-child small { max-width: 230px; margin-top: 3px; overflow: hidden; color: var(--muted); text-overflow: ellipsis; white-space: nowrap; }
            .security-branch-active-cameras { display: inline-flex; align-items: baseline; gap: 4px; white-space: nowrap; font-variant-numeric: tabular-nums; }
            .security-branch-active-cameras i { width: 7px; height: 7px; align-self: center; border-radius: 999px; background: #e04f4f; box-shadow: 0 0 0 3px rgba(224, 79, 79, .12); }
            .security-branch-active-cameras strong { font-size: .82rem; }
            .security-branch-active-cameras small { color: var(--muted); font-size: .7rem; font-weight: 750; }
            .security-branch-row-actions { display: flex; align-items: center; gap: 7px; white-space: nowrap; }
            .security-analytics-overview { padding: 18px; gap: 16px; overflow: hidden; }
            .security-analytics-overview-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; }
            .security-analytics-overview-heading h2,
            .security-analytics-overview-heading p { margin-top: 0; margin-bottom: 0; }
            .security-analytics-overview-heading h2 { font-size: 1.45rem; }
            .security-analytics-breadcrumb { margin-top: 6px !important; display: flex; align-items: center; gap: 7px; color: var(--muted); font-size: .82rem; }
            .security-analytics-breadcrumb strong { max-width: 420px; overflow: hidden; color: var(--text); text-overflow: ellipsis; white-space: nowrap; }
            .security-analytics-date-controls { display: flex; align-items: flex-end; gap: 8px; }
            .security-analytics-date-controls > label { min-width: 154px; gap: 4px; }
            .security-analytics-date-controls > label > span { color: var(--muted); font-size: .68rem; font-weight: 900; text-transform: uppercase; }
            .security-analytics-date-controls input { min-height: 36px; padding: 7px 9px; }
            .security-analytics-date-controls .button { min-height: 36px; }
            .security-analytics-filters { display: grid; grid-template-columns: minmax(220px, .9fr) minmax(240px, 1.1fr) auto; align-items: end; gap: 12px; padding-top: 14px; border-top: 1px solid var(--line); }
            .security-analytics-filters > label { min-width: 0; }
            .security-analytics-selected-company { min-height: 40px; padding: 9px 11px; border: 1px solid var(--line); border-radius: 8px; background: #f5f8fb; display: flex; align-items: center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 750; }
            .security-analytics-submit { min-width: 150px; white-space: nowrap; }
            .security-analytics-submit:disabled { cursor: not-allowed; opacity: .48; }
            .security-analytics-dashboard { min-width: 0; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
            .security-analytics-widget { min-width: 0; min-height: 292px !important; padding: 16px; gap: 12px; overflow: hidden; }
            .security-analytics-widget-heading { min-height: 46px; display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; }
            .security-analytics-widget-heading h3,
            .security-analytics-widget-heading p { margin-top: 0; margin-bottom: 0; }
            .security-analytics-widget-heading h3 { margin-top: 2px; font-size: 1rem; }
            .security-analytics-legend { display: flex; align-items: center; justify-content: flex-end; gap: 12px; flex-wrap: wrap; color: var(--muted); font-size: .72rem; font-weight: 750; }
            .security-analytics-legend span { display: inline-flex; align-items: center; gap: 6px; }
            .security-analytics-legend span::before { content: ''; width: 18px; height: 3px; border-radius: 999px; background: currentColor; }
            .security-analytics-legend .is-blue,
            .security-distribution-dot.is-blue { color: #3478db; }
            .security-analytics-legend .is-green,
            .security-distribution-dot.is-green { color: #31a36b; }
            .security-analytics-legend .is-violet { color: #7557cf; }
            .security-analytics-legend .is-amber,
            .security-distribution-dot.is-amber { color: #d4931e; }
            .security-analytics-chart { min-width: 0; min-height: 206px; position: relative; display: grid; place-items: center; }
            .security-analytics-chart svg { width: 100%; height: 100%; min-height: 206px; }
            .security-chart-grid line { stroke: #e4eaf2; stroke-width: 1; }
            .security-chart-grid .security-chart-axis { stroke: #bdc9d8; }
            .security-chart-labels { fill: #78869b; font-size: 11px; font-weight: 700; }
            .security-analytics-no-data { position: absolute; top: 45%; left: 50%; max-width: 260px; padding: 7px 10px; border: 1px solid #dce4ed; border-radius: 6px; background: rgba(255, 255, 255, .94); color: var(--muted); transform: translate(-50%, -50%); text-align: center; font-size: .76rem; font-weight: 800; }
            .security-analytics-distribution { min-height: 210px; display: grid; grid-template-columns: minmax(140px, .85fr) minmax(180px, 1.15fr); align-items: center; gap: 24px; }
            .security-analytics-donut { width: 156px; height: 156px; justify-self: center; border: 24px solid #e2e8f0; border-radius: 999px; background: #fff; display: grid; place-items: center; }
            .security-analytics-donut > span { color: var(--muted); display: grid; justify-items: center; font-size: .74rem; font-weight: 750; }
            .security-analytics-donut strong { color: var(--text); font-size: 1.55rem; }
            .security-analytics-distribution-list { margin: 0; padding: 0; display: grid; gap: 14px; list-style: none; }
            .security-analytics-distribution-list li { display: grid; grid-template-columns: 10px minmax(0, 1fr) auto; align-items: center; gap: 8px; color: var(--muted); font-size: .8rem; }
            .security-analytics-distribution-list strong { color: var(--text); }
            .security-distribution-dot { width: 9px; height: 9px; border-radius: 999px; background: currentColor; }
            .security-analytics-widget-date { flex: 0 0 auto; padding: 5px 8px; border-radius: 999px; background: #eef2f6; color: var(--muted); font-size: .7rem; font-weight: 850; }
            .security-analytics-ranking { margin: 0; padding: 4px 0 0; display: grid; gap: 11px; list-style: none; counter-reset: none; }
            .security-analytics-ranking li { min-width: 0; display: grid; grid-template-columns: 24px minmax(110px, .7fr) minmax(100px, 1fr) 62px; align-items: center; gap: 9px; }
            .security-ranking-position { width: 24px; height: 24px; border-radius: 6px; background: #eef2f6; color: var(--muted); display: grid; place-items: center; font-size: .7rem; font-weight: 900; }
            .security-analytics-ranking strong { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: .8rem; }
            .security-ranking-bar { height: 7px; border-radius: 999px; background: #e5eaf1; overflow: hidden; }
            .security-ranking-bar i { height: 100%; display: block; border-radius: inherit; background: #3478db; }
            .security-ranking-value { color: var(--muted); text-align: right; font-size: .68rem; font-weight: 800; }
            .security-analytics-widget-empty { min-height: 188px; border: 1px dashed var(--line); border-radius: 7px; background: #f8fbff; display: grid; place-content: center; gap: 5px; color: var(--muted); text-align: center; }
            .security-analytics-widget-empty p { margin: 0; font-size: .78rem; }
            .security-analytics-results-heading { align-items: center; }
            .security-analytics-results-heading > div > p:last-child { margin-top: 5px; color: var(--muted); font-weight: 750; }
            .security-analytics-count { flex: 0 0 auto; min-height: 30px; padding: 6px 10px; border: 1px solid #b7dfca; border-radius: 999px; background: #e8f6ee; color: var(--success); font-size: .82rem; font-weight: 850; }
            .security-analytics-table-scroll { max-height: min(66vh, 620px); border: 1px solid var(--line); border-radius: 8px; }
            .security-analytics-table { min-width: 720px; table-layout: fixed; }
            .security-analytics-table th { position: sticky; top: 0; z-index: 1; background: #f8fbff; }
            .security-analytics-table td { vertical-align: middle; line-height: 1.45; }
            .security-analytics-number-column { width: 72px; }
            .security-analytics-name-column { width: 270px; }
            .security-analytics-number { width: 30px; height: 30px; display: grid; place-items: center; border-radius: 999px; background: #dceff4; color: var(--primary-strong); font-weight: 900; }
            .security-section-company-context { margin: 5px 0 0; color: var(--muted); font-weight: 750; }

            @media (min-width: 1450px) {
                .security-company-insights { grid-template-columns: minmax(0, 1.2fr) minmax(260px, .8fr) minmax(330px, 1fr); }
                .security-company-flow-widget { grid-column: auto; }
            }

            @media (max-width: 1080px) {
                .security-company-metrics { grid-template-columns: repeat(3, minmax(140px, 1fr)); }
                .security-company-dashboard-filters { grid-template-columns: minmax(330px, 1fr) minmax(190px, .65fr); }
                .security-company-refresh { grid-column: 1 / -1; justify-self: start; }
                .security-analytics-dashboard { grid-template-columns: 1fr; }
                .security-camera-preview-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                .security-branch-primary-grid { grid-template-columns: 1fr; }
                .security-branch-primary-grid > .security-branch-form-section { padding-right: 0; padding-left: 0; }
                .security-branch-primary-grid > .security-branch-form-section + .security-branch-form-section { padding-top: 20px; border-top: 1px solid var(--line); border-left: 0; }
            }

            @media (max-width: 760px) {
                .security-detail-heading,
                .security-section-heading,
                .security-analytics-results-heading,
                .security-analytics-overview-heading { align-items: flex-start; flex-direction: column; }
                .security-carousel-shell { grid-template-columns: 30px minmax(0, 1fr) 30px; gap: 7px; }
                .security-carousel-nav { width: 30px; height: 30px; min-height: 30px; }
                .security-company-card { flex-basis: min(205px, calc(100vw - 112px)); }
                .security-company-dashboard-filters,
                .security-company-metrics,
                .security-company-insights { grid-template-columns: 1fr; }
                .security-company-date-range { display: grid; grid-template-columns: 1fr; }
                .security-company-date-range > span { display: none; }
                .security-company-dashboard-filters .security-company-refresh { grid-column: auto; width: 100%; }
                .security-company-flow-widget { grid-column: auto; }
                .security-company-insight > header,
                .security-company-summary-panel > header { align-items: flex-start; flex-direction: column; }
                .security-company-ranking li { grid-template-columns: 23px minmax(90px, 1fr) minmax(70px, 1fr); }
                .security-company-ranking li > small { display: none; }
                .security-detail-heading { display: grid; grid-template-columns: 1fr; }
                .security-analytics-date-controls { width: 100%; }
                .security-analytics-date-controls > label { flex: 1 1 auto; }
                .security-company-detail-grid,
                .security-company-form-grid,
                .security-branch-camera-filter,
                .security-analytics-filters { grid-template-columns: 1fr; }
                .security-branch-toolbar { align-items: stretch; flex-direction: column; }
                .security-branch-camera-filter { width: 100%; }
                .security-branch-toolbar-actions { width: 100%; align-items: stretch; flex-direction: column; }
                .security-camera-preview-grid { grid-template-columns: 1fr; }
                .security-branch-catalog-button,
                .security-branch-add-button { width: 100%; }
                .security-branch-dialog { width: calc(100vw - 16px); max-height: calc(100vh - 16px); }
                .security-branch-catalog-dialog { width: calc(100vw - 16px); max-height: calc(100vh - 16px); }
                .security-branch-create-form { max-height: calc(100vh - 16px); }
                .security-branch-catalog-shell { max-height: calc(100vh - 16px); }
                .security-branch-dialog-heading { padding: 14px 48px 14px 14px; align-items: flex-start; }
                .security-branch-dialog-icon { width: 38px; height: 38px; flex-basis: 38px; }
                .security-branch-dialog-content { padding: 16px; }
                .security-branch-camera-urls-section > header { align-items: stretch; flex-direction: column; }
                .security-branch-camera-urls-section > header .button { width: 100%; justify-content: center; }
                .security-camera-url-row { grid-template-columns: minmax(0, 1fr) 38px; }
                .security-camera-url-row label { grid-column: 1; }
                .security-camera-url-remove { grid-column: 2; grid-row: 1 / 3; align-self: center; }
                .security-branch-location-section,
                .security-branch-secondary-grid { grid-template-columns: 1fr; }
                .security-branch-secondary-grid { gap: 18px; }
                .security-branch-secondary-grid > .security-branch-form-section { padding: 0; }
                .security-branch-secondary-grid > .security-branch-form-section + .security-branch-form-section { padding-top: 18px; border-top: 1px solid var(--line); border-left: 0; }
                .security-branch-form-actions { padding: 12px 16px; }
                .security-analytics-widget { min-height: 270px !important; }
                .security-analytics-widget-heading { min-height: 0; flex-direction: column; }
                .security-analytics-legend { justify-content: flex-start; }
                .security-analytics-distribution { grid-template-columns: 1fr; gap: 14px; }
                .security-analytics-donut { width: 126px; height: 126px; border-width: 19px; }
                .security-analytics-ranking li { grid-template-columns: 24px minmax(90px, .8fr) minmax(70px, 1fr); }
                .security-ranking-value { display: none; }
                .security-company-detail-grid > div { border-right: 0; }
                .security-company-form-wide,
                .security-company-address { grid-column: auto; }
                .security-company-address { border-bottom: 0 !important; }
                .security-analytics-submit { width: 100%; }
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-security-section-carousel]').forEach((carousel) => {
                    const track = carousel.querySelector('[data-security-section-carousel-track]');
                    const previous = carousel.querySelector('[data-security-section-carousel-prev]');
                    const next = carousel.querySelector('[data-security-section-carousel-next]');
                    if (!track || !previous || !next) return;

                    const updateState = () => {
                        const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
                        previous.disabled = track.scrollLeft <= 2;
                        next.disabled = track.scrollLeft >= maxScroll - 2;
                    };

                    const move = (direction) => {
                        const firstItem = track.querySelector('[data-security-section-carousel-item]');
                        const itemWidth = firstItem?.getBoundingClientRect().width || 205;
                        const gap = Number.parseFloat(getComputedStyle(track).gap) || 9;
                        const visibleItems = Math.max(1, Math.floor((track.clientWidth + gap) / (itemWidth + gap)));
                        track.scrollBy({ left: direction * visibleItems * (itemWidth + gap), behavior: 'smooth' });
                    };

                    previous.addEventListener('click', () => move(-1));
                    next.addEventListener('click', () => move(1));
                    track.addEventListener('scroll', updateState, { passive: true });
                    window.addEventListener('resize', updateState);

                    const activeItem = track.querySelector('.security-company-card.is-active');
                    if (activeItem) track.scrollLeft = Math.max(0, activeItem.offsetLeft - track.offsetLeft - 8);
                    updateState();
                });
            });
        </script>

        @if ($securitySectionKey === 'companies')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const carousel = document.querySelector('[data-security-carousel]');

                    if (carousel) {
                        const track = carousel.querySelector('[data-security-carousel-track]');
                        const previous = carousel.querySelector('[data-security-carousel-prev]');
                        const next = carousel.querySelector('[data-security-carousel-next]');
                        const updateState = () => {
                            const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
                            previous.disabled = track.scrollLeft <= 2;
                            next.disabled = track.scrollLeft >= maxScroll - 2;
                        };

                        const move = (direction) => {
                            const firstItem = track.querySelector('[data-security-carousel-item]');
                            const itemWidth = firstItem?.getBoundingClientRect().width || 205;
                            const gap = Number.parseFloat(getComputedStyle(track).gap) || 9;
                            const visibleItems = Math.max(1, Math.floor((track.clientWidth + gap) / (itemWidth + gap)));
                            track.scrollBy({ left: direction * visibleItems * (itemWidth + gap), behavior: 'smooth' });
                        };

                        previous.addEventListener('click', () => move(-1));
                        next.addEventListener('click', () => move(1));
                        track.addEventListener('scroll', updateState, { passive: true });
                        window.addEventListener('resize', updateState);

                        const activeItem = track.querySelector('.security-company-card.is-active');
                        if (activeItem) track.scrollLeft = Math.max(0, activeItem.offsetLeft - track.offsetLeft - 8);

                        updateState();
                    }

                    const dialog = document.getElementById('security-company-dialog');
                    if (!dialog) return;

                    const openDialog = () => {
                        if (typeof dialog.showModal === 'function') {
                            if (!dialog.open) dialog.showModal();
                        } else {
                            dialog.setAttribute('open', 'open');
                        }
                    };
                    const closeDialog = () => {
                        if (typeof dialog.close === 'function') dialog.close();
                        else dialog.removeAttribute('open');
                    };

                    document.querySelectorAll('[data-security-company-open]').forEach((button) => button.addEventListener('click', openDialog));
                    dialog.querySelectorAll('[data-security-company-close]').forEach((button) => button.addEventListener('click', closeDialog));
                    dialog.addEventListener('click', (event) => {
                        if (event.target === dialog) closeDialog();
                    });

                    if (dialog.hasAttribute('data-auto-open')) openDialog();
                });
            </script>
        @endif

        @if ($securitySectionKey === 'branches')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const cameraFilter = document.querySelector('[data-security-branch-camera-filter]');
                    const branchSelect = cameraFilter?.querySelector('[data-security-branch-select]');

                    branchSelect?.addEventListener('change', () => {
                        if (!branchSelect.value) return;

                        if (typeof cameraFilter.requestSubmit === 'function') cameraFilter.requestSubmit();
                        else cameraFilter.submit();
                    });

                    const dialog = document.getElementById('security-branch-dialog');
                    if (!dialog) return;

                    const openDialog = () => {
                        if (typeof dialog.showModal === 'function') {
                            if (!dialog.open) dialog.showModal();
                        } else {
                            dialog.setAttribute('open', 'open');
                        }
                    };
                    const closeDialog = () => {
                        if (typeof dialog.close === 'function') dialog.close();
                        else dialog.removeAttribute('open');
                    };

                    const successDialog = document.getElementById('security-branch-success-dialog');
                    if (successDialog) {
                        const openSuccessDialog = () => {
                            closeDialog();

                            if (typeof successDialog.showModal === 'function') {
                                if (!successDialog.open) successDialog.showModal();
                            } else {
                                successDialog.setAttribute('open', 'open');
                            }
                        };
                        const closeSuccessDialog = () => {
                            if (typeof successDialog.close === 'function') successDialog.close();
                            else successDialog.removeAttribute('open');
                        };

                        successDialog.querySelectorAll('[data-security-branch-success-close]').forEach((button) => {
                            button.addEventListener('click', closeSuccessDialog);
                        });
                        successDialog.addEventListener('click', (event) => {
                            if (event.target === successDialog) closeSuccessDialog();
                        });

                        if (successDialog.hasAttribute('data-auto-open')) openSuccessDialog();
                    }

                    const branchForm = dialog.querySelector('[data-security-branch-form]');
                    const methodInput = branchForm?.querySelector('[data-security-branch-method]');
                    const formModeInput = branchForm?.querySelector('[data-security-branch-form-mode]');
                    const editingIdInput = branchForm?.querySelector('[data-security-branch-editing-id]');
                    const dialogIcon = dialog.querySelector('[data-security-branch-dialog-icon]');
                    const dialogTitle = dialog.querySelector('[data-security-branch-dialog-title]');
                    const dialogDescription = dialog.querySelector('[data-security-branch-dialog-description]');
                    const submitButton = dialog.querySelector('[data-security-branch-submit]');
                    const branchFieldNames = [
                        'name',
                        'code',
                        'description',
                        'address',
                        'country',
                        'state',
                        'city',
                        'postal_code',
                        'phone',
                        'email',
                        'timezone',
                        'status',
                        'analytics_enabled',
                        'alerts_enabled',
                    ];
                    const cameraUrlList = branchForm?.querySelector('[data-security-camera-url-list]');
                    const cameraUrlTemplate = branchForm?.querySelector('[data-security-camera-url-template]');
                    const cameraUrlAddButton = branchForm?.querySelector('[data-security-camera-url-add]');

                    const updateCameraUrlAddState = () => {
                        if (!cameraUrlAddButton || !cameraUrlList) return;
                        cameraUrlAddButton.disabled = cameraUrlList.querySelectorAll('[data-security-camera-url-row]').length >= 24;
                    };

                    const reindexCameraUrlRows = () => {
                        if (!cameraUrlList) return;

                        cameraUrlList.querySelectorAll('[data-security-camera-url-row]').forEach((row, index) => {
                            const number = row.querySelector('[data-security-camera-url-number]');
                            const name = row.querySelector('input[name$="[name]"]');
                            const url = row.querySelector('input[name$="[url]"]');

                            if (number) number.textContent = `Cámara ${index + 1}`;
                            if (name) name.name = `camera_urls[${index}][name]`;
                            if (url) url.name = `camera_urls[${index}][url]`;
                        });

                        updateCameraUrlAddState();
                    };

                    const bindCameraUrlRemove = (row) => {
                        const removeButton = row.querySelector('[data-security-camera-url-remove]');
                        if (!removeButton || removeButton.dataset.bound === 'true') return;

                        removeButton.dataset.bound = 'true';
                        removeButton.addEventListener('click', () => {
                            row.remove();
                            reindexCameraUrlRows();
                        });
                    };

                    const appendCameraUrlRow = (camera = {}, shouldFocus = false) => {
                        if (!cameraUrlList || !cameraUrlTemplate) return;
                        if (cameraUrlList.querySelectorAll('[data-security-camera-url-row]').length >= 24) return;

                        const row = cameraUrlTemplate.content.firstElementChild?.cloneNode(true);
                        if (!row) return;

                        cameraUrlList.append(row);
                        reindexCameraUrlRows();

                        const name = row.querySelector('input[name$="[name]"]');
                        const url = row.querySelector('input[name$="[url]"]');
                        if (name) name.value = camera.name ?? '';
                        if (url) url.value = camera.url ?? '';

                        bindCameraUrlRemove(row);
                        if (shouldFocus) name?.focus();
                    };

                    const renderCameraUrlRows = (cameras = []) => {
                        if (!cameraUrlList) return;

                        cameraUrlList.replaceChildren();
                        const rows = cameras.length ? cameras : [{}];
                        rows.forEach((camera) => appendCameraUrlRow(camera));
                    };

                    cameraUrlList?.querySelectorAll('[data-security-camera-url-row]').forEach(bindCameraUrlRemove);
                    cameraUrlAddButton?.addEventListener('click', () => appendCameraUrlRow({}, true));
                    reindexCameraUrlRows();

                    const setBranchField = (name, value) => {
                        const field = branchForm?.querySelector(`[name="${name}"]:not([type="hidden"])`)
                            || branchForm?.querySelector(`[name="${name}"]`);
                        if (!field) return;

                        if (field.type === 'checkbox') field.checked = Boolean(value);
                        else field.value = value ?? '';
                    };

                    const setBranchFormMode = (mode, branch = null, updateAction = '') => {
                        if (!branchForm) return;

                        const isEditing = mode === 'edit' && branch;
                        branchForm.reset();
                        branchForm.action = isEditing ? updateAction : branchForm.dataset.securityBranchCreateAction;

                        if (methodInput) {
                            methodInput.disabled = !isEditing;
                            methodInput.value = isEditing ? 'PATCH' : '';
                        }
                        if (formModeInput) formModeInput.value = isEditing ? 'edit' : 'create';
                        if (editingIdInput) {
                            editingIdInput.disabled = !isEditing;
                            editingIdInput.value = isEditing ? branch.id : '';
                        }

                        const values = isEditing ? branch : {
                            name: '',
                            code: '',
                            description: '',
                            address: '',
                            country: 'México',
                            state: '',
                            city: '',
                            postal_code: '',
                            phone: '',
                            email: '',
                            timezone: 'America/Mexico_City',
                            status: 'active',
                            analytics_enabled: false,
                            alerts_enabled: false,
                        };

                        branchFieldNames.forEach((name) => setBranchField(name, values[name]));
                        renderCameraUrlRows(isEditing ? (branch.cameras || []) : []);

                        if (dialogIcon) dialogIcon.textContent = isEditing ? 'E' : '+';
                        if (dialogTitle) dialogTitle.textContent = isEditing ? 'Editar sucursal' : 'Nueva sucursal';
                        if (dialogDescription) {
                            dialogDescription.textContent = isEditing
                                ? 'Actualiza la información de la sucursal seleccionada.'
                                : 'Completa la información para registrar una nueva sucursal.';
                        }
                        if (submitButton) submitButton.textContent = isEditing ? 'Guardar cambios' : 'Guardar sucursal';
                    };

                    branchForm?.addEventListener('submit', () => {
                        const isEditing = formModeInput?.value === 'edit';

                        if (isEditing && methodInput) {
                            methodInput.disabled = false;
                            methodInput.value = 'PATCH';
                        }

                        if (submitButton) {
                            submitButton.disabled = true;
                            submitButton.textContent = isEditing ? 'Guardando cambios...' : 'Guardando sucursal...';
                        }
                    });

                    document.querySelectorAll('[data-security-branch-open]').forEach((button) => {
                        button.addEventListener('click', () => {
                            setBranchFormMode('create');
                            openDialog();
                        });
                    });
                    dialog.querySelectorAll('[data-security-branch-close]').forEach((button) => button.addEventListener('click', closeDialog));
                    dialog.addEventListener('click', (event) => {
                        if (event.target === dialog) closeDialog();
                    });

                    const map = dialog.querySelector('[data-security-branch-map]');
                    const mapSearch = dialog.querySelector('[data-security-branch-map-search]');
                    const address = dialog.querySelector('input[name="address"]');
                    let mapGridSize = 34;

                    dialog.querySelectorAll('[data-security-branch-map-zoom]').forEach((button) => {
                        button.addEventListener('click', () => {
                            const direction = button.dataset.securityBranchMapZoom === 'in' ? -4 : 4;
                            mapGridSize = Math.min(54, Math.max(18, mapGridSize + direction));
                            if (map) map.style.backgroundSize = `${mapGridSize}px ${mapGridSize}px`;
                        });
                    });
                    mapSearch?.addEventListener('keydown', (event) => {
                        if (event.key !== 'Enter') return;
                        event.preventDefault();
                        if (address && !address.value.trim()) address.value = mapSearch.value.trim();
                    });

                    const catalogDialog = document.getElementById('security-branch-catalog-dialog');
                    let closeCatalog = () => {};
                    if (catalogDialog) {
                        const openCatalog = () => {
                            if (typeof catalogDialog.showModal === 'function') {
                                if (!catalogDialog.open) catalogDialog.showModal();
                            } else {
                                catalogDialog.setAttribute('open', 'open');
                            }
                        };
                        closeCatalog = () => {
                            if (typeof catalogDialog.close === 'function') catalogDialog.close();
                            else catalogDialog.removeAttribute('open');
                        };

                        document.querySelectorAll('[data-security-branch-catalog-open]').forEach((button) => button.addEventListener('click', openCatalog));
                        catalogDialog.querySelectorAll('[data-security-branch-catalog-close]').forEach((button) => button.addEventListener('click', closeCatalog));
                        catalogDialog.addEventListener('click', (event) => {
                            if (event.target === catalogDialog) closeCatalog();
                        });

                        catalogDialog.querySelectorAll('[data-security-branch-edit]').forEach((button) => {
                            button.addEventListener('click', () => {
                                let branch;

                                try {
                                    branch = JSON.parse(button.dataset.securityBranchEdit || '{}');
                                } catch {
                                    return;
                                }

                                closeCatalog();
                                setBranchFormMode('edit', branch, button.dataset.securityBranchUpdateAction || '');
                                openDialog();
                            });
                        });
                    }

                    if (dialog.hasAttribute('data-auto-open')) openDialog();
                });
            </script>
        @endif

        @if ($securitySectionKey === 'analytics')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const filterPanel = document.querySelector('[data-security-analytics-filter]');
                    if (!filterPanel) return;

                    const branchSelect = filterPanel.querySelector('[data-security-analytics-branch]');
                    const submitButton = filterPanel.querySelector('.security-analytics-submit');
                    if (branchSelect && submitButton) {
                        const updateSubmitState = () => {
                            submitButton.disabled = branchSelect.disabled || !branchSelect.value;
                        };

                        branchSelect.addEventListener('change', updateSubmitState);
                        updateSubmitState();
                    }

                    const dateInput = filterPanel.querySelector('[data-security-analytics-date]');
                    const todayButton = filterPanel.querySelector('[data-security-analytics-today]');
                    const companyId = filterPanel.querySelector('input[name="company_id"]')?.value;

                    const navigateToDate = (date) => {
                        if (!date) return;

                        const url = new URL(window.location.href);
                        url.searchParams.set('section', 'analytics');
                        url.searchParams.set('analytics_date', date);
                        if (companyId) url.searchParams.set('company_id', companyId);
                        window.location.assign(url.toString());
                    };

                    dateInput?.addEventListener('change', () => navigateToDate(dateInput.value));
                    todayButton?.addEventListener('click', () => {
                        const today = filterPanel.dataset.analyticsToday;
                        if (dateInput) dateInput.value = today;
                        navigateToDate(today);
                    });
                });
            </script>
        @endif
    </x-app-shell>
@endsection
