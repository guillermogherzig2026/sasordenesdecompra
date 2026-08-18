<?php
    $roleLabels = [
        'buyer' => 'Compras y Suministros',
        'inventory' => 'Control de inventarios',
        'administrative_assistant' => 'Asistente Administrativo',
    ];
    $buyerSubroleLabels = [
        'purchases' => 'Compras',
        'supplies' => 'Suministros',
        'reimbursements' => 'Reembolsos',
    ];
    $createBuyerSubroles = old('buyer_subroles', ['purchases']);
    $createBuyerSubroles = is_array($createBuyerSubroles) ? $createBuyerSubroles : [$createBuyerSubroles];
?>

<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Autorizaciones de usuarios']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Autorizaciones de usuarios']); ?>
        <section class="panel">
            <div>
                <h2>Alta de usuarios y autorizaciones</h2>
                <p class="fine-print">Finanzas puede crear usuarios de Compras y Suministros, inventarios o asistentes administrativos. Al elegir Compras y Suministros se habilita la subcategoria operativa.</p>
            </div>

            <form class="stack" method="POST" action="<?php echo e(route('finance.admin.users.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="grid-3">
                    <label>Nombre<input name="name" value="<?php echo e(old('name')); ?>" required></label>
                    <label>Correo<input name="email" type="email" value="<?php echo e(old('email')); ?>" required></label>
                    <label>Contrasena inicial<input name="password" value="<?php echo e(old('password')); ?>" required></label>
                </div>
                <div class="grid-2">
                    <div class="role-subcategory-stack">
                        <label>Rol
                            <select name="role" class="role-select" required>
                                <?php $__currentLoopData = $roleLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($role); ?>" <?php if(old('role', 'buyer') === $role): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </label>
                        <div class="buyer-subrole-box">
                            <span class="form-label">Subcategoria</span>
                            <div class="checkbox-grid">
                                <?php $__currentLoopData = $buyerSubroleLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subrole => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label>
                                        <input name="buyer_subroles[]" type="checkbox" value="<?php echo e($subrole); ?>" <?php if(in_array($subrole, $createBuyerSubroles, true)): echo 'checked'; endif; ?>>
                                        <?php echo e($label); ?>

                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                    <?php if (isset($component)) { $__componentOriginal669c8dce83bc863f281078d6fceea0dd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal669c8dce83bc863f281078d6fceea0dd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.company-warehouse-selector','data' => ['companies' => $companies,'supplyWarehouses' => $supplyWarehouses]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('company-warehouse-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['companies' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($companies),'supply-warehouses' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplyWarehouses)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal669c8dce83bc863f281078d6fceea0dd)): ?>
<?php $attributes = $__attributesOriginal669c8dce83bc863f281078d6fceea0dd; ?>
<?php unset($__attributesOriginal669c8dce83bc863f281078d6fceea0dd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal669c8dce83bc863f281078d6fceea0dd)): ?>
<?php $component = $__componentOriginal669c8dce83bc863f281078d6fceea0dd; ?>
<?php unset($__componentOriginal669c8dce83bc863f281078d6fceea0dd); ?>
<?php endif; ?>
                    <?php if(false): ?>
                    <div class="companies-box">
                        <div class="company-selector" data-company-selector>
                            <div class="company-selector-header">
                                <label>Empresas y almacenes autorizados</label>
                                <span data-company-count></span>
                            </div>
                            <input class="company-selector-search" type="search" placeholder="Buscar empresa o almacen...">
                            <div class="company-selector-actions">
                                <button class="button ghost small" type="button" data-company-select-all>Todas</button>
                                <button class="button ghost small" type="button" data-company-clear>Limpiar</button>
                            </div>
                            <div class="company-selector-list">
                                <?php $__currentLoopData = $supplyWarehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplyWarehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="company-selector-option supply-warehouse-option with-warehouses" data-company-option style="display:block;height:auto;min-height:76px;overflow:visible;">
                                        <label class="company-selector-main">
                                            <input class="company-checkbox supply-warehouse-checkbox" name="supply_warehouses[]" type="checkbox" value="<?php echo e($supplyWarehouse['key']); ?>" checked>
                                            <span><?php echo e($supplyWarehouse['label']); ?></span>
                                        </label>
                                        <div class="warehouse-selector-list" style="display:flex;flex-wrap:wrap;gap:6px;padding-left:25px;padding-top:6px;min-height:30px;overflow:visible;">
                                            <small class="fine-print">
                                                Surte a:
                                                <?php echo e(collect($supplyWarehouse['companies'])->pluck('name')->implode(', ') ?: 'Sin empresas asignadas'); ?>

                                                <?php if(! empty($supplyWarehouse['address'])): ?>
                                                    · <?php echo e($supplyWarehouse['address']); ?>

                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $warehouseObjects = $company->warehouseObjects(); ?>
                                    <div class="company-selector-option with-warehouses" data-company-option style="display:block;height:auto;min-height:76px;overflow:visible;">
                                        <label class="company-selector-main">
                                            <input class="company-checkbox" name="companies[]" type="checkbox" value="<?php echo e($company->id); ?>" checked>
                                            <span><?php echo e($company->name); ?></span>
                                        </label>
                                        <div class="warehouse-selector-list" style="display:flex;flex-wrap:wrap;gap:6px;padding-left:25px;padding-top:6px;min-height:30px;overflow:visible;">
                                            <?php $__empty_1 = true; $__currentLoopData = $warehouseObjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <label>
                                                    <input name="warehouses[<?php echo e($company->id); ?>][]" type="checkbox" value="<?php echo e($warehouse['name']); ?>" checked>
                                                    <?php echo e($warehouse['name']); ?><?php if($warehouse['short_name']): ?> <span class="fine-print">(<?php echo e($warehouse['short_name']); ?>)</span><?php endif; ?>
                                                </label>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                <span class="fine-print">Sin almacenes registrados.</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="form-actions">
                    <span class="fine-print">Los usuarios nuevos podran iniciar sesion con el correo y contrasena indicados.</span>
                    <button class="button primary" type="submit">Crear usuario</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <h2>Usuarios autorizados</h2>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Subcategoria</th>
                            <th>Empresas</th>
                            <th>Almacenes</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $managedUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php $assignments = $managedUser->normalizedCompanyAssignments(); ?>
                            <tr>
                                <td><?php echo e($managedUser->name); ?></td>
                                <td><?php echo e($managedUser->email); ?></td>
                                <td><?php echo e($roleLabels[$managedUser->role] ?? $managedUser->role); ?></td>
                                <td>
                                    <?php if($managedUser->role === 'buyer'): ?>
                                        <?php echo e($managedUser->buyerSubroleLabel()); ?>

                                    <?php else: ?>
                                        <span class="fine-print">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(in_array($managedUser->role, ['buyer', 'inventory'], true)): ?>
                                        <?php $__empty_2 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                            <div><?php echo e($assignment['name']); ?></div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                            Sin empresas asignadas
                                        <?php endif; ?>
                                    <?php else: ?>
                                        Todas
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(in_array($managedUser->role, ['buyer', 'inventory'], true)): ?>
                                        <?php $__empty_2 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                            <div>
                                                <strong><?php echo e($assignment['name']); ?>:</strong>
                                                <?php if(count($assignment['warehouses'])): ?>
                                                    <?php echo e(implode(', ', $assignment['warehouses'])); ?>

                                                <?php else: ?>
                                                    <span class="fine-print">Sin almacenes</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                            <span class="fine-print">—</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="fine-print">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status <?php echo e($managedUser->active ? 'approved' : 'canceled'); ?>"><?php echo e($managedUser->active ? 'Activo' : 'Inactivo'); ?></span>
                                </td>
                                <td class="row-actions">
                                    <button class="button ghost small editor-toggle" type="button" data-target="finance-editor-<?php echo e($managedUser->id); ?>">Editar</button>
                                    <form class="inline-form" method="POST" action="<?php echo e(route('finance.admin.users.toggle', $managedUser)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button class="button <?php echo e($managedUser->active ? 'danger' : 'primary'); ?> small" type="submit">
                                            <?php echo e($managedUser->active ? 'Desactivar' : 'Activar'); ?>

                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="editor-row" id="finance-editor-<?php echo e($managedUser->id); ?>" hidden>
                                <td colspan="8">
                                    <form class="stack" method="POST" action="<?php echo e(route('finance.admin.users.update', $managedUser)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="grid-3">
                                            <label>Nombre<input name="name" value="<?php echo e($managedUser->name); ?>" required></label>
                                            <label>Correo<input name="email" type="email" value="<?php echo e($managedUser->email); ?>" required></label>
                                            <label>Nueva contrasena<input name="password" type="text" placeholder="Sin cambio"></label>
                                        </div>
                                        <div class="grid-2">
                                            <div class="role-subcategory-stack">
                                                <label>Rol
                                                    <select name="role" class="role-select" required>
                                                        <?php $__currentLoopData = $roleLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($role); ?>" <?php if($managedUser->role === $role): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </label>
                                                <?php $selectedBuyerSubroles = $managedUser->buyerSubroles(); ?>
                                                <div class="buyer-subrole-box">
                                                    <span class="form-label">Subcategoria</span>
                                                    <div class="checkbox-grid">
                                                        <?php $__currentLoopData = $buyerSubroleLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subrole => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <label>
                                                                <input name="buyer_subroles[]" type="checkbox" value="<?php echo e($subrole); ?>" <?php if(in_array($subrole, $selectedBuyerSubroles, true)): echo 'checked'; endif; ?>>
                                                                <?php echo e($label); ?>

                                                            </label>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if (isset($component)) { $__componentOriginal669c8dce83bc863f281078d6fceea0dd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal669c8dce83bc863f281078d6fceea0dd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.company-warehouse-selector','data' => ['companies' => $companies,'supplyWarehouses' => $supplyWarehouses,'managedUser' => $managedUser]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('company-warehouse-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['companies' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($companies),'supply-warehouses' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($supplyWarehouses),'managed-user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($managedUser)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal669c8dce83bc863f281078d6fceea0dd)): ?>
<?php $attributes = $__attributesOriginal669c8dce83bc863f281078d6fceea0dd; ?>
<?php unset($__attributesOriginal669c8dce83bc863f281078d6fceea0dd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal669c8dce83bc863f281078d6fceea0dd)): ?>
<?php $component = $__componentOriginal669c8dce83bc863f281078d6fceea0dd; ?>
<?php unset($__componentOriginal669c8dce83bc863f281078d6fceea0dd); ?>
<?php endif; ?>
                                            <?php if(false): ?>
                                            <div class="companies-box">
                                                <div class="company-selector" data-company-selector>
                                                    <div class="company-selector-header">
                                                        <label>Empresas y almacenes autorizados</label>
                                                        <span data-company-count></span>
                                                    </div>
                                                    <input class="company-selector-search" type="search" placeholder="Buscar empresa o almacen...">
                                                    <div class="company-selector-actions">
                                                        <button class="button ghost small" type="button" data-company-select-all>Todas</button>
                                                        <button class="button ghost small" type="button" data-company-clear>Limpiar</button>
                                                    </div>
                                                    <div class="company-selector-list">
                                                        <?php
                                                            $selectedSupplyWarehouseKeys = collect($supplyWarehouses)
                                                                ->filter(fn (array $supplyWarehouse) => collect($supplyWarehouse['companies'])->contains(fn (array $company) => in_array($supplyWarehouse['label'], $managedUser->authorizedWarehousesFor($company['name']), true)))
                                                                ->pluck('key')
                                                                ->all();
                                                        ?>
                                                        <?php $__currentLoopData = $supplyWarehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplyWarehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <div class="company-selector-option supply-warehouse-option with-warehouses" data-company-option style="display:block;height:auto;min-height:76px;overflow:visible;">
                                                                <label class="company-selector-main">
                                                                    <input class="company-checkbox supply-warehouse-checkbox" name="supply_warehouses[]" type="checkbox" value="<?php echo e($supplyWarehouse['key']); ?>" <?php if(in_array($supplyWarehouse['key'], $selectedSupplyWarehouseKeys, true)): echo 'checked'; endif; ?>>
                                                                    <span><?php echo e($supplyWarehouse['label']); ?></span>
                                                                </label>
                                                                <div class="warehouse-selector-list" style="display:flex;flex-wrap:wrap;gap:6px;padding-left:25px;padding-top:6px;min-height:30px;overflow:visible;">
                                                                    <small class="fine-print">
                                                                        Surte a:
                                                                        <?php echo e(collect($supplyWarehouse['companies'])->pluck('name')->implode(', ') ?: 'Sin empresas asignadas'); ?>

                                                                        <?php if(! empty($supplyWarehouse['address'])): ?>
                                                                            · <?php echo e($supplyWarehouse['address']); ?>

                                                                        <?php endif; ?>
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <?php
                                                                $selectedCompanies = $managedUser->authorizedCompanyNames();
                                                                $selectedWarehouses = $managedUser->authorizedWarehousesFor($company->name);
                                                                $warehouseObjects = $company->warehouseObjects();
                                                                $companySelected = in_array($company->name, $selectedCompanies, true);
                                                            ?>
                                                            <div class="company-selector-option with-warehouses" data-company-option style="display:block;height:auto;min-height:76px;overflow:visible;">
                                                                <label class="company-selector-main">
                                                                    <input class="company-checkbox" name="companies[]" type="checkbox" value="<?php echo e($company->id); ?>" <?php if($companySelected): echo 'checked'; endif; ?>>
                                                                    <span><?php echo e($company->name); ?></span>
                                                                </label>
                                                                <div class="warehouse-selector-list" style="display:flex;flex-wrap:wrap;gap:6px;padding-left:25px;padding-top:6px;min-height:30px;overflow:visible;">
                                                                    <?php $__empty_2 = true; $__currentLoopData = $warehouseObjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                                                        <label>
                                                                            <input name="warehouses[<?php echo e($company->id); ?>][]" type="checkbox" value="<?php echo e($warehouse['name']); ?>" <?php if(empty($selectedWarehouses) ? $companySelected : in_array($warehouse['name'], $selectedWarehouses, true)): echo 'checked'; endif; ?>>
                                                                            <?php echo e($warehouse['name']); ?><?php if($warehouse['short_name']): ?> <span class="fine-print">(<?php echo e($warehouse['short_name']); ?>)</span><?php endif; ?>
                                                                        </label>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                                        <span class="fine-print">Sin almacenes registrados.</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-actions">
                                            <span class="fine-print">Deja la contrasena vacia para conservar la actual.</span>
                                            <button class="button primary small" type="submit">Guardar cambios</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="8">No hay usuarios autorizados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <script>
            document.querySelectorAll('.role-select').forEach((select) => {
                const scope = select.closest('form') || document;
                const box = scope.querySelector('.companies-box');
                const subroleBox = scope.querySelector('.buyer-subrole-box');
                const sync = () => {
                    if (box) box.style.display = ['buyer', 'inventory'].includes(select.value) ? 'block' : 'none';
                    if (subroleBox) subroleBox.style.display = select.value === 'buyer' ? 'grid' : 'none';
                };
                select.addEventListener('change', sync);
                sync();
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views/finance/admin/users.blade.php ENDPATH**/ ?>