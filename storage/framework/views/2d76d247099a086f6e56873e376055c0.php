<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Almacenes']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Almacenes']); ?>
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Almacenes</h2>
                    <p class="fine-print">Listado de almacenes registrados en todas las empresas, mas el almacen central de suministros.</p>
                </div>
                <form class="toolbar" method="GET" action="<?php echo e(route('inventory.warehouses.index')); ?>">
                    <input name="q" value="<?php echo e($query); ?>" placeholder="Buscar almacen, empresa o RFC...">
                </form>
                <div class="table-export-actions">
                    <a class="button primary" href="<?php echo e(route('inventory.warehouses.supply.create')); ?>">Agregar almacen de suministros</a>
                </div>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Empresa</th>
                            <th>Almacen</th>
                            <th>Nombre corto</th>
                            <th>RFC</th>
                            <th>Direccion / referencia</th>
                            <th>Ver movimientos</th>
                            <th>Ver existencias</th>
                            <th>Catalogo de productos</th>
                            <th>Editar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $warehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><span class="status <?php echo e($warehouse['type'] === 'Almacen de suministros' ? 'approved' : 'pending'); ?>"><?php echo e($warehouse['type']); ?></span></td>
                                <td><?php echo e($warehouse['company']); ?></td>
                                <td><strong><?php echo e($warehouse['warehouse']); ?></strong></td>
                                <td><?php echo e($warehouse['short_name']); ?></td>
                                <td><?php echo e($warehouse['rfc']); ?></td>
                                <td><?php echo e($warehouse['address']); ?></td>
                                <td>
                                    <a class="button ghost small" href="<?php echo e(route('inventory.warehouses.movements', $warehouse['key'])); ?>" target="_blank" rel="noopener">Ver movimientos</a>
                                </td>
                                <td>
                                    <a class="button ghost small" href="<?php echo e(route('inventory.stock.index', ['warehouse' => $warehouse['real_warehouse'] ?: $warehouse['warehouse']])); ?>">Ver existencias</a>
                                </td>
                                <td>
                                    <a class="button ghost small" href="<?php echo e(route('inventory.warehouses.catalog', $warehouse['key'])); ?>">Ver catalogo</a>
                                </td>
                                <td>
                                    <a class="button ghost small" href="<?php echo e(route('inventory.warehouses.edit', $warehouse['key'])); ?>">Editar</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="10">No hay almacenes para mostrar.</td></tr>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\inventory\warehouses\index.blade.php ENDPATH**/ ?>