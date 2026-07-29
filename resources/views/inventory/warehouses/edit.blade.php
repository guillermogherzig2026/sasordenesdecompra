@extends('layouts.app')

@section('body')
    <x-app-shell title="Editar almacen">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">{{ $warehouse['type'] }}</p>
                    <h2>Editar {{ $warehouse['warehouse'] }}</h2>
                    <p class="fine-print">{{ $warehouse['company'] }}</p>
                </div>
                <a class="button ghost" href="{{ route('inventory.warehouses.index') }}">Regresar</a>
            </div>

            <form class="stack" method="POST" action="{{ route('inventory.warehouses.update', $warehouse['key']) }}">
                @csrf
                @method('PUT')

                @if ($warehouse['is_central'])
                    <div class="grid-3">
                        <label>Nombre del almacen
                            <input name="name" value="{{ old('name', $warehouse['warehouse']) }}" required>
                        </label>
                        <label>Nombre corto
                            <input name="short_name" value="{{ old('short_name', $warehouse['short_name'] === '—' ? '' : $warehouse['short_name']) }}">
                        </label>
                        <label>Direccion / referencia
                            <input name="address" value="{{ old('address', $warehouse['address']) }}">
                        </label>
                    </div>

                    <section class="panel">
                        <div>
                            <h3>Empresas surtidas por este almacen</h3>
                            <p class="fine-print">Selecciona las empresas a las que el Almacen de suministros puede surtir productos.</p>
                        </div>
                        <div class="company-selector-list" style="max-height:360px;overflow:auto;display:grid;gap:8px;padding-right:8px">
                            @foreach ($companies as $company)
                                @php
                                    $selected = collect(old('companies', $selectedCompanyIds))->map(fn ($id) => (int) $id)->contains((int) $company->id);
                                @endphp
                                <label class="company-selector-option" style="display:flex;align-items:center;gap:10px;padding:10px;border:1px solid var(--line);border-radius:8px;background:#fff">
                                    <input name="companies[]" type="checkbox" value="{{ $company->id }}" @checked($selected) style="width:auto;min-height:auto">
                                    <span>
                                        <strong>{{ $company->name }}</strong>
                                        <small class="fine-print">RFC: {{ $company->rfc ?: 'Sin RFC' }}</small>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </section>
                @else
                    <div class="grid-2">
                        <label>Empresa
                            <input value="{{ $warehouse['company'] }}" disabled>
                        </label>
                        <label>RFC
                            <input value="{{ $warehouse['rfc'] }}" disabled>
                        </label>
                    </div>
                    <div class="grid-3">
                        <label>Nombre del almacen
                            <input name="warehouse" value="{{ old('warehouse', $warehouse['warehouse']) }}" required>
                        </label>
                        <label>Nombre corto
                            <input name="short_name" value="{{ old('short_name', $warehouse['short_name'] === '—' ? '' : $warehouse['short_name']) }}">
                        </label>
                        <label>Direccion / referencia
                            <input name="address" value="{{ old('address', $warehouse['address']) }}">
                        </label>
                    </div>
                @endif

                <div class="form-actions">
                    <a class="button ghost" href="{{ route('inventory.warehouses.index') }}">Cancelar</a>
                    <button class="button primary" type="submit">Guardar y cerrar</button>
                </div>
            </form>
        </section>
    </x-app-shell>
@endsection
