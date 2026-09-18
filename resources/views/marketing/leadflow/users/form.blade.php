@extends('marketing.leadflow.layout', ['title' => ($userModel->exists ? 'Editar usuario' : 'Nuevo usuario').' | LeadFlow'])

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $userModel->exists ? 'Editar usuario' : 'Nuevo usuario' }}</h1>
        <p>Define el rol y las campañas permitidas para este usuario.</p>
    </div>
</div>

<form class="panel pad" method="post" action="{{ $userModel->exists ? route('marketing.users.update', $userModel) : route('marketing.users.store') }}">
    @csrf
    @if($userModel->exists) @method('put') @endif
    <div class="form-grid">
        <div class="field">
            <label>Empresa</label>
            <select class="select" name="company_id">
                <option value="">Todas las empresas</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" @selected(old('company_id', $userModel->company_id ?: session('marketing_company_id')) == $company->id)>{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label>Rol</label>
            <select class="select" name="role" required>
                @foreach(\App\Models\Marketing\User::ROLES as $value => $label)
                    <option value="{{ $value }}" @selected(old('role', $userModel->role ?: 'asesor') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label>Nombre</label>
            <input class="input" name="name" value="{{ old('name', $userModel->name) }}" required>
        </div>
        <div class="field">
            <label>Correo</label>
            <input class="input" type="email" name="email" value="{{ old('email', $userModel->email) }}" required>
        </div>
        <div class="field">
            <label>Contraseña</label>
            <input class="input" type="password" name="password" placeholder="{{ $userModel->exists ? 'Dejar vacía para no cambiar' : 'Mínimo 8 caracteres' }}">
        </div>
        <div class="field">
            <label>Teléfono</label>
            <input class="input" name="phone" value="{{ old('phone', $userModel->phone) }}">
        </div>
        <div class="field">
            <label>Zona horaria</label>
            <input class="input" name="timezone" value="{{ old('timezone', $userModel->timezone ?: 'America/Mexico_City') }}">
        </div>
        <label class="field" style="display:flex;align-items:center;gap:10px;margin-top:28px">
            <input type="checkbox" name="active" value="1" {{ old('active', $userModel->active ?? true) ? 'checked' : '' }}>
            Usuario activo
        </label>
        <div class="field full">
            <label>Campañas autorizadas</label>
            <div class="filters">
                @foreach($campaigns as $campaign)
                    <label class="badge" style="cursor:pointer">
                        <input type="checkbox" name="campaign_ids[]" value="{{ $campaign->id }}" @checked(in_array($campaign->id, old('campaign_ids', $userModel->campaigns?->pluck('id')->all() ?? [])))>
                        {{ $campaign->name }}
                    </label>
                @endforeach
            </div>
            <p class="muted small">Deja vacío para que el rol use acceso general de su empresa.</p>
        </div>
    </div>
    <div class="toolbar" style="justify-content:flex-end">
        <a class="btn" href="{{ route('marketing.users.index') }}">Cancelar</a>
        <button class="btn primary" type="submit">Guardar usuario</button>
    </div>
</form>
@endsection
