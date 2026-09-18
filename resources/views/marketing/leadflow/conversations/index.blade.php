@extends('marketing.leadflow.layout', ['title' => 'Conversaciones | LeadFlow'])

@section('content')
@include('marketing.leadflow.partials.workspace-selection', [
    'selectionRoute' => 'marketing.conversations.index',
])

<div class="toolbar" style="justify-content:flex-end">
    <a class="btn" href="{{ route('marketing.campaigns.index') }}">Ver detalles de campaña</a>
</div>

<div class="grid cols-4" style="margin-bottom:16px">
    <div class="panel metric"><span class="metric-icon">◌</span><div><span>Conversaciones activas</span><strong>{{ number_format($stats['active']) }}</strong><span class="trend">↑ 8.3%</span></div></div>
    <div class="panel metric"><span class="metric-icon" style="background:var(--amber-100);color:var(--amber-600)">♧</span><div><span>Sin asignar</span><strong>{{ number_format($stats['unassigned']) }}</strong><span class="trend down">↓ 4.2%</span></div></div>
    <div class="panel metric"><span class="metric-icon" style="background:var(--purple-100);color:var(--purple-600)">◷</span><div><span>Esperando respuesta</span><strong>{{ number_format($stats['waiting']) }}</strong><span class="trend">↑ 12.5%</span></div></div>
    <div class="panel metric"><span class="metric-icon" style="background:var(--blue-100);color:var(--blue-600)">↺</span><div><span>Tiempo de respuesta</span><strong>{{ $stats['response_time'] }}</strong><span class="trend">↓ 11%</span></div></div>
</div>

<div class="conversation-shell">
    <aside class="panel conversation-list">
        <div class="panel-head">
            <form class="search" method="get" action="{{ route('marketing.conversations.index') }}">
                <input name="search" value="{{ request('search') }}" placeholder="Buscar conversaciones...">
            </form>
        </div>
        <div class="filters" style="padding:0 14px 12px">
            <a class="badge {{ !request('filter') ? 'green' : '' }}" href="{{ route('marketing.conversations.index') }}">Todas {{ $conversations->count() }}</a>
            <a class="badge {{ request('filter') === 'unread' ? 'green' : '' }}" href="{{ route('marketing.conversations.index', ['filter' => 'unread']) }}">No leídas</a>
            <a class="badge {{ request('filter') === 'unassigned' ? 'green' : '' }}" href="{{ route('marketing.conversations.index', ['filter' => 'unassigned']) }}">Sin asignar</a>
        </div>
        @foreach($conversations as $conversation)
            @php($last = $conversation->latestMessage)
            <a class="conversation-item {{ $selected?->id === $conversation->id ? 'active' : '' }}" href="{{ route('marketing.conversations.index', ['conversation' => $conversation->id]) }}">
                <span class="avatar">{{ substr($conversation->lead->name, 0, 1) }}</span>
                <span>
                    <strong>{{ $conversation->lead->name }}</strong><br>
                    <span class="muted small">{{ $last?->body ? \Illuminate\Support\Str::limit($last->body, 45) : 'Sin mensajes' }}</span><br>
                    <span class="badge green">{{ $conversation->channel }}</span>
                    <span class="badge">{{ \App\Models\Marketing\Lead::STATUSES[$conversation->lead->status] ?? $conversation->lead->status }}</span>
                </span>
                <span>
                    <span class="muted small">{{ $conversation->last_message_at?->format('H:i') ?: '-' }}</span>
                    @if($conversation->unread_count)
                        <br><span class="badge green">{{ $conversation->unread_count }}</span>
                    @endif
                </span>
            </a>
        @endforeach
    </aside>

    <section class="panel chat">
        @if($selected)
            <div class="chat-head">
                <div style="display:flex;gap:12px;align-items:center">
                    <span class="avatar">{{ substr($selected->lead->name, 0, 1) }}</span>
                    <div>
                        <h2 style="margin:0">{{ $selected->lead->name }}</h2>
                        <span class="muted">{{ $selected->channel }} · {{ $selected->lead->phone }}</span>
                    </div>
                </div>
                <div class="filters">
                    <span class="btn">Etiqueta</span>
                    <span class="btn">☆</span>
                    <span class="btn">⋮</span>
                </div>
            </div>
            <div class="chat-body">
                <div class="badge" style="display:flex;width:max-content;margin:0 auto 18px">{{ now()->format('d M Y') }}</div>
                @foreach($selected->messages as $message)
                    @php($class = $message->direction === 'out' ? 'out' : ($message->direction === 'internal' ? 'internal' : ''))
                    <div class="message {{ $class }}">
                        @if($message->message_type === 'file')
                            <strong>{{ $message->file_name }}</strong><br>
                            <span class="muted small">Archivo adjunto</span><br>
                        @endif
                        @if($message->message_type === 'note')
                            <strong>Nota interna</strong><br>
                        @endif
                        {{ $message->body }}
                        <div class="muted small" style="text-align:right;margin-top:6px">{{ $message->sent_at?->format('H:i') }}</div>
                    </div>
                @endforeach
            </div>
            <form class="chat-form" method="post" action="{{ route('marketing.conversations.messages.store', $selected) }}" enctype="multipart/form-data">
                @csrf
                <input class="input" name="body" placeholder="Escribe un mensaje...">
                <button class="btn primary" type="submit">Enviar</button>
                <input class="input" style="grid-column:1 / -1" type="file" name="file">
            </form>
        @else
            <div class="panel pad"><p class="muted">Selecciona una conversación para comenzar.</p></div>
        @endif
    </section>

    <aside class="panel pad detail-panel">
        @if($selected)
            <div class="panel-head" style="padding:0 0 14px">
                <h2>Detalles del lead</h2>
                <span>⌃</span>
            </div>
            <div class="detail-row"><span>Empresa</span><strong>{{ $selected->company->name }}</strong></div>
            <div class="detail-row"><span>Campaña</span><span class="badge green">{{ $selected->campaign?->name ?? 'Sin campaña' }}</span></div>
            <div class="detail-row"><span>Lead</span><strong>{{ $selected->lead->name }}</strong></div>
            <div class="detail-row"><span>Estado del lead</span><span class="badge green">{{ \App\Models\Marketing\Lead::STATUSES[$selected->lead->status] }}</span></div>
            <div class="detail-row"><span>Asesor</span><strong>{{ $selected->assignedUser?->name ?? 'Sin asignar' }}</strong></div>
            <div class="detail-row"><span>Valor potencial</span><strong>${{ number_format($selected->lead->estimated_value, 2) }} MXN</strong></div>
            <div class="detail-row"><span>Fuente</span><strong>{{ $selected->lead->source }}</strong></div>
            <div class="detail-row"><span>Creado el</span><strong>{{ $selected->created_at->format('d M Y, H:i') }}</strong></div>
            <div class="detail-section">
                <h3>Asignación</h3>
                <form method="post" action="{{ route('marketing.conversations.assign', $selected) }}">
                    @csrf @method('patch')
                    <select class="select" name="assigned_user_id">
                        <option value="">Sin asignar</option>
                        @foreach($advisors as $advisor)
                            <option value="{{ $advisor->id }}" @selected($selected->assigned_user_id == $advisor->id)>{{ $advisor->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn block" type="submit" style="margin-top:10px">Asignar conversación</button>
                </form>
            </div>
            <div class="detail-section">
                <h3>Notas</h3>
                <form method="post" action="{{ route('marketing.conversations.notes.store', $selected) }}">
                    @csrf
                    <textarea name="note" rows="4" placeholder="Agregar nota..."></textarea>
                    <button class="btn block" type="submit" style="margin-top:10px">Guardar nota</button>
                </form>
            </div>
            <a class="btn block" href="{{ route('marketing.leads.edit', $selected->lead) }}">Ver perfil completo</a>
        @endif
    </aside>
</div>
@endsection
