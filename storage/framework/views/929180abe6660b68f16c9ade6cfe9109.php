<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Catalogo de servicios']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Catalogo de servicios']); ?>
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Catalogo de servicios</h2>
                    <p class="fine-print">Servicios recurrentes registrados con vigencia y lapso de pago.</p>
                </div>
                <form class="toolbar" method="GET" action="<?php echo e(route('services.catalog')); ?>">
                    <input name="q" value="<?php echo e($query); ?>" placeholder="Buscar servicio...">
                    <a class="button primary" href="<?php echo e(route('services.create')); ?>">Nuevo servicio</a>
                </form>
            </div>

            <div class="table-scroll service-month-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titular</th>
                            <th>Sucursal</th>
                            <th>Ubicacion</th>
                            <th>Banco</th>
                            <th>Cuenta pagadora</th>
                            <th>Servicio</th>
                            <th>Proveedor</th>
                            <th>No. Servicio</th>
                            <th>Categoria</th>
                            <th>Monto</th>
                            <th>Vigencia</th>
                            <th>Lapso pago</th>
                            <th>Fecha inicio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><strong><?php echo e($service->folio); ?></strong></td>
                                <td><?php echo e($service->holder ?: $service->company_name); ?></td>
                                <td><?php echo e($service->display_branch); ?></td>
                                <td><?php echo e($service->display_location); ?></td>
                                <td><?php echo e($service->bank); ?></td>
                                <td><?php echo e($service->payer_account); ?></td>
                                <td><?php echo e($service->service_name); ?></td>
                                <td><?php echo e($service->provider); ?></td>
                                <td><?php echo e($service->service_number); ?></td>
                                <td><?php echo e($service->category); ?></td>
                                <td>$<?php echo e(number_format((float) $service->cost, 2)); ?></td>
                                <td><?php echo e($service->validity); ?></td>
                                <td><?php echo e($service->is_domiciled ? 'Domiciliado' : $service->payment_interval_days . ' dias'); ?></td>
                                <td><?php echo e($service->cutoff_day ? 'Corte dia ' . $service->cutoff_day : $service->start_date?->format('d/m/Y')); ?></td>
                                <td>
                                    <details class="status-menu">
                                        <summary class="status <?php echo e(\App\Support\UiStatus::serviceClass($service->status)); ?>"><?php echo e(\App\Support\UiStatus::service($service->status, 'services')); ?></summary>
                                        <div class="status-menu-panel">
                                            <?php if($service->status === 'active'): ?>
                                                <form class="inline-form" method="POST" action="<?php echo e(route('services.status', [$service, 'paused'])); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?><button class="button ghost small">Pausar</button></form>
                                            <?php endif; ?>
                                            <?php if($service->status === 'paused'): ?>
                                                <form class="inline-form" method="POST" action="<?php echo e(route('services.status', [$service, 'active'])); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?><button class="button primary small">Reactivar</button></form>
                                            <?php endif; ?>
                                            <?php if($service->status !== 'inactive'): ?>
                                                <form class="inline-form" method="POST" action="<?php echo e(route('services.status', [$service, 'inactive'])); ?>" onsubmit="return confirm('Dar de baja <?php echo e($service->folio); ?>?')"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?><button class="button danger small">Baja</button></form>
                                            <?php endif; ?>
                                        </div>
                                    </details>
                                </td>
                                <td>
                                    <div class="item-actions">
                                        <?php if($service->status === 'active'): ?>
                                            <form class="inline-form" method="POST" action="<?php echo e(route('services.status', [$service, 'paused'])); ?>">
                                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                                <button class="button ghost small" type="submit">Pausar</button>
                                            </form>
                                        <?php elseif($service->status === 'paused'): ?>
                                            <form class="inline-form" method="POST" action="<?php echo e(route('services.status', [$service, 'active'])); ?>">
                                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                                <button class="button primary small" type="submit">Reactivar</button>
                                            </form>
                                        <?php endif; ?>
                                        <a class="button ghost small" href="<?php echo e(route('services.edit', $service)); ?>">Editar</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="16">No hay servicios registrados.</td></tr>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\services\catalog.blade.php ENDPATH**/ ?>