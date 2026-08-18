<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Agregar almacen de suministros']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Agregar almacen de suministros']); ?>
        <section class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Almacen de suministros</p>
                    <h2>Nuevo almacen de suministros</h2>
                    <p class="fine-print">Captura la informacion del almacen y selecciona las empresas a las que va a surtir.</p>
                </div>
                <a class="button ghost" href="<?php echo e(route('inventory.warehouses.index')); ?>">Regresar</a>
            </div>

            <form class="stack" method="POST" action="<?php echo e(route('inventory.warehouses.supply.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="grid-3">
                    <label>Nombre del almacen
                        <input name="name" value="<?php echo e(old('name')); ?>" placeholder="Ej. Almacen central norte" required>
                    </label>
                    <label>Nombre corto
                        <input name="short_name" value="<?php echo e(old('short_name')); ?>" placeholder="Ej. Central Norte">
                    </label>
                    <label>Direccion / referencia
                        <input name="address" value="<?php echo e(old('address')); ?>" placeholder="Direccion o ubicacion del almacen">
                    </label>
                </div>

                <section class="panel">
                    <div>
                        <h3>Empresas a surtir</h3>
                        <p class="fine-print">Puedes seleccionar una o varias empresas. Despues podras modificar esta lista desde Editar.</p>
                    </div>
                    <div class="company-selector-list" style="max-height:390px;overflow:auto;display:grid;gap:8px;padding-right:8px">
                        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $selected = collect(old('companies', []))->map(fn ($id) => (int) $id)->contains((int) $company->id);
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

                <div class="form-actions">
                    <a class="button ghost" href="<?php echo e(route('inventory.warehouses.index')); ?>">Cancelar</a>
                    <button class="button primary" type="submit">Crear almacen</button>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\inventory\warehouses\create-supply.blade.php ENDPATH**/ ?>