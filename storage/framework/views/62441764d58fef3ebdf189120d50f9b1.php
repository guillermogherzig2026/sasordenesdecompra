<?php
    $role = auth()->user()->role;
    $reports = match ($role) {
        'finance' => [
            ['Ordenes vigentes', 'finance-active'],
            ['Historial', 'finance-history'],
            ['Auditoria completa', 'audit'],
            ['Servicios y pagos', 'services-payments'],
            ['Catalogo de servicios', 'services-catalog'],
            ['Proveedores', 'providers'],
            ['Empresas', 'companies'],
        ],
        'buyer' => [
            ['Mis ordenes vigentes', 'buyer-active'],
            ['Historial', 'buyer-history'],
            ['Auditoria', 'audit'],
        ],
        'inventory' => [
            ['OC pagadas pendientes', 'inventory-paid'],
            ['Historial', 'inventory-history'],
            ['Auditoria', 'audit'],
        ],
        'services', 'administrative_assistant' => [
            ['Catalogo completo de servicios', 'services-catalog'],
            ['Pagos de servicios', 'services-payments'],
        ],
        default => [],
    };
?>

<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Reportes']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Reportes']); ?>
        <section class="panel">
            <div>
                <h2>Exportaciones</h2>
                <p class="fine-print">Los reportes se descargan en CSV compatible con Excel, igual que el prototipo.</p>
            </div>
            <div class="grid-3">
                <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $type]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a class="button ghost" href="<?php echo e(route('reports.download', $type)); ?>"><?php echo e($label); ?></a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\reports\index.blade.php ENDPATH**/ ?>