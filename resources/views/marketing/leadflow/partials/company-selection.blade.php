@php
    $pickerCompanies = $workspaceCompanies ?? $companies;
    $favoriteCompanies = $pickerCompanies->where('is_favorite', true)->values();
    $selectionRoute = request()->route()->getName();
@endphp
<section class="panel pad marketing-favorites-panel" aria-label="Selección de empresa">
    <div class="panel-head marketing-favorites-heading">
        <h2>Empresas favoritas</h2>
        <span class="badge">{{ $favoriteCompanies->count() }}</span>
    </div>
    <div class="marketing-company-carousel" data-marketing-company-carousel data-marketing-selected-company="{{ $company?->id ?? 'all' }}">
        <button class="carousel-button" type="button" data-marketing-carousel-prev aria-label="Ver favoritas anteriores" title="Anteriores">&lsaquo;</button>
        <div class="marketing-company-carousel-track" data-marketing-carousel-track>
            @if (auth()->user()->canNavigateTo('marketing.companies'))
                <a class="square-card marketing-favorite-card marketing-favorite-command" href="{{ route('marketing.companies.create') }}">
                    <span class="metric-icon" aria-hidden="true">+</span>
                    <strong>Nueva empresa</strong>
                </a>
            @endif
            <a class="square-card marketing-favorite-card marketing-favorite-command {{ $company ? '' : 'active' }}" href="{{ route($selectionRoute, ['company_id' => 'all', 'campaign' => 'all']) }}" @if (!$company) aria-current="page" @endif>
                <span class="metric-icon" aria-hidden="true">▦</span>
                <strong>Todas</strong>
            </a>
            @foreach($favoriteCompanies as $item)
                <div class="marketing-favorite-card-wrap {{ $company?->is($item) ? 'active' : '' }}" data-marketing-favorite-id="{{ $item->id }}">
                    <a class="square-card marketing-favorite-card" href="{{ route($selectionRoute, ['company_id' => $item->id, 'campaign' => 'all']) }}" @if ($company?->is($item)) aria-current="page" @endif>
                        @if ($company?->is($item))<span class="check" aria-hidden="true">✓</span>@endif
                        <span class="logo-circle" style="background:{{ $item->brand_color }}">{{ $item->logo_initials }}</span>
                        <strong>{{ $item->name }}</strong>
                        <span class="status-dot {{ $item->active ? 'active' : 'inactive' }}">{{ $item->active ? 'Activa' : 'Inactiva' }}</span>
                        <span class="muted small">{{ $item->campaigns_count }} campañas · {{ number_format($item->leads_count) }} leads</span>
                    </a>
                    @if (auth()->user()->canNavigateTo('marketing.companies'))
                        <form method="post" action="{{ route('marketing.companies.favorite', $item) }}">
                            @csrf @method('patch')
                            <input type="hidden" name="favorite" value="0">
                            <button class="marketing-favorite-star" type="submit" aria-label="Quitar {{ $item->name }} de favoritas" title="Quitar de favoritas">★</button>
                        </form>
                    @endif
                </div>
            @endforeach
            @if ($favoriteCompanies->isEmpty())
                <span class="marketing-favorite-empty">Sin favoritas</span>
            @endif
        </div>
        <button class="carousel-button" type="button" data-marketing-carousel-next aria-label="Ver favoritas siguientes" title="Siguientes">&rsaquo;</button>
    </div>
    <form class="marketing-company-picker" method="get" action="{{ route($selectionRoute) }}" data-marketing-company-picker>
        <label for="marketing-company-select">Empresa</label>
        <select class="select" id="marketing-company-select" name="company_id">
            <option value="all" @selected(!$company)>Todas las empresas</option>
            @foreach($pickerCompanies as $item)
                <option value="{{ $item->id }}" @selected($company?->id === $item->id)>{{ $item->name }}</option>
            @endforeach
        </select>
        <input type="hidden" name="campaign" value="all">
        <button class="btn" type="submit">Aplicar</button>
    </form>
</section>
