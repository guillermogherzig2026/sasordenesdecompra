<div class="grid-2">
    <label>Obra
        <select name="construction_project_id" data-estimate-project-select required>
            @foreach ($activeProjects as $projectOption)
                <option value="{{ $projectOption->id }}" @selected((int) $values['construction_project_id'] === $projectOption->id)>
                    {{ $projectOption->project_key }} - {{ $projectOption->name }}
                </option>
            @endforeach
        </select>
    </label>
    <label>Codigo
        <input name="code" maxlength="40" value="{{ $values['code'] }}" placeholder="Ej: PAQ-009" required>
    </label>
</div>

<div class="grid-2">
    <label>Contratista
        <input name="contractor" maxlength="255" value="{{ $values['contractor'] }}" placeholder="Opcional">
    </label>
    <label>Descripcion
        <input name="description" maxlength="255" value="{{ $values['description'] }}" placeholder="Ej: Acabados interiores Nivel 01" required>
    </label>
</div>

<div class="grid-2">
    <label>Area / categoria
        <input name="area" maxlength="120" value="{{ $values['area'] }}" placeholder="Ej: Albanileria" required>
    </label>
    <label>Periodicidad
        <select name="periodicity" required>
            @foreach (['Semanal', 'Quincenal', 'Mensual'] as $periodicity)
                <option value="{{ $periodicity }}" @selected($values['periodicity'] === $periodicity)>{{ $periodicity }}</option>
            @endforeach
        </select>
    </label>
</div>

<div class="grid-2">
    <label>Periodo / referencia
        <input name="period_reference" maxlength="120" value="{{ $values['period_reference'] }}" placeholder="Ej: 01/09 - 15/09/2026" required>
    </label>
    <label>Fecha limite de pago
        <input type="date" name="payment_due_date" value="{{ $values['payment_due_date'] }}" required>
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

<label>Estado
    <select name="status" required>
        @foreach (['Sin asignar', 'Programado', 'En ejecucion', 'En revision', 'Aprobado'] as $estimateStatus)
            <option value="{{ $estimateStatus }}" @selected($values['status'] === $estimateStatus)>{{ $estimateStatus }}</option>
        @endforeach
    </select>
</label>
