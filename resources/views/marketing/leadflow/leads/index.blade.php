@extends('marketing.leadflow.layout', ['title' => 'Leads | LeadFlow'])

@section('content')
@include('marketing.leadflow.partials.company-selection')
@include('marketing.leadflow.partials.campaign-selection', ['selectionRoute' => 'marketing.leads.index'])

<div class="page-head">
    <div>
        <h1>Leads</h1>
        <p>Gestiona y da seguimiento a los leads de la empresa y campaña seleccionadas.</p>
    </div>
    <a class="btn primary" href="{{ route('marketing.leads.create') }}">+ Nuevo lead</a>
</div>

<div class="grid cols-4" style="margin-bottom:16px">
    <div class="panel metric"><span class="metric-icon">♙</span><div><span>Nuevos leads</span><strong>{{ number_format($stats['new']) }}</strong><span class="trend">↑ 18.6%</span></div></div>
    <div class="panel metric"><span class="metric-icon" style="background:var(--purple-100);color:var(--purple-600)">☆</span><div><span>Calificados</span><strong>{{ number_format($stats['qualified']) }}</strong><span class="trend">↑ 12.4%</span></div></div>
    <div class="panel metric"><span class="metric-icon" style="background:var(--amber-100);color:var(--amber-600)">◷</span><div><span>En seguimiento</span><strong>{{ number_format($stats['follow_up']) }}</strong><span class="trend down">↓ 4.8%</span></div></div>
    <div class="panel metric"><span class="metric-icon">↗</span><div><span>Conversión</span><strong>{{ number_format($stats['conversion']) }}</strong><span class="trend">↑ 1.3%</span></div></div>
</div>

<div class="split">
    <div>
        <form class="toolbar panel pad" method="get">
            <div class="filters">
                <input class="input" style="width:260px" name="search" value="{{ request('search') }}" placeholder="Buscar leads...">
                <select class="select" style="width:160px" name="status">
                    <option value="">Estado</option>
                    @foreach(\App\Models\Marketing\Lead::STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <select class="select" style="width:170px" name="advisor_id">
                    <option value="">Asesor</option>
                    @foreach($advisors as $advisor)
                        <option value="{{ $advisor->id }}" @selected((int) request('advisor_id') === $advisor->id)>{{ $advisor->name }}</option>
                    @endforeach
                </select>
                <select class="select" style="width:170px" name="product">
                    <option value="">Producto</option>
                    <option value="WhatsApp" @selected(request('product') === 'WhatsApp')>WhatsApp</option>
                    <option value="Promos" @selected(request('product') === 'Promos')>Promos</option>
                </select>
            </div>
            <button class="btn" type="submit">Filtros</button>
        </form>

        <div class="panel table-wrap">
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox"></th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Empresa</th>
                        <th>Campaña</th>
                        <th>Producto</th>
                        <th>Estado</th>
                        <th>Asesor</th>
                        <th>Valor estimado</th>
                        <th>Último contacto</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $lead)
                        @php
                            $badge = match($lead->status) {
                                'nuevo' => 'blue',
                                'seguimiento' => 'amber',
                                'descartado' => '',
                                'convertido', 'calificado', 'interesado' => 'green',
                                default => '',
                            };
                        @endphp
                        <tr class="{{ $selectedLead?->id === $lead->id ? 'selected' : '' }}">
                            <td><input type="checkbox"></td>
                            <td><strong>{{ $lead->name }}</strong></td>
                            <td>{{ $lead->phone }}</td>
                            <td>{{ $lead->company->name }}</td>
                            <td>{{ $lead->campaign?->name ?? '-' }}</td>
                            <td>{{ $lead->product }}</td>
                            <td><span class="badge {{ $badge }}">{{ \App\Models\Marketing\Lead::STATUSES[$lead->status] }}</span></td>
                            <td>{{ $lead->advisor?->name ?? 'Sin asignar' }}</td>
                            <td>${{ number_format($lead->estimated_value, 2) }}</td>
                            <td>{{ $lead->last_contact_at?->format('d M, H:i') ?? '-' }}</td>
                            <td>
                                <a class="btn" href="{{ route('marketing.leads.edit', $lead) }}">Editar</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $leads->links() }}</div>
        </div>
    </div>

    <aside class="panel pad detail-panel">
        @if($selectedLead)
            <div class="page-head" style="margin-bottom:8px">
                <h2>{{ $selectedLead->name }}</h2>
                <span class="muted">×</span>
            </div>
            <div class="filters" style="margin-bottom:16px">
                <a class="btn primary" href="{{ route('marketing.conversations.index', ['conversation' => $selectedLead->conversation?->id]) }}">WhatsApp</a>
                <a class="btn" href="tel:{{ $selectedLead->phone }}">Llamar</a>
                <a class="btn" href="mailto:{{ $selectedLead->email }}">Correo</a>
            </div>
            <div class="detail-section">
                <h3>Información de contacto</h3>
                <div class="detail-row"><span>Teléfono</span><strong>{{ $selectedLead->phone }}</strong></div>
                <div class="detail-row"><span>Correo</span><strong>{{ $selectedLead->email ?: '-' }}</strong></div>
                <div class="detail-row"><span>Empresa</span><strong>{{ $selectedLead->company->name }}</strong></div>
                <div class="detail-row"><span>Cargo</span><strong>{{ $selectedLead->position ?: '-' }}</strong></div>
            </div>
            <div class="detail-section">
                <h3>Lead</h3>
                <div class="detail-row"><span>Campaña</span><span class="badge green">{{ $selectedLead->campaign?->name ?? 'Sin campaña' }}</span></div>
                <div class="detail-row"><span>Producto</span><span class="badge green">{{ $selectedLead->product }}</span></div>
                <div class="detail-row"><span>Estado</span><span class="badge green">{{ \App\Models\Marketing\Lead::STATUSES[$selectedLead->status] }}</span></div>
                <div class="detail-row"><span>Asesor asignado</span><strong>{{ $selectedLead->advisor?->name ?? 'Sin asignar' }}</strong></div>
            </div>
            <div class="detail-section">
                <h3>Etiquetas</h3>
                @foreach($selectedLead->tags ?? [] as $tag)
                    <span class="badge green">{{ $tag }}</span>
                @endforeach
            </div>
            <div class="detail-section">
                <h3>Valor estimado</h3>
                <strong>${{ number_format($selectedLead->estimated_value, 2) }} MXN</strong>
            </div>
            <div class="detail-section">
                <h3>Actividad reciente</h3>
                @if($selectedLead->conversation)
                    @foreach($selectedLead->conversation->messages()->latest('sent_at')->limit(4)->get() as $message)
                        <p class="small"><strong>{{ $message->message_type === 'note' ? 'Nota' : 'Mensaje' }}</strong><br><span class="muted">{{ $message->body }}</span></p>
                    @endforeach
                @endif
            </div>
        @else
            <p class="muted">No hay leads para mostrar.</p>
        @endif
    </aside>
</div>
@endsection
