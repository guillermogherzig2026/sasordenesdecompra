@extends('layouts.app')

@section('title', 'Panel general de obras')
@section('page-title', 'Panel general de obras')
@section('page-subtitle', 'Visualiza y administra todas las obras registradas')

@section('content')
    @php
        $money = fn ($value) => '$'.number_format((float) $value, 2);
        $activeGroup = request('state_group');
    @endphp

    <div class="d-flex justify-content-between align-items-center gap-2 mb-3 flex-wrap">
        <ul class="nav nav-pills gap-2">
            @foreach($counts as $status => $count)
                @php
                    $params = $status === 'Todas'
                        ? request()->except('state_group')
                        : array_merge(request()->except('state_group'), ['state_group' => $status]);
                    $isActive = ($status === 'Todas' && ! $activeGroup) || $activeGroup === $status;
                @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $isActive ? 'active' : '' }}" href="{{ route('obras.index', $params) }}">
                        {{ $status }}
                    </a>
                </li>
            @endforeach
        </ul>

        @if($canCreate)
            <a class="btn btn-aqua" href="{{ route('obras.create') }}">
                <i data-lucide="plus"></i>
                Nueva obra
            </a>
        @endif
    </div>

    @if($projects->isEmpty())
        <div class="empty-state">No hay obras con los filtros seleccionados.</div>
    @else
        <section class="project-grid">
            @foreach($projects as $project)
                @php
                    $visibleStatus = 'En Proceso';
                    $visibleStatusClass = 'process';

                    if ($project->status === 'Terminada' || (float) $project->physical_progress >= 100) {
                        $visibleStatus = 'Terminada';
                        $visibleStatusClass = 'done';
                    } elseif ((float) $project->physical_progress <= 0) {
                        $visibleStatus = 'Por iniciar';
                        $visibleStatusClass = 'start';
                    }
                @endphp
                <a class="project-card {{ $loop->first ? 'featured' : '' }}" href="{{ route('obras.show', $project) }}">
                    <span class="project-state-badge project-state-{{ $visibleStatusClass }}">{{ $visibleStatus }}</span>
                    <div class="project-card-head">
                        <img class="project-photo" src="{{ $project->photo_path ?: asset('images/projects/residencial-los-pinos.png') }}" alt="{{ $project->name }}">
                        <div>
                            <div class="project-key">{{ $project->project_key }}</div>
                            <div class="project-name">{{ $project->name }}</div>
                            <div class="text-muted small">{{ $project->client?->name ?? 'Sin cliente' }}</div>
                            <span class="badge-soft badge-{{ $project->modality === 'Precio alzado' ? 'success' : 'primary' }} mt-2">{{ $project->modality }}</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 small">
                        <span class="status-{{ $project->statusColor() }}">
                            <span class="status-dot"></span>{{ $visibleStatus }}
                        </span>
                        <strong>{{ number_format((float) $project->physical_progress, 1) }}%</strong>
                    </div>
                    <div class="progress mt-2">
                        <div class="progress-bar bg-{{ $project->statusColor() }}" style="width: {{ min((float) $project->physical_progress, 100) }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-end mt-3">
                        <span class="small text-muted">{{ $project->location }}</span>
                        <strong>{{ $money($project->contracted_value) }}</strong>
                    </div>
                </a>
            @endforeach
        </section>
    @endif
@endsection
