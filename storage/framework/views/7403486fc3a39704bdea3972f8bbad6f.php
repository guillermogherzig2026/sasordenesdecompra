<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Proveedores']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Proveedores']); ?>
        <details class="panel provider-catalog-panel">
            <summary class="provider-catalog-summary">
                <div>
                    <h2>Catalogo de proveedores</h2>
                    <p class="fine-print">Consulta todos los proveedores dados de alta en el sistema.</p>
                </div>
                <span class="button ghost provider-catalog-toggle" aria-hidden="true"></span>
            </summary>

            <div class="table-scroll provider-catalog-list">
                <table>
                    <thead>
                        <tr>
                            <th>Razon social</th>
                            <th>RFC</th>
                            <th>Categoria</th>
                            <th>Subcategoria</th>
                            <th>Comprador</th>
                            <th>Banco</th>
                            <th>Cuenta</th>
                            <th>CLABE</th>
                            <th>Referencia</th>
                            <th>Fecha alta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $providers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><strong><?php echo e($provider->business_name); ?></strong></td>
                                <td><?php echo e($provider->rfc); ?></td>
                                <td><?php echo e($provider->businessLine?->name ?? $provider->business_line); ?></td>
                                <td><?php echo e($provider->businessSubcategory?->name ?? $provider->provider_business_subcategory ?? 'Sin subcategoria'); ?></td>
                                <td><?php echo e($provider->buyer?->name ?? 'Sin comprador'); ?></td>
                                <td><?php echo e($provider->bank); ?></td>
                                <td><?php echo e($provider->account_number); ?></td>
                                <td><?php echo e($provider->clabe); ?></td>
                                <td><?php echo e($provider->reference ?: 'Sin referencia'); ?></td>
                                <td><?php echo e($provider->created_at?->format('d/m/Y')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="10" class="empty-state">No hay proveedores registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </details>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Categorias de proveedores</h2>
                    <a class="button primary small" href="<?php echo e(route('superadmin.provider-lines.manage')); ?>">Administrar categorias</a>
                </div>
            </div>

            <div class="table-scroll" id="provider-category-management">
                <table>
                    <thead>
                        <tr>
                            <th>Categoria</th>
                            <th>Proveedores</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="provider-line-row">
                                <td>
                                    <span class="provider-category-cell">
                                        <button class="button ghost small provider-line-toggle" type="button" data-provider-line-toggle="<?php echo e($line->id); ?>" aria-expanded="false">+</button>
                                        <strong><?php echo e($line->name); ?></strong>
                                    </span>
                                </td>
                                <td>
                                    <button class="button ghost small" type="button" data-supply-detail-open="providers-line-<?php echo e($line->id); ?>">
                                        Ver proveedores (<?php echo e($line->providers_count); ?>)
                                    </button>
                                </td>
                                <td>
                                    <span class="status <?php echo e($line->active ? 'approved' : 'canceled'); ?>"><?php echo e($line->active ? 'Activo' : 'Inactivo'); ?></span>
                                </td>
                                <td class="row-actions">
                                    <button class="button ghost small editor-toggle" type="button" data-target="line-editor-<?php echo e($line->id); ?>">Editar</button>
                                    <form class="inline-form" method="POST" action="<?php echo e(route('superadmin.provider-lines.destroy', $line)); ?>" onsubmit="return confirm('Estas seguro que quieres eliminar <?php echo e($line->name); ?>?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button class="button danger small" type="submit" <?php if($line->providers_count > 0): echo 'disabled'; endif; ?>>Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            <?php $__currentLoopData = $line->subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="provider-subcategory-row" data-parent-line="<?php echo e($line->id); ?>" hidden>
                                    <td>
                                        <span class="provider-subcategory-cell"><?php echo e($subcategory->name); ?></span>
                                    </td>
                                    <td>
                                        <button class="button ghost small" type="button" data-supply-detail-open="providers-subcategory-<?php echo e($subcategory->id); ?>">
                                            Ver proveedores (<?php echo e($subcategory->providers_count); ?>)
                                        </button>
                                    </td>
                                    <td>
                                        <span class="status <?php echo e($subcategory->active ? 'approved' : 'canceled'); ?>"><?php echo e($subcategory->active ? 'Activo' : 'Inactivo'); ?></span>
                                    </td>
                                    <td class="row-actions">
                                        <button class="button ghost small editor-toggle" type="button" data-target="subcategory-editor-<?php echo e($subcategory->id); ?>">Editar</button>
                                        <form class="inline-form" method="POST" action="<?php echo e(route('superadmin.provider-lines.subcategories.destroy', $subcategory)); ?>" onsubmit="return confirm('Estas seguro que quieres eliminar <?php echo e($subcategory->name); ?>?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button class="button danger small" type="submit" <?php if($subcategory->providers_count > 0): echo 'disabled'; endif; ?>>Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr class="editor-row provider-subcategory-row" id="subcategory-editor-<?php echo e($subcategory->id); ?>" data-parent-line="<?php echo e($line->id); ?>" hidden>
                                    <td colspan="4">
                                        <form class="stack" method="POST" action="<?php echo e(route('superadmin.provider-lines.subcategories.update', $subcategory)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PUT'); ?>
                                            <div class="grid-3">
                                                <label>Subcategoria<input name="name" value="<?php echo e(old('name', $subcategory->name)); ?>" required></label>
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
                            <tr class="provider-subcategory-row provider-subcategory-create-row" data-parent-line="<?php echo e($line->id); ?>" hidden>
                                <td colspan="4">
                                    <form class="provider-subcategory-create-form" method="POST" action="<?php echo e(route('superadmin.provider-lines.subcategories.store', $line)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <label>
                                            Nueva subcategoria
                                            <input name="name" placeholder="Ej: Medicamentos" required>
                                        </label>
                                        <button class="button primary small" type="submit">Agregar subcategoria</button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="editor-row" id="line-editor-<?php echo e($line->id); ?>" hidden>
                                <td colspan="4">
                                    <form class="stack" method="POST" action="<?php echo e(route('superadmin.provider-lines.update', $line)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="grid-3">
                                            <label>Categoria<input name="name" value="<?php echo e(old('name', $line->name)); ?>" required></label>
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
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="empty-state">No hay categorias registradas.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php $__currentLoopData = $lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <dialog class="supply-detail-dialog provider-line-dialog" id="providers-line-<?php echo e($line->id); ?>" data-supply-detail-dialog>
                    <div class="supply-detail-card">
                        <button class="supply-detail-close" type="button" data-supply-detail-close aria-label="Cerrar">x</button>
                        <div>
                            <h3>Proveedores de <?php echo e($line->name); ?></h3>
                            <p class="fine-print"><?php echo e($line->providers_count); ?> proveedores registrados en esta categoria.</p>
                        </div>

                        <div class="table-scroll provider-line-list">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Razon social</th>
                                        <th>RFC</th>
                                        <th>Comprador</th>
                                        <th>Banco</th>
                                        <th>Cuenta</th>
                                        <th>CLABE</th>
                                        <th>Referencia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $line->providers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><strong><?php echo e($provider->business_name); ?></strong></td>
                                            <td><?php echo e($provider->rfc); ?></td>
                                            <td><?php echo e($provider->buyer?->name ?? 'Sin comprador'); ?></td>
                                            <td><?php echo e($provider->bank); ?></td>
                                            <td><?php echo e($provider->account_number); ?></td>
                                            <td><?php echo e($provider->clabe); ?></td>
                                            <td><?php echo e($provider->reference ?: 'Sin referencia'); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="7" class="empty-state">No hay proveedores registrados en esta categoria.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </dialog>

                <?php $__currentLoopData = $line->subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <dialog class="supply-detail-dialog provider-line-dialog" id="providers-subcategory-<?php echo e($subcategory->id); ?>" data-supply-detail-dialog>
                        <div class="supply-detail-card">
                            <button class="supply-detail-close" type="button" data-supply-detail-close aria-label="Cerrar">x</button>
                            <div>
                                <h3>Proveedores de <?php echo e($subcategory->name); ?></h3>
                                <p class="fine-print"><?php echo e($subcategory->providers_count); ?> proveedores registrados en esta subcategoria.</p>
                            </div>

                            <div class="table-scroll provider-line-list">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Razon social</th>
                                            <th>RFC</th>
                                            <th>Comprador</th>
                                            <th>Banco</th>
                                            <th>Cuenta</th>
                                            <th>CLABE</th>
                                            <th>Referencia</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__empty_1 = true; $__currentLoopData = $subcategory->providers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <tr>
                                                <td><strong><?php echo e($provider->business_name); ?></strong></td>
                                                <td><?php echo e($provider->rfc); ?></td>
                                                <td><?php echo e($provider->buyer?->name ?? 'Sin comprador'); ?></td>
                                                <td><?php echo e($provider->bank); ?></td>
                                                <td><?php echo e($provider->account_number); ?></td>
                                                <td><?php echo e($provider->clabe); ?></td>
                                                <td><?php echo e($provider->reference ?: 'Sin referencia'); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <tr>
                                                <td colspan="7" class="empty-state">No hay proveedores registrados en esta subcategoria.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </dialog>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </section>

        <script>
            document.querySelectorAll('[data-provider-line-toggle]').forEach((button) => {
                button.addEventListener('click', () => {
                    const parentId = button.dataset.providerLineToggle;
                    const expanded = button.getAttribute('aria-expanded') === 'true';

                    document.querySelectorAll(`[data-parent-line="${parentId}"]`).forEach((row) => {
                        row.hidden = true;
                        if (row.classList.contains('editor-row')) {
                            const editorButton = document.querySelector(`[data-target="${row.id}"]`);
                            if (editorButton) editorButton.textContent = 'Editar';
                        }
                    });

                    if (!expanded) {
                        document.querySelectorAll(`[data-parent-line="${parentId}"]:not(.editor-row)`).forEach((row) => {
                            row.hidden = false;
                        });
                    }

                    button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                    button.textContent = expanded ? '+' : '-';
                });
            });

            document.querySelectorAll('.editor-toggle').forEach((button) => {
                button.addEventListener('click', () => {
                    const row = document.getElementById(button.dataset.target);
                    if (!row) return;

                    const isHidden = row.hasAttribute('hidden');
                    row.toggleAttribute('hidden', !isHidden);
                    button.textContent = isHidden ? 'Cerrar' : 'Editar';
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\superadmin\provider-business-lines.blade.php ENDPATH**/ ?>