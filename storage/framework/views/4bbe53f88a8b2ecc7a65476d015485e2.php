<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Ordenes de compra vigentes']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Ordenes de compra vigentes']); ?>
        <section class="panel finance-active-panel" data-section-export-ready="true">
            <div class="panel-header">
                <div class="finance-active-title-row">
                    <h2>OC Vigentes</h2>
                    <span class="finance-active-total">Pendiente por pagar: $<?php echo e(number_format((float) $orders->sum('total'), 2)); ?></span>
                </div>
                <div class="toolbar finance-active-toolbar">
                    <a class="button ghost finance-active-export" href="<?php echo e(route('reports.download', 'finance-active-excel')); ?>">Exportar Excel</a>
                </div>
            </div>

            <div class="table-scroll">
                <table data-excel-filter-table>
                    <thead>
                                                <tr>
                            <th data-excel-filter-column="0"><span>OC</span></th>
                            <th data-excel-filter-column="1" data-excel-filter-type="date-range"><span>Fecha envio</span></th>
                            <th data-excel-filter-column="2"><span>Comprador</span></th>
                            <th data-excel-filter-column="3"><span>Empresa</span></th>
                            <th data-excel-filter-column="4"><span>Proveedor</span></th>
                            <th data-excel-filter-column="5"><span>Cotizacion</span></th>
                            <th data-excel-filter-column="6"><span>Monto</span></th>
                            <th data-excel-filter-column="7"><span>Estado</span></th>
                            <th data-excel-filter-column="8"><span>Credito</span></th>
                            <th data-excel-filter-column="9"><span>Dias de credito</span></th>
                            <th data-excel-filter-column="10" data-excel-filter-type="date-range"><span>Vence</span></th>
                            <th data-excel-filter-column="11"><span>Almacen receptor</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $financeStatusLabel = \App\Support\UiStatus::purchaseOrder($order->status, 'finance');
                                $dueDate = $order->due_date?->copy()->startOfDay();
                                $currentWeekStart = now()->startOfWeek()->startOfDay();
                                $currentWeekEnd = now()->endOfWeek()->endOfDay();
                                $nextWeekEnd = now()->addWeek()->endOfWeek()->endOfDay();
                                $dueDateClass = '';

                                if ($dueDate && $dueDate->lte($currentWeekEnd)) {
                                    $dueDateClass = 'due-date-danger';
                                } elseif ($dueDate && $dueDate->lte($nextWeekEnd)) {
                                    $dueDateClass = 'due-date-warning';
                                }
                            ?>
                            <tr data-excel-filter-row>
                                <td data-excel-filter-value="<?php echo e($order->folio); ?>">
                                    <strong>
                                        <a href="<?php echo e(route('finance.orders.print', $order)); ?>" target="_blank"><?php echo e($order->folio); ?></a>
                                    </strong>
                                </td>
                                                                <td data-excel-filter-value="<?php echo e(($order->created_on ?? $order->created_at)?->format('d/m/Y') ?? 'Sin fecha'); ?>" data-excel-filter-date="<?php echo e(($order->created_on ?? $order->created_at)?->format('Y-m-d')); ?>">
                                    <?php echo e(($order->created_on ?? $order->created_at)?->format('d/m/Y') ?? 'Sin fecha'); ?>

                                </td>
                                <td data-excel-filter-value="<?php echo e($order->buyer->name); ?>"><?php echo e($order->buyer->name); ?></td>
                                <td data-excel-filter-value="<?php echo e($order->company->name); ?>"><?php echo e($order->company->name); ?></td>
                                <td data-excel-filter-value="<?php echo e($order->provider->business_name); ?>">
                                    <?php echo e($order->provider->business_name); ?>

                                    <small class="fine-print"><?php echo e($order->provider->business_line); ?></small>
                                </td>
                                <td data-excel-filter-value="<?php echo e($order->quote_original_name ?: 'Sin cotizacion'); ?>">
                                    <?php if($order->quote_file_path): ?>
                                        <a class="attachment-pill" href="<?php echo e(route('finance.orders.quote', $order)); ?>" target="_blank"><span>Soporte</span><?php echo e($order->quote_original_name); ?></a>
                                    <?php else: ?>
                                        Sin cotizacion
                                    <?php endif; ?>
                                </td>
                                <td data-excel-filter-value="$<?php echo e(number_format((float) $order->total, 2)); ?>">$<?php echo e(number_format((float) $order->total, 2)); ?></td>
                                <td data-excel-filter-value="<?php echo e($financeStatusLabel); ?>">
                                    <details class="status-menu">
                                        <summary class="status <?php echo e(\App\Support\UiStatus::purchaseOrderClass($order->status, 'finance')); ?>"><?php echo e($financeStatusLabel); ?></summary>
                                        <div class="status-menu-panel">
                                            <?php if($order->status === 'sent'): ?>
                                                <form class="inline-form" method="POST" action="<?php echo e(route('finance.orders.approve', $order)); ?>">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PATCH'); ?>
                                                    <button class="button primary small" type="submit">Aprobar</button>
                                                </form>
                                                <form class="inline-form" method="POST" action="<?php echo e(route('finance.orders.reject', $order)); ?>" onsubmit="return confirm('Rechazar <?php echo e($order->folio); ?>?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PATCH'); ?>
                                                    <input type="hidden" name="reason" value="No cumple criterios de autorizacion">
                                                    <button class="button danger small" type="submit">Rechazar</button>
                                                </form>
                                            <?php elseif($order->status === 'approved'): ?>
                                                <a class="button primary small" href="<?php echo e(route('finance.orders.payment', $order)); ?>">Pagar</a>
                                                <form class="inline-form" method="POST" action="<?php echo e(route('finance.orders.reject', $order)); ?>" onsubmit="return confirm('Rechazar <?php echo e($order->folio); ?>?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PATCH'); ?>
                                                    <input type="hidden" name="reason" value="No cumple criterios de autorizacion">
                                                    <button class="button danger small" type="submit">Rechazar</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="fine-print">Sin acciones</span>
                                            <?php endif; ?>
                                        </div>
                                    </details>
                                </td>
                                <td data-excel-filter-value="<?php echo e($order->is_credit ? 'Credito' : 'Contado'); ?>">
                                    <input type="checkbox" disabled <?php if($order->is_credit): echo 'checked'; endif; ?>>
                                </td>
                                <td data-excel-filter-value="<?php echo e($order->is_credit ? $order->credit_days : 'Sin credito'); ?>">
                                    <?php echo e($order->is_credit ? $order->credit_days : '-'); ?>

                                </td>
                                <td class="<?php echo e($dueDateClass); ?>" data-excel-filter-value="<?php echo e($order->due_date->format('d/m/Y')); ?>" data-excel-filter-date="<?php echo e($order->due_date->format('Y-m-d')); ?>"><span class="due-date-pill"><?php echo e($order->due_date->format('d/m/Y')); ?></span></td>
                                <td data-excel-filter-value="<?php echo e($order->warehouse ?: 'Sin almacen asignado'); ?>"><?php echo e($order->warehouse ?: 'Sin almacen asignado'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="12">No hay ordenes vigentes.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9144295cee351e372dbe9bffc4f13bc5)): ?>
<?php $attributes = $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5; ?>
<?php unset($__attributesOriginal9144295cee351e372dbe9bffc4f13bc5); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9144295cee351e372dbe9bffc4f13bc5)): ?>
<?php $component = $__componentOriginal9144295cee351e372dbe9bffc4f13bc5; ?>
<?php unset($__componentOriginal9144295cee351e372dbe9bffc4f13bc5); ?>
<?php endif; ?>


    <script>
        document.body.dataset.generalExportReady = 'true';
    </script>
    <style>
        
        .finance-active-title-row {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
        }

        .finance-active-total {
            border: 1px solid #bdebd2;
            border-radius: 999px;
            background: #effbf5;
            color: #11613b;
            font-size: .92rem;
            font-weight: 900;
            padding: 8px 12px;
            white-space: nowrap;
        }

.finance-active-toolbar {
            align-items: center;
            flex: 1 1 520px;
            justify-content: flex-start;
            margin-left: auto;
        }
.finance-active-export {
            margin-left: auto;
        }


        .finance-active-panel .due-date-pill {
            border-radius: 8px;
            display: inline-flex;
            font-weight: 850;
            justify-content: center;
            min-width: 98px;
            padding: 7px 10px;
        }

        .finance-active-panel td.due-date-danger .due-date-pill {
            background: #fde8e8;
            border: 1px solid #d73b32;
            color: #a8201a;
        }

        .finance-active-panel td.due-date-warning .due-date-pill {
            background: #fff6d8;
            border: 1px solid #c79712;
            color: #8a6400;
        }
        .date-range-calendar {
            display: grid;
            gap: 12px;
            min-width: 0;
            width: 100%;
        }

        .excel-filter-panel.date-range-filter-panel {
            width: 360px;
            max-height: none;
            overflow: visible;
        }

        .date-range-calendar-header {
            align-items: center;
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }

        .date-range-calendar-title {
            color: var(--ink);
            font-size: 1rem;
            font-weight: 900;
            text-align: center;
        }

        .date-range-nav {
            align-items: center;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 10px;
            color: var(--ink);
            cursor: pointer;
            display: inline-flex;
            font-size: 1.1rem;
            font-weight: 900;
            height: 38px;
            justify-content: center;
            width: 38px;
        }

        .date-range-weekdays,
        .date-range-days {
            display: grid;
            grid-template-columns: repeat(7, minmax(32px, 1fr));
            text-align: center;
        }

        .date-range-weekdays span {
            color: var(--muted);
            font-size: .78rem;
            font-weight: 900;
            padding: 6px 0;
        }

        .date-range-day {
            background: transparent;
            border: 0;
            border-radius: 0;
            color: var(--ink);
            cursor: pointer;
            font-weight: 800;
            min-height: 38px;
            padding: 0;
        }

        .date-range-day.is-muted {
            color: #9aa6b2;
        }

        .date-range-day.is-in-range {
            background: #eaf3ff;
        }

        .date-range-day.is-selected {
            background: #1479ff;
            border-radius: 9px;
            color: #fff;
        }

        .date-range-summary {
            color: var(--muted);
            font-size: .8rem;
            font-weight: 800;
        }

        .overdue-filter-button {
            justify-content: center;
            width: 100%;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const normalize = (value) => (value || '').replace(/\s+/g, ' ').trim();
            const parseDateKey = (key) => {
                const [year, month, day] = (key || '').split('-').map(Number);
                return new Date(year, month - 1, day);
            };
            const toDateKey = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };
            const formatHumanDate = (key) => {
                if (!key) return '';
                const [year, month, day] = key.split('-');
                return `${day}/${month}/${year}`;
            };
            const monthTitle = (date) => {
                const label = new Intl.DateTimeFormat('es-MX', { month: 'long', year: 'numeric' }).format(date);
                return label.charAt(0).toUpperCase() + label.slice(1);
            };
            const positionPanel = (panel, toggle) => {
                const rect = toggle.getBoundingClientRect();
                const gap = 8;
                const margin = 12;
                const panelWidth = panel.offsetWidth || 280;
                let left = rect.right - panelWidth;
                let top = rect.bottom + gap;

                left = Math.max(margin, Math.min(left, window.innerWidth - panelWidth - margin));

                if (top + panel.offsetHeight > window.innerHeight - margin) {
                    top = Math.max(margin, rect.top - panel.offsetHeight - gap);
                }

                panel.style.left = `${left}px`;
                panel.style.top = `${top}px`;
            };
            const closePanels = (except = null) => {
                document.querySelectorAll('.excel-filter-panel.open').forEach((panel) => {
                    if (panel !== except) {
                        panel.classList.remove('open');
                        panel.closest('.excel-filter')?.querySelector('.excel-filter-toggle')?.classList.remove('active');
                    }
                });
            };

            document.addEventListener('click', (event) => {
                if (!event.target.closest('.excel-filter')) {
                    closePanels();
                }
            });
            window.addEventListener('resize', () => closePanels());
            window.addEventListener('scroll', () => closePanels(), true);

            document.querySelectorAll('[data-excel-filter-table]').forEach((table) => {
                const rows = Array.from(table.querySelectorAll('tbody tr[data-excel-filter-row]'));
                const headers = Array.from(table.querySelectorAll('thead th[data-excel-filter-column]'));
                const filters = new Map();

                const valueFor = (row, column) => {
                    const cell = row.cells[column];
                    return normalize(cell?.dataset.excelFilterValue || cell?.textContent);
                };
                const dateFor = (row, column) => {
                    const cell = row.cells[column];
                    return normalize(cell?.dataset.excelFilterDate || '');
                };

                const applyFilters = () => {
                    rows.forEach((row) => {
                        const visible = headers.every((header) => {
                            const column = Number(header.dataset.excelFilterColumn);
                            const selected = filters.get(column);

                            if (!selected) {
                                return true;
                            }

                            if (selected.type === 'date-range') {
                                const value = dateFor(row, column);

                                if (!value) {
                                    return false;
                                }

                                return (!selected.from || value >= selected.from)
                                    && (!selected.to || value <= selected.to);
                            }

                            return selected.has(valueFor(row, column));
                        });

                        row.hidden = !visible;
                    });
                };

                const sortRows = (column, direction) => {
                    const sorted = rows.sort((a, b) => {
                        const header = headers.find((item) => Number(item.dataset.excelFilterColumn) === column);
                        const isDateRange = header?.dataset.excelFilterType === 'date-range';
                        const left = isDateRange ? dateFor(a, column) : valueFor(a, column);
                        const right = isDateRange ? dateFor(b, column) : valueFor(b, column);
                        const numericLeft = Number(left.replace(/[$,]/g, ''));
                        const numericRight = Number(right.replace(/[$,]/g, ''));

                        if (!Number.isNaN(numericLeft) && !Number.isNaN(numericRight)) {
                            return direction === 'asc' ? numericLeft - numericRight : numericRight - numericLeft;
                        }

                        return direction === 'asc'
                            ? left.localeCompare(right, 'es', { numeric: true })
                            : right.localeCompare(left, 'es', { numeric: true });
                    });

                    sorted.forEach((row) => table.tBodies[0].appendChild(row));
                };

                headers.forEach((header) => {
                    const column = Number(header.dataset.excelFilterColumn);
                    const title = normalize(header.textContent);
                    const filterType = header.dataset.excelFilterType || 'values';
                    const values = [...new Set(rows.map((row) => valueFor(row, column)).filter(Boolean))]
                        .sort((a, b) => a.localeCompare(b, 'es', { numeric: true }));

                    const head = document.createElement('div');
                    head.className = 'excel-filter-head';

                    const label = document.createElement('span');
                    label.textContent = title;

                    const filter = document.createElement('div');
                    filter.className = 'excel-filter';

                    const toggle = document.createElement('button');
                    toggle.type = 'button';
                    toggle.className = 'excel-filter-toggle';
                    toggle.textContent = 'v';
                    toggle.title = `Filtrar ${title}`;

                    const panel = document.createElement('div');
                    panel.className = 'excel-filter-panel';

                    const sortBox = document.createElement('div');
                    sortBox.className = 'excel-filter-sort';

                    const sortAsc = document.createElement('button');
                    sortAsc.type = 'button';
                    sortAsc.textContent = 'A-Z';
                    sortAsc.addEventListener('click', () => sortRows(column, 'asc'));

                    const sortDesc = document.createElement('button');
                    sortDesc.type = 'button';
                    sortDesc.textContent = 'Z-A';
                    sortDesc.addEventListener('click', () => sortRows(column, 'desc'));

                    if (filterType === 'date-range') {
                        panel.classList.add('date-range-filter-panel');
                        sortAsc.textContent = 'Mas antiguas primero';
                        sortDesc.textContent = 'Mas recientes primero';

                        let draftFrom = '';
                        let draftTo = '';
                        const dateValues = rows.map((row) => dateFor(row, column)).filter(Boolean).sort();
                        let monthCursor = dateValues[0] ? parseDateKey(dateValues[0]) : new Date();
                        const todayKey = toDateKey(new Date());

                        const calendar = document.createElement('div');
                        calendar.className = 'date-range-calendar';

                        const calendarHeader = document.createElement('div');
                        calendarHeader.className = 'date-range-calendar-header';

                        const previous = document.createElement('button');
                        previous.className = 'date-range-nav';
                        previous.type = 'button';
                        previous.textContent = '<';

                        const title = document.createElement('div');
                        title.className = 'date-range-calendar-title';

                        const next = document.createElement('button');
                        next.className = 'date-range-nav';
                        next.type = 'button';
                        next.textContent = '>';

                        calendarHeader.append(previous, title, next);

                        const weekdays = document.createElement('div');
                        weekdays.className = 'date-range-weekdays';
                        ['L', 'M', 'M', 'J', 'V', 'S', 'D'].forEach((day) => {
                            const weekday = document.createElement('span');
                            weekday.textContent = day;
                            weekdays.append(weekday);
                        });

                        const days = document.createElement('div');
                        days.className = 'date-range-days';

                        const summaryRange = document.createElement('div');
                        summaryRange.className = 'date-range-summary';

                        const renderCalendar = () => {
                            title.textContent = monthTitle(monthCursor);
                            days.textContent = '';

                            const firstOfMonth = new Date(monthCursor.getFullYear(), monthCursor.getMonth(), 1);
                            const mondayIndex = (firstOfMonth.getDay() + 6) % 7;
                            const gridStart = new Date(firstOfMonth);
                            gridStart.setDate(firstOfMonth.getDate() - mondayIndex);

                            for (let index = 0; index < 42; index += 1) {
                                const date = new Date(gridStart);
                                date.setDate(gridStart.getDate() + index);
                                const key = toDateKey(date);
                                const day = document.createElement('button');
                                day.className = 'date-range-day';
                                day.type = 'button';
                                day.textContent = date.getDate();

                                if (date.getMonth() !== monthCursor.getMonth()) {
                                    day.classList.add('is-muted');
                                }

                                if (draftFrom && draftTo && key > draftFrom && key < draftTo) {
                                    day.classList.add('is-in-range');
                                }

                                if (key === draftFrom || key === draftTo) {
                                    day.classList.add('is-selected');
                                }

                                day.addEventListener('click', () => {
                                    if (!draftFrom || (draftFrom && draftTo)) {
                                        draftFrom = key;
                                        draftTo = '';
                                    } else if (key < draftFrom) {
                                        draftTo = draftFrom;
                                        draftFrom = key;
                                    } else {
                                        draftTo = key;
                                    }

                                    renderCalendar();
                                });

                                days.append(day);
                            }

                            summaryRange.textContent = draftFrom
                                ? `Rango: ${formatHumanDate(draftFrom)}${draftTo ? ` - ${formatHumanDate(draftTo)}` : ''}`
                                : 'Selecciona fecha inicial y final.';
                        };

                        previous.addEventListener('click', () => {
                            monthCursor = new Date(monthCursor.getFullYear(), monthCursor.getMonth() - 1, 1);
                            renderCalendar();
                        });

                        next.addEventListener('click', () => {
                            monthCursor = new Date(monthCursor.getFullYear(), monthCursor.getMonth() + 1, 1);
                            renderCalendar();
                        });

                        const overdue = document.createElement('button');
                        overdue.type = 'button';
                        overdue.className = 'button ghost small overdue-filter-button';
                        overdue.textContent = 'Vencidas';
                        overdue.addEventListener('click', () => {
                            draftFrom = '';
                            draftTo = todayKey;
                            filters.set(column, { type: 'date-range', from: '', to: todayKey });
                            toggle.classList.add('active');
                            monthCursor = parseDateKey(todayKey);
                            renderCalendar();
                            applyFilters();
                            panel.classList.remove('open');
                        });

                        const actions = document.createElement('div');
                        actions.className = 'excel-filter-actions';

                        const accept = document.createElement('button');
                        accept.type = 'button';
                        accept.className = 'button primary small';
                        accept.textContent = 'Aceptar';
                        accept.addEventListener('click', () => {
                            if (!draftFrom && !draftTo) {
                                filters.delete(column);
                                toggle.classList.remove('active');
                            } else {
                                filters.set(column, { type: 'date-range', from: draftFrom, to: draftTo || draftFrom });
                                toggle.classList.add('active');
                            }

                            applyFilters();
                            panel.classList.remove('open');
                        });

                        const clear = document.createElement('button');
                        clear.type = 'button';
                        clear.className = 'button ghost small';
                        clear.textContent = 'Limpiar';
                        clear.addEventListener('click', () => {
                            draftFrom = '';
                            draftTo = '';
                            filters.delete(column);
                            toggle.classList.remove('active');
                            applyFilters();
                            renderCalendar();
                            panel.classList.remove('open');
                        });

                        const cancel = document.createElement('button');
                        cancel.type = 'button';
                        cancel.className = 'button ghost small';
                        cancel.textContent = 'Cancelar';
                        cancel.addEventListener('click', () => {
                            const current = filters.get(column);
                            draftFrom = current?.type === 'date-range' ? current.from : '';
                            draftTo = current?.type === 'date-range' ? current.to : '';
                            panel.classList.remove('open');
                            toggle.classList.toggle('active', filters.has(column));
                        });

                        toggle.addEventListener('click', (event) => {
                            event.stopPropagation();
                            const isOpen = panel.classList.contains('open');
                            closePanels(panel);

                            if (isOpen) {
                                panel.classList.remove('open');
                                toggle.classList.toggle('active', filters.has(column));
                                return;
                            }

                            const current = filters.get(column);
                            draftFrom = current?.type === 'date-range' ? current.from : '';
                            draftTo = current?.type === 'date-range' ? current.to : '';
                            monthCursor = draftFrom ? parseDateKey(draftFrom) : (dateValues[0] ? parseDateKey(dateValues[0]) : new Date());
                            renderCalendar();
                            panel.classList.add('open');
                            positionPanel(panel, toggle);
                            toggle.classList.add('active');
                        });

                        panel.addEventListener('click', (event) => event.stopPropagation());

                        sortBox.append(sortAsc, sortDesc);
                        actions.append(accept, clear, cancel);
                        calendar.append(calendarHeader, weekdays, days, summaryRange, overdue, actions);
                        panel.append(sortBox, calendar);
                        filter.append(toggle, panel);
                        head.append(label, filter);
                        header.textContent = '';
                        header.append(head);

                        return;
                    }

                    const search = document.createElement('input');
                    search.className = 'excel-filter-search';
                    search.placeholder = 'Buscar';

                    const options = document.createElement('div');
                    options.className = 'excel-filter-options';

                    const allOption = document.createElement('label');
                    allOption.className = 'excel-filter-option';
                    const allCheckbox = document.createElement('input');
                    allCheckbox.type = 'checkbox';
                    allCheckbox.checked = true;
                    const allText = document.createElement('span');
                    allText.textContent = '(Seleccionar todo)';
                    allOption.append(allCheckbox, allText);
                    options.append(allOption);

                    const checkboxes = values.map((value) => {
                        const option = document.createElement('label');
                        option.className = 'excel-filter-option';
                        option.dataset.optionText = value.toLowerCase();

                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.value = value;
                        checkbox.checked = true;

                        const text = document.createElement('span');
                        text.textContent = value;

                        option.append(checkbox, text);
                        options.append(option);

                        checkbox.addEventListener('change', () => {
                            allCheckbox.checked = checkboxes.every((input) => input.checked);
                        });

                        return checkbox;
                    });

                    allCheckbox.addEventListener('change', () => {
                        checkboxes.forEach((checkbox) => checkbox.checked = allCheckbox.checked);
                    });

                    search.addEventListener('input', () => {
                        const term = search.value.toLowerCase().trim();
                        options.querySelectorAll('.excel-filter-option[data-option-text]').forEach((option) => {
                            option.hidden = !option.dataset.optionText.includes(term);
                        });
                    });

                    const actions = document.createElement('div');
                    actions.className = 'excel-filter-actions';

                    const accept = document.createElement('button');
                    accept.type = 'button';
                    accept.className = 'button primary small';
                    accept.textContent = 'Aceptar';
                    accept.addEventListener('click', () => {
                        const selected = checkboxes.filter((checkbox) => checkbox.checked).map((checkbox) => checkbox.value);

                        if (selected.length === checkboxes.length) {
                            filters.delete(column);
                            toggle.classList.remove('active');
                        } else {
                            filters.set(column, new Set(selected));
                            toggle.classList.add('active');
                        }

                        applyFilters();
                        panel.classList.remove('open');
                    });

                    const cancel = document.createElement('button');
                    cancel.type = 'button';
                    cancel.className = 'button ghost small';
                    cancel.textContent = 'Cancelar';
                    cancel.addEventListener('click', () => {
                        const current = filters.get(column);
                        checkboxes.forEach((checkbox) => checkbox.checked = !current || current.has(checkbox.value));
                        allCheckbox.checked = checkboxes.every((checkbox) => checkbox.checked);
                        search.value = '';
                        options.querySelectorAll('.excel-filter-option').forEach((option) => option.hidden = false);
                        panel.classList.remove('open');
                        toggle.classList.toggle('active', filters.has(column));
                    });

                    toggle.addEventListener('click', (event) => {
                        event.stopPropagation();
                        const isOpen = panel.classList.contains('open');
                        closePanels(panel);

                        if (isOpen) {
                            panel.classList.remove('open');
                            toggle.classList.toggle('active', filters.has(column));
                            return;
                        }

                        panel.classList.add('open');
                        positionPanel(panel, toggle);
                        toggle.classList.add('active');
                    });

                    panel.addEventListener('click', (event) => event.stopPropagation());

                    sortBox.append(sortAsc, sortDesc);
                    actions.append(accept, cancel);
                    panel.append(sortBox, search, options, actions);
                    filter.append(toggle, panel);
                    head.append(label, filter);
                    header.textContent = '';
                    header.append(head);
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>








<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\finance\orders\active.blade.php ENDPATH**/ ?>