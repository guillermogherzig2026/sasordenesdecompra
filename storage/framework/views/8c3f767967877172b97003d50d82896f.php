<?php $__env->startSection('body'); ?>
    <?php ($periodAmount = (float) ($receipt?->amount ?? $service->cost)); ?>

    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Cargar factura de servicio']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Cargar factura de servicio']); ?>
        <form class="panel" method="POST" action="<?php echo e(route('services.receipt.store', [$service, $occurrence['due_date']])); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="panel-header">
                <div>
                    <h2><?php echo e($service->folio); ?> · <?php echo e($service->service_name); ?></h2>
                    <p class="fine-print">Este archivo sera el soporte para que Finanzas realice el pago. No cambia el estatus a pagado.</p>
                </div>
                <a class="button ghost" href="<?php echo e(route('services.months')); ?>">Volver</a>
            </div>

            <section class="panel" style="box-shadow:none">
                <div class="grid-3">
                    <div><span class="fine-print">Proveedor</span><strong><?php echo e($service->provider); ?></strong></div>
                    <div><span class="fine-print">Periodo</span><strong><?php echo e(\Illuminate\Support\Carbon::parse($occurrence['period_start'])->format('d/m/Y')); ?> al <?php echo e(\Illuminate\Support\Carbon::parse($occurrence['due_date'])->format('d/m/Y')); ?></strong></div>
                    <div><span class="fine-print">Monto</span><strong>$<?php echo e(number_format($periodAmount, 2)); ?></strong></div>
                </div>
            </section>

            <div class="grid-2">
                <label>
                    Factura
                    <input name="support_file" type="file" required>
                </label>
                <label>
                    Fecha de factura
                    <input name="support_on" type="date" value="<?php echo e(now()->toDateString()); ?>" required>
                </label>
            </div>

            <?php if($receipt?->support_original_name): ?>
                <p class="fine-print">Archivo actual: <?php echo e($receipt->support_original_name); ?></p>
            <?php endif; ?>

            <div class="form-actions">
                <span class="fine-print">Finanzas vera el soporte en Pago Servicios.</span>
                <button class="button primary" type="submit">Guardar factura</button>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\services\receipt.blade.php ENDPATH**/ ?>