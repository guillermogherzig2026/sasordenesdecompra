<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Marketing' }} | {{ config('app.name', 'SAS Ordenes de Compra') }}</title>
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/sas-marketing-shell.css') }}">
</head>
<body>
    @yield('body')
    @stack('scripts')
</body>
</html>
