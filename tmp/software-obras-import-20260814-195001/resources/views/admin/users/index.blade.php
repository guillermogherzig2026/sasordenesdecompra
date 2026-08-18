@extends('layouts.app')

@section('title', 'Usuarios y permisos')
@section('page-title', 'Usuarios y permisos')
@section('page-subtitle', 'Asignacion de consulta y edicion por obra')

@section('content')
    <div class="table-card excel-wrap mb-3">
        <table class="table excel-table align-middle">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Obras con acceso</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role?->name }}</td>
                        <td><span class="badge-soft badge-success">{{ $user->status }}</span></td>
                        <td>{{ $user->projects->count() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="accordion" id="userAccessAccordion">
        @foreach($users as $user)
            @php
                $accessByProject = $user->projects->keyBy('id');
            @endphp
            <div class="accordion-item panel-card mb-2 p-0">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#user-{{ $user->id }}">
                        {{ $user->name }} - {{ $user->role?->name }}
                    </button>
                </h2>
                <div id="user-{{ $user->id }}" class="accordion-collapse collapse" data-bs-parent="#userAccessAccordion">
                    <form class="accordion-body" method="POST" action="{{ route('users.access.update', $user) }}">
                        @csrf
                        @method('PUT')
                        <div class="excel-wrap">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Obra</th>
                                        <th>Cliente</th>
                                        <th class="text-center">Consultar</th>
                                        <th class="text-center">Editar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($projects as $project)
                                        @php
                                            $pivot = $accessByProject->get($project->id)?->pivot;
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $project->project_key }}</strong> {{ $project->name }}</td>
                                            <td>{{ $project->client?->name }}</td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="checkbox" name="projects[{{ $project->id }}][can_view]" value="1" @checked($pivot?->can_view)>
                                            </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="checkbox" name="projects[{{ $project->id }}][can_edit]" value="1" @checked($pivot?->can_edit)>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button class="btn btn-aqua" type="submit">
                                <i data-lucide="save"></i>
                                Guardar permisos
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection
