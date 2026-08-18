<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Alta de proveedor']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Alta de proveedor']); ?>
        <section class="panel">
            <div>
                <h2>Nuevo proveedor</h2>
                <p class="fine-print"><?php echo e($constructionContext ? 'Los proveedores dados de alta aqui quedan disponibles para las ordenes de compra de Administracion de obra.' : 'Los proveedores dados de alta aqui quedan disponibles para tus ordenes de compra.'); ?></p>
            </div>

            <form class="stack" method="POST" action="<?php echo e(route($providerRoutePrefix.'.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="grid-3">
                    <label>
                        Razon social
                        <input name="business_name" value="<?php echo e(old('business_name')); ?>" required>
                    </label>
                    <label>
                        RFC
                        <input name="rfc" value="<?php echo e(old('rfc')); ?>" required>
                    </label>
                </div>
                <div class="grid-4">
                    <label>
                        Giro de proveeduria
                        <select name="business_line_id" data-provider-line-select required>
                            <?php $__currentLoopData = $businessLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($line->id); ?>" <?php if((int) old('business_line_id') === $line->id): echo 'selected'; endif; ?>><?php echo e($line->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                    <label>
                        Subcategoria
                        <select name="business_subcategory_id" data-provider-subcategory-select>
                            <option value="">Sin subcategoria</option>
                            <?php $__currentLoopData = $businessLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $__currentLoopData = $line->subcategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subcategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($subcategory->id); ?>" data-line-id="<?php echo e($line->id); ?>" <?php if((int) old('business_subcategory_id') === $subcategory->id): echo 'selected'; endif; ?>><?php echo e($subcategory->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                    <label>
                        Banco
                        <input name="bank" value="<?php echo e(old('bank')); ?>" required>
                    </label>
                    <label>
                        Cuenta
                        <input name="account_number" value="<?php echo e(old('account_number')); ?>" required>
                    </label>
                    <label>
                        CLABE
                        <input name="clabe" value="<?php echo e(old('clabe')); ?>" maxlength="18" required>
                    </label>
                </div>
                <label>
                    Referencia
                    <input name="reference" value="<?php echo e(old('reference')); ?>" placeholder="Referencia bancaria o linea de captura">
                </label>
                <div class="form-actions">
                    <span class="fine-print">La CLABE debe tener 18 digitos.</span>
                    <button class="button primary" type="submit">Guardar proveedor</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Mis proveedores</h2>
                    <p class="fine-print"><?php echo e($constructionContext ? 'Proveedores disponibles para las ordenes de compra de Administracion de obra.' : 'Catalogo propio del comprador autenticado.'); ?></p>
                </div>
                <form class="toolbar" method="GET" action="<?php echo e(route($providerRoutePrefix.'.index')); ?>">
                    <input name="q" value="<?php echo e($query); ?>" placeholder="Buscar proveedor">
                    <button class="button ghost" type="submit">Buscar</button>
                </form>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Razon social</th>
                            <th>RFC</th>
                            <th>Giro</th>
                            <th>Subcategoria</th>
                            <th>Banco</th>
                            <th>Cuenta</th>
                            <th>CLABE</th>
                            <th>Referencia</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $providers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $provider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><strong><?php echo e($provider->business_name); ?></strong></td>
                                <td><?php echo e($provider->rfc); ?></td>
                                <td><?php echo e($provider->business_line); ?></td>
                                <td><?php echo e($provider->businessSubcategory?->name ?? $provider->provider_business_subcategory ?? 'Sin subcategoria'); ?></td>
                                <td><?php echo e($provider->bank); ?></td>
                                <td><?php echo e($provider->account_number); ?></td>
                                <td><?php echo e($provider->clabe); ?></td>
                                <td><?php echo e($provider->reference ?: 'Sin referencia'); ?></td>
                                <td>
                                    <button class="button ghost small editor-toggle" type="button" data-target="provider-editor-<?php echo e($provider->id); ?>">Editar</button>
                                </td>
                            </tr>
                            <tr class="editor-row" id="provider-editor-<?php echo e($provider->id); ?>" hidden>
                                <td colspan="9">
                                    <form class="stack" method="POST" action="<?php echo e(route($providerRoutePrefix.'.update', $provider)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="grid-4">
                                            <label>Razon social<input name="business_name" value="<?php echo e(old('business_name', $provider->business_name)); ?>" required></label>
                                            <label>RFC<input name="rfc" value="<?php echo e(old('rfc', $provider->rfc)); ?>" required></label>
                                            <label>Giro de proveeduria
                                                <select name="business_line_id" data-provider-line-select required>
                                                    <?php $__currentLoopData = $businessLines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <option value="<?php echo e($line->id); ?>" <?php if((int) old('business_line_id', $provider->provider_business_line_id) === $line->id || (! $provider->provider_business_line_id && $provider->business_line === $line->name)): echo 'selected'; endif; ?>><?php echo e($line->name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </label>
                                            <label>Subcategoria
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
                                            <label>Banco<input name="bank" value="<?php echo e(old('bank', $provider->bank)); ?>" required></label>
                                            <label>Cuenta<input name="account_number" value="<?php echo e(old('account_number', $provider->account_number)); ?>" required></label>
                                            <label>CLABE<input name="clabe" value="<?php echo e(old('clabe', $provider->clabe)); ?>" maxlength="18" required></label>
                                        </div>
                                        <label>Referencia<input name="reference" value="<?php echo e(old('reference', $provider->reference)); ?>" placeholder="Referencia bancaria o linea de captura"></label>
                                        <div class="form-actions">
                                            <span class="fine-print">La CLABE debe tener 18 digitos.</span>
                                            <button class="button primary small" type="submit">Guardar cambios</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="9">No hay proveedores registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <script>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views/buyer/providers/index.blade.php ENDPATH**/ ?>