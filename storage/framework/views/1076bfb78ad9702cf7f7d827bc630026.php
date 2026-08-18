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
        <section class="panel finance-history-panel">
            <div class="panel-header">
                <div>
                    <h2>Historial</h2>
                    <p class="fine-print">Finanzas conserva aqui las ordenes pagadas, rechazadas o canceladas.</p>
                </div>
                <div class="item-actions">
                    <a class="button ghost" href="<?php echo e(route('reports.download', 'finance-history-items-excel')); ?>">Detalle por partida</a>

                </div>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                                                <tr>
                            <th># OC</th>
                            <th>Fecha de pago</th>
                            <th>Comprador</th>
                            <th>Proveedor</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Pago</th>
                            <th>Fecha de pago</th>
                            <th>Recepcion</th>
                            <th>Almacen receptor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $lastReceipt = $order->receipts->sortByDesc('received_on')->first();
                                $ordered = $order->items->sum('quantity');
                                $received = $order->items->sum(fn ($item) => $item->receiptItems->sum('received_quantity'));
                                $receiptText = \App\Support\UiStatus::receipt($order->receipt_status, 'finance');
                                $receiptDetail = $lastReceipt
                                    ? "{$lastReceipt->invoice_number} - {$lastReceipt->received_on?->format('d/m/Y')} - Recibido " . number_format((float) $received, 0) . ' de ' . number_format((float) $ordered, 0)
                                    : null;
                            ?>
                            <tr>
                                <td>
                                    <strong>
                                        <a href="<?php echo e(route('finance.orders.print', $order)); ?>" target="_blank"><?php echo e($order->folio); ?></a>
                                    </strong>
                                </td>
                                                                <td><?php echo e(($order->created_on ?? $order->created_at)?->format('d/m/Y') ?? 'Sin fecha'); ?></td>
                                <td><?php echo e($order->buyer->name); ?></td>
                                <td><?php echo e($order->provider->business_name); ?></td>
                                <td>$<?php echo e(number_format((float) $order->total, 0)); ?></td>
                                <td>
                                    <?php if($order->status === 'paid' && $order->payment): ?>
                                        <a class="status <?php echo e(\App\Support\UiStatus::purchaseOrderClass($order->status, 'finance')); ?>" href="<?php echo e(route('finance.orders.payment-receipt', $order)); ?>" target="_blank" title="Descargar comprobante de pago">
                                            <?php echo e(\App\Support\UiStatus::purchaseOrder($order->status, 'finance')); ?>

                                        </a>
                                    <?php elseif($order->status === 'rejected'): ?>
                                        <details class="rejection-popover">
                                            <summary class="status <?php echo e(\App\Support\UiStatus::purchaseOrderClass($order->status, 'finance')); ?>">
                                                <?php echo e(\App\Support\UiStatus::purchaseOrder($order->status, 'finance')); ?>

                                            </summary>
                                            <div class="rejection-popover-panel">
                                                <button class="rejection-popover-close" type="button" aria-label="Cerrar motivo">x</button>
                                                <strong>Motivo del rechazo</strong>
                                                <p><?php echo e($order->rejection_reason ?: 'Sin motivo registrado.'); ?></p>
                                            </div>
                                        </details>
                                    <?php else: ?>
                                        <span class="status <?php echo e(\App\Support\UiStatus::purchaseOrderClass($order->status, 'finance')); ?>">
                                            <?php echo e(\App\Support\UiStatus::purchaseOrder($order->status, 'finance')); ?>

                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($order->payment): ?>
                                        <a class="attachment-pill" href="<?php echo e(route('finance.orders.payment-receipt', $order)); ?>" target="_blank"><span>Adjunto</span><?php echo e($order->payment->original_name); ?></a>
                                        <?php if($order->status === 'paid'): ?>
                                            <form class="replace-payment-form" method="POST" action="<?php echo e(route('finance.orders.payment-receipt.replace', $order)); ?>" enctype="multipart/form-data">
                                                <?php echo csrf_field(); ?>
                                                <input id="payment-file-<?php echo e($order->id); ?>" name="payment_file" type="file" required>
                                                <button class="button ghost small" type="submit">Cambiar comprobante</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        Sin pago
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo e($order->history_event_date?->format('d/m/Y') ?? 'Sin fecha'); ?>

                                    <small class="fine-print"><?php echo e($order->history_event_label); ?></small>
                                </td>
                                <td>
                                    <?php echo e($receiptText); ?>

                                    <?php if($receiptDetail): ?>
                                        <small class="fine-print"><?php echo e($receiptDetail); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($order->warehouse ?: 'Sin almacen asignado'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="10">No hay historial para mostrar.</td>
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
    <script>
        document.body.dataset.generalExportReady = 'true';
    </script>
    <style>
        .finance-history-panel th:nth-child(2),
        .finance-history-panel td:nth-child(2) {
            min-width: 130px;
            white-space: nowrap;
        }

        .finance-history-panel th:nth-child(3),
        .finance-history-panel td:nth-child(3) {
            min-width: 180px;
        }
    </style>

    <style>
        .replace-payment-form { margin-top: 8px; display: grid; gap: 6px; max-width: 320px; }
        .replace-payment-form input[type=file] { width: 100%; max-width: 300px; font-size: .78rem; }
        .replace-payment-form .button { justify-self: start; }
        .rejection-popover { position: relative; display: inline-block; }
        .rejection-popover summary { cursor: pointer; list-style: none; }
        .rejection-popover summary::-webkit-details-marker { display: none; }
        .rejection-popover summary::after { content: 'v'; margin-left: 4px; opacity: .7; }
        .rejection-popover[open] summary::after { content: '^'; }
        .rejection-popover-panel { position: absolute; z-index: 60; top: calc(100% + 8px); left: 0; width: 260px; padding: 12px 34px 12px 12px; border: 1px solid #f1b8b4; border-radius: 8px; background: #fff; box-shadow: 0 14px 32px rgba(35, 48, 73, .22); color: #233049; }
        .rejection-popover-panel strong { display: block; margin-bottom: 6px; color: #b42318; font-size: .82rem; }
        .rejection-popover-panel p { margin: 0; font-size: .86rem; line-height: 1.35; white-space: normal; }
        .rejection-popover-close { position: absolute; top: 8px; right: 8px; width: 22px; height: 22px; border: 1px solid #f1b8b4; border-radius: 999px; background: #fdecec; color: #b42318; font-weight: 900; cursor: pointer; line-height: 1; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.rejection-popover-close').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    button.closest('.rejection-popover')?.removeAttribute('open');
                });
            });

            document.addEventListener('click', (event) => {
                document.querySelectorAll('.rejection-popover[open]').forEach((popover) => {
                    if (!popover.contains(event.target)) {
                        popover.removeAttribute('open');
                    }
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\finance\orders\history.blade.php ENDPATH**/ ?>