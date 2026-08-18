@extends('layouts.app')

@section('title', 'Nueva obra')
@section('page-title', 'Nueva obra')
@section('page-subtitle', 'Alta de obra y datos principales')

@section('content')
    <form class="form-card" method="POST" action="{{ route('obras.store') }}" enctype="multipart/form-data">
        @csrf
        @include('projects.partials.form', ['project' => $project])
    </form>
@endsection
