<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Movimientos de almacen']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Movimientos de almacen']); ?>
        <section class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow"><?php echo e($warehouse['type']); ?></p>
                    <h2><?php echo e($warehouse['warehouse']); ?></h2>
                    <p class="fine-print"><?php echo e($warehouse['company']); ?> · <?php echo e($warehouse['address']); ?></p>
                </div>
                <div class="table-export-actions">
                    <a class="button ghost" href="<?php echo e(route('inventory.warehouses.index')); ?>">Regresar</a>
                </div>
            </div>

            <form class="panel" method="GET" action="<?php echo e(route('inventory.warehouses.movements', $warehouse['key'])); ?>">
                <div class="grid-4">
                    <label>Tipo de movimiento
                        <select name="type">
                            <option value="">Todos</option>
                            <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($type); ?>" <?php if($filters['type'] === $type): echo 'selected'; endif; ?>><?php echo e($type); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                    <label>Fecha desde<input name="date_from" type="date" value="<?php echo e($filters['date_from']); ?>"></label>
                    <label>Fecha hasta<input name="date_to" type="date" value="<?php echo e($filters['date_to']); ?>"></label>
                    <label>Orden OS / OC<input name="order" value="<?php echo e($filters['order']); ?>" placeholder="Ej. OS-1001 u OC-1001"></label>
                </div>
                <div class="grid-2">
                    <label>Buscar informacion relacionada
                        <input name="q" value="<?php echo e($filters['q']); ?>" placeholder="Producto, empresa, remision, proveedor, usuario...">
                    </label>
                    <div class="form-actions" style="align-self:end;justify-content:flex-start">
                        <button class="button primary" type="submit">Filtrar</button>
                        <a class="button ghost" href="<?php echo e(route('inventory.warehouses.movements', $warehouse['key'])); ?>">Limpiar</a>
                    </div>
                </div>
            </form>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Orden</th>
                            <th>Documento</th>
                            <th>Empresa</th>
                            <th>Almacen</th>
                            <th>Relacionado</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Precio unitario</th>
                            <th>Importe</th>
                            <th>Existencia</th>
                            <th>Estado</th>
                            <th>Informacion relacionada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <?php
                                        $class = match ($movement['type']) {
                                            'Entrada', 'Entrada / Acta de recepcion' => 'approved',
                                            'Salida' => 'rejected',
                                            'Existencia' => 'pending',
                                            default => 'pending',
                                        };
                                    ?>
                                    <span class="status <?php echo e($class); ?>"><?php echo e($movement['type']); ?></span>
                                </td>
                                <td><?php echo e($movement['date']); ?></td>
                                <td><strong><?php echo e($movement['order']); ?></strong></td>
                                <td><?php echo e($movement['document']); ?></td>
                                <td><?php echo e($movement['company']); ?></td>
                                <td><?php echo e($movement['warehouse']); ?></td>
                                <td><?php echo e($movement['related']); ?></td>
                                <td><?php echo e($movement['product']); ?></td>
                                <td><?php echo e($movement['quantity']); ?></td>
                                <td><?php echo e($movement['unit']); ?></td>
                                <td><?php echo e($movement['unit_price']); ?></td>
                                <td><?php echo e($movement['amount']); ?></td>
                                <td><?php echo e($movement['stock']); ?></td>
                                <td><?php echo e($movement['status']); ?></td>
                                <td><?php echo e($movement['details']); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="15">No hay movimientos con los filtros seleccionados.</td></tr>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\inventory\warehouses\movements.blade.php ENDPATH**/ ?>