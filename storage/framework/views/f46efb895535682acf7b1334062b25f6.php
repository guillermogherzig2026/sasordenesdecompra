<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Fotos de avance']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Fotos de avance']); ?>
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Fotos de avance - <?php echo e($paymentOrder->code); ?></h2>
                    <p class="fine-print">
                        <?php echo e($paymentOrder->project?->name); ?> - <?php echo e($paymentOrder->description); ?>

                    </p>
                </div>
                <a
                    class="button ghost"
                    href="<?php echo e(route('construction.placeholder', ['section' => 'mano-obra', 'project' => $paymentOrder->construction_project_id])); ?>"
                >Volver</a>
            </div>

            <div class="payment-photo-grid">
                <?php $__currentLoopData = $photos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photoIndex => $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $photoUrl = route('construction.payment-orders.photos.file', [
                            'paymentOrder' => $paymentOrder,
                            'photoIndex' => $photoIndex,
                        ]);
                    ?>
                    <article class="payment-photo-item">
                        <a href="<?php echo e($photoUrl); ?>" target="_blank" rel="noopener">
                            <img
                                class="payment-photo-preview"
                                src="<?php echo e($photoUrl); ?>"
                                alt="Foto de avance <?php echo e($photoIndex + 1); ?> de <?php echo e($paymentOrder->code); ?>"
                            >
                        </a>
                        <div class="payment-photo-meta">
                            <strong class="payment-photo-name" title="<?php echo e($photo['name'] ?? 'Foto de avance'); ?>">
                                <?php echo e($photo['name'] ?? 'Foto de avance '.($photoIndex + 1)); ?>

                            </strong>
                            <a class="button ghost small" href="<?php echo e($photoUrl); ?>" target="_blank" rel="noopener">Ver</a>
                        </div>
                    </article>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views/construction/payment-photos.blade.php ENDPATH**/ ?>