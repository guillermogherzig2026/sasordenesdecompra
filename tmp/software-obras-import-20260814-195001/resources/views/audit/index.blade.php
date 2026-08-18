@extends('layouts.app')

@section('title', 'Bitacora')
@section('page-title', 'Bitacora')
@section('page-subtitle', 'Cambios relevantes registrados por usuario, modulo y accion')

@section('content')
    <div class="table-card excel-wrap">
        <div class="audit-actions">
            <button class="btn btn-aqua" type="button">
                <i data-lucide="play-circle"></i>
                Correr Auditoria
            </button>
            <button class="btn btn-soft" type="button" data-audit-criteria-open>
                <i data-lucide="list-checks"></i>
                Criterios auditoria
            </button>
        </div>
        <table class="table excel-table align-middle">
            <thead>
                <tr>
                    <th>Fecha y hora</th>
                    <th>Usuario</th>
                    <th>Modulo</th>
                    <th>Registro</th>
                    <th>Accion</th>
                    <th>Valor anterior</th>
                    <th>Valor nuevo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->occurred_at?->format('d/m/Y H:i') }}</td>
                        <td>{{ $log->user?->name ?? 'Sistema' }}</td>
                        <td>{{ $log->module }}</td>
                        <td>{{ class_basename($log->record_type) }} #{{ $log->record_id }}</td>
                        <td>{{ $log->action }}</td>
                        <td><code>{{ json_encode($log->old_values, JSON_UNESCAPED_UNICODE) }}</code></td>
                        <td><code>{{ json_encode($log->new_values, JSON_UNESCAPED_UNICODE) }}</code></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="audit-criteria-modal" data-audit-criteria-modal hidden>
        <div class="audit-criteria-dialog" role="dialog" aria-modal="true" aria-labelledby="auditCriteriaTitle">
            <div class="audit-criteria-header">
                <div>
                    <h2 class="audit-criteria-title" id="auditCriteriaTitle">Criterios de auditoria</h2>
                    <p class="audit-criteria-subtitle">Puntos que se revisan antes de correr una auditoria de obra.</p>
                </div>
                <button class="btn btn-soft btn-sm" type="button" data-audit-criteria-close>Cerrar</button>
            </div>
            <div class="audit-criteria-body">
                @foreach([
                    'Avance fisico contra programa' => 'Comparar avance programado y avance real por obra, partida y semana. Marcar desviaciones mayores al 10%.',
                    'Pagos contra avance' => 'Validar que el porcentaje pagado no sea mayor al avance fisico real ni quede rezagado sin justificacion documental.',
                    'Estimaciones y retenciones' => 'Revisar estimaciones vencidas, autorizaciones pendientes, retenciones aplicadas y retenciones por liberar.',
                    'Materiales, compras y almacen' => 'Detectar consumos mayores a presupuesto, materiales sin movimiento, faltantes, entradas sin orden y diferencias de inventario.',
                    'Nomina y mano de obra' => 'Comparar jornales, horas extra, destajos y pagos contra alcances semanales y evidencia registrada.',
                    'Documentos y autorizaciones' => 'Confirmar contratos, facturas, comprobantes, fotografias, incidencias y cambios con soporte vigente.',
                ] as $label => $criterion)
                    <div class="audit-criteria-row">
                        <span class="audit-criteria-index">{{ $loop->iteration }}</span>
                        <div>
                            <label class="audit-criteria-label">{{ $label }}</label>
                            <textarea class="form-control audit-criteria-input" disabled>{{ $criterion }}</textarea>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="audit-criteria-footer">
                <span class="small text-muted">Los criterios editados aplican para la siguiente corrida.</span>
                <button class="btn btn-aqua" type="button" data-audit-criteria-edit>
                    <i data-lucide="pencil"></i>
                    Editar criterios
                </button>
            </div>
        </div>
    </div>
@endsection
