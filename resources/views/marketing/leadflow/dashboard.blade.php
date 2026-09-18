@extends('marketing.leadflow.layout', ['title' => 'Inicio | LeadFlow'])

@section('content')
<div class="panel pad" style="margin-bottom:16px">
    <h2 class="section-title"><span class="badge green">1</span> Empresa</h2>
    <p class="muted small">Selecciona la empresa que deseas gestionar.</p>
    <div class="card-row">
        @foreach($companies as $item)
            <a class="square-card {{ $company?->id === $item->id ? 'active' : '' }}" href="{{ route('marketing.index', ['company_id' => $item->id, 'campaign' => 'all']) }}">
                @if($company?->id === $item->id)<span class="check">✓</span>@endif
                <span class="logo-circle" style="background:{{ $item->brand_color }}">{{ $item->logo_initials }}</span>
                <strong>{{ $item->name }}</strong>
                <span class="status-dot {{ $item->active ? 'active' : 'inactive' }}">{{ $item->active ? 'Activa' : 'Inactiva' }}</span>
            </a>
        @endforeach
    </div>
</div>

<div class="panel pad" style="margin-bottom:16px">
    <h2 class="section-title"><span class="badge green">2</span> Campaña</h2>
    <p class="muted small">Selecciona una campaña o revisa todas las campañas.</p>
    <div class="card-row">
        <a class="square-card {{ $campaign ? '' : 'active' }}" href="{{ route('marketing.index', ['company_id' => $company?->id, 'campaign' => 'all']) }}">
            @unless($campaign)<span class="check">✓</span>@endunless
            <span class="metric-icon">▦</span>
            <strong>Todas las campañas</strong>
        </a>
        @foreach($campaigns as $item)
            <a class="square-card {{ $campaign?->id === $item->id ? 'active' : '' }}" href="{{ route('marketing.index', ['company_id' => $company?->id, 'campaign_id' => $item->id]) }}">
                @if($campaign?->id === $item->id)<span class="check">✓</span>@endif
                <span class="metric-icon">{{ $loop->iteration }}</span>
                <strong>{{ $item->name }}</strong>
                <span class="status-dot {{ $item->status }}">{{ \App\Models\Marketing\Campaign::STATUSES[$item->status] ?? $item->status }}</span>
            </a>
        @endforeach
    </div>
</div>

<div class="grid cols-4" style="margin-bottom:16px">
    <div class="panel metric"><span class="metric-icon">♙</span><div><span>Leads</span><strong>{{ number_format($metrics['leads']) }}</strong><span class="trend">↑ 12.5%</span></div></div>
    <div class="panel metric"><span class="metric-icon" style="background:var(--blue-100);color:var(--blue-600)">◌</span><div><span>Conversaciones activas</span><strong>{{ number_format($metrics['active_conversations']) }}</strong><span class="trend">↑ 8.3%</span></div></div>
    <div class="panel metric"><span class="metric-icon" style="background:var(--amber-100);color:var(--amber-600)">♧</span><div><span>Sin asignar</span><strong>{{ number_format($metrics['unassigned']) }}</strong><span class="trend down">↓ 4.2%</span></div></div>
    <div class="panel metric"><span class="metric-icon" style="background:var(--purple-100);color:var(--purple-600)">◷</span><div><span>Tiempo de respuesta</span><strong>2m 45s</strong><span class="trend">↓ 11%</span></div></div>
</div>

<div class="grid cols-4">
    <div class="panel">
        <div class="panel-head"><h2>Conversaciones recientes</h2><a class="btn" href="{{ route('marketing.conversations.index') }}">Ver todas</a></div>
        @foreach($recentConversations as $conversation)
            <a class="conversation-item" href="{{ route('marketing.conversations.index', ['conversation' => $conversation->id]) }}">
                <span class="avatar">{{ $conversation->lead->name[0] }}</span>
                <span>
                    <strong>{{ $conversation->lead->name }}</strong><br>
                    <span class="muted small">{{ $conversation->company->name }} · {{ $conversation->campaign?->name ?? 'Sin campaña' }}</span>
                </span>
                <span class="muted small">{{ $conversation->last_message_at?->format('H:i') }}</span>
            </a>
        @endforeach
    </div>

    <div class="panel pad">
        <h2>Embudo de leads</h2>
        @foreach([100, 72, 48, 30, 18] as $width)
            <div class="funnel-step" style="width:{{ $width }}%"></div>
        @endforeach
        <div class="detail-row"><span>Nuevos</span><strong>{{ number_format($metrics['leads']) }}</strong></div>
        <div class="detail-row"><span>Convertidos</span><strong>{{ number_format($metrics['converted']) }}</strong></div>
    </div>

    <div class="panel pad">
        <h2>Top campañas</h2>
        @foreach($topCampaigns as $item)
            <div class="detail-row">
                <span>{{ $loop->iteration }}. {{ $item->name }}</span>
                <strong>{{ number_format($item->leads_count) }}</strong>
            </div>
            <div style="height:6px;background:#eef2f7;border-radius:999px;margin-bottom:10px">
                <div style="width:{{ min(100, $item->leads_count / max(1, $metrics['leads']) * 100) }}%;height:6px;background:var(--green-600);border-radius:999px"></div>
            </div>
        @endforeach
        <a class="btn block" href="{{ route('marketing.campaigns.index') }}">Ver campañas</a>
    </div>

    <div class="panel pad">
        <h2>Actividad reciente</h2>
        @foreach($activity as $message)
            <div class="detail-section">
                <strong>{{ $message->message_type === 'note' ? 'Nota agregada' : 'Mensaje registrado' }}</strong>
                <p class="muted small" style="margin:4px 0 0">{{ $message->conversation->lead->name }} · {{ $message->sent_at?->diffForHumans() }}</p>
            </div>
        @endforeach
    </div>
</div>
@endsection
