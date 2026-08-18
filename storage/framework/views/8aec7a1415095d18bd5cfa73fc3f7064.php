<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Copia de orden original']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Copia de orden original']); ?>
        <form class="panel" method="POST" action="<?php echo e(route('inventory.orders.receipt.store', $order)); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="panel-header">
                <div>
                    <h2>Copia de la orden original <?php echo e($order->folio); ?></h2>
                    <p class="fine-print">Las recepciones anteriores aparecen debajo de cada partida. Captura solo la cantidad recibida ahora.</p>
                </div>
                <a class="button ghost" href="<?php echo e(route('inventory.orders.index')); ?>">Volver</a>
            </div>

            <section class="panel" style="box-shadow:none">
                <div class="grid-3">
                    <div>
                        <span class="fine-print">Comprador</span>
                        <strong><?php echo e($order->buyer->name); ?></strong>
                    </div>
                    <div>
                        <span class="fine-print">Empresa</span>
                        <strong><?php echo e($order->company->name); ?></strong>
                    </div>
                    <div>
                        <span class="fine-print">Proveedor</span>
                        <strong><?php echo e($order->provider->business_name); ?></strong>
                    </div>
                </div>

                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Articulo / registro</th>
                                <th>Cantidad OC</th>
                                <th>Recibido previo</th>
                                <th>Restante</th>
                                <th>Nueva recepcion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $progress; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><strong><?php echo e($row['item']->article); ?></strong></td>
                                    <td><?php echo e(number_format($row['ordered'], 2)); ?></td>
                                    <td><?php echo e(number_format($row['received'], 2)); ?></td>
                                    <td><?php echo e(number_format($row['remaining'], 2)); ?></td>
                                    <td>
                                        <input type="hidden" name="items[<?php echo e($index); ?>][purchase_order_item_id]" value="<?php echo e($row['item']->id); ?>">
                                        <input name="items[<?php echo e($index); ?>][received_quantity]" type="number" min="0" max="<?php echo e($row['remaining']); ?>" step="0.01" value="0" required>
                                    </td>
                                </tr>
                                <?php $__currentLoopData = $row['item']->receiptItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $receiptItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="fine-print">Recepcion previa: <?php echo e($receiptItem->receipt?->invoice_number); ?></td>
                                        <td></td>
                                        <td><?php echo e(number_format((float) $receiptItem->received_quantity, 2)); ?></td>
                                        <td colspan="2" class="fine-print">
                                            <?php echo e($receiptItem->receipt?->received_on?->format('d/m/Y')); ?>

                                            · <?php echo e($receiptItem->receipt?->original_name); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="grid-3">
                <label>
                    Documento de esta recepcion
                    <input name="receipt_file" type="file" required>
                </label>
                <label>
                    Numero de factura
                    <input name="invoice_number" placeholder="F-00000" required>
                </label>
                <label>
                    Fecha de recepcion
                    <input name="received_on" type="date" value="<?php echo e(now()->toDateString()); ?>" required>
                </label>
            </div>

            <div class="form-actions">
                <span class="fine-print">Si todas las cantidades quedan cubiertas, la OC pasara al historial de Inventarios.</span>
                <button class="button primary" type="submit">Guardar recepcion</button>
            </div>
        </form>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\inventory\orders\receipt.blade.php ENDPATH**/ ?>