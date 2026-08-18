<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'companies' => collect(),
    'supplyWarehouses' => collect(),
    'managedUser' => null,
]));

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

foreach (array_filter(([
    'companies' => collect(),
    'supplyWarehouses' => collect(),
    'managedUser' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $companies = collect($companies);
    $supplyWarehouses = collect($supplyWarehouses);
    $selectedCompanyNames = $managedUser ? $managedUser->authorizedCompanyNames() : $companies->pluck('name')->all();
?>

<div class="companies-box">
    <div class="company-selector auth-split-selector" data-company-selector data-company-warehouse-selector>
        <div class="auth-selector-pane auth-company-pane">
            <div class="company-selector-header">
                <label>Empresas autorizadas</label>
                <span data-company-count></span>
            </div>
            <input class="company-selector-search" type="search" placeholder="Buscar empresa...">
            <div class="company-selector-actions">
                <button class="button ghost small" type="button" data-company-select-all>Todas</button>
                <button class="button ghost small" type="button" data-company-clear>Limpiar</button>
            </div>
            <div class="company-selector-list auth-company-list">
                <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $companySelected = in_array($company->name, $selectedCompanyNames, true); ?>
                    <label class="company-selector-option auth-company-option" data-company-option data-company-id="<?php echo e($company->id); ?>">
                        <input class="company-checkbox" name="companies[]" type="checkbox" value="<?php echo e($company->id); ?>" <?php if($companySelected): echo 'checked'; endif; ?>>
                        <span><?php echo e($company->name); ?></span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="auth-selector-pane auth-warehouse-pane">
            <div class="company-selector-header">
                <label>Almacenes autorizados</label>
                <span data-warehouse-count></span>
            </div>
            <div class="auth-warehouse-scroll">
                <table class="auth-warehouse-table">
                    <thead>
                        <tr>
                            <th>Empresa</th>
                            <th>Alias de Almacen</th>
                            <th>Ubicacion del almacen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $supplyWarehouses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplyWarehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $supplyWarehouse['companies']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $servedCompany): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $company = $companies->firstWhere('id', $servedCompany['id']);
                                    if (! $company) {
                                        continue;
                                    }
                                    $selectedWarehouses = $managedUser ? $managedUser->authorizedWarehousesFor($company->name) : [];
                                    $warehouseSelected = $managedUser ? in_array($supplyWarehouse['label'], $selectedWarehouses, true) : true;
                                    $companySelected = in_array($company->name, $selectedCompanyNames, true);
                                ?>
                                <tr class="auth-supply-warehouse-row" data-warehouse-row data-company-id="<?php echo e($company->id); ?>">
                                    <td>
                                        <label class="auth-table-check">
                                            <input name="supply_warehouses[]" type="checkbox" value="<?php echo e($supplyWarehouse['key']); ?>|<?php echo e($company->id); ?>" <?php if($warehouseSelected && $companySelected): echo 'checked'; endif; ?>>
                                            <span><?php echo e($company->name); ?></span>
                                        </label>
                                    </td>
                                    <td><?php echo e($supplyWarehouse['label']); ?></td>
                                    <td><?php echo e($supplyWarehouse['address'] ?: ($company->address ?: 'Sin ubicacion registrada')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $companySelected = in_array($company->name, $selectedCompanyNames, true);
                                $selectedWarehouses = $managedUser ? $managedUser->authorizedWarehousesFor($company->name) : [];
                            ?>
                            <?php $__currentLoopData = $company->warehouseObjects(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $warehouse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $warehouseSelected = $managedUser
                                        ? (empty($selectedWarehouses) ? $companySelected : in_array($warehouse['name'], $selectedWarehouses, true))
                                        : true;
                                ?>
                                <tr data-warehouse-row data-company-id="<?php echo e($company->id); ?>">
                                    <td>
                                        <label class="auth-table-check">
                                            <input name="warehouses[<?php echo e($company->id); ?>][]" type="checkbox" value="<?php echo e($warehouse['name']); ?>" <?php if($warehouseSelected && $companySelected): echo 'checked'; endif; ?>>
                                            <span><?php echo e($company->name); ?></span>
                                        </label>
                                    </td>
                                    <td><?php echo e($warehouse['short_name'] ?: $warehouse['name']); ?></td>
                                    <td><?php echo e($company->address ?: 'Sin ubicacion registrada'); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        <tr data-empty-warehouses hidden>
                            <td colspan="3">Selecciona una empresa para ver sus almacenes.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\Revision OC Software\resources\views/components/company-warehouse-selector.blade.php ENDPATH**/ ?>