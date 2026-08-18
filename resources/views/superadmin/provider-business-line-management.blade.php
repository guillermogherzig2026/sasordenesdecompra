@extends('layouts.app')

@section('body')
    <x-app-shell title="Administrar categorias">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Gestion de categorias de proveedores</h2>
                    <p class="fine-print">Agrega categorias y administra sus subcategorias.</p>
                </div>
                <a class="button ghost small" href="{{ route('superadmin.provider-lines.index') }}">Atras</a>
            </div>

            <form class="stack" method="POST" action="{{ route('superadmin.provider-lines.store') }}">
                @csrf
                <input name="return_to" type="hidden" value="management">
                <div class="grid-2">
                    <label>
                        Nueva categoria
                        <input name="name" value="{{ old('name') }}" placeholder="Nombre de la categoria" required>
                    </label>
                    <div class="form-actions align-end">
                        <button class="button primary" type="submit">Agregar categoria</button>
                    </div>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Categorias y subcategorias</h2>
                    <p class="fine-print">Usa el boton + para mostrar y gestionar las subcategorias.</p>
                </div>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Subcategorias</th>
                            <th>Proveedores</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lines as $line)
                            <tr>
                                <td>
                                    <span class="provider-category-cell">
                                        <button
                                            class="button ghost small provider-line-toggle"
                                            type="button"
                                            data-management-line-toggle="{{ $line->id }}"
                                            aria-expanded="false"
                                            aria-label="Mostrar subcategorias de {{ $line->name }}"
                                        >+</button>
                                        <strong>{{ $line->name }}</strong>
                                    </span>
                                </td>
                                <td>{{ $line->subcategories_count }}</td>
                                <td>{{ $line->providers_count }}</td>
                                <td>
                                    <span class="status {{ $line->active ? 'approved' : 'canceled' }}">{{ $line->active ? 'Activo' : 'Inactivo' }}</span>
                                </td>
                                <td class="row-actions">
                                    <button class="button ghost small management-editor-toggle" type="button" data-target="management-line-editor-{{ $line->id }}">Editar</button>
                                    <form class="inline-form" method="POST" action="{{ route('superadmin.provider-lines.destroy', $line) }}" onsubmit="return confirm('Estas seguro que quieres eliminar {{ $line->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <input name="return_to" type="hidden" value="management">
                                        <button class="button danger small" type="submit" @disabled($line->providers_count > 0)>Eliminar</button>
                                    </form>
                                </td>
                            </tr>

                            <tr class="editor-row" id="management-line-editor-{{ $line->id }}" hidden>
                                <td colspan="5">
                                    <form class="stack" method="POST" action="{{ route('superadmin.provider-lines.update', $line) }}">
                                        @csrf
                                        @method('PUT')
                                        <input name="return_to" type="hidden" value="management">
                                        <div class="grid-3">
                                            <label>Categoria<input name="name" value="{{ $line->name }}" required></label>
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

                            @foreach ($line->subcategories as $subcategory)
                                <tr data-parent-line="{{ $line->id }}" data-line-visible hidden>
                                    <td><span class="provider-subcategory-cell">{{ $subcategory->name }}</span></td>
                                    <td>Subcategoria</td>
                                    <td>{{ $subcategory->providers_count }}</td>
                                    <td>
                                        <span class="status {{ $subcategory->active ? 'approved' : 'canceled' }}">{{ $subcategory->active ? 'Activo' : 'Inactivo' }}</span>
                                    </td>
                                    <td class="row-actions">
                                        <button class="button ghost small management-editor-toggle" type="button" data-target="management-subcategory-editor-{{ $subcategory->id }}">Editar</button>
                                        <form class="inline-form" method="POST" action="{{ route('superadmin.provider-lines.subcategories.destroy', $subcategory) }}" onsubmit="return confirm('Estas seguro que quieres eliminar {{ $subcategory->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <input name="return_to" type="hidden" value="management">
                                            <button class="button danger small" type="submit" @disabled($subcategory->providers_count > 0)>Eliminar</button>
                                        </form>
                                    </td>
                                </tr>

                                <tr class="editor-row" id="management-subcategory-editor-{{ $subcategory->id }}" data-parent-line="{{ $line->id }}" hidden>
                                    <td colspan="5">
                                        <form class="stack" method="POST" action="{{ route('superadmin.provider-lines.subcategories.update', $subcategory) }}">
                                            @csrf
                                            @method('PUT')
                                            <input name="return_to" type="hidden" value="management">
                                            <div class="grid-3">
                                                <label>Subcategoria<input name="name" value="{{ $subcategory->name }}" required></label>
                                                <label class="checkbox-inline">
                                                    <input name="active" type="checkbox" value="1" @checked($subcategory->active)>
                                                    Activo
                                                </label>
                                                <div class="form-actions align-end">
                                                    <button class="button primary small" type="submit">Guardar cambios</button>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach

                            <tr data-parent-line="{{ $line->id }}" data-line-visible hidden>
                                <td colspan="5">
                                    <form class="provider-subcategory-create-form" method="POST" action="{{ route('superadmin.provider-lines.subcategories.store', $line) }}">
                                        @csrf
                                        <input name="return_to" type="hidden" value="management">
                                        <label>
                                            Nueva subcategoria
                                            <input name="name" placeholder="Nombre de la subcategoria" required>
                                        </label>
                                        <button class="button primary small" type="submit">Agregar subcategoria</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">No hay categorias registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <script>
            document.querySelectorAll('[data-management-line-toggle]').forEach((button) => {
                button.addEventListener('click', () => {
                    const parentId = button.dataset.managementLineToggle;
                    const expanded = button.getAttribute('aria-expanded') === 'true';

                    document.querySelectorAll(`[data-parent-line="${parentId}"][data-line-visible]`).forEach((row) => {
                        row.hidden = expanded;
                    });

                    if (expanded) {
                        document.querySelectorAll(`[data-parent-line="${parentId}"].editor-row`).forEach((row) => {
                            row.hidden = true;
                            document.querySelector(`[data-target="${row.id}"]`)?.replaceChildren('Editar');
                        });
                    }

                    button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                    button.textContent = expanded ? '+' : '-';
                });
            });

            document.querySelectorAll('.management-editor-toggle').forEach((button) => {
                button.addEventListener('click', () => {
                    const row = document.getElementById(button.dataset.target);
                    if (!row) return;

                    row.hidden = !row.hidden;
                    button.textContent = row.hidden ? 'Editar' : 'Cerrar';
                });
            });
        </script>
    </x-app-shell>
@endsection
