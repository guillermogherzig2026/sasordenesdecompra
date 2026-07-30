@extends('layouts.app')

@section('body')
    <x-app-shell title="Catalogo de servicios">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Catalogo de servicios</h2>
                    <p class="fine-print">Servicios recurrentes registrados con vigencia y lapso de pago.</p>
                </div>
                <form class="toolbar" method="GET" action="{{ route('services.catalog') }}">
                    <input name="q" value="{{ $query }}" placeholder="Buscar servicio...">
                    <a class="button primary" href="{{ route('services.create') }}">Nuevo servicio</a>
                </form>
            </div>

            <div class="table-scroll service-month-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titular</th>
                            <th>Sucursal</th>
                            <th>Ubicacion</th>
                            <th>Banco</th>
                            <th>Cuenta pagadora</th>
                            <th>Servicio</th>
                            <th>Proveedor</th>
                            <th>No. Servicio</th>
                            <th>Categoria</th>
                            <th>Monto</th>
                            <th>Vigencia</th>
                            <th>Lapso pago</th>
                            <th>Fecha inicio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($services as $service)
                            <tr>
                                <td><strong>{{ $service->folio }}</strong></td>
                                <td>{{ $service->holder ?: $service->company_name }}</td>
                                <td>{{ $service->display_branch }}</td>
                                <td>{{ $service->display_location }}</td>
                                <td>{{ $service->bank }}</td>
                                <td>{{ $service->payer_account }}</td>
                                <td>{{ $service->service_name }}</td>
                                <td>{{ $service->provider }}</td>
                                <td>{{ $service->service_number }}</td>
                                <td>{{ $service->category }}</td>
                                <td>${{ number_format((float) $service->cost, 2) }}</td>
                                <td>{{ $service->validity }}</td>
                                <td>{{ $service->is_domiciled ? 'Domiciliado' : $service->payment_interval_days . ' dias' }}</td>
                                <td>{{ $service->cutoff_day ? 'Corte dia ' . $service->cutoff_day : $service->start_date?->format('d/m/Y') }}</td>
                                <td>
                                    <details class="status-menu">
                                        <summary class="status {{ \App\Support\UiStatus::serviceClass($service->status) }}">{{ \App\Support\UiStatus::service($service->status, 'services') }}</summary>
                                        <div class="status-menu-panel">
                                            @if ($service->status === 'active')
                                                <form class="inline-form" method="POST" action="{{ route('services.status', [$service, 'paused']) }}">@csrf @method('PATCH')<button class="button ghost small">Pausar</button></form>
                                            @endif
                                            @if ($service->status === 'paused')
                                                <form class="inline-form" method="POST" action="{{ route('services.status', [$service, 'active']) }}">@csrf @method('PATCH')<button class="button primary small">Reactivar</button></form>
                                            @endif
                                            @if ($service->status !== 'inactive')
                                                <form class="inline-form" method="POST" action="{{ route('services.status', [$service, 'inactive']) }}" onsubmit="return confirm('Dar de baja {{ $service->folio }}?')">@csrf @method('PATCH')<button class="button danger small">Baja</button></form>
                                            @endif
                                        </div>
                                    </details>
                                </td>
                                <td>
                                    <div class="item-actions">
                                        @if ($service->status === 'active')
                                            <form class="inline-form" method="POST" action="{{ route('services.status', [$service, 'paused']) }}">
                                                @csrf @method('PATCH')
                                                <button class="button ghost small" type="submit">Pausar</button>
                                            </form>
                                        @elseif ($service->status === 'paused')
                                            <form class="inline-form" method="POST" action="{{ route('services.status', [$service, 'active']) }}">
                                                @csrf @method('PATCH')
                                                <button class="button primary small" type="submit">Reactivar</button>
                                            </form>
                                        @endif
                                        <a class="button ghost small" href="{{ route('services.edit', $service) }}">Editar</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="16">No hay servicios registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </x-app-shell>
@endsection
