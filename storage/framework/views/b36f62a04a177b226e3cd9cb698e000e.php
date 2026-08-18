<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => $project->exists ? 'Editar obra' : 'Nueva obra']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($project->exists ? 'Editar obra' : 'Nueva obra')]); ?>
        <form class="panel" method="POST" action="<?php echo e($project->exists ? route('construction.projects.update', $project) : route('construction.projects.store')); ?>">
            <?php echo csrf_field(); ?>
            <?php if($project->exists): ?>
                <?php echo method_field('PUT'); ?>
            <?php endif; ?>

            <div class="panel-header">
                <div>
                    <h2><?php echo e($project->exists ? "Editar {$project->project_key}" : 'Nueva obra'); ?></h2>
                    <p class="fine-print">Captura los datos principales del expediente de obra.</p>
                </div>
                <a class="button ghost" href="<?php echo e(route('construction.dashboard').'#panel-general-obras'); ?>">Volver</a>
            </div>

            <div class="grid-3">
                <label>Clave de obra
                    <input name="project_key" value="<?php echo e($project->project_key); ?>" required readonly aria-readonly="true">
                </label>
                <label>Nombre
                    <input name="name" value="<?php echo e(old('name', $project->name)); ?>" required>
                </label>
                <label>Ubicacion
                    <input name="location" value="<?php echo e(old('location', $project->location)); ?>">
                </label>
            </div>

            <div class="grid-3">
                <label>Empresa
                    <select name="company_id">
                        <option value="">Sin empresa</option>
                        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($company->id); ?>" <?php if((int) old('company_id', $project->company_id) === $company->id): echo 'selected'; endif; ?>><?php echo e($company->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </label>
                <label>Cliente
                    <select name="client_id">
                        <option value="">Sin cliente</option>
                        <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($client->id); ?>" <?php if((int) old('client_id', $project->client_id) === $client->id): echo 'selected'; endif; ?>><?php echo e($client->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </label>
                <label>Responsable
                    <select name="responsible_user_id">
                        <option value="">Sin responsable</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>" <?php if((int) old('responsible_user_id', $project->responsible_user_id) === $user->id): echo 'selected'; endif; ?>><?php echo e($user->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </label>
            </div>

            <div class="grid-4">
                <label>Tipo de obra
                    <input name="project_type" value="<?php echo e(old('project_type', $project->project_type)); ?>">
                </label>
                <label>Modalidad
                    <select name="modality" required>
                        <?php $__currentLoopData = ['Precio alzado', 'Administracion', 'Hibrida']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modality): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($modality); ?>" <?php if(old('modality', $project->modality) === $modality): echo 'selected'; endif; ?>><?php echo e($modality); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </label>
                <label>Estado
                    <select name="status" required>
                        <?php $__currentLoopData = ['Por iniciar', 'En ejecucion', 'Terminada', 'Suspendida']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($status); ?>" <?php if(old('status', $project->status) === $status): echo 'selected'; endif; ?>><?php echo e($status); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </label>
                <label>Foto/ruta
                    <input name="photo_path" value="<?php echo e(old('photo_path', $project->photo_path)); ?>" placeholder="/images/construction-projects/...">
                </label>
            </div>

            <div class="<?php echo e($project->exists ? 'grid-4' : 'grid-2'); ?>">
                <label>Inicio
                    <input type="date" name="start_date" value="<?php echo e(old('start_date', $project->start_date?->format('Y-m-d'))); ?>">
                </label>
                <label>Fin estimado
                    <input type="date" name="estimated_end_date" value="<?php echo e(old('estimated_end_date', $project->estimated_end_date?->format('Y-m-d'))); ?>">
                </label>
                <?php if($project->exists): ?>
                    <label>Avance fisico %
                        <input type="number" step="0.01" min="0" max="100" name="physical_progress" value="<?php echo e(old('physical_progress', $project->physical_progress ?? 0)); ?>">
                    </label>
                    <label>Avance financiero %
                        <input type="number" step="0.01" min="0" max="100" name="financial_progress" value="<?php echo e(old('financial_progress', $project->financial_progress ?? 0)); ?>">
                    </label>
                <?php endif; ?>
            </div>

            <div class="grid-4">
                <label>Valor contratado
                    <input type="number" step="0.01" min="0" name="contracted_value" value="<?php echo e(old('contracted_value', $project->contracted_value ?? 0)); ?>">
                </label>
                <?php if($project->exists): ?>
                    <label>Estimado acumulado
                        <input type="number" step="0.01" min="0" name="estimated_amount" value="<?php echo e(old('estimated_amount', $project->estimated_amount ?? 0)); ?>">
                    </label>
                    <label>Pagado acumulado
                        <input type="number" step="0.01" min="0" name="paid_amount" value="<?php echo e(old('paid_amount', $project->paid_amount ?? 0)); ?>">
                    </label>
                    <label>Retenciones
                        <input type="number" step="0.01" min="0" name="retention_amount" value="<?php echo e(old('retention_amount', $project->retention_amount ?? 0)); ?>">
                    </label>
                <?php endif; ?>
            </div>

            <div class="grid-4">
                <label>Metros cuadrados construidos
                    <input type="number" step="0.01" min="0" name="constructed_area" value="<?php echo e(old('constructed_area', $project->constructed_area ?? 0)); ?>">
                </label>
                <label>Metros cuadrados vendibles o rentables
                    <input type="number" step="0.01" min="0" name="sellable_rentable_area" value="<?php echo e(old('sellable_rentable_area', $project->sellable_rentable_area ?? 0)); ?>">
                </label>
                <label>Metros cuadrados de estacionamientos
                    <input type="number" step="0.01" min="0" name="parking_area" value="<?php echo e(old('parking_area', $project->parking_area ?? 0)); ?>">
                </label>
                <label>Numero de niveles
                    <input type="number" step="1" min="0" max="999" name="levels_count" value="<?php echo e(old('levels_count', $project->levels_count ?? 0)); ?>">
                </label>
            </div>

            <label>Notas
                <textarea name="notes" rows="3"><?php echo e(old('notes', $project->notes)); ?></textarea>
            </label>

            <div class="form-actions">
                <button class="button primary" type="submit">Guardar obra</button>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\construction\projects\form.blade.php ENDPATH**/ ?>