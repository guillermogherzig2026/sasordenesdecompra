@extends('layouts.app')

@section('body')
    <x-app-shell title="Proveedores">
        <details class="panel provider-catalog-panel">
            <summary class="provider-catalog-summary">
                <div>
                    <h2>Catalogo de proveedores</h2>
                    <p class="fine-print">Consulta todos los proveedores dados de alta en el sistema.</p>
                </div>
                <span class="button ghost provider-catalog-toggle" aria-hidden="true"></span>
            </summary>

            <div class="table-scroll provider-catalog-list">
                <table>
                    <thead>
                        <tr>
                            <th>Razon social</th>
                            <th>RFC</th>
                            <th>Categoria</th>
                            <th>Subcategoria</th>
                            <th>Comprador</th>
                            <th>Banco</th>
                            <th>Cuenta</th>
                            <th>CLABE</th>
                            <th>Referencia</th>
                            <th>Fecha alta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($providers as $provider)
                            <tr>
                                <td><strong>{{ $provider->business_name }}</strong></td>
                                <td>{{ $provider->rfc }}</td>
                                <td>{{ $provider->businessLine?->name ?? $provider->business_line }}</td>
                                <td>{{ $provider->businessSubcategory?->name ?? $provider->provider_business_subcategory ?? 'Sin subcategoria' }}</td>
                                <td>{{ $provider->buyer?->name ?? 'Sin comprador' }}</td>
                                <td>{{ $provider->bank }}</td>
                                <td>{{ $provider->account_number }}</td>
                                <td>{{ $provider->clabe }}</td>
                                <td>{{ $provider->reference ?: 'Sin referencia' }}</td>
                                <td>{{ $provider->created_at?->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="empty-state">No hay proveedores registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </details>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Categorias de proveedores</h2>
                    <a class="button primary small" href="{{ route('superadmin.provider-lines.manage') }}">Administrar categorias</a>
                </div>
            </div>

            <div class="table-scroll" id="provider-category-management">
                <table>
                    <thead>
                        <tr>
                            <th>Categoria</th>
                            <th>Proveedores</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lines as $line)
                            <tr class="provider-line-row">
                                <td>
                                    <span class="provider-category-cell">
                                        <button class="button ghost small provider-line-toggle" type="button" data-provider-line-toggle="{{ $line->id }}" aria-expanded="false">+</button>
                                        <strong>{{ $line->name }}</strong>
                                    </span>
                                </td>
                                <td>
                                    <button class="button ghost small" type="button" data-supply-detail-open="providers-line-{{ $line->id }}">
                                        Ver proveedores ({{ $line->providers_count }})
                                    </button>
                                </td>
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
                            @foreach ($line->subcategories as $subcategory)
                                <tr class="provider-subcategory-row" data-parent-line="{{ $line->id }}" hidden>
                                    <td>
                                        <span class="provider-subcategory-cell">{{ $subcategory->name }}</span>
                                    </td>
                                    <td>
                                        <button class="button ghost small" type="button" data-supply-detail-open="providers-subcategory-{{ $subcategory->id }}">
                                            Ver proveedores ({{ $subcategory->providers_count }})
                                        </button>
                                    </td>
                                    <td>
                                        <span class="status {{ $subcategory->active ? 'approved' : 'canceled' }}">{{ $subcategory->active ? 'Activo' : 'Inactivo' }}</span>
                                    </td>
                                    <td class="row-actions">
                                        <button class="button ghost small editor-toggle" type="button" data-target="subcategory-editor-{{ $subcategory->id }}">Editar</button>
                                        <form class="inline-form" method="POST" action="{{ route('superadmin.provider-lines.subcategories.destroy', $subcategory) }}" onsubmit="return confirm('Estas seguro que quieres eliminar {{ $subcategory->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="button danger small" type="submit" @disabled($subcategory->providers_count > 0)>Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr class="editor-row provider-subcategory-row" id="subcategory-editor-{{ $subcategory->id }}" data-parent-line="{{ $line->id }}" hidden>
                                    <td colspan="4">
                                        <form class="stack" method="POST" action="{{ route('superadmin.provider-lines.subcategories.update', $subcategory) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="grid-3">
                                                <label>Subcategoria<input name="name" value="{{ old('name', $subcategory->name) }}" required></label>
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
                            <tr class="provider-subcategory-row provider-subcategory-create-row" data-parent-line="{{ $line->id }}" hidden>
                                <td colspan="4">
                                    <form class="provider-subcategory-create-form" method="POST" action="{{ route('superadmin.provider-lines.subcategories.store', $line) }}">
                                        @csrf
                                        <label>
                                            Nueva subcategoria
                                            <input name="name" placeholder="Ej: Medicamentos" required>
                                        </label>
                                        <button class="button primary small" type="submit">Agregar subcategoria</button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="editor-row" id="line-editor-{{ $line->id }}" hidden>
                                <td colspan="4">
                                    <form class="stack" method="POST" action="{{ route('superadmin.provider-lines.update', $line) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid-3">
                                            <label>Categoria<input name="name" value="{{ old('name', $line->name) }}" required></label>
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
                                <td colspan="4" class="empty-state">No hay categorias registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @foreach ($lines as $line)
                <dialog class="supply-detail-dialog provider-line-dialog" id="providers-line-{{ $line->id }}" data-supply-detail-dialog>
                    <div class="supply-detail-card">
                        <button class="supply-detail-close" type="button" data-supply-detail-close aria-label="Cerrar">x</button>
                        <div>
                            <h3>Proveedores de {{ $line->name }}</h3>
                            <p class="fine-print">{{ $line->providers_count }} proveedores registrados en esta categoria.</p>
                        </div>

                        <div class="table-scroll provider-line-list">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Razon social</th>
                                        <th>RFC</th>
                                        <th>Comprador</th>
                                        <th>Banco</th>
                                        <th>Cuenta</th>
                                        <th>CLABE</th>
                                        <th>Referencia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($line->providers as $provider)
                                        <tr>
                                            <td><strong>{{ $provider->business_name }}</strong></td>
                                            <td>{{ $provider->rfc }}</td>
                                            <td>{{ $provider->buyer?->name ?? 'Sin comprador' }}</td>
                                            <td>{{ $provider->bank }}</td>
                                            <td>{{ $provider->account_number }}</td>
                                            <td>{{ $provider->clabe }}</td>
                                            <td>{{ $provider->reference ?: 'Sin referencia' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="empty-state">No hay proveedores registrados en esta categoria.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </dialog>

                @foreach ($line->subcategories as $subcategory)
                    <dialog class="supply-detail-dialog provider-line-dialog" id="providers-subcategory-{{ $subcategory->id }}" data-supply-detail-dialog>
                        <div class="supply-detail-card">
                            <button class="supply-detail-close" type="button" data-supply-detail-close aria-label="Cerrar">x</button>
                            <div>
                                <h3>Proveedores de {{ $subcategory->name }}</h3>
                                <p class="fine-print">{{ $subcategory->providers_count }} proveedores registrados en esta subcategoria.</p>
                            </div>

                            <div class="table-scroll provider-line-list">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Razon social</th>
                                            <th>RFC</th>
                                            <th>Comprador</th>
                                            <th>Banco</th>
                                            <th>Cuenta</th>
                                            <th>CLABE</th>
                                            <th>Referencia</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($subcategory->providers as $provider)
                                            <tr>
                                                <td><strong>{{ $provider->business_name }}</strong></td>
                                                <td>{{ $provider->rfc }}</td>
                                                <td>{{ $provider->buyer?->name ?? 'Sin comprador' }}</td>
                                                <td>{{ $provider->bank }}</td>
                                                <td>{{ $provider->account_number }}</td>
                                                <td>{{ $provider->clabe }}</td>
                                                <td>{{ $provider->reference ?: 'Sin referencia' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="empty-state">No hay proveedores registrados en esta subcategoria.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </dialog>
                @endforeach
            @endforeach
        </section>

        <script>
            document.querySelectorAll('[data-provider-line-toggle]').forEach((button) => {
                button.addEventListener('click', () => {
                    const parentId = button.dataset.providerLineToggle;
                    const expanded = button.getAttribute('aria-expanded') === 'true';

                    document.querySelectorAll(`[data-parent-line="${parentId}"]`).forEach((row) => {
                        row.hidden = true;
                        if (row.classList.contains('editor-row')) {
                            const editorButton = document.querySelector(`[data-target="${row.id}"]`);
                            if (editorButton) editorButton.textContent = 'Editar';
                        }
                    });

                    if (!expanded) {
                        document.querySelectorAll(`[data-parent-line="${parentId}"]:not(.editor-row)`).forEach((row) => {
                            row.hidden = false;
                        });
                    }

                    button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                    button.textContent = expanded ? '+' : '-';
                });
            });

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
