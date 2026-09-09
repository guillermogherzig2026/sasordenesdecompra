@extends('layouts.app')

@section('body')
    <x-app-shell title="Centro de agentes">
        <section class="panel">
            <div class="panel-header">
                <h2>Agentes</h2>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Agente</th>
                            <th>Area</th>
                            <th>Estado</th>
                            <th>Ultima actividad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="empty-state" colspan="5">No hay agentes registrados.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </x-app-shell>
@endsection
