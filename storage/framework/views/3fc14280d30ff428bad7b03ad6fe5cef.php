<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => $label]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($label)]); ?>
        <?php if($showProjectCarousel): ?>
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
                    <a class="button ghost small" href="<?php echo e(route('construction.dashboard')); ?>">Atras</a>
                </div>

                <div class="construction-carousel-shell">
                    <button class="construction-carousel-nav" type="button" data-carousel-prev aria-label="Anterior">&lt;</button>

                    <div class="construction-carousel-track" data-construction-carousel-track>
                        <?php if($showMaterialsCatalogButton ?? false): ?>
                            <a
                                class="construction-project-tile construction-project-tile-catalog"
                                href="#materials-explosion-catalog"
                                data-carousel-option
                                data-materials-catalog-select
                                aria-label="Catalogo de explosion de insumos"
                                aria-pressed="false"
                            >
                                <span class="construction-project-avatar">CAT</span>
                                <span class="construction-project-key">Catalogo general</span>
                                <strong class="construction-project-name">Catalogo de explosion de insumos</strong>
                                <span class="construction-project-status"><span></span>Informacion general</span>
                            </a>
                        <?php endif; ?>

                        <?php $__empty_1 = true; $__currentLoopData = $activeProjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <button class="construction-project-tile <?php echo e($loop->first ? 'active' : ''); ?>" type="button" data-carousel-option data-project-select aria-pressed="<?php echo e($loop->first ? 'true' : 'false'); ?>">
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
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <button class="construction-project-tile active" type="button" disabled>
                                <span class="construction-project-avatar">OB</span>
                                <span class="construction-project-key">Sin obras</span>
                                <strong class="construction-project-name">No hay obras visibles</strong>
                                <span class="construction-project-status"><span></span>Pendiente</span>
                            </button>
                        <?php endif; ?>
                    </div>

                    <button class="construction-carousel-nav" type="button" data-carousel-next aria-label="Siguiente">&gt;</button>
                </div>
            </section>
        <?php endif; ?>

        <?php if($showGeneratorPanel ?? false): ?>
            <?php echo $__env->make('construction.partials.generator-quantification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php elseif($showMaterialsCatalogButton ?? false): ?>
            <?php echo $__env->make('construction.partials.materials-explosion-catalog', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php else: ?>
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2><?php echo e($label); ?></h2>
                        <p class="fine-print">Esta seccion venia como menu del codigo de Software Obras y quedo conectada dentro de Administracion de obra.</p>
                    </div>
                    <a class="button ghost" href="<?php echo e(route('construction.dashboard')); ?>">Panel de obra</a>
                </div>
                <p>La pantalla especifica de <strong><?php echo e($label); ?></strong> esta lista como entrada de menu y queda preparada para desarrollar su CRUD en la siguiente fase.</p>
            </section>
        <?php endif; ?>

        <?php if($showProjectCarousel): ?>
            <script>
                document.querySelectorAll('[data-construction-carousel]').forEach((carousel) => {
                    const track = carousel.querySelector('[data-construction-carousel-track]');
                    const scrollByTile = () => Math.max(220, Math.round((track?.clientWidth || 260) * 0.72));

                    carousel.querySelector('[data-carousel-prev]')?.addEventListener('click', () => {
                        track?.scrollBy({ left: -scrollByTile(), behavior: 'smooth' });
                    });

                    carousel.querySelector('[data-carousel-next]')?.addEventListener('click', () => {
                        track?.scrollBy({ left: scrollByTile(), behavior: 'smooth' });
                    });

                    carousel.querySelectorAll('[data-carousel-option]').forEach((button) => {
                        button.addEventListener('click', () => {
                            carousel.querySelectorAll('[data-carousel-option]').forEach((item) => {
                                item.classList.toggle('active', item === button);
                                item.setAttribute('aria-pressed', item === button ? 'true' : 'false');
                            });
                        });
                    });
                });
            </script>
        <?php endif; ?>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\construction\placeholder.blade.php ENDPATH**/ ?>