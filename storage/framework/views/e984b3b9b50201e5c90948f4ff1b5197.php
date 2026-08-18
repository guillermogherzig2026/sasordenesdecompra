<?php $__env->startSection('body'); ?>
    <?php
        $inventory = $item->inventories->first();
        $categoryOptions = $categoryOptions ?? collect();
        $subcategoryOptions = $subcategoryOptions ?? collect();
    ?>

    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Editar producto']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Editar producto']); ?>
        <section class="panel">
            <div class="panel-header">
                <div>
                    <small class="eyebrow">Catalogo de productos</small>
                    <h2>Editar producto de <?php echo e($warehouse['warehouse']); ?></h2>
                    <p class="fine-print">El SKU es consecutivo y no se puede modificar manualmente.</p>
                </div>
                <a class="button ghost" href="<?php echo e(route('inventory.warehouses.catalog', $warehouse['key'])); ?>">Regresar</a>
            </div>

            <form class="stack" method="POST" action="<?php echo e(route('inventory.warehouses.catalog.update', ['warehouseKey' => $warehouse['key'], 'warehouseCatalogItem' => $item])); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <datalist id="catalog-categories">
                    <?php $__currentLoopData = $categoryOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($category); ?>"></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </datalist>
                <datalist id="catalog-subcategories">
                    <?php $__currentLoopData = $subcategoryOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($subcategory); ?>"></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </datalist>

                <div class="grid-4">
                    <label>SKU
                        <input value="<?php echo e($item->sku ?: 'Pendiente'); ?>" disabled>
                    </label>
                    <label>Categoria
                        <input name="category" list="catalog-categories" value="<?php echo e(old('category', $item->category)); ?>" data-catalog-field="category" placeholder="Selecciona o escribe categoria" required>
                        <button class="button ghost small" type="button" data-catalog-new-field="category">+ Nueva categoria</button>
                    </label>
                    <label>Subcategoria
                        <input name="subcategory" list="catalog-subcategories" value="<?php echo e(old('subcategory', $item->subcategory)); ?>" data-catalog-field="subcategory" placeholder="Selecciona o escribe subcategoria" required>
                        <button class="button ghost small" type="button" data-catalog-new-field="subcategory">+ Nueva subcategoria</button>
                    </label>
                    <label>Nombre del producto
                        <input name="name" value="<?php echo e(old('name', $item->name)); ?>" required>
                    </label>
                </div>

                <div class="grid-4">
                    <label>Unidad
                        <input name="unit" value="<?php echo e(old('unit', $item->unit)); ?>" required>
                    </label>
                    <label>Precio unitario
                        <input name="unit_cost" type="number" min="0" step="0.01" value="<?php echo e(old('unit_cost', $item->unit_cost)); ?>">
                    </label>
                    <label>Existencia en <?php echo e($warehouse['warehouse']); ?>

                        <input name="quantity" type="number" min="0" step="0.01" value="<?php echo e(old('quantity', $inventory?->quantity ?? 0)); ?>" required>
                    </label>
                    <label>Minimo
                        <input name="minimum_quantity" type="number" min="0" step="0.01" value="<?php echo e(old('minimum_quantity', $inventory?->minimum_quantity ?? 0)); ?>">
                    </label>
                </div>

                <input name="authorized" type="hidden" value="0">
                <label class="checkbox-inline">
                    <input name="authorized" type="checkbox" value="1" <?php if(old('authorized', $item->authorized)): echo 'checked'; endif; ?>>
                    Autorizado para OS
                </label>

                <label>Descripcion
                    <textarea name="description" rows="3"><?php echo e(old('description', $item->description)); ?></textarea>
                </label>

                <div class="form-actions">
                    <a class="button ghost" href="<?php echo e(route('inventory.warehouses.catalog', $warehouse['key'])); ?>">Cancelar</a>
                    <button class="button primary" type="submit">Guardar cambios</button>
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

    <script>
        document.querySelectorAll('[data-catalog-new-field]').forEach((button) => {
            button.addEventListener('click', () => {
                const field = document.querySelector(`[data-catalog-field="${button.dataset.catalogNewField}"]`);
                if (!field) return;

                field.value = '';
                field.focus();
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\inventory\catalog\edit.blade.php ENDPATH**/ ?>