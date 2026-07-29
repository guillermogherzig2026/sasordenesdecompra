@extends('layouts.app')

@section('body')
    <x-app-shell title="Agregar almacen de suministros">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <p class="eyebrow">Almacen de suministros</p>
                    <h2>Nuevo almacen de suministros</h2>
                    <p class="fine-print">Captura la informacion del almacen y selecciona las empresas a las que va a surtir.</p>
                </div>
                <a class="button ghost" href="{{ route('inventory.warehouses.index') }}">Regresar</a>
            </div>

            <form class="stack" method="POST" action="{{ route('inventory.warehouses.supply.store') }}">
                @csrf
                <div class="grid-3">
                    <label>Nombre del almacen
                        <input name="name" value="{{ old('name') }}" placeholder="Ej. Almacen central norte" required>
                    </label>
                    <label>Nombre corto
                        <input name="short_name" value="{{ old('short_name') }}" placeholder="Ej. Central Norte">
                    </label>
                    <label>Direccion / referencia
                        <input name="address" value="{{ old('address') }}" placeholder="Direccion o ubicacion del almacen">
                    </label>
                </div>

                <section class="panel">
                    <div>
                        <h3>Empresas a surtir</h3>
                        <p class="fine-print">Puedes seleccionar una o varias empresas. Despues podras modificar esta lista desde Editar.</p>
                    </div>
                    <div class="company-selector-list" style="max-height:390px;overflow:auto;display:grid;gap:8px;padding-right:8px">
                        @foreach ($companies as $company)
                            @php
                                $selected = collect(old('companies', []))->map(fn ($id) => (int) $id)->contains((int) $company->id);
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

                <div class="form-actions">
                    <a class="button ghost" href="{{ route('inventory.warehouses.index') }}">Cancelar</a>
                    <button class="button primary" type="submit">Crear almacen</button>
                </div>
            </form>
        </section>
    </x-app-shell>
@endsection
