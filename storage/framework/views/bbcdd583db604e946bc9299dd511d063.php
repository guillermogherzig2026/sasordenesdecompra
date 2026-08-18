<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Administracion de obra']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Administracion de obra']); ?>
        <?php
            $money = fn ($value) => '$'.number_format((float) $value, 2);
            $carouselProjects = $projects->where('status', 'En ejecucion')->values();

            if ($carouselProjects->isEmpty()) {
                $carouselProjects = $projects->values();
            }
        ?>

        <section class="panel construction-carousel-panel" data-construction-carousel>
            <div class="construction-carousel-header">
                <div class="construction-carousel-title">
                    <span class="construction-carousel-count"><?php echo e($summary['active']); ?></span>
                    <h2>Obras activas</h2>
                </div>
            </div>

            <div class="construction-carousel-shell">
                <button class="construction-carousel-nav" type="button" data-carousel-prev aria-label="Anterior">&lt;</button>

                <div class="construction-carousel-track" data-construction-carousel-track>
                    <a class="construction-project-tile construction-project-tile-create" href="<?php echo e(route('construction.projects.create')); ?>">
                        <span class="construction-project-add">+</span>
                        <span class="construction-project-key">Nueva</span>
                        <strong class="construction-project-name">Nueva obra</strong>
                        <span class="construction-project-status"><span></span>Registrar obra</span>
                    </a>

                    <?php $__currentLoopData = $carouselProjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a
                            class="construction-project-tile <?php echo e($loop->first ? 'active' : ''); ?>"
                            href="#project-row-<?php echo e($project->id); ?>"
                            aria-label="Ver <?php echo e($project->project_key); ?> - <?php echo e($project->name); ?> en el Panel general"
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
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <button class="construction-carousel-nav" type="button" data-carousel-next aria-label="Siguiente">&gt;</button>
            </div>
        </section>

        <section class="panel" id="panel-general-obras">
            <div class="panel-header">
                <div>
                    <h2>Panel general de obras</h2>
                    <p class="fine-print">Vista ejecutiva de obras activas, avances y pagos.</p>
                </div>
            </div>

            <div class="table-scroll">
                <table class="construction-overview-table">
                    <thead>
                        <tr>
                            <th>Obra</th>
                            <th>Cliente</th>
                            <th>Metros cuadrados construidos</th>
                            <th>Metros cuadrados vendibles o rentables</th>
                            <th>Metros cuadrados de estacionamientos</th>
                            <th>Numero de niveles</th>
                            <th>Estado</th>
                            <th>Avance fisico</th>
                            <th>Avance financiero</th>
                            <th>Por pagar</th>
                            <th class="construction-actions-column" data-no-filter data-no-sort>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $projects->sortByDesc('balance_to_pay')->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr id="project-row-<?php echo e($project->id); ?>">
                                <td>
                                    <strong><?php echo e($project->project_key); ?></strong>
                                    <br><span class="fine-print"><?php echo e($project->name); ?></span>
                                </td>
                                <td><?php echo e($project->client?->name ?? 'Sin cliente'); ?></td>
                                <td><?php echo e(number_format((float) $project->constructed_area, 2)); ?> m2</td>
                                <td><?php echo e(number_format((float) $project->sellable_rentable_area, 2)); ?> m2</td>
                                <td><?php echo e(number_format((float) $project->parking_area, 2)); ?> m2</td>
                                <td><?php echo e(number_format((int) $project->levels_count)); ?></td>
                                <td><span class="status <?php echo e($project->statusColor()); ?>"><?php echo e($project->status); ?></span></td>
                                <td><?php echo e(number_format((float) $project->physical_progress, 2)); ?>%</td>
                                <td><?php echo e(number_format((float) $project->financial_progress, 2)); ?>%</td>
                                <td><strong><?php echo e($money($project->balance_to_pay)); ?></strong></td>
                                <td class="construction-actions-column">
                                    <?php if(in_array($project->id, $editableProjectIds, true)): ?>
                                        <a
                                            class="button ghost small"
                                            href="<?php echo e(route('construction.projects.edit', $project)); ?>"
                                            aria-label="Editar <?php echo e($project->project_key); ?> - <?php echo e($project->name); ?>"
                                        >Editar</a>
                                    <?php else: ?>
                                        <span class="fine-print">Solo lectura</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="11">No hay obras registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="panel">
            <h2>Bitacora reciente</h2>
            <ul class="audit-list">
                <?php $__empty_1 = true; $__currentLoopData = $auditLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li>
                        <strong><?php echo e($entry->action); ?></strong>
                        <?php echo e($entry->description); ?>

                        <small><?php echo e($entry->user?->name ?? 'Sistema'); ?> &middot; <?php echo e($entry->occurred_at->format('d/m/Y H:i')); ?></small>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li>Sin movimientos registrados.</li>
                <?php endif; ?>
            </ul>
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
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-construction-carousel]').forEach((carousel) => {
                const track = carousel.querySelector('[data-construction-carousel-track]');
                const scrollByTile = () => Math.max(220, Math.round((track?.clientWidth || 260) * 0.72));

                carousel.querySelector('[data-carousel-prev]')?.addEventListener('click', () => {
                    track?.scrollBy({ left: -scrollByTile(), behavior: 'smooth' });
                });

                carousel.querySelector('[data-carousel-next]')?.addEventListener('click', () => {
                    track?.scrollBy({ left: scrollByTile(), behavior: 'smooth' });
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\construction\dashboard.blade.php ENDPATH**/ ?>