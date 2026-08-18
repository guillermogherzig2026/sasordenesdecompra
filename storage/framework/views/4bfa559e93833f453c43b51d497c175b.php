<?php $__env->startSection('body'); ?>
    <?php if (isset($component)) { $__componentOriginal9144295cee351e372dbe9bffc4f13bc5 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9144295cee351e372dbe9bffc4f13bc5 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-shell','data' => ['title' => 'Tabulador de precios unitarios']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-shell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Tabulador de precios unitarios']); ?>
        <section class="panel unit-price-panel" data-no-section-export>
            <div class="panel-header">
                <div class="panel-header-title">
                    <h2>Tabulador general de precios unitarios</h2>
                    <p class="fine-print">Gobierno de la Ciudad de Mexico · Actualizacion enero 2026</p>
                </div>
                <a class="button ghost" href="<?php echo e(route('construction.dashboard')); ?>">Panel de obra</a>
            </div>

            <div class="unit-price-summary" aria-label="Informacion del catalogo">
                <span><strong><?php echo e(number_format($unitPrices->total())); ?></strong> conceptos encontrados</span>
                <span><strong>27.47%</strong> indirecto integrado en el P.U. oficial</span>
            </div>

            <form class="unit-price-filters" method="GET" action="<?php echo e(route('construction.placeholder', 'tabulador-precios-unitarios')); ?>">
                <label>
                    Buscar concepto
                    <input name="q" value="<?php echo e($search); ?>" placeholder="Clave, concepto o unidad">
                </label>
                <label>
                    Capitulo
                    <select name="chapter">
                        <option value="">Todos los capitulos</option>
                        <?php $__currentLoopData = $chapters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chapter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($chapter->chapter_code); ?>" <?php if($selectedChapter === $chapter->chapter_code): echo 'selected'; endif; ?>>
                                <?php echo e($chapter->chapter_code); ?> · <?php echo e($chapter->chapter_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </label>
                <div class="unit-price-filter-actions">
                    <button class="button primary" type="submit">Buscar</button>
                    <a class="button ghost" href="<?php echo e(route('construction.placeholder', 'tabulador-precios-unitarios')); ?>">Limpiar</a>
                </div>
            </form>

            <p class="unit-price-source-note">
                El documento fuente publica unicamente el P.U. integrado. Las columnas separadas de mano de obra y materiales se conservan como datos independientes sin asignar valores estimados.
            </p>

            <div class="table-scroll unit-price-scroll">
                <table class="unit-price-table" data-no-column-tools>
                    <thead>
                        <tr>
                            <th>Clave</th>
                            <th>Concepto de obra</th>
                            <th>Unidad</th>
                            <th>P.U. mano de obra</th>
                            <th>P.U. materiales</th>
                            <th>P.U. total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $unitPrices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unitPrice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($unitPrice->code); ?></strong>
                                    <?php if($unitPrice->source_page): ?>
                                        <small>Pagina <?php echo e($unitPrice->source_page); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($unitPrice->description); ?></td>
                                <td><?php echo e($unitPrice->unit); ?></td>
                                <td class="unit-price-amount">
                                    <?php if($unitPrice->labor_unit_price !== null): ?>
                                        $<?php echo e(number_format((float) $unitPrice->labor_unit_price, 2)); ?>

                                    <?php else: ?>
                                        <span class="unit-price-unavailable">No publicado</span>
                                    <?php endif; ?>
                                </td>
                                <td class="unit-price-amount">
                                    <?php if($unitPrice->material_unit_price !== null): ?>
                                        $<?php echo e(number_format((float) $unitPrice->material_unit_price, 2)); ?>

                                    <?php else: ?>
                                        <span class="unit-price-unavailable">No publicado</span>
                                    <?php endif; ?>
                                </td>
                                <td class="unit-price-amount unit-price-total">$<?php echo e(number_format((float) $unitPrice->total_unit_price, 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td class="empty-state" colspan="6">No hay conceptos que coincidan con los filtros.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination-bar unit-price-pagination">
                <span>
                    <?php if($unitPrices->total()): ?>
                        Mostrando <?php echo e(number_format($unitPrices->firstItem())); ?> a <?php echo e(number_format($unitPrices->lastItem())); ?> de <?php echo e(number_format($unitPrices->total())); ?>

                    <?php else: ?>
                        Sin resultados
                    <?php endif; ?>
                </span>
                <div class="filter-actions">
                    <?php if($unitPrices->onFirstPage()): ?>
                        <span class="button ghost small disabled">Anterior</span>
                    <?php else: ?>
                        <a class="button ghost small" href="<?php echo e($unitPrices->previousPageUrl()); ?>">Anterior</a>
                    <?php endif; ?>

                    <span>Pagina <?php echo e($unitPrices->currentPage()); ?> de <?php echo e($unitPrices->lastPage()); ?></span>

                    <?php if($unitPrices->hasMorePages()): ?>
                        <a class="button ghost small" href="<?php echo e($unitPrices->nextPageUrl()); ?>">Siguiente</a>
                    <?php else: ?>
                        <span class="button ghost small disabled">Siguiente</span>
                    <?php endif; ?>
                </div>
            </div>
        </section>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Revision OC Software\resources\views\construction\unit-prices\index.blade.php ENDPATH**/ ?>