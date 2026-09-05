@extends('layouts.app')

@section('body')
    <x-app-shell :title="$label">
        @if ($showProjectCarousel)
            <section class="panel construction-carousel-panel" data-construction-carousel>
                <div class="construction-carousel-header">
                    <div class="construction-carousel-title">
                        <span class="construction-carousel-count">{{ $carouselProjects->count() }}</span>
                        <h2>Obras activas y por iniciar</h2>
                    </div>
                    <a class="button ghost small" href="{{ route('construction.dashboard') }}">Atras</a>
                </div>

                <div class="construction-carousel-shell">
                    <button class="construction-carousel-nav" type="button" data-carousel-prev aria-label="Anterior">&lt;</button>

                    <div class="construction-carousel-track" data-construction-carousel-track>
                        @if ($showMaterialsCatalogButton ?? false)
                            <a
                                class="construction-project-tile construction-project-tile-catalog"
                                href="#materials-explosion-catalog"
                                data-carousel-option
                                data-materials-catalog-select
                                aria-label="Catalogo de explosion de insumos"
                                aria-pressed="false"
                            >
                                <span class="construction-project-avatar">CAT</span>
                                <span class="construction-project-key">Catalogo general</span>
                                <strong class="construction-project-name">Catalogo de explosion de insumos</strong>
                                <span class="construction-project-status"><span></span>Informacion general</span>
                            </a>
                        @endif

                        @forelse ($carouselProjects as $project)
                            <button class="construction-project-tile {{ $loop->first ? 'active' : '' }}" type="button" data-carousel-option data-project-select data-carousel-project-id="{{ $project->id }}" data-carousel-project-status="{{ $project->status }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}">
                                <span class="construction-project-avatar">
                                    @if ($project->photo_path)
                                        <img src="{{ $project->photo_path }}" alt="">
                                    @else
                                        {{ substr($project->project_key, -2) }}
                                    @endif
                                </span>
                                <span class="construction-project-key">{{ $project->project_key }}</span>
                                <strong class="construction-project-name">{{ $project->name }}</strong>
                                <span class="construction-project-status"><span></span>{{ $project->status }}</span>
                            </button>
                        @empty
                            <button class="construction-project-tile active" type="button" disabled>
                                <span class="construction-project-avatar">OB</span>
                                <span class="construction-project-key">Sin obras</span>
                                <strong class="construction-project-name">No hay obras visibles</strong>
                                <span class="construction-project-status"><span></span>Pendiente</span>
                            </button>
                        @endforelse
                    </div>

                    <button class="construction-carousel-nav" type="button" data-carousel-next aria-label="Siguiente">&gt;</button>
                </div>
            </section>
        @endif

        @if ($showGeneratorPanel ?? false)
            @include('construction.partials.generator-quantification', [
                'activeProjects' => $carouselProjects,
            ])
        @elseif ($showMaterialsCatalogButton ?? false)
            @include('construction.partials.materials-explosion-catalog')
        @else
            <section class="panel">
                <div class="panel-header">
                    <div>
                        <h2>{{ $label }}</h2>
                        <p class="fine-print">Esta seccion venia como menu del codigo de Software Obras y quedo conectada dentro de Administracion de obra.</p>
                    </div>
                    <a class="button ghost" href="{{ route('construction.dashboard') }}">Panel de obra</a>
                </div>
                <p>La pantalla especifica de <strong>{{ $label }}</strong> esta lista como entrada de menu y queda preparada para desarrollar su CRUD en la siguiente fase.</p>
            </section>
        @endif

        @if ($showProjectCarousel)
            <script>
                document.querySelectorAll('[data-construction-carousel]').forEach((carousel) => {
                    const track = carousel.querySelector('[data-construction-carousel-track]');
                    const scrollByTile = () => Math.max(220, Math.round((track?.clientWidth || 260) * 0.72));

                    carousel.querySelector('[data-carousel-prev]')?.addEventListener('click', () => {
                        track?.scrollBy({ left: -scrollByTile(), behavior: 'smooth' });
                    });

                    carousel.querySelector('[data-carousel-next]')?.addEventListener('click', () => {
                        track?.scrollBy({ left: scrollByTile(), behavior: 'smooth' });
                    });

                    carousel.querySelectorAll('[data-carousel-option]').forEach((button) => {
                        button.addEventListener('click', () => {
                            carousel.querySelectorAll('[data-carousel-option]').forEach((item) => {
                                item.classList.toggle('active', item === button);
                                item.setAttribute('aria-pressed', item === button ? 'true' : 'false');
                            });
                        });
                    });
                });
            </script>
        @endif
    </x-app-shell>
@endsection
