<div class="grid-2">
    <label>Obra
        <select name="construction_project_id" <?php if($isCreateForm ?? false): ?> data-payroll-project-select <?php endif; ?> required>
            <?php $__currentLoopData = $activeProjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $projectOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($projectOption->id); ?>" <?php if((int) $values['construction_project_id'] === $projectOption->id): echo 'selected'; endif; ?>>
                    <?php echo e($projectOption->project_key); ?> - <?php echo e($projectOption->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </label>
    <label>Codigo
        <input name="code" maxlength="40" value="<?php echo e($values['code']); ?>" placeholder="Ej: NOM-S28" required>
    </label>
</div>

<div class="grid-2">
    <label>Contratista
        <input name="contractor" maxlength="255" value="<?php echo e($values['contractor']); ?>" required>
    </label>
    <label>Descripcion
        <input name="description" maxlength="255" value="<?php echo e($values['description']); ?>" placeholder="Ej: Nomina quincenal S28" required>
    </label>
</div>

<div class="grid-2">
    <label>Area / categoria
        <input name="area" maxlength="120" value="<?php echo e($values['area']); ?>" placeholder="Ej: Mano de obra">
    </label>
    <label>Periodicidad
        <select name="periodicity" required>
            <?php $__currentLoopData = $payrollPeriodicityOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $periodicity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($periodicity); ?>" <?php if($values['periodicity'] === $periodicity): echo 'selected'; endif; ?>><?php echo e($periodicity); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </label>
</div>

<div class="grid-2">
    <label>Inicio del periodo
        <input type="date" name="period_start" value="<?php echo e($values['period_start']); ?>" required>
    </label>
    <label>Fin del periodo
        <input type="date" name="period_end" value="<?php echo e($values['period_end']); ?>" required>
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

<div class="grid-2">
    <label>Estado
        <select name="status" required>
            <?php $__currentLoopData = $payrollStatusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payrollStatus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($payrollStatus); ?>" <?php if($values['status'] === $payrollStatus): echo 'selected'; endif; ?>><?php echo e($payrollStatus); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </label>
    <label>Fecha limite de pago
        <input type="date" name="payment_due_date" value="<?php echo e($values['payment_due_date']); ?>" required>
    </label>
</div>
<?php /**PATH C:\laragon\www\Revision OC Software\resources\views/construction/partials/payroll-form-fields.blade.php ENDPATH**/ ?>