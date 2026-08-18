<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Editar empresa']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Editar empresa']); ?>
        <form class="panel" method="POST" action="<?php echo e(route('finance.admin.companies.update', $company)); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="panel-header">
                <div>
                    <h2>Editar empresa</h2>
                    <p class="fine-print">Actualiza datos fiscales, logotipo y compradores autorizados.</p>
                </div>
                <a class="button ghost" href="<?php echo e(route('finance.admin.companies')); ?>">Volver</a>
            </div>

            <div class="grid-3">
                <label>
                    Razon Social
                    <input name="name" value="<?php echo e(old('name', $company->name)); ?>" required>
                </label>
                <label>
                    RFC
                    <input name="rfc" value="<?php echo e(old('rfc', $company->rfc)); ?>" required>
                </label>
                <label>
                    Logotipo
                    <input name="logo" type="file" accept="image/*">
                    <?php if($company->logo_path): ?>
                        <small class="fine-print">
                            Logo actual:
                            <a href="<?php echo e(route('companies.logo', $company)); ?>" target="_blank">Ver logotipo cargado</a>
                        </small>
                    <?php else: ?>
                        <small class="fine-print">Sin logotipo cargado.</small>
                    <?php endif; ?>
                </label>
            </div>

            <label>
                Direccion
                <textarea name="address" required><?php echo e(old('address', $company->address)); ?></textarea>
            </label>

            <label>
                Almacenes
                <div id="warehouses-list" style="display:flex;flex-direction:column;gap:6px;margin-bottom:6px">
                    <?php $__currentLoopData = $company->warehouseObjects(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="display:flex;gap:6px;align-items:center">
                            <input name="warehouses[<?php echo e($loop->index); ?>][name]" value="<?php echo e($warehouse['name']); ?>" placeholder="Nombre del almacen" required style="flex:3">
                            <input name="warehouses[<?php echo e($loop->index); ?>][short_name]" value="<?php echo e($warehouse['short_name']); ?>" placeholder="Nombre corto (ej: AC)" style="flex:1">
                            <button type="button" class="button ghost small" onclick="this.parentElement.remove()">✕</button>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <button type="button" class="button ghost small" onclick="addWarehouse()">+ Agregar almacen</button>
            </label>

            <label>
                Observaciones para OC
                <textarea name="purchase_order_notes" placeholder="Ej: caducidad minima, documentos requeridos, condiciones de entrega o pago."><?php echo e(old('purchase_order_notes', $company->purchase_order_notes)); ?></textarea>
            </label>

            <div>
                <label>Compradores autorizados</label>
                <div class="item-actions">
                    <?php $__currentLoopData = $buyers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $buyer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label style="display:flex; gap:6px; align-items:center">
                            <input name="buyer_ids[]" type="checkbox" value="<?php echo e($buyer->id); ?>" <?php if(in_array($company->name, $buyer->authorizedCompanyNames(), true)): echo 'checked'; endif; ?>>
                            <?php echo e($buyer->name); ?>

                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="form-actions">
                <span class="fine-print">Los cambios se reflejaran en nuevas ordenes y autorizaciones.</span>
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

    <script>
        function addWarehouse(name = '', shortName = '') {
            const list = document.getElementById('warehouses-list');
            const idx = list.children.length;
            const row = document.createElement('div');
            row.style.cssText = 'display:flex;gap:6px;align-items:center';
            row.innerHTML = `
                <input name="warehouses[${idx}][name]" value="${name}" placeholder="Nombre del almacen" required style="flex:3">
                <input name="warehouses[${idx}][short_name]" value="${shortName}" placeholder="Nombre corto (ej: AC)" style="flex:1">
                <button type="button" class="button ghost small" onclick="this.parentElement.remove()">✕</button>
            `;
            list.appendChild(row);
        }
    </script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\finance\admin\company-edit.blade.php ENDPATH**/ ?>