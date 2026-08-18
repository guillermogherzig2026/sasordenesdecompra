<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Usuarios y permisos de obra']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Usuarios y permisos de obra']); ?>
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Usuarios y permisos</h2>
                    <p class="fine-print">Asigna consulta y edicion por obra a usuarios existentes del sistema.</p>
                </div>
                <a class="button ghost" href="<?php echo e(route('construction.projects.index')); ?>">Ver obras</a>
            </div>
        </section>

        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $userAccess = $accessByUser->get($user->id, collect()); ?>
            <form class="panel" method="POST" action="<?php echo e(route('construction.users-access.update', $user)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="panel-header">
                    <div>
                        <h2><?php echo e($user->name); ?></h2>
                        <p class="fine-print"><?php echo e($user->email); ?> &middot; <?php echo e($user->role); ?></p>
                    </div>
                    <button class="button primary" type="submit">Guardar permisos</button>
                </div>

                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Obra</th>
                                <th>Cliente</th>
                                <th>Consultar</th>
                                <th>Editar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php $pivot = $userAccess->get($project->id); ?>
                                <tr>
                                    <td><strong><?php echo e($project->project_key); ?></strong> <?php echo e($project->name); ?></td>
                                    <td><?php echo e($project->client?->name ?? 'Sin cliente'); ?></td>
                                    <td>
                                        <input type="checkbox" name="projects[<?php echo e($project->id); ?>][can_view]" value="1" <?php if($pivot?->can_view): echo 'checked'; endif; ?>>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="projects[<?php echo e($project->id); ?>][can_edit]" value="1" <?php if($pivot?->can_edit): echo 'checked'; endif; ?>>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="4">No hay obras registradas.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\construction\users-access.blade.php ENDPATH**/ ?>