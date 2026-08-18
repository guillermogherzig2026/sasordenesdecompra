@props([
    'companies' => collect(),
    'supplyWarehouses' => collect(),
    'managedUser' => null,
])

@php
    $companies = collect($companies);
    $supplyWarehouses = collect($supplyWarehouses);
    $selectedCompanyNames = $managedUser ? $managedUser->authorizedCompanyNames() : $companies->pluck('name')->all();
@endphp

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
                @foreach ($companies as $company)
                    @php $companySelected = in_array($company->name, $selectedCompanyNames, true); @endphp
                    <label class="company-selector-option auth-company-option" data-company-option data-company-id="{{ $company->id }}">
                        <input class="company-checkbox" name="companies[]" type="checkbox" value="{{ $company->id }}" @checked($companySelected)>
                        <span>{{ $company->name }}</span>
                    </label>
                @endforeach
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
                        @foreach ($supplyWarehouses as $supplyWarehouse)
                            @foreach ($supplyWarehouse['companies'] as $servedCompany)
                                @php
                                    $company = $companies->firstWhere('id', $servedCompany['id']);
                                    if (! $company) {
                                        continue;
                                    }
                                    $selectedWarehouses = $managedUser ? $managedUser->authorizedWarehousesFor($company->name) : [];
                                    $warehouseSelected = $managedUser ? in_array($supplyWarehouse['label'], $selectedWarehouses, true) : true;
                                    $companySelected = in_array($company->name, $selectedCompanyNames, true);
                                @endphp
                                <tr class="auth-supply-warehouse-row" data-warehouse-row data-company-id="{{ $company->id }}">
                                    <td>
                                        <label class="auth-table-check">
                                            <input name="supply_warehouses[]" type="checkbox" value="{{ $supplyWarehouse['key'] }}|{{ $company->id }}" @checked($warehouseSelected && $companySelected)>
                                            <span>{{ $company->name }}</span>
                                        </label>
                                    </td>
                                    <td>{{ $supplyWarehouse['label'] }}</td>
                                    <td>{{ $supplyWarehouse['address'] ?: ($company->address ?: 'Sin ubicacion registrada') }}</td>
                                </tr>
                            @endforeach
                        @endforeach

                        @foreach ($companies as $company)
                            @php
                                $companySelected = in_array($company->name, $selectedCompanyNames, true);
                                $selectedWarehouses = $managedUser ? $managedUser->authorizedWarehousesFor($company->name) : [];
                            @endphp
                            @foreach ($company->warehouseObjects() as $warehouse)
                                @php
                                    $warehouseSelected = $managedUser
                                        ? (empty($selectedWarehouses) ? $companySelected : in_array($warehouse['name'], $selectedWarehouses, true))
                                        : true;
                                @endphp
                                <tr data-warehouse-row data-company-id="{{ $company->id }}">
                                    <td>
                                        <label class="auth-table-check">
                                            <input name="warehouses[{{ $company->id }}][]" type="checkbox" value="{{ $warehouse['name'] }}" @checked($warehouseSelected && $companySelected)>
                                            <span>{{ $company->name }}</span>
                                        </label>
                                    </td>
                                    <td>{{ $warehouse['short_name'] ?: $warehouse['name'] }}</td>
                                    <td>{{ $company->address ?: 'Sin ubicacion registrada' }}</td>
                                </tr>
                            @endforeach
                        @endforeach

                        <tr data-empty-warehouses hidden>
                            <td colspan="3">Selecciona una empresa para ver sus almacenes.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
