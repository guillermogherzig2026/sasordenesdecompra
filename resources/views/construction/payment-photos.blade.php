@extends('layouts.app')

@section('body')
    <x-app-shell title="Fotos de avance">
        <section class="panel">
            <div class="panel-header">
                <div>
                    <h2>Fotos de avance - {{ $paymentOrder->code }}</h2>
                    <p class="fine-print">
                        {{ $paymentOrder->project?->name }} - {{ $paymentOrder->description }}
                    </p>
                </div>
                <a
                    class="button ghost"
                    href="{{ route('construction.placeholder', ['section' => 'mano-obra', 'project' => $paymentOrder->construction_project_id]) }}"
                >Volver</a>
            </div>

            <div class="payment-photo-grid">
                @foreach ($photos as $photoIndex => $photo)
                    @php
                        $photoUrl = route('construction.payment-orders.photos.file', [
                            'paymentOrder' => $paymentOrder,
                            'photoIndex' => $photoIndex,
                        ]);
                    @endphp
                    <article class="payment-photo-item">
                        <a href="{{ $photoUrl }}" target="_blank" rel="noopener">
                            <img
                                class="payment-photo-preview"
                                src="{{ $photoUrl }}"
                                alt="Foto de avance {{ $photoIndex + 1 }} de {{ $paymentOrder->code }}"
                            >
                        </a>
                        <div class="payment-photo-meta">
                            <strong class="payment-photo-name" title="{{ $photo['name'] ?? 'Foto de avance' }}">
                                {{ $photo['name'] ?? 'Foto de avance '.($photoIndex + 1) }}
                            </strong>
                            <a class="button ghost small" href="{{ $photoUrl }}" target="_blank" rel="noopener">Ver</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </x-app-shell>
@endsection
