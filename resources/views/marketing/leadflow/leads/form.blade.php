@extends('marketing.leadflow.layout', ['title' => ($lead->exists ? 'Editar lead' : 'Nuevo lead').' | LeadFlow'])

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $lead->exists ? 'Editar lead' : 'Nuevo lead' }}</h1>
        <p>Registra los datos comerciales y de seguimiento del lead.</p>
    </div>
</div>

<form class="panel pad" method="post" action="{{ $lead->exists ? route('marketing.leads.update', $lead) : route('marketing.leads.store') }}" data-marketing-lead-form>
    @csrf
    @if($lead->exists) @method('put') @endif
    <div class="form-grid">
        <div class="field">
            <label>Empresa</label>
            <select class="select" name="company_id" required>
                @if (!$lead->exists && session('marketing_company_scope') === 'all')
                    <option value="" disabled @selected(!old('company_id'))>Selecciona una empresa</option>
                @endif
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" @selected(old('company_id', $lead->company_id ?: (session('marketing_company_scope') === 'all' ? null : session('marketing_company_id'))) == $company->id)>{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label>Campaña</label>
            <select class="select" name="campaign_id">
                <option value="">Sin campaña</option>
                @foreach($campaigns as $campaign)
                    <option value="{{ $campaign->id }}" data-company-id="{{ $campaign->company_id }}" @selected(old('campaign_id', $lead->campaign_id ?: (session('marketing_company_scope') === 'all' ? null : session('marketing_campaign_id'))) == $campaign->id)>{{ $campaign->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label>Nombre</label>
            <input class="input" name="name" value="{{ old('name', $lead->name) }}" required>
        </div>
        <div class="field">
            <label>Teléfono</label>
            <input class="input" name="phone" value="{{ old('phone', $lead->phone) }}" required>
        </div>
        <div class="field">
            <label>Correo</label>
            <input class="input" type="email" name="email" value="{{ old('email', $lead->email) }}">
        </div>
        <div class="field">
            <label>Cargo</label>
            <input class="input" name="position" value="{{ old('position', $lead->position) }}">
        </div>
        <div class="field">
            <label>Producto</label>
            <input class="input" name="product" value="{{ old('product', $lead->product ?: 'WhatsApp') }}">
        </div>
        <div class="field">
            <label>Estado</label>
            <select class="select" name="status">
                @foreach(\App\Models\Marketing\Lead::STATUSES as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $lead->status ?: 'nuevo') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label>Asesor</label>
            <select class="select" name="advisor_id">
                <option value="">Sin asignar</option>
                @foreach($advisors as $advisor)
                    <option value="{{ $advisor->id }}" data-company-id="{{ $advisor->company_id }}" @selected(old('advisor_id', $lead->advisor_id) == $advisor->id)>{{ $advisor->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label>Valor estimado</label>
            <input class="input" type="number" step="0.01" name="estimated_value" value="{{ old('estimated_value', $lead->estimated_value ?? 0) }}">
        </div>
        <div class="field">
            <label>Último contacto</label>
            <input class="input" type="datetime-local" name="last_contact_at" value="{{ old('last_contact_at', $lead->last_contact_at?->format('Y-m-d\TH:i')) }}">
        </div>
        <div class="field">
            <label>Próximo seguimiento</label>
            <input class="input" type="datetime-local" name="next_follow_up_at" value="{{ old('next_follow_up_at', $lead->next_follow_up_at?->format('Y-m-d\TH:i')) }}">
        </div>
        <div class="field full">
            <label>Notas</label>
            <textarea name="notes" rows="4">{{ old('notes', $lead->notes) }}</textarea>
        </div>
    </div>
    <div class="toolbar" style="justify-content:flex-end">
        <a class="btn" href="{{ route('marketing.leads.index') }}">Cancelar</a>
        <button class="btn primary" type="submit">Guardar lead</button>
    </div>
</form>
@endsection

@push('scripts')
    <script src="{{ asset('modules/marketing/lead-company-fields.js') }}" defer></script>
@endpush
