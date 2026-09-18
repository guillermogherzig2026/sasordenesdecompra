@extends('marketing.leadflow.layout', ['title' => 'Empresas | LeadFlow'])

@section('content')
<div>
    <div>
        <div class="panel pad">
            <div class="panel-head" style="padding:0 0 16px;border:0">
                <h2>Empresas activas</h2>
            </div>
            <div class="card-row">
                <a class="square-card" href="{{ route('marketing.companies.create') }}">
                    <span class="metric-icon">+</span>
                    <strong>Nueva empresa</strong>
                </a>
                <a class="square-card" href="{{ route('marketing.companies.index') }}">
                    <span class="metric-icon">▦</span>
                    <strong>Todas</strong>
                </a>
                @foreach($companies as $company)
                    <a class="square-card {{ $selected?->id === $company->id ? 'active' : '' }}" href="{{ route('marketing.companies.index', ['company_id' => $company->id]) }}">
                        @if($selected?->id === $company->id)<span class="check">✓</span>@endif
                        <span class="logo-circle" style="background:{{ $company->brand_color }}">{{ $company->logo_initials }}</span>
                        <strong>{{ $company->name }}</strong>
                        <span class="status-dot {{ $company->active ? 'active' : 'inactive' }}">{{ $company->active ? 'Activa' : 'Inactiva' }}</span>
                        <span class="muted small">{{ $company->campaigns_count }} campañas · {{ number_format($company->leads_count) }} leads</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="panel">
            <div class="panel-header company-table-toolbar">
                <form class="filters" method="get">
                    <input class="input" style="width:280px" name="search" value="{{ request('search') }}" placeholder="Buscar empresas...">
                    <select class="select" style="width:180px" name="status">
                        <option value="">Estado: Todas</option>
                        <option value="active">Activas</option>
                        <option value="inactive">Inactivas</option>
                    </select>
                    <button class="btn" type="submit">Filtros</button>
                </form>
            </div>

            <div class="table-wrap">
                <table>
                <thead>
                    <tr>
                        <th>Empresa</th>
                        <th>WhatsApp activos</th>
                        <th>Campañas</th>
                        <th>Leads</th>
                        <th>Conversaciones</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($companies as $company)
                        <tr class="{{ $selected?->id === $company->id ? 'selected' : '' }}">
                            <td><strong>{{ $company->name }}</strong></td>
                            <td>{{ $company->whatsappNumbers->count() }}</td>
                            <td>{{ $company->campaigns_count }}</td>
                            <td>{{ number_format($company->leads_count) }}</td>
                            <td>{{ number_format($company->conversations_count) }}</td>
                            <td><span class="badge {{ $company->active ? 'green' : '' }}">{{ $company->active ? 'Activa' : 'Inactiva' }}</span></td>
                            <td>
                                <a class="btn" href="{{ route('marketing.companies.edit', $company) }}">Editar</a>
                                <form method="post" action="{{ route('marketing.companies.toggle', $company) }}" style="display:inline">
                                    @csrf @method('patch')
                                    <button class="btn" type="submit">{{ $company->active ? 'Desactivar' : 'Activar' }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
