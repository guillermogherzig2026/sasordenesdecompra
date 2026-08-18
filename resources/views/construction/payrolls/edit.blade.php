@extends('layouts.app')

@section('body')
    <x-app-shell title="Editar nomina periodica">
        @php
            $backUrl = route('construction.placeholder', [
                'section' => 'mano-obra',
                'project' => $payroll->construction_project_id,
                'open_payroll' => 1,
            ]);
            $values = [
                'construction_project_id' => old('construction_project_id', $payroll->construction_project_id),
                'code' => old('code', $payroll->code),
                'contractor' => old('contractor', $payroll->contractor),
                'description' => old('description', $payroll->description),
                'area' => old('area', $payroll->area),
                'periodicity' => old('periodicity', $payroll->periodicity),
                'period_start' => old('period_start', $payroll->period_start?->format('Y-m-d')),
                'period_end' => old('period_end', $payroll->period_end?->format('Y-m-d')),
                'progress' => old('progress', $payroll->progress),
                'amount' => old('amount', $payroll->amount),
                'status' => old('status', $payroll->status),
                'payment_due_date' => old('payment_due_date', $payroll->payment_due_date?->format('Y-m-d')),
            ];
        @endphp

        <form class="panel payroll-form" method="POST" action="{{ route('construction.payrolls.update', $payroll) }}">
            @csrf
            @method('PUT')

            <div class="panel-header">
                <div class="panel-header-title">
                    <h2>Editar {{ $payroll->code }}</h2>
                    <p class="fine-print">Actualiza los datos de esta nomina periodica.</p>
                </div>
                <a class="button ghost" href="{{ $backUrl }}">Volver al catalogo</a>
            </div>

            @include('construction.partials.payroll-form-fields', [
                'activeProjects' => $projects,
                'values' => $values,
                'isCreateForm' => false,
            ])

            <div class="form-actions payroll-form-actions">
                <a class="button ghost" href="{{ $backUrl }}">Cancelar</a>
                <button class="button primary" type="submit">Guardar cambios</button>
            </div>
        </form>
    </x-app-shell>
@endsection
