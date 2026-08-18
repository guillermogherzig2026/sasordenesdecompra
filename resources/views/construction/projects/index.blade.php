@extends('layouts.app')

@section('body')
    <x-app-shell title="Panel general de obras">
        @php
            $money = fn ($value) => '$'.number_format((float) $value, 2);
        @endphp

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Obras</h2>
                    <p class="fine-print">Visualiza y administra todas las obras registradas.</p>
                </div>
                @if ($canCreate)
                    <a class="button primary" href="{{ route('construction.projects.create') }}">Nueva obra</a>
                @endif
            </div>

        </section>

        <div class="metrics-grid">
            @foreach ($counts as $label => $count)
                <article class="metric-card">
                    <span>{{ $label }}</span>
                    <strong>{{ $count }}</strong>
                    <small>Obras</small>
                </article>
            @endforeach
        </div>

        <section class="panel">
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Clave</th>
                            <th>Obra</th>
                            <th>Cliente</th>
                            <th>Responsable</th>
                            <th>Modalidad</th>
                            <th>Estado</th>
                            <th>Avance</th>
                            <th>Contrato</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projects as $project)
                            <tr>
                                <td><strong>{{ $project->project_key }}</strong></td>
                                <td>
                                    {{ $project->name }}
                                    <br><span class="fine-print">{{ $project->location ?: 'Sin ubicacion' }}</span>
                                </td>
                                <td>{{ $project->client?->name ?? 'Sin cliente' }}</td>
                                <td>{{ $project->responsible?->name ?? 'Sin responsable' }}</td>
                                <td>{{ $project->modality }}</td>
                                <td><span class="status {{ $project->statusColor() }}">{{ $project->status }}</span></td>
                                <td>{{ number_format((float) $project->physical_progress, 2) }}%</td>
                                <td>{{ $money($project->contracted_value) }}</td>
                                <td><a class="button ghost small" href="{{ route('construction.dashboard').'#project-row-'.$project->id }}">Panel general</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="9">No hay obras con los filtros seleccionados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </x-app-shell>
@endsection
