@extends('layouts.app')

@section('body')
    <x-app-shell title="Historial de pagos">
        @php
            $activeProjects = $projects->where('status', 'En ejecucion')->values();

            if ($activeProjects->isEmpty()) {
                $activeProjects = $projects->values();
            }
        @endphp

        <section class="panel construction-carousel-panel" data-construction-carousel>
            <div class="construction-carousel-header">
                <div class="construction-carousel-title">
                    <span class="construction-carousel-count">{{ $activeProjects->count() }}</span>
                    <h2>Obras activas</h2>
                </div>
                <a class="button ghost small" href="{{ route('construction.placeholder', ['section' => 'mano-obra', 'project' => $selectedProjectId]) }}">Atras</a>
            </div>

            <div class="construction-carousel-shell">
                <button class="construction-carousel-nav" type="button" data-carousel-prev aria-label="Anterior">&lt;</button>
                <div class="construction-carousel-track" data-construction-carousel-track>
                    @forelse ($activeProjects as $project)
                        <a
                            class="construction-project-tile {{ $project->id === $selectedProjectId ? 'active' : '' }}"
                            href="{{ route('construction.placeholder', ['section' => 'pagos', 'project' => $project->id]) }}"
                            aria-current="{{ $project->id === $selectedProjectId ? 'page' : 'false' }}"
                        >
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
                        </a>
                    @empty
                        <span class="construction-project-tile">
                            <strong class="construction-project-name">No hay obras visibles</strong>
                        </span>
                    @endforelse
                </div>
                <button class="construction-carousel-nav" type="button" data-carousel-next aria-label="Siguiente">&gt;</button>
            </div>
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Historial de pagos</h2>
                    <p class="fine-print">Nominas y estimaciones pagadas de la obra seleccionada.</p>
                </div>
            </div>

            @include('construction.partials.payment-order-table', [
                'paymentOrders' => $paymentOrders,
                'financeContext' => false,
                'allowPaymentUpload' => false,
                'emptyMessage' => 'No hay pagos realizados para esta obra.',
            ])
        </section>

        <script>
            (() => {
                document.querySelectorAll('[data-construction-carousel]').forEach((carousel) => {
                    const track = carousel.querySelector('[data-construction-carousel-track]');
                    const amount = () => Math.max(220, Math.round((track?.clientWidth || 260) * 0.72));
                    carousel.querySelector('[data-carousel-prev]')?.addEventListener('click', () => track?.scrollBy({ left: -amount(), behavior: 'smooth' }));
                    carousel.querySelector('[data-carousel-next]')?.addEventListener('click', () => track?.scrollBy({ left: amount(), behavior: 'smooth' }));
                });
            })();
        </script>
    </x-app-shell>
@endsection
