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
        <input name="code" maxlength="40" value="{{ $values['code'] }}" placeholder="Ej: NOM-S28" required>
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
    <label>Periodicidad
        <select name="periodicity" required>
            @foreach ($payrollPeriodicityOptions as $periodicity)
                <option value="{{ $periodicity }}" @selected($values['periodicity'] === $periodicity)>{{ $periodicity }}</option>
            @endforeach
        </select>
    </label>
</div>

<div class="grid-2">
    <label>Inicio del periodo
        <input type="date" name="period_start" value="{{ $values['period_start'] }}" required>
    </label>
    <label>Fin del periodo
        <input type="date" name="period_end" value="{{ $values['period_end'] }}" required>
    </label>
</div>

<div class="grid-2">
    <label>Avance %
        <input type="number" name="progress" min="0" max="100" step="0.01" value="{{ $values['progress'] }}" required>
    </label>
    <label>Monto
        <input type="number" name="amount" min="0" step="0.01" value="{{ $values['amount'] }}" required>
    </label>
</div>

<div class="grid-2">
    <label>Estado
        <select name="status" required>
            @foreach ($payrollStatusOptions as $payrollStatus)
                <option value="{{ $payrollStatus }}" @selected($values['status'] === $payrollStatus)>{{ $payrollStatus }}</option>
            @endforeach
        </select>
    </label>
    <label>Fecha limite de pago
        <input type="date" name="payment_due_date" value="{{ $values['payment_due_date'] }}" required>
    </label>
</div>
