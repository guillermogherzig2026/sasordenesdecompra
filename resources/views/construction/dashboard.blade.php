@extends('layouts.app')

@section('body')
    <x-app-shell title="Administracion de obra">
        @php
            $money = fn ($value) => '$'.number_format((float) $value, 2);
            $date = fn ($value) => $value?->format('d/m/Y') ?? 'Sin fecha';
        @endphp

        <section class="panel construction-carousel-panel" data-construction-carousel>
            <div class="construction-carousel-header">
                <div class="construction-carousel-title">
                    <span class="construction-carousel-count">{{ $carouselProjects->count() }}</span>
                    <h2>Obras activas y por iniciar</h2>
                </div>
            </div>

            <div class="construction-carousel-shell">
                <button class="construction-carousel-nav" type="button" data-carousel-prev aria-label="Anterior">&lt;</button>

                <div class="construction-carousel-track" data-construction-carousel-track>
                    <button
                        class="construction-project-tile construction-project-tile-all active"
                        type="button"
                        data-dashboard-all
                        aria-pressed="true"
                        aria-label="Mostrar todas las obras"
                    >
                        <span class="construction-project-avatar">{{ $projects->count() }}</span>
                        <span class="construction-project-key">Panel general</span>
                        <strong class="construction-project-name">Todas</strong>
                        <span class="construction-project-status"><span></span>Ver todas las obras</span>
                    </button>

                    @foreach ($carouselProjects as $project)
                        <button
                            class="construction-project-tile"
                            type="button"
                            data-dashboard-project
                            data-project-id="{{ $project->id }}"
                            data-carousel-project-id="{{ $project->id }}"
                            data-carousel-project-status="{{ $project->status }}"
                            aria-pressed="false"
                            aria-label="Mostrar informacion de {{ $project->project_key }} - {{ $project->name }}"
                        >
                            <span class="construction-project-avatar">
                                @if ($project->photo_path)
                                    <img src="{{ $project->photo_path }}" alt="">
                                @else
                                    {{ substr($project->project_key, -2) }}
                                @endif
                            </span>
                            <span class="construction-project-key">{{ $project->project_key }}</span>
                            <strong class="construction-project-name">{{ $project->name }}</strong>
                            <span class="construction-project-status"><span></span>{{ $project->status }}</span>
                        </button>
                    @endforeach

                    <a
                        class="construction-project-tile construction-project-tile-create"
                        href="{{ route('construction.projects.create') }}"
                        data-dashboard-create
                    >
                        <span class="construction-project-add">+</span>
                        <span class="construction-project-key">Nueva</span>
                        <strong class="construction-project-name">Nueva obra</strong>
                        <span class="construction-project-status"><span></span>Registrar obra</span>
                    </a>
                </div>

                <button class="construction-carousel-nav" type="button" data-carousel-next aria-label="Siguiente">&gt;</button>
            </div>
        </section>

        <section class="construction-project-information" aria-labelledby="construction-project-information-title" data-project-information>
            <div class="construction-project-information-header">
                <h2 id="construction-project-information-title">Informacion de obras</h2>
                <span class="construction-project-information-count" data-project-information-count>
                    {{ $projects->count() }} {{ $projects->count() === 1 ? 'obra' : 'obras' }}
                </span>
            </div>

            <div class="construction-project-information-grid">
                @forelse ($projects as $project)
                    <article class="construction-project-information-card" data-project-information-card="{{ $project->id }}">
                        <header class="construction-project-information-card-header">
                            <span class="construction-project-information-avatar" aria-hidden="true">
                                @if ($project->photo_path)
                                    <img src="{{ $project->photo_path }}" alt="">
                                @else
                                    {{ substr($project->project_key, -2) }}
                                @endif
                            </span>
                            <div class="construction-project-information-title">
                                <span>{{ $project->project_key }}</span>
                                <h3>{{ $project->name }}</h3>
                            </div>
                            <span class="status {{ $project->statusColor() }}">{{ $project->status }}</span>
                        </header>

                        <dl class="construction-project-information-data construction-project-information-data-general">
                            <div>
                                <dt>Cliente</dt>
                                <dd>{{ $project->client?->name ?? 'Sin cliente' }}</dd>
                            </div>
                            <div>
                                <dt>Responsable</dt>
                                <dd>{{ $project->responsible?->name ?? 'Sin responsable' }}</dd>
                            </div>
                            <div>
                                <dt>Ubicacion</dt>
                                <dd>{{ $project->location ?: 'Sin ubicacion' }}</dd>
                            </div>
                            <div>
                                <dt>Tipo de obra</dt>
                                <dd>{{ $project->project_type ?: 'Sin tipo' }}</dd>
                            </div>
                            <div>
                                <dt>Modalidad</dt>
                                <dd>{{ $project->modality ?: 'Sin modalidad' }}</dd>
                            </div>
                            <div>
                                <dt>Periodo</dt>
                                <dd>{{ $date($project->start_date) }} - {{ $date($project->estimated_end_date) }}</dd>
                            </div>
                        </dl>

                        <dl class="construction-project-information-data construction-project-information-data-metrics">
                            <div>
                                <dt>Construidos</dt>
                                <dd>{{ number_format((float) $project->constructed_area, 2) }} m2</dd>
                            </div>
                            <div>
                                <dt>Vendibles / rentables</dt>
                                <dd>{{ number_format((float) $project->sellable_rentable_area, 2) }} m2</dd>
                            </div>
                            <div>
                                <dt>Estacionamientos</dt>
                                <dd>{{ number_format((float) $project->parking_area, 2) }} m2</dd>
                            </div>
                            <div>
                                <dt>Niveles</dt>
                                <dd>{{ number_format((int) $project->levels_count) }}</dd>
                            </div>
                            <div>
                                <dt>Avance fisico</dt>
                                <dd>{{ number_format((float) $project->physical_progress, 2) }}%</dd>
                            </div>
                            <div>
                                <dt>Avance financiero</dt>
                                <dd>{{ number_format((float) $project->financial_progress, 2) }}%</dd>
                            </div>
                        </dl>

                        <dl class="construction-project-information-data construction-project-information-data-financial">
                            <div>
                                <dt>Contrato</dt>
                                <dd>{{ $money($project->contracted_value) }}</dd>
                            </div>
                            <div>
                                <dt>Pagado</dt>
                                <dd>{{ $money($project->paid_amount) }}</dd>
                            </div>
                            <div>
                                <dt>Por pagar</dt>
                                <dd>{{ $money($project->balance_to_pay) }}</dd>
                            </div>
                        </dl>
                    </article>
                @empty
                    <div class="construction-project-information-empty">No hay obras registradas.</div>
                @endforelse
            </div>
        </section>

        <section class="panel">
            <h2>Bitacora reciente</h2>
            <ul class="audit-list">
                @forelse ($auditLogs as $entry)
                    <li>
                        <strong>{{ $entry->action }}</strong>
                        {{ $entry->description }}
                        <small>{{ $entry->user?->name ?? 'Sistema' }} &middot; {{ $entry->occurred_at->format('d/m/Y H:i') }}</small>
                    </li>
                @empty
                    <li>Sin movimientos registrados.</li>
                @endforelse
            </ul>
        </section>

        @if (session('construction_project_created'))
            <dialog
                class="construction-created-dialog"
                id="construction-created-dialog"
                aria-labelledby="construction-created-title"
                data-construction-created-dialog
                data-auto-open
            >
                <div class="construction-created-content">
                    <span class="construction-created-icon" aria-hidden="true">&#10003;</span>
                    <h2 id="construction-created-title">{{ session('construction_project_created') }}</h2>
                    <button
                        class="construction-created-close"
                        type="button"
                        data-construction-created-close
                        aria-label="Cerrar confirmacion"
                        title="Cerrar"
                    >&times;</button>
                </div>
            </dialog>
        @endif
    </x-app-shell>

    <style>
        button.construction-project-tile { font: inherit; }
        .construction-project-tile-all { background: #f7fafc; border-color: #a9c7d3; }
        .construction-project-tile-all .construction-project-status span { background: #607d8b; }
        [data-construction-carousel-track] > .construction-project-tile-create {
            scroll-snap-align: end;
        }
        .construction-project-information {
            min-width: 0;
            display: grid;
            gap: 12px;
        }
        .construction-project-information-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .construction-project-information-header h2 { margin: 0; font-size: 1.12rem; }
        .construction-project-information-count {
            min-height: 28px;
            padding: 5px 10px;
            border: 1px solid #b7d9cc;
            border-radius: 999px;
            background: #eaf7f1;
            color: #087443;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: .8rem;
            font-weight: 850;
            white-space: nowrap;
        }
        .construction-project-information-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(430px, 100%), 1fr));
            gap: 12px;
        }
        .construction-project-information-card {
            min-width: 0;
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            display: grid;
            align-content: start;
            gap: 14px;
            box-shadow: 0 5px 16px rgba(15, 45, 64, .05);
        }
        .construction-project-information-card[hidden] { display: none; }
        .construction-project-information-card-header {
            min-width: 0;
            display: grid;
            grid-template-columns: 44px minmax(0, 1fr) auto;
            align-items: center;
            gap: 10px;
        }
        .construction-project-information-avatar {
            width: 44px;
            height: 44px;
            border-radius: 7px;
            background: #eaf4f7;
            color: var(--primary-strong);
            display: grid;
            place-items: center;
            overflow: hidden;
            font-size: .78rem;
            font-weight: 900;
        }
        .construction-project-information-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .construction-project-information-title { min-width: 0; }
        .construction-project-information-title span {
            color: var(--primary-strong);
            font-size: .72rem;
            font-weight: 900;
        }
        .construction-project-information-title h3 {
            margin: 2px 0 0;
            font-size: .98rem;
            line-height: 1.25;
            overflow-wrap: anywhere;
        }
        .construction-project-information-data {
            min-width: 0;
            margin: 0;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0;
        }
        .construction-project-information-data + .construction-project-information-data {
            padding-top: 12px;
            border-top: 1px solid var(--line);
        }
        .construction-project-information-data > div {
            min-width: 0;
            padding: 5px 10px;
            border-left: 1px solid var(--line);
        }
        .construction-project-information-data > div:nth-child(3n + 1) { padding-left: 0; border-left: 0; }
        .construction-project-information-data dt {
            margin-bottom: 3px;
            color: var(--muted);
            font-size: .67rem;
            font-weight: 900;
            text-transform: uppercase;
        }
        .construction-project-information-data dd {
            min-width: 0;
            margin: 0;
            font-size: .82rem;
            font-weight: 750;
            line-height: 1.3;
            overflow-wrap: anywhere;
        }
        .construction-project-information-data-financial dd { color: #087443; font-weight: 900; }
        .construction-project-information-empty {
            min-height: 110px;
            padding: 18px;
            border: 1px dashed var(--line);
            border-radius: 8px;
            background: #fff;
            color: var(--muted);
            display: grid;
            place-items: center;
            font-weight: 750;
        }
        @media (max-width: 720px) {
            .construction-project-information-card-header { grid-template-columns: 44px minmax(0, 1fr); }
            .construction-project-information-card-header .status { grid-column: 1 / -1; justify-self: start; }
            .construction-project-information-data { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .construction-project-information-data > div:nth-child(3n + 1) { padding-left: 10px; border-left: 1px solid var(--line); }
            .construction-project-information-data > div:nth-child(2n + 1) { padding-left: 0; border-left: 0; }
        }
        .construction-created-dialog {
            width: min(440px, calc(100vw - 32px));
            padding: 0;
            border: 0;
            border-radius: 8px;
            color: var(--text);
            box-shadow: 0 24px 70px rgba(24, 34, 53, .3);
        }
        .construction-created-dialog::backdrop { background: rgba(16, 43, 58, .5); }
        .construction-created-content {
            min-height: 104px;
            padding: 24px 58px 24px 24px;
            position: relative;
            display: flex;
            align-items: center;
            gap: 14px;
            background: #fff;
        }
        .construction-created-content h2 { margin: 0; font-size: 1.08rem; }
        .construction-created-icon {
            width: 40px;
            height: 40px;
            flex: 0 0 40px;
            border-radius: 999px;
            background: #e3f5ee;
            color: #14845a;
            display: grid;
            place-items: center;
            font-size: 1.15rem;
            font-weight: 900;
        }
        .construction-created-close {
            width: 32px;
            height: 32px;
            padding: 0;
            position: absolute;
            top: 12px;
            right: 12px;
            border: 1px solid var(--line);
            border-radius: 7px;
            background: #fff;
            color: var(--text);
            display: grid;
            place-items: center;
            font-size: 1.15rem;
            line-height: 1;
            cursor: pointer;
        }
        .construction-created-close:hover { background: #f4f7fb; color: var(--primary); }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const createdDialog = document.querySelector('[data-construction-created-dialog]');

            if (createdDialog) {
                const closeCreatedDialog = () => {
                    if (typeof createdDialog.close === 'function') createdDialog.close();
                    else createdDialog.removeAttribute('open');
                };

                createdDialog.querySelector('[data-construction-created-close]')?.addEventListener('click', closeCreatedDialog);
                createdDialog.addEventListener('click', (event) => {
                    if (event.target === createdDialog) closeCreatedDialog();
                });

                if (createdDialog.hasAttribute('data-auto-open')) {
                    if (typeof createdDialog.showModal === 'function') createdDialog.showModal();
                    else createdDialog.setAttribute('open', 'open');
                }
            }

            document.querySelectorAll('[data-construction-carousel]').forEach((carousel) => {
                const track = carousel.querySelector('[data-construction-carousel-track]');
                const scrollByTile = () => Math.max(220, Math.round((track?.clientWidth || 260) * 0.72));
                const allProjectsButton = carousel.querySelector('[data-dashboard-all]');
                const projectButtons = [...carousel.querySelectorAll('[data-dashboard-project]')];
                const informationCards = [...document.querySelectorAll('[data-project-information-card]')];
                const informationCount = document.querySelector('[data-project-information-count]');

                const selectProject = (projectId = null) => {
                    const showAll = projectId === null;

                    allProjectsButton?.classList.toggle('active', showAll);
                    allProjectsButton?.setAttribute('aria-pressed', showAll ? 'true' : 'false');

                    projectButtons.forEach((button) => {
                        const isSelected = !showAll && button.dataset.projectId === projectId;
                        button.classList.toggle('active', isSelected);
                        button.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
                    });

                    informationCards.forEach((card) => {
                        card.hidden = !showAll && card.dataset.projectInformationCard !== projectId;
                    });

                    if (informationCount) {
                        informationCount.textContent = showAll
                            ? `${informationCards.length} ${informationCards.length === 1 ? 'obra' : 'obras'}`
                            : '1 obra';
                    }
                };

                allProjectsButton?.addEventListener('click', () => selectProject());
                projectButtons.forEach((button) => {
                    button.addEventListener('click', () => selectProject(button.dataset.projectId));
                });

                carousel.querySelector('[data-carousel-prev]')?.addEventListener('click', () => {
                    track?.scrollBy({ left: -scrollByTile(), behavior: 'smooth' });
                });

                carousel.querySelector('[data-carousel-next]')?.addEventListener('click', () => {
                    track?.scrollBy({ left: scrollByTile(), behavior: 'smooth' });
                });
            });
        });
    </script>
@endsection
