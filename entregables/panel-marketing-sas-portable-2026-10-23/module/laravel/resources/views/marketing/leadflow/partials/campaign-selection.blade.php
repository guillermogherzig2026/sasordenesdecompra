<section class="panel pad campaign-selector" aria-label="Selección de campaña">
    <h2>Campañas de {{ $company?->name ?? 'todas las empresas' }}</h2>
    <div class="card-row">
        @if (auth()->user()->canNavigateTo('marketing.campaigns'))
            <a class="square-card" href="{{ route('marketing.campaigns.create', ['company_id' => $company?->id]) }}">
                <span class="metric-icon" aria-hidden="true">+</span>
                <strong>Nueva campaña</strong>
            </a>
        @endif
        <a class="square-card {{ $campaign ? '' : 'active' }}" href="{{ route($selectionRoute, array_merge($selectionParameters ?? [], ['company_id' => $company?->id ?? 'all', 'campaign' => 'all'])) }}" @if (!$campaign) aria-current="page" @endif>
            @unless($campaign)<span class="check" aria-hidden="true">✓</span>@endunless
            <span class="metric-icon" aria-hidden="true">▦</span>
            <strong>Todas las campañas</strong>
        </a>
        @foreach($campaigns as $item)
            <a class="square-card {{ $campaign?->is($item) ? 'active' : '' }}" href="{{ route($selectionRoute, array_merge($selectionParameters ?? [], ['company_id' => $item->company_id, 'campaign_id' => $item->id])) }}" @if ($campaign?->is($item)) aria-current="page" @endif>
                @if($campaign?->is($item))<span class="check" aria-hidden="true">✓</span>@endif
                <span class="metric-icon">{{ $loop->iteration }}</span>
                <strong>{{ $item->name }}</strong>
                <span class="status-dot {{ $item->status }}">{{ \App\Models\Marketing\Campaign::STATUSES[$item->status] ?? $item->status }}</span>
            </a>
        @endforeach
    </div>
</section>
