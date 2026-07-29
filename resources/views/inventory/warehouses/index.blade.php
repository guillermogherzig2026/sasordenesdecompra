@extends('layouts.app')

@section('body')
    <x-app-shell title="Almacenes">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Almacenes</h2>
                    <p class="fine-print">Listado de almacenes registrados en todas las empresas, mas el almacen central de suministros.</p>
                </div>
                <form class="toolbar" method="GET" action="{{ route('inventory.warehouses.index') }}">
                    <input name="q" value="{{ $query }}" placeholder="Buscar almacen, empresa o RFC...">
                </form>
                <div class="table-export-actions">
                    <a class="button primary" href="{{ route('inventory.warehouses.supply.create') }}">Agregar almacen de suministros</a>
                </div>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Empresa</th>
                            <th>Almacen</th>
                            <th>Nombre corto</th>
                            <th>RFC</th>
                            <th>Direccion / referencia</th>
                            <th>Ver movimientos</th>
                            <th>Ver existencias</th>
                            <th>Catalogo de productos</th>
                            <th>Editar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($warehouses as $warehouse)
                            <tr>
                                <td><span class="status {{ $warehouse['type'] === 'Almacen de suministros' ? 'approved' : 'pending' }}">{{ $warehouse['type'] }}</span></td>
                                <td>{{ $warehouse['company'] }}</td>
                                <td><strong>{{ $warehouse['warehouse'] }}</strong></td>
                                <td>{{ $warehouse['short_name'] }}</td>
                                <td>{{ $warehouse['rfc'] }}</td>
                                <td>{{ $warehouse['address'] }}</td>
                                <td>
                                    <a class="button ghost small" href="{{ route('inventory.warehouses.movements', $warehouse['key']) }}" target="_blank" rel="noopener">Ver movimientos</a>
                                </td>
                                <td>
                                    <a class="button ghost small" href="{{ route('inventory.stock.index', ['warehouse' => $warehouse['real_warehouse'] ?: $warehouse['warehouse']]) }}">Ver existencias</a>
                                </td>
                                <td>
                                    <a class="button ghost small" href="{{ route('inventory.warehouses.catalog', $warehouse['key']) }}">Ver catalogo</a>
                                </td>
                                <td>
                                    <a class="button ghost small" href="{{ route('inventory.warehouses.edit', $warehouse['key']) }}">Editar</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10">No hay almacenes para mostrar.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </x-app-shell>
@endsection
