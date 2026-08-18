<?php
    $roleLabels = [
        'superadmin' => 'Super Administrador',
        'finance' => 'Finanzas',
        'buyer' => 'Compras y Suministros',
        'inventory' => 'Control de inventarios',
        'services' => 'Servicios',
        'administrative_assistant' => 'Asistente Administrativo',
    ];
    $roleDescriptions = [
        'superadmin' => 'Acceso total al sistema: usuarios, roles, empresas, catalogos, finanzas, compras, inventarios, servicios y auditoria.',
        'finance' => 'Gestiona autorizaciones, empresas, proveedores, pagos, ordenes de compra, ordenes de suministro y ordenes de reembolso.',
        'buyer' => 'Crea y da seguimiento a solicitudes segun sus subcategorias asignadas: compras, suministros y/o reembolsos.',
        'inventory' => 'Administra almacenes e inventarios, registra recepciones, controla existencias y procesa entregas de suministros.',
        'services' => 'Registra servicios, consulta catalogos de servicios y da seguimiento a soportes operativos relacionados.',
        'administrative_assistant' => 'Apoya en actividades administrativas como alta y seguimiento de servicios, sin permisos financieros completos.',
    ];
    $buyerSubroleLabels = [
        'purchases' => 'Compras',
        'supplies' => 'Suministros',
        'reimbursements' => 'Reembolsos',
    ];

    $selectedRole = $filters['role'] ?? '';
    $selectedStatus = $filters['status'] ?? '';
    $query = $filters['q'] ?? '';
    $createBuyerSubroles = old('buyer_subroles', ['purchases']);
    $createBuyerSubroles = is_array($createBuyerSubroles) ? $createBuyerSubroles : [$createBuyerSubroles];
?>

<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Usuarios y roles']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Usuarios y roles']); ?>
        <div class="metrics-grid compact-metrics">
            <article class="metric-card">
                <span>Total usuarios</span>
                <strong><?php echo e($totalUsers); ?></strong>
                <small>Cuentas registradas</small>
            </article>
            <article class="metric-card">
                <span>Activos</span>
                <strong><?php echo e($activeUsers); ?></strong>
                <small>Con acceso permitido</small>
            </article>
            <article class="metric-card">
                <span>Inactivos</span>
                <strong><?php echo e($inactiveUsers); ?></strong>
                <small>Acceso bloqueado</small>
            </article>
            <article class="metric-card">
                <span>Resultado actual</span>
                <strong><?php echo e($users->total()); ?></strong>
                <small>Usuarios con los filtros</small>
            </article>
        </div>

        <details class="panel compact-create" <?php if($errors->any()): ?> open <?php endif; ?>>
            <summary>
                <span>
                    <strong>Crear usuario</strong>
                    <small>Crea la cuenta y asigna su rol inicial.</small>
                </span>
                <span class="button primary small">Nuevo usuario</span>
            </summary>

            <form class="stack" method="POST" action="<?php echo e(route('superadmin.users.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="grid-3">
                    <label>Nombre<input name="name" value="<?php echo e(old('name')); ?>" required></label>
                    <label>Correo<input name="email" type="email" value="<?php echo e(old('email')); ?>" required></label>
                    <label>Contrasena inicial<input name="password" value="<?php echo e(old('password')); ?>" required></label>
                </div>
                <div class="role-subcategory-stack">
                    <label>Rol
                        <select name="role" class="role-select" required>
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($role); ?>" <?php if(old('role', 'buyer') === $role): echo 'selected'; endif; ?>><?php echo e($roleLabels[$role]); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </label>
                    <div class="role-capabilities" data-role-capabilities>
                        <span class="form-label">Guia rapida de capacidades por rol</span>
                        <div class="role-capability-list">
                            <?php $__currentLoopData = $roleDescriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role => $description): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <article class="role-capability-card" data-role-card="<?php echo e($role); ?>">
                                    <strong><?php echo e($roleLabels[$role]); ?></strong>
                                    <span><?php echo e($description); ?></span>
                                </article>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
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
                                            <small class="fine-print">Sin almacenes registrados</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <span class="fine-print">Compras y Suministros e inventarios pueden limitarse por empresa y almacen.</span>
                    <button class="button primary" type="submit">Crear usuario</button>
                </div>
            </form>
        </details>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Usuarios del sistema</h2>
                    <p class="fine-print">Busca, filtra y edita solo cuando lo necesites.</p>
                </div>
            </div>

            <form class="user-filters" method="GET" action="<?php echo e(route('superadmin.users.index')); ?>">
                <label>
                    Buscar usuario
                    <input name="q" value="<?php echo e($query); ?>" placeholder="Nombre o correo">
                </label>
                <label>
                    Rol
                    <select name="role">
                        <option value="">Todos los roles</option>
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($role); ?>" <?php if($selectedRole === $role): echo 'selected'; endif; ?>><?php echo e($roleLabels[$role]); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </label>
                <label>
                    Estado
                    <select name="status">
                        <option value="">Todos</option>
                        <option value="active" <?php if($selectedStatus === 'active'): echo 'selected'; endif; ?>>Activos</option>
                        <option value="inactive" <?php if($selectedStatus === 'inactive'): echo 'selected'; endif; ?>>Inactivos</option>
                    </select>
                </label>
                <div class="filter-actions">
                    <button class="button primary" type="submit">Filtrar</button>
                    <a class="button ghost" href="<?php echo e(route('superadmin.users.index')); ?>">Limpiar</a>
                </div>
            </form>

            <div class="table-scroll">
                <table class="compact-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Contrasena</th>
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
                            <tr>
                                <td>
                                    <strong><?php echo e($managedUser->name); ?></strong>
                                    <small><?php echo e($managedUser->email); ?></small>
                                </td>
                                <td>
                                    <?php if($managedUser->plain_password): ?>
                                        <strong><?php echo e($managedUser->plain_password); ?></strong>
                                    <?php else: ?>
                                        <span class="fine-print">No registrada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="role-chip"><?php echo e($roleLabels[$managedUser->role] ?? $managedUser->role); ?></span>
                                </td>
                                <td>
                                    <?php if($managedUser->role === 'buyer'): ?>
                                        <?php echo e($managedUser->buyerSubroleLabel()); ?>

                                    <?php else: ?>
                                        <span class="fine-print">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(in_array($managedUser->role, ['buyer', 'inventory'], true)): ?>
                                        <?php $assignments = $managedUser->normalizedCompanyAssignments(); ?>
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
                                        <?php $assignments = $managedUser->normalizedCompanyAssignments(); ?>
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
                                    <button class="button ghost small editor-toggle" type="button" data-target="editor-<?php echo e($managedUser->id); ?>">Editar</button>

                                    <?php if($managedUser->id !== auth()->id()): ?>
                                        <form class="inline-form" method="POST" action="<?php echo e(route('superadmin.users.toggle', $managedUser)); ?>">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <button class="button <?php echo e($managedUser->active ? 'danger' : 'primary'); ?> small" type="submit">
                                                <?php echo e($managedUser->active ? 'Desactivar' : 'Activar'); ?>

                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <small class="fine-print">Cuenta actual</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr class="editor-row" id="editor-<?php echo e($managedUser->id); ?>" hidden>
                                <td colspan="8">
                                    <form class="stack" method="POST" action="<?php echo e(route('superadmin.users.update', $managedUser)); ?>">
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
                                                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($role); ?>" <?php if($managedUser->role === $role): echo 'selected'; endif; ?>><?php echo e($roleLabels[$role]); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </label>
                                                <div class="role-capabilities" data-role-capabilities>
                                                    <span class="form-label">Guia rapida de capacidades por rol</span>
                                                    <div class="role-capability-list">
                                                        <?php $__currentLoopData = $roleDescriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role => $description): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <article class="role-capability-card" data-role-card="<?php echo e($role); ?>">
                                                                <strong><?php echo e($roleLabels[$role]); ?></strong>
                                                                <span><?php echo e($description); ?></span>
                                                            </article>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                </div>
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
                                                                        <small class="fine-print">Sin almacenes registrados</small>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </div>
                                                </div>
                                            </div>
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
                                <td colspan="8" class="empty-state">No hay usuarios con esos filtros.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if($users->hasPages()): ?>
                <div class="pagination-bar">
                    <span>Pagina <?php echo e($users->currentPage()); ?> de <?php echo e($users->lastPage()); ?></span>
                    <div class="item-actions">
                        <?php if($users->onFirstPage()): ?>
                            <span class="button ghost small disabled">Anterior</span>
                        <?php else: ?>
                            <a class="button ghost small" href="<?php echo e($users->previousPageUrl()); ?>">Anterior</a>
                        <?php endif; ?>

                        <?php if($users->hasMorePages()): ?>
                            <a class="button ghost small" href="<?php echo e($users->nextPageUrl()); ?>">Siguiente</a>
                        <?php else: ?>
                            <span class="button ghost small disabled">Siguiente</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <script>
            document.querySelectorAll('.role-select').forEach((select) => {
                const scope = select.closest('form') || document;
                const box = scope.querySelector('.companies-box');
                const subroleBox = scope.querySelector('.buyer-subrole-box');
                const roleCards = scope.querySelectorAll('[data-role-card]');
                const sync = () => {
                    if (box) box.style.display = ['buyer', 'inventory'].includes(select.value) ? 'block' : 'none';
                    if (subroleBox) subroleBox.style.display = select.value === 'buyer' ? 'grid' : 'none';
                    roleCards.forEach((card) => card.classList.toggle('is-active', card.dataset.roleCard === select.value));
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


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\superadmin\users.blade.php ENDPATH**/ ?>