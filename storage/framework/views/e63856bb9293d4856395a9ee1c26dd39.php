<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Alta de empresas']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Alta de empresas']); ?>
        <section class="panel">
            <div>
                <h2>Alta de empresas</h2>
            </div>

            <form class="stack" method="POST" action="<?php echo e(route('finance.admin.companies.store')); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="grid-3">
                    <label>Razon social<input name="name" value="<?php echo e(old('name')); ?>" required></label>
                    <label>RFC<input name="rfc" value="<?php echo e(old('rfc')); ?>" required></label>
                    <label>Logotipo<input name="logo" type="file" accept="image/*"></label>
                </div>
                <label>Direccion<textarea name="address" required><?php echo e(old('address')); ?></textarea></label>
                <label>
                    Almacenes
                    <div id="warehouses-list" style="display:flex;flex-direction:column;gap:6px;margin-bottom:6px"></div>
                    <button type="button" class="button ghost small" onclick="addWarehouse()">+ Agregar almacen</button>
                </label>
                <label>
                    Observaciones para OC
                    <textarea name="purchase_order_notes" placeholder="Ej: caducidad minima, documentos requeridos, condiciones de entrega o pago."><?php echo e(old('purchase_order_notes')); ?></textarea>
                </label>
                <div>
                    <label>Compradores autorizados</label>
                    <div class="item-actions">
                        <?php $__empty_1 = true; $__currentLoopData = $buyers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $buyer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <label style="display:flex; gap:6px; align-items:center">
                                <input name="buyer_ids[]" type="checkbox" value="<?php echo e($buyer->id); ?>">
                                <?php echo e($buyer->name); ?>

                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <span class="fine-print">No hay compradores activos.</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-actions">
                    <span class="fine-print">Despues de guardar la empresa, aparecera en Autorizaciones para asignarla a compradores.</span>
                    <button class="button primary" type="submit">Guardar empresa</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Empresas registradas</h2>
                    <p class="fine-print">Catalogo usado en ordenes de compra y autorizaciones.</p>
                </div>
                <form class="toolbar" method="GET" action="<?php echo e(route('finance.admin.companies')); ?>">
                    <input name="q" value="<?php echo e($query); ?>" placeholder="Buscar empresa...">
                    <a class="button ghost" href="<?php echo e(route('reports.download', 'companies')); ?>">Exportar Excel</a>
                </form>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Razon Social</th>
                            <th>RFC</th>
                            <th>Direccion</th>
                            <th>Almacenes</th>
                            <th>Fecha alta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><span class="company-logo-thumb"><?php echo e($company->initials()); ?></span></td>
                                <td><?php echo e($company->name); ?></td>
                                <td><?php echo e($company->rfc); ?></td>
                                <td><?php echo e($company->address); ?></td>
                                <td>
                                    <?php $objects = $company->warehouseObjects(); ?>
                                    <?php if(count($objects)): ?>
                                        <?php $__currentLoopData = $objects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $wh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php echo e($wh['name']); ?><?php if($wh['short_name']): ?> <span class="fine-print">(<?php echo e($wh['short_name']); ?>)</span><?php endif; ?><?php echo e($loop->last ? '' : ', '); ?>

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        Sin almacenes
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($company->created_at?->format('d/m/Y')); ?></td>
                                <td>
                                    <div class="item-actions">
                                        <a class="button ghost small" href="<?php echo e(route('finance.admin.companies.edit', $company)); ?>">Editar</a>
                                        <button class="button danger small" type="button" data-dialog-target="delete-company-<?php echo e($company->id); ?>">Eliminar</button>
                                    </div>

                                    <dialog class="confirm-dialog" id="delete-company-<?php echo e($company->id); ?>">
                                        <form class="confirm-card" method="POST" action="<?php echo e(route('finance.admin.companies.destroy', $company)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <h3>Eliminar empresa</h3>
                                            <p>Estas seguro que quieres eliminar <?php echo e($company->name); ?>.</p>
                                            <div class="form-actions">
                                                <button class="button danger" type="submit">Si eliminar</button>
                                                <button class="button ghost" type="button" data-dialog-close>Cancelar</button>
                                            </div>
                                        </form>
                                    </dialog>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="7">No hay empresas registradas.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

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

            document.querySelectorAll('[data-dialog-target]').forEach((button) => {
                button.addEventListener('click', () => {
                    const dialog = document.getElementById(button.dataset.dialogTarget);
                    if (dialog) dialog.showModal();
                });
            });

            document.querySelectorAll('[data-dialog-close]').forEach((button) => {
                button.addEventListener('click', () => {
                    button.closest('dialog')?.close();
                });
            });
        </script>
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


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\finance\admin\companies.blade.php ENDPATH**/ ?>