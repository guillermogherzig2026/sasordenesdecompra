@extends('layouts.app')

@php
    $items = old('items', $order?->items?->map(fn ($item) => [
        'article' => $item->article,
        'quantity' => $item->quantity,
        'unit_price' => $item->unit_price,
    ])->all() ?? [['article' => '', 'quantity' => '', 'unit_price' => 0]]);
    $isCredit = old('is_credit', $order?->is_credit ? '1' : null);
    $selectedProviderId = (int) old('provider_id', $order?->provider_id);
    $selectedProvider = $providers->firstWhere('id', $selectedProviderId);
    $referenceValue = old('reference', $order?->reference ?? $selectedProvider?->reference);
    $selectedCompanyId = (int) old('company_id', $order?->company_id);
    $selectedWarehouse = old('warehouse', $order?->warehouse);
    $constructionContext = $constructionContext ?? false;
    $constructionProjects = $constructionProjects ?? collect();
    $routeContext = $constructionContext ? ['context' => 'construction'] : [];
    $selectedConstructionProjectId = (int) old('construction_project_id', $order?->construction_project_id);
    $formAction = $order
        ? route('buyer.orders.update', array_merge(['purchaseOrder' => $order], $routeContext))
        : route('buyer.orders.store', $routeContext);
    $currentUser = auth()->user();
    $warehousesByCompany = $companies->mapWithKeys(function ($company) use ($currentUser) {
        if ($currentUser?->role === 'superadmin') {
            $objects = $company->warehouseObjects();
        } else {
            $authorized = $currentUser?->authorizedWarehousesFor($company->name) ?: $company->warehouseList();
            $objects = collect($authorized)->map(fn ($name) => [
                'name' => $name,
                'short_name' => $company->warehouseShortNameFor($name),
            ])->all();
        }

        return [$company->id => $objects];
    });
@endphp

@section('body')
    <x-app-shell :title="$constructionContext ? ($order ? 'Editar orden de compra de obra' : 'Nueva orden de compra de obra') : ($order ? 'Editar orden de compra' : 'Nueva orden de compra')">
        <form class="panel" method="POST" enctype="multipart/form-data" action="{{ $formAction }}">
            @csrf
            @if ($order)
                @method('PUT')
            @endif

            <div class="panel-header">
                <div>
                    <h2>{{ $order ? "Editar {$order->folio}" : ($constructionContext ? 'Nueva orden de compra de obra' : 'Nueva orden de compra') }}</h2>
                    <p class="fine-print">{{ $constructionContext ? 'La orden quedara vinculada exclusivamente a la obra seleccionada.' : 'La orden se guarda como enviada y queda pendiente de revision por Finanzas.' }}</p>
                </div>
                <a class="button ghost" href="{{ route('buyer.orders.index', $routeContext) }}">Volver</a>
            </div>

            @if ($constructionContext)
                <div class="grid-3">
                    <label>
                        Obra
                        <select name="construction_project_id" required>
                            <option value="">Seleccionar obra...</option>
                            @foreach ($constructionProjects as $project)
                                <option value="{{ $project->id }}" @selected($selectedConstructionProjectId === $project->id)>
                                    {{ $project->project_key }} - {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </label>
                </div>
            @endif

            <div class="grid-3">
                <label>
                    Empresa autorizada
                    <select id="company-select" name="company_id" required>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" @selected($selectedCompanyId === $company->id)>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Almacen o departamento de recepcion
                    <select id="warehouse-select" name="warehouse" data-selected="{{ $selectedWarehouse }}" aria-describedby="warehouse-help" required></select>
                    <span id="warehouse-help" class="fine-print" aria-live="polite"></span>
                </label>
                <label>
                    Proveedor
                    <select name="provider_id" required>
                        <option value="">Seleccionar proveedor...</option>
                        @foreach ($providers as $provider)
                            <option value="{{ $provider->id }}" data-reference="{{ $provider->reference }}" @selected((int) old('provider_id', $order?->provider_id) === $provider->id)>
                                {{ $provider->business_name }} - {{ $provider->business_line }}
                            </option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="grid-3">
                <label>
                    Fecha de entrega
                    <input name="delivery_date" type="date" value="{{ old('delivery_date', $order?->delivery_date?->format('Y-m-d')) }}" required>
                </label>
                <label>
                    Fecha de vencimiento
                    <input name="due_date" type="date" value="{{ old('due_date', $order?->due_date?->format('Y-m-d')) }}" required>
                </label>
                <div class="credit-control">
                    <label class="checkbox-inline">
                        <input id="is-credit" name="is_credit" type="checkbox" value="1" @checked((bool) $isCredit)>
                        Credito
                    </label>
                    <label>
                        Dias de credito
                        <input id="credit-days" name="credit_days" type="number" min="1" max="366" value="{{ old('credit_days', $order?->credit_days) }}" @disabled(! $isCredit)>
                    </label>
                </div>
            </div>

            <div class="grid-2">
                <label>
                    Referencia
                    <input id="order-reference" name="reference" value="{{ $referenceValue }}" placeholder="Referencia bancaria o linea de captura">
                </label>
                <label>
                    Concepto de pago
                    <input name="payment_concept" value="{{ old('payment_concept', $order?->payment_concept) }}" placeholder="Concepto libre para el pago">
                </label>
            </div>

            <label>
                Observaciones para PDF
                <textarea name="observations" rows="3" placeholder="Observaciones que solo apareceran en el PDF de la orden de compra">{{ old('observations', $order?->observations) }}</textarea>
            </label>

            <label>
                Cotizacion del proveedor (PDF/JPG)
                <input name="quote_file" type="file" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png">
                @if ($order?->quote_file_path)
                    <span class="fine-print">Archivo actual: {{ $order->quote_original_name }}</span>
                @endif
            </label>

            <div class="table-scroll">
                <table id="items-table">
                    <thead>
                        <tr>
                            <th>Articulo</th>
                            <th>Cantidad</th>
                            <th>Precio unit.</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $index => $item)
                            <tr>
                                <td><input name="items[{{ $index }}][article]" value="{{ $item['article'] ?? '' }}" required></td>
                                <td><input name="items[{{ $index }}][quantity]" type="number" min="0.01" step="0.01" value="{{ $item['quantity'] ?? '' }}" required></td>
                                <td><input name="items[{{ $index }}][unit_price]" type="number" min="0" step="0.001" value="{{ $item['unit_price'] ?? 0 }}" required></td>
                                <td><strong data-row-subtotal>$0.00</strong></td>
                                <td><button class="button ghost small" type="button" data-remove-row>Quitar</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="form-actions order-total-actions">
                <button class="button ghost" type="button" id="add-item">Agregar articulo</button>
                <div class="total-preview" aria-live="polite">
                    <span>Total de articulos</span>
                    <strong id="order-total">$0.00</strong>
                </div>
                <button class="button primary" id="order-submit" type="submit">{{ $order ? 'Guardar cambios' : 'Enviar OC' }}</button>
            </div>
        </form>

        <script>
            const tableBody = document.querySelector('#items-table tbody');
            const addButton = document.querySelector('#add-item');
            const companySelect = document.querySelector('#company-select');
            const warehouseSelect = document.querySelector('#warehouse-select');
            const warehouseHelp = document.querySelector('#warehouse-help');
            const orderSubmit = document.querySelector('#order-submit');
            const warehousesByCompany = @json($warehousesByCompany);
            const creditCheckbox = document.querySelector('#is-credit');
            const creditDays = document.querySelector('#credit-days');
            const deliveryDate = document.querySelector('input[name="delivery_date"]');
            const dueDate = document.querySelector('input[name="due_date"]');
            const providerSelect = document.querySelector('select[name="provider_id"]');
            const referenceInput = document.querySelector('#order-reference');
            const orderTotal = document.querySelector('#order-total');
            let lastProviderReference = referenceInput?.value || '';

            function syncWarehouseOptions() {
                if (!companySelect || !warehouseSelect) return;

                const selected = warehouseSelect.dataset.selected || warehouseSelect.value;
                const warehouses = warehousesByCompany[companySelect.value] || [];
                warehouseSelect.innerHTML = '';
                warehouseSelect.required = true;

                if (!warehouses.length) {
                    const companySelected = Boolean(companySelect.value);
                    warehouseSelect.disabled = true;
                    warehouseSelect.insertAdjacentHTML(
                        'beforeend',
                        `<option value="">${companySelected ? 'La empresa no tiene almacenes disponibles' : 'Selecciona una empresa primero'}</option>`
                    );
                    warehouseHelp.textContent = companySelected
                        ? 'Registra un almacen para la empresa antes de enviar la orden de compra.'
                        : 'Selecciona una empresa para consultar sus almacenes.';
                    orderSubmit.disabled = true;
                    return;
                }

                warehouseSelect.disabled = false;
                warehouseHelp.textContent = '';
                orderSubmit.disabled = false;
                warehouseSelect.insertAdjacentHTML('beforeend', '<option value="">Seleccionar almacen o departamento de recepcion...</option>');
                warehouses.forEach((warehouse) => {
                    const label = warehouse.short_name ? `${warehouse.name} (${warehouse.short_name})` : warehouse.name;
                    const option = new Option(label, warehouse.name, false, warehouse.name === selected);
                    warehouseSelect.add(option);
                });
            }

            companySelect?.addEventListener('change', () => {
                warehouseSelect.dataset.selected = '';
                syncWarehouseOptions();
            });
            syncWarehouseOptions();

            function calculateDueDateFromCredit() {
                if (!creditCheckbox.checked || !deliveryDate.value || !creditDays.value) return;

                const days = Number.parseInt(creditDays.value, 10);
                if (!Number.isFinite(days) || days < 1) return;

                const nextDueDate = new Date(`${deliveryDate.value}T00:00:00`);
                nextDueDate.setDate(nextDueDate.getDate() + days);
                dueDate.value = nextDueDate.toISOString().slice(0, 10);
            }

            function syncCredit() {
                creditDays.disabled = !creditCheckbox.checked;
                creditDays.required = creditCheckbox.checked;
                if (!creditCheckbox.checked) {
                    creditDays.value = '';
                    return;
                }

                calculateDueDateFromCredit();
            }

            creditCheckbox.addEventListener('change', syncCredit);
            creditDays.addEventListener('input', calculateDueDateFromCredit);
            deliveryDate.addEventListener('change', calculateDueDateFromCredit);
            syncCredit();

            providerSelect.addEventListener('change', () => {
                const nextReference = providerSelect.selectedOptions[0]?.dataset.reference || '';

                if (!referenceInput.value || referenceInput.value === lastProviderReference) {
                    referenceInput.value = nextReference;
                }

                lastProviderReference = nextReference;
            });

            function renumberRows() {
                tableBody.querySelectorAll('tr').forEach((row, index) => {
                    row.querySelectorAll('input').forEach((input) => {
                        input.name = input.name.replace(/items\[\d+\]/, `items[${index}]`);
                    });
                });
            }

            const currency = new Intl.NumberFormat('es-MX', {
                style: 'currency',
                currency: 'MXN',
            });

            function numericValue(input) {
                return Number.parseFloat(input?.value || '0') || 0;
            }

            function updateOrderTotal() {
                let total = 0;

                tableBody.querySelectorAll('tr').forEach((row) => {
                    const quantity = numericValue(row.querySelector('input[name*="[quantity]"]'));
                    const unitPrice = numericValue(row.querySelector('input[name*="[unit_price]"]'));
                    const subtotal = quantity * unitPrice;
                    total += subtotal;

                    const subtotalCell = row.querySelector('[data-row-subtotal]');
                    if (subtotalCell) subtotalCell.textContent = currency.format(subtotal);
                });

                if (orderTotal) orderTotal.textContent = currency.format(total);
            }

            addButton.addEventListener('click', () => {
                const index = tableBody.querySelectorAll('tr').length;
                tableBody.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td><input name="items[${index}][article]" required></td>
                        <td><input name="items[${index}][quantity]" type="number" min="0.01" step="0.01" required></td>
                        <td><input name="items[${index}][unit_price]" type="number" min="0" step="0.001" value="0" required></td>
                        <td><strong data-row-subtotal>$0.00</strong></td>
                        <td><button class="button ghost small" type="button" data-remove-row>Quitar</button></td>
                    </tr>
                `);
                updateOrderTotal();
            });

            tableBody.addEventListener('input', (event) => {
                if (event.target.matches('input[name*="[quantity]"], input[name*="[unit_price]"]')) {
                    updateOrderTotal();
                }
            });

            tableBody.addEventListener('click', (event) => {
                const button = event.target.closest('[data-remove-row]');
                if (!button) return;
                if (tableBody.querySelectorAll('tr').length === 1) return;
                button.closest('tr').remove();
                renumberRows();
                updateOrderTotal();
            });

            updateOrderTotal();
        </script>
    </x-app-shell>
@endsection
