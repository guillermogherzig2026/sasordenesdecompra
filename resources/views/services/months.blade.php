@extends('layouts.app')

@section('body')
    @php
        $pageTitle = $title ?? 'Servicios por mes';
        $downloadReport = $downloadReport ?? 'services-months-excel';
        $monthSubtitle = $monthSubtitle ?? 'Servicios ordenados de la fecha de corte mas proxima a la mas tardia.';
        $emptyMessage = $emptyMessage ?? 'No hay servicios programados para este mes.';
        $historyMode = $historyMode ?? false;
        $metricLabels = $metricLabels ?? [
            'total' => 'Monto total mensual',
            'paid' => 'Monto total pagado',
            'next_week' => 'Monto a pagar la proxima semana',
            'pending' => 'Monto pendiente por pagar',
        ];
        $expandedMonthKey = request()->query('month');
    @endphp

    <x-app-shell :title="$pageTitle">
        @if ($downloadReport)
            <x-slot:actions>
                <a class="button ghost" href="{{ route('reports.download', $downloadReport) }}">Descarga para filtrar</a>
            </x-slot:actions>
        @endif
        @foreach ($months as $month)
            @php
                $monthDetailId = 'service-month-detail-' . $month['month_key'];
                $showDemoPayment = $historyMode && ($demoPaymentMonth ?? null) === $month['month_key'];
                $isExpanded = $expandedMonthKey ? $month['month_key'] === $expandedMonthKey : $loop->first;
            @endphp
            <section @class(['panel service-month-panel', 'service-history-month-panel' => $historyMode]) data-month-panel data-month-key="{{ $month['month_key'] }}">
                <div class="panel-header service-month-header service-month-header-compact">
                    <button class="month-toggle" type="button" data-month-toggle
                        aria-controls="{{ $monthDetailId }}" aria-expanded="{{ $isExpanded ? 'true' : 'false' }}">
                        <span class="month-toggle-sign" aria-hidden="true">{{ $isExpanded ? '-' : '+' }}</span>
                        <span class="month-toggle-copy">
                            <h2>{{ $month['label'] }} <span class="month-total-inline">Total:
                                    ${{ number_format((float) $month['total'], 0) }}</span></h2>
                            <span class="fine-print">{{ $monthSubtitle }}</span>
                        </span>
                    </button>
                    <div class="month-summary-metrics">
                        <article class="metric-card compact-metric">
                            <span>{{ $metricLabels['total'] }}</span>
                            <strong>${{ number_format((float) $month['total'], 2) }}</strong>
                        </article>

                        <article class="metric-card compact-metric">
                            <span>{{ $metricLabels['paid'] }}</span>
                            <strong>${{ number_format((float) $month['paid_total'], 2) }}</strong>
                        </article>

                        <article class="metric-card compact-metric">
                            <span>{{ $metricLabels['next_week'] }}</span>
                            <strong>${{ number_format((float) $next_week_total, 2) }}</strong>
                        </article>

                        <article class="metric-card compact-metric">
                            <span>{{ $metricLabels['pending'] }}</span>
                            <strong>${{ number_format((float) $month['pending_total'], 2) }}</strong>
                        </article>
                    </div>
                </div>

                <div id="{{ $monthDetailId }}" class="service-month-detail" @if (! $isExpanded) hidden @endif>
                    @if ($showDemoPayment)
                        <p class="service-demo-notice">Pago de demostracion. No es un movimiento real y no se incluye en los totales ni reportes.</p>
                    @endif
                    <div class="table-scroll service-month-scroll">
                        <table class="service-month-table" data-column-filter-table>
                            <thead>
                                <tr>
                                    <th data-filter-column="0"><span>ID</span></th>
                                    <th data-filter-column="1"><span>Titular</span></th>
                                    <th data-filter-column="2"><span>Sucursal</span></th>
                                    <th data-filter-column="3"><span>Servicio</span></th>
                                    <th data-filter-column="4"><span>Banco</span></th>
                                    <th data-filter-column="5"><span>Cuenta pagadora</span></th>
                                    <th data-filter-column="6"><span>Proveedor</span></th>
                                    <th data-filter-column="7"><span>No. Servicio</span></th>
                                    <th data-filter-column="8"><span>Periodo</span></th>
                                    <th data-filter-column="9" data-filter-type="date-range"><span>Vencimiento</span></th>
                                    <th data-filter-column="10"><span>Monto</span></th>
                                    <th data-filter-column="11"><span>Referencia</span></th>
                                    <th data-filter-column="12"><span>Estado</span></th>
                                    <th data-filter-column="13"><span>Factura</span></th>
                                    <th data-filter-column="14"><span>Comprobante pago</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($showDemoPayment)
                                    <tr class="service-row-locked service-demo-payment-row" data-filter-row data-demo-row>
                                        <td data-filter-value="DEMO-SEP-2026"><strong>DEMO-SEP-2026</strong></td>
                                        <td data-filter-value="Empresa de ejemplo">Empresa de ejemplo</td>
                                        <td data-filter-value="Sucursal demo">Sucursal demo</td>
                                        <td data-filter-value="Internet empresarial (demo)">Internet empresarial (demo)</td>
                                        <td data-filter-value="Banco de ejemplo">Banco de ejemplo</td>
                                        <td data-filter-value="No aplica">No aplica</td>
                                        <td data-filter-value="Proveedor de ejemplo">Proveedor de ejemplo</td>
                                        <td data-filter-value="DEMO-001">DEMO-001</td>
                                        <td data-filter-value="01/08/2026 al 31/08/2026">01/08 al 31/08</td>
                                        <td data-filter-value="18/09/2026" data-filter-date="2026-09-18">18/09/2026</td>
                                        <td data-filter-value="$1,250.00">$1,250.00</td>
                                        <td data-filter-value="PAGO-DEMO-SEP-2026">PAGO-DEMO-SEP-2026</td>
                                        <td data-filter-value="Pagado (demo)">
                                            <button class="status primary service-payment-trigger" type="button"
                                                data-payment-preview
                                                data-dialog-title="Comprobante de pago"
                                                data-payment-title="Internet empresarial (demo)"
                                                data-preview-kind="demo"
                                                data-preview-url="{{ route('services.demo-payment-file') }}"
                                                data-download-url="{{ route('services.demo-payment-file', ['download' => 1]) }}">
                                                Pagado (demo)
                                            </button>
                                        </td>
                                        <td data-filter-value="Ver">
                                            <button class="attachment-pill service-payment-trigger" type="button"
                                                data-payment-preview
                                                data-dialog-title="Factura"
                                                aria-label="Ver factura de ejemplo"
                                                title="Factura de ejemplo"
                                                data-payment-title="Internet empresarial (demo)"
                                                data-preview-kind="invoice-demo"
                                                data-preview-url="{{ route('services.demo-invoice-file') }}"
                                                data-download-url="{{ route('services.demo-invoice-file', ['download' => 1]) }}">
                                                Ver
                                            </button>
                                        </td>
                                        <td data-filter-value="Ver">
                                            <button class="attachment-pill service-payment-trigger" type="button"
                                                data-payment-preview
                                                data-dialog-title="Comprobante de pago"
                                                aria-label="Ver comprobante de pago de ejemplo"
                                                title="Comprobante de ejemplo"
                                                data-payment-title="Internet empresarial (demo)"
                                                data-preview-kind="demo"
                                                data-preview-url="{{ route('services.demo-payment-file') }}"
                                                data-download-url="{{ route('services.demo-payment-file', ['download' => 1]) }}">
                                                Ver
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                                @unless ($historyMode)
                                    @foreach ($month['occasional_payments'] as $payment)
                                        <tr data-filter-row class="service-occasional-payment-row">
                                            <td data-filter-value="OCP-{{ $payment->id }}"><strong>OCP-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                            <td data-filter-value="{{ $payment->entry->name }}">{{ $payment->entry->name }}</td>
                                            <td data-filter-value="Sin sucursal">-</td>
                                            <td data-filter-value="{{ $payment->entry->category->name }} (ocasional)">{{ $payment->entry->category->name }} (ocasional)</td>
                                            <td data-filter-value="Sin banco">-</td>
                                            <td data-filter-value="Sin cuenta">-</td>
                                            <td data-filter-value="Sin proveedor">-</td>
                                            <td data-filter-value="{{ $payment->entry->reference_number }}">{{ $payment->entry->reference_number }}</td>
                                            <td data-filter-value="Pago único">Pago único</td>
                                            <td data-filter-value="{{ $payment->due_date->format('d/m/Y') }}" data-filter-date="{{ $payment->due_date->toDateString() }}">{{ $payment->due_date->format('d/m/Y') }}</td>
                                            <td data-filter-value="${{ number_format((float) $payment->amount, 2) }}">${{ number_format((float) $payment->amount, 2) }}</td>
                                            <td data-filter-value="{{ $payment->entry->reference_number }}">{{ $payment->entry->reference_number }}</td>
                                            <td data-filter-value="Pendiente"><span class="status pending">Pendiente</span></td>
                                            <td data-filter-value="Sin Archivo">Sin Archivo</td>
                                            <td data-filter-value="Sin Archivo">Sin Archivo</td>
                                        </tr>
                                    @endforeach
                                @endunless
                                @forelse ($month['items'] as $item)
                                    @php
                                        $service = $item['service'];
                                        $receipt = $item['receipt'];
                                        $isDomiciled = $service->is_domiciled;
                                        $paid = filled($receipt?->payment_file_path);
                                        $hasSupport = filled($receipt?->support_file_path);
                                        $statusText = $isDomiciled
                                            ? 'DOM'
                                            : ($paid
                                                ? 'Pagado'
                                                : ($hasSupport
                                                    ? 'Listo para pago'
                                                    : 'Pendiente'));
                                        $statusClass = $isDomiciled
                                            ? 'domiciled'
                                            : ($paid
                                                ? 'paid'
                                                : ($hasSupport
                                                    ? 'approved'
                                                    : 'pending'));
                                        $periodAmount = (float) ($receipt?->amount ?? $service->cost);
                                        $paymentFilename = $receipt?->payment_original_name ?: ($receipt?->payment_file_path ?? '');
                                        $paymentExtension = strtolower(pathinfo($paymentFilename, PATHINFO_EXTENSION));
                                        $paymentPreviewKind = $paymentExtension === 'pdf' ? 'pdf' : (in_array($paymentExtension, ['jpg', 'jpeg', 'png', 'webp'], true) ? 'image' : 'other');
                                        $supportFilename = $receipt?->support_original_name ?: ($receipt?->support_file_path ?? '');
                                        $supportExtension = strtolower(pathinfo($supportFilename, PATHINFO_EXTENSION));
                                        $supportPreviewKind = $supportExtension === 'pdf' ? 'pdf' : (in_array($supportExtension, ['jpg', 'jpeg', 'png', 'webp'], true) ? 'image' : 'other');
                                        $paymentDueDate = \Illuminate\Support\Carbon::parse(
                                            $item['payment_due_date'] ?? $item['due_date'],
                                        );
                                    @endphp
                                    <tr data-filter-row @class(['service-row-locked' => $historyMode || ($paid && !$isDomiciled)])>
                                        <td data-filter-value="{{ $item['service']->folio }}">
                                            <strong>{{ $item['service']->folio }}</strong>
                                        </td>
                                        <td
                                            data-filter-value="{{ $item['service']->holder ?: $item['service']->company_name }}">
                                            {{ $item['service']->holder ?: $item['service']->company_name }}</td>
                                        <td data-filter-value="{{ $item['service']->display_branch }}">
                                            {{ $item['service']->display_branch }}</td>
                                        <td data-filter-value="{{ $item['service']->service_name }}">
                                            {{ $item['service']->service_name }}</td>
                                        <td data-filter-value="{{ $item['service']->bank }}">{{ $item['service']->bank }}
                                        </td>
                                        <td data-filter-value="{{ $item['service']->payer_account }}">
                                            {{ $item['service']->payer_account }}</td>
                                        <td data-filter-value="{{ $item['service']->provider }}">
                                            {{ $item['service']->provider }}</td>
                                        <td data-filter-value="{{ $item['service']->service_number }}">
                                            {{ $item['service']->service_number }}</td>
                                        <td
                                            data-filter-value="{{ \Illuminate\Support\Carbon::parse($item['period_start'])->format('d/m/Y') }} al {{ \Illuminate\Support\Carbon::parse($item['due_date'])->format('d/m/Y') }}">
                                            {{ \Illuminate\Support\Carbon::parse($item['period_start'])->format('d/m') }}
                                            al {{ \Illuminate\Support\Carbon::parse($item['due_date'])->format('d/m') }}
                                        </td>
                                        <td data-filter-value="{{ $paymentDueDate->format('d/m/Y') }}"
                                            data-filter-date="{{ $paymentDueDate->format('Y-m-d') }}">
                                            {{ $paymentDueDate->format('d/m/Y') }}</td>
                                        <td data-filter-value="${{ number_format($periodAmount, 2) }}">
                                            @if ($historyMode || ($paid && !$isDomiciled))
                                                ${{ number_format($periodAmount, 2) }}
                                            @else
                                                <form class="amount-edit" method="POST"
                                                    action="{{ route('services.amount.update', [$item['service'], $item['due_date']]) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input name="amount" type="number" min="0" step="0.01"
                                                        value="{{ number_format($periodAmount, 2, '.', '') }}" required>
                                                    <button class="button ghost small" type="submit">Guardar</button>
                                                </form>
                                            @endif
                                        </td>
                                        <td data-filter-value="{{ $item['service']->reference }}">
                                            {{ $item['service']->reference }}</td>
                                        <td data-filter-value="{{ $statusText }}">
                                            @if ($paid && ! $isDomiciled)
                                                <button class="status paid service-payment-trigger" type="button"
                                                    data-payment-preview
                                                    data-dialog-title="Comprobante de pago"
                                                    data-payment-title="{{ $service->service_name }}"
                                                    data-payment-filename="{{ $paymentFilename }}"
                                                    data-preview-kind="{{ $paymentPreviewKind }}"
                                                    data-preview-url="{{ route('services.payment-file', $receipt) }}"
                                                    data-download-url="{{ route('services.payment-file.pdf', $receipt) }}">
                                                    Pagado
                                                </button>
                                            @else
                                                <details class="status-menu">
                                                    <summary class="status {{ $statusClass }}">{{ $statusText }}</summary>
                                                    <div class="status-menu-panel">
                                                        <span
                                                            class="fine-print">{{ $isDomiciled ? 'Pago domiciliado automatico' : ($paid ? 'Bloqueado por pago' : 'Estado de factura') }}</span>
                                                    </div>
                                                </details>
                                            @endif
                                        </td>

                                        <td data-filter-value="{{ $supportFilename ?: 'Sin Archivo' }}">
                                            @if ($hasSupport)
                                                <div class="item-actions">
                                                    <button class="attachment-pill service-payment-trigger" type="button"
                                                        data-payment-preview
                                                        data-dialog-title="Factura"
                                                        aria-label="Ver factura {{ $supportFilename }}"
                                                        title="{{ $supportFilename }}"
                                                        data-payment-title="{{ $service->service_name }}"
                                                        data-payment-filename="{{ $supportFilename }}"
                                                        data-preview-kind="{{ $supportPreviewKind }}"
                                                        data-preview-url="{{ route('services.support-file', $receipt) }}"
                                                        data-download-url="{{ route('services.support-file.pdf', $receipt) }}">Ver</button>
                                                    @if (! $historyMode && $item['service']->status !== 'inactive' && (!$paid || $isDomiciled))
                                                        <a class="button ghost small"
                                                            href="{{ route('services.receipt', [$item['service'], $item['due_date']]) }}">Cambiar
                                                            factura</a>
                                                    @elseif ($paid)
                                                        <span class="fine-print">Bloqueado por pago</span>
                                                    @endif
                                                </div>
                                            @else
                                                @if (! $historyMode && $item['service']->status !== 'inactive')
                                                    <a class="button primary small"
                                                        href="{{ route('services.receipt', [$item['service'], $item['due_date']]) }}">Subir
                                                        factura</a>
                                                @else
                                                    Sin Archivo
                                                @endif
                                            @endif
                                        </td>
                                        <td data-filter-value="{{ $paid ? $paymentFilename : 'Sin Archivo' }}">
                                            @if ($paid)
                                                <button class="attachment-pill service-payment-trigger" type="button"
                                                    data-payment-preview
                                                    data-dialog-title="Comprobante de pago"
                                                    aria-label="Ver comprobante de pago {{ $paymentFilename }}"
                                                    title="{{ $paymentFilename }}"
                                                    data-payment-title="{{ $service->service_name }}"
                                                    data-payment-filename="{{ $paymentFilename }}"
                                                    data-preview-kind="{{ $paymentPreviewKind }}"
                                                    data-preview-url="{{ route('services.payment-file', $receipt) }}"
                                                    data-download-url="{{ route('services.payment-file.pdf', $receipt) }}">Ver</button>
                                            @else
                                                Sin Archivo
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    @unless ($showDemoPayment || (! $historyMode && $month['occasional_payments']->isNotEmpty()))
                                        <tr>
                                            <td colspan="15">{{ $emptyMessage }}</td>
                                        </tr>
                                    @endunless
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        @endforeach
    </x-app-shell>

    @if (! $historyMode && $canCreateOccasionalPayment)
        <dialog class="occasional-payment-dialog" data-occasional-payment-dialog aria-labelledby="occasional-payment-title"
            @if (request()->boolean('pay_occasional') || (old('_service_form') === 'occasional_payment' && $errors->any())) data-auto-open @endif>
            <form method="POST" action="{{ route('services.occasional.payments.store') }}">
                @csrf
                <input type="hidden" name="_service_form" value="occasional_payment">
                <header class="service-payment-dialog-header">
                    <h2 id="occasional-payment-title">Pagar servicio ocasional</h2>
                    <button class="service-payment-close" type="button" data-occasional-payment-close aria-label="Cerrar" title="Cerrar">&times;</button>
                </header>
                <div class="occasional-payment-fields">
                    @if (old('_service_form') === 'occasional_payment' && $errors->any())
                        <div class="error-list" role="alert">
                            <strong>Revisa los datos capturados.</strong>
                            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif
                    @php $paymentMode = old('mode') === 'new' || $occasional_entries->isEmpty() ? 'new' : 'existing'; @endphp
                    <div class="occasional-payment-modes" role="group" aria-label="Origen del servicio ocasional">
                        <label>
                            <input type="radio" name="mode" value="existing" @checked($paymentMode === 'existing') @disabled($occasional_entries->isEmpty())>
                            <span>Registrado</span>
                        </label>
                        <label>
                            <input type="radio" name="mode" value="new" @checked($paymentMode === 'new')>
                            <span>Nuevo servicio</span>
                        </label>
                    </div>
                    <fieldset class="occasional-payment-source" data-payment-existing @if ($paymentMode === 'new') hidden disabled @endif>
                        <label for="occasional-payment-entry">Servicio ocasional
                            <select id="occasional-payment-entry" name="occasional_service_entry_id" required>
                                <option value="">Seleccionar servicio</option>
                                @foreach ($occasional_entries as $entry)
                                    <option value="{{ $entry->id }}" @selected((string) old('occasional_service_entry_id') === (string) $entry->id)>{{ $entry->category->name }} - {{ $entry->name }} - {{ $entry->reference_number }}</option>
                                @endforeach
                            </select>
                        </label>
                    </fieldset>
                    <fieldset class="occasional-payment-source" data-payment-new @if ($paymentMode === 'existing') hidden disabled @endif>
                        <label for="occasional-payment-category">Categoría
                            <select id="occasional-payment-category" name="category_id" required>
                                <option value="">Seleccionar categoría</option>
                                @foreach ($occasional_categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label for="occasional-payment-name">Nombre del servicio/Titular
                            <input id="occasional-payment-name" name="name" maxlength="100" value="{{ old('name') }}" required>
                        </label>
                        <label for="occasional-payment-reference">Número de referencia
                            <input id="occasional-payment-reference" name="reference_number" maxlength="100" value="{{ old('reference_number') }}" required>
                        </label>
                    </fieldset>
                    <div class="occasional-payment-grid">
                        <label for="occasional-payment-amount">Monto (MXN)
                            <input id="occasional-payment-amount" name="amount" type="number" min="0.01" max="9999999999.99" step="0.01" value="{{ old('amount') }}" required>
                        </label>
                        <label for="occasional-payment-date">Fecha prevista de pago
                            <input id="occasional-payment-date" name="due_date" type="date" min="{{ now()->toDateString() }}" max="{{ now()->addMonthsNoOverflow(12)->endOfMonth()->toDateString() }}" value="{{ old('due_date', now()->toDateString()) }}" required>
                        </label>
                    </div>
                </div>
                <footer class="service-payment-dialog-footer">
                    <button class="button ghost" type="button" data-occasional-payment-close>Cancelar</button>
                    <button class="button primary" type="submit" @disabled($occasional_entries->isEmpty() && $occasional_categories->isEmpty())>Agregar a Servicios Mensuales</button>
                </footer>
            </form>
        </dialog>
    @endif

    <dialog class="service-payment-dialog" data-payment-dialog aria-labelledby="service-payment-dialog-title">
        <header class="service-payment-dialog-header">
            <div>
                <h2 id="service-payment-dialog-title">Comprobante de pago</h2>
                <p class="fine-print" data-payment-dialog-subtitle></p>
            </div>
            <button class="service-payment-close" type="button" data-payment-close aria-label="Cerrar ventana" title="Cerrar">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </header>
        <div class="service-payment-preview">
            <div class="service-payment-original" data-payment-original-bar hidden>
                <span data-payment-original-name></span>
                <a data-payment-original-link href="#" target="_blank" rel="noopener">Abrir archivo original</a>
            </div>
            @if ($historyMode && filled($demoPaymentMonth ?? null))
                <div class="service-payment-demo" data-invoice-demo hidden>
                    <div class="service-payment-demo-mark">OC System</div>
                    <h3>Factura de ejemplo</h3>
                    <p class="service-payment-demo-warning">DEMOSTRACIÓN - NO ES CFDI NI TIENE VALIDEZ FISCAL</p>
                    <dl>
                        <div><dt>Folio de servicio</dt><dd>DEMO-SEP-2026</dd></div>
                        <div><dt>Titular</dt><dd>Empresa de ejemplo</dd></div>
                        <div><dt>Sucursal</dt><dd>Sucursal demo</dd></div>
                        <div><dt>Servicio</dt><dd>Internet empresarial (demo)</dd></div>
                        <div><dt>Proveedor</dt><dd>Proveedor de ejemplo</dd></div>
                        <div><dt>Vencimiento</dt><dd>18/09/2026</dd></div>
                        <div><dt>Monto</dt><dd>$1,250.00 MXN</dd></div>
                    </dl>
                    <p class="fine-print">Esta vista es una muestra visual. No sustituye una factura emitida por el proveedor.</p>
                </div>
                <div class="service-payment-demo" data-payment-demo hidden>
                    <div class="service-payment-demo-mark">OC System</div>
                    <h3>Comprobante de pago</h3>
                    <p class="service-payment-demo-warning">DEMOSTRACIÓN - SIN VALIDEZ COMO COMPROBANTE DE PAGO</p>
                    <dl>
                        <div><dt>Folio</dt><dd>DEMO-SEP-2026</dd></div>
                        <div><dt>Titular</dt><dd>Empresa de ejemplo</dd></div>
                        <div><dt>Sucursal</dt><dd>Sucursal demo</dd></div>
                        <div><dt>Servicio</dt><dd>Internet empresarial (demo)</dd></div>
                        <div><dt>Proveedor</dt><dd>Proveedor de ejemplo</dd></div>
                        <div><dt>Fecha de pago</dt><dd>18/09/2026</dd></div>
                        <div><dt>Referencia</dt><dd>PAGO-DEMO-SEP-2026</dd></div>
                        <div><dt>Monto</dt><dd>$1,250.00 MXN</dd></div>
                    </dl>
                    <p class="fine-print">Este documento es únicamente una muestra. No registra ni acredita un pago real.</p>
                </div>
            @endif
            <iframe data-payment-frame title="Vista previa del documento" hidden></iframe>
            <img data-payment-image alt="Documento adjunto" hidden>
            <p data-payment-unavailable hidden>No hay vista previa disponible para este tipo de archivo. Puedes abrir el archivo original o descargar un resumen en PDF.</p>
        </div>
        <footer class="service-payment-dialog-footer">
            <a class="button primary" data-payment-download href="#" title="Descargar PDF" aria-label="Descargar PDF">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/><path d="M12 15V3"/></svg>
                Descargar PDF
            </a>
        </footer>
    </dialog>

    <style>
        .service-month-panel:not(.service-history-month-panel) .month-toggle h2 { font-size: 1.2rem; }
        .occasional-payment-dialog { border: 1px solid var(--line); border-radius: 8px; box-shadow: 0 24px 64px rgba(16, 43, 58, .24); color: var(--text); max-height: calc(100dvh - 32px); padding: 0; width: min(560px, calc(100vw - 32px)); }
        .occasional-payment-dialog::backdrop { background: rgba(16, 43, 58, .55); }
        .occasional-payment-dialog form { display: flex; flex-direction: column; max-height: calc(100dvh - 32px); }
        .occasional-payment-fields { display: grid; gap: 16px; overflow-y: auto; padding: 20px; }
        .occasional-payment-fields label { display: grid; gap: 6px; font-size: .88rem; font-weight: 800; min-width: 0; }
        .occasional-payment-fields input, .occasional-payment-fields select { background: #fff; border: 1px solid var(--line); border-radius: 6px; color: var(--text); font: inherit; font-weight: 500; min-height: 40px; min-width: 0; padding: 8px 10px; width: 100%; }
        .occasional-payment-modes { border: 1px solid var(--line); border-radius: 6px; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); padding: 3px; }
        .occasional-payment-modes label { cursor: pointer; display: block; }
        .occasional-payment-modes input { position: absolute; opacity: 0; width: 1px; height: 1px; min-height: 0; }
        .occasional-payment-modes span { border-radius: 4px; display: block; padding: 8px; text-align: center; }
        .occasional-payment-modes input:checked + span { background: #e5f3f7; color: var(--primary-strong); }
        .occasional-payment-modes input:focus-visible + span { outline: 2px solid var(--primary); }
        .occasional-payment-modes input:disabled + span { color: var(--muted); cursor: not-allowed; opacity: .6; }
        .occasional-payment-source { border: 0; display: grid; gap: 14px; margin: 0; min-width: 0; padding: 0; }
        .occasional-payment-grid { display: grid; gap: 14px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .occasional-payment-dialog .service-payment-dialog-footer { gap: 8px; }
        .occasional-payment-dialog button:disabled { cursor: not-allowed; opacity: .55; }
        @media (max-width: 560px) { .occasional-payment-grid { grid-template-columns: 1fr; } .occasional-payment-dialog .service-payment-dialog-footer { flex-wrap: wrap; } }
        .service-payment-trigger { cursor: pointer; font-family: inherit; }
        .service-payment-trigger:hover { filter: brightness(.96); }
        .service-payment-trigger:focus-visible, .service-payment-close:focus-visible { outline: 2px solid var(--primary); outline-offset: 2px; }
        button.attachment-pill { cursor: pointer; font: inherit; font-weight: 800; text-align: left; }
        .service-payment-dialog { border: 1px solid var(--line); border-radius: 8px; box-shadow: 0 24px 64px rgba(16, 43, 58, .24); color: var(--text); max-height: calc(100dvh - 32px); padding: 0; width: min(900px, calc(100vw - 32px)); }
        .service-payment-dialog[open] { display: flex; flex-direction: column; }
        .service-payment-dialog::backdrop { background: rgba(16, 43, 58, .55); }
        .service-payment-dialog-header { align-items: flex-start; border-bottom: 1px solid var(--line); display: flex; gap: 16px; justify-content: space-between; padding: 16px 20px; }
        .service-payment-dialog-header h2 { font-size: 1.15rem; margin: 0; }
        .service-payment-dialog-header p { margin: 4px 0 0; }
        .service-payment-close { align-items: center; background: #fff; border: 1px solid var(--line); border-radius: 6px; color: var(--text); cursor: pointer; display: inline-flex; flex: 0 0 auto; height: 34px; justify-content: center; padding: 0; width: 34px; }
        .service-payment-preview { align-items: center; background: #f1f5f9; display: flex; flex-direction: column; height: min(68dvh, 680px); justify-content: center; min-height: 280px; overflow: auto; }
        .service-payment-preview iframe { border: 0; flex: 1; min-height: 0; width: 100%; }
        .service-payment-preview img { height: auto; max-height: 100%; max-width: 100%; object-fit: contain; }
        .service-payment-preview p { color: var(--muted); max-width: 420px; padding: 24px; text-align: center; }
        .service-payment-original { align-items: center; background: #fff; border-bottom: 1px solid var(--line); color: var(--muted); display: flex; flex: 0 0 auto; font-size: .86rem; gap: 16px; justify-content: space-between; padding: 10px 16px; width: 100%; }
        .service-payment-original span { min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .service-payment-original a { color: var(--primary-strong); flex: 0 0 auto; font-weight: 800; }
        .service-payment-demo { align-self: center; background: #fff; border: 1px solid var(--line); margin: 20px; max-width: 100%; overflow: auto; padding: 28px 36px; width: 640px; }
        .service-payment-demo-mark { color: var(--primary); font-size: .82rem; font-weight: 900; text-transform: uppercase; }
        .service-payment-demo h3 { font-size: 1.55rem; margin: 8px 0 16px; }
        .service-payment-preview .service-payment-demo-warning { background: #eaf5f8; border: 1px solid var(--primary); color: var(--primary-strong); font-size: .85rem; font-weight: 850; margin: 0 0 16px; max-width: none; padding: 12px; text-align: center; }
        .service-payment-demo dl { margin: 0; }
        .service-payment-demo dl div { border-bottom: 1px solid var(--line); display: grid; gap: 12px; grid-template-columns: minmax(110px, 38%) minmax(0, 1fr); padding: 10px 0; }
        .service-payment-demo dt { color: var(--muted); }
        .service-payment-demo dd { font-weight: 800; margin: 0; overflow-wrap: anywhere; }
        .service-payment-preview .service-payment-demo .fine-print { margin: 16px 0 0; max-width: none; padding: 0; text-align: left; }
        .service-payment-dialog-footer { border-top: 1px solid var(--line); display: flex; justify-content: flex-end; padding: 14px 20px; }
        @media (max-width: 600px) { .service-payment-demo { margin: 12px; padding: 18px; } .service-payment-demo dl div { grid-template-columns: 1fr; gap: 3px; } }

        .service-demo-notice {
            margin: 0;
            padding: 9px 20px;
            border-bottom: 1px solid var(--line);
            background: #eaf5f8;
            color: var(--primary-strong);
            font-size: .84rem;
        }

        .service-month-table .column-filter summary {
            align-items: center;
            border-radius: 50%;
            display: inline-flex;
            height: 30px;
            justify-content: center;
            min-height: 30px;
            padding: 0;
            width: 30px;
        }

        .service-month-table .column-filter summary::after {
            content: none;
            margin-left: 0;
        }

        .service-month-table .column-filter[open] summary::after {
            content: none;
        }

        .service-month-table .column-filter[open] summary {
            background: var(--primary-strong);
            color: #fff;
        }

        .date-range-calendar {
            display: grid;
            gap: 12px;
            min-width: 0;
            width: 100%;
        }

        .column-filter-panel.date-range-filter-panel {
            position: fixed;
            width: 340px;
            max-height: none;
            overflow: visible;
            z-index: 2200;
        }

        .date-range-calendar-header {
            align-items: center;
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }

        .date-range-calendar-title {
            color: var(--ink);
            font-size: 1rem;
            font-weight: 900;
            text-align: center;
        }

        .date-range-nav {
            align-items: center;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 10px;
            color: var(--ink);
            cursor: pointer;
            display: inline-flex;
            font-size: 1.1rem;
            font-weight: 900;
            height: 38px;
            justify-content: center;
            width: 38px;
        }

        .date-range-weekdays,
        .date-range-days {
            display: grid;
            grid-template-columns: repeat(7, minmax(32px, 1fr));
            text-align: center;
        }

        .date-range-weekdays span {
            color: var(--muted);
            font-size: .78rem;
            font-weight: 900;
            padding: 6px 0;
        }

        .date-range-day {
            background: transparent;
            border: 0;
            border-radius: 0;
            color: var(--ink);
            cursor: pointer;
            font-weight: 800;
            min-height: 38px;
            padding: 0;
        }

        .date-range-day.is-muted {
            color: #9aa6b2;
        }

        .date-range-day.is-in-range {
            background: #eaf3ff;
        }

        .date-range-day.is-selected {
            background: #1479ff;
            border-radius: 9px;
            color: #fff;
        }

        .date-range-summary {
            color: var(--muted);
            font-size: .8rem;
            font-weight: 800;
        }

        .date-range-sort {
            border-bottom: 1px solid var(--line);
            display: grid;
            gap: 4px;
            padding-bottom: 10px;
        }

        .date-range-sort button {
            background: transparent;
            border: 0;
            border-radius: 7px;
            color: var(--ink);
            cursor: pointer;
            font-size: .8rem;
            font-weight: 800;
            padding: 7px 6px;
            text-align: left;
            width: 100%;
        }

        .date-range-sort button:hover {
            background: #f3f6fa;
        }

        .overdue-filter-button {
            justify-content: center;
            width: 100%;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const occasionalPaymentDialog = document.querySelector('[data-occasional-payment-dialog]');
            const paymentModes = occasionalPaymentDialog?.querySelectorAll('input[name="mode"]') ?? [];
            const syncPaymentMode = () => {
                const isNew = occasionalPaymentDialog?.querySelector('input[name="mode"]:checked')?.value === 'new';
                const existingFields = occasionalPaymentDialog?.querySelector('[data-payment-existing]');
                const newFields = occasionalPaymentDialog?.querySelector('[data-payment-new]');
                if (!existingFields || !newFields) return;
                existingFields.hidden = isNew;
                existingFields.disabled = isNew;
                newFields.hidden = !isNew;
                newFields.disabled = !isNew;
            };
            paymentModes.forEach((mode) => mode.addEventListener('change', syncPaymentMode));
            syncPaymentMode();
            if (occasionalPaymentDialog?.hasAttribute('data-auto-open')) occasionalPaymentDialog.showModal();
            occasionalPaymentDialog?.querySelectorAll('[data-occasional-payment-close]').forEach((button) => {
                button.addEventListener('click', () => occasionalPaymentDialog.close());
            });
            occasionalPaymentDialog?.addEventListener('click', (event) => {
                if (event.target === occasionalPaymentDialog) occasionalPaymentDialog.close();
            });
            occasionalPaymentDialog?.addEventListener('close', () => {
                const url = new URL(window.location.href);
                if (url.searchParams.has('pay_occasional')) {
                    url.searchParams.delete('pay_occasional');
                    window.history.replaceState(null, '', url);
                }
            });
            const paymentDialog = document.querySelector('[data-payment-dialog]');
            const paymentFrame = paymentDialog?.querySelector('[data-payment-frame]');
            const paymentImage = paymentDialog?.querySelector('[data-payment-image]');
            const paymentUnavailable = paymentDialog?.querySelector('[data-payment-unavailable]');
            const paymentDownload = paymentDialog?.querySelector('[data-payment-download]');
            const paymentDemo = paymentDialog?.querySelector('[data-payment-demo]');
            const invoiceDemo = paymentDialog?.querySelector('[data-invoice-demo]');
            const paymentOriginalBar = paymentDialog?.querySelector('[data-payment-original-bar]');
            const paymentOriginalLink = paymentDialog?.querySelector('[data-payment-original-link]');

            document.querySelectorAll('[data-payment-preview]').forEach((trigger) => {
                trigger.addEventListener('click', () => {
                    if (!paymentDialog || !trigger.dataset.previewUrl || !trigger.dataset.downloadUrl) return;

                    const kind = trigger.dataset.previewKind;
                    paymentDialog.querySelector('#service-payment-dialog-title').textContent = trigger.dataset.dialogTitle || 'Comprobante de pago';
                    paymentDialog.querySelector('[data-payment-dialog-subtitle]').textContent = trigger.dataset.paymentTitle || '';
                    paymentDownload.href = trigger.dataset.downloadUrl;
                    if (paymentDemo) paymentDemo.hidden = kind !== 'demo';
                    if (invoiceDemo) invoiceDemo.hidden = kind !== 'invoice-demo';
                    paymentFrame.hidden = kind !== 'pdf';
                    paymentImage.hidden = kind !== 'image';
                    paymentUnavailable.hidden = kind !== 'other';
                    paymentOriginalBar.hidden = kind === 'demo' || kind === 'invoice-demo';
                    paymentOriginalBar.querySelector('[data-payment-original-name]').textContent = trigger.dataset.paymentFilename || 'Documento adjunto';
                    paymentOriginalLink.href = trigger.dataset.previewUrl;
                    if (kind === 'pdf') paymentFrame.src = trigger.dataset.previewUrl;
                    if (kind === 'image') paymentImage.src = trigger.dataset.previewUrl;
                    paymentDialog.showModal();
                });
            });

            paymentDialog?.querySelector('[data-payment-close]')?.addEventListener('click', () => paymentDialog.close());
            paymentDialog?.addEventListener('click', (event) => {
                if (event.target === paymentDialog) paymentDialog.close();
            });
            paymentDialog?.addEventListener('close', () => {
                paymentFrame.src = 'about:blank';
                paymentImage.removeAttribute('src');
                paymentDownload.removeAttribute('href');
                paymentOriginalLink.removeAttribute('href');
            });

            document.querySelectorAll('[data-month-toggle]').forEach((toggle) => {
                toggle.addEventListener('click', () => {
                    const panel = toggle.closest('[data-month-panel]');
                    const detail = panel?.querySelector('.service-month-detail');
                    if (!detail) return;

                    const isOpen = toggle.getAttribute('aria-expanded') === 'true';
                    toggle.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                    toggle.querySelector('.month-toggle-sign').textContent = isOpen ? '+' : '-';
                    detail.hidden = isOpen;
                });
            });

            const normalize = (value) => (value || '').replace(/\s+/g, ' ').trim();
            const normalizeSearch = (value) => normalize(value).toLocaleLowerCase('es-MX')
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            const closeColumnFilters = (except = null) => {
                document.querySelectorAll('.column-filter[open]').forEach((filter) => {
                    if (filter !== except) {
                        filter.removeAttribute('open');
                    }
                });
            };
            const parseDateKey = (key) => {
                const [year, month, day] = (key || '').split('-').map(Number);
                return new Date(year, month - 1, day);
            };
            const toDateKey = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };
            const formatHumanDate = (key) => {
                if (!key) return '';
                const [year, month, day] = key.split('-');
                return `${day}/${month}/${year}`;
            };
            const monthTitle = (date) => {
                const label = new Intl.DateTimeFormat('es-MX', {
                    month: 'long',
                    year: 'numeric'
                }).format(date);
                return label.charAt(0).toUpperCase() + label.slice(1);
            };
            const positionFilterPanel = (panel, trigger) => {
                const rect = trigger.getBoundingClientRect();
                const gap = 8;
                const margin = 12;
                const width = panel.offsetWidth || 340;
                const height = panel.offsetHeight || 360;
                let left = rect.right - width;
                let top = rect.bottom + gap;

                left = Math.max(margin, Math.min(left, window.innerWidth - width - margin));

                if (top + height > window.innerHeight - margin) {
                    top = Math.max(margin, rect.top - height - gap);
                }

                panel.style.left = `${left}px`;
                panel.style.top = `${top}px`;
            };

            document.addEventListener('click', (event) => {
                if (!event.target.closest('.column-filter')) {
                    closeColumnFilters();
                }
            });

            document.querySelectorAll('[data-column-filter-table]').forEach((table) => {
                const rows = Array.from(table.querySelectorAll('tbody tr[data-filter-row]'));
                const headers = Array.from(table.querySelectorAll('thead th[data-filter-column]'));
                const filters = new Map();

                const applyFilters = () => {
                    rows.forEach((row) => {
                        const visible = headers.every((header) => {
                            const column = Number(header.dataset.filterColumn);
                            const selected = filters.get(column);

                            if (!selected) {
                                return true;
                            }

                            const cell = row.cells[column];

                            if (selected.type === 'date-range') {
                                const value = normalize(cell?.dataset.filterDate);

                                if (!value) {
                                    return false;
                                }

                                return (!selected.from || value >= selected.from) && (!
                                    selected.to || value <= selected.to);
                            }

                            const value = normalize(cell?.dataset.filterValue || cell
                                ?.textContent);

                            return selected.has(value);
                        });

                        row.hidden = !visible;
                    });
                };

                const sortRowsByDate = (column, direction) => {
                    const sortedRows = [...rows].sort((leftRow, rightRow) => {
                        const leftDate = normalize(
                            leftRow.cells[column]?.dataset.filterDate
                        );

                        const rightDate = normalize(
                            rightRow.cells[column]?.dataset.filterDate
                        );

                        if (direction === 'asc') {
                            return leftDate.localeCompare(rightDate);
                        }

                        return rightDate.localeCompare(leftDate);
                    });

                    sortedRows.forEach((row) => {
                        table.tBodies[0].appendChild(row);
                    });
                };

                headers.forEach((header) => {
                    const column = Number(header.dataset.filterColumn);
                    const label = normalize(header.textContent);
                    const values = [...new Set(rows.map((row) => {
                        const cell = row.cells[column];
                        return normalize(cell?.dataset.filterValue || cell
                            ?.textContent);
                    }).filter(Boolean))].sort((a, b) => a.localeCompare(b, 'es'));

                    const wrapper = document.createElement('div');
                    wrapper.className = 'th-filter';

                    const text = document.createElement('span');
                    text.textContent = label;

                    const details = document.createElement('details');
                    details.className = 'column-filter';

                    const summary = document.createElement('summary');
                    summary.textContent = 'v';
                    summary.title = `Filtrar ${label}`;
                    summary.setAttribute('aria-label', `Filtrar ${label}`);
                    summary.addEventListener('click', () => {
                        if (!details.open) {
                            closeColumnFilters(details);
                        }
                    });

                    const panel = document.createElement('div');
                    panel.className = 'column-filter-panel';

                    panel.addEventListener('click', (event) => {
                        event.stopPropagation();
                    });

                    if (header.dataset.filterType === 'date-range') {
                        panel.classList.add('date-range-filter-panel');
                        const sortBox = document.createElement('div');
                        sortBox.className = 'date-range-sort';

                        const sortOldest = document.createElement('button');
                        sortOldest.type = 'button';
                        sortOldest.textContent = 'Más antiguas primero';

                        sortOldest.addEventListener('click', () => {
                            sortRowsByDate(column, 'asc');
                        });

                        const sortNewest = document.createElement('button');
                        sortNewest.type = 'button';
                        sortNewest.textContent = 'Más recientes primero';

                        sortNewest.addEventListener('click', () => {
                            sortRowsByDate(column, 'desc');
                        });

                        sortBox.append(sortOldest, sortNewest);
                        let draftFrom = '';
                        let draftTo = '';
                        const dateValues = rows.map((row) => normalize(row.cells[column]?.dataset
                            .filterDate)).filter(Boolean).sort();
                        let monthCursor = dateValues[0] ? parseDateKey(dateValues[0]) : new Date();
                        const todayKey = toDateKey(new Date());

                        const calendar = document.createElement('div');
                        calendar.className = 'date-range-calendar';

                        const calendarHeader = document.createElement('div');
                        calendarHeader.className = 'date-range-calendar-header';

                        const previous = document.createElement('button');
                        previous.className = 'date-range-nav';
                        previous.type = 'button';
                        previous.textContent = '<';

                        const title = document.createElement('div');
                        title.className = 'date-range-calendar-title';

                        const next = document.createElement('button');
                        next.className = 'date-range-nav';
                        next.type = 'button';
                        next.textContent = '>';

                        calendarHeader.append(previous, title, next);

                        const weekdays = document.createElement('div');
                        weekdays.className = 'date-range-weekdays';
                        ['L', 'M', 'M', 'J', 'V', 'S', 'D'].forEach((day) => {
                            const weekday = document.createElement('span');
                            weekday.textContent = day;
                            weekdays.append(weekday);
                        });

                        const days = document.createElement('div');
                        days.className = 'date-range-days';

                        const summaryRange = document.createElement('div');
                        summaryRange.className = 'date-range-summary';

                        const renderCalendar = () => {
                            title.textContent = monthTitle(monthCursor);
                            days.textContent = '';

                            const firstOfMonth = new Date(monthCursor.getFullYear(), monthCursor
                                .getMonth(), 1);
                            const mondayIndex = (firstOfMonth.getDay() + 6) % 7;
                            const gridStart = new Date(firstOfMonth);
                            gridStart.setDate(firstOfMonth.getDate() - mondayIndex);

                            for (let index = 0; index < 42; index += 1) {
                                const date = new Date(gridStart);
                                date.setDate(gridStart.getDate() + index);
                                const key = toDateKey(date);
                                const day = document.createElement('button');
                                day.className = 'date-range-day';
                                day.type = 'button';
                                day.textContent = date.getDate();

                                if (date.getMonth() !== monthCursor.getMonth()) {
                                    day.classList.add('is-muted');
                                }

                                if (draftFrom && draftTo && key > draftFrom && key < draftTo) {
                                    day.classList.add('is-in-range');
                                }

                                if (key === draftFrom || key === draftTo) {
                                    day.classList.add('is-selected');
                                }

                                day.addEventListener('click', () => {
                                    if (!draftFrom || (draftFrom && draftTo)) {
                                        draftFrom = key;
                                        draftTo = '';
                                    } else if (key < draftFrom) {
                                        draftTo = draftFrom;
                                        draftFrom = key;
                                    } else {
                                        draftTo = key;
                                    }

                                    renderCalendar();
                                });

                                days.append(day);
                            }

                            summaryRange.textContent = draftFrom ?
                                `Rango: ${formatHumanDate(draftFrom)}${draftTo ? ` - ${formatHumanDate(draftTo)}` : ''}` :
                                'Selecciona fecha inicial y final.';
                        };

                        previous.addEventListener('click', () => {
                            monthCursor = new Date(monthCursor.getFullYear(), monthCursor
                                .getMonth() - 1, 1);
                            renderCalendar();
                        });

                        next.addEventListener('click', () => {
                            monthCursor = new Date(monthCursor.getFullYear(), monthCursor
                                .getMonth() + 1, 1);
                            renderCalendar();
                        });

                        const overdue = document.createElement('button');
                        overdue.type = 'button';
                        overdue.className = 'button ghost small overdue-filter-button';
                        overdue.textContent = 'Vencidas';

                        overdue.addEventListener('click', () => {
                            draftFrom = '';
                            draftTo = todayKey;

                            filters.set(column, {
                                type: 'date-range',
                                from: '',
                                to: todayKey
                            });

                            monthCursor = parseDateKey(todayKey);

                            renderCalendar();
                            applyFilters();
                            details.removeAttribute('open');
                        });

                        const actions = document.createElement('div');
                        actions.className = 'column-filter-actions';

                        const apply = document.createElement('button');
                        apply.className = 'button primary small';
                        apply.type = 'button';
                        apply.textContent = 'Aceptar';
                        apply.addEventListener('click', () => {
                            if (!draftFrom && !draftTo) {
                                filters.delete(column);
                            } else {
                                filters.set(column, {
                                    type: 'date-range',
                                    from: draftFrom,
                                    to: draftTo || draftFrom
                                });
                            }

                            applyFilters();
                            details.removeAttribute('open');
                        });

                        const clear = document.createElement('button');
                        clear.className = 'button ghost small';
                        clear.type = 'button';
                        clear.textContent = 'Limpiar';
                        clear.addEventListener('click', () => {
                            draftFrom = '';
                            draftTo = '';
                            filters.delete(column);
                            applyFilters();
                            renderCalendar();
                            details.removeAttribute('open');
                        });

                        const cancel = document.createElement('button');
                        cancel.className = 'button ghost small';
                        cancel.type = 'button';
                        cancel.textContent = 'Cancelar';
                        cancel.addEventListener('click', () => {
                            const selected = filters.get(column) || {};
                            draftFrom = selected.from || '';
                            draftTo = selected.to || '';
                            details.removeAttribute('open');
                        });

                        details.addEventListener('toggle', () => {
                            if (details.open) {
                                const selected = filters.get(column) || {};
                                draftFrom = selected.from || '';
                                draftTo = selected.to || '';
                                monthCursor = draftFrom ? parseDateKey(draftFrom) : (
                                    dateValues[0] ? parseDateKey(dateValues[0]) :
                                    new Date());
                                renderCalendar();
                                requestAnimationFrame(() => positionFilterPanel(panel,
                                    summary));
                            }
                        });

                        actions.append(apply, clear, cancel);

                        calendar.append(
                            calendarHeader,
                            weekdays,
                            days,
                            summaryRange,
                            overdue,
                            actions
                        );

                        panel.append(sortBox, calendar);
                        details.append(summary, panel);
                        wrapper.append(text, details);
                        header.textContent = '';
                        header.append(wrapper);
                        return;
                    }

                    const search = document.createElement('input');
                    search.type = 'search';
                    search.className = 'column-filter-search';
                    search.placeholder = 'Buscar';
                    search.autocomplete = 'off';
                    search.setAttribute('aria-label', `Buscar en ${label}`);

                    const options = document.createElement('div');
                    options.className = 'column-filter-options';

                    const checkboxes = values.map((value) => {
                        const option = document.createElement('label');
                        option.className = 'column-filter-option';
                        option.dataset.filterSearch = normalizeSearch(value);

                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.value = value;
                        checkbox.checked = true;

                        const optionText = document.createElement('span');
                        optionText.textContent = value;

                        checkbox.addEventListener('change', () => {
                            const checked = checkboxes.filter((input) => input
                                .checked).map((input) => input.value);

                            if (checked.length === checkboxes.length) {
                                filters.delete(column);
                            } else {
                                filters.set(column, new Set(checked));
                            }

                            applyFilters();
                        });

                        option.append(checkbox, optionText);
                        options.append(option);

                        return checkbox;
                    });

                    const noMatches = document.createElement('div');
                    noMatches.className = 'column-filter-empty';
                    noMatches.textContent = 'Sin coincidencias';
                    noMatches.hidden = true;
                    options.append(noMatches);

                    search.addEventListener('input', () => {
                        const query = normalizeSearch(search.value);
                        let visibleOptions = 0;
                        options.querySelectorAll('.column-filter-option').forEach((option) => {
                            const matches = !query || option.dataset.filterSearch.includes(query);
                            option.hidden = !matches;
                            if (matches) visibleOptions += 1;
                        });
                        noMatches.hidden = values.length === 0 || visibleOptions > 0;
                    });

                    const actions = document.createElement('div');
                    actions.className = 'column-filter-actions';

                    const selectAll = document.createElement('button');
                    selectAll.className = 'button ghost small';
                    selectAll.type = 'button';
                    selectAll.textContent = 'Todos';
                    selectAll.addEventListener('click', () => {
                        checkboxes.forEach((checkbox) => checkbox.checked = true);
                        filters.delete(column);
                        applyFilters();
                    });

                    const clear = document.createElement('button');
                    clear.className = 'button ghost small';
                    clear.type = 'button';
                    clear.textContent = 'Limpiar';
                    clear.addEventListener('click', () => {
                        checkboxes.forEach((checkbox) => checkbox.checked = false);
                        filters.set(column, new Set());
                        applyFilters();
                    });

                    actions.append(selectAll, clear);
                    panel.append(search, options, actions);
                    details.append(summary, panel);
                    details.addEventListener('toggle', () => {
                        if (details.open) {
                            closeColumnFilters(details);
                            search.value = '';
                            options.querySelectorAll('.column-filter-option').forEach((option) => option.hidden = false);
                            noMatches.hidden = true;
                            requestAnimationFrame(() => {
                                positionFilterPanel(panel, summary);
                                search.focus();
                            });
                        }
                    });
                    wrapper.append(text, details);
                    header.textContent = '';
                    header.append(wrapper);
                });
            });
        });
    </script>
@endsection
