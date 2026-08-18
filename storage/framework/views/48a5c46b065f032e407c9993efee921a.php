<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Alta de proveedores']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Alta de proveedores']); ?>
        <section class="panel">
            <div>
                <h2>Nuevo proveedor</h2>
                <p class="fine-print">Los proveedores dados de alta aqui quedan disponibles para las ordenes de compra del comprador asignado.</p>
            </div>

            <form class="stack" method="POST" action="<?php echo e(route('finance.admin.providers.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="grid-3">
                    <label>
                        Comprador
                        <select name="buyer_id" required>
                            <option value="">Seleccionar comprador...</option>
                            <?php $__currentLoopData = $buyers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $buyer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($buyer->id); ?>" <?php if((int) old('buyer_id') === $buyer->id): echo 'selected'; endif; ?>><?php echo e($buyer->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
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
                </div>

                <div class="grid-2">
                    <label>
                        CLABE
                        <input name="clabe" value="<?php echo e(old('clabe')); ?>" maxlength="18" required>
                    </label>
                    <label>
                        Referencia
                        <input name="reference" value="<?php echo e(old('reference')); ?>" placeholder="Referencia bancaria o linea de captura">
                    </label>
                </div>

                <div class="form-actions">
                    <span class="fine-print">La CLABE debe tener 18 digitos.</span>
                    <button class="button primary" type="submit">Guardar proveedor</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Alta de proveedores</h2>
                    <p class="fine-print">Vista consolidada de todos los proveedores dados de alta por compradores.</p>
                </div>
                <form class="toolbar" method="GET" action="<?php echo e(route('finance.admin.providers')); ?>">
                    <input name="q" value="<?php echo e($query); ?>" placeholder="Buscar proveedor...">
                    <a class="button ghost" href="<?php echo e(route('reports.download', 'providers')); ?>">Exportar CSV</a>
                    <a class="button ghost" href="<?php echo e(route('reports.download', 'providers-excel')); ?>">Exportar Excel</a>
                </form>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Acciones</th>
                            <th>Comprador</th>
                            <th>Razon Social</th>
                            <th>RFC</th>
                            <th>Giro</th>
                            <th>Subcategoria</th>
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
                                <td><a class="button ghost small" href="<?php echo e(route('finance.admin.providers.edit', $provider)); ?>">Editar</a></td>
                                <td><?php echo e($provider->buyer?->name ?? 'Sin comprador'); ?></td>
                                <td><?php echo e($provider->business_name); ?></td>
                                <td><?php echo e($provider->rfc); ?></td>
                                <td><?php echo e($provider->business_line); ?></td>
                                <td><?php echo e($provider->businessSubcategory?->name ?? $provider->provider_business_subcategory ?? 'Sin subcategoria'); ?></td>
                                <td><?php echo e($provider->bank); ?></td>
                                <td><?php echo e($provider->account_number); ?></td>
                                <td><?php echo e($provider->clabe); ?></td>
                                <td><?php echo e($provider->reference ?: 'Sin referencia'); ?></td>
                                <td><?php echo e($provider->created_at?->format('d/m/Y')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="11">Aun no hay proveedores registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views/finance/admin/providers.blade.php ENDPATH**/ ?>