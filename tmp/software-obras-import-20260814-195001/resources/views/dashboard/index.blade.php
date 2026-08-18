@extends('layouts.app')

@section('title', 'Inicio | Control de Obras')
@section('page-title', 'Inicio')
@section('page-subtitle', 'Vista ejecutiva de obras activas, avances y pagos')

@section('content')
    @php
        $money = fn ($value) => '$'.number_format((float) $value, 2);
    @endphp

    <section class="metric-grid mb-3">
        <article class="metric-card">
            <div class="metric-label">Obras registradas</div>
            <div class="metric-value">{{ $summary['projects'] }}</div>
            <div class="metric-hint">{{ $summary['active'] }} en ejecucion</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Valor contratado</div>
            <div class="metric-value">{{ $money($summary['contracted']) }}</div>
            <div class="metric-hint">Todas las obras visibles</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Pagado acumulado</div>
            <div class="metric-value">{{ $money($summary['paid']) }}</div>
            <div class="metric-hint">Saldo: {{ $money($summary['pending']) }}</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Promedio de avance</div>
            <div class="metric-value">{{ $summary['physical'] }}%</div>
            <div class="metric-hint">Financiero: {{ $summary['financial'] }}%</div>
        </article>
    </section>

    <div class="row g-3">
        <div class="col-xl-7">
            <section class="panel-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h5 m-0 fw-bold">Avance fisico vs financiero</h2>
                    <a class="btn btn-aqua btn-sm" href="{{ route('obras.index') }}">
                        <i data-lucide="building-2"></i>
                        Ver obras
                    </a>
                </div>
                <canvas id="dashboardChart" height="140"></canvas>
            </section>
        </div>
        <div class="col-xl-5">
            <section class="panel-card h-100">
                <h2 class="h5 fw-bold mb-3">Obras con mayor saldo</h2>
                <div class="excel-wrap">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Obra</th>
                                <th>Estado</th>
                                <th class="text-end">Por pagar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projects->sortByDesc('balance_to_pay')->take(5) as $project)
                                <tr>
                                    <td>
                                        <strong>{{ $project->project_key }}</strong><br>
                                        <span class="text-muted">{{ $project->name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge-soft badge-{{ $project->statusColor() }}">
                                            <span class="status-dot"></span>{{ $project->status }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold">{{ $money($project->balance_to_pay) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Sin obras visibles</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const dashboardChartElement = document.getElementById('dashboardChart');
        if (dashboardChartElement && window.Chart) {
            new Chart(dashboardChartElement, {
                type: 'bar',
                data: {
                    labels: @json($chart['labels']),
                    datasets: [
                        {
                            label: 'Avance fisico',
                            data: @json($chart['physical']),
                            borderColor: '#009c95',
                            backgroundColor: 'rgba(0, 156, 149, 0.72)',
                            borderRadius: 6,
                        },
                        {
                            label: 'Avance financiero',
                            data: @json($chart['financial']),
                            borderColor: '#1f8fff',
                            backgroundColor: 'rgba(31, 143, 255, 0.62)',
                            borderRadius: 6,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: (value) => value + '%',
                            },
                        },
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                    },
                },
            });
        }
    </script>
@endpush
