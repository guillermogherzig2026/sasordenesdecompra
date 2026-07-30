<?php $__env->startSection('body'); ?>
    <?php
        $service = $service ?? null;
        $isEditing = (bool) $service;
        $selectedCompany = old('company_name', $service?->company_name ?? $companies->first()?->name);
        $validityOptions = ['Indefinido', '12 meses', '24 meses', '36 meses', 'Anual'];
        $selectedValidity = old('validity', $service?->validity ?? 'Indefinido');
        $selectedPaymentLapse = old('payment_lapse', $service?->payment_interval_days ?? 30);
        $selectedDueDaysAfterCutoff = old('due_days_after_cutoff', $service?->due_days_after_cutoff ?? 0);
        $isDomiciled = old('is_domiciled', $service?->is_domiciled ?? false);
        $selectedStartDay = old('start_day', $service?->start_date?->day ?? now()->day);
        $selectedStartMonth = old('start_month', $service?->start_date?->month ?? now()->month);
        $selectedStartYear = old('start_year', $service?->start_date?->year ?? now()->year);
        $selectedCutoffDay = old('cutoff_day', $service?->cutoff_day ?? 5);
        $selectedCutoffMonth = old('cutoff_month', $service?->cutoff_month ?? $selectedStartMonth);
        $selectedCutoffYear = old('cutoff_year', $service?->cutoff_year ?? $selectedStartYear);
        $monthNames = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];
        $yearOptions = range(now()->year - 5, now()->year + 10);
    ?>

    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => $isEditing ? 'Editar servicio' : 'Alta de servicio']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isEditing ? 'Editar servicio' : 'Alta de servicio')]); ?>
        <form class="panel" method="POST" action="<?php echo e($isEditing ? route('services.update', $service) : route('services.store')); ?>">
            <?php echo csrf_field(); ?>
            <?php if($isEditing): ?>
                <?php echo method_field('PUT'); ?>
            <?php endif; ?>
            <input type="hidden" name="start_date" value="<?php echo e(old('start_date', $service?->start_date?->toDateString() ?? now()->toDateString())); ?>">
            <div class="panel-header">
                <div>
                    <h2><?php echo e($isEditing ? 'Editar servicio ' . $service->folio : 'Alta de nuevo servicio'); ?></h2>
                    <p class="fine-print"><?php echo e($isEditing ? 'Actualiza los datos del servicio recurrente.' : 'Registra servicios recurrentes con vigencia, lapso de pago, cuenta pagadora y periodo de facturacion.'); ?></p>
                </div>
            </div>

            <div class="grid-4">
                <label>Empresa / titular
                    <select name="company_name" required onchange="this.form.holder.value = this.value">
                        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($company->name); ?>" <?php if($selectedCompany === $company->name): echo 'selected'; endif; ?>><?php echo e($company->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <input type="hidden" name="holder" value="<?php echo e(old('holder', $service?->holder ?? $selectedCompany)); ?>">
                </label>
                <label>Nombre del servicio<input name="service_name" value="<?php echo e(old('service_name', $service?->service_name)); ?>" placeholder="Ej: Telefonia Telmex" required></label>
                <label>Categoria
                    <select name="category" required>
                        <option value="">Seleccionar categoria...</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category); ?>" <?php if(old('category', $service?->category) === $category): echo 'selected'; endif; ?>><?php echo e($category); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </label>
                <label>Proveedor<input name="provider" value="<?php echo e(old('provider', $service?->provider)); ?>" placeholder="Nombre del proveedor" required></label>
            </div>

            <div class="grid-4">
                <label>Numero de servicio<input name="service_number" value="<?php echo e(old('service_number', $service?->service_number)); ?>" placeholder="Numero de servicio" required></label>
                <label>Costo del servicio<input name="cost" type="number" min="0" step="0.01" value="<?php echo e(old('cost', $service?->cost)); ?>" placeholder="0.00" required></label>
                <label>Vigencia del servicio
                    <select name="validity" required>
                        <?php $__currentLoopData = $validityOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $validityOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($validityOption); ?>" <?php if($selectedValidity === $validityOption): echo 'selected'; endif; ?>><?php echo e($validityOption); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($selectedValidity && ! in_array($selectedValidity, $validityOptions, true)): ?>
                            <option value="<?php echo e($selectedValidity); ?>" selected><?php echo e($selectedValidity); ?></option>
                        <?php endif; ?>
                    </select>
                </label>
                <label>Lapso de pago en dias
                    <select name="payment_lapse" required>
                        <option value="30" <?php if((string) $selectedPaymentLapse === '30'): echo 'selected'; endif; ?>>30</option>
                        <option value="60" <?php if((string) $selectedPaymentLapse === '60'): echo 'selected'; endif; ?>>60</option>
                        <option value="90" <?php if((string) $selectedPaymentLapse === '90'): echo 'selected'; endif; ?>>90</option>
                        <option value="180" <?php if((string) $selectedPaymentLapse === '180'): echo 'selected'; endif; ?>>180</option>
                        <option value="365" <?php if((string) $selectedPaymentLapse === '365'): echo 'selected'; endif; ?>>365</option>
                    </select>
                </label>
            </div>

            <div class="grid-4">
                <label>Periodo — Fecha de inicio
                    <div style="display:flex;gap:6px">
                        <select name="start_day" required style="flex:1" onchange="recalcDueDate()">
                            <?php $__currentLoopData = range(1, 31); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($d); ?>" <?php if((int) $selectedStartDay === $d): echo 'selected'; endif; ?>><?php echo e($d); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <select name="start_month" required style="flex:2" onchange="recalcDueDate()">
                            <?php $__currentLoopData = $monthNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($num); ?>" <?php if((int) $selectedStartMonth === $num): echo 'selected'; endif; ?>><?php echo e($name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <select name="start_year" required style="flex:1" onchange="recalcDueDate()">
                            <?php $__currentLoopData = $yearOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($year); ?>" <?php if((int) $selectedStartYear === $year): echo 'selected'; endif; ?>><?php echo e($year); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </label>
                <label>Periodo — Fecha de corte
                    <div style="display:flex;gap:6px">
                        <select name="cutoff_day" required style="flex:1" onchange="recalcDueDate()">
                            <?php $__currentLoopData = range(1, 31); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($d); ?>" <?php if((int) $selectedCutoffDay === $d): echo 'selected'; endif; ?>><?php echo e($d); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <select name="cutoff_month" required style="flex:2" onchange="recalcDueDate()">
                            <?php $__currentLoopData = $monthNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($num); ?>" <?php if((int) $selectedCutoffMonth === $num): echo 'selected'; endif; ?>><?php echo e($name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <select name="cutoff_year" required style="flex:1" onchange="recalcDueDate()">
                            <?php $__currentLoopData = $yearOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($year); ?>" <?php if((int) $selectedCutoffYear === $year): echo 'selected'; endif; ?>><?php echo e($year); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </label>
                <label>Fecha de vencimiento
                    <input type="date" id="due_date_display" readonly required style="background:#f8f9fa;cursor:not-allowed">
                    <span class="fine-print">Se calcula automaticamente: fecha de corte + lapso de pago.</span>
                </label>
                <label>Banco<input name="bank" value="<?php echo e(old('bank', $service?->bank)); ?>" placeholder="Banco" required></label>
            </div>

            <div class="grid-4">
                <label>Cuenta pagadora<input name="payer_account" value="<?php echo e(old('payer_account', $service?->payer_account)); ?>" placeholder="Cuenta" required></label>
                <label>Referencia o linea de captura<input name="reference" value="<?php echo e(old('reference', $service?->reference)); ?>" placeholder="Referencia" required></label>
            </div>

            <label class="checkbox-inline">
                <input name="is_domiciled" type="checkbox" value="1" <?php if($isDomiciled): echo 'checked'; endif; ?>>
                Pago Domiciliado
                <span class="fine-print">Se carga automaticamente a la cuenta o tarjeta de la empresa y se marcara como DOM.</span>
            </label>

            <label>Sucursal
                <input name="branch" value="<?php echo e(old('branch', $service?->branch)); ?>" placeholder="Nombre de la sucursal">
            </label>

            <label>Ubicacion / Direccion del servicio
                <textarea name="service_location" placeholder="Direccion donde se presta o factura el servicio"><?php echo e(old('service_location', $service?->service_location)); ?></textarea>
            </label>

            <label>Notas adicionales<textarea name="notes" placeholder="Observaciones..."><?php echo e(old('notes', $service?->notes)); ?></textarea></label>

            <div class="form-actions">
                <span class="fine-print"><?php echo e($isEditing ? 'Los cambios se reflejaran en catalogo y vistas mensuales.' : 'El servicio quedara activo y aparecera en las vistas mensuales.'); ?></span>
                <div class="actions">
                    <?php if($isEditing): ?>
                        <a class="button ghost" href="<?php echo e(route('services.catalog')); ?>">Cancelar</a>
                    <?php endif; ?>
                    <button class="button primary" type="submit">Guardar servicio</button>
                </div>
            </div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const monthNames = <?php echo json_encode($monthNames, 15, 512) ?>;
                const paymentLapse = document.querySelector('select[name="payment_lapse"]');
                const startDay = document.querySelector('select[name="start_day"]');
                const startMonth = document.querySelector('select[name="start_month"]');
                const startYear = document.querySelector('select[name="start_year"]');
                const cutoffDay = document.querySelector('select[name="cutoff_day"]');
                const cutoffMonth = document.querySelector('select[name="cutoff_month"]');
                const cutoffYear = document.querySelector('select[name="cutoff_year"]');
                const dueDateDisplay = document.getElementById('due_date_display');

                function recalcDueDate() {
                    const cDay = parseInt(cutoffDay.value, 10);
                    const lapse = parseInt(paymentLapse.value, 10) || 30;
                    const cMonth = parseInt(cutoffMonth.value, 10) - 1;
                    const cYear = parseInt(cutoffYear.value, 10);
                    const daysInCutoffMonth = new Date(cYear, cMonth + 1, 0).getDate();

                    if (cDay > daysInCutoffMonth) {
                        dueDateDisplay.value = '';
                        return;
                    }

                    let cutoff = new Date(cYear, cMonth, cDay);
                    const due = new Date(cutoff);
                    due.setDate(due.getDate() + lapse);
                    const y = due.getFullYear();
                    const m = String(due.getMonth() + 1).padStart(2, '0');
                    const d = String(due.getDate()).padStart(2, '0');
                    dueDateDisplay.value = `${y}-${m}-${d}`;
                }

                window.recalcDueDate = recalcDueDate;
                paymentLapse.addEventListener('change', recalcDueDate);
                cutoffDay.addEventListener('change', recalcDueDate);
                cutoffMonth.addEventListener('change', recalcDueDate);
                cutoffYear.addEventListener('change', recalcDueDate);
                recalcDueDate();
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\sasordenesdecompra\resources\views/services/form.blade.php ENDPATH**/ ?>