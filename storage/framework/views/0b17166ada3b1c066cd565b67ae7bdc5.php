<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Nueva orden de reembolso']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Nueva orden de reembolso']); ?>
        <section class="panel reimbursement-panel">
            <div>
                <h2>Solicitud de reembolso</h2>
                <p class="fine-print">Captura proveedor, monto, cotizacion y soporte del producto o servicio si ya lo tienes.</p>
            </div>

            <form class="stack reimbursement-form" method="POST" action="<?php echo e(route('buyer.reimbursement-orders.store')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="grid-3">
                    <label>Empresa
                        <select name="company_id" required>
                            <option value="">Selecciona...</option>
                            <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($company->id); ?>" <?php if(old('company_id') == $company->id): echo 'selected'; endif; ?>><?php echo e($company->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                    <label>Proveedor<input name="provider" value="<?php echo e(old('provider')); ?>" required></label>
                    <label>Monto<input name="amount" type="number" min="0.01" step="0.01" value="<?php echo e(old('amount')); ?>" required></label>
                </div>
                <label class="reimbursement-concept">Concepto<input name="concept" value="<?php echo e(old('concept')); ?>" placeholder="Producto o servicio a reembolsar"></label>
                <div class="grid-2">
                    <label>Cotizacion<input name="quote_file" type="file" accept=".pdf,.jpg,.jpeg,.png" required></label>
                    <label>Soporte del producto o servicio<input name="support_file" type="file" accept=".pdf,.jpg,.jpeg,.png"></label>
                </div>
                <label>Notas<textarea name="notes" rows="3"><?php echo e(old('notes')); ?></textarea></label>

                <div class="form-actions">
                    <a class="button ghost" href="<?php echo e(route('buyer.reimbursement-orders.index')); ?>">Cancelar</a>
                    <button class="button primary" type="submit">Enviar OR</button>
                </div>
            </form>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\buyer\reimbursement-orders\form.blade.php ENDPATH**/ ?>