<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Registrar pago']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Registrar pago']); ?>
        <form class="panel" method="POST" action="<?php echo e(route('finance.orders.payment.store', $order)); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="panel-header">
                <div>
                    <h2>Registrar pago de <?php echo e($order->folio); ?></h2>
                    <p class="fine-print">Al adjuntar el comprobante, la OC cambia automaticamente a pagada y aparece en Inventarios.</p>
                </div>
                <a class="button ghost" href="<?php echo e(route('finance.orders.active')); ?>">Volver</a>
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
                                <th>Articulo</th>
                                <th>Cantidad</th>
                                <th>Precio unit.</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($item->article); ?></td>
                                    <td><?php echo e(number_format((float) $item->quantity, 2)); ?></td>
                                    <td>$<?php echo e(number_format((float) $item->unit_price, 2)); ?></td>
                                    <td>$<?php echo e(number_format((float) $item->line_total, 2)); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <strong>Total: $<?php echo e(number_format((float) $order->total, 2)); ?></strong>
            </section>

            <div class="grid-2">
                <label>
                    Archivo de pago
                    <input name="payment_file" type="file" required>
                </label>
                <label>
                    Fecha de pago
                    <input name="paid_on" type="date" value="<?php echo e(now()->toDateString()); ?>" required>
                </label>
            </div>

            <div class="form-actions">
                <span class="fine-print">El comprobante queda almacenado y visible en historial.</span>
                <button class="button primary" type="submit">Guardar pago</button>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\finance\orders\payment.blade.php ENDPATH**/ ?>