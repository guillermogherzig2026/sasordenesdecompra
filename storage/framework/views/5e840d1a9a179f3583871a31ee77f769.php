<?php
    $materialsCategories = collect($materialsCatalog['categories'] ?? []);
?>

<section
    class="panel materials-explosion-panel"
    id="materials-explosion-catalog"
    data-materials-explosion-panel
    data-no-section-export
    aria-hidden="true"
    hidden
>
    <div class="panel-header materials-explosion-header">
        <div class="panel-header-title">
            <h2>Detalle de explosion de insumos</h2>
            <p class="fine-print">Catalogo general para cuantificar materiales e insumos en todas las obras.</p>
        </div>
        <div class="materials-explosion-actions">
            <span class="fine-print" data-materials-status aria-live="polite"></span>
            <input type="file" accept=".xlsx,.xls,.csv" data-materials-import-input hidden>
            <button class="button ghost small" type="button" data-materials-import>Importar Excel</button>
            <button class="button ghost small" type="button" data-materials-download>Descargar</button>
        </div>
    </div>

    <div class="materials-formula">
        <span class="materials-formula-icon" aria-hidden="true">i</span>
        <strong>Insumo requerido = Cantidad de obra x Factor de consumo + Merma</strong>
    </div>

    <div class="materials-category-list" data-materials-category-list>
        <?php $__currentLoopData = $materialsCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <section class="materials-category" data-materials-category="<?php echo e($category['key']); ?>">
                <button
                    class="materials-category-heading"
                    type="button"
                    data-materials-category-toggle
                    aria-expanded="<?php echo e($category['expanded'] ? 'true' : 'false'); ?>"
                >
                    <span class="materials-square-toggle" data-materials-toggle-symbol aria-hidden="true"><?php echo e($category['expanded'] ? '-' : '+'); ?></span>
                    <strong><?php echo e($category['name']); ?></strong>
                    <span class="materials-count-badge"><?php echo e(count($category['concepts'])); ?> conceptos</span>
                    <span class="materials-chevron" data-materials-chevron aria-hidden="true"><?php echo e($category['expanded'] ? '^' : 'v'); ?></span>
                </button>

                <div class="materials-category-details" data-materials-category-details <?php if(! $category['expanded']): ?> hidden <?php endif; ?>>
                    <?php if(! empty($category['note'])): ?>
                        <p class="materials-category-note"><?php echo e($category['note']); ?></p>
                    <?php endif; ?>

                    <div class="materials-concept-scroll">
                        <div class="materials-concept-list" data-materials-concepts>
                            <?php $__currentLoopData = $category['concepts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $concept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $conceptTotal = collect($concept['supplies'])->sum(function (array $item) use ($concept): float {
                                        $required = (float) $item['dosage'] * (float) $concept['quantity'] * (1 + ((float) $item['waste'] / 100));

                                        return $required * (float) $item['unit_cost'];
                                    });
                                ?>
                                <section class="materials-concept" data-materials-concept="<?php echo e($concept['key']); ?>">
                                    <div class="materials-concept-summary">
                                        <button
                                            class="materials-square-toggle"
                                            type="button"
                                            data-materials-concept-toggle
                                            aria-label="<?php echo e($concept['expanded'] ? 'Ocultar' : 'Mostrar'); ?> insumos de <?php echo e($concept['name']); ?>"
                                            aria-expanded="<?php echo e($concept['expanded'] ? 'true' : 'false'); ?>"
                                        ><?php echo e($concept['expanded'] ? '-' : '+'); ?></button>
                                        <strong><?php echo e($concept['name']); ?></strong>
                                        <span><?php echo e(number_format((float) $concept['quantity'], 2)); ?></span>
                                        <span><?php echo e($concept['unit']); ?></span>
                                        <span class="materials-count-badge"><?php echo e(count($concept['supplies'])); ?> insumos</span>
                                        <div class="materials-concept-actions">
                                            <button class="button ghost small" type="button" data-materials-edit-concept aria-pressed="false">Editar</button>
                                            <button class="button danger small" type="button" data-materials-delete-concept>Eliminar</button>
                                        </div>
                                    </div>

                                    <div class="materials-concept-details" data-materials-concept-details <?php if(! $concept['expanded']): ?> hidden <?php endif; ?>>
                                        <div class="table-scroll materials-supply-scroll">
                                            <table class="materials-supply-table" data-no-column-tools>
                                                <thead>
                                                    <tr>
                                                        <th>Insumo</th>
                                                        <th>Dosificacion por <?php echo e($concept['unit']); ?></th>
                                                        <th>Unidad de dosificacion</th>
                                                        <th>Cantidad del concepto</th>
                                                        <th>Merma</th>
                                                        <th>Total requerido</th>
                                                        <th>Unidad de compra</th>
                                                        <th>Costo unitario</th>
                                                        <th>Importe</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody data-materials-supply-rows>
                                                    <?php $__currentLoopData = $concept['supplies']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php
                                                            $required = (float) $item['dosage'] * (float) $concept['quantity'] * (1 + ((float) $item['waste'] / 100));
                                                            $amount = $required * (float) $item['unit_cost'];
                                                        ?>
                                                        <tr data-materials-supply-row>
                                                            <td><input type="text" value="<?php echo e($item['name']); ?>" data-materials-supply-name aria-label="Insumo" readonly></td>
                                                            <td><input type="number" min="0" step="0.01" value="<?php echo e(number_format((float) $item['dosage'], 2, '.', '')); ?>" data-materials-dosage aria-label="Dosificacion" readonly></td>
                                                            <td><?php echo e($item['dosage_unit']); ?></td>
                                                            <td>
                                                                <span class="materials-inline-value">
                                                                    <input type="number" min="0" step="0.01" value="<?php echo e(number_format((float) $concept['quantity'], 2, '.', '')); ?>" data-materials-concept-quantity aria-label="Cantidad del concepto" readonly>
                                                                    <span><?php echo e($concept['unit']); ?></span>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span class="materials-inline-value">
                                                                    <input type="number" min="0" step="0.01" value="<?php echo e(number_format((float) $item['waste'], 2, '.', '')); ?>" data-materials-waste aria-label="Merma" readonly>
                                                                    <span>%</span>
                                                                </span>
                                                            </td>
                                                            <td><strong data-materials-required><?php echo e(number_format($required, 2)); ?></strong></td>
                                                            <td><?php echo e($item['purchase_unit']); ?></td>
                                                            <td>
                                                                <span class="materials-currency-input">
                                                                    <span>$</span>
                                                                    <input type="number" min="0" step="0.01" value="<?php echo e(number_format((float) $item['unit_cost'], 2, '.', '')); ?>" data-materials-unit-cost aria-label="Costo unitario" readonly>
                                                                </span>
                                                            </td>
                                                            <td><strong data-materials-amount>$<?php echo e(number_format($amount, 2)); ?></strong></td>
                                                            <td>
                                                                <div class="materials-row-actions">
                                                                    <button class="button ghost small" type="button" data-materials-edit-row aria-pressed="false">Editar</button>
                                                                    <button class="button danger small" type="button" data-materials-delete-row>Eliminar</button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="materials-concept-footer">
                                            <strong>Base de calculo: <?php echo e(number_format((float) $concept['quantity'], 2)); ?> <?php echo e($concept['unit']); ?> por concepto</strong>
                                            <strong data-materials-concept-total>$<?php echo e(number_format($conceptTotal, 2)); ?></strong>
                                        </div>
                                    </div>
                                </section>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>

<script>
    (() => {
        const panel = document.querySelector('[data-materials-explosion-panel]');
        const catalogButton = document.querySelector('[data-materials-catalog-select]');
        if (!panel || !catalogButton) return;

        const projectButtons = Array.from(document.querySelectorAll('[data-project-select]'));
        const status = panel.querySelector('[data-materials-status]');
        const numberFormatter = new Intl.NumberFormat('es-MX', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
        const currencyFormatter = new Intl.NumberFormat('es-MX', {
            style: 'currency',
            currency: 'MXN',
        });
        const normalizeNumber = (value) => Number.parseFloat(value) || 0;

        const setStatus = (message) => {
            if (!status) return;
            status.textContent = message;
            window.clearTimeout(status.materialsStatusTimeout);
            status.materialsStatusTimeout = window.setTimeout(() => status.textContent = '', 3200);
        };

        const setCatalogVisibility = (visible, updateUrl = false) => {
            panel.hidden = !visible;
            panel.setAttribute('aria-hidden', visible ? 'false' : 'true');
            catalogButton.classList.toggle('active', visible);
            catalogButton.setAttribute('aria-pressed', visible ? 'true' : 'false');

            if (visible) {
                projectButtons.forEach((button) => {
                    button.classList.remove('active');
                    button.setAttribute('aria-pressed', 'false');
                });
            }

            if (updateUrl) {
                const nextUrl = visible
                    ? `${window.location.pathname}${window.location.search}#materials-explosion-catalog`
                    : `${window.location.pathname}${window.location.search}`;
                window.history.replaceState({}, '', nextUrl);
            }
        };

        catalogButton.addEventListener('click', (event) => {
            event.preventDefault();
            setCatalogVisibility(true, true);
        });

        projectButtons.forEach((button) => {
            button.addEventListener('click', () => setCatalogVisibility(false, true));
        });

        window.addEventListener('hashchange', () => {
            setCatalogVisibility(window.location.hash === '#materials-explosion-catalog');
        });

        setCatalogVisibility(window.location.hash === '#materials-explosion-catalog');

        const setCategoryExpanded = (button, expanded) => {
            const category = button.closest('[data-materials-category]');
            const details = category?.querySelector('[data-materials-category-details]');
            button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            const symbol = button.querySelector('[data-materials-toggle-symbol]');
            const chevron = button.querySelector('[data-materials-chevron]');
            if (symbol) symbol.textContent = expanded ? '-' : '+';
            if (chevron) chevron.textContent = expanded ? '^' : 'v';
            if (details) details.hidden = !expanded;
        };

        panel.querySelectorAll('[data-materials-category-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                setCategoryExpanded(button, button.getAttribute('aria-expanded') !== 'true');
            });
        });

        const setConceptExpanded = (concept, expanded) => {
            const toggle = concept.querySelector('[data-materials-concept-toggle]');
            const details = concept.querySelector('[data-materials-concept-details]');
            if (toggle) {
                toggle.textContent = expanded ? '-' : '+';
                toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            }
            if (details) details.hidden = !expanded;
        };

        panel.querySelectorAll('[data-materials-concept-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const concept = button.closest('[data-materials-concept]');
                if (!concept) return;
                setConceptExpanded(concept, button.getAttribute('aria-expanded') !== 'true');
            });
        });

        const recalculateConcept = (concept) => {
            let conceptTotal = 0;
            concept.querySelectorAll('[data-materials-supply-row]').forEach((row) => {
                const dosage = normalizeNumber(row.querySelector('[data-materials-dosage]')?.value);
                const quantity = normalizeNumber(row.querySelector('[data-materials-concept-quantity]')?.value);
                const waste = normalizeNumber(row.querySelector('[data-materials-waste]')?.value);
                const unitCost = normalizeNumber(row.querySelector('[data-materials-unit-cost]')?.value);
                const required = dosage * quantity * (1 + (waste / 100));
                const amount = required * unitCost;
                conceptTotal += amount;

                const requiredOutput = row.querySelector('[data-materials-required]');
                const amountOutput = row.querySelector('[data-materials-amount]');
                if (requiredOutput) requiredOutput.textContent = numberFormatter.format(required);
                if (amountOutput) amountOutput.textContent = currencyFormatter.format(amount);
            });

            const totalOutput = concept.querySelector('[data-materials-concept-total]');
            if (totalOutput) totalOutput.textContent = currencyFormatter.format(conceptTotal);
        };

        const setRowEditing = (row, editing) => {
            row.classList.toggle('is-editing', editing);
            row.querySelectorAll('input').forEach((input) => input.readOnly = !editing);
            const editButton = row.querySelector('[data-materials-edit-row]');
            if (editButton) {
                editButton.textContent = editing ? 'Listo' : 'Editar';
                editButton.classList.toggle('primary', editing);
                editButton.classList.toggle('ghost', !editing);
                editButton.setAttribute('aria-pressed', editing ? 'true' : 'false');
            }
        };

        panel.addEventListener('input', (event) => {
            const row = event.target.closest('[data-materials-supply-row]');
            const concept = row?.closest('[data-materials-concept]');
            if (concept) recalculateConcept(concept);
        });

        panel.addEventListener('click', (event) => {
            const editRowButton = event.target.closest('[data-materials-edit-row]');
            if (editRowButton) {
                const row = editRowButton.closest('[data-materials-supply-row]');
                if (row) setRowEditing(row, !row.classList.contains('is-editing'));
                return;
            }

            const deleteRowButton = event.target.closest('[data-materials-delete-row]');
            if (deleteRowButton) {
                const row = deleteRowButton.closest('[data-materials-supply-row]');
                const concept = row?.closest('[data-materials-concept]');
                row?.remove();
                if (concept) recalculateConcept(concept);
                setStatus('Insumo eliminado del catalogo.');
                return;
            }

            const editConceptButton = event.target.closest('[data-materials-edit-concept]');
            if (editConceptButton) {
                const concept = editConceptButton.closest('[data-materials-concept]');
                if (!concept) return;
                const editing = editConceptButton.getAttribute('aria-pressed') !== 'true';
                setConceptExpanded(concept, true);
                concept.querySelectorAll('[data-materials-supply-row]').forEach((row) => setRowEditing(row, editing));
                editConceptButton.textContent = editing ? 'Listo' : 'Editar';
                editConceptButton.setAttribute('aria-pressed', editing ? 'true' : 'false');
                editConceptButton.classList.toggle('primary', editing);
                editConceptButton.classList.toggle('ghost', !editing);
                return;
            }

            const deleteConceptButton = event.target.closest('[data-materials-delete-concept]');
            if (deleteConceptButton) {
                deleteConceptButton.closest('[data-materials-concept]')?.remove();
                setStatus('Concepto eliminado del catalogo.');
            }
        });

        const importButton = panel.querySelector('[data-materials-import]');
        const importInput = panel.querySelector('[data-materials-import-input]');
        importButton?.addEventListener('click', () => importInput?.click());
        importInput?.addEventListener('change', () => {
            const fileName = importInput.files?.[0]?.name;
            if (fileName) setStatus(`Archivo seleccionado: ${fileName}`);
        });

        panel.querySelector('[data-materials-download]')?.addEventListener('click', () => {
            const rows = [['Categoria', 'Concepto', 'Insumo', 'Dosificacion', 'Merma', 'Total requerido', 'Costo unitario', 'Importe']];
            panel.querySelectorAll('[data-materials-category]').forEach((category) => {
                const categoryName = category.querySelector('.materials-category-heading strong')?.textContent?.trim() || '';
                category.querySelectorAll('[data-materials-concept]').forEach((concept) => {
                    const conceptName = concept.querySelector('.materials-concept-summary > strong')?.textContent?.trim() || '';
                    concept.querySelectorAll('[data-materials-supply-row]').forEach((row) => {
                        rows.push([
                            categoryName,
                            conceptName,
                            row.querySelector('[data-materials-supply-name]')?.value || '',
                            row.querySelector('[data-materials-dosage]')?.value || '',
                            row.querySelector('[data-materials-waste]')?.value || '',
                            row.querySelector('[data-materials-required]')?.textContent || '',
                            row.querySelector('[data-materials-unit-cost]')?.value || '',
                            row.querySelector('[data-materials-amount]')?.textContent || '',
                        ]);
                    });
                });
            });

            const csv = rows.map((row) => row.map((value) => `"${String(value).replaceAll('"', '""')}"`).join(',')).join('\n');
            const blob = new Blob([`\uFEFF${csv}`], { type: 'text/csv;charset=utf-8' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'catalogo-explosion-insumos.csv';
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.setTimeout(() => URL.revokeObjectURL(link.href), 1000);
            setStatus('Catalogo descargado.');
        });
    })();
</script>
<?php /**PATH C:\laragon\www\Revision OC Software\resources\views\construction\partials\materials-explosion-catalog.blade.php ENDPATH**/ ?>