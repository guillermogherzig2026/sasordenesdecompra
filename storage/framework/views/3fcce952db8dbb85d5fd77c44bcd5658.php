<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'OR Vigentes']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'OR Vigentes']); ?>
        <section class="panel finance-active-panel">
            <div class="panel-header">
                <div>
                    <h2>OR Vigentes</h2>
                    <p class="fine-print">Reembolsos pendientes de autorizacion o pago.</p>
                </div>
                <form class="toolbar" method="GET" action="<?php echo e(route('finance.reimbursement-orders.active')); ?>">
                    <input name="q" value="<?php echo e($query); ?>" placeholder="Buscar OR...">
                </form>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha de envio</th>
                            <th>Usuario</th>
                            <th>Empresa</th>
                            <th>Proveedor</th>
                            <th>Cotizacion</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Soporte del producto o servicio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><strong><?php echo e($order->folio); ?></strong></td>
                                <td><?php echo e($order->created_on?->format('d/m/Y')); ?></td>
                                <td><?php echo e($order->requester->name); ?></td>
                                <td><?php echo e($order->company->name); ?></td>
                                <td><?php echo e($order->provider); ?></td>
                                <td><a class="attachment-pill" href="<?php echo e(route('finance.reimbursement-orders.quote', $order)); ?>" target="_blank"><span>Ver</span><?php echo e($order->quote_original_name); ?></a></td>
                                <td>$<?php echo e(number_format((float) $order->amount, 2)); ?></td>
                                <td>
                                    <details class="status-menu">
                                        <summary class="status <?php echo e(\App\Support\UiStatus::workflowClass($order->status)); ?>"><?php echo e(\App\Support\UiStatus::reimbursementOrder($order->status)); ?></summary>
                                        <div class="status-menu-panel reimbursement-status-panel">
                                            <?php if($order->status === 'sent'): ?>
                                                <form class="inline-form" method="POST" action="<?php echo e(route('finance.reimbursement-orders.approve', $order)); ?>">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PATCH'); ?>
                                                    <button class="button primary small" type="submit">Autorizada</button>
                                                </form>
                                                <form class="inline-form" method="POST" action="<?php echo e(route('finance.reimbursement-orders.reject', $order)); ?>">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PATCH'); ?>
                                                    <button class="button danger small" type="submit">Rechazada</button>
                                                </form>
                                            <?php elseif($order->status === 'approved'): ?>
                                                <form class="stack" method="POST" action="<?php echo e(route('finance.reimbursement-orders.payment.store', $order)); ?>" enctype="multipart/form-data">
                                                    <?php echo csrf_field(); ?>
                                                    <input name="paid_on" type="date" value="<?php echo e(now()->toDateString()); ?>" required>
                                                    <input name="payment_file" type="file" required>
                                                    <button class="button primary small" type="submit">Subir pago</button>
                                                </form>
                                                <form class="inline-form" method="POST" action="<?php echo e(route('finance.reimbursement-orders.reject', $order)); ?>">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PATCH'); ?>
                                                    <button class="button danger small" type="submit">Rechazar</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </details>
                                </td>
                                <td>
                                    <?php if($order->support_file_path): ?>
                                        <a class="attachment-pill" href="<?php echo e(route('finance.reimbursement-orders.support', $order)); ?>" target="_blank"><span>Soporte</span><?php echo e($order->support_original_name); ?></a>
                                    <?php else: ?>
                                        <span class="status pending">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="9">No hay OR vigentes.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <style>
            .reimbursement-status-panel { min-width: 230px; }
            .reimbursement-status-panel input[type=file] { max-width: 210px; font-size: .78rem; }
        </style>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\finance\reimbursement-orders\active.blade.php ENDPATH**/ ?>