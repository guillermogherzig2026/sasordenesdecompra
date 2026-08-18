@extends('layouts.app')

@section('title', $project->project_key.' | Expediente')
@section('page-title', $project->name)
@section('page-subtitle', $project->project_key.' - '.$project->client?->name.' - '.$project->location)

@section('content')
    @php
        $money = fn ($value) => '$'.number_format((float) $value, 2);
        $percent = fn ($value) => number_format((float) $value, 2).'%';
        $scheduledPhysical = round($project->workItems->avg('scheduled_percent') ?? 0, 2);
        $toEstimate = max((float) $project->contracted_value - (float) $project->estimated_amount, 0);
        $toPay = max((float) $project->estimated_amount - (float) $project->paid_amount, 0);
        $totalWorkContracted = $project->workItems->sum(fn ($item) => (float) $item->amount);
        $totalWorkEstimated = $project->workItems->sum(fn ($item) => (float) $item->estimated_amount);
        $totalWorkPaid = $project->workItems->sum(fn ($item) => (float) $item->paid_amount);
        $statusClass = function (?string $status): string {
            return match ($status) {
                'Pagada', 'Completada', 'Completa', 'Aprobada', 'Liberado', 'Aplicado', 'Activo', 'Vigente' => 'success',
                'Autorizada', 'En revision', 'En proceso', 'En seguimiento', 'Programado' => 'warning',
                'Pago parcial', 'Parcialmente surtido', 'En transito' => 'warning',
                'Vencida', 'Rechazada', 'Cancelada', 'Critica' => 'danger',
                'Pendiente', 'Borrador', 'Sin iniciar', null => 'secondary',
                default => 'primary',
            };
        };
        $estimateLabels = $project->estimates->sortBy('estimate_number')->map(fn ($estimate) => 'Est. '.$estimate->estimate_number)->values();
        $estimateProgress = $project->estimates->sortBy('estimate_number')->pluck('cumulative_progress')->map(fn ($value) => (float) $value)->values();
        $estimatePaid = $project->estimates->sortBy('estimate_number')->map(function ($estimate) use ($project) {
            return (float) $project->contracted_value > 0 ? round(((float) $estimate->paid_amount / (float) $project->contracted_value) * 100, 2) : 0;
        })->values();
    @endphp

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div class="d-flex gap-2 flex-wrap">
            <span class="badge-soft badge-{{ $project->statusColor() }}"><span class="status-dot"></span>{{ $project->status }}</span>
            <span class="badge-soft badge-{{ $project->modality === 'Precio alzado' ? 'success' : 'primary' }}">{{ $project->modality }}</span>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-soft" href="{{ route('obras.index') }}">
                <i data-lucide="arrow-left"></i>
                Obras
            </a>
            @if($canEdit)
                <a class="btn btn-aqua" href="{{ route('obras.edit', $project) }}">
                    <i data-lucide="pencil"></i>
                    Editar
                </a>
            @endif
        </div>
    </div>

    <section class="metric-grid mb-3">
        <article class="metric-card">
            <div class="metric-label">Valor contratado</div>
            <div class="metric-value">{{ $money($project->contracted_value) }}</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Avance fisico programado</div>
            <div class="metric-value">{{ $percent($scheduledPhysical) }}</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Avance fisico real</div>
            <div class="metric-value">{{ $percent($project->physical_progress) }}</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Avance financiero</div>
            <div class="metric-value">{{ $percent($project->financial_progress) }}</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Estimado acumulado</div>
            <div class="metric-value">{{ $money($project->estimated_amount) }}</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Pagado acumulado</div>
            <div class="metric-value">{{ $money($project->paid_amount) }}</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Retenciones</div>
            <div class="metric-value">{{ $money($project->retention_amount) }}</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Por estimar</div>
            <div class="metric-value">{{ $money($toEstimate) }}</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Por pagar</div>
            <div class="metric-value">{{ $money($toPay) }}</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Diferencia fisico-financiera</div>
            <div class="metric-value">{{ $percent($project->physical_financial_difference) }}</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Dias transcurridos</div>
            <div class="metric-value">{{ $project->days_elapsed }}</div>
        </article>
        <article class="metric-card">
            <div class="metric-label">Dias restantes</div>
            <div class="metric-value">{{ $project->days_remaining }}</div>
        </article>
    </section>

    <ul class="nav work-tabs mb-3" id="projectTabs" role="tablist">
        @foreach([
            'resumen' => 'Resumen',
            'general' => 'Informacion general',
            'contrato' => 'Contrato',
            'presupuesto' => 'Presupuesto',
            'partidas' => 'Partidas',
            'estimaciones' => 'Estimaciones',
            'alcances' => 'Alcances semanales',
            'avances' => 'Avances',
            'materiales' => 'Materiales',
            'mano' => 'Mano de obra',
            'nomina' => 'Nomina',
            'pagos' => 'Pagos',
            'calendario' => 'Calendario',
            'bitacora' => 'Bitacora',
            'fotografias' => 'Fotografias',
            'incidencias' => 'Incidencias',
            'cambios' => 'Cambios',
            'documentos' => 'Documentos',
            'reportes' => 'Reportes',
        ] as $id => $label)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ $id }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $id }}" type="button" role="tab">{{ $label }}</button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content">
        <section class="tab-pane fade show active" id="resumen" role="tabpanel">
            <div class="row g-3">
                <div class="col-xl-7">
                    <div class="panel-card h-100">
                        <h2 class="h5 fw-bold mb-3">Pagos vs avance de obra</h2>
                        <canvas id="projectProgressChart" height="150"></canvas>
                    </div>
                </div>
                <div class="col-xl-5">
                    <div class="panel-card h-100">
                        <h2 class="h5 fw-bold mb-3">Resumen financiero</h2>
                        <table class="table table-sm align-middle">
                            <tbody>
                                <tr><th>Contrato</th><td class="text-end">{{ $money($project->contracted_value) }}</td></tr>
                                <tr><th>Estimado</th><td class="text-end">{{ $money($project->estimated_amount) }}</td></tr>
                                <tr><th>Pagado</th><td class="text-end">{{ $money($project->paid_amount) }}</td></tr>
                                <tr><th>Saldo por pagar</th><td class="text-end">{{ $money($toPay) }}</td></tr>
                                <tr><th>Retenciones</th><td class="text-end">{{ $money($project->retention_amount) }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section class="tab-pane fade" id="general" role="tabpanel">
            <div class="panel-card">
                <div class="row g-3 align-items-start">
                    <div class="col-lg-4">
                        <img class="w-100 rounded-3" src="{{ $project->photo_path ?: asset('images/projects/residencial-los-pinos.png') }}" alt="{{ $project->name }}">
                    </div>
                    <div class="col-lg-8">
                        <div class="row g-3">
                            @foreach([
                                'Clave' => $project->project_key,
                                'Cliente' => $project->client?->name ?? 'Sin cliente',
                                'Empresa' => $project->company?->name,
                                'Ubicacion' => $project->location,
                                'Tipo de obra' => $project->project_type,
                                'Responsable' => $project->responsible?->name ?? 'Sin responsable',
                                'Fecha de inicio' => $project->start_date?->format('d/m/Y'),
                                'Terminacion estimada' => $project->estimated_end_date?->format('d/m/Y'),
                            ] as $label => $value)
                                <div class="col-md-6">
                                    <div class="metric-card h-100">
                                        <div class="metric-label">{{ $label }}</div>
                                        <div class="fw-bold">{{ $value }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="tab-pane fade" id="contrato" role="tabpanel">
            <div class="table-card excel-wrap">
                <table class="table excel-table align-middle">
                    <thead>
                        <tr>
                            <th>Contrato</th>
                            <th>Firma</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Total</th>
                            <th>Retencion</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->contracts as $contract)
                            <tr>
                                <td>{{ $contract->contract_number }}</td>
                                <td>{{ $contract->signed_at?->format('d/m/Y') }}</td>
                                <td>{{ $contract->start_date?->format('d/m/Y') }}</td>
                                <td>{{ $contract->end_date?->format('d/m/Y') }}</td>
                                <td>{{ $money($contract->total_value) }}</td>
                                <td>{{ $percent($contract->retention_percentage) }}</td>
                                <td><span class="badge-soft badge-{{ $statusClass($contract->status) }}">{{ $contract->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="tab-pane fade" id="presupuesto" role="tabpanel">
            <div class="table-card excel-wrap">
                <table class="table excel-table align-middle">
                    <thead>
                        <tr>
                            <th>Clave</th>
                            <th>Categoria</th>
                            <th>Valor contratado</th>
                            <th>% contrato</th>
                            <th>Avance programado</th>
                            <th>Avance real</th>
                            <th>Diferencia fisica</th>
                            <th>Estimado acumulado</th>
                            <th>Pagado acumulado</th>
                            <th>Retenciones</th>
                            <th>Por estimar</th>
                            <th>Por pagar</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->categories as $category)
                            @php
                                $items = $category->workItems;
                                $contracted = $items->sum(fn ($item) => (float) $item->amount);
                                $estimated = $items->sum(fn ($item) => (float) $item->estimated_amount);
                                $paid = $items->sum(fn ($item) => (float) $item->paid_amount);
                                $programmed = round($items->avg('scheduled_percent') ?? 0, 2);
                                $real = round($items->avg('progress_percent') ?? 0, 2);
                            @endphp
                            <tr>
                                <td>{{ $category->code }}</td>
                                <td>{{ $category->name }}</td>
                                <td>{{ $money($contracted) }}</td>
                                <td>{{ (float) $project->contracted_value > 0 ? $percent(($contracted / (float) $project->contracted_value) * 100) : '0.00%' }}</td>
                                <td>{{ $percent($programmed) }}</td>
                                <td>{{ $percent($real) }}</td>
                                <td class="{{ $real < $programmed ? 'text-danger' : 'text-success' }}">{{ $percent($real - $programmed) }}</td>
                                <td>{{ $money($estimated) }}</td>
                                <td>{{ $money($paid) }}</td>
                                <td>{{ $money($estimated * 0.05) }}</td>
                                <td>{{ $money(max($contracted - $estimated, 0)) }}</td>
                                <td>{{ $money(max($estimated - $paid, 0)) }}</td>
                                <td><span class="badge-soft badge-{{ $statusClass($items->first()?->status) }}">{{ $items->first()?->status ?? 'Sin iniciar' }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">Totales</th>
                            <th>{{ $money($totalWorkContracted) }}</th>
                            <th>{{ (float) $project->contracted_value > 0 ? $percent(($totalWorkContracted / (float) $project->contracted_value) * 100) : '0.00%' }}</th>
                            <th>{{ $percent($project->workItems->avg('scheduled_percent') ?? 0) }}</th>
                            <th>{{ $percent($project->workItems->avg('progress_percent') ?? 0) }}</th>
                            <th>{{ $percent(($project->workItems->avg('progress_percent') ?? 0) - ($project->workItems->avg('scheduled_percent') ?? 0)) }}</th>
                            <th>{{ $money($totalWorkEstimated) }}</th>
                            <th>{{ $money($totalWorkPaid) }}</th>
                            <th>{{ $money($totalWorkEstimated * 0.05) }}</th>
                            <th>{{ $money(max($totalWorkContracted - $totalWorkEstimated, 0)) }}</th>
                            <th>{{ $money(max($totalWorkEstimated - $totalWorkPaid, 0)) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>

        <section class="tab-pane fade" id="partidas" role="tabpanel">
            <div class="table-card excel-wrap">
                <table class="table excel-table align-middle">
                    <thead>
                        <tr>
                            <th>Clave</th>
                            <th>Partida</th>
                            <th>Categoria</th>
                            <th>Unidad</th>
                            <th>Cantidad contratada</th>
                            <th>Cantidad ejecutada</th>
                            <th>% programado</th>
                            <th>% real</th>
                            <th>Diferencia</th>
                            <th>Valor contratado</th>
                            <th>Estimado acumulado</th>
                            <th>Pagado acumulado</th>
                            <th>Saldo por estimar</th>
                            <th>Saldo por pagar</th>
                            <th>Fecha termino</th>
                            <th>Responsable</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->workItems as $item)
                            <tr>
                                <td>{{ $item->code }}</td>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->category?->name }}</td>
                                <td>{{ $item->unit }}</td>
                                <td>{{ number_format((float) $item->contracted_quantity, 2) }}</td>
                                <td>{{ number_format((float) $item->executed_quantity, 2) }}</td>
                                <td>{{ $percent($item->scheduled_percent) }}</td>
                                <td>{{ $percent($item->progress_percent) }}</td>
                                <td class="{{ (float) $item->progress_percent < (float) $item->scheduled_percent ? 'text-danger' : 'text-success' }}">{{ $percent((float) $item->progress_percent - (float) $item->scheduled_percent) }}</td>
                                <td>{{ $money($item->amount) }}</td>
                                <td>{{ $money($item->estimated_amount) }}</td>
                                <td>{{ $money($item->paid_amount) }}</td>
                                <td>{{ $money(max((float) $item->amount - (float) $item->estimated_amount, 0)) }}</td>
                                <td>{{ $money(max((float) $item->estimated_amount - (float) $item->paid_amount, 0)) }}</td>
                                <td>{{ $item->estimated_end_date?->format('d/m/Y') }}</td>
                                <td>{{ $project->responsible?->name }}</td>
                                <td><span class="badge-soft badge-{{ $statusClass($item->status) }}">{{ $item->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="9">Totales</th>
                            <th>{{ $money($totalWorkContracted) }}</th>
                            <th>{{ $money($totalWorkEstimated) }}</th>
                            <th>{{ $money($totalWorkPaid) }}</th>
                            <th>{{ $money(max($totalWorkContracted - $totalWorkEstimated, 0)) }}</th>
                            <th>{{ $money(max($totalWorkEstimated - $totalWorkPaid, 0)) }}</th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>

        <section class="tab-pane fade" id="estimaciones" role="tabpanel">
            <div class="table-card excel-wrap">
                <table class="table excel-table align-middle">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Periodo</th>
                            <th>Fecha corte</th>
                            <th>Autorizacion</th>
                            <th>Pago programado</th>
                            <th>Avance anterior</th>
                            <th>Avance periodo</th>
                            <th>Avance acumulado</th>
                            <th>Importe bruto</th>
                            <th>Retencion</th>
                            <th>Importe neto</th>
                            <th>Pagado</th>
                            <th>Saldo</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->estimates as $estimate)
                            <tr>
                                <td>{{ $estimate->estimate_number }}</td>
                                <td>{{ $estimate->period_start->format('d/m/Y') }} - {{ $estimate->period_end->format('d/m/Y') }}</td>
                                <td>{{ $estimate->cutoff_date?->format('d/m/Y') }}</td>
                                <td>{{ $estimate->authorized_at?->format('d/m/Y') }}</td>
                                <td>{{ $estimate->scheduled_payment_date?->format('d/m/Y') }}</td>
                                <td>{{ $percent($estimate->previous_progress) }}</td>
                                <td>{{ $percent($estimate->period_progress) }}</td>
                                <td>{{ $percent($estimate->cumulative_progress) }}</td>
                                <td>{{ $money($estimate->gross_amount) }}</td>
                                <td>{{ $money($estimate->retention) }}</td>
                                <td>{{ $money($estimate->net_amount) }}</td>
                                <td>{{ $money($estimate->paid_amount) }}</td>
                                <td>{{ $money($estimate->balance) }}</td>
                                <td><span class="badge-soft badge-{{ $statusClass($estimate->status) }}">{{ $estimate->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="tab-pane fade" id="alcances" role="tabpanel">
            <div class="table-card excel-wrap">
                <table class="table excel-table align-middle">
                    <thead>
                        <tr>
                            <th>Semana</th>
                            <th>Actividad</th>
                            <th>Responsable</th>
                            <th>Cuadrilla</th>
                            <th>Unidad</th>
                            <th>Cant. programada</th>
                            <th>Cant. ejecutada</th>
                            <th>Cumplimiento</th>
                            <th>Presupuesto semanal</th>
                            <th>Gasto real</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->weeklyScopes as $scope)
                            <tr>
                                <td>S{{ $scope->week_number }}</td>
                                <td>{{ $scope->activity }}</td>
                                <td>{{ $project->responsible?->name }}</td>
                                <td>{{ $scope->crew?->name }}</td>
                                <td>{{ $scope->unit }}</td>
                                <td>{{ number_format((float) $scope->programmed_quantity, 2) }}</td>
                                <td>{{ number_format((float) $scope->executed_quantity, 2) }}</td>
                                <td>{{ $percent($scope->fulfillment_percent) }}</td>
                                <td>{{ $money($scope->weekly_budget) }}</td>
                                <td>{{ $money($scope->actual_cost) }}</td>
                                <td><span class="badge-soft badge-{{ $statusClass($scope->status) }}">{{ $scope->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="tab-pane fade" id="avances" role="tabpanel">
            <div class="table-card excel-wrap">
                <table class="table excel-table align-middle">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Semana</th>
                            <th>Partida</th>
                            <th>Programado</th>
                            <th>Real</th>
                            <th>Cantidad ejecutada</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->progressRecords as $record)
                            <tr>
                                <td>{{ $record->record_date?->format('d/m/Y') }}</td>
                                <td>{{ $record->week_number }}</td>
                                <td>{{ $record->workItem?->description }}</td>
                                <td>{{ $percent($record->programmed_percent) }}</td>
                                <td>{{ $percent($record->actual_percent) }}</td>
                                <td>{{ number_format((float) $record->quantity_executed, 2) }}</td>
                                <td><span class="badge-soft badge-{{ $statusClass($record->status) }}">{{ $record->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="tab-pane fade" id="materiales" role="tabpanel">
            <div class="row g-3">
                <div class="col-xl-6">
                    <div class="table-card excel-wrap h-100">
                        <h2 class="h5 fw-bold mb-3">Requerimientos</h2>
                        <table class="table excel-table align-middle">
                            <thead>
                                <tr>
                                    <th>Folio</th>
                                    <th>Fecha requerida</th>
                                    <th>Prioridad</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($project->materialRequests as $requestItem)
                                    <tr>
                                        <td>{{ $requestItem->folio }}</td>
                                        <td>{{ $requestItem->required_at?->format('d/m/Y') }}</td>
                                        <td>{{ $requestItem->priority }}</td>
                                        <td><span class="badge-soft badge-{{ $statusClass($requestItem->status) }}">{{ $requestItem->status }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="table-card excel-wrap h-100">
                        <h2 class="h5 fw-bold mb-3">Ordenes de suministro</h2>
                        <table class="table excel-table align-middle">
                            <thead>
                                <tr>
                                    <th>Folio</th>
                                    <th>Compromiso</th>
                                    <th>Detalle</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($project->supplyOrders as $order)
                                    <tr>
                                        <td>{{ $order->folio }}</td>
                                        <td>{{ $order->commitment_date?->format('d/m/Y') }}</td>
                                        <td>{{ $order->items->count() }} materiales</td>
                                        <td><span class="badge-soft badge-{{ $statusClass($order->status) }}">{{ $order->status }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <section class="tab-pane fade" id="mano" role="tabpanel">
            <div class="table-card excel-wrap">
                <table class="table excel-table align-middle">
                    <thead>
                        <tr>
                            <th>Usuario asignado</th>
                            <th>Rol</th>
                            <th>Consulta</th>
                            <th>Edicion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->role?->name }}</td>
                                <td>{{ $user->pivot->can_view ? 'Si' : 'No' }}</td>
                                <td>{{ $user->pivot->can_edit ? 'Si' : 'No' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="tab-pane fade" id="nomina" role="tabpanel">
            <div class="table-card excel-wrap">
                <table class="table excel-table align-middle">
                    <thead>
                        <tr>
                            <th>Semana</th>
                            <th>Periodo</th>
                            <th>Bruto</th>
                            <th>Deducciones</th>
                            <th>Neto</th>
                            <th>Pago</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->payrolls as $payroll)
                            <tr>
                                <td>S{{ $payroll->week_number }}</td>
                                <td>{{ $payroll->period_start->format('d/m/Y') }} - {{ $payroll->period_end->format('d/m/Y') }}</td>
                                <td>{{ $money($payroll->gross_amount) }}</td>
                                <td>{{ $money($payroll->deductions) }}</td>
                                <td>{{ $money($payroll->net_amount) }}</td>
                                <td>{{ $payroll->paid_at?->format('d/m/Y') }}</td>
                                <td><span class="badge-soft badge-{{ $statusClass($payroll->status) }}">{{ $payroll->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="tab-pane fade" id="pagos" role="tabpanel">
            <div class="table-card excel-wrap">
                <table class="table excel-table align-middle">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Beneficiario</th>
                            <th>Concepto</th>
                            <th>Solicitado</th>
                            <th>Programado</th>
                            <th>Pagado</th>
                            <th>Monto</th>
                            <th>Retencion</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_type }}</td>
                                <td>{{ $payment->beneficiary }}</td>
                                <td>{{ $payment->concept }}</td>
                                <td>{{ $payment->requested_at?->format('d/m/Y') }}</td>
                                <td>{{ $payment->scheduled_at?->format('d/m/Y') }}</td>
                                <td>{{ $payment->paid_at?->format('d/m/Y') }}</td>
                                <td>{{ $money($payment->amount) }}</td>
                                <td>{{ $money($payment->retention) }}</td>
                                <td><span class="badge-soft badge-{{ $statusClass($payment->status) }}">{{ $payment->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="tab-pane fade" id="calendario" role="tabpanel">
            <div class="table-card excel-wrap">
                <table class="table excel-table align-middle">
                    <thead>
                        <tr>
                            <th>Evento</th>
                            <th>Tipo</th>
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->events as $event)
                            <tr>
                                <td>{{ $event->title }}</td>
                                <td>{{ $event->event_type }}</td>
                                <td>{{ $event->starts_at?->format('d/m/Y H:i') }}</td>
                                <td>{{ $event->ends_at?->format('d/m/Y H:i') }}</td>
                                <td><span class="badge-soft badge-{{ $statusClass($event->status) }}">{{ $event->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="tab-pane fade" id="bitacora" role="tabpanel">
            <div class="table-card excel-wrap">
                <table class="table excel-table align-middle">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Clima</th>
                            <th>Personal</th>
                            <th>Actividades</th>
                            <th>Problemas</th>
                            <th>Responsable</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->dailyLogs as $log)
                            <tr>
                                <td>{{ $log->log_date?->format('d/m/Y') }}</td>
                                <td>{{ $log->weather }}</td>
                                <td>{{ $log->personnel }}</td>
                                <td>{{ $log->activities }}</td>
                                <td>{{ $log->problems }}</td>
                                <td>{{ $project->responsible?->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="tab-pane fade" id="fotografias" role="tabpanel">
            <div class="panel-card">
                @if($project->photos->isEmpty())
                    <div class="empty-state">Sin fotografias registradas.</div>
                @else
                    <div class="photo-strip">
                        @foreach($project->photos as $photo)
                            <figure class="m-0">
                                <img class="photo-tile" src="{{ $photo->file_path }}" alt="{{ $photo->title }}">
                                <figcaption class="small mt-2 fw-bold">{{ $photo->title }}</figcaption>
                            </figure>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section class="tab-pane fade" id="incidencias" role="tabpanel">
            <div class="table-card excel-wrap">
                <table class="table excel-table align-middle">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Fecha</th>
                            <th>Categoria</th>
                            <th>Prioridad</th>
                            <th>Responsable</th>
                            <th>Compromiso</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->incidents as $incident)
                            <tr>
                                <td>{{ $incident->folio }}</td>
                                <td>{{ $incident->incident_date?->format('d/m/Y') }}</td>
                                <td>{{ $incident->category }}</td>
                                <td>{{ $incident->priority }}</td>
                                <td>{{ $incident->responsible?->name }}</td>
                                <td>{{ $incident->commitment_date?->format('d/m/Y') }}</td>
                                <td><span class="badge-soft badge-{{ $statusClass($incident->status) }}">{{ $incident->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="tab-pane fade" id="cambios" role="tabpanel">
            <div class="table-card excel-wrap">
                <table class="table excel-table align-middle">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Fecha</th>
                            <th>Descripcion</th>
                            <th>Impacto costo</th>
                            <th>Impacto plazo</th>
                            <th>Nuevo contrato</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->changeOrders as $change)
                            <tr>
                                <td>{{ $change->folio }}</td>
                                <td>{{ $change->requested_at?->format('d/m/Y') }}</td>
                                <td>{{ $change->description }}</td>
                                <td>{{ $money($change->cost_impact) }}</td>
                                <td>{{ $change->schedule_impact_days }} dias</td>
                                <td>{{ $money($change->new_contract_amount) }}</td>
                                <td><span class="badge-soft badge-{{ $statusClass($change->status) }}">{{ $change->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="tab-pane fade" id="documentos" role="tabpanel">
            <div class="table-card excel-wrap">
                <table class="table excel-table align-middle">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Categoria</th>
                            <th>Fecha</th>
                            <th>Version</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->documents as $document)
                            <tr>
                                <td>{{ $document->name }}</td>
                                <td>{{ $document->document_type }}</td>
                                <td>{{ $document->category }}</td>
                                <td>{{ $document->document_date?->format('d/m/Y') }}</td>
                                <td>{{ $document->version }}</td>
                                <td><span class="badge-soft badge-{{ $statusClass($document->status) }}">{{ $document->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="tab-pane fade" id="reportes" role="tabpanel">
            <div class="row g-3">
                <div class="col-xl-7">
                    <div class="panel-card h-100">
                        <h2 class="h5 fw-bold mb-3">Curva S base</h2>
                        <canvas id="sCurveChart" height="150"></canvas>
                    </div>
                </div>
                <div class="col-xl-5">
                    <div class="panel-card h-100">
                        <h2 class="h5 fw-bold mb-3">Semaforo de control</h2>
                        <table class="table table-sm align-middle">
                            <tbody>
                                <tr><th>Tiempo</th><td><span class="badge-soft badge-warning">En riesgo</span></td></tr>
                                <tr><th>Costo</th><td><span class="badge-soft badge-success">Aceptable</span></td></tr>
                                <tr><th>Calidad</th><td><span class="badge-soft badge-success">Aceptable</span></td></tr>
                                <tr><th>Alcance</th><td><span class="badge-soft badge-success">Aceptable</span></td></tr>
                                <tr><th>Pagos</th><td><span class="badge-soft badge-danger">En riesgo</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        const estimateLabels = @json($estimateLabels);
        const estimateProgress = @json($estimateProgress);
        const estimatePaid = @json($estimatePaid);

        const progressChart = document.getElementById('projectProgressChart');
        if (progressChart && window.Chart) {
            new Chart(progressChart, {
                type: 'line',
                data: {
                    labels: estimateLabels.length ? estimateLabels : ['Inicio', 'Actual'],
                    datasets: [
                        {
                            label: 'Avance fisico real',
                            data: estimateProgress.length ? estimateProgress : [0, {{ (float) $project->physical_progress }}],
                            borderColor: '#009c95',
                            backgroundColor: 'rgba(0, 156, 149, 0.12)',
                            tension: 0.35,
                        },
                        {
                            label: 'Pagado acumulado',
                            data: estimatePaid.length ? estimatePaid : [0, {{ (float) $project->financial_progress }}],
                            borderColor: '#7c3aed',
                            backgroundColor: 'rgba(124, 58, 237, 0.08)',
                            tension: 0.35,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: (value) => value + '%',
                            },
                        },
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                    },
                },
            });
        }

        const sCurveChart = document.getElementById('sCurveChart');
        if (sCurveChart && window.Chart) {
            new Chart(sCurveChart, {
                type: 'line',
                data: {
                    labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                    datasets: [
                        {
                            label: 'Programado',
                            data: [5, 12, 22, 35, 48, 60, 72, 82, 90, 96, 100, 100],
                            borderColor: '#1f8fff',
                            tension: 0.35,
                        },
                        {
                            label: 'Real',
                            data: [4, 13, 21, 32, 45, {{ (float) $project->physical_progress }}, null, null, null, null, null, null],
                            borderColor: '#009c95',
                            tension: 0.35,
                        },
                        {
                            label: 'Pagado',
                            data: [3, 10, 18, 28, 39, {{ (float) $project->financial_progress }}, null, null, null, null, null, null],
                            borderColor: '#7c3aed',
                            tension: 0.35,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: (value) => value + '%',
                            },
                        },
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                        },
                    },
                },
            });
        }
    </script>
@endpush
