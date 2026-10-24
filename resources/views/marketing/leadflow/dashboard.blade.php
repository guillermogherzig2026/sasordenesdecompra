@extends('marketing.leadflow.layout', ['title' => 'Panel general | Marketing'])

@push('styles')
    <link rel="stylesheet" href="{{ asset('modules/marketing/dashboard.css') }}?v={{ filemtime(public_path('modules/marketing/dashboard.css')) }}">
@endpush
@push('scripts')
    <script src="{{ asset('modules/marketing/dashboard.js') }}?v={{ filemtime(public_path('modules/marketing/dashboard.js')) }}" defer></script>
@endpush

@section('content')
@php
    $can = fn (string $permission) => auth()->user()->canNavigateTo('marketing.'.$permission);
    $companyParameters = ['company_id' => $company?->id ?? 'all', 'campaign' => 'all'];
@endphp
<div class="marketing-dashboard" data-marketing-dashboard>
    @include('marketing.leadflow.partials.company-selection')

    <section class="marketing-workspace-panel" aria-label="Espacio de marketing">

    <header class="marketing-dashboard-header">
        <nav class="marketing-dashboard-tabs" aria-label="Secciones del panel de marketing">
            <a class="{{ $dashboardView === 'summary' ? 'is-active' : '' }}" href="{{ route('marketing.index', array_merge($scopeParameters, ['view' => 'summary'])) }}" @if($dashboardView === 'summary') aria-current="page" @endif><x-marketing-icon name="summary" />Resumen</a>
            @if($can('reports'))
                <a href="{{ route('marketing.reports.index', $scopeParameters) }}"><x-marketing-icon name="report" />Reportes</a>
            @endif
            <a class="{{ $dashboardView === 'channels' ? 'is-active' : '' }}" href="{{ route('marketing.index', array_merge($scopeParameters, ['view' => 'channels'])) }}" @if($dashboardView === 'channels') aria-current="page" @endif><x-marketing-icon name="settings" />Configuraciones</a>
        </nav>
        <div class="marketing-dashboard-actions">
            @if($can('campaigns'))
                <a class="btn" href="{{ route('marketing.campaigns.index', $scopeParameters) }}"><x-marketing-icon name="campaign" />Ver campañas</a>
            @endif
            @if($company)
                <a class="btn primary" href="{{ route('marketing.index', array_merge($scopeParameters, ['view' => 'channels'])) }}#marketing-channels"><x-marketing-icon name="settings" />Configurar canales</a>
            @endif
        </div>
    </header>

    @include('marketing.leadflow.partials.dashboard-channels')

    <section class="marketing-overview" aria-label="Resumen de marketing" @if($dashboardView !== 'summary') hidden @endif>
        @include('marketing.leadflow.partials.campaign-selection', ['selectionRoute' => 'marketing.index', 'selectionParameters' => ['view' => 'summary']])
        <div class="marketing-overview-metrics">
            @foreach([
                ['Leads', number_format($metrics['leads']), 'Contactos registrados', 'users'],
                ['Conversaciones activas', number_format($metrics['active_conversations']), 'Conversaciones abiertas', 'conversation'],
                ['Sin asignar', number_format($metrics['unassigned']), 'Pendientes de responsable', 'clock'],
                ['Convertidos', number_format($metrics['converted']), 'Leads con conversión registrada', 'summary'],
            ] as [$label, $value, $caption, $icon])
                <div class="panel marketing-overview-metric"><x-marketing-icon :name="$icon" /><span>{{ $label }}</span><strong>{{ $value }}</strong><small>{{ $caption }}</small></div>
            @endforeach
        </div>
        <div class="marketing-overview-grid">
            <div class="panel pad">
                <div class="panel-head"><h2>Conversaciones recientes</h2>@if($can('conversations'))<a class="btn" href="{{ route('marketing.conversations.index', $scopeParameters) }}">Ver todas</a>@endif</div>
                @forelse($recentConversations as $conversation)
                    <div class="conversation-item">
                        <span class="avatar">{{ mb_substr($conversation->lead->name, 0, 1) }}</span>
                        <span>@if($can('conversations'))<a href="{{ route('marketing.conversations.index', array_merge($scopeParameters, ['conversation' => $conversation->id])) }}"><strong>{{ $conversation->lead->name }}</strong></a>@else<strong>{{ $conversation->lead->name }}</strong>@endif<br><span class="muted small">{{ $conversation->company->name }} · {{ $conversation->campaign?->name ?? 'Sin campaña' }}</span></span>
                        <span class="muted small">{{ $conversation->last_message_at?->format('d/m H:i') }}</span>
                    </div>
                @empty
                    <p class="marketing-dashboard-empty">Todavía no hay conversaciones para esta selección.</p>
                @endforelse
            </div>
            <div class="panel pad">
                <h2>Estado de los leads</h2>
                @foreach(\App\Models\Marketing\Lead::STATUSES as $status => $label)
                    <div class="detail-row"><span>{{ $label }}</span><strong>{{ number_format($leadStatusCounts[$status] ?? 0) }}</strong></div>
                    <progress class="marketing-lead-progress" max="{{ max(1, $metrics['leads']) }}" value="{{ $leadStatusCounts[$status] ?? 0 }}" aria-label="{{ $label }}"></progress>
                @endforeach
            </div>
            <div class="panel pad">
                <h2>Campañas con más leads</h2>
                @forelse($topCampaigns as $item)
                    <div class="detail-row"><span>{{ $item->name }}</span><strong>{{ number_format($item->leads_count) }} leads</strong></div>
                @empty
                    <p class="marketing-dashboard-empty">No hay campañas en esta selección.</p>
                @endforelse
            </div>
            <div class="panel pad">
                <h2>Actividad reciente</h2>
                @forelse($activity as $message)
                    <div class="detail-section"><strong>{{ $message->message_type === 'note' ? 'Nota agregada' : 'Mensaje registrado' }}</strong><p class="muted small">{{ $message->conversation->lead->name }} · {{ $message->sent_at?->diffForHumans() }}</p></div>
                @empty
                    <p class="marketing-dashboard-empty">La actividad de tu equipo aparecerá aquí.</p>
                @endforelse
            </div>
        </div>
    </section>

    </section>

    @include('marketing.leadflow.partials.channel-dialogs')
</div>
@endsection
