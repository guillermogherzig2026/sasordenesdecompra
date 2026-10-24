@props(['title' => 'Marketing'])

@php
    $user = auth()->user();
    $items = collect(\App\Support\MarketingNavigation::items())
        ->filter(fn (array $item) => $user?->canNavigateTo($item['permission']) ?? false);
    $initials = collect(preg_split('/\s+/', trim((string) ($user?->name ?? 'Usuario'))))
        ->filter()
        ->take(2)
        ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');
@endphp

<div class="sas-app-shell">
    <aside class="sas-sidebar" aria-label="Navegacion principal">
        <a class="sas-brand" href="{{ route('marketing.index') }}">
            <span class="sas-brand-mark" aria-hidden="true">OC</span>
            <span>
                <strong>{{ config('app.name', 'OC System') }}</strong>
                <small>Panel de Marketing</small>
            </span>
        </a>

        <nav class="sas-navigation">
            <details class="sas-nav-group" open>
                <summary>
                    <span>Marketing</span>
                    <span class="sas-nav-chevron" aria-hidden="true">⌃</span>
                </summary>
                <div class="sas-nav-list">
                    @foreach ($items as $item)
                        @php
                            $active = request()->routeIs($item['route'])
                                || request()->routeIs($item['route_prefix'].'*');
                        @endphp
                        <a class="sas-nav-link {{ $active ? 'is-active' : '' }}"
                           href="{{ route($item['route']) }}"
                           @if ($active) aria-current="page" @endif>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            </details>
        </nav>
    </aside>

    <section class="sas-page">
        <header class="sas-topbar">
            <div>
                <span class="sas-eyebrow">Panel administrativo</span>
                <h1>{{ $title }}</h1>
            </div>
            <div class="sas-account">
                <span class="sas-avatar" aria-hidden="true">{{ $initials ?: 'U' }}</span>
                <span class="sas-account-name">{{ $user?->name ?? 'Usuario' }}</span>
                @if (Route::has('logout'))
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="sas-logout" type="submit">Salir</button>
                    </form>
                @endif
            </div>
        </header>

        <main class="sas-view">
            {{ $slot }}
        </main>
    </section>
</div>
