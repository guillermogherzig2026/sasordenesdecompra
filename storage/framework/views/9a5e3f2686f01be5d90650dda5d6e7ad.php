<?php
    $items = old('items', $order?->items?->map(fn ($item) => [
        'article' => $item->article,
        'quantity' => $item->quantity,
        'unit_price' => $item->unit_price,
    ])->all() ?? [['article' => '', 'quantity' => '', 'unit_price' => 0]]);
    $isCredit = old('is_credit', $order?->is_credit ? '1' : null);
    $selectedProviderId = (int) old('provider_id', $order?->provider_id);
    $selectedProvider = $providers->firstWhere('id', $selectedProviderId);
    $referenceValue = old('reference', $order?->reference ?? $selectedProvider?->reference);
    $selectedCompanyId = (int) old('company_id', $order?->company_id);
    $selectedWarehouse = old('warehouse', $order?->warehouse);
    $constructionContext = $constructionContext ?? false;
    $constructionProjects = $constructionProjects ?? collect();
    $routeContext = $constructionContext ? ['context' => 'construction'] : [];
    $selectedConstructionProjectId = (int) old('construction_project_id', $order?->construction_project_id);
    $formAction = $order
        ? route('buyer.orders.update', array_merge(['purchaseOrder' => $order], $routeContext))
        : route('buyer.orders.store', $routeContext);
    $currentUser = auth()->user();
    $warehousesByCompany = $companies->mapWithKeys(function ($company) use ($currentUser) {
        if ($currentUser?->role === 'superadmin') {
            $objects = $company->warehouseObjects();
        } else {
            $authorized = $currentUser?->authorizedWarehousesFor($company->name) ?: $company->warehouseList();
            $objects = collect($authorized)->map(fn ($name) => [
                'name' => $name,
                'short_name' => $company->warehouseShortNameFor($name),
            ])->all();
        }

        return [$company->id => $objects];
    });
?>

<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => $constructionContext ? ($order ? 'Editar orden de compra de obra' : 'Nueva orden de compra de obra') : ($order ? 'Editar orden de compra' : 'Nueva orden de compra')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($constructionContext ? ($order ? 'Editar orden de compra de obra' : 'Nueva orden de compra de obra') : ($order ? 'Editar orden de compra' : 'Nueva orden de compra'))]); ?>
        <form class="panel" method="POST" enctype="multipart/form-data" action="<?php echo e($formAction); ?>">
            <?php echo csrf_field(); ?>
            <?php if($order): ?>
                <?php echo method_field('PUT'); ?>
            <?php endif; ?>

            <div class="panel-header">
                <div>
                    <h2><?php echo e($order ? "Editar {$order->folio}" : ($constructionContext ? 'Nueva orden de compra de obra' : 'Nueva orden de compra')); ?></h2>
                    <p class="fine-print"><?php echo e($constructionContext ? 'La orden quedara vinculada exclusivamente a la obra seleccionada.' : 'La orden se guarda como enviada y queda pendiente de revision por Finanzas.'); ?></p>
                </div>
                <a class="button ghost" href="<?php echo e(route('buyer.orders.index', $routeContext)); ?>">Volver</a>
            </div>

            <?php if($constructionContext): ?>
                <div class="grid-3">
                    <label>
                        Obra
                        <select name="construction_project_id" required>
                            <option value="">Seleccionar obra...</option>
                            <?php $__currentLoopData = $constructionProjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($project->id); ?>" <?php if($selectedConstructionProjectId === $project->id): echo 'selected'; endif; ?>>
                                    <?php echo e($project->project_key); ?> - <?php echo e($project->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                </div>
            <?php endif; ?>

            <div class="grid-3">
                <label>
                    Empresa autorizada
                    <select id="company-select" name="company_id" required>
                        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($company->id); ?>" <?php if($selectedCompanyId === $company->id): echo 'selected'; endif; ?>>
                                <?php echo e($company->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </label>
                <label>
                    Almacen
                    <select id="warehouse-select" name="warehouse" data-selected="<?php echo e($selectedWarehouse); ?>"></select>
                </label>
                <label>
                    Proveedor
                    <select name="provider_id" required>
                        <option value="">Seleccionar proveedor...</option>
                        <?php $__currentLoopData = $providers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($provider->id); ?>" data-reference="<?php echo e($provider->reference); ?>" <?php if((int) old('provider_id', $order?->provider_id) === $provider->id): echo 'selected'; endif; ?>>
                                <?php echo e($provider->business_name); ?> - <?php echo e($provider->business_line); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </label>
            </div>

            <div class="grid-3">
                <label>
                    Fecha de entrega
                    <input name="delivery_date" type="date" value="<?php echo e(old('delivery_date', $order?->delivery_date?->format('Y-m-d'))); ?>" required>
                </label>
                <label>
                    Fecha de vencimiento
                    <input name="due_date" type="date" value="<?php echo e(old('due_date', $order?->due_date?->format('Y-m-d'))); ?>" required>
                </label>
                <div class="credit-control">
                    <label class="checkbox-inline">
                        <input id="is-credit" name="is_credit" type="checkbox" value="1" <?php if((bool) $isCredit): echo 'checked'; endif; ?>>
                        Credito
                    </label>
                    <label>
                        Dias de credito
                        <input id="credit-days" name="credit_days" type="number" min="1" max="366" value="<?php echo e(old('credit_days', $order?->credit_days)); ?>" <?php if(! $isCredit): echo 'disabled'; endif; ?>>
                    </label>
                </div>
            </div>

            <div class="grid-2">
                <label>
                    Referencia
                    <input id="order-reference" name="reference" value="<?php echo e($referenceValue); ?>" placeholder="Referencia bancaria o linea de captura">
                </label>
                <label>
                    Concepto de pago
                    <input name="payment_concept" value="<?php echo e(old('payment_concept', $order?->payment_concept)); ?>" placeholder="Concepto libre para el pago">
                </label>
            </div>

            <label>
                Observaciones para PDF
                <textarea name="observations" rows="3" placeholder="Observaciones que solo apareceran en el PDF de la orden de compra"><?php echo e(old('observations', $order?->observations)); ?></textarea>
            </label>

            <label>
                Cotizacion del proveedor (PDF/JPG)
                <input name="quote_file" type="file" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png">
                <?php if($order?->quote_file_path): ?>
                    <span class="fine-print">Archivo actual: <?php echo e($order->quote_original_name); ?></span>
                <?php endif; ?>
            </label>

            <div class="table-scroll">
                <table id="items-table">
                    <thead>
                        <tr>
                            <th>Articulo</th>
                            <th>Cantidad</th>
                            <th>Precio unit.</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><input name="items[<?php echo e($index); ?>][article]" value="<?php echo e($item['article'] ?? ''); ?>" required></td>
                                <td><input name="items[<?php echo e($index); ?>][quantity]" type="number" min="0.01" step="0.01" value="<?php echo e($item['quantity'] ?? ''); ?>" required></td>
                                <td><input name="items[<?php echo e($index); ?>][unit_price]" type="number" min="0" step="0.001" value="<?php echo e($item['unit_price'] ?? 0); ?>" required></td>
                                <td><strong data-row-subtotal>$0.00</strong></td>
                                <td><button class="button ghost small" type="button" data-remove-row>Quitar</button></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="form-actions order-total-actions">
                <button class="button ghost" type="button" id="add-item">Agregar articulo</button>
                <div class="total-preview" aria-live="polite">
                    <span>Total de articulos</span>
                    <strong id="order-total">$0.00</strong>
                </div>
                <button class="button primary" type="submit"><?php echo e($order ? 'Guardar cambios' : 'Enviar OC'); ?></button>
            </div>
        </form>

        <script>
            const tableBody = document.querySelector('#items-table tbody');
            const addButton = document.querySelector('#add-item');
            const companySelect = document.querySelector('#company-select');
            const warehouseSelect = document.querySelector('#warehouse-select');
            const warehousesByCompany = <?php echo json_encode($warehousesByCompany, 15, 512) ?>;
            const creditCheckbox = document.querySelector('#is-credit');
            const creditDays = document.querySelector('#credit-days');
            const deliveryDate = document.querySelector('input[name="delivery_date"]');
            const dueDate = document.querySelector('input[name="due_date"]');
            const providerSelect = document.querySelector('select[name="provider_id"]');
            const referenceInput = document.querySelector('#order-reference');
            const orderTotal = document.querySelector('#order-total');
            let lastProviderReference = referenceInput?.value || '';

            function syncWarehouseOptions() {
                if (!companySelect || !warehouseSelect) return;

                const selected = warehouseSelect.dataset.selected || warehouseSelect.value;
                const warehouses = warehousesByCompany[companySelect.value] || [];
                warehouseSelect.innerHTML = '';

                if (!warehouses.length) {
                    warehouseSelect.required = false;
                    warehouseSelect.insertAdjacentHTML('beforeend', '<option value="">Sin almacen asignado</option>');
                    return;
                }

                warehouseSelect.required = true;
                warehouseSelect.insertAdjacentHTML('beforeend', '<option value="">Seleccionar almacen...</option>');
                warehouses.forEach((warehouse) => {
                    const label = warehouse.short_name ? `${warehouse.name} (${warehouse.short_name})` : warehouse.name;
                    const option = new Option(label, warehouse.name, false, warehouse.name === selected);
                    warehouseSelect.add(option);
                });
            }

            companySelect?.addEventListener('change', () => {
                warehouseSelect.dataset.selected = '';
                syncWarehouseOptions();
            });
            syncWarehouseOptions();

            function calculateDueDateFromCredit() {
                if (!creditCheckbox.checked || !deliveryDate.value || !creditDays.value) return;

                const days = Number.parseInt(creditDays.value, 10);
                if (!Number.isFinite(days) || days < 1) return;

                const nextDueDate = new Date(`${deliveryDate.value}T00:00:00`);
                nextDueDate.setDate(nextDueDate.getDate() + days);
                dueDate.value = nextDueDate.toISOString().slice(0, 10);
            }

            function syncCredit() {
                creditDays.disabled = !creditCheckbox.checked;
                creditDays.required = creditCheckbox.checked;
                if (!creditCheckbox.checked) {
                    creditDays.value = '';
                    return;
                }

                calculateDueDateFromCredit();
            }

            creditCheckbox.addEventListener('change', syncCredit);
            creditDays.addEventListener('input', calculateDueDateFromCredit);
            deliveryDate.addEventListener('change', calculateDueDateFromCredit);
            syncCredit();

            providerSelect.addEventListener('change', () => {
                const nextReference = providerSelect.selectedOptions[0]?.dataset.reference || '';

                if (!referenceInput.value || referenceInput.value === lastProviderReference) {
                    referenceInput.value = nextReference;
                }

                lastProviderReference = nextReference;
            });

            function renumberRows() {
                tableBody.querySelectorAll('tr').forEach((row, index) => {
                    row.querySelectorAll('input').forEach((input) => {
                        input.name = input.name.replace(/items\[\d+\]/, `items[${index}]`);
                    });
                });
            }

            const currency = new Intl.NumberFormat('es-MX', {
                style: 'currency',
                currency: 'MXN',
            });

            function numericValue(input) {
                return Number.parseFloat(input?.value || '0') || 0;
            }

            function updateOrderTotal() {
                let total = 0;

                tableBody.querySelectorAll('tr').forEach((row) => {
                    const quantity = numericValue(row.querySelector('input[name*="[quantity]"]'));
                    const unitPrice = numericValue(row.querySelector('input[name*="[unit_price]"]'));
                    const subtotal = quantity * unitPrice;
                    total += subtotal;

                    const subtotalCell = row.querySelector('[data-row-subtotal]');
                    if (subtotalCell) subtotalCell.textContent = currency.format(subtotal);
                });

                if (orderTotal) orderTotal.textContent = currency.format(total);
            }

            addButton.addEventListener('click', () => {
                const index = tableBody.querySelectorAll('tr').length;
                tableBody.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td><input name="items[${index}][article]" required></td>
                        <td><input name="items[${index}][quantity]" type="number" min="0.01" step="0.01" required></td>
                        <td><input name="items[${index}][unit_price]" type="number" min="0" step="0.001" value="0" required></td>
                        <td><strong data-row-subtotal>$0.00</strong></td>
                        <td><button class="button ghost small" type="button" data-remove-row>Quitar</button></td>
                    </tr>
                `);
                updateOrderTotal();
            });

            tableBody.addEventListener('input', (event) => {
                if (event.target.matches('input[name*="[quantity]"], input[name*="[unit_price]"]')) {
                    updateOrderTotal();
                }
            });

            tableBody.addEventListener('click', (event) => {
                const button = event.target.closest('[data-remove-row]');
                if (!button) return;
                if (tableBody.querySelectorAll('tr').length === 1) return;
                button.closest('tr').remove();
                renumberRows();
                updateOrderTotal();
            });

            updateOrderTotal();
        </script>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\buyer\orders\form.blade.php ENDPATH**/ ?>