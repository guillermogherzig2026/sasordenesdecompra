<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Pago de servicios']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Pago de servicios']); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <a class="button ghost" href="<?php echo e(route('reports.download', 'services-months-excel')); ?>">Descargar</a>
         <?php $__env->endSlot(); ?>
        <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <section class="panel service-month-panel" data-month-panel>
                <div class="panel-header service-month-header service-month-header-compact">
                    <button class="month-toggle" type="button" data-month-toggle
                        aria-expanded="<?php echo e($loop->first ? 'true' : 'false'); ?>">
                        <span class="month-toggle-sign" aria-hidden="true"><?php echo e($loop->first ? '-' : '+'); ?></span>
                        <span class="month-toggle-copy">
                            <h2><?php echo e($month['label']); ?> <span class="month-total-inline">Total: $<?php echo e(number_format((float) $month['total'], 0)); ?></span></h2>
                            <span class="fine-print">Servicios ordenados de la fecha de corte mas proxima a la mas tardia.</span>
                        </span>
                    </button>
                    <div class="month-summary-metrics">
                        <article class="metric-card compact-metric">
                            <span>Monto total mensual</span>
                            <strong>$<?php echo e(number_format((float) $month['total'], 2)); ?></strong>
                        </article>
                        <article class="metric-card compact-metric">
                            <span>Monto total pagado</span>
                            <strong>$<?php echo e(number_format((float) $month['paid_total'], 2)); ?></strong>
                        </article>
                        <article class="metric-card compact-metric">
                            <span>Monto pendiente por pagar</span>
                            <strong>$<?php echo e(number_format((float) $month['pending_total'], 2)); ?></strong>
                        </article>
                    </div>
                </div>

                <div class="service-month-detail" <?php if(!$loop->first): ?> hidden <?php endif; ?>>
                    <div class="table-scroll service-month-scroll">
                        <table class="service-month-table" data-column-filter-table>
                            <thead>
                                <tr>
                                    <th data-filter-column="0"><span>ID</span></th>
                                    <th data-filter-column="1"><span>Titular</span></th>
                                    <th data-filter-column="2"><span>Sucursal</span></th>
                                    <th data-filter-column="3"><span>Servicio</span></th>
                                    <th data-filter-column="4"><span>Banco</span></th>
                                    <th data-filter-column="5"><span>Cuenta pagadora</span></th>
                                    <th data-filter-column="6"><span>Proveedor</span></th>
                                    <th data-filter-column="7"><span>No. Servicio</span></th>
                                    <th data-filter-column="8"><span>Periodo</span></th>
                                    <th data-filter-column="9" data-filter-type="date-range"><span>Vencimiento</span></th>
                                    <th data-filter-column="10"><span>Monto</span></th>
                                    <th data-filter-column="11"><span>Referencia</span></th>
                                    <th data-filter-column="12"><span>Estado</span></th>
                                    <th data-filter-column="13"><span>Factura</span></th>
                                    <th data-filter-column="14"><span>Comprobante pago</span></th>
                                    <th data-filter-column="15"><span>Pausar</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $month['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $service = $item['service'];
                                        $receipt = $item['receipt'];
                                        $isDomiciled = $service->is_domiciled;
                                        $paid = filled($receipt?->payment_file_path);
                                        $hasSupport = filled($receipt?->support_file_path);
                                        $paused = $service->status === 'paused';
                                        $statusText = $paused ? 'Pausado' : ($isDomiciled ? 'DOM' : ($paid ? 'Pagado' : ($hasSupport ? 'Listo para pago' : 'Pendiente')));
                                        $statusClass = $paused ? 'rejected' : ($isDomiciled ? 'domiciled' : ($paid ? 'paid' : ($hasSupport ? 'approved' : 'pending')));
                                        $periodAmount = (float) ($receipt?->amount ?? $service->cost);
                                        $paymentDueDate = \Illuminate\Support\Carbon::parse($item['payment_due_date'] ?? $item['due_date']);
                                    ?>
                                    <tr data-filter-row>
                                        <td data-filter-value="<?php echo e($service->folio); ?>"><strong><?php echo e($service->folio); ?></strong></td>
                                        <td data-filter-value="<?php echo e($service->holder ?: $service->company_name); ?>"><?php echo e($service->holder ?: $service->company_name); ?></td>
                                        <td data-filter-value="<?php echo e($service->display_branch); ?>"><?php echo e($service->display_branch); ?></td>
                                        <td data-filter-value="<?php echo e($service->service_name); ?>"><?php echo e($service->service_name); ?></td>
                                        <td data-filter-value="<?php echo e($service->bank); ?>"><?php echo e($service->bank); ?></td>
                                        <td data-filter-value="<?php echo e($service->payer_account); ?>"><?php echo e($service->payer_account); ?></td>
                                        <td data-filter-value="<?php echo e($service->provider); ?>"><?php echo e($service->provider); ?></td>
                                        <td data-filter-value="<?php echo e($service->service_number); ?>"><?php echo e($service->service_number); ?></td>
                                        <td data-filter-value="<?php echo e(\Illuminate\Support\Carbon::parse($item['period_start'])->format('d/m/Y')); ?> al <?php echo e(\Illuminate\Support\Carbon::parse($item['due_date'])->format('d/m/Y')); ?>"><?php echo e(\Illuminate\Support\Carbon::parse($item['period_start'])->format('d/m')); ?> al <?php echo e(\Illuminate\Support\Carbon::parse($item['due_date'])->format('d/m')); ?></td>
                                        <td data-filter-value="<?php echo e($paymentDueDate->format('d/m/Y')); ?>" data-filter-date="<?php echo e($paymentDueDate->format('Y-m-d')); ?>"><?php echo e($paymentDueDate->format('d/m/Y')); ?></td>
                                        <td data-filter-value="$<?php echo e(number_format($periodAmount, 2)); ?>">$<?php echo e(number_format($periodAmount, 2)); ?></td>
                                        <td data-filter-value="<?php echo e($service->reference); ?>"><?php echo e($service->reference); ?></td>
                                        <td data-filter-value="<?php echo e($statusText); ?>">
                                            <details class="status-menu">
                                                <summary class="status <?php echo e($statusClass); ?>"><?php echo e($statusText); ?></summary>
                                                <div class="status-menu-panel">
                                                    <?php if($isDomiciled): ?>
                                                        <span class="fine-print">Pago domiciliado automatico</span>
                                                    <?php elseif($service->status !== 'inactive' && $hasSupport && ! $paid): ?>
                                                        <a class="button primary small" href="<?php echo e(route('finance.services.payment', [$service, $item['due_date']])); ?>">Subir comprobante</a>
                                                    <?php elseif($paid): ?>
                                                        <span class="fine-print">Pagado</span>
                                                    <?php elseif(! $hasSupport): ?>
                                                        <span class="fine-print">Esperando factura</span>
                                                    <?php endif; ?>
                                                </div>
                                            </details>
                                        </td>
                                        <td data-filter-value="<?php echo e($receipt?->support_original_name ?? 'Pendiente'); ?>">
                                            <?php if($receipt?->support_file_path): ?>
                                                <a class="attachment-pill" href="<?php echo e(route('finance.services.support-file', $receipt)); ?>" target="_blank" rel="noopener"><span>Adjunto</span><?php echo e($receipt->support_original_name ?: 'Factura'); ?></a>
                                            <?php else: ?>
                                                Pendiente
                                            <?php endif; ?>
                                        </td>
                                        <td data-filter-value="<?php echo e($isDomiciled ? 'DOM' : ($receipt?->payment_original_name ?? 'Pendiente')); ?>">
                                            <?php if($isDomiciled): ?>
                                                DOM
                                            <?php elseif($receipt?->payment_file_path): ?>
                                                <a class="attachment-pill" href="<?php echo e(route('finance.services.payment-file', $receipt)); ?>" target="_blank" rel="noopener"><span>Adjunto</span><?php echo e($receipt->payment_original_name ?: 'Comprobante'); ?></a>
                                            <?php else: ?>
                                                Pendiente
                                            <?php endif; ?>
                                        </td>
                                        <td data-filter-value="<?php echo e($service->status === 'paused' ? 'Reactivar' : 'Pausar'); ?>">
                                            <div class="item-actions service-actions">
                                                <?php if($service->status === 'paused'): ?>
                                                    <form class="inline-form" method="POST" action="<?php echo e(route('finance.services.status', [$service, 'active'])); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PATCH'); ?>
                                                        <button class="button primary small" type="submit">Reactivar</button>
                                                    </form>
                                                <?php else: ?>
                                                    <form class="inline-form" method="POST" action="<?php echo e(route('finance.services.status', [$service, 'paused'])); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PATCH'); ?>
                                                        <button class="button ghost small" type="submit">Pausar</button>
                                                    </form>
                                                <?php endif; ?>
                                                <form class="inline-form" method="POST" action="<?php echo e(route('finance.services.status', [$service, 'inactive'])); ?>" onsubmit="return confirm('Dar de baja <?php echo e($service->folio); ?>?')">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PATCH'); ?>
                                                    <button class="button danger small" type="submit">Dar de baja</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr><td colspan="16">No hay servicios programados para este mes.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

    <style>
        .date-range-calendar { display: grid; gap: 12px; min-width: 0; width: 100%; }
        .column-filter-panel.date-range-filter-panel { position: fixed; width: 340px; max-height: none; overflow: visible; z-index: 2200; }
        .date-range-calendar-header { align-items: center; display: flex; justify-content: space-between; gap: 12px; }
        .date-range-calendar-title { color: var(--ink); font-size: 1rem; font-weight: 900; text-align: center; }
        .date-range-nav { align-items: center; background: #fff; border: 1px solid var(--line); border-radius: 10px; color: var(--ink); cursor: pointer; display: inline-flex; font-size: 1.1rem; font-weight: 900; height: 38px; justify-content: center; width: 38px; }
        .date-range-weekdays, .date-range-days { display: grid; grid-template-columns: repeat(7, minmax(32px, 1fr)); text-align: center; }
        .date-range-weekdays span { color: var(--muted); font-size: .78rem; font-weight: 900; padding: 6px 0; }
        .date-range-day { background: transparent; border: 0; border-radius: 0; color: var(--ink); cursor: pointer; font-weight: 800; min-height: 38px; padding: 0; }
        .date-range-day.is-muted { color: #9aa6b2; }
        .date-range-day.is-in-range { background: #eaf3ff; }
        .date-range-day.is-selected { background: #1479ff; border-radius: 9px; color: #fff; }
        .date-range-summary { color: var(--muted); font-size: .8rem; font-weight: 800; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-month-toggle]').forEach((toggle) => {
                toggle.addEventListener('click', () => {
                    const panel = toggle.closest('[data-month-panel]');
                    const detail = panel?.querySelector('.service-month-detail');
                    if (!detail) return;

                    const isOpen = toggle.getAttribute('aria-expanded') === 'true';
                    toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                    toggle.querySelector('.month-toggle-sign').textContent = isOpen ? '+' : '-';
                    detail.hidden = isOpen;
                });
            });

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
            const closeColumnFilters = (except = null) => {
                document.querySelectorAll('.column-filter[open]').forEach((filter) => {
                    if (filter !== except) filter.removeAttribute('open');
                });
            };
            const positionFilterPanel = (panel, trigger) => {
                const rect = trigger.getBoundingClientRect();
                const gap = 8;
                const margin = 12;
                const width = panel.offsetWidth || 340;
                const height = panel.offsetHeight || 360;
                let left = rect.right - width;
                let top = rect.bottom + gap;
                left = Math.max(margin, Math.min(left, window.innerWidth - width - margin));
                if (top + height > window.innerHeight - margin) {
                    top = Math.max(margin, rect.top - height - gap);
                }
                panel.style.left = `${left}px`;
                panel.style.top = `${top}px`;
            };

            document.addEventListener('click', (event) => {
                if (!event.target.closest('.column-filter')) closeColumnFilters();
            });
            window.addEventListener('resize', () => closeColumnFilters());

            document.querySelectorAll('[data-column-filter-table]').forEach((table) => {
                const rows = Array.from(table.querySelectorAll('tbody tr[data-filter-row]'));
                const headers = Array.from(table.querySelectorAll('thead th[data-filter-column]'));
                const filters = new Map();

                const applyFilters = () => {
                    rows.forEach((row) => {
                        const visible = headers.every((header) => {
                            const column = Number(header.dataset.filterColumn);
                            const selected = filters.get(column);
                            if (!selected) return true;

                            const cell = row.cells[column];
                            if (selected.type === 'date-range') {
                                const value = normalize(cell?.dataset.filterDate);
                                return value && (!selected.from || value >= selected.from) && (!selected.to || value <= selected.to);
                            }

                            const value = normalize(cell?.dataset.filterValue || cell?.textContent);
                            return selected.has(value);
                        });
                        row.hidden = !visible;
                    });
                };

                headers.forEach((header) => {
                    const column = Number(header.dataset.filterColumn);
                    const label = normalize(header.textContent);
                    const wrapper = document.createElement('div');
                    wrapper.className = 'th-filter';
                    const text = document.createElement('span');
                    text.textContent = label;
                    const details = document.createElement('details');
                    details.className = 'column-filter';
                    const summary = document.createElement('summary');
                    summary.textContent = 'Filtro';
                    summary.title = `Filtrar ${label}`;
                    const panel = document.createElement('div');
                    panel.className = 'column-filter-panel';

                    summary.addEventListener('click', () => {
                        if (!details.open) closeColumnFilters(details);
                    });

                    if (header.dataset.filterType === 'date-range') {
                        panel.classList.add('date-range-filter-panel');
                        let draftFrom = '';
                        let draftTo = '';
                        const dateValues = rows.map((row) => normalize(row.cells[column]?.dataset.filterDate)).filter(Boolean).sort();
                        let monthCursor = dateValues[0] ? parseDateKey(dateValues[0]) : new Date();
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
                                if (date.getMonth() !== monthCursor.getMonth()) day.classList.add('is-muted');
                                if (draftFrom && draftTo && key > draftFrom && key < draftTo) day.classList.add('is-in-range');
                                if (key === draftFrom || key === draftTo) day.classList.add('is-selected');
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

                        const actions = document.createElement('div');
                        actions.className = 'column-filter-actions';
                        const apply = document.createElement('button');
                        apply.className = 'button primary small';
                        apply.type = 'button';
                        apply.textContent = 'Aceptar';
                        apply.addEventListener('click', () => {
                            if (!draftFrom && !draftTo) filters.delete(column);
                            else filters.set(column, { type: 'date-range', from: draftFrom, to: draftTo || draftFrom });
                            applyFilters();
                            details.removeAttribute('open');
                        });
                        const clear = document.createElement('button');
                        clear.className = 'button ghost small';
                        clear.type = 'button';
                        clear.textContent = 'Limpiar';
                        clear.addEventListener('click', () => {
                            draftFrom = '';
                            draftTo = '';
                            filters.delete(column);
                            applyFilters();
                            renderCalendar();
                            details.removeAttribute('open');
                        });
                        const cancel = document.createElement('button');
                        cancel.className = 'button ghost small';
                        cancel.type = 'button';
                        cancel.textContent = 'Cancelar';
                        cancel.addEventListener('click', () => {
                            const selected = filters.get(column) || {};
                            draftFrom = selected.from || '';
                            draftTo = selected.to || '';
                            details.removeAttribute('open');
                        });

                        details.addEventListener('toggle', () => {
                            if (details.open) {
                                const selected = filters.get(column) || {};
                                draftFrom = selected.from || '';
                                draftTo = selected.to || '';
                                monthCursor = draftFrom ? parseDateKey(draftFrom) : (dateValues[0] ? parseDateKey(dateValues[0]) : new Date());
                                renderCalendar();
                                requestAnimationFrame(() => positionFilterPanel(panel, summary));
                            }
                        });

                        actions.append(apply, clear, cancel);
                        calendar.append(calendarHeader, weekdays, days, summaryRange, actions);
                        panel.append(calendar);
                    } else {
                        const values = [...new Set(rows.map((row) => {
                            const cell = row.cells[column];
                            return normalize(cell?.dataset.filterValue || cell?.textContent);
                        }).filter(Boolean))].sort((a, b) => a.localeCompare(b, 'es'));
                        const options = document.createElement('div');
                        options.className = 'column-filter-options';
                        const checkboxes = values.map((value) => {
                            const option = document.createElement('label');
                            option.className = 'column-filter-option';
                            const checkbox = document.createElement('input');
                            checkbox.type = 'checkbox';
                            checkbox.value = value;
                            checkbox.checked = true;
                            const optionText = document.createElement('span');
                            optionText.textContent = value;
                            checkbox.addEventListener('change', () => {
                                const checked = checkboxes.filter((input) => input.checked).map((input) => input.value);
                                if (checked.length === checkboxes.length) filters.delete(column);
                                else filters.set(column, new Set(checked));
                                applyFilters();
                            });
                            option.append(checkbox, optionText);
                            options.append(option);
                            return checkbox;
                        });
                        const actions = document.createElement('div');
                        actions.className = 'column-filter-actions';
                        const selectAll = document.createElement('button');
                        selectAll.className = 'button ghost small';
                        selectAll.type = 'button';
                        selectAll.textContent = 'Todos';
                        selectAll.addEventListener('click', () => {
                            checkboxes.forEach((checkbox) => checkbox.checked = true);
                            filters.delete(column);
                            applyFilters();
                        });
                        const clear = document.createElement('button');
                        clear.className = 'button ghost small';
                        clear.type = 'button';
                        clear.textContent = 'Limpiar';
                        clear.addEventListener('click', () => {
                            checkboxes.forEach((checkbox) => checkbox.checked = false);
                            filters.set(column, new Set());
                            applyFilters();
                        });
                        actions.append(selectAll, clear);
                        panel.append(options, actions);
                    }

                    details.append(summary, panel);
                    wrapper.append(text, details);
                    header.textContent = '';
                    header.append(wrapper);
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\finance\services\index.blade.php ENDPATH**/ ?>