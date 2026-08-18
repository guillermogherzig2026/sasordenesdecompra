@extends('layouts.app')

@section('title', 'Editar obra')
@section('page-title', 'Editar obra')
@section('page-subtitle', $project->project_key.' - '.$project->name)

@section('content')
    <form class="form-card" method="POST" action="{{ route('obras.update', $project) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('projects.partials.form', ['project' => $project])
    </form>
@endsection
