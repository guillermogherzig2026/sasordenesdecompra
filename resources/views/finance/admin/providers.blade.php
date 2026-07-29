@extends('layouts.app')

@section('body')
    <x-app-shell title="Alta de proveedores">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Alta de proveedores</h2>
                    <p class="fine-print">Vista consolidada de todos los proveedores dados de alta por compradores.</p>
                </div>
                <form class="toolbar" method="GET" action="{{ route('finance.admin.providers') }}">
                    <input name="q" value="{{ $query }}" placeholder="Buscar proveedor...">
                    <a class="button ghost" href="{{ route('reports.download', 'providers') }}">Exportar CSV</a>
                    <a class="button ghost" href="{{ route('reports.download', 'providers-excel') }}">Exportar Excel</a>
                </form>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>Acciones</th>
                            <th>Comprador</th>
                            <th>Razon Social</th>
                            <th>RFC</th>
                            <th>Giro</th>
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
                                <td><a class="button ghost small" href="{{ route('finance.admin.providers.edit', $provider) }}">Editar</a></td>
                                <td>{{ $provider->buyer?->name ?? 'Sin comprador' }}</td>
                                <td>{{ $provider->business_name }}</td>
                                <td>{{ $provider->rfc }}</td>
                                <td>{{ $provider->business_line }}</td>
                                <td>{{ $provider->bank }}</td>
                                <td>{{ $provider->account_number }}</td>
                                <td>{{ $provider->clabe }}</td>
                                <td>{{ $provider->reference ?: 'Sin referencia' }}</td>
                                <td>{{ $provider->created_at?->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="10">Aun no hay proveedores registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </x-app-shell>
@endsection
