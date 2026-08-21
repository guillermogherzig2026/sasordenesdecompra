@extends('layouts.app')

@section('body')
    <x-app-shell title="Administracion de obra">
        @php
            $money = fn ($value) => '$'.number_format((float) $value, 2);
            $carouselProjects = $projects->where('status', 'En ejecucion')->values();
        @endphp

        <section class="panel construction-carousel-panel" data-construction-carousel>
            <div class="construction-carousel-header">
                <div class="construction-carousel-title">
                    <span class="construction-carousel-count">{{ $summary['active'] }}</span>
                    <h2>Obras activas</h2>
                </div>
            </div>

            <div class="construction-carousel-shell">
                <button class="construction-carousel-nav" type="button" data-carousel-prev aria-label="Anterior">&lt;</button>

                <div class="construction-carousel-track" data-construction-carousel-track>
                    <a class="construction-project-tile construction-project-tile-create" href="{{ route('construction.projects.create') }}">
                        <span class="construction-project-add">+</span>
                        <span class="construction-project-key">Nueva</span>
                        <strong class="construction-project-name">Nueva obra</strong>
                        <span class="construction-project-status"><span></span>Registrar obra</span>
                    </a>

                    @foreach ($carouselProjects as $project)
                        <a
                            class="construction-project-tile {{ $loop->first ? 'active' : '' }}"
                            href="#project-row-{{ $project->id }}"
                            data-dashboard-project
                            data-project-row="project-row-{{ $project->id }}"
                            data-carousel-project-id="{{ $project->id }}"
                            aria-label="Ver {{ $project->project_key }} - {{ $project->name }} en el Panel general"
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
                        </a>
                    @endforeach
                </div>

                <button class="construction-carousel-nav" type="button" data-carousel-next aria-label="Siguiente">&gt;</button>
            </div>
        </section>

        <section class="panel" id="panel-general-obras">
            <div class="panel-header">
                <div>
                    <h2>Panel general de obras</h2>
                    <p class="fine-print">Vista ejecutiva de obras activas, avances y pagos.</p>
                </div>
            </div>

            <div class="table-scroll">
                <table class="construction-overview-table">
                    <thead>
                        <tr>
                            <th>Obra</th>
                            <th>Cliente</th>
                            <th>Metros cuadrados construidos</th>
                            <th>Metros cuadrados vendibles o rentables</th>
                            <th>Metros cuadrados de estacionamientos</th>
                            <th>Numero de niveles</th>
                            <th>Estado</th>
                            <th>Avance fisico</th>
                            <th>Avance financiero</th>
                            <th>Por pagar</th>
                            <th class="construction-actions-column" data-no-filter data-no-sort>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projects->sortByDesc('balance_to_pay')->take(8) as $project)
                            <tr id="project-row-{{ $project->id }}">
                                <td>
                                    <strong>{{ $project->project_key }}</strong>
                                    <br><span class="fine-print">{{ $project->name }}</span>
                                </td>
                                <td>{{ $project->client?->name ?? 'Sin cliente' }}</td>
                                <td>{{ number_format((float) $project->constructed_area, 2) }} m2</td>
                                <td>{{ number_format((float) $project->sellable_rentable_area, 2) }} m2</td>
                                <td>{{ number_format((float) $project->parking_area, 2) }} m2</td>
                                <td>{{ number_format((int) $project->levels_count) }}</td>
                                <td data-filter-value="{{ $project->status }}">
                                    @if (in_array($project->id, $editableProjectIds, true))
                                        <details class="status-menu">
                                            <summary
                                                class="status {{ $project->statusColor() }}"
                                                aria-label="Cambiar estatus de {{ $project->project_key }}"
                                            >{{ $project->status }}</summary>
                                            <div class="status-menu-panel">
                                                @foreach ($projectStatuses as $projectStatus)
                                                    @continue($projectStatus === $project->status)
                                                    @php
                                                        $statusOptionClass = match ($projectStatus) {
                                                            'Por iniciar' => 'project-status-option-warning',
                                                            'En ejecucion' => 'project-status-option-running',
                                                            default => 'project-status-option-completed',
                                                        };
                                                    @endphp
                                                    <form class="inline-form" method="POST" action="{{ route('construction.projects.status.update', $project) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="{{ $projectStatus }}">
                                                        <button class="button small {{ $statusOptionClass }}" type="submit">{{ $projectStatus }}</button>
                                                    </form>
                                                @endforeach
                                            </div>
                                        </details>
                                    @else
                                        <span class="status {{ $project->statusColor() }}">{{ $project->status }}</span>
                                    @endif
                                </td>
                                <td>{{ number_format((float) $project->physical_progress, 2) }}%</td>
                                <td>{{ number_format((float) $project->financial_progress, 2) }}%</td>
                                <td><strong>{{ $money($project->balance_to_pay) }}</strong></td>
                                <td class="construction-actions-column">
                                    @if (in_array($project->id, $editableProjectIds, true))
                                        <a
                                            class="button ghost small"
                                            href="{{ route('construction.projects.edit', $project) }}"
                                            aria-label="Editar {{ $project->project_key }} - {{ $project->name }}"
                                        >Editar</a>
                                    @else
                                        <span class="fine-print">Solo lectura</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="11">No hay obras registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
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
    </x-app-shell>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-construction-carousel]').forEach((carousel) => {
                const track = carousel.querySelector('[data-construction-carousel-track]');
                const scrollByTile = () => Math.max(220, Math.round((track?.clientWidth || 260) * 0.72));
                const projectTiles = [...carousel.querySelectorAll('[data-dashboard-project]')];
                const projectRows = [...document.querySelectorAll('[id^="project-row-"]')];

                const selectProject = (selectedTile) => {
                    const selectedRowId = selectedTile.dataset.projectRow;

                    projectTiles.forEach((tile) => {
                        tile.classList.toggle('active', tile === selectedTile);
                    });

                    projectRows.forEach((row) => {
                        row.classList.toggle('is-highlighted', row.id === selectedRowId);
                    });
                };

                projectTiles.forEach((tile) => {
                    tile.addEventListener('click', (event) => {
                        event.preventDefault();
                        selectProject(tile);

                        if (window.location.hash) {
                            window.history.replaceState(null, '', `${window.location.pathname}${window.location.search}`);
                        }
                    });
                });

                carousel.querySelector('[data-carousel-prev]')?.addEventListener('click', () => {
                    track?.scrollBy({ left: -scrollByTile(), behavior: 'smooth' });
                });

                carousel.querySelector('[data-carousel-next]')?.addEventListener('click', () => {
                    track?.scrollBy({ left: scrollByTile(), behavior: 'smooth' });
                });

                const hashTile = projectTiles.find((tile) => `#${tile.dataset.projectRow}` === window.location.hash);

                if (hashTile) {
                    selectProject(hashTile);
                }
            });
        });
    </script>
@endsection
