<?php
    $isHistory = $panel === 'history';
?>

<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => $isHistory ? 'Historial de OR' : 'OR pendientes']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isHistory ? 'Historial de OR' : 'OR pendientes')]); ?>
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2><?php echo e($isHistory ? 'OR Historial' : 'OR Pendientes'); ?></h2>
                    <p class="fine-print"><?php echo e($isHistory ? 'Reembolsos pagados o rechazados.' : 'Reembolsos pendientes de autorizacion o pago.'); ?></p>
                </div>
                <div class="item-actions">
                    <form class="toolbar" method="GET" action="<?php echo e(route('buyer.reimbursement-orders.index')); ?>">
                        <?php if($isHistory): ?>
                            <input type="hidden" name="panel" value="history">
                        <?php endif; ?>
                        <input name="q" value="<?php echo e($query); ?>" placeholder="Buscar OR...">
                    </form>
                    <?php if(! $isHistory): ?>
                        <a class="button primary" href="<?php echo e(route('buyer.reimbursement-orders.create')); ?>">Nueva OR</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha envio</th>
                            <th>Empresa</th>
                            <th>Proveedor</th>
                            <th>Cotizacion</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Soporte producto/servicio</th>
                            <th>Pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><strong><?php echo e($order->folio); ?></strong></td>
                                <td><?php echo e($order->created_on?->format('d/m/Y')); ?></td>
                                <td><?php echo e($order->company->name); ?></td>
                                <td><?php echo e($order->provider); ?></td>
                                <td><a class="attachment-pill" href="<?php echo e(route('buyer.reimbursement-orders.quote', $order)); ?>" target="_blank"><span>Ver</span><?php echo e($order->quote_original_name); ?></a></td>
                                <td>$<?php echo e(number_format((float) $order->amount, 2)); ?></td>
                                <td><span class="status <?php echo e(\App\Support\UiStatus::workflowClass($order->status)); ?>"><?php echo e(\App\Support\UiStatus::reimbursementOrder($order->status, 'buyer')); ?></span></td>
                                <td>
                                    <?php if($order->support_file_path): ?>
                                        <a class="attachment-pill" href="<?php echo e(route('buyer.reimbursement-orders.support', $order)); ?>" target="_blank"><span>Soporte</span><?php echo e($order->support_original_name); ?></a>
                                    <?php elseif(! $isHistory): ?>
                                        <form class="stack" method="POST" action="<?php echo e(route('buyer.reimbursement-orders.support.store', $order)); ?>" enctype="multipart/form-data">
                                            <?php echo csrf_field(); ?>
                                            <span class="status pending">Pendiente</span>
                                            <input name="support_file" type="file" accept=".pdf,.jpg,.jpeg,.png" required>
                                            <button class="button ghost small" type="submit">Subir soporte</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="status pending">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($order->payment_file_path): ?>
                                        <a class="attachment-pill" href="<?php echo e(route('buyer.reimbursement-orders.payment', $order)); ?>" target="_blank"><span>Pago</span><?php echo e($order->payment_original_name); ?></a>
                                    <?php else: ?>
                                        <span class="fine-print">Sin pago</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="9">No hay OR para mostrar.</td></tr>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\buyer\reimbursement-orders\index.blade.php ENDPATH**/ ?>