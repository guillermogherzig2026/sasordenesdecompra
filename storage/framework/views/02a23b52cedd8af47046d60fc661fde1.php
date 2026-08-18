<?php
    $isHistory = $panel === 'history';
?>

<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => $isHistory ? 'Historial de OS' : 'OS pendientes']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isHistory ? 'Historial de OS' : 'OS pendientes')]); ?>
        <section class="panel supply-orders-panel">
            <div class="panel-header">
                <div>
                    <h2><?php echo e($isHistory ? 'OS Historial' : 'OS Pendientes'); ?></h2>
                    <p class="fine-print"><?php echo e($isHistory ? 'Ordenes recibidas, rechazadas o canceladas.' : 'Solicitudes enviadas, autorizadas o con remision pendiente de recepcion.'); ?></p>
                </div>
                <div class="item-actions">
                    <form class="toolbar" method="GET" action="<?php echo e(route('buyer.supply-orders.index')); ?>">
                        <?php if($isHistory): ?>
                            <input type="hidden" name="panel" value="history">
                        <?php endif; ?>
                        <input name="q" value="<?php echo e($query); ?>" placeholder="Buscar OS...">
                    </form>
                    <a class="button ghost" href="<?php echo e(route('reports.download', 'supply-orders-excel')); ?>">Exportar Excel</a>
                    <?php if(! $isHistory): ?>
                        <a class="button primary" href="<?php echo e(route('buyer.supply-orders.create')); ?>">Nueva OS</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-scroll supply-orders-table-scroll">
                <table class="supply-orders-table">
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
                                    <small class="fine-print"><?php echo e(\App\Support\UiStatus::supplyOrder($order->status, 'buyer')); ?></small>
                                </td>
                                <td><?php echo e($order->created_on?->format('d/m/Y')); ?></td>
                                <td><?php echo e($order->requester?->name ?: 'Usuario'); ?></td>
                                <td><?php echo e($order->company->name); ?></td>
                                <td><?php echo e(number_format((float) $totalQuantity, 2)); ?></td>
                                <td><?php echo e($unitLabel); ?></td>
                                <td>
                                    <?php if (isset($component)) { $__componentOriginal036e9cea2ea3ff109a40294db5da929d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal036e9cea2ea3ff109a40294db5da929d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.supply-order-items-dialog','data' => ['order' => $order,'dialogId' => 'buyer-supply-detail-'.$order->id]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('supply-order-items-dialog'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['order' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($order),'dialog-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('buyer-supply-detail-'.$order->id)]); ?>
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
                                        <a class="button ghost small" href="<?php echo e(route('buyer.supply-orders.remission', $order)); ?>" target="_blank">Remision</a>
                                        <div class="fine-print"><?php echo e($order->formatted_delivery_remission_number); ?></div>
                                        <?php if($order->status === 'remitted'): ?>
                                            <div class="fine-print">Pendiente de recibir</div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="button ghost small disabled" aria-disabled="true">Remision</span>
                                        <div class="fine-print">Pendiente</div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="11">No hay OS para mostrar.</td></tr>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\buyer\supply-orders\index.blade.php ENDPATH**/ ?>