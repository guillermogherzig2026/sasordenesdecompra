@php
    $activeCarouselProjects = $activeCarouselProjects ?? collect();
    $currentProject = request()->route('project');
    $currentProjectId = $currentProject?->id;
@endphp

@if($activeCarouselProjects->isNotEmpty())
    <section class="active-project-panel" data-project-carousel>
        <div class="active-project-head">
            <div class="active-project-title">
                <span class="active-project-number">{{ $activeCarouselProjects->count() }}</span>
                <span>Obras activas</span>
            </div>
            <button class="btn btn-soft back-button" type="button" data-back-button data-fallback-url="{{ route('dashboard') }}" title="Volver a la pantalla anterior">
                <i data-lucide="arrow-left"></i>
                Atras
            </button>
        </div>
        <div class="project-carousel-shell">
            <button class="project-carousel-button project-carousel-prev" type="button" aria-label="Obras anteriores" data-carousel-prev>&lsaquo;</button>
            <div class="project-mini-track" data-project-track>
                @foreach($activeCarouselProjects as $carouselProject)
                    <a class="project-mini-card {{ $currentProjectId === $carouselProject->id ? 'active' : '' }}" href="{{ route('obras.show', $carouselProject) }}">
                        <img
                            class="project-mini-image"
                            src="{{ $carouselProject->photo_path ?: asset('images/projects/residencial-los-pinos.png') }}"
                            alt="{{ $carouselProject->name }}"
                        >
                        <div class="project-mini-key">{{ $carouselProject->project_key }}</div>
                        <div class="project-mini-name">{{ $carouselProject->name }}</div>
                        <span class="project-mini-state">
                            <span class="status-dot {{ $carouselProject->status === 'Por iniciar' ? 'dot-secondary' : 'dot-success' }}"></span>
                            {{ $carouselProject->status }}
                        </span>
                    </a>
                @endforeach
            </div>
            <button class="project-carousel-button project-carousel-next" type="button" aria-label="Obras siguientes" data-carousel-next>&rsaquo;</button>
        </div>
    </section>
@else
    <div class="screen-back-row">
        <button class="btn btn-soft back-button" type="button" data-back-button data-fallback-url="{{ route('dashboard') }}" title="Volver a la pantalla anterior">
            <i data-lucide="arrow-left"></i>
            Atras
        </button>
    </div>
@endif
