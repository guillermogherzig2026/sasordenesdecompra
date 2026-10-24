@extends('marketing.leadflow.layout', ['title' => ($campaign->exists ? 'Editar campaña' : 'Nueva campaña').' | LeadFlow'])

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $campaign->exists ? 'Editar campaña' : 'Nueva campaña' }}</h1>
        <p>Registra nombre, estado, fechas, presupuesto, producto y responsable.</p>
    </div>
</div>

<form class="panel pad" method="post" action="{{ $campaign->exists ? route('marketing.campaigns.update', $campaign) : route('marketing.campaigns.store') }}">
    @csrf
    @if($campaign->exists) @method('put') @endif
    <div class="form-grid">
        <div class="field">
            <label>Empresa</label>
            <select class="select" name="company_id" required>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" @selected(old('company_id', $campaign->company_id) == $company->id)>{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label>Responsable</label>
            <select class="select" name="responsible_user_id">
                <option value="">Sin responsable</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(old('responsible_user_id', $campaign->responsible_user_id) == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label>Nombre</label>
            <input class="input" name="name" value="{{ old('name', $campaign->name) }}" required>
        </div>
        <div class="field">
            <label>Estado</label>
            <select class="select" name="status">
                @foreach(\App\Models\Marketing\Campaign::STATUSES as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $campaign->status ?: 'active') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label>Fecha de inicio</label>
            <input class="input" type="date" name="starts_at" value="{{ old('starts_at', $campaign->starts_at?->format('Y-m-d')) }}">
        </div>
        <div class="field">
            <label>Fecha de fin</label>
            <input class="input" type="date" name="ends_at" value="{{ old('ends_at', $campaign->ends_at?->format('Y-m-d')) }}">
        </div>
        <div class="field">
            <label>Presupuesto</label>
            <input class="input" type="number" step="0.01" name="budget" value="{{ old('budget', $campaign->budget ?? 0) }}">
        </div>
        <div class="field">
            <label>Inversión usada</label>
            <input class="input" type="number" step="0.01" name="spent" value="{{ old('spent', $campaign->spent ?? 0) }}">
        </div>
        <div class="field">
            <label>Producto</label>
            <input class="input" name="product" value="{{ old('product', $campaign->product) }}">
        </div>
        <div class="field full">
            <label>Redes sociales</label>
            @php
                $selectedNetworks = old('social_networks', $campaign->social_networks ?: ($campaign->source ? array_map('trim', explode(',', $campaign->source)) : []));
                $networkOptions = ['Facebook', 'Instagram', 'TikTok', 'LinkedIn', 'YouTube', 'Google Ads', 'Meta Ads', 'WhatsApp'];
            @endphp
            <div class="filters">
                @foreach($networkOptions as $network)
                    <label class="badge" style="cursor:pointer">
                        <input type="checkbox" name="social_networks[]" value="{{ $network }}" @checked(in_array($network, $selectedNetworks, true))>
                        {{ $network }}
                    </label>
                @endforeach
            </div>
            <p class="muted small">Puedes seleccionar una o varias redes sociales para la campaña.</p>
            <input type="hidden" name="source" value="{{ old('source', $campaign->source ?: 'Redes sociales') }}">
        </div>
        <div class="field full">
            <label>Descripción</label>
            <textarea name="description" rows="4">{{ old('description', $campaign->description) }}</textarea>
        </div>
    </div>
    <div class="toolbar" style="justify-content:flex-end">
        <a class="btn" href="{{ route('marketing.campaigns.index') }}">Cancelar</a>
        <button class="btn primary" type="submit">Guardar campaña</button>
    </div>
</form>
@endsection
