@extends('layouts.app')

@section('body')
    @php
        $warehouse = $warehouse ?? [
            'key' => 'central',
            'warehouse' => 'Almacen central',
            'company' => 'Prodifem',
            'type' => 'Almacen de suministros',
            'address' => 'San Francisco 516',
            'is_central' => true,
        ];
        $isSupplyCatalog = $isSupplyCatalog ?? true;
        $companyCatalogRows = $companyCatalogRows ?? collect();
        $categoryOptions = $categoryOptions ?? collect();
        $subcategoryOptions = $subcategoryOptions ?? collect();
        $catalogSearchQuery = $catalogSearchQuery ?? '';
        $catalogSearchSuggestions = $catalogSearchSuggestions ?? collect();
    @endphp

    <x-app-shell title="Catalogo de productos">
        @if ($canManageCatalog && $isSupplyCatalog)
            <section class="panel">
                <button class="month-toggle" type="button" data-toggle-section="catalog-create-form" aria-controls="catalog-create-form" aria-expanded="false">
                    <span class="month-toggle-sign" data-toggle-sign>+</span>
                    <span class="month-toggle-copy">
                        <h2>Agregar producto a {{ $warehouse['warehouse'] }}</h2>
                        <small class="fine-print">Presiona + para capturar un producto nuevo en este almacen.</small>
                    </span>
                </button>

                <form id="catalog-create-form" class="stack" method="POST" action="{{ route('inventory.warehouses.catalog.store', $warehouse['key']) }}" hidden>
                    @csrf
                    <datalist id="catalog-categories">
                        @foreach ($categoryOptions as $category)
                            <option value="{{ $category }}"></option>
                        @endforeach
                    </datalist>
                    <datalist id="catalog-subcategories">
                        @foreach ($subcategoryOptions as $subcategory)
                            <option value="{{ $subcategory }}"></option>
                        @endforeach
                    </datalist>
                    <div class="grid-4">
                        <label>SKU
                            <input value="Se asigna automaticamente" disabled>
                        </label>
                        <label>Categoria
                            <input name="category" list="catalog-categories" value="{{ old('category') }}" data-catalog-field="category" placeholder="Selecciona o escribe categoria" required>
                            <button class="button ghost small" type="button" data-catalog-new-field="category">+ Nueva categoria</button>
                        </label>
                        <label>Subcategoria
                            <input name="subcategory" list="catalog-subcategories" value="{{ old('subcategory') }}" data-catalog-field="subcategory" placeholder="Selecciona o escribe subcategoria" required>
                            <button class="button ghost small" type="button" data-catalog-new-field="subcategory">+ Nueva subcategoria</button>
                        </label>
                        <label>Nombre del producto<input name="name" value="{{ old('name') }}" required></label>
                    </div>
                    <div class="grid-4">
                        <label>Unidad<input name="unit" value="{{ old('unit', 'pieza') }}" required></label>
                        <label>Precio unitario<input name="unit_cost" type="number" min="0" step="0.01" value="{{ old('unit_cost', 0) }}"></label>
                        <label>Existencia inicial<input name="quantity" type="number" min="0" step="0.01" value="{{ old('quantity', 0) }}"></label>
                        <label>Minimo<input name="minimum_quantity" type="number" min="0" step="0.01" value="{{ old('minimum_quantity', 0) }}"></label>
                    </div>
                    <label class="checkbox-inline"><input name="authorized" type="checkbox" value="1" checked> Autorizado para OS</label>
                    <label>Descripcion<textarea name="description" rows="2">{{ old('description') }}</textarea></label>
                    <div class="form-actions"><button class="button primary" type="submit">Agregar producto</button></div>
                </form>
            </section>
        @endif

        <section class="panel catalog-products-panel">
            <div class="panel-header catalog-header">
                <div class="catalog-header-copy">
                    <p class="eyebrow">{{ $warehouse['type'] }}</p>
                    <h2>Catalogo de productos - {{ $warehouse['warehouse'] }}</h2>
                    <p class="fine-print">
                        {{ $warehouse['company'] }} · {{ $warehouse['address'] }}
                        @if (! $isSupplyCatalog)
                            · Vista de productos recibidos por este almacen.
                        @endif
                    </p>
                </div>
                <div class="toolbar catalog-header-actions">
                    <a class="button ghost" href="{{ route('inventory.warehouses.index') }}">Regresar</a>
                    <a class="button ghost" href="{{ route('inventory.warehouses.movements', $warehouse['key']) }}" target="_blank" rel="noopener">Ver movimientos</a>
                    <a class="button ghost" href="{{ route('inventory.warehouses.edit', $warehouse['key']) }}">Editar almacen</a>
                </div>
            </div>

            <form class="catalog-search-form" method="GET" action="{{ route('inventory.warehouses.catalog', $warehouse['key']) }}">
                <label class="catalog-search-label">Buscador general
                    <span class="catalog-search-box">
                        <input name="q" value="{{ $catalogSearchQuery }}" placeholder="Buscar producto, SKU, categoria o subcategoria..." autocomplete="off" data-catalog-search-input aria-autocomplete="list" aria-expanded="false" aria-controls="catalog-search-suggestions">
                        <span id="catalog-search-suggestions" class="catalog-search-suggestions" data-catalog-search-suggestions hidden></span>
                    </span>
                </label>
                <button class="button primary" type="submit">Buscar</button>
                @if ($catalogSearchQuery !== '')
                    <a class="button ghost" href="{{ route('inventory.warehouses.catalog', $warehouse['key']) }}">Limpiar</a>
                @endif
            </form>
            <script type="application/json" id="catalog-search-suggestions-data">@json($catalogSearchSuggestions->values())</script>

            <div class="table-scroll catalog-table-scroll">
                <table class="catalog-products-table">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Categoria</th>
                            <th>Subcategoria</th>
                            <th>Producto</th>
                            <th>Unidad</th>
                            <th>Precio unitario</th>
                            <th>{{ $isSupplyCatalog ? 'Existencia' : 'Recibido' }}</th>
                            <th>Minimo</th>
                            <th>Estado</th>
                            @if ($canManageCatalog && $isSupplyCatalog)
                                <th>Editar</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if ($isSupplyCatalog)
                            @forelse ($items as $item)
                                @php $inventory = $item->inventories->first(); @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $item->sku ?: 'Pendiente' }}</strong>
                                        @if ($canManageCatalog)
                                            <small class="fine-print">Automatico</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->category ?: 'Sin categoria' }}</td>
                                    <td>{{ $item->subcategory ?: 'Sin subcategoria' }}</td>
                                    <td>
                                        <strong>{{ $item->name }}</strong>
                                        <small class="fine-print">{{ $item->description ?: 'Sin descripcion' }}</small>
                                    </td>
                                    <td>{{ $item->unit }}</td>
                                    <td>${{ number_format((float) $item->unit_cost, 2) }}</td>
                                    <td>{{ number_format((float) ($inventory?->quantity ?? 0), 2) }}</td>
                                    <td>{{ number_format((float) ($inventory?->minimum_quantity ?? 0), 2) }}</td>
                                    <td>
                                        <span class="status {{ $item->authorized ? 'approved' : 'canceled' }}">{{ $item->authorized ? 'Autorizado' : 'Bloqueado' }}</span>
                                    </td>
                                    @if ($canManageCatalog)
                                        <td>
                                            <a class="button ghost small" href="{{ route('inventory.warehouses.catalog.edit', ['warehouseKey' => $warehouse['key'], 'warehouseCatalogItem' => $item]) }}">Editar</a>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr><td colspan="{{ $canManageCatalog ? 10 : 9 }}">Aun no hay productos en el catalogo de este almacen.</td></tr>
                            @endforelse
                        @else
                            @forelse ($companyCatalogRows as $row)
                                <tr>
                                    <td>{{ $row['sku'] }}</td>
                                    <td>{{ $row['category'] }}</td>
                                    <td>{{ $row['subcategory'] }}</td>
                                    <td>
                                        <strong>{{ $row['name'] }}</strong>
                                        <small class="fine-print">{{ $row['description'] }} · Ultima recepcion: {{ $row['updated_at'] }}</small>
                                    </td>
                                    <td>{{ $row['unit'] }}</td>
                                    <td>${{ number_format((float) $row['unit_cost'], 2) }}</td>
                                    <td>{{ number_format((float) $row['quantity'], 2) }}</td>
                                    <td>{{ $row['minimum_quantity'] === null ? '—' : number_format((float) $row['minimum_quantity'], 2) }}</td>
                                    <td><span class="status approved">{{ $row['status'] }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="9">Aun no hay productos recibidos en este almacen.</td></tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="catalog-bottom-scroll-shell" data-catalog-bottom-scroll-shell hidden>
                <div class="catalog-bottom-scroll-rail" data-catalog-bottom-scroll aria-label="Movimiento lateral del catalogo">
                    <div class="catalog-bottom-scroll-track" data-catalog-bottom-scroll-track></div>
                </div>
            </div>
        </section>
    </x-app-shell>

    <style>
        .catalog-products-panel {
            align-content: start;
        }

        .catalog-header {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto auto;
            align-items: start;
            column-gap: 10px;
        }

        .catalog-header-copy {
            min-width: 0;
        }

        .catalog-header-actions {
            grid-column: 3;
            grid-row: 1;
            margin-left: auto;
            justify-content: flex-end;
        }

        .catalog-header > .table-export-actions {
            grid-column: 2;
            grid-row: 1;
            align-self: start;
            margin-left: auto;
            justify-content: flex-end;
        }

        .catalog-search-form {
            width: min(680px, 100%);
            display: grid;
            grid-template-columns: minmax(280px, 1fr) auto auto;
            align-items: end;
            gap: 10px;
        }

        .catalog-search-label {
            color: var(--muted);
            font-size: .86rem;
            font-weight: 800;
        }

        .catalog-search-label input {
            margin-top: 6px;
            min-height: 42px;
        }

        .catalog-search-box {
            position: relative;
            display: block;
            margin-top: 6px;
        }

        .catalog-search-box input {
            margin-top: 0;
        }

        .catalog-search-suggestions {
            position: absolute;
            z-index: 2400;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            max-height: 315px;
            overflow: auto;
            display: grid;
            gap: 2px;
            padding: 8px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 18px 42px rgba(35, 48, 73, .2);
        }

        .catalog-search-suggestions[hidden] {
            display: none !important;
        }

        .catalog-search-suggestion {
            width: 100%;
            min-height: 34px;
            padding: 8px 10px;
            border: 0;
            border-radius: 7px;
            background: #fff;
            color: var(--text);
            text-align: left;
            font-weight: 760;
            cursor: pointer;
        }

        .catalog-search-suggestion:hover,
        .catalog-search-suggestion.is-active {
            background: #e5f3f7;
            color: var(--primary-strong);
        }

        .catalog-search-empty {
            padding: 10px;
            color: var(--muted);
            font-size: .88rem;
            font-weight: 700;
        }

        .catalog-table-scroll {
            min-height: calc(100vh - 360px);
            overflow-x: auto;
            overflow-y: auto;
            padding-bottom: 28px;
            scrollbar-gutter: stable both-edges;
        }

        .catalog-products-table {
            min-width: 1960px;
        }

        .catalog-bottom-scroll-shell {
            position: fixed;
            z-index: 2300;
            bottom: 0;
            padding: 4px clamp(16px, 3vw, 30px);
            border-top: 1px solid var(--line);
            background: rgba(244, 247, 251, .96);
            box-shadow: 0 -8px 18px rgba(35, 48, 73, .08);
        }

        .catalog-bottom-scroll-rail {
            width: 100%;
            height: 18px;
            overflow-x: auto;
            overflow-y: hidden;
        }

        .catalog-bottom-scroll-track {
            height: 1px;
        }

        .catalog-bottom-scroll-rail::-webkit-scrollbar {
            height: 12px;
        }

        .catalog-bottom-scroll-rail::-webkit-scrollbar-track {
            background: #edf3f8;
            border-radius: 999px;
        }

        .catalog-bottom-scroll-rail::-webkit-scrollbar-thumb {
            background: #8fa7bb;
            border-radius: 999px;
            border: 2px solid #edf3f8;
        }

        .catalog-bottom-scroll-rail::-webkit-scrollbar-thumb:hover {
            background: #6f879b;
        }

        .catalog-table-scroll::-webkit-scrollbar {
            height: 12px;
            width: 10px;
        }

        .catalog-table-scroll::-webkit-scrollbar-track {
            background: #edf3f8;
            border-radius: 999px;
        }

        .catalog-table-scroll::-webkit-scrollbar-thumb {
            background: #9eb2c3;
            border-radius: 999px;
            border: 2px solid #edf3f8;
        }

        .catalog-table-scroll::-webkit-scrollbar-thumb:hover {
            background: #7890a3;
        }
    </style>

    <script>
        document.querySelectorAll('[data-toggle-section]').forEach((button) => {
            const target = document.getElementById(button.dataset.toggleSection);
            const sign = button.querySelector('[data-toggle-sign]');
            if (!target || !sign) return;

            const refresh = () => {
                const isOpen = !target.hidden;
                sign.textContent = isOpen ? '-' : '+';
                button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            };

            refresh();
            button.addEventListener('click', () => {
                target.hidden = !target.hidden;
                refresh();
            });
        });

        document.querySelectorAll('[data-catalog-new-field]').forEach((button) => {
            button.addEventListener('click', () => {
                const field = document.querySelector(`[data-catalog-field="${button.dataset.catalogNewField}"]`);
                if (!field) return;

                field.value = '';
                field.focus();
            });
        });

        (() => {
            const input = document.querySelector('[data-catalog-search-input]');
            const suggestionsBox = document.querySelector('[data-catalog-search-suggestions]');
            const data = document.getElementById('catalog-search-suggestions-data');
            const form = input?.closest('form');
            if (!input || !suggestionsBox || !data || !form) return;

            let suggestions = [];
            try {
                suggestions = JSON.parse(data.textContent || '[]');
            } catch (error) {
                suggestions = [];
            }

            const normalize = (value) => String(value || '')
                .toLocaleLowerCase('es')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim();

            const closeSuggestions = () => {
                suggestionsBox.hidden = true;
                input.setAttribute('aria-expanded', 'false');
            };

            const openSuggestions = () => {
                suggestionsBox.hidden = false;
                input.setAttribute('aria-expanded', 'true');
            };

            const renderSuggestions = () => {
                const query = normalize(input.value);
                suggestionsBox.innerHTML = '';

                if (!query) {
                    closeSuggestions();
                    return;
                }

                const matches = suggestions
                    .filter((suggestion) => normalize(suggestion).includes(query))
                    .slice(0, 16);

                if (!matches.length) {
                    const empty = document.createElement('span');
                    empty.className = 'catalog-search-empty';
                    empty.textContent = 'No hay productos coincidentes.';
                    suggestionsBox.appendChild(empty);
                    openSuggestions();
                    return;
                }

                matches.forEach((suggestion) => {
                    const option = document.createElement('button');
                    option.type = 'button';
                    option.className = 'catalog-search-suggestion';
                    option.textContent = suggestion;
                    option.addEventListener('click', () => {
                        input.value = suggestion;
                        closeSuggestions();
                        form.requestSubmit();
                    });
                    suggestionsBox.appendChild(option);
                });

                openSuggestions();
            };

            input.addEventListener('input', renderSuggestions);
            input.addEventListener('focus', renderSuggestions);
            input.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closeSuggestions();
            });

            document.addEventListener('click', (event) => {
                if (!form.contains(event.target)) closeSuggestions();
            });
        })();

        (() => {
            const tableScroll = document.querySelector('.catalog-table-scroll');
            const shell = document.querySelector('[data-catalog-bottom-scroll-shell]');
            const rail = document.querySelector('[data-catalog-bottom-scroll]');
            const track = document.querySelector('[data-catalog-bottom-scroll-track]');

            if (!tableScroll || !shell || !rail || !track) return;

            let syncing = false;

            const positionShell = () => {
                const contentShell = document.querySelector('.content-shell')?.getBoundingClientRect();
                if (!contentShell) return;

                shell.style.left = `${Math.max(0, contentShell.left)}px`;
                shell.style.right = `${Math.max(0, window.innerWidth - contentShell.right)}px`;
            };

            const refresh = () => {
                positionShell();
                track.style.width = `${tableScroll.scrollWidth}px`;
                shell.hidden = tableScroll.scrollWidth <= tableScroll.clientWidth + 2;
                rail.scrollLeft = tableScroll.scrollLeft;
            };

            tableScroll.addEventListener('scroll', () => {
                if (syncing) return;
                syncing = true;
                rail.scrollLeft = tableScroll.scrollLeft;
                syncing = false;
            });

            rail.addEventListener('scroll', () => {
                if (syncing) return;
                syncing = true;
                tableScroll.scrollLeft = rail.scrollLeft;
                syncing = false;
            });

            window.addEventListener('resize', refresh);
            window.addEventListener('load', refresh);
            refresh();
            setTimeout(refresh, 250);

            if ('ResizeObserver' in window) {
                const observer = new ResizeObserver(refresh);
                observer.observe(tableScroll);
                observer.observe(tableScroll.querySelector('table'));
            }
        })();
    </script>
@endsection
