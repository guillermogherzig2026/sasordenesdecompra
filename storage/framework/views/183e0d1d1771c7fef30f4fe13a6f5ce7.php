<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Editar nomina periodica']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Editar nomina periodica']); ?>
        <?php
            $backUrl = route('construction.placeholder', [
                'section' => 'mano-obra',
                'project' => $payroll->construction_project_id,
                'open_payroll' => 1,
            ]);
            $values = [
                'construction_project_id' => old('construction_project_id', $payroll->construction_project_id),
                'code' => old('code', $payroll->code),
                'contractor' => old('contractor', $payroll->contractor),
                'description' => old('description', $payroll->description),
                'area' => old('area', $payroll->area),
                'periodicity' => old('periodicity', $payroll->periodicity),
                'period_start' => old('period_start', $payroll->period_start?->format('Y-m-d')),
                'period_end' => old('period_end', $payroll->period_end?->format('Y-m-d')),
                'progress' => old('progress', $payroll->progress),
                'amount' => old('amount', $payroll->amount),
                'status' => old('status', $payroll->status),
                'payment_due_date' => old('payment_due_date', $payroll->payment_due_date?->format('Y-m-d')),
            ];
        ?>

        <form class="panel payroll-form" method="POST" action="<?php echo e(route('construction.payrolls.update', $payroll)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="panel-header">
                <div class="panel-header-title">
                    <h2>Editar <?php echo e($payroll->code); ?></h2>
                    <p class="fine-print">Actualiza los datos de esta nomina periodica.</p>
                </div>
                <a class="button ghost" href="<?php echo e($backUrl); ?>">Volver al catalogo</a>
            </div>

            <?php echo $__env->make('construction.partials.payroll-form-fields', [
                'activeProjects' => $projects,
                'values' => $values,
                'isCreateForm' => false,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <div class="form-actions payroll-form-actions">
                <a class="button ghost" href="<?php echo e($backUrl); ?>">Cancelar</a>
                <button class="button primary" type="submit">Guardar cambios</button>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\construction\payrolls\edit.blade.php ENDPATH**/ ?>