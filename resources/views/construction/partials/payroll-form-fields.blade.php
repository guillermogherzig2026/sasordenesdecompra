@php
    $periodEndIndefinite = (bool) ($values['period_end_indefinite'] ?? true);
    $periodEndInputId = ($isCreateForm ?? false) ? 'payroll-period-end-create' : 'payroll-period-end-edit';
    $periodEndIndefiniteId = $periodEndInputId.'-indefinite';
    $periodicityInputId = ($isCreateForm ?? false) ? 'payroll-periodicity-create' : 'payroll-periodicity-edit';
    $periodicityDescriptions = [
        'Semanal' => 'Cada domingo se genera una OP en Finanzas con fecha limite de pago el viernes siguiente.',
        'Quincenal' => 'Pago programado los dias 15 y 30 de cada mes. Aparece en Finanzas desde los dias 1 y 16.',
        'Mensual' => 'Pago programado una vez al mes en la fecha limite indicada.',
    ];
    $amountValue = trim((string) ($values['amount'] ?? ''));
    $amountValue = preg_replace('/^\$\s*/', '', $amountValue) ?? $amountValue;
    $amountNumericValue = str_replace([',', ' '], '', $amountValue);
    $amountDisplayValue = is_numeric($amountNumericValue) ? number_format((float) $amountNumericValue, 2, '.', ',') : $amountValue;
@endphp

<div class="grid-2">
    <label>Obra
        <select name="construction_project_id" @if ($isCreateForm ?? false) data-payroll-project-select @endif required>
            @foreach ($activeProjects as $projectOption)
                <option value="{{ $projectOption->id }}" @selected((int) $values['construction_project_id'] === $projectOption->id)>
                    {{ $projectOption->project_key }} - {{ $projectOption->name }}
                </option>
            @endforeach
        </select>
    </label>
<label>Codigo
    <input name="code" maxlength="9" value="{{ $values['code'] }}" readonly aria-readonly="true" required>
</label>
</div>

<div class="grid-2">
    <label>Contratista
        <input name="contractor" maxlength="255" value="{{ $values['contractor'] }}" required>
    </label>
    <label>Descripcion
        <input name="description" maxlength="255" value="{{ $values['description'] }}" placeholder="Ej: Nomina quincenal S28" required>
    </label>
</div>

<div class="grid-2">
    <label>Area / categoria
        <input name="area" maxlength="120" value="{{ $values['area'] }}" placeholder="Ej: Mano de obra">
    </label>
    <div class="payroll-periodicity-field" data-payroll-periodicity-field>
        <label for="{{ $periodicityInputId }}">Periodicidad</label>
        <select id="{{ $periodicityInputId }}" name="periodicity" data-payroll-periodicity required>
            @foreach ($payrollPeriodicityOptions as $periodicity)
                <option value="{{ $periodicity }}" @selected($values['periodicity'] === $periodicity)>{{ $periodicity }}</option>
            @endforeach
        </select>
        <p class="fine-print payroll-periodicity-description" data-payroll-periodicity-description>{{ $periodicityDescriptions[$values['periodicity']] ?? '' }}</p>
    </div>
</div>

<div class="grid-2">
    <label>Inicio del periodo
        <input type="date" name="period_start" value="{{ $values['period_start'] }}" data-payroll-period-start required>
    </label>
    <div class="payroll-period-end-field" data-payroll-period-end-field>
        <label for="{{ $periodEndInputId }}">Fin del periodo</label>
        <div class="payroll-period-end-control">
            <input id="{{ $periodEndInputId }}" type="date" name="period_end" value="{{ $values['period_end'] }}" data-payroll-period-end @disabled($periodEndIndefinite) @required(! $periodEndIndefinite)>
            <input type="hidden" name="period_end_indefinite" value="0">
            <label class="payroll-period-end-toggle" for="{{ $periodEndIndefiniteId }}">
                <input id="{{ $periodEndIndefiniteId }}" type="checkbox" name="period_end_indefinite" value="1" @checked($periodEndIndefinite) data-payroll-period-end-toggle>
                <span>Indefinido</span>
            </label>
        </div>
    </div>
</div>

<input type="hidden" name="progress" value="{{ $values['progress'] }}">
<label>Monto
    <span class="payroll-currency-input">
        <span class="payroll-currency-symbol" aria-hidden="true">$</span>
        <input type="text" name="amount" inputmode="decimal" autocomplete="off" spellcheck="false" value="{{ $amountDisplayValue }}" placeholder="0.00" required>
    </span>
</label>

<div class="grid-2">
    <label>Estado
        <select name="status" required>
            @foreach ($payrollStatusOptions as $payrollStatus)
                <option value="{{ $payrollStatus }}" @selected($values['status'] === $payrollStatus)>{{ $payrollStatus }}</option>
            @endforeach
        </select>
    </label>
    <label>Fecha limite de pago
        <input type="date" name="payment_due_date" value="{{ $values['payment_due_date'] }}" data-payroll-payment-due @readonly(in_array($values['periodicity'], ['Semanal', 'Quincenal'], true)) required>
    </label>
</div>

@once
    <script>
        (() => {
            const initializeIndefinitePeriodFields = () => {
                document.querySelectorAll('[data-payroll-period-end-field]').forEach((field) => {
                    const dateInput = field.querySelector('[data-payroll-period-end]');
                    const indefiniteToggle = field.querySelector('[data-payroll-period-end-toggle]');

                    if (!dateInput || !indefiniteToggle) {
                        return;
                    }

                    const synchronizePeriodEnd = () => {
                        dateInput.disabled = indefiniteToggle.checked;
                        dateInput.required = !indefiniteToggle.checked;

                        if (indefiniteToggle.checked) {
                            dateInput.value = '';
                        }
                    };

                    indefiniteToggle.addEventListener('change', synchronizePeriodEnd);
                    synchronizePeriodEnd();
                });
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeIndefinitePeriodFields, { once: true });
            } else {
                initializeIndefinitePeriodFields();
            }
        })();
    </script>
@endonce

@once
    <script>
        (() => {
            const periodicityDescriptions = @json($periodicityDescriptions);

            const formatLocalDate = (date) => [
                date.getFullYear(),
                String(date.getMonth() + 1).padStart(2, '0'),
                String(date.getDate()).padStart(2, '0'),
            ].join('-');

            const calculateWeeklyDueDate = (value) => {
                if (!value) {
                    return '';
                }

                const [year, month, day] = value.split('-').map(Number);
                if (!year || !month || !day) {
                    return '';
                }

                const date = new Date(year, month - 1, day, 12);
                const daysUntilSunday = (7 - date.getDay()) % 7;
                date.setDate(date.getDate() + daysUntilSunday + 5);

                return formatLocalDate(date);
            };

            const calculateQuincenalDueDate = (value) => {
                if (!value) {
                    return '';
                }

                const [year, month, day] = value.split('-').map(Number);
                if (!year || !month || !day) {
                    return '';
                }

                let dueYear = year;
                let dueMonth = month;
                let dueDay;

                if (day <= 15) {
                    dueDay = 15;
                } else if (day <= 30) {
                    const lastDay = new Date(year, month, 0).getDate();
                    dueDay = Math.min(30, lastDay);
                } else {
                    dueMonth += 1;
                    if (dueMonth > 12) {
                        dueMonth = 1;
                        dueYear += 1;
                    }
                    dueDay = 15;
                }

                return [
                    dueYear,
                    String(dueMonth).padStart(2, '0'),
                    String(dueDay).padStart(2, '0'),
                ].join('-');
            };

            const initializePeriodicityFields = () => {
                document.querySelectorAll('[data-payroll-periodicity-field]').forEach((field) => {
                    const select = field.querySelector('[data-payroll-periodicity]');
                    const description = field.querySelector('[data-payroll-periodicity-description]');
                    const form = field.closest('form');
                    const periodStart = form?.querySelector('[data-payroll-period-start]');
                    const paymentDue = form?.querySelector('[data-payroll-payment-due]');

                    if (!select || !description || !periodStart || !paymentDue) {
                        return;
                    }

                    const synchronizePeriodicity = () => {
                        const isWeekly = select.value === 'Semanal';
                        const isQuincenal = select.value === 'Quincenal';
                        const isAutomatic = isWeekly || isQuincenal;
                        description.textContent = periodicityDescriptions[select.value] || '';
                        paymentDue.readOnly = isAutomatic;
                        paymentDue.toggleAttribute('aria-readonly', isAutomatic);

                        if (isWeekly) {
                            paymentDue.value = calculateWeeklyDueDate(periodStart.value);
                        } else if (isQuincenal) {
                            paymentDue.value = calculateQuincenalDueDate(periodStart.value);
                        }
                    };

                    select.addEventListener('change', synchronizePeriodicity);
                    periodStart.addEventListener('change', synchronizePeriodicity);
                    synchronizePeriodicity();
                });
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializePeriodicityFields, { once: true });
            } else {
                initializePeriodicityFields();
            }
        })();
    </script>
@endonce
