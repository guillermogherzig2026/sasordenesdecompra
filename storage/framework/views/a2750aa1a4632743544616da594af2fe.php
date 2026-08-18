<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Historial']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Historial']); ?>
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Historial</h2>
                    <p class="fine-print">Ordenes pagadas que ya fueron recibidas en su totalidad.</p>
                </div>
                <a class="button ghost" href="<?php echo e(route('reports.download', 'inventory-history')); ?>">Exportar CSV</a>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th># OC</th>
                            <th>Fecha envio</th>
                            <th>Comprador</th>
                            <th>Proveedor</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Pago</th>
                            <th>Fecha pago</th>
                            <th>Recepcion</th>
                            <th>Almacen receptor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $lastReceipt = $order->receipts->sortByDesc('received_on')->first();
                                $ordered = (float) $order->items->sum('quantity');
                                $received = (float) $order->items->sum(fn ($item) => $item->receiptItems->sum('received_quantity'));
                            ?>
                            <tr>
                                <td><strong><?php echo e($order->folio); ?></strong></td>
                                <td><?php echo e(($order->created_on ?? $order->created_at)?->format('d/m/Y') ?? 'Sin fecha'); ?></td>
                                <td><?php echo e($order->buyer->name); ?></td>
                                <td><?php echo e($order->provider->business_name); ?></td>
                                <td>$<?php echo e(number_format((float) $order->total, 0)); ?></td>
                                <td>
                                    <details class="status-menu">
                                        <summary class="status <?php echo e(\App\Support\UiStatus::receiptClass($order->receipt_status, 'inventory')); ?>"><?php echo e(\App\Support\UiStatus::receipt($order->receipt_status, 'inventory')); ?></summary>
                                        <div class="status-menu-panel">
                                            <a class="button ghost small" href="<?php echo e(route('inventory.orders.print', $order)); ?>" target="_blank">PDF</a>
                                        </div>
                                    </details>
                                </td>
                                <td>
                                    <?php if($order->payment?->original_name): ?>
                                        <a class="attachment-pill" href="<?php echo e(route('inventory.orders.payment-receipt', $order)); ?>" target="_blank" rel="noopener"><span>Adjunto</span><?php echo e($order->payment->original_name); ?></a>
                                    <?php else: ?>
                                        Sin pago
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($order->payment?->paid_on?->format('d/m/Y') ?? 'Sin pago'); ?></td>
                                <td>
                                    <?php echo e(\App\Support\UiStatus::receipt($order->receipt_status, 'inventory')); ?>

                                    <?php if($lastReceipt): ?>
                                        <small class="fine-print"><?php echo e($lastReceipt->invoice_number); ?> - <?php echo e($lastReceipt->received_on?->format('d/m/Y')); ?> - Recibido <?php echo e(number_format($received, 0)); ?> de <?php echo e(number_format($ordered, 0)); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($order->warehouse ?: 'Sin almacen asignado'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="10">No hay registros en historial.</td>
                            </tr>
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


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\inventory\orders\history.blade.php ENDPATH**/ ?>