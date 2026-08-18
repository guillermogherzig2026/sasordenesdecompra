<?php
    $digitalUrl = $order->remission_token ? route('supply-orders.digital.show', $order->remission_token) : null;
    $qrUrl = $digitalUrl ? 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data='.rawurlencode($digitalUrl) : null;
?>

<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Remision de entrega']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Remision de entrega']); ?>
        <section class="panel remission-print">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Remision de entrega</p>
                    <h2><?php echo e($order->formatted_delivery_remission_number ?: 'Remision pendiente'); ?></h2>
                    <p class="fine-print">ID OS <?php echo e($order->supply_consecutive); ?></p>
                    <p class="fine-print">OS <?php echo e($order->folio); ?> · <?php echo e(\App\Support\UiStatus::supplyOrder($order->status, 'inventory')); ?></p>
                </div>
                <button class="button ghost no-print" onclick="window.print()" type="button">Imprimir</button>
            </div>

            <div class="grid-4">
                <article class="metric-card"><span>Fecha salida</span><strong style="font-size:1rem"><?php echo e($order->delivered_on?->format('d/m/Y') ?: 'Pendiente'); ?></strong></article>
                <article class="metric-card"><span>Fecha recepcion</span><strong style="font-size:1rem"><?php echo e($order->received_on?->format('d/m/Y') ?: 'Pendiente'); ?></strong></article>
                <article class="metric-card"><span>Almacen origen</span><strong style="font-size:1rem"><?php echo e($order->warehouse_from); ?></strong></article>
                <article class="metric-card"><span>Almacen destino</span><strong style="font-size:1rem"><?php echo e($order->warehouse_to ?: 'Sin destino capturado'); ?></strong></article>
            </div>

            <div class="grid-3">
                <div class="panel">
                    <strong>Usuario solicitante</strong>
                    <p><?php echo e($order->requester->name); ?></p>
                    <p class="fine-print"><?php echo e($order->requester->email); ?></p>
                    <p class="fine-print">Rol: <?php echo e($order->requester->role === 'buyer' ? $order->requester->buyerSubroleLabel() : ucfirst($order->requester->role)); ?></p>
                </div>
                <div class="panel">
                    <strong>Empresa receptora</strong>
                    <p><?php echo e($order->company->name); ?></p>
                    <p class="fine-print">RFC: <?php echo e($order->company->rfc ?: 'Sin RFC'); ?></p>
                    <p class="fine-print"><?php echo e($order->company->address ?: 'Sin direccion capturada'); ?></p>
                </div>
                <div class="panel">
                    <strong>Confirmacion digital</strong>
                    <?php if($qrUrl): ?>
                        <img src="<?php echo e($qrUrl); ?>" alt="QR de remision <?php echo e($order->formatted_delivery_remission_number); ?>" width="180" height="180" style="width:180px;height:180px">
                        <p class="fine-print">Escanea para abrir el formato digital y recibir mercancia.</p>
                        <p class="fine-print" style="word-break:break-all"><?php echo e($digitalUrl); ?></p>
                    <?php else: ?>
                        <p class="fine-print">El QR se generara cuando exista una remision.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Descripcion</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Precio unitario</th>
                            <th>Precio total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($item->article); ?></strong>
                                    <?php if($item->catalogItem?->sku): ?>
                                        <small class="fine-print">SKU <?php echo e($item->catalogItem->sku); ?></small>
                                    <?php endif; ?>
                                    <?php if($item->catalogItem?->description): ?>
                                        <small class="fine-print"><?php echo e($item->catalogItem->description); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e(number_format((float) $item->quantity, 2)); ?></td>
                                <td><?php echo e($item->catalogItem?->unit ?: 'unidad'); ?></td>
                                <td>$<?php echo e(number_format((float) $item->unit_cost, 2)); ?></td>
                                <td>$<?php echo e(number_format((float) $item->line_total, 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" style="text-align:right">Total</th>
                            <th>$<?php echo e(number_format((float) $order->total, 2)); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="grid-3">
                <div class="panel"><strong>Entrega</strong><p><?php echo e($order->deliveredBy?->name ?: 'Inventarios'); ?></p></div>
                <div class="panel"><strong>Recibe</strong><p><?php echo e($order->received_by_name ?: ($order->warehouse_to ?: 'Pendiente de recepcion')); ?></p></div>
                <div class="panel"><strong>Notas</strong><p><?php echo e($order->notes ?: $order->company->purchase_order_notes ?: 'Sin notas adicionales.'); ?></p></div>
            </div>
        </section>

        <style>
            @media print {
                .sidebar, .topbar, .no-print, .table-export-actions, .column-sort-button, .column-filter { display: none !important; }
                .app-shell { display: block; }
                .content-shell, .view { min-height: auto; padding: 0; }
                .panel { box-shadow: none; }
                .remission-print { border: 0; }
                body { background: #fff; padding: 0; }
            }
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\inventory\supply-orders\remission.blade.php ENDPATH**/ ?>