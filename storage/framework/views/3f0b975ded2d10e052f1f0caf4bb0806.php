<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Editar almacen']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Editar almacen']); ?>
        <section class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow"><?php echo e($warehouse['type']); ?></p>
                    <h2>Editar <?php echo e($warehouse['warehouse']); ?></h2>
                    <p class="fine-print"><?php echo e($warehouse['company']); ?></p>
                </div>
                <a class="button ghost" href="<?php echo e(route('inventory.warehouses.index')); ?>">Regresar</a>
            </div>

            <form class="stack" method="POST" action="<?php echo e(route('inventory.warehouses.update', $warehouse['key'])); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <?php if($warehouse['is_central']): ?>
                    <div class="grid-3">
                        <label>Nombre del almacen
                            <input name="name" value="<?php echo e(old('name', $warehouse['warehouse'])); ?>" required>
                        </label>
                        <label>Nombre corto
                            <input name="short_name" value="<?php echo e(old('short_name', $warehouse['short_name'] === '—' ? '' : $warehouse['short_name'])); ?>">
                        </label>
                        <label>Direccion / referencia
                            <input name="address" value="<?php echo e(old('address', $warehouse['address'])); ?>">
                        </label>
                    </div>

                    <section class="panel">
                        <div>
                            <h3>Empresas surtidas por este almacen</h3>
                            <p class="fine-print">Selecciona las empresas a las que el Almacen de suministros puede surtir productos.</p>
                        </div>
                        <div class="company-selector-list" style="max-height:360px;overflow:auto;display:grid;gap:8px;padding-right:8px">
                            <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $selected = collect(old('companies', $selectedCompanyIds))->map(fn ($id) => (int) $id)->contains((int) $company->id);
                                ?>
                                <label class="company-selector-option" style="display:flex;align-items:center;gap:10px;padding:10px;border:1px solid var(--line);border-radius:8px;background:#fff">
                                    <input name="companies[]" type="checkbox" value="<?php echo e($company->id); ?>" <?php if($selected): echo 'checked'; endif; ?> style="width:auto;min-height:auto">
                                    <span>
                                        <strong><?php echo e($company->name); ?></strong>
                                        <small class="fine-print">RFC: <?php echo e($company->rfc ?: 'Sin RFC'); ?></small>
                                    </span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </section>
                <?php else: ?>
                    <div class="grid-2">
                        <label>Empresa
                            <input value="<?php echo e($warehouse['company']); ?>" disabled>
                        </label>
                        <label>RFC
                            <input value="<?php echo e($warehouse['rfc']); ?>" disabled>
                        </label>
                    </div>
                    <div class="grid-3">
                        <label>Nombre del almacen
                            <input name="warehouse" value="<?php echo e(old('warehouse', $warehouse['warehouse'])); ?>" required>
                        </label>
                        <label>Nombre corto
                            <input name="short_name" value="<?php echo e(old('short_name', $warehouse['short_name'] === '—' ? '' : $warehouse['short_name'])); ?>">
                        </label>
                        <label>Direccion / referencia
                            <input name="address" value="<?php echo e(old('address', $warehouse['address'])); ?>">
                        </label>
                    </div>
                <?php endif; ?>

                <div class="form-actions">
                    <a class="button ghost" href="<?php echo e(route('inventory.warehouses.index')); ?>">Cancelar</a>
                    <button class="button primary" type="submit">Guardar y cerrar</button>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\inventory\warehouses\edit.blade.php ENDPATH**/ ?>