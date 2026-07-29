@extends('layouts.app')

@section('body')
    <x-app-shell title="Movimientos de almacen">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">{{ $warehouse['type'] }}</p>
                    <h2>{{ $warehouse['warehouse'] }}</h2>
                    <p class="fine-print">{{ $warehouse['company'] }} · {{ $warehouse['address'] }}</p>
                </div>
                <div class="table-export-actions">
                    <a class="button ghost" href="{{ route('inventory.warehouses.index') }}">Regresar</a>
                </div>
            </div>

            <form class="panel" method="GET" action="{{ route('inventory.warehouses.movements', $warehouse['key']) }}">
                <div class="grid-4">
                    <label>Tipo de movimiento
                        <select name="type">
                            <option value="">Todos</option>
                            @foreach ($types as $type)
                                <option value="{{ $type }}" @selected($filters['type'] === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Fecha desde<input name="date_from" type="date" value="{{ $filters['date_from'] }}"></label>
                    <label>Fecha hasta<input name="date_to" type="date" value="{{ $filters['date_to'] }}"></label>
                    <label>Orden OS / OC<input name="order" value="{{ $filters['order'] }}" placeholder="Ej. OS-1001 u OC-1001"></label>
                </div>
                <div class="grid-2">
                    <label>Buscar informacion relacionada
                        <input name="q" value="{{ $filters['q'] }}" placeholder="Producto, empresa, remision, proveedor, usuario...">
                    </label>
                    <div class="form-actions" style="align-self:end;justify-content:flex-start">
                        <button class="button primary" type="submit">Filtrar</button>
                        <a class="button ghost" href="{{ route('inventory.warehouses.movements', $warehouse['key']) }}">Limpiar</a>
                    </div>
                </div>
            </form>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Orden</th>
                            <th>Documento</th>
                            <th>Empresa</th>
                            <th>Almacen</th>
                            <th>Relacionado</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Precio unitario</th>
                            <th>Importe</th>
                            <th>Existencia</th>
                            <th>Estado</th>
                            <th>Informacion relacionada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movements as $movement)
                            <tr>
                                <td>
                                    @php
                                        $class = match ($movement['type']) {
                                            'Entrada', 'Entrada / Acta de recepcion' => 'approved',
                                            'Salida' => 'rejected',
                                            'Existencia' => 'pending',
                                            default => 'pending',
                                        };
                                    @endphp
                                    <span class="status {{ $class }}">{{ $movement['type'] }}</span>
                                </td>
                                <td>{{ $movement['date'] }}</td>
                                <td><strong>{{ $movement['order'] }}</strong></td>
                                <td>{{ $movement['document'] }}</td>
                                <td>{{ $movement['company'] }}</td>
                                <td>{{ $movement['warehouse'] }}</td>
                                <td>{{ $movement['related'] }}</td>
                                <td>{{ $movement['product'] }}</td>
                                <td>{{ $movement['quantity'] }}</td>
                                <td>{{ $movement['unit'] }}</td>
                                <td>{{ $movement['unit_price'] }}</td>
                                <td>{{ $movement['amount'] }}</td>
                                <td>{{ $movement['stock'] }}</td>
                                <td>{{ $movement['status'] }}</td>
                                <td>{{ $movement['details'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="15">No hay movimientos con los filtros seleccionados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </x-app-shell>
@endsection
