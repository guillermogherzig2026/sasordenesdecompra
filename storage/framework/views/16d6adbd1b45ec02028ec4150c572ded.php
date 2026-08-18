<?php $__env->startSection('body'); ?>
    <?php ($periodAmount = (float) ($receipt?->amount ?? $service->cost)); ?>

    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Registrar pago de servicio']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Registrar pago de servicio']); ?>
        <form class="panel" method="POST" action="<?php echo e(route('finance.services.payment.store', [$service, $occurrence['due_date']])); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="panel-header">
                <div>
                    <h2><?php echo e($service->folio); ?> · <?php echo e($service->service_name); ?></h2>
                    <p class="fine-print">Adjunta el comprobante para marcar este periodo como pagado.</p>
                </div>
                <a class="button ghost" href="<?php echo e(route('finance.services.index')); ?>">Volver</a>
            </div>

            <section class="panel" style="box-shadow:none">
                <div class="grid-3">
                    <div><span class="fine-print">Proveedor</span><strong><?php echo e($service->provider); ?></strong></div>
                    <div><span class="fine-print">Periodo</span><strong><?php echo e(\Illuminate\Support\Carbon::parse($occurrence['period_start'])->format('d/m/Y')); ?> al <?php echo e(\Illuminate\Support\Carbon::parse($occurrence['due_date'])->format('d/m/Y')); ?></strong></div>
                    <div><span class="fine-print">Monto</span><strong>$<?php echo e(number_format($periodAmount, 2)); ?></strong></div>
                </div>
                <p class="fine-print">Factura/recibo cargado por Asistente Administrativo: <?php echo e($receipt?->support_original_name ?? 'Pendiente'); ?></p>
            </section>

            <div class="grid-2">
                <label>
                    Comprobante de pago
                    <input name="payment_file" type="file" required>
                </label>
                <label>
                    Fecha de pago
                    <input name="payment_paid_on" type="date" value="<?php echo e(now()->toDateString()); ?>" required>
                </label>
            </div>

            <div class="form-actions">
                <span class="fine-print">Al guardar, el periodo quedara pagado y bloqueado para el Asistente Administrativo.</span>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\finance\services\payment.blade.php ENDPATH**/ ?>