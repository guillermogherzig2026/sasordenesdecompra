@extends('layouts.app')

@section('body')
    <x-app-shell title="Bitacora de obra">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Bitacora</h2>
                    <p class="fine-print">Movimientos principales del modulo de administracion de obra.</p>
                </div>
                <a class="button ghost" href="{{ route('construction.dashboard') }}">Panel de obra</a>
            </div>

            <ul class="audit-list">
                @forelse ($logs as $entry)
                    <li>
                        <strong>{{ $entry->action }}</strong>
                        {{ $entry->description }}
                        @if ($entry->project)
                            <span class="fine-print">Obra: {{ $entry->project->project_key }}</span>
                        @endif
                        <small>{{ $entry->user?->name ?? 'Sistema' }} &middot; {{ $entry->occurred_at->format('d/m/Y H:i') }}</small>
                    </li>
                @empty
                    <li>Sin movimientos registrados.</li>
                @endforelse
            </ul>
        </section>
    </x-app-shell>
@endsection
