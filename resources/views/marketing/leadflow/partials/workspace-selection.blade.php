<div class="panel pad" style="margin-bottom:16px">
    <h2><span class="badge green">1</span> Empresa seleccionada</h2>
    <div class="card-row">
        @foreach($companies as $item)
            <a
                class="square-card {{ $company?->id === $item->id ? 'active' : '' }}"
                href="{{ route($selectionRoute, ['company_id' => $item->id, 'campaign' => 'all']) }}"
            >
                @if($company?->id === $item->id)<span class="check">✓</span>@endif
                <span class="logo-circle" style="background:{{ $item->brand_color }}">{{ $item->logo_initials }}</span>
                <strong>{{ $item->name }}</strong>
                <span class="status-dot {{ $item->active ? 'active' : 'inactive' }}">{{ $item->active ? 'Activa' : 'Inactiva' }}</span>
            </a>
        @endforeach
    </div>
</div>

<div class="panel pad" style="margin-bottom:16px">
    <h2><span class="badge green">2</span> Campañas de {{ $company?->name ?? 'la empresa seleccionada' }}</h2>
    <div class="card-row">
        <a class="square-card" href="{{ route('marketing.campaigns.create', ['company_id' => $company?->id]) }}">
            <span class="metric-icon">+</span>
            <strong>Nueva campaña</strong>
        </a>
        <a
            class="square-card {{ $campaign ? '' : 'active' }}"
            href="{{ route($selectionRoute, ['company_id' => $company?->id, 'campaign' => 'all']) }}"
        >
            @unless($campaign)<span class="check">✓</span>@endunless
            <span class="metric-icon">▦</span>
            <strong>Todas</strong>
        </a>
        @foreach($campaigns as $item)
            <a
                class="square-card {{ $campaign?->id === $item->id ? 'active' : '' }}"
                href="{{ route($selectionRoute, ['company_id' => $company?->id, 'campaign_id' => $item->id]) }}"
            >
                @if($campaign?->id === $item->id)<span class="check">✓</span>@endif
                <span class="metric-icon">{{ $loop->iteration }}</span>
                <strong>{{ $item->name }}</strong>
                <span class="status-dot {{ $item->status }}">{{ \App\Models\Marketing\Campaign::STATUSES[$item->status] ?? $item->status }}</span>
            </a>
        @endforeach
    </div>
</div>
