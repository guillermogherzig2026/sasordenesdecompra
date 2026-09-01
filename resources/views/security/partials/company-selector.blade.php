@php
    $selectorQuery = ['section' => $securitySectionKey];

    if ($securitySectionKey === 'analytics') {
        $selectorQuery['analytics_date'] = $analyticsDate;
    }
@endphp

<section class="security-section-company-selector" data-security-section-company-selector data-security-section-carousel>
    <div class="security-carousel-shell">
        <button class="security-carousel-nav" type="button" data-security-section-carousel-prev aria-label="Ver empresas anteriores">&lsaquo;</button>

        <div class="security-carousel-track" data-security-section-carousel-track>
            @forelse ($securityCompanies as $securityCompany)
                <a
                    class="security-company-card is-tone-{{ ($loop->index % 5) + 1 }} {{ $selectedSectionCompany?->is($securityCompany) ? 'is-active' : '' }}"
                    href="{{ route('security.index', array_merge($selectorQuery, ['company_id' => $securityCompany->id])) }}"
                    data-security-section-carousel-item
                    @if ($selectedSectionCompany?->is($securityCompany)) aria-current="true" @endif
                >
                    <span class="security-company-avatar" aria-hidden="true">{{ $securityCompany->initials() }}</span>
                    <span class="security-company-card-copy">
                        <strong>{{ $securityCompany->name }}</strong>
                        <small>{{ $securityCompany->branches_count }} {{ $securityCompany->branches_count === 1 ? 'sucursal' : 'sucursales' }}</small>
                    </span>
                </a>
            @empty
                <a class="security-company-card security-company-selector-empty" href="{{ route('security.index') }}">
                    <span class="security-company-avatar" aria-hidden="true">+</span>
                    <span class="security-company-card-copy">
                        <strong>Registrar empresa o negocio</strong>
                        <small>Abre el catálogo de Vigilancia.</small>
                    </span>
                </a>
            @endforelse
        </div>

        <button class="security-carousel-nav" type="button" data-security-section-carousel-next aria-label="Ver empresas siguientes">&rsaquo;</button>
    </div>

</section>
