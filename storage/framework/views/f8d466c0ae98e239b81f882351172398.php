<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Inventarios por almacen']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Inventarios por almacen']); ?>
        <section class="panel">
            <div>
                <h2>Actualizar inventario</h2>
                <p class="fine-print">Administra existencias por almacen. Para OS se usa el almacen central San Francisco 516.</p>
            </div>
            <form class="stack" method="POST" action="<?php echo e(route('inventory.stock.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="grid-4">
                    <label>Insumo
                        <select name="warehouse_catalog_item_id" required>
                            <option value="">Selecciona...</option>
                            <?php $__currentLoopData = $catalogItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($item->id); ?>"><?php echo e($item->name); ?> <?php if($item->sku): ?> (<?php echo e($item->sku); ?>) <?php endif; ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                    <label>Almacen<input name="warehouse" value="<?php echo e(old('warehouse', \App\Models\WarehouseInventoryItem::CENTRAL_WAREHOUSE)); ?>" required></label>
                    <label>Existencia<input name="quantity" type="number" min="0" step="0.01" required></label>
                    <label>Minimo<input name="minimum_quantity" type="number" min="0" step="0.01" value="0"></label>
                </div>
                <div class="form-actions"><button class="button primary" type="submit">Guardar inventario</button></div>
            </form>
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Inventarios</h2>
                    <?php if(($warehouseFilter ?? '') !== ''): ?>
                        <p class="fine-print">Existencias filtradas por almacen: <strong><?php echo e($warehouseFilter); ?></strong></p>
                    <?php endif; ?>
                </div>
                <form class="toolbar" method="GET" action="<?php echo e(route('inventory.stock.index')); ?>">
                    <?php if(($warehouseFilter ?? '') !== ''): ?>
                        <input name="warehouse" type="hidden" value="<?php echo e($warehouseFilter); ?>">
                    <?php endif; ?>
                    <input name="q" value="<?php echo e($query); ?>" placeholder="Buscar insumo...">
                    <?php if(($warehouseFilter ?? '') !== ''): ?>
                        <a class="button ghost small" href="<?php echo e(route('inventory.stock.index')); ?>">Ver todos</a>
                    <?php endif; ?>
                </form>
            </div>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Almacen</th>
                            <th>SKU</th>
                            <th>Insumo</th>
                            <th>Existencia</th>
                            <th>Minimo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($item->warehouse); ?></td>
                                <td><?php echo e($item->catalogItem->sku ?: '—'); ?></td>
                                <td><?php echo e($item->catalogItem->name); ?></td>
                                <td><?php echo e(number_format((float) $item->quantity, 2)); ?></td>
                                <td><?php echo e(number_format((float) $item->minimum_quantity, 2)); ?></td>
                                <td>
                                    <?php if((float) $item->quantity <= (float) $item->minimum_quantity): ?>
                                        <span class="status rejected">Bajo minimo</span>
                                    <?php else: ?>
                                        <span class="status paid">Disponible</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="6">No hay inventario registrado.</td></tr>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\inventory\stock\index.blade.php ENDPATH**/ ?>