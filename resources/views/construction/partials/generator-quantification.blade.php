@php
    $generatorLevels = collect($generatorPanel['levels'] ?? []);
    $generatorCategories = collect($generatorPanel['categories'] ?? []);
    $selectedGeneratorLevel = $generatorPanel['selected_level'] ?? $generatorLevels->first()['key'] ?? null;
    $selectedProjectName = $activeProjects->first()?->name ?? 'Sin obra seleccionada';
@endphp

<section class="panel generator-levels-panel" data-generator-levels data-no-section-export>
    <div class="panel-header">
        <div class="panel-header-title">
            <h2>Niveles de la obra</h2>
            <p class="fine-print">Selecciona el nivel que deseas cuantificar.</p>
        </div>
    </div>

    <div class="generator-level-shell">
        <button class="generator-level-nav" type="button" data-generator-level-prev aria-label="Nivel anterior">&lt;</button>

        <div class="generator-level-track" data-generator-level-track>
            @foreach ($generatorLevels as $level)
                @php
                    $isSelectedLevel = $level['key'] === $selectedGeneratorLevel;
                @endphp
                <button
                    class="generator-level-card {{ $isSelectedLevel ? 'is-active' : '' }}"
                    type="button"
                    data-generator-level="{{ $level['key'] }}"
                    data-generator-level-name="{{ $level['name'] }}"
                    data-generator-level-value="{{ number_format((float) $level['area'], 2, '.', '') }}"
                    aria-pressed="{{ $isSelectedLevel ? 'true' : 'false' }}"
                >
                    <span class="generator-level-code">{{ $level['short'] }}</span>
                    <span>
                        <strong>{{ $level['name'] }}</strong>
                        <small data-generator-level-area>{{ number_format((float) $level['area'], 2) }} m2</small>
                    </span>
                    <span class="generator-level-check" aria-hidden="true">{{ $isSelectedLevel ? 'OK' : '' }}</span>
                </button>
            @endforeach
        </div>

        <button class="generator-level-nav" type="button" data-generator-level-next aria-label="Nivel siguiente">&gt;</button>
    </div>
</section>

<section class="panel generator-detail-panel" data-generator-panel data-no-section-export>
    <div class="panel-header generator-detail-header">
        <div class="panel-header-title">
            <h2>Detalle de cuantificacion</h2>
            <p class="fine-print">
                <span data-generator-project-name>{{ $selectedProjectName }}</span>
                <span aria-hidden="true"> / </span>
                <strong data-generator-level-name>{{ $generatorLevels->firstWhere('key', $selectedGeneratorLevel)['name'] ?? '' }}</strong>
            </p>
        </div>
        <div class="generator-header-actions">
            <span class="fine-print" data-generator-status aria-live="polite"></span>
            <button class="button ghost small" type="button" data-generator-download>Descargar Excel</button>
        </div>
    </div>

    <div class="generator-tabs" role="tablist" aria-label="Vista de generadores">
        <button class="generator-tab is-active" type="button" role="tab" aria-selected="true" data-generator-tab="capture">Captura</button>
        <button class="generator-tab" type="button" role="tab" aria-selected="false" data-generator-tab="history">Historial</button>
    </div>

    <div class="generator-formula" data-generator-view="capture">
        <span class="generator-formula-icon" aria-hidden="true">i</span>
        <strong>Cantidad neta = Largo x Alto x Piezas - Vacios</strong>
    </div>

    <div class="generator-capture" data-generator-view="capture">
        @foreach ($generatorCategories as $category)
            @php
                $categoryTotal = collect($category['rows'])->sum(function (array $row): float {
                    return max(0, ((float) $row['length'] * (float) $row['height'] * (float) $row['pieces']) - (float) $row['voids']);
                });
            @endphp
            <section class="generator-category" data-generator-category="{{ $category['key'] }}">
                <button
                    class="generator-category-heading"
                    type="button"
                    data-generator-category-toggle
                    aria-expanded="{{ $category['expanded'] ? 'true' : 'false' }}"
                >
                    <span class="generator-category-toggle" aria-hidden="true">{{ $category['expanded'] ? '-' : '+' }}</span>
                    <strong>{{ $category['name'] }}</strong>
                    <span data-generator-category-total>{{ number_format($categoryTotal, 2) }} m2</span>
                </button>

                <div class="generator-category-details" data-generator-category-details @if (! $category['expanded']) hidden @endif>
                    <form data-generator-grid onsubmit="return false;">
                        <div class="table-scroll generator-table-scroll">
                            <table class="generator-table">
                                <thead>
                                    <tr>
                                        <th>Subcategoria / concepto</th>
                                        <th>Zona / eje</th>
                                        <th>Largo (m)</th>
                                        <th>Alto (m)</th>
                                        <th>Piezas</th>
                                        <th>Vacios (m2)</th>
                                        <th>Cantidad neta</th>
                                        <th>Unidad</th>
                                        <th>Plano / evidencia</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody data-generator-rows>
                                    @foreach ($category['rows'] as $row)
                                        @php
                                            $netQuantity = max(0, ((float) $row['length'] * (float) $row['height'] * (float) $row['pieces']) - (float) $row['voids']);
                                        @endphp
                                        <tr data-generator-row>
                                            <td><input type="text" value="{{ $row['concept'] }}" data-generator-concept aria-label="Concepto" readonly></td>
                                            <td><input type="text" value="{{ $row['zone'] }}" data-generator-zone aria-label="Zona o eje" readonly></td>
                                            <td><input type="number" min="0" step="0.01" value="{{ number_format((float) $row['length'], 2, '.', '') }}" data-generator-length aria-label="Largo" readonly></td>
                                            <td><input type="number" min="0" step="0.01" value="{{ number_format((float) $row['height'], 2, '.', '') }}" data-generator-height aria-label="Alto" readonly></td>
                                            <td><input type="number" min="0" step="1" value="{{ $row['pieces'] }}" data-generator-pieces aria-label="Piezas" readonly></td>
                                            <td><input type="number" min="0" step="0.01" value="{{ number_format((float) $row['voids'], 2, '.', '') }}" data-generator-voids aria-label="Vacios" readonly></td>
                                            <td><input class="generator-net-input" type="text" value="{{ number_format($netQuantity, 2, '.', '') }}" data-generator-net aria-label="Cantidad neta" readonly></td>
                                            <td>
                                                <select data-generator-unit aria-label="Unidad" disabled>
                                                    @foreach (['m2', 'm3', 'ml', 'pza'] as $unit)
                                                        <option value="{{ $unit }}" @selected($row['unit'] === $unit)>{{ $unit }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" value="{{ $row['evidence'] }}" data-generator-evidence aria-label="Plano o evidencia" readonly></td>
                                            <td>
                                                <div class="generator-row-actions">
                                                    <button class="button ghost small" type="button" data-generator-edit-row aria-pressed="false">Editar</button>
                                                    <button class="button danger small" type="button" data-generator-delete-row>Eliminar</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="generator-empty-state" data-generator-empty @if (count($category['rows'])) hidden @endif>
                            No hay conceptos capturados en esta partida.
                        </div>

                        <div class="generator-category-footer">
                            <button class="button ghost small" type="button" data-generator-add-row>Agregar concepto</button>
                            <strong>Subtotal {{ $category['name'] }}: <span data-generator-subtotal>{{ number_format($categoryTotal, 2) }} m2</span></strong>
                        </div>
                    </form>
                </div>
            </section>
        @endforeach
    </div>

    <div class="generator-history" data-generator-view="history" hidden>
        @foreach ($generatorPanel['history'] ?? [] as $historyItem)
            <article class="generator-history-item">
                <div>
                    <strong>{{ $historyItem['action'] }}</strong>
                    <span>{{ $historyItem['detail'] }}</span>
                </div>
                <time>{{ $historyItem['date'] }}</time>
            </article>
        @endforeach
    </div>

    <template data-generator-row-template>
        <tr data-generator-row class="is-editing">
            <td><input type="text" value="Nuevo concepto" data-generator-concept aria-label="Concepto"></td>
            <td><input type="text" value="" data-generator-zone aria-label="Zona o eje"></td>
            <td><input type="number" min="0" step="0.01" value="0.00" data-generator-length aria-label="Largo"></td>
            <td><input type="number" min="0" step="0.01" value="0.00" data-generator-height aria-label="Alto"></td>
            <td><input type="number" min="0" step="1" value="1" data-generator-pieces aria-label="Piezas"></td>
            <td><input type="number" min="0" step="0.01" value="0.00" data-generator-voids aria-label="Vacios"></td>
            <td><input class="generator-net-input" type="text" value="0.00" data-generator-net aria-label="Cantidad neta" readonly></td>
            <td>
                <select data-generator-unit aria-label="Unidad">
                    <option value="m2">m2</option>
                    <option value="m3">m3</option>
                    <option value="ml">ml</option>
                    <option value="pza">pza</option>
                </select>
            </td>
            <td><input type="text" value="" data-generator-evidence aria-label="Plano o evidencia"></td>
            <td>
                <div class="generator-row-actions">
                    <button class="button primary small" type="button" data-generator-edit-row aria-pressed="true">Listo</button>
                    <button class="button danger small" type="button" data-generator-delete-row>Eliminar</button>
                </div>
            </td>
        </tr>
    </template>
</section>

<script>
    (() => {
        const panel = document.querySelector('[data-generator-panel]');
        const levelsPanel = document.querySelector('[data-generator-levels]');
        if (!panel || !levelsPanel) return;

        const numberFormatter = new Intl.NumberFormat('es-MX', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
        const normalizeNumber = (value) => Number.parseFloat(value) || 0;
        const formatArea = (value) => `${numberFormatter.format(value)} m2`;
        const activeLevel = () => levelsPanel.querySelector('[data-generator-level][aria-pressed="true"]');

        const setStatus = (message) => {
            const status = panel.querySelector('[data-generator-status]');
            if (!status) return;
            status.textContent = message;
            window.clearTimeout(status.generatorTimeout);
            status.generatorTimeout = window.setTimeout(() => status.textContent = '', 2600);
        };

        const updateLevelSummary = (total) => {
            const level = activeLevel();
            if (!level) return;
            level.dataset.generatorLevelValue = total.toFixed(2);
            const area = level.querySelector('[data-generator-level-area]');
            if (area) area.textContent = formatArea(total);
        };

        const updateOverallTotal = () => {
            const total = Array.from(panel.querySelectorAll('[data-generator-net]'))
                .reduce((sum, input) => sum + normalizeNumber(input.value), 0);
            updateLevelSummary(total);
        };

        const updateCategory = (category) => {
            const rows = Array.from(category.querySelectorAll('[data-generator-row]'));
            const total = rows.reduce((sum, row) => sum + normalizeNumber(row.querySelector('[data-generator-net]')?.value), 0);
            category.querySelectorAll('[data-generator-category-total], [data-generator-subtotal]').forEach((element) => {
                element.textContent = formatArea(total);
            });
            const empty = category.querySelector('[data-generator-empty]');
            if (empty) empty.hidden = rows.length > 0;
            updateOverallTotal();
        };

        const recalculateRow = (row) => {
            const length = normalizeNumber(row.querySelector('[data-generator-length]')?.value);
            const height = normalizeNumber(row.querySelector('[data-generator-height]')?.value);
            const pieces = normalizeNumber(row.querySelector('[data-generator-pieces]')?.value);
            const voids = normalizeNumber(row.querySelector('[data-generator-voids]')?.value);
            const net = Math.max(0, (length * height * pieces) - voids);
            const output = row.querySelector('[data-generator-net]');
            if (output) output.value = net.toFixed(2);
            const category = row.closest('[data-generator-category]');
            if (category) updateCategory(category);
        };

        const setRowEditing = (row, editing) => {
            row.classList.toggle('is-editing', editing);
            row.querySelectorAll('input:not([data-generator-net])').forEach((input) => {
                input.readOnly = !editing;
            });
            row.querySelectorAll('select').forEach((select) => {
                select.disabled = !editing;
            });

            const editButton = row.querySelector('[data-generator-edit-row]');
            if (!editButton) return;
            editButton.textContent = editing ? 'Listo' : 'Editar';
            editButton.setAttribute('aria-pressed', String(editing));
            editButton.classList.toggle('primary', editing);
            editButton.classList.toggle('ghost', !editing);
        };

        const bindRow = (row) => {
            setRowEditing(row, row.classList.contains('is-editing'));

            row.querySelectorAll('[data-generator-length], [data-generator-height], [data-generator-pieces], [data-generator-voids]')
                .forEach((input) => input.addEventListener('input', () => recalculateRow(row)));

            row.querySelector('[data-generator-edit-row]')?.addEventListener('click', () => {
                const editing = !row.classList.contains('is-editing');
                panel.querySelectorAll('[data-generator-row].is-editing').forEach((activeRow) => {
                    if (activeRow !== row) setRowEditing(activeRow, false);
                });
                setRowEditing(row, editing);
                if (editing) row.querySelector('[data-generator-concept]')?.focus();
            });

            row.querySelector('[data-generator-delete-row]')?.addEventListener('click', () => {
                if (!window.confirm('Eliminar este concepto de la cuantificacion?')) return;
                const category = row.closest('[data-generator-category]');
                row.remove();
                if (category) updateCategory(category);
                setStatus('Concepto eliminado.');
            });
        };

        panel.querySelectorAll('[data-generator-row]').forEach(bindRow);

        panel.querySelectorAll('[data-generator-add-row]').forEach((button) => {
            button.addEventListener('click', () => {
                const category = button.closest('[data-generator-category]');
                const template = panel.querySelector('[data-generator-row-template]');
                const row = template?.content.firstElementChild?.cloneNode(true);
                if (!category || !row) return;
                category.querySelector('[data-generator-rows]')?.append(row);
                bindRow(row);
                updateCategory(category);
                row.querySelector('[data-generator-concept]')?.focus();
            });
        });

        panel.querySelectorAll('[data-generator-category-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const details = button.closest('[data-generator-category]')?.querySelector('[data-generator-category-details]');
                if (!details) return;
                details.hidden = !details.hidden;
                button.setAttribute('aria-expanded', String(!details.hidden));
                const icon = button.querySelector('.generator-category-toggle');
                if (icon) icon.textContent = details.hidden ? '+' : '-';
            });
        });

        panel.querySelectorAll('[data-generator-tab]').forEach((tab) => {
            tab.addEventListener('click', () => {
                const view = tab.dataset.generatorTab;
                panel.querySelectorAll('[data-generator-tab]').forEach((item) => {
                    const selected = item === tab;
                    item.classList.toggle('is-active', selected);
                    item.setAttribute('aria-selected', String(selected));
                });
                panel.querySelectorAll('[data-generator-view]').forEach((content) => {
                    content.hidden = content.dataset.generatorView !== view;
                });
            });
        });

        const levelTrack = levelsPanel.querySelector('[data-generator-level-track]');
        const scrollLevels = (direction) => levelTrack?.scrollBy({ left: direction * 360, behavior: 'smooth' });
        levelsPanel.querySelector('[data-generator-level-prev]')?.addEventListener('click', () => scrollLevels(-1));
        levelsPanel.querySelector('[data-generator-level-next]')?.addEventListener('click', () => scrollLevels(1));

        levelsPanel.querySelectorAll('[data-generator-level]').forEach((level) => {
            level.addEventListener('click', () => {
                levelsPanel.querySelectorAll('[data-generator-level]').forEach((item) => {
                    const selected = item === level;
                    item.classList.toggle('is-active', selected);
                    item.setAttribute('aria-pressed', String(selected));
                    const check = item.querySelector('.generator-level-check');
                    if (check) check.textContent = selected ? 'OK' : '';
                });
                panel.querySelector('[data-generator-level-name]').textContent = level.dataset.generatorLevelName;
            });
        });

        document.querySelectorAll('[data-project-select]').forEach((project) => {
            project.addEventListener('click', () => {
                const projectName = project.querySelector('.construction-project-name')?.textContent?.trim();
                if (projectName) panel.querySelector('[data-generator-project-name]').textContent = projectName;
            });
        });

        panel.querySelector('[data-generator-download]')?.addEventListener('click', () => {
            const escapeHtml = (value) => String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
            const headers = ['Partida', 'Concepto', 'Zona / eje', 'Largo', 'Alto', 'Piezas', 'Vacios', 'Cantidad neta', 'Unidad', 'Plano / evidencia'];
            const rows = [];

            panel.querySelectorAll('[data-generator-category]').forEach((category) => {
                const categoryName = category.querySelector('.generator-category-heading strong')?.textContent?.trim() || '';
                category.querySelectorAll('[data-generator-row]').forEach((row) => {
                    rows.push([
                        categoryName,
                        row.querySelector('[data-generator-concept]')?.value,
                        row.querySelector('[data-generator-zone]')?.value,
                        row.querySelector('[data-generator-length]')?.value,
                        row.querySelector('[data-generator-height]')?.value,
                        row.querySelector('[data-generator-pieces]')?.value,
                        row.querySelector('[data-generator-voids]')?.value,
                        row.querySelector('[data-generator-net]')?.value,
                        row.querySelector('[data-generator-unit]')?.value,
                        row.querySelector('[data-generator-evidence]')?.value,
                    ]);
                });
            });

            const projectName = panel.querySelector('[data-generator-project-name]')?.textContent?.trim() || 'obra';
            const levelName = panel.querySelector('[data-generator-level-name]')?.textContent?.trim() || 'nivel';
            const tableRows = rows.map((row) => `<tr>${row.map((cell) => `<td>${escapeHtml(cell)}</td>`).join('')}</tr>`).join('');
            const html = `<html><head><meta charset="UTF-8"></head><body><h2>${escapeHtml(projectName)} - ${escapeHtml(levelName)}</h2><table border="1"><thead><tr>${headers.map((header) => `<th>${escapeHtml(header)}</th>`).join('')}</tr></thead><tbody>${tableRows}</tbody></table></body></html>`;
            const blob = new Blob(['\ufeff', html], { type: 'application/vnd.ms-excel;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `generadores-${projectName.toLowerCase().replace(/[^a-z0-9]+/g, '-')}.xls`;
            document.body.append(link);
            link.click();
            URL.revokeObjectURL(link.href);
            link.remove();
            setStatus('Archivo generado correctamente.');
        });

        panel.querySelectorAll('[data-generator-category]').forEach(updateCategory);
    })();
</script>
