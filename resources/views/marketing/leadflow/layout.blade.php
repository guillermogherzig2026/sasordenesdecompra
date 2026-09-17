@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/marketing-leadflow.css') }}">
@endpush

@section('body')
    <x-app-shell title="Marketing">
        <div class="leadflow">
            @yield('content')
        </div>
    </x-app-shell>
@endsection
