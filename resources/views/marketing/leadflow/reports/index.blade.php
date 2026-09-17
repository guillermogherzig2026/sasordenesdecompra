@extends('marketing.leadflow.layout', ['title' => 'Reportes | LeadFlow'])

@section('content')
<div class="page-head">
    <div>
        <h1>Reportes</h1>
        <p>Analiza el rendimiento de tus empresas, campañas, leads y conversaciones en un solo lugar.</p>
    </div>
    <button class="btn">Exportar ⌄</button>
</div>

@include('marketing.leadflow.partials.workspace-selection', [
    'selectionRoute' => 'marketing.reports.index',
])

<div class="panel pad" style="margin-bottom:16px">
    <form class="selector-row" method="get">
        <input type="hidden" name="company_id" value="{{ $company?->id }}">
        <input type="hidden" name="campaign_id" value="{{ $campaign?->id ?? 'all' }}">
        <div class="field">
            <label>Rango de fechas</label>
            <select class="select" name="range">
                <option>15 may 2024 - 15 may 2024</option>
                <option>Últimos 7 días</option>
                <option>Últimos 30 días</option>
            </select>
        </div>
        <div class="field" style="align-self:end"><button class="btn block" type="submit">Filtros</button></div>
    </form>
</div>

<div class="grid cols-4" style="margin-bottom:16px">
    <div class="panel metric"><span class="metric-icon">♙</span><div><span>Leads totales</span><strong>{{ number_format($metrics['leads']) }}</strong><span class="trend">↑ 12.5%</span></div></div>
    <div class="panel metric"><span class="metric-icon" style="background:var(--blue-100);color:var(--blue-600)">◌</span><div><span>Conversaciones</span><strong>{{ number_format($metrics['conversations']) }}</strong><span class="trend">↑ 8.3%</span></div></div>
    <div class="panel metric"><span class="metric-icon" style="background:var(--purple-100);color:var(--purple-600)">↗</span><div><span>Tasa de conversión</span><strong>{{ $metrics['conversion_rate'] }}%</strong><span class="trend">↑ 1.2 pp</span></div></div>
    <div class="panel metric"><span class="metric-icon">$</span><div><span>Ventas estimadas</span><strong>${{ number_format($metrics['sales'], 2) }}</strong><span class="trend">↑ 18.4%</span></div></div>
</div>

<div class="grid cols-3" style="margin-bottom:16px">
    <div class="panel pad">
        <div class="panel-head" style="padding:0 0 12px"><h2>Leads a lo largo del tiempo</h2><span class="btn">Diario</span></div>
        <div class="bar-line">
            @foreach([44, 62, 36, 30, 64, 80, 90] as $height)
                <div class="bar" style="--h:{{ $height }}%"></div>
            @endforeach
        </div>
    </div>
    <div class="panel pad">
        <h2>Conversaciones por campaña</h2>
        <div class="donut" data-label="{{ number_format($metrics['conversations']) }} Total"></div>
        @foreach($campaignPerformance->take(5) as $item)
            <div class="detail-row"><span>{{ $item->name }}</span><strong>{{ number_format($item->conversations_count) }}</strong></div>
        @endforeach
    </div>
    <div class="panel pad">
        <h2>Embudo de conversión</h2>
        @foreach([100, 76, 54, 38, 22] as $width)
            <div class="funnel-step" style="width:{{ $width }}%"></div>
        @endforeach
        <div class="detail-row"><span>Leads</span><strong>{{ number_format($metrics['leads']) }}</strong></div>
        <div class="detail-row"><span>Conversaciones</span><strong>{{ number_format($metrics['conversations']) }}</strong></div>
        <div class="detail-row"><span>Ventas</span><strong>{{ number_format($campaignPerformance->sum('converted_sales')) }}</strong></div>
    </div>
</div>

<div class="grid cols-3" style="margin-bottom:16px">
    <div class="panel pad">
        <div class="panel-head" style="padding:0 0 12px"><h2>Tiempo de respuesta</h2><span class="btn">Diario</span></div>
        <div class="bar-line">
            @foreach([64, 78, 58, 56, 42, 34, 40] as $height)
                <div class="bar" style="--h:{{ $height }}%;background:linear-gradient(180deg,#6d28d9,#ddd6fe)"></div>
            @endforeach
        </div>
    </div>
    <div class="panel pad">
        <h2>Ventas y ROAS</h2>
        <div class="bar-line">
            @foreach([54, 66, 48, 64, 75, 70, 82] as $height)
                <div class="bar" style="--h:{{ $height }}%"></div>
            @endforeach
        </div>
    </div>
    <div class="panel pad">
        <h2>Leads por empresa</h2>
        <div class="donut" data-label="{{ number_format($metrics['leads']) }} Total"></div>
        @foreach($leadsByCompany as $row)
            <div class="detail-row"><span>{{ $row->company->name }}</span><strong>{{ number_format($row->total) }}</strong></div>
        @endforeach
    </div>
</div>

<div class="grid cols-2">
    <div class="panel table-wrap">
        <div class="panel-head"><h2>Top campañas por rendimiento</h2></div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Campaña</th>
                    <th>Leads</th>
                    <th>Conversaciones</th>
                    <th>Tasa conv.</th>
                    <th>Ventas</th>
                    <th>ROAS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($campaignPerformance as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $item->name }}</strong> <span class="badge green">{{ \App\Models\Marketing\Campaign::STATUSES[$item->status] }}</span></td>
                        <td>{{ number_format($item->leads_count) }}</td>
                        <td>{{ number_format($item->conversations_count) }}</td>
                        <td>{{ $item->leads_count ? round(($item->conversations_count / $item->leads_count) * 100, 1) : 0 }}%</td>
                        <td>${{ number_format($item->converted_sales ?? 0, 2) }}</td>
                        <td>{{ $item->spent > 0 ? round(($item->converted_sales ?? 0) / $item->spent, 1) : 0 }}x</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="panel pad">
        <h2>Actividad reciente de reportes</h2>
        @foreach($recentReports as $message)
            <div class="detail-section">
                <strong>{{ $message->message_type === 'note' ? 'Resumen de conversación' : 'Análisis de lead' }}</strong>
                <p class="muted small" style="margin:4px 0 0">Generado por {{ $message->user?->name ?? 'Sistema' }} · {{ $message->sent_at?->format('d M Y, H:i') }}</p>
            </div>
        @endforeach
        <a class="btn block" href="{{ route('marketing.reports.index') }}">Ver todos los reportes</a>
    </div>
</div>
@endsection
