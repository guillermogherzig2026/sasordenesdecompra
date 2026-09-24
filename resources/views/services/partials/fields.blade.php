    @php
        $service = $service ?? null;
        $isEditing = (bool) $service;
        $requestedCompany = (string) request()->query('company');
        $defaultCompany = $companies->contains('name', $requestedCompany) ? $requestedCompany : $companies->first()?->name;
        $selectedCompany = old('company_name', $service?->company_name ?? $defaultCompany);
        $requestedCategory = (string) request()->query('category');
        $selectedCategory = old('category', $service?->category ?? ($categories->contains($requestedCategory) ? $requestedCategory : ''));
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
    @endphp

            <section class="service-form-section" aria-labelledby="service-form-general-title">
                <h3 id="service-form-general-title">Información del servicio</h3>
            <div class="grid-4">
                <label>Empresa / titular
                    <select name="company_name" required onchange="this.form.holder.value = this.value">
                        @foreach ($companies as $company)
                            <option value="{{ $company->name }}" @selected($selectedCompany === $company->name)>{{ $company->name }}</option>
                        @endforeach
                        @if ($selectedCompany && ! $companies->contains('name', $selectedCompany))
                            <option value="{{ $selectedCompany }}" selected>{{ $selectedCompany }}</option>
                        @endif
                    </select>
                    <input type="hidden" name="holder" value="{{ old('holder', $service?->holder ?? $selectedCompany) }}">
                </label>
                <label>Nombre del servicio<input name="service_name" value="{{ old('service_name', $service?->service_name) }}" placeholder="Ej: Telefonia Telmex" required></label>
                <label>Categoria
                    <select name="category" required>
                        <option value="">Seleccionar categoria...</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" @selected($selectedCategory === $category)>{{ $category === 'Mantenimiento' ? 'Mantenimiento correctivo y preventivo' : $category }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Proveedor<input name="provider" value="{{ old('provider', $service?->provider) }}" placeholder="Nombre del proveedor" required></label>
            </div>

            <div class="grid-4">
                <label>Numero de servicio<input name="service_number" value="{{ old('service_number', $service?->service_number) }}" placeholder="Numero de servicio" required></label>
                <label>Costo del servicio<input name="cost" type="number" min="0" step="0.01" value="{{ old('cost', $service?->cost) }}" placeholder="0.00" required></label>
                <label>Vigencia del servicio
                    <select name="validity" required>
                        @foreach ($validityOptions as $validityOption)
                            <option value="{{ $validityOption }}" @selected($selectedValidity === $validityOption)>{{ $validityOption }}</option>
                        @endforeach
                        @if ($selectedValidity && ! in_array($selectedValidity, $validityOptions, true))
                            <option value="{{ $selectedValidity }}" selected>{{ $selectedValidity }}</option>
                        @endif
                    </select>
                </label>
                <label>Lapso de pago en dias
                    <select name="payment_lapse" required>
                        <option value="30" @selected((string) $selectedPaymentLapse === '30')>30</option>
                        <option value="60" @selected((string) $selectedPaymentLapse === '60')>60</option>
                        <option value="90" @selected((string) $selectedPaymentLapse === '90')>90</option>
                        <option value="180" @selected((string) $selectedPaymentLapse === '180')>180</option>
                        <option value="365" @selected((string) $selectedPaymentLapse === '365')>365</option>
                    </select>
                </label>
            </div>
            </section>

            <section class="service-form-section" aria-labelledby="service-form-schedule-title">
                <h3 id="service-form-schedule-title">Programación y fechas</h3>
            <div class="grid-4 service-form-dates">
                <label>Periodo — Fecha de inicio
                    <div style="display:flex;gap:6px">
                        <select name="start_day" required style="flex:1">
                            @foreach (range(1, 31) as $d)
                                <option value="{{ $d }}" @selected((int) $selectedStartDay === $d)>{{ $d }}</option>
                            @endforeach
                        </select>
                        <select name="start_month" required style="flex:2">
                            @foreach ($monthNames as $num => $name)
                                <option value="{{ $num }}" @selected((int) $selectedStartMonth === $num)>{{ $name }}</option>
                            @endforeach
                        </select>
                        <select name="start_year" required style="flex:1">
                            @foreach ($yearOptions as $year)
                                <option value="{{ $year }}" @selected((int) $selectedStartYear === $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </label>
                <label>Periodo — Fecha de corte
                    <div style="display:flex;gap:6px">
                        <select name="cutoff_day" required style="flex:1">
                            @foreach (range(1, 31) as $d)
                                <option value="{{ $d }}" @selected((int) $selectedCutoffDay === $d)>{{ $d }}</option>
                            @endforeach
                        </select>
                        <select name="cutoff_month" required style="flex:2">
                            @foreach ($monthNames as $num => $name)
                                <option value="{{ $num }}" @selected((int) $selectedCutoffMonth === $num)>{{ $name }}</option>
                            @endforeach
                        </select>
                        <select name="cutoff_year" required style="flex:1">
                            @foreach ($yearOptions as $year)
                                <option value="{{ $year }}" @selected((int) $selectedCutoffYear === $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </label>
                <label>Fecha de vencimiento
                    <input type="date" id="due_date_display" readonly required style="background:#f8f9fa;cursor:not-allowed">
                </label>
            </div>
            </section>

            <section class="service-form-section" aria-labelledby="service-form-payment-title">
                <h3 id="service-form-payment-title">Pago y ubicación</h3>
            <div class="grid-4">
                <label>Banco<input name="bank" value="{{ old('bank', $service?->bank) }}" placeholder="Banco" required></label>
                <label>Cuenta pagadora<input name="payer_account" value="{{ old('payer_account', $service?->payer_account) }}" placeholder="Cuenta" required></label>
                <label>Referencia o linea de captura<input name="reference" value="{{ old('reference', $service?->reference) }}" placeholder="Referencia" required></label>
            </div>

            <label class="checkbox-inline">
                <input name="is_domiciled" type="checkbox" value="1" @checked($isDomiciled)>
                Pago Domiciliado
            </label>

            <label>Sucursal
                <input name="branch" value="{{ old('branch', $service?->branch) }}" placeholder="Nombre de la sucursal">
            </label>

            <label>Ubicacion / Direccion del servicio
                <textarea name="service_location" placeholder="Direccion donde se presta o factura el servicio">{{ old('service_location', $service?->service_location) }}</textarea>
            </label>

            <label>Notas adicionales<textarea name="notes" placeholder="Observaciones...">{{ old('notes', $service?->notes) }}</textarea></label>
            </section>
