@extends('layouts.app')

@php
    $role = auth()->user()->role;
    $reports = match ($role) {
        'finance' => [
            ['Ordenes vigentes', 'finance-active'],
            ['Historial', 'finance-history'],
            ['Auditoria completa', 'audit'],
            ['Servicios y pagos', 'services-payments'],
            ['Catalogo de servicios', 'services-catalog'],
            ['Proveedores', 'providers'],
            ['Empresas', 'companies'],
        ],
        'buyer' => [
            ['Mis ordenes vigentes', 'buyer-active'],
            ['Historial', 'buyer-history'],
            ['Auditoria', 'audit'],
        ],
        'inventory' => [
            ['OC pagadas pendientes', 'inventory-paid'],
            ['Historial', 'inventory-history'],
            ['Auditoria', 'audit'],
        ],
        'services', 'administrative_assistant' => [
            ['Catalogo completo de servicios', 'services-catalog'],
            ['Pagos de servicios', 'services-payments'],
        ],
        default => [],
    };
@endphp

@section('body')
    <x-app-shell title="Reportes">
        <section class="panel">
            <div>
                <h2>Exportaciones</h2>
                <p class="fine-print">Los reportes se descargan en CSV compatible con Excel, igual que el prototipo.</p>
            </div>
            <div class="grid-3">
                @foreach ($reports as [$label, $type])
                    <a class="button ghost" href="{{ route('reports.download', $type) }}">{{ $label }}</a>
                @endforeach
            </div>
        </section>
    </x-app-shell>
@endsection
