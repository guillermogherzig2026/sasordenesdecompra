@extends('layouts.app')

@section('body')
    <x-app-shell title="Inventarios por almacen">
        <section class="panel">
            <div>
                <h2>Actualizar inventario</h2>
                <p class="fine-print">Administra existencias por almacen. Para OS se usa el almacen central San Francisco 516.</p>
            </div>
            <form class="stack" method="POST" action="{{ route('inventory.stock.store') }}">
                @csrf
                <div class="grid-4">
                    <label>Insumo
                        <select name="warehouse_catalog_item_id" required>
                            <option value="">Selecciona...</option>
                            @foreach ($catalogItems as $item)
                                <option value="{{ $item->id }}">{{ $item->name }} @if($item->sku) ({{ $item->sku }}) @endif</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Almacen<input name="warehouse" value="{{ old('warehouse', \App\Models\WarehouseInventoryItem::CENTRAL_WAREHOUSE) }}" required></label>
                    <label>Existencia<input name="quantity" type="number" min="0" step="0.01" required></label>
                    <label>Minimo<input name="minimum_quantity" type="number" min="0" step="0.01" value="0"></label>
                </div>
                <div class="form-actions"><button class="button primary" type="submit">Guardar inventario</button></div>
            </form>
        </section>

        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Inventarios</h2>
                    @if (($warehouseFilter ?? '') !== '')
                        <p class="fine-print">Existencias filtradas por almacen: <strong>{{ $warehouseFilter }}</strong></p>
                    @endif
                </div>
                <form class="toolbar" method="GET" action="{{ route('inventory.stock.index') }}">
                    @if (($warehouseFilter ?? '') !== '')
                        <input name="warehouse" type="hidden" value="{{ $warehouseFilter }}">
                    @endif
                    <input name="q" value="{{ $query }}" placeholder="Buscar insumo...">
                    @if (($warehouseFilter ?? '') !== '')
                        <a class="button ghost small" href="{{ route('inventory.stock.index') }}">Ver todos</a>
                    @endif
                </form>
            </div>
            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Almacen</th>
                            <th>SKU</th>
                            <th>Insumo</th>
                            <th>Existencia</th>
                            <th>Minimo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td>{{ $item->warehouse }}</td>
                                <td>{{ $item->catalogItem->sku ?: '—' }}</td>
                                <td>{{ $item->catalogItem->name }}</td>
                                <td>{{ number_format((float) $item->quantity, 2) }}</td>
                                <td>{{ number_format((float) $item->minimum_quantity, 2) }}</td>
                                <td>
                                    @if ((float) $item->quantity <= (float) $item->minimum_quantity)
                                        <span class="status rejected">Bajo minimo</span>
                                    @else
                                        <span class="status paid">Disponible</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">No hay inventario registrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </x-app-shell>
@endsection
