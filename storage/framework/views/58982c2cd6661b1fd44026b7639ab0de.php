<div class="grid-2">
    <label>Obra
        <select name="construction_project_id" data-estimate-project-select required>
            <?php $__currentLoopData = $activeProjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projectOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($projectOption->id); ?>" <?php if((int) $values['construction_project_id'] === $projectOption->id): echo 'selected'; endif; ?>>
                    <?php echo e($projectOption->project_key); ?> - <?php echo e($projectOption->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </label>
    <label>Codigo
        <input name="code" maxlength="40" value="<?php echo e($values['code']); ?>" placeholder="Ej: PAQ-009" required>
    </label>
</div>

<div class="grid-2">
    <label>Contratista
        <input name="contractor" maxlength="255" value="<?php echo e($values['contractor']); ?>" placeholder="Opcional">
    </label>
    <label>Descripcion
        <input name="description" maxlength="255" value="<?php echo e($values['description']); ?>" placeholder="Ej: Acabados interiores Nivel 01" required>
    </label>
</div>

<div class="grid-2">
    <label>Area / categoria
        <input name="area" maxlength="120" value="<?php echo e($values['area']); ?>" placeholder="Ej: Albanileria" required>
    </label>
    <label>Periodicidad
        <select name="periodicity" required>
            <?php $__currentLoopData = ['Semanal', 'Quincenal', 'Mensual']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $periodicity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($periodicity); ?>" <?php if($values['periodicity'] === $periodicity): echo 'selected'; endif; ?>><?php echo e($periodicity); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </label>
</div>

<div class="grid-2">
    <label>Periodo / referencia
        <input name="period_reference" maxlength="120" value="<?php echo e($values['period_reference']); ?>" placeholder="Ej: 01/09 - 15/09/2026" required>
    </label>
    <label>Fecha limite de pago
        <input type="date" name="payment_due_date" value="<?php echo e($values['payment_due_date']); ?>" required>
    </label>
</div>

<div class="grid-2">
    <label>Avance %
        <input type="number" name="progress" min="0" max="100" step="0.01" value="<?php echo e($values['progress']); ?>" required>
    </label>
    <label>Monto
        <input type="number" name="amount" min="0" step="0.01" value="<?php echo e($values['amount']); ?>" required>
    </label>
</div>

<label>Estado
    <select name="status" required>
        <?php $__currentLoopData = ['Sin asignar', 'Programado', 'En ejecucion', 'En revision', 'Aprobado']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estimateStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($estimateStatus); ?>" <?php if($values['status'] === $estimateStatus): echo 'selected'; endif; ?>><?php echo e($estimateStatus); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</label>
<?php /**PATH C:\laragon\www\Revision OC Software\resources\views/construction/partials/estimate-form-fields.blade.php ENDPATH**/ ?>