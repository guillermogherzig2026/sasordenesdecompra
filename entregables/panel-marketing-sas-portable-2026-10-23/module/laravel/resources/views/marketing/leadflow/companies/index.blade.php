@extends('marketing.leadflow.layout', ['title' => 'Empresas | LeadFlow'])

@section('content')
@php
    $tableFilters = request()->only(['search', 'status']);
@endphp
<div>
    <div>
        @include('marketing.leadflow.partials.company-selection')

        <div class="panel">
            <div class="panel-header company-table-toolbar">
                <form class="filters" method="get">
                    <input type="hidden" name="company_id" value="{{ $selected?->id ?? 'all' }}">
                    <input class="input" style="width:280px" name="search" value="{{ request('search') }}" placeholder="Buscar empresas...">
                    <select class="select" style="width:180px" name="status">
                        <option value="">Estado: Todas</option>
                        <option value="active" @selected(request('status') === 'active')>Activas</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactivas</option>
                    </select>
                    <button class="btn" type="submit">Filtros</button>
                </form>
            </div>

            <div class="table-wrap marketing-company-table">
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
                    @forelse($companies as $company)
                        <tr class="{{ $selected?->id === $company->id ? 'selected' : '' }}">
                            <td>
                                <div class="marketing-company-name-cell">
                                    <form method="post" action="{{ route('marketing.companies.favorite', $company) }}">
                                        @csrf @method('patch')
                                        <input type="hidden" name="favorite" value="{{ $company->is_favorite ? 0 : 1 }}">
                                        <button class="marketing-favorite-star {{ $company->is_favorite ? 'is-on' : '' }}" type="submit" aria-label="{{ $company->is_favorite ? 'Quitar' : 'Agregar' }} {{ $company->name }} {{ $company->is_favorite ? 'de' : 'a' }} favoritas" title="{{ $company->is_favorite ? 'Quitar de favoritas' : 'Agregar a favoritas' }}">{{ $company->is_favorite ? '★' : '☆' }}</button>
                                    </form>
                                    <a class="marketing-company-name-link" href="{{ route('marketing.companies.index', ['company_id' => $company->id] + $tableFilters) }}" @if ($selected?->is($company)) aria-current="page" @endif>{{ $company->name }}</a>
                                </div>
                            </td>
                            <td>{{ $company->whatsappNumbers->count() }}</td>
                            <td>{{ $company->campaigns_count }}</td>
                            <td>{{ number_format($company->leads_count) }}</td>
                            <td>{{ number_format($company->conversations_count) }}</td>
                            <td><span class="badge {{ $company->active ? 'green' : '' }}">{{ $company->active ? 'Activa' : 'Inactiva' }}</span></td>
                            <td>
                                <div class="marketing-company-row-actions">
                                    @if ($selected?->is($company))
                                        <span class="marketing-company-selected-label">✓ Seleccionada</span>
                                    @else
                                        <a class="btn marketing-company-select" href="{{ route('marketing.companies.index', ['company_id' => $company->id] + $tableFilters) }}">Seleccionar</a>
                                    @endif
                                    <a class="btn" href="{{ route('marketing.companies.edit', $company) }}">Editar</a>
                                    <form method="post" action="{{ route('marketing.companies.toggle', $company) }}">
                                        @csrf @method('patch')
                                        <button class="btn" type="submit">{{ $company->active ? 'Desactivar' : 'Activar' }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="marketing-company-empty">No hay empresas con esos filtros.</td></tr>
                    @endforelse
                </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
