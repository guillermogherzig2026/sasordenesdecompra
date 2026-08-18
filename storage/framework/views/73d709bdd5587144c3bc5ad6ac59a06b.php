<?php $__env->startSection('body'); ?>
    <?php
        $catalogItems = $catalogItems ?? collect();
        $itemRows = collect(old('items', [['warehouse_catalog_item_id' => '', 'quantity' => '']]));
        if ($itemRows->isEmpty()) {
            $itemRows = collect([['warehouse_catalog_item_id' => '', 'quantity' => '']]);
        }
        $productOptions = $catalogItems
            ->map(fn ($item) => [
                'id' => (int) $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'unit' => $item->unit ?: 'unidad',
                'price' => '$'.number_format((float) $item->unit_cost, 2),
                'unit_cost' => (float) $item->unit_cost,
                'category' => $item->category ?: '',
                'subcategory' => $item->subcategory ?: '',
                'label' => trim($item->name.' '.($item->sku ? "({$item->sku})" : '')),
            ])
            ->values();
        $productOptionsById = $productOptions->keyBy('id');
    ?>

    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Nueva orden de suministro']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Nueva orden de suministro']); ?>
        <section class="panel supply-order-panel">
            <div class="panel-header">
                <div>
                    <h2>Nueva OS</h2>
                    <p class="fine-print">Solicita insumos del almacen central San Francisco 516.</p>
                </div>
            </div>

            <form class="stack supply-order-form" method="POST" action="<?php echo e(route('buyer.supply-orders.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="grid-2">
                    <label>Empresa
                        <select name="company_id" required>
                            <option value="">Selecciona...</option>
                            <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($company->id); ?>" <?php if(old('company_id') == $company->id): echo 'selected'; endif; ?>><?php echo e($company->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                    <label>Fecha requerida<input name="delivery_date" type="date" value="<?php echo e(old('delivery_date', now()->toDateString())); ?>"></label>
                </div>

                <section class="panel supply-items-panel">
                    <h3>Insumos solicitados</h3>
                    <div class="table-scroll supply-items-scroll">
                        <table class="supply-items-table">
                            <thead>
                                <tr>
                                    <th>Insumo autorizado</th>
                                    <th>Unidad</th>
                                    <th>Precio unitario</th>
                                    <th>Cantidad solicitada</th>
                                    <th>Eliminar</th>
                                </tr>
                            </thead>
                            <tbody data-supply-items-body>
                                <?php $__currentLoopData = $itemRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $rowIndex = is_numeric($index) ? (int) $index : $loop->index;
                                        $selectedId = (int) ($row['warehouse_catalog_item_id'] ?? 0);
                                        $selectedProduct = $selectedId ? $productOptionsById->get($selectedId) : null;
                                    ?>
                                    <tr data-supply-row data-row-index="<?php echo e($rowIndex); ?>">
                                        <td>
                                            <div class="supply-picker" data-supply-picker>
                                                <input type="hidden" name="items[<?php echo e($rowIndex); ?>][warehouse_catalog_item_id]" value="<?php echo e($selectedId ?: ''); ?>" data-supply-product-id>
                                                <button class="supply-picker-toggle" type="button" data-supply-picker-toggle>
                                                    <?php echo e($selectedProduct['label'] ?? 'Selecciona...'); ?>

                                                </button>
                                                <div class="supply-picker-panel" data-supply-picker-panel hidden>
                                                    <input class="supply-picker-search" type="search" placeholder="Buscar insumo..." data-supply-picker-search>
                                                    <div class="supply-picker-options" data-supply-picker-options></div>
                                                    <div class="supply-picker-actions">
                                                        <button class="button primary small" type="button" data-supply-picker-accept>Aceptar</button>
                                                        <button class="button ghost small" type="button" data-supply-picker-cancel>Cancelar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span data-supply-unit><?php echo e($selectedProduct['unit'] ?? '—'); ?></span></td>
                                        <td><span data-supply-price><?php echo e($selectedProduct['price'] ?? '—'); ?></span></td>
                                        <td><input name="items[<?php echo e($rowIndex); ?>][quantity]" type="number" min="0.01" step="0.01" value="<?php echo e($row['quantity'] ?? ''); ?>" required></td>
                                        <td><button class="button danger small supply-remove-row-button" type="button" data-remove-supply-row>-</button></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="supply-add-row-actions">
                        <button class="button ghost supply-add-row-button" type="button" data-add-supply-row>+ Agregar insumo</button>
                    </div>
                </section>

                <div class="form-actions">
                    <a class="button ghost" href="<?php echo e(route('buyer.supply-orders.index')); ?>">Cancelar</a>
                    <button class="button primary" type="submit">Enviar OS</button>
                </div>
            </form>
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

    <template id="supply-row-template">
        <tr data-supply-row data-row-index="__INDEX__">
            <td>
                <div class="supply-picker" data-supply-picker>
                    <input type="hidden" name="items[__INDEX__][warehouse_catalog_item_id]" value="" data-supply-product-id>
                    <button class="supply-picker-toggle" type="button" data-supply-picker-toggle>Selecciona...</button>
                    <div class="supply-picker-panel" data-supply-picker-panel hidden>
                        <input class="supply-picker-search" type="search" placeholder="Buscar insumo..." data-supply-picker-search>
                        <div class="supply-picker-options" data-supply-picker-options></div>
                        <div class="supply-picker-actions">
                            <button class="button primary small" type="button" data-supply-picker-accept>Aceptar</button>
                            <button class="button ghost small" type="button" data-supply-picker-cancel>Cancelar</button>
                        </div>
                    </div>
                </div>
            </td>
            <td><span data-supply-unit>—</span></td>
            <td><span data-supply-price>—</span></td>
            <td><input name="items[__INDEX__][quantity]" type="number" min="0.01" step="0.01" value="" required></td>
            <td><button class="button danger small supply-remove-row-button" type="button" data-remove-supply-row>-</button></td>
        </tr>
    </template>

    <script type="application/json" id="supply-catalog-data"><?php echo json_encode($productOptions, 15, 512) ?></script>

    <style>
        .supply-order-panel {
            min-height: calc(100vh - 126px);
            align-content: stretch;
        }

        .supply-order-form {
            min-height: calc(100vh - 250px);
            display: grid;
            grid-template-rows: auto minmax(0, 1fr) auto;
        }

        .supply-items-panel {
            min-height: min(680px, calc(100vh - 320px));
            align-content: stretch;
            grid-template-rows: auto minmax(0, 1fr) auto;
        }

        .supply-items-scroll {
            min-height: 0;
            overflow: visible;
        }

        .supply-items-table {
            min-width: 1160px;
        }

        .supply-items-table th:nth-child(1),
        .supply-items-table td:nth-child(1) {
            width: 420px;
        }

        .supply-picker {
            position: relative;
            min-width: 320px;
        }

        .supply-picker-toggle {
            width: 100%;
            min-height: 42px;
            padding: 10px 38px 10px 12px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            color: var(--text);
            text-align: left;
            font-weight: 700;
            cursor: pointer;
            position: relative;
        }

        .supply-picker-toggle::after {
            content: 'v';
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: .82rem;
        }

        .supply-picker-panel {
            position: absolute;
            z-index: 2500;
            top: calc(100% + 5px);
            left: 0;
            width: min(420px, calc(100vw - 80px));
            padding: 10px;
            border: 1px solid #b8b8b8;
            border-radius: 6px;
            background: #fff;
            box-shadow: 0 18px 42px rgba(35, 48, 73, .2);
            display: grid;
            gap: 8px;
        }

        .supply-picker-panel[hidden] {
            display: none !important;
        }

        .supply-picker-search {
            min-height: 34px;
            padding: 7px 10px;
            border-radius: 4px;
        }

        .supply-picker-options {
            max-height: 240px;
            overflow: auto;
            display: grid;
            gap: 2px;
            padding: 3px;
            border: 1px solid #d0d0d0;
            background: #fff;
        }

        .supply-picker-option {
            display: flex;
            align-items: flex-start;
            gap: 6px;
            min-height: 28px;
            padding: 5px 4px;
            color: #111827;
            font-size: .88rem;
            font-weight: 700;
            line-height: 1.2;
            cursor: pointer;
        }

        .supply-picker-option:hover {
            background: #e5f3f7;
        }

        .supply-picker-option input {
            width: 14px;
            min-height: 14px;
            margin-top: 2px;
            accent-color: #176b87;
        }

        .supply-picker-option small {
            display: block;
            margin-top: 2px;
            color: var(--muted);
            font-weight: 650;
        }

        .supply-picker-empty {
            padding: 9px;
            color: var(--muted);
            font-weight: 700;
        }

        .supply-picker-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .supply-add-row-actions {
            display: flex;
            justify-content: flex-start;
            padding-top: 12px;
        }

        .supply-add-row-button {
            min-width: 170px;
        }

        .supply-remove-row-button {
            min-width: 34px;
            padding-inline: 10px;
            font-size: 1.1rem;
            font-weight: 900;
        }
    </style>

    <script>
        (() => {
            const data = document.getElementById('supply-catalog-data');
            const body = document.querySelector('[data-supply-items-body]');
            const template = document.getElementById('supply-row-template');
            const addButton = document.querySelector('[data-add-supply-row]');
            if (!data || !body || !template || !addButton) return;

            let products = [];
            try {
                products = JSON.parse(data.textContent || '[]');
            } catch (error) {
                products = [];
            }

            const normalize = (value) => String(value || '')
                .toLocaleLowerCase('es')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim();

            const productById = new Map(products.map((product) => [String(product.id), product]));
            const searchText = (product) => normalize([
                product.name,
                product.sku,
                product.category,
                product.subcategory,
                product.unit,
            ].join(' '));

            const closeAllPickers = (except = null) => {
                document.querySelectorAll('[data-supply-picker]').forEach((picker) => {
                    if (picker === except) return;
                    picker.querySelector('[data-supply-picker-panel]').hidden = true;
                });
            };

            const setRowProduct = (row, productId) => {
                const product = productById.get(String(productId));
                row.querySelector('[data-supply-product-id]').value = product ? product.id : '';
                row.querySelector('[data-supply-picker-toggle]').textContent = product ? product.label : 'Selecciona...';
                row.querySelector('[data-supply-unit]').textContent = product ? product.unit : '—';
                row.querySelector('[data-supply-price]').textContent = product ? product.price : '—';
            };

            const renderOptions = (picker) => {
                const panel = picker.querySelector('[data-supply-picker-panel]');
                const options = picker.querySelector('[data-supply-picker-options]');
                const search = picker.querySelector('[data-supply-picker-search]');
                const hidden = picker.querySelector('[data-supply-product-id]');
                const query = normalize(search.value);
                const pending = String(picker.dataset.pendingProductId || hidden.value || '');

                options.innerHTML = '';
                const matches = products
                    .filter((product) => !query || searchText(product).includes(query))
                    .slice(0, 120);

                if (!matches.length) {
                    const empty = document.createElement('span');
                    empty.className = 'supply-picker-empty';
                    empty.textContent = 'No hay insumos coincidentes.';
                    options.appendChild(empty);
                    panel.hidden = false;
                    return;
                }

                matches.forEach((product) => {
                    const label = document.createElement('label');
                    label.className = 'supply-picker-option';

                    const checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.value = product.id;
                    checkbox.checked = String(product.id) === pending;

                    const copy = document.createElement('span');
                    const title = document.createElement('strong');
                    title.textContent = product.name;
                    const detail = document.createElement('small');
                    detail.textContent = [product.sku, product.category, product.subcategory].filter(Boolean).join(' · ');
                    copy.append(title, detail);

                    checkbox.addEventListener('change', () => {
                        options.querySelectorAll('input[type="checkbox"]').forEach((input) => {
                            if (input !== checkbox) input.checked = false;
                        });
                        picker.dataset.pendingProductId = checkbox.checked ? String(product.id) : '';
                    });

                    label.append(checkbox, copy);
                    options.appendChild(label);
                });
            };

            const initPicker = (picker) => {
                const row = picker.closest('[data-supply-row]');
                const panel = picker.querySelector('[data-supply-picker-panel]');
                const toggle = picker.querySelector('[data-supply-picker-toggle]');
                const search = picker.querySelector('[data-supply-picker-search]');
                const accept = picker.querySelector('[data-supply-picker-accept]');
                const cancel = picker.querySelector('[data-supply-picker-cancel]');
                const hidden = picker.querySelector('[data-supply-product-id]');

                toggle.addEventListener('click', () => {
                    const shouldOpen = panel.hidden;
                    closeAllPickers(picker);
                    if (!shouldOpen) {
                        panel.hidden = true;
                        return;
                    }
                    picker.dataset.pendingProductId = hidden.value || '';
                    search.value = '';
                    renderOptions(picker);
                    panel.hidden = false;
                    search.focus();
                });

                search.addEventListener('input', () => renderOptions(picker));
                accept.addEventListener('click', () => {
                    setRowProduct(row, picker.dataset.pendingProductId || '');
                    panel.hidden = true;
                });
                cancel.addEventListener('click', () => {
                    picker.dataset.pendingProductId = hidden.value || '';
                    panel.hidden = true;
                });
            };

            const nextIndex = () => {
                const indexes = Array.from(body.querySelectorAll('[data-supply-row]'))
                    .map((row) => Number(row.dataset.rowIndex))
                    .filter((value) => Number.isFinite(value));
                return indexes.length ? Math.max(...indexes) + 1 : 0;
            };

            const addRow = () => {
                const index = nextIndex();
                const wrapper = document.createElement('tbody');
                wrapper.innerHTML = template.innerHTML.replaceAll('__INDEX__', String(index)).trim();
                const row = wrapper.firstElementChild;
                body.appendChild(row);
                initPicker(row.querySelector('[data-supply-picker]'));
            };

            const ensureOneRow = () => {
                if (body.querySelector('[data-supply-row]')) return;
                addRow();
            };

            body.querySelectorAll('[data-supply-picker]').forEach(initPicker);
            addButton.addEventListener('click', addRow);

            body.addEventListener('click', (event) => {
                const removeButton = event.target.closest('[data-remove-supply-row]');
                if (!removeButton) return;

                removeButton.closest('[data-supply-row]')?.remove();
                ensureOneRow();
            });

            document.addEventListener('click', (event) => {
                if (event.target.closest('[data-supply-picker]')) return;
                closeAllPickers();
            });
        })();
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\buyer\supply-orders\form.blade.php ENDPATH**/ ?>