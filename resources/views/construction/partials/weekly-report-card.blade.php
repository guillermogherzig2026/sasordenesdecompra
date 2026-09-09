@php
    $showActions = $showActions ?? true;
    $embedded = $embedded ?? false;
@endphp

<article
    class="weekly-report-card {{ $embedded ? 'weekly-report-card-embedded' : '' }}"
    id="{{ $embedded ? 'weekly-report-dialog-card-' : 'weekly-report-' }}{{ $report['id'] }}"
    data-weekly-report-card="{{ $report['id'] }}"
    data-weekly-report-actual="{{ $report['actual_progress'] }}"
    data-weekly-report-planned="{{ $report['planned_progress'] }}"
>
    <div class="weekly-report-cover">
        <img src="{{ $report['hero_photo'] }}" alt="Avance de la obra {{ $report['project_name'] }}">
        <div class="weekly-report-cover-copy">
            <strong>Reporte<br>semanal de obra</strong>
            <span>{{ $report['project_name'] }} &middot; {{ $report['project_key'] }}</span>
            <span>{{ $report['week_label'] }} &middot; {{ $report['period_long'] }}</span>
        </div>
    </div>

    @if ($embedded)
        <div class="weekly-report-meta">
            <span class="weekly-report-generated"><i aria-hidden="true">&#10003;</i>{{ $report['status'] }}</span>
            <span><i class="weekly-report-calendar-icon" aria-hidden="true"></i>{{ $report['reported_at'] }}</span>
        </div>
    @endif

    <div class="weekly-report-metrics" aria-label="Indicadores del reporte semanal">
        <div class="weekly-report-metric">
            <span class="weekly-report-metric-icon chart" aria-hidden="true"><i></i><i></i><i></i></span>
            <span class="weekly-report-metric-copy">
                <strong>{{ $report['actual_progress'] }}%</strong>
                <small>Avance real</small>
            </span>
        </div>
        <div class="weekly-report-metric">
            <span class="weekly-report-metric-icon calendar" aria-hidden="true"><i></i></span>
            <span class="weekly-report-metric-copy">
                <strong>{{ $report['planned_progress'] }}%</strong>
                <small>Programado</small>
            </span>
        </div>
        <div class="weekly-report-metric">
            <span class="weekly-report-metric-icon variation {{ $report['variation'] < 0 ? 'negative' : 'positive' }}" aria-hidden="true"><i></i></span>
            <span class="weekly-report-metric-copy">
                <strong class="{{ $report['variation'] < 0 ? 'negative' : 'positive' }}">{{ $report['variation_label'] }}</strong>
                <small>Variación</small>
            </span>
        </div>
        <div class="weekly-report-metric">
            <span class="weekly-report-metric-icon photo" aria-hidden="true"><i></i></span>
            <span class="weekly-report-metric-copy">
                <strong>{{ $report['photo_count'] }}</strong>
                <small>fotografías</small>
            </span>
        </div>
    </div>

    <section class="weekly-report-overview" aria-labelledby="weekly-report-overview-title-{{ $report['id'] }}{{ $embedded ? '-dialog' : '' }}">
        <header>
            <h3 id="weekly-report-overview-title-{{ $report['id'] }}{{ $embedded ? '-dialog' : '' }}">
                <span class="weekly-report-document-icon" aria-hidden="true"></span>
                Resumen semanal de la obra
            </h3>
            <span class="weekly-report-auto-copy"><i aria-hidden="true"></i>Redacción generada automáticamente</span>
        </header>
        <p>{{ $report['summary'] }}</p>
    </section>

    <section class="weekly-report-packages" aria-labelledby="weekly-report-packages-title-{{ $report['id'] }}{{ $embedded ? '-dialog' : '' }}">
        <h3 id="weekly-report-packages-title-{{ $report['id'] }}{{ $embedded ? '-dialog' : '' }}">
            <span class="weekly-report-camera-icon" aria-hidden="true"></span>
            Paquetes de fotografías
        </h3>

        <div class="weekly-report-package-grid">
            @foreach ($report['photo_packages'] as $package)
                <article class="weekly-report-package">
                    <header class="weekly-report-package-header">
                        <span class="weekly-report-contractor-icon" aria-hidden="true"></span>
                        <strong>{{ $package['contractor'] }}</strong>
                        <span>{{ $package['photo_count'] }} {{ $package['photo_count'] === 1 ? 'fotografía' : 'fotografías' }}</span>
                        <i aria-hidden="true">&#8964;</i>
                    </header>

                    <div class="weekly-report-package-groups">
                        @foreach ($package['groups'] as $group)
                            <section class="weekly-report-package-group">
                                <h4>{{ $group['name'] }} <span>({{ count($group['photos']) }})</span></h4>
                                <div class="weekly-report-package-photos">
                                    @foreach ($group['photos'] as $photo)
                                        <figure>
                                            <img src="{{ $photo['url'] }}" alt="{{ $photo['caption'] }}" loading="lazy">
                                            <figcaption>
                                                <span><i class="weekly-report-tiny-calendar" aria-hidden="true"></i>{{ $photo['date'] }}</span>
                                                <strong>{{ $photo['caption'] }}</strong>
                                            </figcaption>
                                        </figure>
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <div class="weekly-report-followup">
        <section class="weekly-report-note-card">
            <header>
                <h3><span class="weekly-report-alert-icon" aria-hidden="true">!</span>Incidencias y acciones</h3>
                <span class="weekly-report-auto-copy"><i aria-hidden="true"></i>Redacción generada automáticamente</span>
            </header>
            <p>{{ $report['incident_actions'] }}</p>
        </section>
        <section class="weekly-report-note-card">
            <header>
                <h3><span class="weekly-report-next-icon" aria-hidden="true"></span>Próxima semana</h3>
                <span class="weekly-report-auto-copy"><i aria-hidden="true"></i>Redacción generada automáticamente</span>
            </header>
            <p>{{ $report['next_week_summary'] }}</p>
        </section>
        <div class="weekly-report-incidents {{ $report['incidents'] === 0 ? 'clear' : '' }}">
            <span class="weekly-report-warning-icon" aria-hidden="true">@if ($report['incidents'] === 0)&#10003;@else!@endif</span>
            <strong>{{ $report['incidents_label'] }}</strong>
            <span class="weekly-report-incidents-chevron" aria-hidden="true">&gt;</span>
        </div>
    </div>

    @if ($showActions)
        <div class="weekly-report-actions">
            <button class="weekly-report-action" type="button" data-weekly-report-open="{{ $report['id'] }}">
                <span class="weekly-report-eye-icon" aria-hidden="true"></span>
                Ver reporte
            </button>
            <button class="weekly-report-action" type="button" data-weekly-report-print="{{ $report['id'] }}">
                <span class="weekly-report-download-icon" aria-hidden="true"></span>
                Descargar PDF
            </button>
            <button class="weekly-report-action primary" type="button" data-weekly-report-share="{{ $report['id'] }}">
                <span class="weekly-report-share-icon" aria-hidden="true"></span>
                Compartir
            </button>
        </div>
    @endif
</article>
