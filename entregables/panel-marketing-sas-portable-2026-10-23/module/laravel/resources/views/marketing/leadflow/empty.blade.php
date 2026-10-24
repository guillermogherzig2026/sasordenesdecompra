@extends('marketing.leadflow.layout', ['title' => 'Marketing'])

@section('content')
    <section class="panel empty-state">
        <span class="metric-icon" aria-hidden="true">+</span>
        <h1>{{ $heading }}</h1>
        <p>{{ $message }}</p>
        <a class="btn primary" href="{{ route('marketing.companies.create') }}">Registrar empresa</a>
    </section>
@endsection
