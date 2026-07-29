@extends('layouts.app')

@section('body')
    <x-app-shell title="Giros de proveeduria">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Catalogo de giros</h2>
                    <p class="fine-print">Administra las opciones disponibles para el alta y edicion de proveedores.</p>
                </div>
            </div>

            <form class="stack" method="POST" action="{{ route('superadmin.provider-lines.store') }}">
                @csrf
                <div class="grid-2">
                    <label>
                        Nuevo giro de proveeduria
                        <input name="name" value="{{ old('name') }}" placeholder="Ej: Medicamentos" required>
                    </label>
                    <div class="form-actions align-end">
                        <button class="button primary" type="submit">Agregar giro</button>
                    </div>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Giros registrados</h2>
                    <p class="fine-print">Los giros relacionados a proveedores no se eliminan para conservar el historial.</p>
                </div>
                <form class="toolbar" method="GET" action="{{ route('superadmin.provider-lines.index') }}">
                    <input name="q" value="{{ $query }}" placeholder="Buscar giro">
                    <button class="button ghost" type="submit">Buscar</button>
                </form>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Giro</th>
                            <th>Proveedores</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lines as $line)
                            <tr>
                                <td><strong>{{ $line->name }}</strong></td>
                                <td>{{ $line->providers_count }}</td>
                                <td>
                                    <span class="status {{ $line->active ? 'approved' : 'canceled' }}">{{ $line->active ? 'Activo' : 'Inactivo' }}</span>
                                </td>
                                <td class="row-actions">
                                    <button class="button ghost small editor-toggle" type="button" data-target="line-editor-{{ $line->id }}">Editar</button>
                                    <form class="inline-form" method="POST" action="{{ route('superadmin.provider-lines.destroy', $line) }}" onsubmit="return confirm('Estas seguro que quieres eliminar {{ $line->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button danger small" type="submit" @disabled($line->providers_count > 0)>Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="editor-row" id="line-editor-{{ $line->id }}" hidden>
                                <td colspan="4">
                                    <form class="stack" method="POST" action="{{ route('superadmin.provider-lines.update', $line) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid-3">
                                            <label>Giro<input name="name" value="{{ old('name', $line->name) }}" required></label>
                                            <label class="checkbox-inline">
                                                <input name="active" type="checkbox" value="1" @checked($line->active)>
                                                Activo
                                            </label>
                                            <div class="form-actions align-end">
                                                <button class="button primary small" type="submit">Guardar cambios</button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">No hay giros registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <script>
            document.querySelectorAll('.editor-toggle').forEach((button) => {
                button.addEventListener('click', () => {
                    const row = document.getElementById(button.dataset.target);
                    if (!row) return;

                    const isHidden = row.hasAttribute('hidden');
                    row.toggleAttribute('hidden', !isHidden);
                    button.textContent = isHidden ? 'Cerrar' : 'Editar';
                });
            });
        </script>
    </x-app-shell>
@endsection
