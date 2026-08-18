<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Historial de pagos']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Historial de pagos']); ?>
        <?php
            $activeProjects = $projects->where('status', 'En ejecucion')->values();

            if ($activeProjects->isEmpty()) {
                $activeProjects = $projects->values();
            }
        ?>

        <section class="panel construction-carousel-panel" data-construction-carousel>
            <div class="construction-carousel-header">
                <div class="construction-carousel-title">
                    <span class="construction-carousel-count"><?php echo e($activeProjects->count()); ?></span>
                    <h2>Obras activas</h2>
                </div>
                <a class="button ghost small" href="<?php echo e(route('construction.placeholder', ['section' => 'mano-obra', 'project' => $selectedProjectId])); ?>">Atras</a>
            </div>

            <div class="construction-carousel-shell">
                <button class="construction-carousel-nav" type="button" data-carousel-prev aria-label="Anterior">&lt;</button>
                <div class="construction-carousel-track" data-construction-carousel-track>
                    <?php $__empty_1 = true; $__currentLoopData = $activeProjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a
                            class="construction-project-tile <?php echo e($project->id === $selectedProjectId ? 'active' : ''); ?>"
                            href="<?php echo e(route('construction.placeholder', ['section' => 'pagos', 'project' => $project->id])); ?>"
                            aria-current="<?php echo e($project->id === $selectedProjectId ? 'page' : 'false'); ?>"
                        >
                            <span class="construction-project-avatar">
                                <?php if($project->photo_path): ?>
                                    <img src="<?php echo e($project->photo_path); ?>" alt="">
                                <?php else: ?>
                                    <?php echo e(substr($project->project_key, -2)); ?>

                                <?php endif; ?>
                            </span>
                            <span class="construction-project-key"><?php echo e($project->project_key); ?></span>
                            <strong class="construction-project-name"><?php echo e($project->name); ?></strong>
                            <span class="construction-project-status"><span></span><?php echo e($project->status); ?></span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <span class="construction-project-tile">
                            <strong class="construction-project-name">No hay obras visibles</strong>
                        </span>
                    <?php endif; ?>
                </div>
                <button class="construction-carousel-nav" type="button" data-carousel-next aria-label="Siguiente">&gt;</button>
            </div>
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Historial de pagos</h2>
                    <p class="fine-print">Nominas y estimaciones pagadas de la obra seleccionada.</p>
                </div>
            </div>

            <?php echo $__env->make('construction.partials.payment-order-table', [
                'paymentOrders' => $paymentOrders,
                'financeContext' => false,
                'allowPaymentUpload' => false,
                'emptyMessage' => 'No hay pagos realizados para esta obra.',
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </section>

        <script>
            (() => {
                document.querySelectorAll('[data-construction-carousel]').forEach((carousel) => {
                    const track = carousel.querySelector('[data-construction-carousel-track]');
                    const amount = () => Math.max(220, Math.round((track?.clientWidth || 260) * 0.72));
                    carousel.querySelector('[data-carousel-prev]')?.addEventListener('click', () => track?.scrollBy({ left: -amount(), behavior: 'smooth' }));
                    carousel.querySelector('[data-carousel-next]')?.addEventListener('click', () => track?.scrollBy({ left: amount(), behavior: 'smooth' }));
                });
            })();
        </script>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views/construction/payment-history.blade.php ENDPATH**/ ?>