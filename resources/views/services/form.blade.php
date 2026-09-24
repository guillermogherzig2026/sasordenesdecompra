@extends('layouts.app')

@section('body')
    @php
        $service = $service ?? null;
        $isEditing = (bool) $service;
    @endphp

    <x-app-shell :title="$isEditing ? 'Editar servicio' : 'Alta de servicio'">
        <form class="panel service-form" method="POST" action="{{ $isEditing ? route('services.update', $service) : route('services.store') }}">
            @csrf
            @if ($isEditing)
                @method('PUT')
            @endif
            <input type="hidden" name="start_date" value="{{ old('start_date', $service?->start_date?->toDateString() ?? now()->toDateString()) }}">
            <div class="panel-header">
                <div>
                    <h2>{{ $isEditing ? 'Editar servicio ' . $service->folio : 'Alta de nuevo servicio' }}</h2>
                </div>
                <a class="button ghost" href="{{ route('services.catalog') }}">Volver al catálogo</a>
            </div>

            @include('services.partials.fields')

            <div class="form-actions">
                <div class="actions">
                    <a class="button ghost" href="{{ route('services.catalog') }}">Cancelar</a>
                    <button class="button primary" type="submit">Guardar servicio</button>
                </div>
            </div>
        </form>

        @include('services.partials.assets')
    </x-app-shell>
@endsection
