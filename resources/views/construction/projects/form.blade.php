@extends('layouts.app')

@section('body')
    <x-app-shell :title="$project->exists ? 'Editar obra' : 'Nueva obra'">
        <form class="panel" method="POST" action="{{ $project->exists ? route('construction.projects.update', $project) : route('construction.projects.store') }}">
            @csrf
            @if ($project->exists)
                @method('PUT')
            @endif

            <div class="panel-header">
                <div>
                    <h2>{{ $project->exists ? "Editar {$project->project_key}" : 'Nueva obra' }}</h2>
                    <p class="fine-print">Captura los datos principales del expediente de obra.</p>
                </div>
                <a class="button ghost" href="{{ route('construction.dashboard') }}">Volver</a>
            </div>

            <div class="grid-3">
                <label>Clave de obra
                    <input name="project_key" value="{{ $project->project_key }}" required readonly aria-readonly="true">
                </label>
                <label>Nombre
                    <input name="name" value="{{ old('name', $project->name) }}" required>
                </label>
                <label>Ubicacion
                    <input name="location" value="{{ old('location', $project->location) }}">
                </label>
            </div>

            <div class="grid-3">
                <label>Empresa
                    <select name="company_id">
                        <option value="">Sin empresa</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" @selected((int) old('company_id', $project->company_id) === $company->id)>{{ $company->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Cliente
                    <select name="client_id">
                        <option value="">Sin cliente</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}" @selected((int) old('client_id', $project->client_id) === $client->id)>{{ $client->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Responsable
                    <select name="responsible_user_id">
                        <option value="">Sin responsable</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected((int) old('responsible_user_id', $project->responsible_user_id) === $user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="grid-4">
                <label>Tipo de obra
                    <input name="project_type" value="{{ old('project_type', $project->project_type) }}">
                </label>
                <label>Modalidad
                    <select name="modality" required>
                        @foreach (['Precio alzado', 'Administracion', 'Hibrida'] as $modality)
                            <option value="{{ $modality }}" @selected(old('modality', $project->modality) === $modality)>{{ $modality }}</option>
                        @endforeach
                    </select>
                </label>
                @php
                    $selectedProjectStatus = old('status', $project->status === 'Terminada' ? 'Concluida' : $project->status);
                @endphp
                <label>Estado
                    <select name="status" required>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected($selectedProjectStatus === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Foto/ruta
                    <input name="photo_path" value="{{ old('photo_path', $project->photo_path) }}" placeholder="/images/construction-projects/...">
                </label>
            </div>

            <div class="{{ $project->exists ? 'grid-4' : 'grid-2' }}">
                <label>Inicio
                    <input type="date" name="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}">
                </label>
                <label>Fin estimado
                    <input type="date" name="estimated_end_date" value="{{ old('estimated_end_date', $project->estimated_end_date?->format('Y-m-d')) }}">
                </label>
                @if ($project->exists)
                    <label>Avance fisico %
                        <input type="number" step="0.01" min="0" max="100" name="physical_progress" value="{{ old('physical_progress', $project->physical_progress ?? 0) }}">
                    </label>
                    <label>Avance financiero %
                        <input type="number" step="0.01" min="0" max="100" name="financial_progress" value="{{ old('financial_progress', $project->financial_progress ?? 0) }}">
                    </label>
                @endif
            </div>

            <div class="grid-4">
                <label>Valor contratado
                    <input type="number" step="0.01" min="0" name="contracted_value" value="{{ old('contracted_value', $project->contracted_value ?? 0) }}">
                </label>
                @if ($project->exists)
                    <label>Estimado acumulado
                        <input type="number" step="0.01" min="0" name="estimated_amount" value="{{ old('estimated_amount', $project->estimated_amount ?? 0) }}">
                    </label>
                    <label>Pagado acumulado
                        <input type="number" step="0.01" min="0" name="paid_amount" value="{{ old('paid_amount', $project->paid_amount ?? 0) }}">
                    </label>
                    <label>Retenciones
                        <input type="number" step="0.01" min="0" name="retention_amount" value="{{ old('retention_amount', $project->retention_amount ?? 0) }}">
                    </label>
                @endif
            </div>

            <div class="grid-4">
                <label>Metros cuadrados construidos
                    <input type="number" step="0.01" min="0" name="constructed_area" value="{{ old('constructed_area', $project->constructed_area ?? 0) }}">
                </label>
                <label>Metros cuadrados vendibles o rentables
                    <input type="number" step="0.01" min="0" name="sellable_rentable_area" value="{{ old('sellable_rentable_area', $project->sellable_rentable_area ?? 0) }}">
                </label>
                <label>Metros cuadrados de estacionamientos
                    <input type="number" step="0.01" min="0" name="parking_area" value="{{ old('parking_area', $project->parking_area ?? 0) }}">
                </label>
                <label>Numero de niveles
                    <input type="number" step="1" min="0" max="999" name="levels_count" value="{{ old('levels_count', $project->levels_count ?? 0) }}">
                </label>
            </div>

            <label>Notas
                <textarea name="notes" rows="3">{{ old('notes', $project->notes) }}</textarea>
            </label>

            <div class="form-actions">
                <button class="button primary" type="submit">Guardar obra</button>
            </div>
        </form>
    </x-app-shell>
@endsection
