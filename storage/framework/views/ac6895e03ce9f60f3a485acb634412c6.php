<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Administrar categorias']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Administrar categorias']); ?>
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Gestion de categorias de proveedores</h2>
                    <p class="fine-print">Agrega categorias y administra sus subcategorias.</p>
                </div>
                <a class="button ghost small" href="<?php echo e(route('superadmin.provider-lines.index')); ?>">Atras</a>
            </div>

            <form class="stack" method="POST" action="<?php echo e(route('superadmin.provider-lines.store')); ?>">
                <?php echo csrf_field(); ?>
                <input name="return_to" type="hidden" value="management">
                <div class="grid-2">
                    <label>
                        Nueva categoria
                        <input name="name" value="<?php echo e(old('name')); ?>" placeholder="Nombre de la categoria" required>
                    </label>
                    <div class="form-actions align-end">
                        <button class="button primary" type="submit">Agregar categoria</button>
                    </div>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Categorias y subcategorias</h2>
                    <p class="fine-print">Usa el boton + para mostrar y gestionar las subcategorias.</p>
                </div>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Subcategorias</th>
                            <th>Proveedores</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <span class="provider-category-cell">
                                        <button
                                            class="button ghost small provider-line-toggle"
                                            type="button"
                                            data-management-line-toggle="<?php echo e($line->id); ?>"
                                            aria-expanded="false"
                                            aria-label="Mostrar subcategorias de <?php echo e($line->name); ?>"
                                        >+</button>
                                        <strong><?php echo e($line->name); ?></strong>
                                    </span>
                                </td>
                                <td><?php echo e($line->subcategories_count); ?></td>
                                <td><?php echo e($line->providers_count); ?></td>
                                <td>
                                    <span class="status <?php echo e($line->active ? 'approved' : 'canceled'); ?>"><?php echo e($line->active ? 'Activo' : 'Inactivo'); ?></span>
                                </td>
                                <td class="row-actions">
                                    <button class="button ghost small management-editor-toggle" type="button" data-target="management-line-editor-<?php echo e($line->id); ?>">Editar</button>
                                    <form class="inline-form" method="POST" action="<?php echo e(route('superadmin.provider-lines.destroy', $line)); ?>" onsubmit="return confirm('Estas seguro que quieres eliminar <?php echo e($line->name); ?>?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <input name="return_to" type="hidden" value="management">
                                        <button class="button danger small" type="submit" <?php if($line->providers_count > 0): echo 'disabled'; endif; ?>>Eliminar</button>
                                    </form>
                                </td>
                            </tr>

                            <tr class="editor-row" id="management-line-editor-<?php echo e($line->id); ?>" hidden>
                                <td colspan="5">
                                    <form class="stack" method="POST" action="<?php echo e(route('superadmin.provider-lines.update', $line)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <input name="return_to" type="hidden" value="management">
                                        <div class="grid-3">
                                            <label>Categoria<input name="name" value="<?php echo e($line->name); ?>" required></label>
                                            <label class="checkbox-inline">
                                                <input name="active" type="checkbox" value="1" <?php if($line->active): echo 'checked'; endif; ?>>
                                                Activo
                                            </label>
                                            <div class="form-actions align-end">
                                                <button class="button primary small" type="submit">Guardar cambios</button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                            </tr>

                            <?php $__currentLoopData = $line->subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr data-parent-line="<?php echo e($line->id); ?>" data-line-visible hidden>
                                    <td><span class="provider-subcategory-cell"><?php echo e($subcategory->name); ?></span></td>
                                    <td>Subcategoria</td>
                                    <td><?php echo e($subcategory->providers_count); ?></td>
                                    <td>
                                        <span class="status <?php echo e($subcategory->active ? 'approved' : 'canceled'); ?>"><?php echo e($subcategory->active ? 'Activo' : 'Inactivo'); ?></span>
                                    </td>
                                    <td class="row-actions">
                                        <button class="button ghost small management-editor-toggle" type="button" data-target="management-subcategory-editor-<?php echo e($subcategory->id); ?>">Editar</button>
                                        <form class="inline-form" method="POST" action="<?php echo e(route('superadmin.provider-lines.subcategories.destroy', $subcategory)); ?>" onsubmit="return confirm('Estas seguro que quieres eliminar <?php echo e($subcategory->name); ?>?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <input name="return_to" type="hidden" value="management">
                                            <button class="button danger small" type="submit" <?php if($subcategory->providers_count > 0): echo 'disabled'; endif; ?>>Eliminar</button>
                                        </form>
                                    </td>
                                </tr>

                                <tr class="editor-row" id="management-subcategory-editor-<?php echo e($subcategory->id); ?>" data-parent-line="<?php echo e($line->id); ?>" hidden>
                                    <td colspan="5">
                                        <form class="stack" method="POST" action="<?php echo e(route('superadmin.provider-lines.subcategories.update', $subcategory)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PUT'); ?>
                                            <input name="return_to" type="hidden" value="management">
                                            <div class="grid-3">
                                                <label>Subcategoria<input name="name" value="<?php echo e($subcategory->name); ?>" required></label>
                                                <label class="checkbox-inline">
                                                    <input name="active" type="checkbox" value="1" <?php if($subcategory->active): echo 'checked'; endif; ?>>
                                                    Activo
                                                </label>
                                                <div class="form-actions align-end">
                                                    <button class="button primary small" type="submit">Guardar cambios</button>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <tr data-parent-line="<?php echo e($line->id); ?>" data-line-visible hidden>
                                <td colspan="5">
                                    <form class="provider-subcategory-create-form" method="POST" action="<?php echo e(route('superadmin.provider-lines.subcategories.store', $line)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <input name="return_to" type="hidden" value="management">
                                        <label>
                                            Nueva subcategoria
                                            <input name="name" placeholder="Nombre de la subcategoria" required>
                                        </label>
                                        <button class="button primary small" type="submit">Agregar subcategoria</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="empty-state">No hay categorias registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <script>
            document.querySelectorAll('[data-management-line-toggle]').forEach((button) => {
                button.addEventListener('click', () => {
                    const parentId = button.dataset.managementLineToggle;
                    const expanded = button.getAttribute('aria-expanded') === 'true';

                    document.querySelectorAll(`[data-parent-line="${parentId}"][data-line-visible]`).forEach((row) => {
                        row.hidden = expanded;
                    });

                    if (expanded) {
                        document.querySelectorAll(`[data-parent-line="${parentId}"].editor-row`).forEach((row) => {
                            row.hidden = true;
                            document.querySelector(`[data-target="${row.id}"]`)?.replaceChildren('Editar');
                        });
                    }

                    button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                    button.textContent = expanded ? '+' : '-';
                });
            });

            document.querySelectorAll('.management-editor-toggle').forEach((button) => {
                button.addEventListener('click', () => {
                    const row = document.getElementById(button.dataset.target);
                    if (!row) return;

                    row.hidden = !row.hidden;
                    button.textContent = row.hidden ? 'Editar' : 'Cerrar';
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\superadmin\provider-business-line-management.blade.php ENDPATH**/ ?>