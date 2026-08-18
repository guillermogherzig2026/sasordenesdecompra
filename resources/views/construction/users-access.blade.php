@extends('layouts.app')

@section('body')
    <x-app-shell title="Usuarios y permisos de obra">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Usuarios y permisos</h2>
                    <p class="fine-print">Asigna consulta y edicion por obra a usuarios existentes del sistema.</p>
                </div>
                <a class="button ghost" href="{{ route('construction.projects.index') }}">Ver obras</a>
            </div>
        </section>

        @foreach ($users as $user)
            @php $userAccess = $accessByUser->get($user->id, collect()); @endphp
            <form class="panel" method="POST" action="{{ route('construction.users-access.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="panel-header">
                    <div>
                        <h2>{{ $user->name }}</h2>
                        <p class="fine-print">{{ $user->email }} &middot; {{ $user->role }}</p>
                    </div>
                    <button class="button primary" type="submit">Guardar permisos</button>
                </div>

                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Obra</th>
                                <th>Cliente</th>
                                <th>Consultar</th>
                                <th>Editar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($projects as $project)
                                @php $pivot = $userAccess->get($project->id); @endphp
                                <tr>
                                    <td><strong>{{ $project->project_key }}</strong> {{ $project->name }}</td>
                                    <td>{{ $project->client?->name ?? 'Sin cliente' }}</td>
                                    <td>
                                        <input type="checkbox" name="projects[{{ $project->id }}][can_view]" value="1" @checked($pivot?->can_view)>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="projects[{{ $project->id }}][can_edit]" value="1" @checked($pivot?->can_edit)>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4">No hay obras registradas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        @endforeach
    </x-app-shell>
@endsection
