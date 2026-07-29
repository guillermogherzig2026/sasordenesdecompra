@extends('layouts.app')

@section('body')
    @php
        $inventory = $item->inventories->first();
        $categoryOptions = $categoryOptions ?? collect();
        $subcategoryOptions = $subcategoryOptions ?? collect();
    @endphp

    <x-app-shell title="Editar producto">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <small class="eyebrow">Catalogo de productos</small>
                    <h2>Editar producto de {{ $warehouse['warehouse'] }}</h2>
                    <p class="fine-print">El SKU es consecutivo y no se puede modificar manualmente.</p>
                </div>
                <a class="button ghost" href="{{ route('inventory.warehouses.catalog', $warehouse['key']) }}">Regresar</a>
            </div>

            <form class="stack" method="POST" action="{{ route('inventory.warehouses.catalog.update', ['warehouseKey' => $warehouse['key'], 'warehouseCatalogItem' => $item]) }}">
                @csrf
                @method('PUT')
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
                        <input value="{{ $item->sku ?: 'Pendiente' }}" disabled>
                    </label>
                    <label>Categoria
                        <input name="category" list="catalog-categories" value="{{ old('category', $item->category) }}" data-catalog-field="category" placeholder="Selecciona o escribe categoria" required>
                        <button class="button ghost small" type="button" data-catalog-new-field="category">+ Nueva categoria</button>
                    </label>
                    <label>Subcategoria
                        <input name="subcategory" list="catalog-subcategories" value="{{ old('subcategory', $item->subcategory) }}" data-catalog-field="subcategory" placeholder="Selecciona o escribe subcategoria" required>
                        <button class="button ghost small" type="button" data-catalog-new-field="subcategory">+ Nueva subcategoria</button>
                    </label>
                    <label>Nombre del producto
                        <input name="name" value="{{ old('name', $item->name) }}" required>
                    </label>
                </div>

                <div class="grid-4">
                    <label>Unidad
                        <input name="unit" value="{{ old('unit', $item->unit) }}" required>
                    </label>
                    <label>Precio unitario
                        <input name="unit_cost" type="number" min="0" step="0.01" value="{{ old('unit_cost', $item->unit_cost) }}">
                    </label>
                    <label>Existencia en {{ $warehouse['warehouse'] }}
                        <input name="quantity" type="number" min="0" step="0.01" value="{{ old('quantity', $inventory?->quantity ?? 0) }}" required>
                    </label>
                    <label>Minimo
                        <input name="minimum_quantity" type="number" min="0" step="0.01" value="{{ old('minimum_quantity', $inventory?->minimum_quantity ?? 0) }}">
                    </label>
                </div>

                <input name="authorized" type="hidden" value="0">
                <label class="checkbox-inline">
                    <input name="authorized" type="checkbox" value="1" @checked(old('authorized', $item->authorized))>
                    Autorizado para OS
                </label>

                <label>Descripcion
                    <textarea name="description" rows="3">{{ old('description', $item->description) }}</textarea>
                </label>

                <div class="form-actions">
                    <a class="button ghost" href="{{ route('inventory.warehouses.catalog', $warehouse['key']) }}">Cancelar</a>
                    <button class="button primary" type="submit">Guardar cambios</button>
                </div>
            </form>
        </section>
    </x-app-shell>

    <script>
        document.querySelectorAll('[data-catalog-new-field]').forEach((button) => {
            button.addEventListener('click', () => {
                const field = document.querySelector(`[data-catalog-field="${button.dataset.catalogNewField}"]`);
                if (!field) return;

                field.value = '';
                field.focus();
            });
        });
    </script>
@endsection
