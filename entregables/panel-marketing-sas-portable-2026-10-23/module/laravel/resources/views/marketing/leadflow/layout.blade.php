@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/marketing-leadflow.css') }}">
    @if (request()->routeIs('marketing.index', 'marketing.companies.index', 'marketing.campaigns.index', 'marketing.leads.index', 'marketing.conversations.index'))
        <link rel="stylesheet" href="{{ asset('modules/marketing/company-favorites.css') }}">
    @endif
@endpush

@if (request()->routeIs('marketing.index', 'marketing.companies.index', 'marketing.campaigns.index', 'marketing.leads.index', 'marketing.conversations.index'))
    @push('scripts')
        <script src="{{ asset('modules/marketing/company-favorites.js') }}" defer></script>
    @endpush
@endif

@section('body')
    <x-app-shell title="Marketing">
        <div class="leadflow">
            @yield('content')
        </div>
    </x-app-shell>
@endsection
