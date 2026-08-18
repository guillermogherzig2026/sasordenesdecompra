@php
    $value = fn ($field, $default = null) => old($field, $project->{$field} ?? $default);
    $dateValue = function ($field) use ($value) {
        $current = $value($field);
        return $current instanceof \Carbon\CarbonInterface ? $current->format('Y-m-d') : $current;
    };
@endphp

@if($errors->any())
    <div class="alert alert-danger">
        Revisa los campos marcados antes de continuar.
    </div>
@endif

<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label" for="project_key">Clave</label>
        <input id="project_key" class="form-control @error('project_key') is-invalid @enderror" name="project_key" value="{{ $value('project_key') }}" required>
        @error('project_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-5">
        <label class="form-label" for="name">Nombre</label>
        <input id="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $value('name') }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="project_type">Tipo de obra</label>
        <input id="project_type" class="form-control @error('project_type') is-invalid @enderror" name="project_type" value="{{ $value('project_type') }}">
        @error('project_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="company_id">Empresa</label>
        <select id="company_id" class="form-select @error('company_id') is-invalid @enderror" name="company_id" required>
            <option value="">Seleccionar</option>
            @foreach($companies as $company)
                <option value="{{ $company->id }}" @selected((string) $value('company_id') === (string) $company->id)>{{ $company->name }}</option>
            @endforeach
        </select>
        @error('company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="client_id">Cliente</label>
        <select id="client_id" class="form-select @error('client_id') is-invalid @enderror" name="client_id">
            <option value="">Sin cliente</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}" @selected((string) $value('client_id') === (string) $client->id)>{{ $client->name }}</option>
            @endforeach
        </select>
        @error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="responsible_user_id">Responsable</label>
        <select id="responsible_user_id" class="form-select @error('responsible_user_id') is-invalid @enderror" name="responsible_user_id">
            <option value="">Sin responsable</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" @selected((string) $value('responsible_user_id') === (string) $user->id)>{{ $user->name }}</option>
            @endforeach
        </select>
        @error('responsible_user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="location">Ubicacion</label>
        <input id="location" class="form-control @error('location') is-invalid @enderror" name="location" value="{{ $value('location') }}">
        @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="modality">Modalidad</label>
        <select id="modality" class="form-select @error('modality') is-invalid @enderror" name="modality" required>
            @foreach(['Precio alzado', 'Administracion'] as $modality)
                <option value="{{ $modality }}" @selected($value('modality', 'Precio alzado') === $modality)>{{ $modality }}</option>
            @endforeach
        </select>
        @error('modality')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label" for="status">Estado</label>
        <select id="status" class="form-select @error('status') is-invalid @enderror" name="status" required>
            @foreach(['Por iniciar', 'En Proceso', 'Terminada'] as $status)
                <option value="{{ $status }}" @selected($value('status', 'Por iniciar') === $status)>{{ $status }}</option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="start_date">Inicio</label>
        <input id="start_date" class="form-control @error('start_date') is-invalid @enderror" type="date" name="start_date" value="{{ $dateValue('start_date') }}">
        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="estimated_end_date">Terminacion estimada</label>
        <input id="estimated_end_date" class="form-control @error('estimated_end_date') is-invalid @enderror" type="date" name="estimated_end_date" value="{{ $dateValue('estimated_end_date') }}">
        @error('estimated_end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="physical_progress">Avance fisico %</label>
        <input id="physical_progress" class="form-control @error('physical_progress') is-invalid @enderror" type="number" step="0.01" min="0" max="100" name="physical_progress" value="{{ $value('physical_progress', 0) }}">
        @error('physical_progress')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="financial_progress">Avance financiero %</label>
        <input id="financial_progress" class="form-control @error('financial_progress') is-invalid @enderror" type="number" step="0.01" min="0" max="100" name="financial_progress" value="{{ $value('financial_progress', 0) }}">
        @error('financial_progress')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="contracted_value">Valor contratado</label>
        <input id="contracted_value" class="form-control @error('contracted_value') is-invalid @enderror" type="number" step="0.01" min="0" name="contracted_value" value="{{ $value('contracted_value', 0) }}" required>
        @error('contracted_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="estimated_amount">Monto estimado</label>
        <input id="estimated_amount" class="form-control @error('estimated_amount') is-invalid @enderror" type="number" step="0.01" min="0" name="estimated_amount" value="{{ $value('estimated_amount', 0) }}">
        @error('estimated_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="paid_amount">Monto pagado</label>
        <input id="paid_amount" class="form-control @error('paid_amount') is-invalid @enderror" type="number" step="0.01" min="0" name="paid_amount" value="{{ $value('paid_amount', 0) }}">
        @error('paid_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label" for="retention_amount">Retenciones</label>
        <input id="retention_amount" class="form-control @error('retention_amount') is-invalid @enderror" type="number" step="0.01" min="0" name="retention_amount" value="{{ $value('retention_amount', 0) }}">
        @error('retention_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="photo">Fotografia</label>
        <input id="photo" class="form-control @error('photo') is-invalid @enderror" type="file" name="photo" accept="image/png,image/jpeg,image/webp">
        @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="photo_path">URL o ruta de fotografia</label>
        <input id="photo_path" class="form-control @error('photo_path') is-invalid @enderror" name="photo_path" value="{{ $value('photo_path') }}">
        @error('photo_path')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label class="form-label" for="notes">Notas</label>
        <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3">{{ $value('notes') }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a class="btn btn-soft" href="{{ $project->exists ? route('obras.show', $project) : route('obras.index') }}">Cancelar</a>
    <button class="btn btn-aqua" type="submit">
        <i data-lucide="save"></i>
        Guardar obra
    </button>
</div>
