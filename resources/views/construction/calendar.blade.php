@extends('layouts.app')

@section('body')
    <x-app-shell title="Calendario y Avances">
        <section class="panel construction-carousel-panel" data-construction-carousel>
            <div class="construction-carousel-header">
                <div class="construction-carousel-title">
                    <span class="construction-carousel-count">{{ $carouselProjects->count() }}</span>
                    <h2>Obras activas y por iniciar</h2>
                </div>
                <a class="button ghost small" href="{{ route('construction.dashboard') }}">Atras</a>
            </div>

            <div class="construction-carousel-shell">
                <button class="construction-carousel-nav" type="button" data-carousel-prev aria-label="Obra anterior">&lt;</button>
                <div class="construction-carousel-track" data-construction-carousel-track>
                    @forelse ($carouselProjects as $project)
                        <a
                            class="construction-project-tile {{ $project->id === $selectedProjectId ? 'active' : '' }}"
                            href="{{ route('construction.placeholder', ['section' => 'calendario', 'project' => $project->id, 'month' => $monthValue, ...($calendarView === 'advances' ? ['view' => 'advances'] : [])]) }}"
                            data-carousel-project-id="{{ $project->id }}"
                            data-carousel-project-status="{{ $project->status }}"
                            data-calendar-project-link
                            aria-current="{{ $project->id === $selectedProjectId ? 'page' : 'false' }}"
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
                    @empty
                        <span class="construction-project-tile">
                            <span class="construction-project-avatar">OB</span>
                            <span class="construction-project-key">Sin obras</span>
                            <strong class="construction-project-name">No hay obras visibles</strong>
                        </span>
                    @endforelse
                </div>
                <button class="construction-carousel-nav" type="button" data-carousel-next aria-label="Siguiente obra">&gt;</button>
            </div>
        </section>

        <div class="schedule-view-switch" role="tablist" aria-label="Calendario y Avances" data-initial-schedule-view="{{ $calendarView }}">
            <button
                class="schedule-view-tab {{ $calendarView === 'calendar' ? 'active' : '' }}"
                type="button"
                role="tab"
                aria-selected="{{ $calendarView === 'calendar' ? 'true' : 'false' }}"
                aria-controls="schedule-calendar-view"
                data-schedule-view-tab="calendar"
            >Calendario</button>
            <button
                class="schedule-view-tab {{ $calendarView === 'advances' ? 'active' : '' }}"
                type="button"
                role="tab"
                aria-selected="{{ $calendarView === 'advances' ? 'true' : 'false' }}"
                aria-controls="schedule-advances-view"
                data-schedule-view-tab="advances"
            >Avances</button>
        </div>

        <section
            class="panel schedule-panel"
            id="schedule-calendar-view"
            role="tabpanel"
            data-schedule-view-panel="calendar"
            data-schedule-calendar
            @if ($calendarView !== 'calendar') hidden @endif
        >
            <div class="schedule-heading">
                <div>
                    <h2>Calendario de alcances</h2>
                    @if ($selectedProject)
                        <p>{{ $selectedProject->name }} &middot; {{ $selectedProject->project_key }}</p>
                    @else
                        <p>Selecciona una obra para consultar sus alcances.</p>
                    @endif
                </div>
            </div>

            @if ($selectedProject)
                <div class="schedule-toolbar">
                    <div class="schedule-month-heading">
                        <h3>{{ $monthTitle }}</h3>
                        <div class="schedule-month-nav" aria-label="Navegacion mensual">
                            <a class="schedule-icon-button" href="{{ route('construction.placeholder', ['section' => 'calendario', 'project' => $selectedProject->id, 'month' => $previousMonth]) }}" aria-label="Mes anterior">&lt;</a>
                            <a class="button ghost small" href="{{ route('construction.placeholder', ['section' => 'calendario', 'project' => $selectedProject->id, 'month' => $todayMonth]) }}">Hoy</a>
                            <a class="schedule-icon-button" href="{{ route('construction.placeholder', ['section' => 'calendario', 'project' => $selectedProject->id, 'month' => $nextMonth]) }}" aria-label="Mes siguiente">&gt;</a>
                        </div>
                    </div>
                    <div class="schedule-toolbar-actions">
                        <label class="schedule-filter">
                            <span class="sr-only">Filtrar por contratista</span>
                            <select data-calendar-contractor-filter>
                                <option value="">Todos los contratistas</option>
                                @foreach ($contractors as $contractor)
                                    <option value="{{ $contractor }}">{{ $contractor }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label class="schedule-filter">
                            <span class="sr-only">Filtrar por alcance</span>
                            <select data-calendar-scope-filter>
                                <option value="">Todos los alcances</option>
                                @foreach ($scopes as $scope)
                                    <option value="{{ $scope }}">{{ $scope }}</option>
                                @endforeach
                            </select>
                        </label>
                        @if ($canEditCalendar)
                            <button class="button primary schedule-add-button" type="button" data-schedule-create>
                                <span aria-hidden="true">+</span> Agregar alcance
                            </button>
                        @endif
                    </div>
                </div>

                <div class="schedule-contractor-legend" aria-label="Contratistas del calendario">
                    @forelse ($contractors as $contractor)
                        @php
                            $style = $contractorStyles[$contractor];
                        @endphp
                        <span data-calendar-contractor-legend="{{ $contractor }}">
                            <i style="--legend-color: {{ $style['line'] }}"></i>{{ $contractor }}
                        </span>
                    @empty
                        <span class="schedule-empty-legend">Los contratistas apareceran al registrar alcances.</span>
                    @endforelse
                </div>

                <div class="schedule-calendar-scroll">
                    <div class="schedule-calendar-grid" role="grid" aria-label="{{ $monthTitle }}">
                        <div class="schedule-weekdays" role="row">
                            @foreach (['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'] as $weekday)
                                <div role="columnheader">{{ $weekday }}</div>
                            @endforeach
                        </div>

                        @foreach ($calendarWeeks as $week)
                            <div class="schedule-week" role="row" style="grid-template-rows: 36px repeat({{ max($week['lanes'], 1) }}, 64px);">
                                @foreach ($week['days'] as $day)
                                    <div
                                        class="schedule-day {{ $day['in_month'] ? '' : 'outside-month' }} {{ $day['is_today'] ? 'today' : '' }}"
                                        style="grid-column: {{ $loop->iteration }};"
                                        role="gridcell"
                                        aria-label="{{ $day['date']->locale('es')->translatedFormat('j \d\e F \d\e Y') }}"
                                    >
                                        <span>{{ $day['date']->day }}</span>
                                    </div>
                                @endforeach

                                @foreach ($week['segments'] as $segment)
                                    @php
                                        $item = $segment['item'];
                                        $eventStyle = $contractorStyles[$item->contractor] ?? ['line' => '#64748b', 'background' => '#eef2f7', 'text' => '#334155'];
                                    @endphp
                                    <button
                                        class="schedule-event"
                                        type="button"
                                        style="grid-column: {{ $segment['start_column'] }} / span {{ $segment['span'] }}; grid-row: {{ $segment['lane'] + 2 }}; --event-line: {{ $eventStyle['line'] }}; --event-background: {{ $eventStyle['background'] }}; --event-text: {{ $eventStyle['text'] }};"
                                        data-schedule-event
                                        data-schedule-edit="{{ $item->id }}"
                                        data-contractor="{{ $item->contractor }}"
                                        data-scope="{{ $item->title }}"
                                        data-status="{{ $item->status }}"
                                        aria-label="{{ $item->contractor }} - {{ $item->title }}, alcance numero {{ $item->contractor_sequence }}, {{ $item->progress }} por ciento"
                                        @disabled(! $canEditCalendar)
                                    >
                                        <span class="schedule-event-title">
                                            <span class="schedule-event-title-text">
                                                <span class="schedule-event-contractor">{{ $item->contractor }}</span>
                                                <span class="schedule-event-title-separator" aria-hidden="true">-</span>
                                                <span class="schedule-event-scope">{{ $item->title }}</span>
                                            </span>
                                            @if ($item->contractor_sequence)
                                                <span class="schedule-event-sequence">#{{ str_pad((string) $item->contractor_sequence, 3, '0', STR_PAD_LEFT) }}</span>
                                            @endif
                                        </span>
                                        <span class="schedule-event-meta">{{ $item->progress }}%</span>
                                        @if ($item->description)
                                            <span class="schedule-event-description">{{ $item->description }}</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="schedule-status-legend" aria-label="Estados de los alcances">
                    <span><i class="programmed"></i>Programado</span>
                    <span><i class="in-progress"></i>En proceso</span>
                    <span><i class="completed"></i>Concluido</span>
                </div>
            @else
                <div class="schedule-empty-state">No hay obras disponibles para mostrar en el calendario.</div>
            @endif
        </section>

        <section
            class="weekly-reports-layout"
            id="schedule-advances-view"
            role="tabpanel"
            data-schedule-view-panel="advances"
            @if ($calendarView !== 'advances') hidden @endif
        >
            @if ($selectedProject && $currentWeeklyReport)
                <div class="weekly-reports-dashboard" data-weekly-reports-dashboard>
                    <aside class="weekly-report-navigation" aria-labelledby="weekly-report-navigation-title">
                        <div class="weekly-report-navigation-header">
                            <span class="weekly-report-navigation-mark" aria-hidden="true">
                                <i></i><i></i><i></i>
                            </span>
                            <h2 id="weekly-report-navigation-title">Reportes semanales</h2>
                        </div>

                        <div class="weekly-report-navigation-summary" aria-label="Resumen de reportes">
                            <a href="{{ route('construction.placeholder', ['section' => 'calendario', 'project' => $selectedProject->id, 'month' => $previousMonth, 'view' => 'advances']) }}" aria-label="Mes anterior">&lt;</a>
                            <span><strong>{{ count($weeklyReports) }} reportes</strong><i>&middot;</i>{{ $monthTitle }}</span>
                            <a href="{{ route('construction.placeholder', ['section' => 'calendario', 'project' => $selectedProject->id, 'month' => $nextMonth, 'view' => 'advances']) }}" aria-label="Mes siguiente">&gt;</a>
                        </div>

                        <nav class="weekly-report-menu" aria-label="Seleccionar reporte semanal">
                            @foreach ($weeklyReports as $report)
                                <button
                                    class="weekly-report-menu-item {{ $loop->first ? 'active' : '' }}"
                                    type="button"
                                    data-weekly-report-select="{{ $report['id'] }}"
                                    aria-controls="weekly-report-panel-{{ $report['id'] }}"
                                    aria-current="{{ $loop->first ? 'true' : 'false' }}"
                                >
                                    <span class="weekly-report-menu-calendar" aria-hidden="true"></span>
                                    <span class="weekly-report-menu-copy">
                                        <strong>{{ $report['week_label'] }}</strong>
                                        <small>{{ $report['period_short'] }}</small>
                                    </span>
                                    <span class="weekly-report-menu-progress">{{ $report['actual_progress'] }}%</span>
                                    <span class="weekly-report-menu-status">
                                        <i aria-hidden="true">&#10003;</i>
                                        {{ $loop->first ? 'Actual' : $report['status'] }}
                                    </span>
                                    <span class="weekly-report-menu-chevron" aria-hidden="true">&gt;</span>
                                </button>
                            @endforeach
                        </nav>
                    </aside>

                    <div class="weekly-reports-content">
                        @foreach ($weeklyReports as $report)
                            @php
                                $olderReport = $weeklyReports[$loop->index + 1] ?? null;
                                $newerReport = $loop->index > 0 ? $weeklyReports[$loop->index - 1] : null;
                            @endphp
                            <div
                                id="weekly-report-panel-{{ $report['id'] }}"
                                data-weekly-report-panel="{{ $report['id'] }}"
                                role="region"
                                aria-label="{{ $report['week_label'] }}"
                                @if (! $loop->first) hidden @endif
                            >
                                <section class="weekly-report-workspace">
                                    <header class="weekly-reports-heading">
                                        <div class="weekly-reports-heading-copy">
                                            <h2>Reporte semanal de obra</h2>
                                            <p>
                                                <span>{{ $selectedProject->name }} &middot; {{ $selectedProject->project_key }}</span>
                                                <span class="weekly-report-project-badge"><i aria-hidden="true">&#10003;</i>Obra seleccionada previamente</span>
                                            </p>
                                        </div>
                                        <div class="weekly-report-toolbar">
                                            <button
                                                type="button"
                                                data-weekly-report-select-target="{{ $olderReport['id'] ?? '' }}"
                                                aria-label="Reporte anterior"
                                                @disabled(! $olderReport)
                                            >&lt;</button>
                                            <strong>{{ $report['week_label'] }} <span>&middot; {{ $report['period_long'] }}</span></strong>
                                            <button
                                                type="button"
                                                data-weekly-report-select-target="{{ $newerReport['id'] ?? '' }}"
                                                aria-label="Reporte siguiente"
                                                @disabled(! $newerReport)
                                            >&gt;</button>
                                            <span class="weekly-report-toolbar-status">
                                                <i aria-hidden="true">&#10003;</i>{{ $report['status'] }}
                                            </span>
                                        </div>
                                    </header>

                                    @include('construction.partials.weekly-report-card', ['report' => $report])

                                    <footer class="weekly-report-generation-note">
                                        <i aria-hidden="true">i</i>
                                        @if ($loop->first)
                                            El próximo reporte se generará automáticamente el {{ $report['next_report_at'] }} a las 07:00.
                                        @else
                                            Reporte enviado el {{ $report['reported_at'] }}.
                                        @endif
                                    </footer>
                                </section>
                            </div>
                        @endforeach
                    </div>
                </div>

                @foreach ($weeklyReports as $report)
                    <dialog class="weekly-report-dialog" id="weekly-report-dialog-{{ $report['id'] }}" data-weekly-report-dialog="{{ $report['id'] }}" aria-labelledby="weekly-report-dialog-title-{{ $report['id'] }}">
                        <div class="weekly-report-dialog-header">
                            <div>
                                <span>Detalle del reporte</span>
                                <h2 id="weekly-report-dialog-title-{{ $report['id'] }}">{{ $report['week_label'] }}</h2>
                            </div>
                            <button type="button" data-weekly-report-close aria-label="Cerrar">&times;</button>
                        </div>
                        @include('construction.partials.weekly-report-card', ['report' => $report, 'showActions' => false, 'embedded' => true])
                    </dialog>
                @endforeach

                <div class="weekly-report-notice" role="status" aria-live="polite" data-weekly-report-notice hidden>
                    <span data-weekly-report-notice-text></span>
                    <button type="button" data-weekly-report-notice-close aria-label="Cerrar">&times;</button>
                </div>
            @else
                <div class="panel schedule-empty-state">No hay información suficiente para generar reportes semanales.</div>
            @endif
        </section>

        @if ($selectedProject && $canEditCalendar)
            <dialog class="schedule-dialog" data-schedule-create-dialog aria-labelledby="schedule-create-title">
                <form method="POST" action="{{ route('construction.schedule-items.store') }}">
                    @csrf
                    <input type="hidden" name="construction_project_id" value="{{ $selectedProject->id }}">
                    <div class="schedule-dialog-header">
                        <div>
                            <h2 id="schedule-create-title">Agregar alcance</h2>
                            <p>{{ $selectedProject->name }}</p>
                        </div>
                        <button class="schedule-dialog-close" type="button" data-schedule-close aria-label="Cerrar">&times;</button>
                    </div>
                    <div class="schedule-form-grid">
                        <label class="schedule-field schedule-field-wide">
                            <span>Contratista</span>
                            <input name="contractor" value="{{ old('contractor') }}" maxlength="160" list="schedule-contractors" required>
                        </label>
                        <label class="schedule-field schedule-field-wide">
                            <span>Alcance</span>
                            <input name="title" value="{{ old('title') }}" maxlength="160" required>
                        </label>
                        <label class="schedule-field schedule-field-wide">
                            <span>Inicio</span>
                            <input type="date" name="start_date" value="{{ old('start_date', $monthValue.'-01') }}" required>
                        </label>
                        <label class="schedule-field schedule-field-wide">
                            <span>Fin</span>
                            <input type="date" name="end_date" value="{{ old('end_date', $monthValue.'-01') }}" required>
                        </label>
                        <input type="hidden" name="progress" value="0">
                        <input type="hidden" name="status" value="Programado">
                        <label class="schedule-field schedule-field-full">
                            <span>Descripcion</span>
                            <textarea name="description" rows="3" maxlength="1200">{{ old('description') }}</textarea>
                        </label>
                    </div>
                    <div class="schedule-dialog-actions">
                        <button class="button ghost" type="button" data-schedule-close>Cancelar</button>
                        <button class="button primary" type="submit">Guardar alcance</button>
                    </div>
                </form>
            </dialog>

            <dialog class="schedule-dialog" data-schedule-edit-dialog aria-labelledby="schedule-edit-title">
                <form method="POST" data-schedule-edit-form>
                    @csrf
                    <div class="schedule-dialog-header">
                        <div>
                            <h2 id="schedule-edit-title">Editar alcance</h2>
                            <p data-schedule-edit-project>{{ $selectedProject->name }}</p>
                        </div>
                        <button class="schedule-dialog-close" type="button" data-schedule-close aria-label="Cerrar">&times;</button>
                    </div>
                    <div class="schedule-form-grid">
                        <label class="schedule-field schedule-field-wide">
                            <span>Contratista</span>
                            <input name="contractor" maxlength="160" list="schedule-contractors" required data-schedule-edit-field="contractor">
                        </label>
                        <label class="schedule-field schedule-field-wide">
                            <span>Alcance</span>
                            <input name="title" maxlength="160" required data-schedule-edit-field="title">
                        </label>
                        <label class="schedule-field schedule-field-wide">
                            <span>Inicio</span>
                            <input type="date" name="start_date" required data-schedule-edit-field="start_date">
                        </label>
                        <label class="schedule-field schedule-field-wide">
                            <span>Fin</span>
                            <input type="date" name="end_date" required data-schedule-edit-field="end_date">
                        </label>
                        <input type="hidden" name="progress" value="0" data-schedule-edit-field="progress">
                        <input type="hidden" name="status" value="Programado" data-schedule-edit-field="status">
                        <label class="schedule-field schedule-field-full">
                            <span>Descripcion</span>
                            <textarea name="description" rows="3" maxlength="1200" data-schedule-edit-field="description"></textarea>
                        </label>
                    </div>
                    <div class="schedule-dialog-actions schedule-dialog-actions-split">
                        <button class="button danger" type="submit" name="_method" value="DELETE" formnovalidate data-schedule-delete>Eliminar</button>
                        <span>
                            <button class="button ghost" type="button" data-schedule-close>Cancelar</button>
                            <button class="button primary" type="submit" name="_method" value="PUT">Guardar cambios</button>
                        </span>
                    </div>
                </form>
            </dialog>

            <datalist id="schedule-contractors">
                @foreach ($contractors as $contractor)
                    <option value="{{ $contractor }}"></option>
                @endforeach
            </datalist>
        @endif

        <style>
            .schedule-view-switch { width: min(100%, 360px); display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 3px; margin-inline: auto; padding: 3px; border: 1px solid #bfe6e2; border-radius: 999px; background: #f8fffe; box-shadow: 0 2px 8px rgba(16, 108, 102, .1); }
            .schedule-view-tab { min-width: 0; min-height: 34px; padding: 6px 14px; border: 0; border-radius: 999px; background: transparent; color: var(--muted); font: inherit; font-size: .82rem; font-weight: 900; cursor: pointer; }
            .schedule-view-tab:hover { background: #e9f8f6; color: var(--primary-strong); }
            .schedule-view-tab.active { background: #12b5aa; color: #fff; box-shadow: 0 2px 6px rgba(11, 128, 120, .18); }
            [data-schedule-view-panel][hidden] { display: none; }
            .schedule-panel { padding: 18px; overflow: hidden; }
            .schedule-heading, .schedule-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
            .schedule-heading h2, .schedule-month-heading h3 { margin: 0; }
            .schedule-heading p { margin: 4px 0 0; color: var(--muted); }
            .schedule-month-heading { display: flex; align-items: center; justify-content: center; gap: 10px; flex: 1; }
            .schedule-month-nav { display: inline-flex; align-items: center; gap: 8px; }
            .schedule-icon-button { display: inline-grid; place-items: center; width: 34px; height: 34px; border: 1px solid var(--line); border-radius: 6px; background: #fff; color: var(--primary-strong); font-weight: 900; text-decoration: none; }
            .schedule-toolbar { margin-top: 10px; }
            .schedule-month-heading h3 { font-size: 1.18rem; }
            .schedule-toolbar-actions { display: flex; align-items: center; justify-content: flex-end; gap: 10px; flex-wrap: wrap; }
            .schedule-filter select { min-width: 210px; height: 36px; padding: 0 34px 0 10px; border: 1px solid var(--line); border-radius: 6px; background: #fff; color: var(--text); font: inherit; }
            .schedule-add-button { min-height: 36px; }
            .schedule-add-button span { font-size: 1.3rem; line-height: 0; }
            .schedule-contractor-legend { display: flex; align-items: center; min-height: 38px; margin-top: 10px; border: 1px solid var(--line); overflow-x: auto; }
            .schedule-contractor-legend span { display: inline-flex; align-items: center; gap: 8px; padding: 8px 14px; white-space: nowrap; font-weight: 700; }
            .schedule-contractor-legend span + span { border-left: 1px solid var(--line); }
            .schedule-contractor-legend i { width: 10px; height: 10px; border-radius: 999px; background: var(--legend-color); }
            .schedule-contractor-legend .schedule-empty-legend { color: var(--muted); font-weight: 500; }
            .schedule-calendar-scroll { overflow-x: auto; border: 1px solid var(--line); border-top: 0; }
            .schedule-calendar-grid { min-width: 1080px; background: #fff; }
            .schedule-weekdays { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); border-bottom: 1px solid var(--line); }
            .schedule-weekdays div { padding: 7px; text-align: center; font-weight: 900; }
            .schedule-week { display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); position: relative; border-bottom: 1px solid var(--line); }
            .schedule-week:last-child { border-bottom: 0; }
            .schedule-day { grid-row: 1 / -1; min-width: 0; padding: 7px 9px; border-right: 1px solid var(--line); background: #fff; color: var(--text); }
            .schedule-day:last-of-type { border-right: 0; }
            .schedule-day.outside-month { background: #f7f9fc; color: #98a2b3; }
            .schedule-day.today { background: #effbf8; }
            .schedule-day.today span { display: inline-grid; place-items: center; min-width: 24px; height: 24px; border-radius: 999px; background: #0ea5a0; color: #fff; font-weight: 900; }
            .schedule-event { z-index: 2; align-self: stretch; min-width: 0; margin: 3px 5px; padding: 5px 9px; border: 1px solid var(--event-line); border-left-width: 4px; border-radius: 7px; background: var(--event-background); color: var(--event-text); display: grid; align-content: center; text-align: left; overflow: hidden; cursor: pointer; }
            .schedule-event:disabled { opacity: 1; cursor: default; }
            .schedule-event[data-status="En proceso"] { border-style: dashed; border-left-style: solid; }
            .schedule-event[data-status="Concluido"] { box-shadow: inset 0 -3px #18a66a; }
            .schedule-event-title { display: flex; align-items: center; gap: 6px; min-width: 0; font-weight: 900; }
            .schedule-event-title-text, .schedule-event-meta, .schedule-event-description { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .schedule-event-title-text { min-width: 0; }
            .schedule-event-sequence { flex: 0 0 auto; padding: 1px 5px; border: 1px solid currentColor; border-radius: 4px; font-size: .68rem; line-height: 1.25; }
            .schedule-event-meta { font-size: .78rem; }
            .schedule-event-description { margin-top: 2px; font-size: .72rem; }
            .schedule-event[hidden] { display: none; }
            .schedule-status-legend { display: flex; align-items: center; justify-content: center; gap: 34px; min-height: 42px; margin-top: 10px; border: 1px solid var(--line); border-radius: 6px; }
            .schedule-status-legend span { display: inline-flex; align-items: center; gap: 8px; color: var(--muted); }
            .schedule-status-legend i { width: 24px; height: 0; border-top: 3px solid #94a3b8; }
            .schedule-status-legend i.in-progress { border-top-style: dashed; border-color: #3b82f6; }
            .schedule-status-legend i.completed { border-color: #18a66a; }
            .schedule-empty-state { display: grid; place-items: center; min-height: 260px; color: var(--muted); }
            .weekly-reports-layout { min-width: 0; padding: 4px 0 24px; }
            .weekly-reports-dashboard { width: min(calc(100% - 24px), 1500px); margin-inline: auto; display: grid; grid-template-columns: 300px minmax(0, 1fr); align-items: start; gap: 12px; }
            .weekly-report-navigation { position: sticky; top: 74px; min-width: 0; overflow: hidden; border: 1px solid #d8e4ef; border-radius: 8px; background: #fff; box-shadow: 0 8px 24px rgba(24, 62, 82, .08); }
            .weekly-report-navigation-header { min-height: 68px; display: flex; align-items: center; gap: 12px; padding: 12px 14px; background: #123a4d; color: #fff; }
            .weekly-report-navigation-header h2 { margin: 0; color: #fff; font-size: 1.05rem; line-height: 1.2; }
            .weekly-report-navigation-mark { width: 40px; height: 40px; display: grid; grid-template-columns: repeat(3, 4px); align-items: end; justify-content: center; gap: 3px; flex: 0 0 auto; padding: 9px; border-radius: 6px; background: #1b7b93; }
            .weekly-report-navigation-mark i { width: 4px; display: block; border-radius: 2px 2px 0 0; background: #fff; }
            .weekly-report-navigation-mark i:nth-child(1) { height: 10px; }
            .weekly-report-navigation-mark i:nth-child(2) { height: 18px; }
            .weekly-report-navigation-mark i:nth-child(3) { height: 14px; }
            .weekly-report-navigation-summary { min-height: 48px; display: grid; grid-template-columns: 28px minmax(0, 1fr) 28px; align-items: center; gap: 8px; padding: 7px 10px; border-bottom: 1px solid var(--line); background: #f7fafc; }
            .weekly-report-navigation-summary > a { width: 28px; height: 28px; display: grid; place-items: center; border: 1px solid #d3e0eb; border-radius: 5px; background: #fff; color: #17637f; font-weight: 900; text-decoration: none; }
            .weekly-report-navigation-summary > a:hover { border-color: #8ed4cc; background: #eefaf8; }
            .weekly-report-navigation-summary > span { min-width: 0; display: flex; align-items: center; justify-content: center; gap: 6px; overflow: hidden; color: #51657d; font-size: .74rem; text-transform: capitalize; white-space: nowrap; }
            .weekly-report-navigation-summary strong { color: #17276f; }
            .weekly-report-navigation-summary i { color: #9aabba; font-style: normal; }
            .weekly-report-menu { max-height: calc(100vh - 205px); display: grid; gap: 4px; padding: 8px; overflow-y: auto; }
            .weekly-report-menu-item { width: 100%; min-width: 0; min-height: 78px; display: grid; grid-template-columns: 32px minmax(0, 1fr) auto 10px; grid-template-rows: auto auto; align-items: center; gap: 3px 9px; padding: 10px 8px; border: 1px solid transparent; border-left: 3px solid transparent; border-radius: 6px; background: transparent; color: #334861; font: inherit; text-align: left; cursor: pointer; }
            .weekly-report-menu-item:hover { border-color: #d8e9e7; background: #f3faf9; }
            .weekly-report-menu-item.active { border-color: #a8ddd5; border-left-color: #0bad9e; background: #eaf8f6; color: #123a4d; box-shadow: 0 2px 8px rgba(16, 108, 102, .08); }
            .weekly-report-menu-calendar { position: relative; width: 27px; height: 27px; grid-row: 1 / span 2; display: inline-block; border: 2px solid #47708c; border-radius: 4px; }
            .weekly-report-menu-calendar::before { position: absolute; top: 5px; left: 0; right: 0; content: ''; border-top: 2px solid #47708c; }
            .weekly-report-menu-calendar::after { position: absolute; top: -4px; left: 5px; width: 13px; height: 5px; content: ''; border-right: 2px solid #47708c; border-left: 2px solid #47708c; }
            .weekly-report-menu-item.active .weekly-report-menu-calendar { border-color: #078b82; }
            .weekly-report-menu-item.active .weekly-report-menu-calendar::before,
            .weekly-report-menu-item.active .weekly-report-menu-calendar::after { border-color: #078b82; }
            .weekly-report-menu-copy { min-width: 0; display: grid; gap: 2px; }
            .weekly-report-menu-copy strong { overflow: hidden; color: #17276f; font-size: .8rem; text-overflow: ellipsis; white-space: nowrap; }
            .weekly-report-menu-copy small { overflow: hidden; color: #687a90; font-size: .68rem; text-overflow: ellipsis; white-space: nowrap; }
            .weekly-report-menu-progress { color: #17276f; font-size: .78rem; font-weight: 900; font-variant-numeric: tabular-nums; }
            .weekly-report-menu-status { grid-column: 2; display: inline-flex; align-items: center; gap: 5px; color: #16876e; font-size: .66rem; font-weight: 800; }
            .weekly-report-menu-status i { width: 14px; height: 14px; display: inline-grid; place-items: center; flex: 0 0 auto; border-radius: 50%; background: #20b897; color: #fff; font-size: .55rem; font-style: normal; }
            .weekly-report-menu-chevron { grid-column: 4; grid-row: 1 / span 2; color: #17637f; font-size: .95rem; font-weight: 900; }
            .weekly-reports-content { min-width: 0; width: 100%; }
            [data-weekly-report-panel][hidden] { display: none; }
            .weekly-report-workspace { overflow: hidden; border: 1px solid #d8e4ef; border-radius: 8px; background: #fff; box-shadow: 0 8px 24px rgba(24, 62, 82, .08); }
            .weekly-reports-heading { min-height: 64px; display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 8px 10px; border-bottom: 1px solid #edf2f6; }
            .weekly-reports-heading h2, .weekly-reports-heading p { margin: 0; }
            .weekly-reports-heading h2 { color: #111d50; font-size: 1.18rem; }
            .weekly-reports-heading-copy { min-width: 0; }
            .weekly-reports-heading p { display: flex; align-items: center; gap: 9px; margin-top: 3px; color: var(--muted); font-size: .78rem; }
            .weekly-report-project-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 8px; border-radius: 999px; background: #e5f8f2; color: #117f69; font-size: .66rem; font-weight: 800; white-space: nowrap; }
            .weekly-report-project-badge i { width: 14px; height: 14px; display: inline-grid; place-items: center; border-radius: 50%; background: #19b594; color: #fff; font-size: .55rem; font-style: normal; }
            .weekly-report-toolbar { min-width: 0; display: flex; align-items: center; justify-content: flex-end; gap: 6px; }
            .weekly-report-toolbar > button { width: 30px; height: 30px; display: grid; place-items: center; flex: 0 0 auto; border: 1px solid #d3e0eb; border-radius: 6px; background: #fff; color: #126986; font: inherit; font-weight: 900; cursor: pointer; }
            .weekly-report-toolbar > button:hover:not(:disabled) { border-color: #8ed4cc; background: #eefaf8; }
            .weekly-report-toolbar > button:disabled { color: #a8b6c4; cursor: default; opacity: .55; }
            .weekly-report-toolbar > strong { color: #1c315f; font-size: .66rem; white-space: nowrap; }
            .weekly-report-toolbar > strong span { font-weight: 650; }
            .weekly-report-toolbar-status { min-height: 30px; display: inline-flex; align-items: center; gap: 5px; margin-left: 4px; padding: 5px 9px; border-radius: 999px; background: #e4f7f1; color: #0f8069; font-size: .64rem; font-weight: 850; white-space: nowrap; }
            .weekly-report-toolbar-status i { width: 15px; height: 15px; display: inline-grid; place-items: center; border-radius: 50%; background: #20b897; color: #fff; font-size: .58rem; font-style: normal; }
            .weekly-report-workspace > .weekly-report-card { border: 0; border-radius: 0; box-shadow: none; }
            .weekly-report-card { overflow: hidden; border: 1px solid #d8e4ef; border-radius: 8px; background: #fff; box-shadow: 0 8px 24px rgba(24, 62, 82, .09); }
            .weekly-report-card-embedded { border: 0; border-radius: 0; box-shadow: none; }
            .weekly-report-cover { position: relative; min-height: 190px; aspect-ratio: 3.6 / 1; overflow: hidden; background: #16394b; }
            .weekly-report-workspace .weekly-report-cover { height: clamp(170px, 13vw, 215px); min-height: 0; margin: 0 10px; border-radius: 6px; aspect-ratio: auto; }
            .weekly-report-card-embedded .weekly-report-cover { margin: 10px 10px 0; border-radius: 6px; }
            .weekly-report-cover::after { position: absolute; inset: 0; z-index: 1; content: ''; background: rgba(6, 36, 52, .42); }
            .weekly-report-cover img { width: 100%; height: 100%; display: block; object-fit: cover; object-position: center 48%; }
            .weekly-report-cover-copy { position: absolute; inset: 0; z-index: 2; width: min(64%, 500px); display: flex; flex-direction: column; justify-content: center; gap: 6px; padding: 24px 32px; color: #fff; }
            .weekly-report-cover-copy strong { font-size: 1.7rem; line-height: 1.05; text-transform: uppercase; }
            .weekly-report-cover-copy span { font-size: .8rem; font-weight: 750; }
            .weekly-report-meta { min-height: 42px; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 8px 14px; border-bottom: 1px solid var(--line); color: var(--muted); font-size: .78rem; }
            .weekly-report-meta > span { display: inline-flex; align-items: center; gap: 7px; }
            .weekly-report-generated { padding: 5px 9px; border-radius: 999px; background: #e4f7f1; color: #0f8e72; font-weight: 800; }
            .weekly-report-generated i { width: 15px; height: 15px; display: inline-grid; place-items: center; border-radius: 50%; background: #20b897; color: #fff; font-size: .65rem; font-style: normal; }
            .weekly-report-calendar-icon { position: relative; width: 15px; height: 14px; display: inline-block; border: 2px solid #47708c; border-radius: 3px; }
            .weekly-report-calendar-icon::before { position: absolute; top: 2px; left: 0; right: 0; content: ''; border-top: 2px solid #47708c; }
            .weekly-report-metrics { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; padding: 10px; }
            .weekly-report-metric { min-width: 0; min-height: 62px; display: flex; align-items: center; justify-content: center; gap: 10px; padding: 8px 10px; border: 1px solid #dce7f0; border-radius: 6px; background: #fff; box-shadow: 0 2px 8px rgba(24, 62, 82, .04); }
            .weekly-report-metric-copy { min-width: 0; display: grid; gap: 1px; }
            .weekly-report-metric-copy strong { color: #171c68; font-size: 1.38rem; line-height: 1.05; font-variant-numeric: tabular-nums; }
            .weekly-report-metric-copy strong.negative { color: #dc2626; }
            .weekly-report-metric-copy strong.positive { color: #0a8b69; }
            .weekly-report-metric-copy small { color: #52657d; font-size: .72rem; }
            .weekly-report-metric-icon { width: 36px; height: 36px; display: grid; place-items: center; flex: 0 0 auto; border-radius: 50%; background: #eff8ff; color: #0785d1; }
            .weekly-report-metric-icon.chart { display: flex; align-items: flex-end; justify-content: center; gap: 3px; padding-bottom: 10px; }
            .weekly-report-metric-icon.chart i { width: 3px; border-radius: 2px 2px 0 0; background: currentColor; }
            .weekly-report-metric-icon.chart i:nth-child(1) { height: 9px; }
            .weekly-report-metric-icon.chart i:nth-child(2) { height: 18px; }
            .weekly-report-metric-icon.chart i:nth-child(3) { height: 13px; }
            .weekly-report-metric-icon.calendar i { position: relative; width: 21px; height: 20px; border: 2px solid currentColor; border-radius: 4px; }
            .weekly-report-metric-icon.calendar i::before { position: absolute; top: 4px; left: 0; right: 0; content: ''; border-top: 2px solid currentColor; }
            .weekly-report-metric-icon.calendar i::after { position: absolute; top: -5px; left: 4px; width: 9px; height: 5px; content: ''; border-right: 2px solid currentColor; border-left: 2px solid currentColor; }
            .weekly-report-metric-icon.variation { background: #fff0f0; color: #dc2626; }
            .weekly-report-metric-icon.variation.positive { background: #eaf8f2; color: #0a8b69; }
            .weekly-report-metric-icon.variation i { position: relative; width: 22px; height: 24px; }
            .weekly-report-metric-icon.variation i::before { position: absolute; top: 1px; left: 10px; height: 18px; content: ''; border-left: 2px solid currentColor; }
            .weekly-report-metric-icon.variation i::after { position: absolute; left: 5px; bottom: 2px; width: 10px; height: 10px; content: ''; border-right: 2px solid currentColor; border-bottom: 2px solid currentColor; transform: rotate(45deg); }
            .weekly-report-metric-icon.photo i { position: relative; width: 22px; height: 20px; overflow: hidden; border: 2px solid currentColor; border-radius: 4px; }
            .weekly-report-metric-icon.photo i::before { position: absolute; right: 3px; top: 3px; width: 4px; height: 4px; content: ''; border-radius: 50%; background: currentColor; }
            .weekly-report-metric-icon.photo i::after { position: absolute; left: 3px; bottom: -2px; width: 12px; height: 12px; content: ''; border-top: 2px solid currentColor; border-left: 2px solid currentColor; transform: rotate(45deg); }
            .weekly-report-overview, .weekly-report-packages { min-width: 0; margin: 0 10px 8px; border: 1px solid #dce7f0; border-radius: 6px; background: #fff; }
            .weekly-report-overview { padding: 10px 12px; }
            .weekly-report-overview header, .weekly-report-note-card header { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
            .weekly-report-overview h3, .weekly-report-packages > h3, .weekly-report-note-card h3 { display: flex; align-items: center; gap: 8px; margin: 0; color: #17276f; font-size: .82rem; }
            .weekly-report-overview p, .weekly-report-note-card p { margin: 6px 0 0; color: #405b86; font-size: .71rem; line-height: 1.45; }
            .weekly-report-auto-copy { min-height: 21px; display: inline-flex; align-items: center; gap: 5px; padding: 3px 8px; border-radius: 999px; background: #e4f7f1; color: #10836d; font-size: .61rem; font-weight: 750; white-space: nowrap; }
            .weekly-report-auto-copy i { width: 7px; height: 7px; border-radius: 50%; background: #13ad91; }
            .weekly-report-packages { padding: 8px; }
            .weekly-report-packages > h3 { margin-bottom: 7px; }
            .weekly-report-camera-icon { position: relative; width: 22px; height: 16px; display: inline-block; border-radius: 3px; background: #098bd5; }
            .weekly-report-camera-icon::before { position: absolute; top: -4px; left: 4px; width: 8px; height: 5px; content: ''; border-radius: 2px 2px 0 0; background: #098bd5; }
            .weekly-report-camera-icon::after { position: absolute; top: 4px; left: 8px; width: 6px; height: 6px; content: ''; border: 2px solid #fff; border-radius: 50%; }
            .weekly-report-document-icon { position: relative; width: 18px; height: 21px; display: inline-block; border-radius: 3px; background: #098bd5; }
            .weekly-report-document-icon::before { position: absolute; top: 5px; left: 5px; width: 8px; height: 6px; content: ''; border-top: 2px solid #fff; border-bottom: 2px solid #fff; }
            .weekly-report-package-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); align-items: stretch; gap: 7px; }
            .weekly-report-package { min-width: 0; overflow: hidden; border: 1px solid #d9e5ef; border-radius: 6px; background: #fbfdff; }
            .weekly-report-package-header { min-height: 39px; display: grid; grid-template-columns: 25px minmax(0, auto) auto 1fr; align-items: center; gap: 7px; padding: 6px 8px; border-bottom: 1px solid #d9e5ef; color: #111d62; }
            .weekly-report-package-header strong { min-width: 0; overflow: hidden; font-size: .76rem; text-overflow: ellipsis; white-space: nowrap; }
            .weekly-report-package-header > span:not(.weekly-report-contractor-icon) { padding: 3px 8px; border-radius: 999px; background: #dceeff; color: #12618f; font-size: .59rem; font-weight: 800; white-space: nowrap; }
            .weekly-report-package-header > i { justify-self: end; color: #132778; font-size: 1rem; font-style: normal; font-weight: 900; line-height: 1; transform: translateY(-2px); }
            .weekly-report-contractor-icon { position: relative; width: 22px; height: 22px; display: inline-block; border-radius: 50%; background: #e9f5ff; }
            .weekly-report-contractor-icon::before { position: absolute; left: 6px; top: 3px; width: 10px; height: 8px; content: ''; border-radius: 7px 7px 3px 3px; background: #0879c4; }
            .weekly-report-contractor-icon::after { position: absolute; left: 4px; bottom: 3px; width: 14px; height: 7px; content: ''; border-radius: 7px 7px 3px 3px; background: #0879c4; }
            .weekly-report-package-groups { display: grid; gap: 7px; padding: 6px; }
            .weekly-report-package-group + .weekly-report-package-group { padding-top: 7px; border-top: 1px solid #d9e5ef; }
            .weekly-report-package-group h4 { margin: 0 0 5px; color: #17276f; font-size: .68rem; }
            .weekly-report-package-group h4 span { font-weight: 700; }
            .weekly-report-package-photos { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 6px; }
            .weekly-report-package-photos figure { min-width: 0; margin: 0; }
            .weekly-report-package-photos figure:last-child:nth-child(odd) { grid-column: 1 / -1; }
            .weekly-report-package-photos img { width: 100%; aspect-ratio: 1.55 / 1; display: block; object-fit: cover; border-radius: 4px; background: #e8eef3; }
            .weekly-report-package-photos figcaption { min-width: 0; display: grid; gap: 2px; margin-top: 4px; color: #315c9c; font-size: .57rem; }
            .weekly-report-package-photos figcaption > span { display: flex; align-items: center; gap: 4px; color: #54749f; white-space: nowrap; }
            .weekly-report-package-photos figcaption strong { overflow: hidden; font-size: .59rem; font-weight: 650; text-overflow: ellipsis; white-space: nowrap; }
            .weekly-report-tiny-calendar { position: relative; width: 9px; height: 9px; display: inline-block; border: 1px solid #267cc2; border-radius: 1px; }
            .weekly-report-tiny-calendar::before { position: absolute; top: 2px; left: 0; right: 0; content: ''; border-top: 1px solid #267cc2; }
            .weekly-report-followup { display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(0, 1.15fr) minmax(220px, .8fr); gap: 8px; padding: 0 10px 8px; }
            .weekly-report-note-card, .weekly-report-incidents { min-width: 0; min-height: 78px; border: 1px solid #dce7f0; border-radius: 6px; background: #fff; }
            .weekly-report-note-card { padding: 8px 9px; }
            .weekly-report-note-card h3 { font-size: .7rem; }
            .weekly-report-note-card p { font-size: .61rem; }
            .weekly-report-alert-icon { width: 18px; height: 18px; display: inline-grid; place-items: center; border-radius: 4px; background: #098bd5; color: #fff; font-size: .7rem; }
            .weekly-report-next-icon { position: relative; width: 18px; height: 18px; display: inline-block; border: 2px solid #098bd5; border-radius: 3px; }
            .weekly-report-next-icon::before { position: absolute; top: 3px; left: 0; right: 0; content: ''; border-top: 2px solid #098bd5; }
            .weekly-report-incidents { display: flex; align-items: center; gap: 11px; padding: 9px 11px; border-color: #f2d39a; background: #fff9ec; color: #32455e; font-size: .72rem; }
            .weekly-report-warning-icon { width: 34px; height: 32px; display: inline-grid; place-items: center; flex: 0 0 auto; border-radius: 6px; background: #f59e0b; color: #fff; font-size: 1rem; font-weight: 900; }
            .weekly-report-incidents.clear { border-color: #a9dfcf; background: #eefaf6; }
            .weekly-report-incidents.clear .weekly-report-warning-icon { border-radius: 50%; background: #19a77f; }
            .weekly-report-incidents-chevron { margin-left: auto; color: #17637f; font-size: 1rem; font-weight: 900; }
            .weekly-report-actions { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; padding: 0 10px 10px; }
            .weekly-report-action { min-width: 0; min-height: 44px; display: inline-flex; align-items: center; justify-content: center; gap: 9px; border: 1px solid #6f91ff; border-radius: 6px; background: #fff; color: #17276f; font: inherit; font-size: .78rem; font-weight: 850; cursor: pointer; }
            .weekly-report-action:hover { background: #f2f6ff; }
            .weekly-report-action.primary { border-color: #0bad9e; background: #10b7a9; color: #fff; }
            .weekly-report-action.primary:hover { background: #07998d; }
            .weekly-report-eye-icon { position: relative; width: 18px; height: 12px; display: inline-block; border: 2px solid currentColor; border-radius: 50%; }
            .weekly-report-eye-icon::after { position: absolute; top: 50%; left: 50%; width: 4px; height: 4px; content: ''; border-radius: 50%; background: currentColor; transform: translate(-50%, -50%); }
            .weekly-report-download-icon { position: relative; width: 17px; height: 18px; display: inline-block; border-bottom: 2px solid currentColor; }
            .weekly-report-download-icon::before { position: absolute; top: 0; left: 8px; height: 11px; content: ''; border-left: 2px solid currentColor; }
            .weekly-report-download-icon::after { position: absolute; top: 6px; left: 5px; width: 6px; height: 6px; content: ''; border-right: 2px solid currentColor; border-bottom: 2px solid currentColor; transform: rotate(45deg); }
            .weekly-report-share-icon { position: relative; width: 19px; height: 18px; display: inline-block; }
            .weekly-report-share-icon::before { position: absolute; top: 1px; right: 1px; width: 5px; height: 5px; content: ''; border: 2px solid currentColor; border-radius: 50%; box-shadow: -12px 7px 0 -2px currentColor, 0 13px 0 -2px currentColor; }
            .weekly-report-share-icon::after { position: absolute; top: 6px; left: 5px; width: 10px; height: 8px; content: ''; border-top: 2px solid currentColor; border-bottom: 2px solid currentColor; transform: skewY(-28deg); }
            .weekly-report-generation-note { min-height: 40px; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 8px 12px; border-top: 1px solid #dce7f0; background: #f2f8fb; color: #62788f; font-size: .7rem; text-align: center; }
            .weekly-report-generation-note i { width: 16px; height: 16px; display: inline-grid; place-items: center; flex: 0 0 auto; border-radius: 50%; background: #67a9c0; color: #fff; font-size: .65rem; font-style: normal; font-weight: 900; }
            .weekly-report-notice { position: fixed; top: 72px; right: 22px; z-index: 90; width: min(360px, calc(100vw - 32px)); display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 12px 12px 12px 15px; border: 1px solid #99d8c6; border-radius: 8px; background: #edfbf6; color: #11634f; box-shadow: 0 16px 38px rgba(24, 62, 82, .16); font-size: .82rem; font-weight: 750; }
            .weekly-report-notice[hidden] { display: none; }
            .weekly-report-notice button { width: 28px; height: 28px; display: grid; place-items: center; flex: 0 0 auto; border: 0; border-radius: 50%; background: transparent; color: #11634f; font-size: 1.1rem; cursor: pointer; }
            .weekly-report-notice button:hover { background: #d9f4e9; }
            .weekly-report-dialog { width: min(1200px, calc(100vw - 34px)); max-height: calc(100vh - 40px); padding: 0; overflow-y: auto; border: 0; border-radius: 8px; color: var(--text); box-shadow: 0 24px 70px rgba(24, 34, 53, .28); }
            .weekly-report-dialog::backdrop { background: rgba(16, 43, 58, .48); }
            .weekly-report-dialog-header { position: sticky; top: 0; z-index: 5; display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 12px 14px; border-bottom: 1px solid var(--line); background: #fff; }
            .weekly-report-dialog-header span { color: var(--muted); font-size: .72rem; font-weight: 800; text-transform: uppercase; }
            .weekly-report-dialog-header h2 { margin: 2px 0 0; font-size: 1.05rem; }
            .weekly-report-dialog-header button { width: 32px; height: 32px; border: 1px solid var(--line); border-radius: 50%; background: #fff; color: #bd2929; font-size: 1.15rem; cursor: pointer; }
            .schedule-dialog { width: min(760px, calc(100vw - 34px)); padding: 0; border: 0; border-radius: 8px; color: var(--text); box-shadow: 0 24px 70px rgba(24, 34, 53, .28); }
            .schedule-dialog::backdrop { background: rgba(16, 43, 58, .38); }
            .schedule-dialog form { padding: 18px; }
            .schedule-dialog-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 16px; }
            .schedule-dialog-header h2 { margin: 0; }
            .schedule-dialog-header p { margin: 4px 0 0; color: var(--muted); }
            .schedule-dialog-close { width: 34px; height: 34px; border: 1px solid var(--line); border-radius: 6px; background: #fff; color: var(--text); font-size: 1.2rem; cursor: pointer; }
            .schedule-form-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
            .schedule-field { display: grid; gap: 5px; min-width: 0; }
            .schedule-field span { font-weight: 800; }
            .schedule-field input, .schedule-field select, .schedule-field textarea { width: 100%; min-width: 0; border: 1px solid var(--line); border-radius: 6px; background: #fff; color: var(--text); font: inherit; }
            .schedule-field input, .schedule-field select { height: 36px; padding: 0 9px; }
            .schedule-field textarea { padding: 9px; resize: vertical; }
            .schedule-field-wide { grid-column: span 2; }
            .schedule-field-full { grid-column: 1 / -1; }
            .schedule-dialog-actions { display: flex; align-items: center; justify-content: flex-end; gap: 8px; margin-top: 18px; }
            .schedule-dialog-actions-split { justify-content: space-between; }
            .schedule-dialog-actions-split span { display: inline-flex; gap: 8px; }
            .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
            @media (max-width: 1180px) {
                .weekly-reports-dashboard { grid-template-columns: minmax(0, 1fr); }
                .weekly-report-navigation { position: static; }
                .weekly-report-menu { max-height: none; grid-template-columns: repeat(2, minmax(0, 1fr)); }
            }
            @media (max-width: 900px) {
                .schedule-heading, .schedule-toolbar { align-items: stretch; flex-direction: column; }
                .schedule-month-heading { align-self: flex-start; justify-content: flex-start; flex-wrap: wrap; }
                .schedule-toolbar-actions { justify-content: stretch; }
                .schedule-filter { flex: 1 1 220px; }
                .schedule-filter select { width: 100%; min-width: 0; }
                .weekly-report-package-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                .weekly-report-followup { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                .weekly-report-incidents { grid-column: 1 / -1; }
            }
            @media (max-width: 640px) {
                .schedule-view-switch { border-radius: 8px; }
                .schedule-view-tab { border-radius: 6px; }
                .schedule-panel { padding: 12px; }
                .schedule-form-grid { grid-template-columns: 1fr 1fr; }
                .schedule-field-wide, .schedule-field-full { grid-column: 1 / -1; }
                .schedule-status-legend { justify-content: flex-start; gap: 18px; overflow-x: auto; padding: 0 12px; }
                .weekly-reports-heading { align-items: flex-start; flex-direction: column; }
                .weekly-reports-heading p { align-items: flex-start; flex-direction: column; }
                .weekly-report-toolbar { width: 100%; justify-content: flex-start; flex-wrap: wrap; }
                .weekly-report-toolbar-status { margin-left: 0; }
                .weekly-report-workspace .weekly-report-cover { height: 180px; }
                .weekly-report-cover-copy { width: 78%; padding: 18px; }
                .weekly-report-cover-copy strong { font-size: 1.15rem; }
                .weekly-report-meta { align-items: flex-start; flex-direction: column; }
                .weekly-report-menu { grid-template-columns: minmax(0, 1fr); }
                .weekly-report-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                .weekly-report-overview header, .weekly-report-note-card header { align-items: flex-start; flex-direction: column; }
                .weekly-report-package-grid, .weekly-report-followup { grid-template-columns: minmax(0, 1fr); }
                .weekly-report-incidents { grid-column: auto; }
            }
            @media (max-width: 420px) {
                .weekly-report-actions { grid-template-columns: 1fr; }
            }
            @media print {
                html.weekly-report-printing body * { visibility: hidden !important; }
                html.weekly-report-printing .weekly-report-dialog[open],
                html.weekly-report-printing .weekly-report-dialog[open] * { visibility: visible !important; }
                html.weekly-report-printing .weekly-report-dialog[open] { position: absolute; inset: 0; width: 100%; max-height: none; overflow: visible; border: 0; box-shadow: none; }
                html.weekly-report-printing .weekly-report-dialog-header button { display: none; }
                html.weekly-report-printing .weekly-report-card { break-inside: avoid; }
                html.weekly-report-printing { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            }
        </style>

        @php
            $scheduleItemsPayload = $scheduleItems->mapWithKeys(function ($item) {
                return [$item->id => [
                    'title' => $item->title,
                    'contractor' => $item->contractor,
                    'description' => $item->description,
                    'start_date' => $item->start_date?->format('Y-m-d'),
                    'end_date' => $item->end_date?->format('Y-m-d'),
                    'progress' => $item->progress,
                    'status' => $item->status,
                ]];
            });
        @endphp

        <script>
            (() => {
                const scheduleViewTabs = [...document.querySelectorAll('[data-schedule-view-tab]')];
                const scheduleViewPanels = [...document.querySelectorAll('[data-schedule-view-panel]')];
                const initialScheduleView = @json($calendarView);
                const setScheduleView = (view, updateUrl = true) => {
                    const nextView = view === 'advances' ? 'advances' : 'calendar';

                    scheduleViewTabs.forEach((tab) => {
                        const active = tab.dataset.scheduleViewTab === nextView;
                        tab.classList.toggle('active', active);
                        tab.setAttribute('aria-selected', active ? 'true' : 'false');
                    });
                    scheduleViewPanels.forEach((panel) => {
                        panel.hidden = panel.dataset.scheduleViewPanel !== nextView;
                    });
                    document.querySelectorAll('[data-calendar-project-link]').forEach((link) => {
                        const projectUrl = new URL(link.href, window.location.href);
                        if (nextView === 'advances') projectUrl.searchParams.set('view', 'advances');
                        else projectUrl.searchParams.delete('view');
                        link.href = projectUrl.toString();
                    });

                    if (updateUrl) {
                        const currentUrl = new URL(window.location.href);
                        if (nextView === 'advances') currentUrl.searchParams.set('view', 'advances');
                        else currentUrl.searchParams.delete('view');
                        window.history.replaceState({}, '', currentUrl.toString());
                    }
                };

                scheduleViewTabs.forEach((tab) => {
                    tab.addEventListener('click', () => setScheduleView(tab.dataset.scheduleViewTab));
                });
                setScheduleView(initialScheduleView, false);

                const weeklyReports = new Map(@json($weeklyReports).map((report) => [report.id, report]));
                const reportSelectButtons = [...document.querySelectorAll('[data-weekly-report-select]')];
                const reportStepButtons = [...document.querySelectorAll('[data-weekly-report-select-target]')];
                const inlineReportPanels = [...document.querySelectorAll('[data-weekly-report-panel]')];
                const reportDialog = (id) => document.getElementById(`weekly-report-dialog-${id}`);
                const notice = document.querySelector('[data-weekly-report-notice]');
                const noticeText = notice?.querySelector('[data-weekly-report-notice-text]');
                let noticeTimer;
                const selectWeeklyReport = (id, updateUrl = true) => {
                    const selectedButton = reportSelectButtons.find((button) => button.dataset.weeklyReportSelect === id);
                    if (!selectedButton) return;

                    reportSelectButtons.forEach((button) => {
                        const active = button === selectedButton;
                        button.classList.toggle('active', active);
                        button.setAttribute('aria-current', active ? 'true' : 'false');
                    });
                    inlineReportPanels.forEach((panel) => {
                        panel.hidden = panel.dataset.weeklyReportPanel !== id;
                    });

                    if (updateUrl) {
                        const url = new URL(window.location.href);
                        url.hash = `weekly-report-${id}`;
                        window.history.replaceState({}, '', url.toString());
                    }
                };
                const showReportNotice = (message) => {
                    if (!notice || !noticeText) return;
                    window.clearTimeout(noticeTimer);
                    noticeText.textContent = message;
                    notice.hidden = false;
                    noticeTimer = window.setTimeout(() => { notice.hidden = true; }, 4200);
                };
                const openWeeklyReport = (id) => {
                    const dialog = reportDialog(id);
                    if (!dialog) return;
                    if (typeof dialog.showModal === 'function') dialog.showModal();
                    else dialog.setAttribute('open', 'open');
                };
                const copyReportLink = async (url) => {
                    if (navigator.clipboard?.writeText) {
                        await navigator.clipboard.writeText(url);
                        return;
                    }

                    const field = document.createElement('textarea');
                    field.value = url;
                    field.style.position = 'fixed';
                    field.style.opacity = '0';
                    document.body.appendChild(field);
                    field.select();
                    document.execCommand('copy');
                    field.remove();
                };

                reportSelectButtons.forEach((button) => {
                    button.addEventListener('click', () => selectWeeklyReport(button.dataset.weeklyReportSelect));
                });
                reportStepButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        if (button.dataset.weeklyReportSelectTarget) {
                            selectWeeklyReport(button.dataset.weeklyReportSelectTarget);
                        }
                    });
                });
                const requestedReportId = window.location.hash.startsWith('#weekly-report-')
                    ? window.location.hash.slice('#weekly-report-'.length)
                    : null;
                if (requestedReportId && weeklyReports.has(requestedReportId)) {
                    selectWeeklyReport(requestedReportId, false);
                }

                document.querySelectorAll('[data-weekly-report-open]').forEach((button) => {
                    button.addEventListener('click', () => openWeeklyReport(button.dataset.weeklyReportOpen));
                });
                document.querySelectorAll('[data-weekly-report-close]').forEach((button) => {
                    button.addEventListener('click', () => button.closest('dialog')?.close());
                });
                document.querySelectorAll('[data-weekly-report-dialog]').forEach((dialog) => {
                    dialog.addEventListener('click', (event) => {
                        if (event.target === dialog) dialog.close();
                    });
                });
                document.querySelectorAll('[data-weekly-report-print]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const dialog = reportDialog(button.dataset.weeklyReportPrint);
                        if (!dialog) return;
                        const wasOpen = dialog.open;
                        if (!wasOpen) openWeeklyReport(button.dataset.weeklyReportPrint);
                        document.documentElement.classList.add('weekly-report-printing');
                        window.print();
                        document.documentElement.classList.remove('weekly-report-printing');
                        if (!wasOpen) dialog.close();
                    });
                });
                document.querySelectorAll('[data-weekly-report-share]').forEach((button) => {
                    button.addEventListener('click', async () => {
                        const report = weeklyReports.get(button.dataset.weeklyReportShare);
                        if (!report) return;
                        const url = new URL(window.location.href);
                        url.hash = `weekly-report-${report.id}`;
                        const shareData = {
                            title: `Reporte semanal ${report.project_key}`,
                            text: `${report.week_label}: ${report.actual_progress}% de avance real.`,
                            url: url.toString(),
                        };

                        try {
                            if (navigator.share) {
                                await navigator.share(shareData);
                                showReportNotice('Reporte compartido correctamente.');
                            } else {
                                await copyReportLink(shareData.url);
                                showReportNotice('Enlace del reporte copiado correctamente.');
                            }
                        } catch (error) {
                            if (error?.name !== 'AbortError') {
                                showReportNotice('No fue posible compartir el reporte.');
                            }
                        }
                    });
                });
                notice?.querySelector('[data-weekly-report-notice-close]')?.addEventListener('click', () => {
                    notice.hidden = true;
                });

                document.querySelectorAll('[data-construction-carousel]').forEach((carousel) => {
                    const track = carousel.querySelector('[data-construction-carousel-track]');
                    const amount = () => Math.max(220, Math.round((track?.clientWidth || 260) * 0.72));
                    carousel.querySelector('[data-carousel-prev]')?.addEventListener('click', () => track?.scrollBy({ left: -amount(), behavior: 'smooth' }));
                    carousel.querySelector('[data-carousel-next]')?.addEventListener('click', () => track?.scrollBy({ left: amount(), behavior: 'smooth' }));
                });

                const calendar = document.querySelector('[data-schedule-calendar]');
                const contractorFilter = calendar?.querySelector('[data-calendar-contractor-filter]');
                const scopeFilter = calendar?.querySelector('[data-calendar-scope-filter]');
                const applyFilters = () => {
                    const contractor = contractorFilter?.value || '';
                    const scope = scopeFilter?.value || '';
                    calendar?.querySelectorAll('[data-schedule-event]').forEach((eventButton) => {
                        eventButton.hidden = Boolean(
                            (contractor && eventButton.dataset.contractor !== contractor)
                            || (scope && eventButton.dataset.scope !== scope)
                        );
                    });
                    calendar?.querySelectorAll('[data-calendar-contractor-legend]').forEach((legendItem) => {
                        legendItem.hidden = Boolean(contractor && legendItem.dataset.calendarContractorLegend !== contractor);
                    });
                };
                contractorFilter?.addEventListener('change', applyFilters);
                scopeFilter?.addEventListener('change', applyFilters);

                const createDialog = document.querySelector('[data-schedule-create-dialog]');
                const editDialog = document.querySelector('[data-schedule-edit-dialog]');
                const editForm = document.querySelector('[data-schedule-edit-form]');
                const scheduleItems = @json($scheduleItemsPayload);
                const updateRoute = @json(route('construction.schedule-items.update', ['scheduleItem' => '__ITEM__']));
                const openDialog = (dialog) => {
                    if (!dialog) return;
                    if (typeof dialog.showModal === 'function') dialog.showModal();
                    else dialog.setAttribute('open', 'open');
                };

                document.querySelector('[data-schedule-create]')?.addEventListener('click', () => openDialog(createDialog));
                document.querySelectorAll('[data-schedule-close]').forEach((button) => {
                    button.addEventListener('click', () => button.closest('dialog')?.close());
                });
                document.querySelectorAll('.schedule-dialog').forEach((dialog) => {
                    dialog.addEventListener('click', (event) => {
                        if (event.target === dialog) dialog.close();
                    });
                });

                document.querySelectorAll('[data-schedule-edit]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const item = scheduleItems[button.dataset.scheduleEdit];
                        if (!item || !editForm) return;
                        editForm.action = updateRoute.replace('__ITEM__', button.dataset.scheduleEdit);
                        Object.entries(item).forEach(([field, value]) => {
                            const input = editForm.querySelector(`[data-schedule-edit-field="${field}"]`);
                            if (input) input.value = value ?? '';
                        });
                        openDialog(editDialog);
                    });
                });

                document.querySelector('[data-schedule-delete]')?.addEventListener('click', (event) => {
                    if (!window.confirm('Estas seguro que quieres eliminar este alcance?')) {
                        event.preventDefault();
                    }
                });

                @if ($errors->any())
                    openDialog(createDialog);
                @endif
            })();
        </script>
    </x-app-shell>
@endsection
