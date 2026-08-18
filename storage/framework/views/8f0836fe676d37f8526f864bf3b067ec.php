<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'OS por entregar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'OS por entregar']); ?>
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>OS por Entregar</h2>
                    <p class="fine-print">Genera la remision, descuenta el inventario central y deja la OS pendiente de recepcion.</p>
                </div>
                <form class="toolbar" method="GET" action="<?php echo e(route('inventory.supply-orders.active')); ?>">
                    <input name="q" value="<?php echo e($query); ?>" placeholder="Buscar OS...">
                    <a class="button ghost" href="<?php echo e(route('reports.download', 'supply-orders-excel')); ?>">Exportar Excel</a>
                </form>
            </div>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID consecutivo</th>
                            <th>OS</th>
                            <th>Fecha de envio</th>
                            <th>Usuario</th>
                            <th>Empresa</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Descripcion</th>
                            <th>Precio unitario</th>
                            <th>Precio total</th>
                            <th>Remision</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $itemCount = $order->items->count();
                                $totalQuantity = $order->items->sum(fn ($item) => (float) $item->quantity);
                                $units = $order->items->map(fn ($item) => $item->catalogItem?->unit ?: 'unidad')->unique()->values();
                                $unitLabel = $units->count() === 1 ? $units->first() : 'Varias';
                                $singleItem = $itemCount === 1 ? $order->items->first() : null;
                                $totalAmount = $order->items->sum(fn ($item) => (float) $item->line_total);
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($order->supply_consecutive); ?></strong>
                                    <small class="fine-print">General</small>
                                </td>
                                <td>
                                    <strong><?php echo e($order->folio); ?></strong>
                                    <small class="fine-print"><?php echo e(\App\Support\UiStatus::supplyOrder($order->status, 'inventory')); ?></small>
                                </td>
                                <td><?php echo e($order->created_on?->format('d/m/Y')); ?></td>
                                <td><?php echo e($order->requester->name); ?></td>
                                <td><?php echo e($order->company->name); ?></td>
                                <td><?php echo e(number_format((float) $totalQuantity, 2)); ?></td>
                                <td><?php echo e($unitLabel); ?></td>
                                <td>
                                    <?php if (isset($component)) { $__componentOriginal036e9cea2ea3ff109a40294db5da929d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal036e9cea2ea3ff109a40294db5da929d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.supply-order-items-dialog','data' => ['order' => $order,'dialogId' => 'inventory-active-supply-detail-'.$order->id]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('supply-order-items-dialog'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['order' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order),'dialog-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('inventory-active-supply-detail-'.$order->id)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal036e9cea2ea3ff109a40294db5da929d)): ?>
<?php $attributes = $__attributesOriginal036e9cea2ea3ff109a40294db5da929d; ?>
<?php unset($__attributesOriginal036e9cea2ea3ff109a40294db5da929d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal036e9cea2ea3ff109a40294db5da929d)): ?>
<?php $component = $__componentOriginal036e9cea2ea3ff109a40294db5da929d; ?>
<?php unset($__componentOriginal036e9cea2ea3ff109a40294db5da929d); ?>
<?php endif; ?>
                                    <small class="fine-print"><?php echo e($itemCount); ?> <?php echo e($itemCount === 1 ? 'partida' : 'partidas'); ?></small>
                                </td>
                                <td>
                                    <?php if($singleItem): ?>
                                        $<?php echo e(number_format((float) $singleItem->unit_cost, 2)); ?>

                                    <?php else: ?>
                                        <span class="fine-print">Ver detalle</span>
                                    <?php endif; ?>
                                </td>
                                <td>$<?php echo e(number_format((float) $totalAmount, 2)); ?></td>
                                <td>
                                    <?php if($order->delivery_remission_number): ?>
                                        <a class="attachment-pill" href="<?php echo e(route('inventory.supply-orders.remission', $order)); ?>" target="_blank"><span>Remision</span><?php echo e($order->formatted_delivery_remission_number); ?></a>
                                        <div class="fine-print">Pendiente de recibir</div>
                                    <?php else: ?>
                                        <form class="stack" method="POST" action="<?php echo e(route('inventory.supply-orders.deliver', $order)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <label>Fecha salida<input name="delivered_on" type="date" value="<?php echo e(now()->toDateString()); ?>" required></label>
                                            <label>PIN recepcion<input name="receiving_pin" type="password" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" placeholder="1234"></label>
                                            <button class="button primary small" type="submit">Generar remision</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="11">No hay OS autorizadas por entregar.</td></tr>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\inventory\supply-orders\active.blade.php ENDPATH**/ ?>