@extends('layouts.app')

@section('body')
    <x-app-shell title="Calendario">
        <section class="panel construction-carousel-panel" data-construction-carousel>
            <div class="construction-carousel-header">
                <div class="construction-carousel-title">
                    <span class="construction-carousel-count">{{ $activeProjects->count() }}</span>
                    <h2>Obras activas</h2>
                </div>
                <a class="button ghost small" href="{{ route('construction.dashboard') }}">Atras</a>
            </div>

            <div class="construction-carousel-shell">
                <button class="construction-carousel-nav" type="button" data-carousel-prev aria-label="Obra anterior">&lt;</button>
                <div class="construction-carousel-track" data-construction-carousel-track>
                    @forelse ($activeProjects as $project)
                        <a
                            class="construction-project-tile {{ $project->id === $selectedProjectId ? 'active' : '' }}"
                            href="{{ route('construction.placeholder', ['section' => 'calendario', 'project' => $project->id, 'month' => $monthValue]) }}"
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

        <section class="panel schedule-panel" data-schedule-calendar>
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
            @media (max-width: 900px) {
                .schedule-heading, .schedule-toolbar { align-items: stretch; flex-direction: column; }
                .schedule-month-heading { align-self: flex-start; justify-content: flex-start; flex-wrap: wrap; }
                .schedule-toolbar-actions { justify-content: stretch; }
                .schedule-filter { flex: 1 1 220px; }
                .schedule-filter select { width: 100%; min-width: 0; }
            }
            @media (max-width: 640px) {
                .schedule-panel { padding: 12px; }
                .schedule-form-grid { grid-template-columns: 1fr 1fr; }
                .schedule-field-wide, .schedule-field-full { grid-column: 1 / -1; }
                .schedule-status-legend { justify-content: flex-start; gap: 18px; overflow-x: auto; padding: 0 12px; }
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
