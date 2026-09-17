@extends('marketing.leadflow.layout', ['title' => 'Campañas | LeadFlow'])

@section('content')
@include('marketing.leadflow.partials.workspace-selection', [
    'selectionRoute' => 'marketing.campaigns.index',
])

<div class="grid cols-4" style="margin-bottom:16px">
    <div class="panel metric"><span class="metric-icon">$</span><div><span>Inversión</span><strong>${{ number_format($summary['spent'], 2) }}</strong><span class="trend">↑ 18.6%</span></div></div>
    <div class="panel metric"><span class="metric-icon" style="background:var(--blue-100);color:var(--blue-600)">◌</span><div><span>Conversaciones</span><strong>{{ number_format($summary['conversations']) }}</strong><span class="trend">↑ 8.3%</span></div></div>
    <div class="panel metric"><span class="metric-icon">♙</span><div><span>Leads</span><strong>{{ number_format($summary['leads']) }}</strong><span class="trend">↑ 12.5%</span></div></div>
    <div class="panel metric"><span class="metric-icon" style="background:var(--amber-100);color:var(--amber-600)">▱</span><div><span>Ventas</span><strong>${{ number_format($summary['sales'], 2) }}</strong><span class="trend">↑ 17.8%</span></div></div>
</div>

<div class="split">
    <div class="panel table-wrap">
        <div class="panel-head">
            <h2><span class="badge green">3</span> Lista de campañas ({{ $campaigns->count() }})</h2>
            <div class="filters">
                <a class="btn" href="{{ route('marketing.campaigns.index', ['company_id' => $company?->id]) }}">Filtros</a>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Campaña</th>
                    <th>Empresa</th>
                    <th>Redes sociales</th>
                    <th>Inversión</th>
                    <th>Estado</th>
                    <th>Leads</th>
                    <th>Conversaciones</th>
                    <th>Ventas</th>
                    <th>Responsable</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($campaigns as $item)
                    <tr class="{{ $campaign?->id === $item->id ? 'selected' : '' }}">
                        <td><strong>{{ $item->name }}</strong><br><span class="muted small">{{ $item->product }}</span></td>
                        <td>{{ $item->company->name }}</td>
                        <td>
                            @forelse(($item->social_networks ?: array_filter(array_map('trim', explode(',', $item->source ?: '')))) as $network)
                                <span class="badge blue">{{ $network }}</span>
                            @empty
                                <span class="badge">Sin definir</span>
                            @endforelse
                        </td>
                        <td>${{ number_format($item->spent, 2) }}</td>
                        <td><span class="badge {{ $item->status === 'paused' ? 'amber' : ($item->status === 'ended' ? '' : 'green') }}">{{ \App\Models\Marketing\Campaign::STATUSES[$item->status] }}</span></td>
                        <td>{{ number_format($item->leads_count) }}</td>
                        <td>{{ number_format($item->conversations_count) }}</td>
                        <td>${{ number_format($item->leads_sum_estimated_value ?? 0, 2) }}</td>
                        <td>{{ $item->responsible?->name ?? 'Sin asignar' }}</td>
                        <td>
                            <a class="btn" href="{{ route('marketing.campaigns.edit', $item) }}">Editar</a>
                            <form method="post" action="{{ route('marketing.campaigns.toggle', $item) }}" style="display:inline">
                                @csrf @method('patch')
                                <button class="btn" type="submit">{{ $item->status === 'active' ? 'Pausar' : 'Activar' }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <aside class="panel pad detail-panel">
        @php($detail = $campaign ?: $campaigns->first())
        @if($detail)
            <h2><span class="badge green">4</span> Detalles de la campaña</h2>
            <div style="display:flex;gap:12px;align-items:center;margin:14px 0">
                <span class="metric-icon">{{ substr($detail->name, 0, 1) }}</span>
                <div>
                    <h2 style="margin:0">{{ $detail->name }}</h2>
                    <span class="badge green">{{ \App\Models\Marketing\Campaign::STATUSES[$detail->status] }}</span>
                </div>
            </div>
            <div class="detail-row"><span>Empresa</span><strong>{{ $detail->company->name }}</strong></div>
            <div class="detail-row"><span>Responsable</span><strong>{{ $detail->responsible?->name ?? 'Sin asignar' }}</strong></div>
            <div class="detail-row"><span>Fecha de inicio</span><strong>{{ $detail->starts_at?->format('d M Y') ?? '-' }}</strong></div>
            <div class="detail-row"><span>Fecha de fin</span><strong>{{ $detail->ends_at?->format('d M Y') ?? '-' }}</strong></div>
            <div class="detail-row"><span>Presupuesto</span><strong>${{ number_format($detail->budget, 2) }}</strong></div>
            <div class="detail-row"><span>Inversión usada</span><strong>${{ number_format($detail->spent, 2) }}</strong></div>
            <div class="bar-line">
                @foreach([32, 48, 36, 54, 72, 84, 76] as $height)
                    <div class="bar" style="--h:{{ $height }}%"></div>
                @endforeach
            </div>
            <a class="btn primary block" href="{{ route('marketing.reports.index', ['company_id' => $company?->id, 'campaign_id' => $detail->id]) }}">Ver reporte completo</a>
        @endif
    </aside>
</div>
@endsection
