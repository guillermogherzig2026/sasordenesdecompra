<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Mano de obra']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Mano de obra']); ?>
        <?php
            $activeProjects = $projects->where('status', 'En ejecucion')->values();

            if ($activeProjects->isEmpty()) {
                $activeProjects = $projects->values();
            }

            $selectedProjectId = (int) ($selectedProjectId ?? $activeProjects->first()?->id);

            if (! $activeProjects->contains('id', $selectedProjectId)) {
                $selectedProjectId = (int) $activeProjects->first()?->id;
            }

            $money = fn ($value) => '$'.number_format((float) $value, 2);
            $payrollRows = collect($catalogRows)->where('type', 'Nomina')->values();
            $estimateRows = collect($catalogRows)->where('type', 'Estimacion')->values();
            $selectedProjectName = $activeProjects->firstWhere('id', $selectedProjectId)?->name ?? 'Sin obra seleccionada';
            $payrollFormContext = old('payroll_form');
            $creatingPayroll = $payrollFormContext === 'create';
            $estimateFormContext = old('estimate_form');
            $creatingEstimate = $estimateFormContext === 'create';
            $invalidPayrollDialogId = null;

            if ($payrollFormContext === 'create') {
                $invalidPayrollDialogId = 'new-payroll-dialog';
            } elseif ($estimateFormContext === 'create') {
                $invalidPayrollDialogId = 'new-estimate-dialog';
            }
        ?>

        <section class="panel construction-carousel-panel" data-construction-carousel>
            <div class="construction-carousel-header">
                <div class="construction-carousel-title">
                    <span class="construction-carousel-count"><?php echo e($activeProjects->count()); ?></span>
                    <h2>Obras activas</h2>
                </div>
                <a class="button ghost small" href="<?php echo e(route('construction.dashboard')); ?>">Atras</a>
            </div>

            <div class="construction-carousel-shell">
                <button class="construction-carousel-nav" type="button" data-carousel-prev aria-label="Anterior">&lt;</button>

                <div class="construction-carousel-track" data-construction-carousel-track>
                    <?php $__empty_1 = true; $__currentLoopData = $activeProjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $isSelectedProject = $project->id === $selectedProjectId;
                        ?>
                        <button class="construction-project-tile <?php echo e($isSelectedProject ? 'active' : ''); ?>" type="button" data-labor-project data-project-id="<?php echo e($project->id); ?>" data-project-name="<?php echo e($project->name); ?>" aria-pressed="<?php echo e($isSelectedProject ? 'true' : 'false'); ?>">
                            <span class="construction-project-avatar">
                                <?php if($project->photo_path): ?>
                                    <img src="<?php echo e($project->photo_path); ?>" alt="">
                                <?php else: ?>
                                    <?php echo e(substr($project->project_key, -2)); ?>

                                <?php endif; ?>
                            </span>
                            <span class="construction-project-key"><?php echo e($project->project_key); ?></span>
                            <strong class="construction-project-name"><?php echo e($project->name); ?></strong>
                            <span class="construction-project-status"><span></span><?php echo e($project->status); ?></span>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <button class="construction-project-tile" type="button" disabled>
                            <span class="construction-project-avatar">OB</span>
                            <span class="construction-project-key">Sin obras</span>
                            <strong class="construction-project-name">No hay obras visibles</strong>
                            <span class="construction-project-status"><span></span>Pendiente</span>
                        </button>
                    <?php endif; ?>
                </div>

                <button class="construction-carousel-nav" type="button" data-carousel-next aria-label="Siguiente">&gt;</button>
            </div>
        </section>

        <section class="panel labor-budget-panel" data-no-section-export>
            <div class="construction-carousel-title">
                <span class="construction-carousel-count">2</span>
                <h2>Presupuestos</h2>
            </div>

            <div class="labor-budget-grid">
                <button class="labor-budget-card" type="button" data-labor-catalog-toggle="payroll" aria-expanded="false" aria-controls="labor-payroll-catalog">
                    <span class="labor-budget-icon">NOM</span>
                    <strong>Nomina</strong>
                    <span class="labor-budget-toggle" data-labor-catalog-indicator aria-hidden="true">+</span>
                </button>
                <button class="labor-budget-card" type="button" data-labor-catalog-toggle="estimates" aria-expanded="false" aria-controls="labor-estimate-catalog">
                    <span class="labor-budget-icon">EST</span>
                    <strong>Estimaciones</strong>
                    <span class="labor-budget-toggle" data-labor-catalog-indicator aria-hidden="true">+</span>
                </button>
            </div>

            <div class="labor-payroll-catalog" id="labor-payroll-catalog" data-labor-catalog="payroll" hidden>
                <div class="labor-payroll-header">
                    <div>
                        <h3>Cat&aacute;logo de n&oacute;minas</h3>
                        <p>N&oacute;minas de la obra seleccionada: <strong data-selected-project-name><?php echo e($selectedProjectName); ?></strong></p>
                    </div>
                    <div class="labor-payroll-actions">
                        <button class="button primary small" type="button" data-payroll-create data-supply-detail-open="new-payroll-dialog" <?php if($activeProjects->isEmpty()): echo 'disabled'; endif; ?>>
                            Nueva nomina periodica
                        </button>
                        <button class="labor-catalog-collapse" type="button" data-labor-catalog-close="payroll" title="Ocultar catalogo" aria-label="Ocultar catalogo de nominas">&minus;</button>
                    </div>
                </div>

                <div class="table-scroll labor-catalog-scroll">
                    <table class="labor-catalog-table">
                        <thead>
                            <tr>
                                <th>Codigo</th>
                                <th>Contratista</th>
                                <th>Descripcion</th>
                                <th>Area / categoria</th>
                                <th>Periodicidad</th>
                                <th>Monto presupuestado</th>
                                <th>Monto erogado</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $payrollRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr data-payroll-row data-payroll-project="<?php echo e($row['project_id']); ?>" <?php if((int) $row['project_id'] !== $selectedProjectId): ?> hidden <?php endif; ?>>
                                    <td><?php echo e($row['code']); ?></td>
                                    <td><?php echo e($row['responsible']); ?></td>
                                    <td><?php echo e($row['description']); ?></td>
                                    <td><span class="labor-area-badge"><?php echo e($row['area']); ?></span></td>
                                    <td><?php echo e($row['periodicity']); ?></td>
                                    <td><?php echo e($money($row['amount'])); ?></td>
                                    <td data-disbursed-amount="<?php echo e(number_format($row['disbursed_amount'], 2, '.', '')); ?>"><?php echo e($money($row['disbursed_amount'])); ?></td>
                                    <td><span class="status <?php echo e($row['status_class']); ?>"><?php echo e($row['status']); ?></span></td>
                                    <td>
                                        <div class="labor-file-actions">
                                            <a class="button ghost small" href="<?php echo e(route('construction.payrolls.edit', $row['id'])); ?>">Editar</a>
                                            <button
                                                class="button danger small"
                                                type="button"
                                                data-labor-delete
                                                data-labor-delete-url="<?php echo e($row['delete_url']); ?>"
                                            >Eliminar</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr data-payroll-empty <?php if($payrollRows->contains(fn ($row) => (int) $row['project_id'] === $selectedProjectId)): ?> hidden <?php endif; ?>>
                                <td class="empty-state" colspan="9">No hay nominas registradas para esta obra.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="labor-payroll-catalog labor-estimate-catalog" id="labor-estimate-catalog" data-labor-catalog="estimates" hidden>
                <div class="labor-payroll-header">
                    <div>
                        <h3>Cat&aacute;logo de estimaciones</h3>
                        <p>Estimaciones de la obra seleccionada: <strong data-selected-project-name><?php echo e($selectedProjectName); ?></strong></p>
                    </div>
                    <div class="labor-payroll-actions">
                        <button class="button primary small" type="button" data-estimate-create data-supply-detail-open="new-estimate-dialog" <?php if($activeProjects->isEmpty()): echo 'disabled'; endif; ?>>
                            Nuevo paquete de estimaciones
                        </button>
                        <button class="labor-catalog-collapse" type="button" data-labor-catalog-close="estimates" title="Ocultar catalogo" aria-label="Ocultar catalogo de estimaciones">&minus;</button>
                    </div>
                </div>

                <div class="table-scroll labor-catalog-scroll">
                    <table class="labor-catalog-table">
                        <thead>
                            <tr>
                                <th>Codigo</th>
                                <th>Contratista</th>
                                <th>Descripcion</th>
                                <th>Area / categoria</th>
                                <th>Periodicidad</th>
                                <th>Monto presupuestado</th>
                                <th>Monto erogado</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $estimateRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr data-estimate-row data-estimate-project="<?php echo e($row['project_id']); ?>" <?php if((int) $row['project_id'] !== $selectedProjectId): ?> hidden <?php endif; ?>>
                                    <td><?php echo e($row['code']); ?></td>
                                    <td><?php echo e($row['responsible']); ?></td>
                                    <td><?php echo e($row['description']); ?></td>
                                    <td><span class="labor-area-badge"><?php echo e($row['area']); ?></span></td>
                                    <td><?php echo e($row['periodicity']); ?></td>
                                    <td><?php echo e($money($row['amount'])); ?></td>
                                    <td data-disbursed-amount="<?php echo e(number_format($row['disbursed_amount'], 2, '.', '')); ?>"><?php echo e($money($row['disbursed_amount'])); ?></td>
                                    <td><span class="status <?php echo e($row['status_class']); ?>"><?php echo e($row['status']); ?></span></td>
                                    <td>
                                        <div class="labor-file-actions">
                                            <button class="button ghost small" type="button">Editar</button>
                                            <button
                                                class="button danger small"
                                                type="button"
                                                data-labor-delete
                                                data-labor-delete-url="<?php echo e($row['delete_url']); ?>"
                                            >Eliminar</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr data-estimate-empty <?php if($estimateRows->contains(fn ($row) => (int) $row['project_id'] === $selectedProjectId)): ?> hidden <?php endif; ?>>
                                <td class="empty-state" colspan="9">No hay estimaciones registradas para esta obra.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="panel labor-tracking-panel">
            <h2 class="labor-tracking-title">Pagos Vigentes</h2>

            <div class="labor-toolbar">
                <div class="labor-tabs" role="tablist" aria-label="Seguimiento de mano de obra">
                    <button class="labor-tab is-active" type="button" data-labor-filter="all">Todos</button>
                    <button class="labor-tab" type="button" data-labor-filter="nomina">Nomina Quincenal</button>
                    <button class="labor-tab" type="button" data-labor-filter="estimacion">Estimaciones quincenales</button>
                </div>
                <div class="labor-toolbar-note">
                    <span class="fine-print">Pendientes de pago de la obra seleccionada</span>
                    <a
                        class="button ghost"
                        data-labor-history-link
                        data-history-base="<?php echo e(route('construction.placeholder', ['section' => 'pagos'])); ?>"
                        href="<?php echo e(route('construction.placeholder', ['section' => 'pagos', 'project' => $selectedProjectId])); ?>"
                    >Historial</a>
                </div>
            </div>

            <div class="table-scroll labor-table-scroll">
                <table class="labor-table">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Codigo</th>
                            <th>Descripcion / alcance</th>
                            <th>Area / categoria</th>
                            <th>Responsable</th>
                            <th>Periodo / referencia</th>
                            <th>Fecha limite de pago</th>
                            <th>% Avance</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Factura</th>
                            <th>Fotos</th>
                            <th>Pago</th>
                            <th>Fecha pago</th>
                            <th class="labor-actions-column" data-no-filter data-no-sort>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $laborRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr data-labor-row="<?php echo e(strtolower($row['type'])); ?>" data-labor-project-id="<?php echo e($row['project_id']); ?>" data-labor-code="<?php echo e($row['code']); ?>" <?php if((int) $row['project_id'] !== $selectedProjectId): ?> hidden <?php endif; ?>>
                                <td><span class="labor-type-badge <?php echo e(strtolower($row['type'])); ?>"><?php echo e($row['type']); ?></span></td>
                                <td><?php echo e($row['code']); ?></td>
                                <td><strong><?php echo e($row['description']); ?></strong></td>
                                <td><span class="labor-area-badge"><?php echo e($row['area']); ?></span></td>
                                <td><?php echo e($row['responsible']); ?></td>
                                <td><?php echo e($row['period']); ?></td>
                                <td><?php echo e($row['payment_due_date']); ?></td>
                                <td>
                                    <div class="labor-progress">
                                        <strong><?php echo e($row['progress']); ?>%</strong>
                                        <span class="labor-progress-track"><span style="width: <?php echo e($row['progress']); ?>%;"></span></span>
                                    </div>
                                </td>
                                <td><?php echo e($money($row['amount'])); ?></td>
                                <td><span class="status <?php echo e($row['status_class']); ?>"><?php echo e($row['status']); ?></span></td>
                                <td>
                                    <div class="labor-file-actions">
                                        <form method="POST" action="<?php echo e($row['invoice_upload_url']); ?>" enctype="multipart/form-data">
                                            <?php echo csrf_field(); ?>
                                            <label class="button ghost small" title="Subir factura">
                                                Subir
                                                <input class="file-upload-input" type="file" name="invoice_file" accept=".pdf,.jpg,.jpeg,.png,.webp" data-auto-file-submit required>
                                            </label>
                                        </form>
                                        <?php if(filled($row['invoice_file_url'])): ?>
                                            <a class="button ghost small labor-view-button" href="<?php echo e($row['invoice_file_url']); ?>" target="_blank" rel="noopener">Ver</a>
                                        <?php else: ?>
                                            <button class="button ghost small labor-view-button" type="button" disabled aria-disabled="true" title="Sin archivo adjunto">Ver</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="labor-file-actions">
                                        <form method="POST" action="<?php echo e($row['photos_upload_url']); ?>" enctype="multipart/form-data">
                                            <?php echo csrf_field(); ?>
                                            <label class="button ghost small" title="Subir fotos de avance">
                                                Subir
                                                <input class="file-upload-input" type="file" name="photo_files[]" accept=".jpg,.jpeg,.png,.webp" multiple data-auto-file-submit required>
                                            </label>
                                        </form>
                                        <?php if(filled($row['photo_files_url'])): ?>
                                            <a class="button ghost small labor-view-button" href="<?php echo e($row['photo_files_url']); ?>" target="_blank" rel="noopener" data-photo-view-enabled="<?php echo e($row['payment_order_id']); ?>">Ver</a>
                                        <?php else: ?>
                                            <button class="button ghost small labor-view-button" type="button" disabled aria-disabled="true" title="Sin fotos adjuntas" data-photo-view-disabled="<?php echo e($row['payment_order_id']); ?>">Ver</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="labor-file-actions">
                                        <?php if(auth()->user()?->canAccessRole('finance')): ?>
                                            <form method="POST" action="<?php echo e($row['payment_upload_url']); ?>" enctype="multipart/form-data">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="paid_on" value="<?php echo e(now()->toDateString()); ?>">
                                                <label class="button ghost small" title="Subir comprobante de pago">
                                                    Subir
                                                    <input class="file-upload-input" type="file" name="payment_file" accept=".pdf,.jpg,.jpeg,.png,.webp" data-auto-file-submit required>
                                                </label>
                                            </form>
                                        <?php else: ?>
                                            <button class="button ghost small" type="button" disabled aria-disabled="true" title="Disponible para Finanzas">Subir</button>
                                        <?php endif; ?>
                                        <?php if(filled($row['payment_file_url'])): ?>
                                            <a class="button ghost small labor-view-button" href="<?php echo e($row['payment_file_url']); ?>" target="_blank" rel="noopener">Ver</a>
                                        <?php else: ?>
                                            <button class="button ghost small labor-view-button" type="button" disabled aria-disabled="true" title="Sin archivo adjunto">Ver</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?php echo e($row['payment_date']); ?></td>
                                <td class="labor-actions-column">
                                    <button
                                        class="button danger small"
                                        type="button"
                                        data-labor-delete
                                        data-labor-delete-url="<?php echo e($row['delete_url']); ?>"
                                    >Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr data-labor-empty hidden>
                            <td class="empty-state" colspan="15">No hay pagos vigentes para esta obra.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <dialog class="confirm-dialog" data-labor-delete-dialog aria-labelledby="labor-delete-title">
            <div class="confirm-card">
                <h3 id="labor-delete-title">Eliminar registro</h3>
                <p>Estas seguro que quieres eliminar?</p>
                <div class="form-actions">
                    <button class="button ghost" type="button" data-labor-delete-no>No</button>
                    <button class="button danger" type="button" data-labor-delete-yes>Si</button>
                </div>
            </div>
        </dialog>

        <form method="POST" data-labor-delete-form hidden>
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
        </form>

        <?php
            $createPayrollValues = [
                'construction_project_id' => $creatingPayroll ? old('construction_project_id', $selectedProjectId) : $selectedProjectId,
                'code' => $creatingPayroll ? old('code', '') : '',
                'contractor' => $creatingPayroll ? old('contractor', '') : '',
                'description' => $creatingPayroll ? old('description', '') : '',
                'area' => $creatingPayroll ? old('area', 'Mano de obra') : 'Mano de obra',
                'periodicity' => $creatingPayroll ? old('periodicity', 'Quincenal') : 'Quincenal',
                'period_start' => $creatingPayroll ? old('period_start', '') : '',
                'period_end' => $creatingPayroll ? old('period_end', '') : '',
                'progress' => $creatingPayroll ? old('progress', 0) : 0,
                'amount' => $creatingPayroll ? old('amount', 0) : 0,
                'status' => $creatingPayroll ? old('status', 'Borrador') : 'Borrador',
                'payment_due_date' => $creatingPayroll ? old('payment_due_date', '') : '',
            ];
        ?>

        <dialog class="supply-detail-dialog payroll-dialog" id="new-payroll-dialog" data-supply-detail-dialog>
            <div class="supply-detail-card">
                <button class="supply-detail-close" type="button" data-supply-detail-close aria-label="Cerrar">x</button>
                <div>
                    <h3>Nueva nomina periodica</h3>
                    <p class="fine-print">Registra el periodo, contratista y monto de la nomina para la obra seleccionada.</p>
                </div>

                <form class="payroll-form" method="POST" action="<?php echo e(route('construction.payrolls.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="payroll_form" value="create">
                    <?php echo $__env->make('construction.partials.payroll-form-fields', [
                        'values' => $createPayrollValues,
                        'isCreateForm' => true,
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    <div class="form-actions payroll-form-actions">
                        <button class="button ghost" type="button" data-supply-detail-close>Cancelar</button>
                        <button class="button primary" type="submit">Guardar nomina</button>
                    </div>
                </form>
            </div>
        </dialog>

        <?php
            $createEstimateValues = [
                'construction_project_id' => $creatingEstimate ? old('construction_project_id', $selectedProjectId) : $selectedProjectId,
                'code' => $creatingEstimate ? old('code', '') : '',
                'contractor' => $creatingEstimate ? old('contractor', '') : '',
                'description' => $creatingEstimate ? old('description', '') : '',
                'area' => $creatingEstimate ? old('area', '') : '',
                'periodicity' => $creatingEstimate ? old('periodicity', 'Quincenal') : 'Quincenal',
                'period_reference' => $creatingEstimate ? old('period_reference', '') : '',
                'payment_due_date' => $creatingEstimate ? old('payment_due_date', '') : '',
                'progress' => $creatingEstimate ? old('progress', 0) : 0,
                'amount' => $creatingEstimate ? old('amount', 0) : 0,
                'status' => $creatingEstimate ? old('status', 'Sin asignar') : 'Sin asignar',
            ];
        ?>

        <dialog class="supply-detail-dialog payroll-dialog" id="new-estimate-dialog" data-supply-detail-dialog>
            <div class="supply-detail-card">
                <button class="supply-detail-close" type="button" data-supply-detail-close aria-label="Cerrar">x</button>
                <div>
                    <h3>Nuevo paquete de estimaciones</h3>
                    <p class="fine-print">Registra el periodo, contratista y monto de la estimacion para la obra seleccionada.</p>
                </div>

                <form class="payroll-form" method="POST" action="<?php echo e(route('construction.estimates.store')); ?>">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="estimate_form" value="create">
                    <?php echo $__env->make('construction.partials.estimate-form-fields', [
                        'values' => $createEstimateValues,
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    <div class="form-actions payroll-form-actions">
                        <button class="button ghost" type="button" data-supply-detail-close>Cancelar</button>
                        <button class="button primary" type="submit">Guardar paquete</button>
                    </div>
                </form>
            </div>
        </dialog>

        <script>
            (() => {
                document.querySelectorAll('[data-construction-carousel]').forEach((carousel) => {
                    const track = carousel.querySelector('[data-construction-carousel-track]');
                    const scrollByTile = () => Math.max(220, Math.round((track?.clientWidth || 260) * 0.72));

                    carousel.querySelector('[data-carousel-prev]')?.addEventListener('click', () => {
                        track?.scrollBy({ left: -scrollByTile(), behavior: 'smooth' });
                    });

                    carousel.querySelector('[data-carousel-next]')?.addEventListener('click', () => {
                        track?.scrollBy({ left: scrollByTile(), behavior: 'smooth' });
                    });
                });

                const projectButtons = document.querySelectorAll('[data-labor-project]');
                const selectedProjectNames = document.querySelectorAll('[data-selected-project-name]');
                const payrollRows = [...document.querySelectorAll('[data-payroll-row]')];
                const estimateRows = [...document.querySelectorAll('[data-estimate-row]')];
                let laborRows = [...document.querySelectorAll('[data-labor-row]')];
                const payrollEmpty = document.querySelector('[data-payroll-empty]');
                const estimateEmpty = document.querySelector('[data-estimate-empty]');
                const laborEmpty = document.querySelector('[data-labor-empty]');
                const createProjectSelect = document.querySelector('[data-payroll-project-select]');
                const estimateProjectSelect = document.querySelector('[data-estimate-project-select]');
                const laborDeleteDialog = document.querySelector('[data-labor-delete-dialog]');
                const laborDeleteForm = document.querySelector('[data-labor-delete-form]');
                const laborHistoryLink = document.querySelector('[data-labor-history-link]');
                let pendingDeleteRow = null;
                let pendingDeleteUrl = '';
                let selectedProjectId = <?php echo json_encode((string) $selectedProjectId, 15, 512) ?>;
                let laborFilter = 'all';

                const applyProjectFilters = () => {
                    let visiblePayrolls = 0;

                    payrollRows.forEach((row) => {
                        const visible = row.dataset.payrollProject === selectedProjectId;
                        row.hidden = !visible;
                        visiblePayrolls += visible ? 1 : 0;
                    });

                    if (payrollEmpty) {
                        payrollEmpty.hidden = visiblePayrolls > 0;
                    }

                    let visibleEstimates = 0;

                    estimateRows.forEach((row) => {
                        const visible = row.dataset.estimateProject === selectedProjectId;
                        row.hidden = !visible;
                        visibleEstimates += visible ? 1 : 0;
                    });

                    if (estimateEmpty) {
                        estimateEmpty.hidden = visibleEstimates > 0;
                    }

                    let visibleLaborRows = 0;

                    laborRows.forEach((row) => {
                        const matchesProject = row.dataset.laborProjectId === selectedProjectId;
                        const matchesType = laborFilter === 'all' || row.dataset.laborRow === laborFilter;
                        const visible = matchesProject && matchesType;
                        row.hidden = !visible;
                        visibleLaborRows += visible ? 1 : 0;
                    });

                    if (laborEmpty) {
                        laborEmpty.hidden = visibleLaborRows > 0;
                    }
                };

                projectButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        selectedProjectId = button.dataset.projectId || '';

                        projectButtons.forEach((projectButton) => {
                            const selected = projectButton === button;
                            projectButton.classList.toggle('active', selected);
                            projectButton.setAttribute('aria-pressed', selected ? 'true' : 'false');
                        });

                        selectedProjectNames.forEach((projectName) => {
                            projectName.textContent = button.dataset.projectName || 'Sin obra seleccionada';
                        });

                        if (createProjectSelect) {
                            createProjectSelect.value = selectedProjectId;
                        }

                        if (estimateProjectSelect) {
                            estimateProjectSelect.value = selectedProjectId;
                        }

                        if (laborHistoryLink) {
                            const historyUrl = new URL(laborHistoryLink.dataset.historyBase, window.location.origin);
                            historyUrl.searchParams.set('project', selectedProjectId);
                            laborHistoryLink.href = historyUrl.toString();
                        }

                        applyProjectFilters();
                    });
                });

                const catalogToggles = [...document.querySelectorAll('[data-labor-catalog-toggle]')];
                const catalogs = [...document.querySelectorAll('[data-labor-catalog]')];

                const setCatalogOpen = (catalogName, open) => {
                    catalogs.forEach((catalog) => {
                        catalog.hidden = !(open && catalog.dataset.laborCatalog === catalogName);
                    });

                    catalogToggles.forEach((toggle) => {
                        const active = open && toggle.dataset.laborCatalogToggle === catalogName;
                        toggle.classList.toggle('is-active', active);
                        toggle.setAttribute('aria-expanded', active ? 'true' : 'false');
                        const indicator = toggle.querySelector('[data-labor-catalog-indicator]');
                        if (indicator) indicator.textContent = active ? '\u2212' : '+';
                    });
                };

                catalogToggles.forEach((toggle) => {
                    toggle.addEventListener('click', () => {
                        const catalogName = toggle.dataset.laborCatalogToggle;
                        const catalog = catalogs.find((item) => item.dataset.laborCatalog === catalogName);
                        setCatalogOpen(catalogName, catalog?.hidden ?? true);
                    });
                });

                document.querySelectorAll('[data-labor-catalog-close]').forEach((button) => {
                    button.addEventListener('click', () => {
                        const catalogName = button.dataset.laborCatalogClose;
                        setCatalogOpen(catalogName, false);
                        document.querySelector(`[data-labor-catalog-toggle="${catalogName}"]`)?.focus();
                    });
                });

                document.querySelectorAll('[data-labor-filter]').forEach((button) => {
                    button.addEventListener('click', () => {
                        laborFilter = button.dataset.laborFilter || 'all';

                        document.querySelectorAll('[data-labor-filter]').forEach((tab) => {
                            tab.classList.toggle('is-active', tab === button);
                        });

                        applyProjectFilters();
                    });
                });

                document.querySelector('[data-payroll-create]')?.addEventListener('click', () => {
                    if (createProjectSelect) {
                        createProjectSelect.value = selectedProjectId;
                    }
                });

                document.querySelector('[data-estimate-create]')?.addEventListener('click', () => {
                    if (estimateProjectSelect) {
                        estimateProjectSelect.value = selectedProjectId;
                    }
                });

                document.querySelectorAll('[data-auto-file-submit]').forEach((input) => {
                    input.addEventListener('change', () => {
                        if (input.files?.length) {
                            input.form?.submit();
                        }
                    });
                });

                document.querySelectorAll('[data-labor-delete]').forEach((button) => {
                    button.addEventListener('click', () => {
                        pendingDeleteRow = button.closest('[data-labor-row], [data-payroll-row], [data-estimate-row]');
                        pendingDeleteUrl = button.dataset.laborDeleteUrl || '';

                        if (pendingDeleteRow && pendingDeleteUrl && laborDeleteDialog) {
                            laborDeleteDialog.showModal();
                        }
                    });
                });

                const closeLaborDeleteDialog = () => {
                    laborDeleteDialog?.close();
                    pendingDeleteRow = null;
                    pendingDeleteUrl = '';
                };

                document.querySelector('[data-labor-delete-no]')?.addEventListener('click', closeLaborDeleteDialog);

                document.querySelector('[data-labor-delete-yes]')?.addEventListener('click', () => {
                    if (pendingDeleteUrl && laborDeleteForm) {
                        laborDeleteForm.action = pendingDeleteUrl;
                        laborDeleteForm.submit();
                    }
                });

                laborDeleteDialog?.addEventListener('click', (event) => {
                    if (event.target === laborDeleteDialog) {
                        closeLaborDeleteDialog();
                    }
                });

                laborDeleteDialog?.addEventListener('close', () => {
                    pendingDeleteRow = null;
                    pendingDeleteUrl = '';
                });

                applyProjectFilters();

                if (<?php echo json_encode(request()->boolean('open_payroll') || $invalidPayrollDialogId === 'new-payroll-dialog', 15, 512) ?>) {
                    setCatalogOpen('payroll', true);
                }

                if (<?php echo json_encode(request()->boolean('open_estimates') || $invalidPayrollDialogId === 'new-estimate-dialog', 15, 512) ?>) {
                    setCatalogOpen('estimates', true);
                }

                const invalidPayrollDialogId = <?php echo json_encode($invalidPayrollDialogId, 15, 512) ?>;
                if (invalidPayrollDialogId) {
                    const invalidDialog = document.getElementById(invalidPayrollDialogId);
                    window.requestAnimationFrame(() => invalidDialog?.showModal());
                }
            })();
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views/construction/labor-tracking.blade.php ENDPATH**/ ?>