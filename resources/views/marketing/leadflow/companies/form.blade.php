@extends('marketing.leadflow.layout', ['title' => ($company->exists ? 'Editar empresa' : 'Nueva empresa').' | LeadFlow'])

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $company->exists ? 'Editar empresa' : 'Nueva empresa' }}</h1>
        <p>Configura los datos principales de la empresa cliente.</p>
    </div>
</div>

<form class="panel pad" method="post" action="{{ $company->exists ? route('marketing.companies.update', $company) : route('marketing.companies.store') }}">
    @csrf
    @if($company->exists) @method('put') @endif
    <div class="form-grid">
        <div class="field">
            <label>Nombre</label>
            <input class="input" name="name" value="{{ old('name', $company->name) }}" required>
        </div>
        <div class="field">
            <label>Dominio</label>
            <input class="input" name="domain" value="{{ old('domain', $company->domain) }}" placeholder="empresa.com">
        </div>
        <div class="field">
            <label>Industria</label>
            <input class="input" name="industry" value="{{ old('industry', $company->industry) }}" placeholder="Tecnología">
        </div>
        <div class="field">
            <label>Iniciales del logo</label>
            <input class="input" name="logo_initials" value="{{ old('logo_initials', $company->logo_initials) }}" maxlength="8">
        </div>
        <div class="field">
            <label>Color de marca</label>
            <input class="input" type="color" name="brand_color" value="{{ old('brand_color', $company->brand_color ?: '#059669') }}">
        </div>
        <label class="field" style="display:flex;align-items:center;gap:10px;margin-top:28px">
            <input type="checkbox" name="active" value="1" {{ old('active', $company->active ?? true) ? 'checked' : '' }}>
            Empresa activa
        </label>
    </div>
    <div class="toolbar" style="justify-content:flex-end">
        <a class="btn" href="{{ route('marketing.companies.index') }}">Cancelar</a>
        <button class="btn primary" type="submit">Guardar empresa</button>
    </div>
</form>
@endsection
