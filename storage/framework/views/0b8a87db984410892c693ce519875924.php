<?php $__env->startSection('body'); ?>
    <main class="view" style="max-width:1100px;margin:0 auto;width:100%">
        <?php if(session('status')): ?>
            <div class="alert"><?php echo e(session('status')); ?></div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="error-list">
                <strong>Revisa la contrasena capturada.</strong>
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Formato digital de solicitud</p>
                    <h1 style="margin:0"><?php echo e($order->formatted_delivery_remission_number); ?></h1>
                    <p class="fine-print">ID OS <?php echo e($order->supply_consecutive); ?></p>
                    <p class="fine-print">OS <?php echo e($order->folio); ?> · <?php echo e(\App\Support\UiStatus::supplyOrder($order->status, 'buyer')); ?></p>
                </div>
                <?php if($order->status === 'delivered'): ?>
                    <span class="status paid">Mercancia recibida</span>
                <?php else: ?>
                    <button class="button primary" type="button" data-dialog-target="receive-supply-order">Recibir mercancia</button>
                <?php endif; ?>
            </div>

            <div class="grid-4">
                <article class="metric-card"><span>Fecha salida</span><strong style="font-size:1rem"><?php echo e($order->delivered_on?->format('d/m/Y') ?: 'Pendiente'); ?></strong></article>
                <article class="metric-card"><span>Fecha recepcion</span><strong style="font-size:1rem"><?php echo e($order->received_on?->format('d/m/Y') ?: 'Pendiente'); ?></strong></article>
                <article class="metric-card"><span>Origen</span><strong style="font-size:1rem"><?php echo e($order->warehouse_from); ?></strong></article>
                <article class="metric-card"><span>Destino</span><strong style="font-size:1rem"><?php echo e($order->warehouse_to ?: 'Sin destino'); ?></strong></article>
            </div>

            <div class="grid-2">
                <div class="panel">
                    <strong>Solicitante</strong>
                    <p><?php echo e($order->requester->name); ?></p>
                    <p class="fine-print"><?php echo e($order->requester->email); ?></p>
                </div>
                <div class="panel">
                    <strong>Empresa receptora</strong>
                    <p><?php echo e($order->company->name); ?></p>
                    <p class="fine-print">RFC: <?php echo e($order->company->rfc ?: 'Sin RFC'); ?></p>
                    <p class="fine-print"><?php echo e($order->company->address ?: 'Sin direccion capturada'); ?></p>
                </div>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Descripcion</th>
                            <th>Precio unitario</th>
                            <th>Precio total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e(number_format((float) $item->quantity, 2)); ?></td>
                                <td><?php echo e($item->catalogItem?->unit ?: 'unidad'); ?></td>
                                <td>
                                    <strong><?php echo e($item->article); ?></strong>
                                    <?php if($item->catalogItem?->description): ?>
                                        <small class="fine-print"><?php echo e($item->catalogItem->description); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>$<?php echo e(number_format((float) $item->unit_cost, 2)); ?></td>
                                <td>$<?php echo e(number_format((float) $item->line_total, 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </section>

        <dialog class="confirm-dialog" id="receive-supply-order">
            <form class="confirm-card" method="POST" action="<?php echo e(route('supply-orders.digital.receive', $order->remission_token)); ?>">
                <?php echo csrf_field(); ?>
                <h3>Recibir mercancia</h3>
                <p>Ingresa la contrasena de 4 digitos del almacen receptor para marcar esta OS como recibida.</p>
                <label>Contrasena
                    <input name="receiving_pin" type="password" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" required autofocus>
                </label>
                <div class="form-actions">
                    <button class="button ghost" type="button" data-dialog-close>Cancelar</button>
                    <button class="button primary" type="submit">Aceptar</button>
                </div>
            </form>
        </dialog>
    </main>

    <script>
        document.querySelectorAll('[data-dialog-target]').forEach((button) => {
            button.addEventListener('click', () => document.getElementById(button.dataset.dialogTarget)?.showModal());
        });

        document.querySelectorAll('[data-dialog-close]').forEach((button) => {
            button.addEventListener('click', () => button.closest('dialog')?.close());
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\supply-orders\digital.blade.php ENDPATH**/ ?>