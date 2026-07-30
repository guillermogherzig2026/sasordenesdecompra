<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Resumen operativo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Resumen operativo']); ?>
        <div class="metrics-grid">
            <?php if($user->role === 'superadmin'): ?>
                <article class="metric-card">
                    <span>Usuarios</span>
                    <strong><?php echo e($usersCount); ?></strong>
                    <small>Cuentas registradas</small>
                </article>
                <article class="metric-card">
                    <span>Activos</span>
                    <strong><?php echo e($activeUsersCount); ?></strong>
                    <small>Pueden iniciar sesion</small>
                </article>
                <article class="metric-card">
                    <span>Roles</span>
                    <strong><?php echo e($rolesCount); ?></strong>
                    <small>Perfiles operativos usados</small>
                </article>
                <article class="metric-card">
                    <span>Empresas</span>
                    <strong><?php echo e($companiesCount); ?></strong>
                    <small>Asignables a compradores</small>
                </article>
            <?php elseif($user->role === 'finance'): ?>
                <article class="metric-card">
                    <span>OC pendientes</span>
                    <strong><?php echo e($financeSentCount); ?></strong>
                    <small>Esperan revision de Finanzas</small>
                </article>
                <article class="metric-card">
                    <span>Aprobadas</span>
                    <strong><?php echo e($financeApprovedCount); ?></strong>
                    <small>Pendientes de pago</small>
                </article>
                <article class="metric-card">
                    <span>Monto pendiente</span>
                    <strong>$<?php echo e(number_format((float) $financePendingAmount, 0)); ?></strong>
                    <small>Ordenado por vencimiento</small>
                </article>
                <article class="metric-card">
                    <span>Monto total</span>
                    <strong>$<?php echo e(number_format((float) $financeCurrentMonthTotal, 0)); ?></strong>
                    <small>Mes en curso: <?php echo e($currentMonthLabel); ?></small>
                </article>
            <?php elseif($user->role === 'buyer'): ?>
                <article class="metric-card">
                    <span>Enviadas</span>
                    <strong><?php echo e($buyerSentCount); ?></strong>
                    <small>Antes de aprobacion</small>
                </article>
                <article class="metric-card">
                    <span>Aprobadas</span>
                    <strong><?php echo e($buyerApprovedCount); ?></strong>
                    <small>Listas para pago</small>
                </article>
                <article class="metric-card">
                    <span>Pagadas</span>
                    <strong><?php echo e($buyerPaidCount); ?></strong>
                    <small>Con archivo de pago</small>
                </article>
                <article class="metric-card">
                    <span>Monto total</span>
                    <strong>$<?php echo e(number_format((float) $buyerCurrentMonthTotal, 0)); ?></strong>
                    <small>Mes en curso: <?php echo e($currentMonthLabel); ?></small>
                </article>
            <?php elseif($user->role === 'inventory'): ?>
                <article class="metric-card">
                    <span>Pendientes</span>
                    <strong><?php echo e($inventoryPendingCount); ?></strong>
                    <small>Sin comprobacion</small>
                </article>
                <article class="metric-card">
                    <span>Parciales</span>
                    <strong><?php echo e($inventoryPartialCount); ?></strong>
                    <small>Recepcion incompleta</small>
                </article>
                <article class="metric-card">
                    <span>Completadas</span>
                    <strong><?php echo e($inventoryCompletedCount); ?></strong>
                    <small>Cantidad recibida completa</small>
                </article>
                <article class="metric-card">
                    <span>Monto completado</span>
                    <strong>$<?php echo e(number_format((float) $inventoryCompletedAmount, 0)); ?></strong>
                    <small>Recepciones cerradas</small>
                </article>
            <?php elseif(in_array($user->role, ['services', 'administrative_assistant'], true)): ?>
                <article class="metric-card">
                    <span>Servicios activos</span>
                    <strong><?php echo e($servicesCount); ?></strong>
                    <small>Generan pagos recurrentes</small>
                </article>
                <article class="metric-card">
                    <span>Por pagar este mes</span>
                    <strong><?php echo e($servicesDueThisMonthCount); ?></strong>
                    <small>Fechas de corte del mes</small>
                </article>
                <article class="metric-card">
                    <span>Monto del mes</span>
                    <strong>$<?php echo e(number_format((float) $servicesMonthAmount, 0)); ?></strong>
                    <small><?php echo e($currentMonthLabel); ?></small>
                </article>
                <article class="metric-card">
                    <span>Recibos cargados</span>
                    <strong><?php echo e($servicesReceiptsLoadedCount); ?></strong>
                    <small>Soporte para Finanzas</small>
                </article>
            <?php else: ?>
                <article class="metric-card">
                    <span>Servicios activos</span>
                    <strong><?php echo e($servicesCount); ?></strong>
                    <small>Generan pagos recurrentes</small>
                </article>
                <article class="metric-card">
                    <span>Total OC</span>
                    <strong><?php echo e($ordersCount); ?></strong>
                    <small>Referencia general del sistema</small>
                </article>
            <?php endif; ?>

            <?php if(! in_array($user->role, ['superadmin', 'finance', 'buyer', 'inventory', 'services', 'administrative_assistant'], true)): ?>
                <article class="metric-card">
                    <span>Total OC</span>
                    <strong><?php echo e($ordersCount); ?></strong>
                    <small>Ordenes registradas en el sistema</small>
                </article>
                <article class="metric-card">
                    <span>Auditoria</span>
                    <strong><?php echo e($auditLogs->count()); ?></strong>
                    <small>Ultimos movimientos visibles</small>
                </article>
            <?php endif; ?>
        </div>

        <?php if($user->role === 'superadmin'): ?>
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2>Gestion de accesos</h2>
                        <p class="fine-print">Crea usuarios, asigna roles, limita empresas para compradores y activa o desactiva cuentas.</p>
                    </div>
                    <a class="button primary" href="<?php echo e(route('superadmin.users.index')); ?>">Usuarios y Roles</a>
                </div>
            </section>

            <section class="panel">
                <h2>Auditoria reciente</h2>
                <ul class="audit-list">
                    <?php $__empty_1 = true; $__currentLoopData = $auditLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li>
                            <strong><?php echo e($entry->action); ?></strong>
                            <?php echo e($entry->description); ?>

                            <small><?php echo e($entry->user?->name ?? 'Sistema'); ?> &middot; <?php echo e($entry->created_at->format('d/m/Y H:i')); ?></small>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li>Sin movimientos registrados.</li>
                    <?php endif; ?>
                </ul>
            </section>
        <?php elseif($user->role === 'finance'): ?>
        <?php elseif($user->role === 'buyer'): ?>
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2>Nueva orden de compra</h2>
                        <p class="fine-print">Captura proveedor, fechas, empresa autorizada y articulos. La OC se envia a Finanzas automaticamente.</p>
                    </div>
                    <a class="button primary" href="<?php echo e(route('buyer.orders.create')); ?>">Crear OC</a>
                </div>
            </section>

        <?php elseif($user->role === 'inventory'): ?>
        <?php elseif(in_array($user->role, ['services', 'administrative_assistant'], true)): ?>
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2>Seguimiento de servicios</h2>
                        <p class="fine-print">Da de alta servicios recurrentes, revisa fechas de corte y adjunta facturas por periodo.</p>
                    </div>
                    <a class="button primary" href="<?php echo e(route('services.create')); ?>">Alta servicio</a>
                </div>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <h2>Auditoria reciente</h2>
                    <p class="fine-print">Cada cambio genera una notificacion interna simulada.</p>
                </div>
                <ul class="audit-list">
                    <?php $__empty_1 = true; $__currentLoopData = $orderAuditLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li>
                            <strong><?php echo e($entry->auditable?->folio); ?></strong>
                            <?php echo e($entry->description); ?>

                            <small><?php echo e($entry->user?->name ?? 'Sistema'); ?> &middot; <?php echo e($entry->created_at->format('d/m/Y, h:i a')); ?></small>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li>Sin movimientos registrados.</li>
                    <?php endif; ?>
                </ul>
            </section>
        <?php else: ?>
            <section class="panel">
                <div>
                    <h2>Base Laravel lista</h2>
                    <p class="fine-print">
                        Esta pantalla ya usa autenticacion, roles y datos persistentes en MySQL.
                        El modulo Compras y Suministros ya tiene navegacion real para OC, OS, OR y proveedores.
                    </p>
                </div>
            </section>

            <section class="panel">
                <h2>Auditoria reciente</h2>
                <ul class="audit-list">
                    <?php $__empty_1 = true; $__currentLoopData = $auditLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <li>
                            <strong><?php echo e($entry->action); ?></strong>
                            <?php echo e($entry->description); ?>

                            <small><?php echo e($entry->user?->name ?? 'Sistema'); ?> &middot; <?php echo e($entry->created_at->format('d/m/Y H:i')); ?></small>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <li>Sin movimientos registrados.</li>
                    <?php endif; ?>
                </ul>
            </section>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\sasordenesdecompra\resources\views/dashboard.blade.php ENDPATH**/ ?>