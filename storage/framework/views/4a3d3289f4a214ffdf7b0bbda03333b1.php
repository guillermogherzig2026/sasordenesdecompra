<?php
    $financeContext = $financeContext ?? false;
    $allowPaymentUpload = $allowPaymentUpload ?? false;
    $emptyMessage = $emptyMessage ?? 'No hay pagos registrados.';
    $money = fn ($value) => '$'.number_format((float) $value, 2);
?>

<div class="table-scroll construction-payment-table-scroll">
    <table class="construction-payment-table">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Codigo</th>
                <th>Descripcion</th>
                <th>Contratista</th>
                <th>Periodo</th>
                <th>Fecha limite de pago</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Factura</th>
                <th>Pago</th>
                <th>Fecha de Pago</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $paymentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paymentOrder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $invoiceUrl = filled($paymentOrder->invoice_file_path)
                        ? ($financeContext
                            ? route('finance.construction-payment-orders.invoice', $paymentOrder)
                            : route('construction.payment-orders.invoice', $paymentOrder))
                        : null;
                    $paymentUrl = filled($paymentOrder->payment_file_path)
                        ? ($financeContext
                            ? route('finance.construction-payment-orders.payment', $paymentOrder)
                            : route('construction.payment-orders.payment', $paymentOrder))
                        : null;
                ?>
                <tr data-construction-payment-order="<?php echo e($paymentOrder->id); ?>" data-payment-code="<?php echo e($paymentOrder->code); ?>">
                    <td><span class="labor-type-badge <?php echo e(strtolower($paymentOrder->type)); ?>"><?php echo e($paymentOrder->type); ?></span></td>
                    <td><strong><?php echo e($paymentOrder->code); ?></strong></td>
                    <td><?php echo e($paymentOrder->description); ?></td>
                    <td><?php echo e($paymentOrder->contractor ?: '-'); ?></td>
                    <td><?php echo e($paymentOrder->periodLabel()); ?></td>
                    <td><?php echo e($paymentOrder->payment_due_date?->format('d/m/Y') ?? '-'); ?></td>
                    <td><?php echo e($money($paymentOrder->amount)); ?></td>
                    <td><span class="status <?php echo e($paymentOrder->statusClass()); ?>"><?php echo e($paymentOrder->status); ?></span></td>
                    <td>
                        <div class="labor-file-actions">
                            <?php if($invoiceUrl): ?>
                                <a class="button ghost small labor-view-button" href="<?php echo e($invoiceUrl); ?>" target="_blank" rel="noopener">Ver</a>
                            <?php else: ?>
                                <button class="button ghost small labor-view-button" type="button" disabled aria-disabled="true" title="Sin archivo adjunto">Ver</button>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <div class="labor-file-actions">
                            <?php if($allowPaymentUpload && blank($paymentOrder->payment_file_path)): ?>
                                <form method="POST" action="<?php echo e(route('finance.construction-payment-orders.payment.store', $paymentOrder)); ?>" enctype="multipart/form-data">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="paid_on" value="<?php echo e(now()->toDateString()); ?>">
                                    <label class="button primary small" title="Subir comprobante de pago">
                                        Subir
                                        <input class="file-upload-input" type="file" name="payment_file" accept=".pdf,.jpg,.jpeg,.png,.webp" data-auto-file-submit required>
                                    </label>
                                </form>
                            <?php endif; ?>
                            <?php if($paymentUrl): ?>
                                <a class="button ghost small labor-view-button" href="<?php echo e($paymentUrl); ?>" target="_blank" rel="noopener">Ver</a>
                            <?php else: ?>
                                <button class="button ghost small labor-view-button" type="button" disabled aria-disabled="true" title="Sin archivo adjunto">Ver</button>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><?php echo e($paymentOrder->paid_on?->format('d/m/Y') ?? '-'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td class="empty-state" colspan="11"><?php echo e($emptyMessage); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if($allowPaymentUpload): ?>
    <script>
        (() => {
            document.querySelectorAll('[data-auto-file-submit]').forEach((input) => {
                if (input.dataset.autoSubmitBound) return;
                input.dataset.autoSubmitBound = 'true';
                input.addEventListener('change', () => {
                    if (input.files?.length) input.form?.submit();
                });
            });
        })();
    </script>
<?php endif; ?>
<?php /**PATH C:\laragon\www\Revision OC Software\resources\views\construction\partials\payment-order-table.blade.php ENDPATH**/ ?>