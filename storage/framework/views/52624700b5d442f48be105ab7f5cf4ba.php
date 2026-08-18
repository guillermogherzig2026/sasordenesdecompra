<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Editar proveedor']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Editar proveedor']); ?>
        <form class="panel" method="POST" action="<?php echo e(route('finance.admin.providers.update', $provider)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="panel-header">
                <div>
                    <h2>Editar proveedor</h2>
                    <p class="fine-print">Proveedor dado de alta por <?php echo e($provider->buyer?->name ?? 'Sin comprador'); ?>.</p>
                </div>
                <a class="button ghost" href="<?php echo e(route('finance.admin.providers')); ?>">Volver</a>
            </div>

            <div class="grid-4">
                <label>
                    Razon Social
                    <input name="business_name" value="<?php echo e(old('business_name', $provider->business_name)); ?>" required>
                </label>
                <label>
                    RFC
                    <input name="rfc" value="<?php echo e(old('rfc', $provider->rfc)); ?>" required>
                </label>
                <label>
                    Giro
                    <select name="business_line_id" data-provider-line-select required>
                        <?php $__currentLoopData = $businessLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($line->id); ?>" <?php if((int) old('business_line_id', $provider->provider_business_line_id) === $line->id || (! $provider->provider_business_line_id && $provider->business_line === $line->name)): echo 'selected'; endif; ?>><?php echo e($line->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </label>
                <label>
                    Subcategoria
                    <select name="business_subcategory_id" data-provider-subcategory-select>
                        <option value="">Sin subcategoria</option>
                        <?php $__currentLoopData = $businessLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $line->subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($subcategory->id); ?>" data-line-id="<?php echo e($line->id); ?>" <?php if((int) old('business_subcategory_id', $provider->provider_business_subcategory_id) === $subcategory->id): echo 'selected'; endif; ?>><?php echo e($subcategory->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </label>
            </div>

            <div class="grid-3">
                <label>
                    Banco
                    <input name="bank" value="<?php echo e(old('bank', $provider->bank)); ?>" required>
                </label>
                <label>
                    Cuenta
                    <input name="account_number" value="<?php echo e(old('account_number', $provider->account_number)); ?>" required>
                </label>
                <label>
                    CLABE
                    <input name="clabe" value="<?php echo e(old('clabe', $provider->clabe)); ?>" maxlength="18" required>
                </label>
            </div>

            <label>
                Referencia
                <input name="reference" value="<?php echo e(old('reference', $provider->reference)); ?>" placeholder="Referencia bancaria o linea de captura">
            </label>

            <div class="form-actions">
                <span class="fine-print">Los cambios se reflejaran en OC vigentes e historial.</span>
                <button class="button primary" type="submit">Guardar cambios</button>
            </div>
        </form>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\finance\admin\provider-edit.blade.php ENDPATH**/ ?>