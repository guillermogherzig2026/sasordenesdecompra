<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Panel general de obras']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Panel general de obras']); ?>
        <?php
            $money = fn ($value) => '$'.number_format((float) $value, 2);
        ?>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Obras</h2>
                    <p class="fine-print">Visualiza y administra todas las obras registradas.</p>
                </div>
                <?php if($canCreate): ?>
                    <a class="button primary" href="<?php echo e(route('construction.projects.create')); ?>">Nueva obra</a>
                <?php endif; ?>
            </div>

        </section>

        <div class="metrics-grid">
            <?php $__currentLoopData = $counts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="metric-card">
                    <span><?php echo e($label); ?></span>
                    <strong><?php echo e($count); ?></strong>
                    <small>Obras</small>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <section class="panel">
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Clave</th>
                            <th>Obra</th>
                            <th>Cliente</th>
                            <th>Responsable</th>
                            <th>Modalidad</th>
                            <th>Estado</th>
                            <th>Avance</th>
                            <th>Contrato</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><strong><?php echo e($project->project_key); ?></strong></td>
                                <td>
                                    <?php echo e($project->name); ?>

                                    <br><span class="fine-print"><?php echo e($project->location ?: 'Sin ubicacion'); ?></span>
                                </td>
                                <td><?php echo e($project->client?->name ?? 'Sin cliente'); ?></td>
                                <td><?php echo e($project->responsible?->name ?? 'Sin responsable'); ?></td>
                                <td><?php echo e($project->modality); ?></td>
                                <td><span class="status <?php echo e($project->statusColor()); ?>"><?php echo e($project->status); ?></span></td>
                                <td><?php echo e(number_format((float) $project->physical_progress, 2)); ?>%</td>
                                <td><?php echo e($money($project->contracted_value)); ?></td>
                                <td><a class="button ghost small" href="<?php echo e(route('construction.dashboard').'#project-row-'.$project->id); ?>">Panel general</a></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="9">No hay obras con los filtros seleccionados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\construction\projects\index.blade.php ENDPATH**/ ?>