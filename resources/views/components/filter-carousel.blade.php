@props(['label', 'items', 'selected', 'routeName', 'parameter'])

@php($baseQuery = request()->except([$parameter, 'page']))

<nav class="catalog-filter-carousel" aria-label="{{ $label }}" data-catalog-filter-carousel>
    <button class="catalog-filter-arrow" type="button" aria-label="Filtros anteriores" title="Filtros anteriores" data-carousel-previous>‹</button>
    <div class="catalog-filter-track" data-carousel-track>
        <a class="catalog-filter-card {{ $selected === '' ? 'is-selected' : '' }}"
           href="{{ route($routeName, $baseQuery) }}" @if ($selected === '') aria-current="page" @endif>
            <span class="catalog-filter-symbol" aria-hidden="true">▦</span>
            <strong>Todos</strong>
        </a>
        @foreach ($items as $item)
            <a class="catalog-filter-card {{ $selected === $item['value'] ? 'is-selected' : '' }}"
               href="{{ route($routeName, [...$baseQuery, $parameter => $item['value']]) }}"
               @if ($selected === $item['value']) aria-current="page" @endif>
                <span class="catalog-filter-symbol" aria-hidden="true">{{ $item['symbol'] ?? '▣' }}</span>
                <strong>{{ $item['label'] }}</strong>
            </a>
        @endforeach
    </div>
    <button class="catalog-filter-arrow" type="button" aria-label="Más filtros" title="Más filtros" data-carousel-next>›</button>
</nav>

@once
    <style>
        .catalog-filter-carousel { min-width: 0; display: grid; grid-template-columns: 30px minmax(0, 1fr) 30px; align-items: center; gap: 9px; }
        .catalog-filter-arrow { width: 30px; height: 30px; padding: 0; border: 1px solid var(--line); border-radius: 50%; background: #fff; color: var(--primary-strong); cursor: pointer; font-size: 1.25rem; line-height: 1; box-shadow: 0 2px 6px rgba(24, 44, 64, .1); }
        .catalog-filter-arrow:disabled { opacity: .42; cursor: default; box-shadow: none; }
        .catalog-filter-track { min-width: 0; display: flex; gap: 8px; overflow-x: auto; scroll-behavior: smooth; scroll-snap-type: x mandatory; scrollbar-width: none; padding: 2px 1px 7px; }
        .catalog-filter-track::-webkit-scrollbar { display: none; }
        .catalog-filter-card { width: max-content; min-width: 112px; max-width: 200px; height: 82px; flex: 0 0 auto; padding: 8px 10px; border: 1px solid #d6e3f1; border-radius: 7px; background: #fff; color: #17324a; display: grid; justify-items: center; align-content: center; gap: 4px; text-align: center; text-decoration: none; scroll-snap-align: start; box-shadow: 0 1px 3px rgba(23, 49, 74, .05); }
        .catalog-filter-card strong { max-width: 100%; overflow-wrap: anywhere; font-size: .78rem; line-height: 1.15; }
        .catalog-filter-symbol { color: #24669d; font-size: 1.43rem; line-height: 1; font-weight: 850; }
        .catalog-filter-card:nth-child(4n+2) .catalog-filter-symbol { color: #078fb3; }
        .catalog-filter-card:nth-child(4n+3) .catalog-filter-symbol { color: #5168b9; }
        .catalog-filter-card:nth-child(4n+4) .catalog-filter-symbol { color: #149566; }
        .catalog-filter-card.is-selected { border-color: #1b6d88; background: #1b6d88; color: #fff; }
        .catalog-filter-card.is-selected .catalog-filter-symbol { color: #fff; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-catalog-filter-carousel]').forEach((carousel) => {
                const track = carousel.querySelector('[data-carousel-track]');
                const previous = carousel.querySelector('[data-carousel-previous]');
                const next = carousel.querySelector('[data-carousel-next]');
                const update = () => {
                    previous.disabled = track.scrollLeft <= 1;
                    next.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 1;
                };
                previous.addEventListener('click', () => track.scrollBy({ left: -Math.max(track.clientWidth * .7, 230), behavior: 'smooth' }));
                next.addEventListener('click', () => track.scrollBy({ left: Math.max(track.clientWidth * .7, 230), behavior: 'smooth' }));
                track.addEventListener('scroll', update, { passive: true });
                window.addEventListener('resize', update);
                track.querySelector('[aria-current="page"]')?.scrollIntoView({ block: 'nearest', inline: 'nearest' });
                update();
            });
        });
    </script>
@endonce
