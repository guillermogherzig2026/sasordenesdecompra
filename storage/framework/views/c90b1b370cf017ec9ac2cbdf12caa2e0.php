<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['order', 'dialogId']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['order', 'dialogId']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<button class="button ghost small" type="button" data-supply-detail-open="<?php echo e($dialogId); ?>">Ver</button>

<dialog class="supply-detail-dialog" id="<?php echo e($dialogId); ?>" data-supply-detail-dialog>
    <div class="supply-detail-card">
        <button class="supply-detail-close" type="button" data-supply-detail-close aria-label="Cerrar">×</button>
        <div>
            <h3>Partidas de <?php echo e($order->folio); ?></h3>
            <p class="fine-print">Detalle de insumos solicitados en esta orden de suministro.</p>
        </div>

        <div class="supply-detail-lines" role="table" aria-label="Detalle de partidas">
            <div class="supply-detail-line supply-detail-head" role="row">
                <span>Cantidad</span>
                <span>Unidad</span>
                <span>Descripcion del insumo</span>
                <span>Precio unitario</span>
                <span>Precio total</span>
            </div>
            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="supply-detail-line" role="row">
                    <span><?php echo e(number_format((float) $item->quantity, 2)); ?></span>
                    <span><?php echo e($item->catalogItem?->unit ?: 'unidad'); ?></span>
                    <span>
                        <strong><?php echo e($item->article); ?></strong>
                        <?php if($item->catalogItem?->description): ?>
                            <small class="fine-print"><?php echo e($item->catalogItem->description); ?></small>
                        <?php endif; ?>
                    </span>
                    <span>$<?php echo e(number_format((float) $item->unit_cost, 2)); ?></span>
                    <span>$<?php echo e(number_format((float) $item->line_total, 2)); ?></span>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</dialog>
<?php /**PATH C:\laragon\www\Revision OC Software\resources\views\components\supply-order-items-dialog.blade.php ENDPATH**/ ?>