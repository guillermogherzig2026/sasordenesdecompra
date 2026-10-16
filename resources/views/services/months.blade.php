@extends('layouts.app')

@section('body')
    @php
        $pageTitle = $title ?? 'Servicios por mes';
        $monthSubtitle = $monthSubtitle ?? 'Servicios ordenados de la fecha de corte mas proxima a la mas tardia.';
        $emptyMessage = $emptyMessage ?? 'No hay servicios programados para este mes.';
        $historyMode = $historyMode ?? false;
        $downloadReport = $historyMode ? null : ($downloadReport ?? 'services-months-excel');
        $generalHistory = $generalHistory ?? false;
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
                $showDemoPayment = $historyMode && ($generalHistory || ($demoPaymentMonth ?? null) === $month['month_key']) && filled($demoPaymentMonth ?? null);
                $isExpanded = $expandedMonthKey ? $month['month_key'] === $expandedMonthKey : $loop->first;
            @endphp
            <section @class(['panel service-month-panel', 'service-history-month-panel' => $historyMode, 'service-general-history-panel' => $generalHistory]) @unless ($generalHistory) data-month-panel data-month-key="{{ $month['month_key'] }}" @endunless>
                @unless ($generalHistory)
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
                @endunless

                <div id="{{ $monthDetailId }}" class="service-month-detail" @if (! $generalHistory && ! $isExpanded) hidden @endif>
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
                                            <td data-filter-value="{{ $payment->entry->bank ?: 'Sin banco' }}">{{ $payment->entry->bank ?: '-' }}</td>
                                            <td data-filter-value="{{ $payment->entry->payer_account ?: 'Sin cuenta' }}">{{ $payment->entry->payer_account ?: '-' }}</td>
                                            <td data-filter-value="{{ $payment->entry->name }}">{{ $payment->entry->name }}</td>
                                            <td data-filter-value="{{ $payment->allocations->isNotEmpty() ? 'Varios' : $payment->entry->reference_number }}">
                                                @if ($payment->allocations->isNotEmpty())
                                                    <button class="button ghost small" type="button" data-fuel-breakdown="{{ $payment->id }}" aria-label="Ver asignaciones del pago OCP-{{ $payment->id }}">Varios</button>
                                                @else
                                                    {{ $payment->entry->reference_number }}
                                                @endif
                                            </td>
                                            <td data-filter-value="Pago único">Pago único</td>
                                            <td data-filter-value="{{ $payment->due_date->format('d/m/Y') }}" data-filter-date="{{ $payment->due_date->toDateString() }}">{{ $payment->due_date->format('d/m/Y') }}</td>
                                            <td data-filter-value="${{ number_format((float) $payment->amount, 2) }}">${{ number_format((float) $payment->amount, 2) }}</td>
                                            <td data-filter-value="{{ $payment->allocations->isNotEmpty() ? 'Varios' : $payment->entry->reference_number }}">{{ $payment->allocations->isNotEmpty() ? 'Varios' : $payment->entry->reference_number }}</td>
                                            <td data-filter-value="Pendiente"><span class="status pending">Pendiente</span></td>
                                            <td data-filter-value="Sin Archivo">Sin Archivo</td>
                                            <td data-filter-value="Sin Archivo">Sin Archivo</td>
                                        </tr>
                                    @endforeach
                                @endunless
                                @forelse ($month['items'] as $item)
                                    @if (isset($item['occasional_payment']))
                                        @php
                                            $payment = $item['occasional_payment'];
                                            $allocation = $item['allocation'];
                                            $holder = $allocation?->recipient_name ?? $payment->entry->name;
                                            $reference = $allocation?->reference_number ?? $payment->entry->reference_number;
                                            $amount = (float) ($allocation?->amount ?? $payment->amount);
                                            $paymentFilename = $payment->payment_original_name ?: $payment->payment_file_path;
                                            $paymentExtension = strtolower(pathinfo($paymentFilename, PATHINFO_EXTENSION));
                                            $paymentPreviewKind = $paymentExtension === 'pdf' ? 'pdf' : (in_array($paymentExtension, ['jpg', 'jpeg', 'png', 'webp'], true) ? 'image' : 'other');
                                        @endphp
                                        <tr data-filter-row class="service-row-locked service-occasional-payment-row">
                                            <td data-filter-value="OCP-{{ $payment->id }}"><strong>OCP-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                                            <td data-filter-value="{{ $holder }}">{{ $holder }}</td>
                                            <td data-filter-value="Sin sucursal">-</td>
                                            <td data-filter-value="{{ $payment->entry->category->name }} (ocasional)">{{ $payment->entry->category->name }} (ocasional)</td>
                                            <td data-filter-value="{{ $payment->entry->bank ?: 'Sin banco' }}">{{ $payment->entry->bank ?: '-' }}</td>
                                            <td data-filter-value="{{ $payment->entry->payer_account ?: 'Sin cuenta' }}">{{ $payment->entry->payer_account ?: '-' }}</td>
                                            <td data-filter-value="{{ $payment->entry->name }}">{{ $payment->entry->name }}</td>
                                            <td data-filter-value="{{ $reference }}">{{ $reference }}</td>
                                            <td data-filter-value="Pago único">Pago único</td>
                                            <td data-filter-value="{{ $payment->payment_paid_on->format('d/m/Y') }}" data-filter-date="{{ $payment->payment_paid_on->toDateString() }}">{{ $payment->payment_paid_on->format('d/m/Y') }}</td>
                                            <td data-filter-value="${{ number_format($amount, 2) }}">${{ number_format($amount, 2) }}</td>
                                            <td data-filter-value="{{ $reference }}">{{ $reference }}</td>
                                            <td data-filter-value="Pagado"><span class="status paid">Pagado</span></td>
                                            <td data-filter-value="Sin Archivo">Sin Archivo</td>
                                            <td data-filter-value="{{ $paymentFilename }}">
                                                <button class="attachment-pill service-payment-trigger" type="button"
                                                    data-payment-preview data-dialog-title="Comprobante de pago"
                                                    data-payment-title="{{ $payment->entry->category->name }} - {{ $holder }}"
                                                    data-payment-filename="{{ $paymentFilename }}"
                                                    data-preview-kind="{{ $paymentPreviewKind }}"
                                                    data-preview-url="{{ route('services.occasional.payments.file', $payment) }}"
                                                    data-download-url="{{ route('services.occasional.payments.file.pdf', $payment) }}">Ver</button>
                                            </td>
                                        </tr>
                                    @else
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
                                    @endif
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

    @unless ($historyMode)
        @php
            $fuelPreviewPayments = collect($months)->flatMap(fn (array $month) => $month['occasional_payments'])
                ->filter(fn ($payment) => $payment->allocations->isNotEmpty());
        @endphp
        @include('services.partials.fuel-allocation-preview', ['payments' => $fuelPreviewPayments])
    @endunless

    @if (! $historyMode && $canCreateOccasionalPayment)
        @php $fuelCategory = $occasional_categories->firstWhere('name', 'Vale de Gasolina'); @endphp
        <dialog class="occasional-payment-dialog" data-occasional-payment-dialog data-fuel-category-id="{{ $fuelCategory?->id }}" aria-labelledby="occasional-payment-title"
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
                        <nav class="occasional-payment-carousel" aria-label="Filtrar servicios ocasionales" data-payment-carousel>
                            <button class="occasional-payment-carousel-arrow" type="button" data-payment-carousel-direction="previous" aria-label="Servicios anteriores" title="Servicios anteriores">&lsaquo;</button>
                            <div class="occasional-payment-carousel-track" data-payment-carousel-track>
                                <button class="occasional-payment-category is-selected" type="button" data-payment-category="" aria-pressed="true" aria-controls="occasional-payment-entry">
                                    <span class="occasional-payment-category-icon" aria-hidden="true">&#9638;</span>
                                    <span>Todos</span>
                                </button>
                                @foreach ($occasional_categories as $category)
                                    <button class="occasional-payment-category" type="button" data-payment-category="{{ $category->id }}" aria-pressed="false" aria-controls="occasional-payment-entry" title="{{ $category->name }}">
                                        <span class="occasional-payment-category-icon" aria-hidden="true">@include('services.partials.occasional-icon', ['icon' => $category->icon])</span>
                                        <span>{{ $category->name }}</span>
                                    </button>
                                @endforeach
                            </div>
                            <button class="occasional-payment-carousel-arrow" type="button" data-payment-carousel-direction="next" aria-label="Más servicios" title="Más servicios">&rsaquo;</button>
                        </nav>
                        <label for="occasional-payment-entry"><span data-payment-entry-label>Servicio ocasional</span>
                            <select id="occasional-payment-entry" name="occasional_service_entry_id" required>
                                <option value="">Seleccionar servicio</option>
                                @foreach ($occasional_entries as $entry)
                                    @continue($entry->category->name === 'Vale de Gasolina' && $entry->reference_number !== \App\Models\OccasionalServiceEntry::FUEL_PROVIDER_REFERENCE)
                                    <option value="{{ $entry->id }}" data-payment-entry-category="{{ $entry->occasional_service_id }}" @selected((string) old('occasional_service_entry_id') === (string) $entry->id)>{{ $entry->reference_number === \App\Models\OccasionalServiceEntry::FUEL_PROVIDER_REFERENCE ? 'Efectivale' : $entry->category->name.' - '.$entry->name.' - '.$entry->reference_number }}</option>
                                @endforeach
                            </select>
                        </label>
                    </fieldset>
                    <fieldset class="occasional-payment-source" data-payment-new @if ($paymentMode === 'existing') hidden disabled @endif>
                        <label for="occasional-payment-category">Categoría
                            <select id="occasional-payment-category" name="category_id" required>
                                <option value="">Seleccionar categoría</option>
                                @foreach ($occasional_categories as $category)
                                    @continue($category->name === 'Vale de Gasolina')
                                    <option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label for="occasional-payment-name"><span data-payment-new-name-label>Nombre del servicio/Titular</span>
                            <input id="occasional-payment-name" name="name" maxlength="100" value="{{ old('name') }}" required>
                        </label>
                        <label for="occasional-payment-reference">Número de referencia
                            <input id="occasional-payment-reference" name="reference_number" maxlength="100" value="{{ old('reference_number') }}" required>
                        </label>
                        <label for="occasional-payment-bank">Banco (opcional)
                            <input id="occasional-payment-bank" name="bank" maxlength="120" value="{{ old('bank') }}">
                        </label>
                        <label for="occasional-payment-payer-account">Cuenta pagadora (opcional)
                            <input id="occasional-payment-payer-account" name="payer_account" maxlength="80" value="{{ old('payer_account') }}">
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
                    <fieldset class="fuel-allocation-panel" data-fuel-allocation aria-label="Asignar monto a tarjetas" hidden disabled>
                        <div class="fuel-allocation-heading">
                            <div>
                                <h3>Asignar monto a tarjetas</h3>
                                <p>Distribuye el monto entre las referencias o usuarios de la empresa.</p>
                            </div>
                            <button class="button ghost small" type="button" data-add-fuel-allocation>+ Agregar tarjeta</button>
                        </div>
                        <div class="fuel-allocation-table-wrap">
                            <table class="fuel-allocation-table">
                                <thead><tr><th>Referencia / tarjeta</th><th>Usuario</th><th>Monto asignado (MXN)</th><th><span class="fuel-allocation-visually-hidden">Quitar</span></th></tr></thead>
                                <tbody data-fuel-allocation-rows></tbody>
                            </table>
                        </div>
                        <div class="fuel-allocation-totals">
                            <div><span>Monto del pago</span><strong data-fuel-payment-total>$0.00</strong></div>
                            <div><span>Total asignado</span><strong data-fuel-assigned-total>$0.00</strong></div>
                            <div><span data-fuel-difference-label>Diferencia</span><strong data-fuel-difference>$0.00</strong></div>
                        </div>
                        <p class="fuel-allocation-feedback" data-fuel-allocation-feedback role="status" aria-live="polite"></p>
                        <p class="fuel-allocation-note">El pago se envía al proveedor. La dispersión se realiza en su portal.</p>
                    </fieldset>
                </div>
                <section class="fuel-allocation-review" data-fuel-review hidden aria-label="Resumen del pago ocasional">
                    <h3>Resumen del pago</h3>
                    <dl>
                        <div><dt>Cuenta concentradora</dt><dd data-fuel-review-account></dd></div>
                        <div><dt>Monto a transferir</dt><dd data-fuel-review-amount></dd></div>
                        <div><dt>Fecha prevista</dt><dd data-fuel-review-date></dd></div>
                    </dl>
                    <h4>Asignación a tarjetas</h4>
                    <ul data-fuel-review-list></ul>
                    <p>La asignación registrada debe aplicarse en el portal del proveedor.</p>
                </section>
                <footer class="service-payment-dialog-footer">
                    <button class="button ghost" type="button" data-occasional-payment-close>Cancelar</button>
                    <button class="button ghost" type="button" data-fuel-review-back hidden>Volver</button>
                    <button class="button primary" type="button" data-fuel-review-next hidden disabled>Continuar al resumen</button>
                    <button class="button primary" type="submit" data-payment-submit @disabled($occasional_entries->isEmpty() && $occasional_categories->isEmpty())>Agregar a Servicios Mensuales</button>
                </footer>
            </form>
            <template data-fuel-allocation-template>
                <tr>
                    <td>
                        <select data-fuel-recipient required aria-label="Tarjeta o referencia">
                            <option value="">Seleccionar tarjeta</option>
                            @foreach ($occasional_entries->where('occasional_service_id', $fuelCategory?->id) as $entry)
                                @continue($entry->reference_number === \App\Models\OccasionalServiceEntry::FUEL_PROVIDER_REFERENCE)
                                <option value="{{ $entry->id }}" data-recipient-name="{{ $entry->name }}" data-reference-number="{{ $entry->reference_number }}">{{ $entry->name }} - {{ $entry->reference_number }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><span data-fuel-recipient-name>—</span></td>
                    <td><input data-fuel-recipient-amount type="number" min="0.01" max="9999999999.99" step="0.01" required aria-label="Monto asignado en MXN"></td>
                    <td><button class="fuel-allocation-remove" type="button" data-remove-fuel-allocation aria-label="Quitar tarjeta" title="Quitar tarjeta">&times;</button></td>
                </tr>
            </template>
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
        @media (min-width: 901px) {
            body:has(.service-general-history-panel) { padding-bottom: 0; }
            .content-shell:has(.service-general-history-panel) { height: 100dvh; min-height: 0; }
            .content-shell:has(.service-general-history-panel) > .view {
                flex: 1 1 auto;
                min-height: 0;
                grid-template-rows: minmax(0, 1fr);
                padding-bottom: 14px;
            }
            .view > .service-general-history-panel:last-child {
                min-height: 0;
                max-height: none;
                display: flex;
                flex-direction: column;
                gap: 0;
            }
            .service-general-history-panel .service-month-detail {
                flex: 1 1 auto;
                min-height: 0;
                display: flex;
                flex-direction: column;
            }
            .service-general-history-panel .service-month-scroll {
                flex: 1 1 auto;
                min-height: 0;
                max-height: none;
            }
        }
        .occasional-payment-dialog { border: 1px solid var(--line); border-radius: 8px; box-shadow: 0 24px 64px rgba(16, 43, 58, .24); color: var(--text); max-height: calc(100dvh - 32px); overflow: hidden; padding: 0; width: min(700px, calc(100vw - 32px)); }
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
        .occasional-payment-source[hidden] { display: none; }
        .occasional-payment-carousel { align-items: center; display: flex; gap: 6px; min-width: 0; }
        .occasional-payment-carousel-arrow { align-items: center; background: #fff; border: 1px solid var(--line); border-radius: 50%; color: var(--primary-strong); cursor: pointer; display: inline-flex; flex: 0 0 28px; font-size: 1.3rem; height: 28px; justify-content: center; padding: 0; }
        .occasional-payment-carousel-track { display: flex; flex: 1 1 auto; gap: 6px; min-width: 0; overflow-x: auto; padding: 2px; scrollbar-width: none; scroll-behavior: smooth; scroll-snap-type: x mandatory; }
        .occasional-payment-carousel-track::-webkit-scrollbar { display: none; }
        .occasional-payment-category { align-items: center; background: #fff; border: 1px solid var(--line); border-radius: 6px; color: var(--text); cursor: pointer; display: flex; flex: 0 0 102px; flex-direction: column; font: inherit; font-size: .72rem; font-weight: 800; gap: 4px; height: 70px; justify-content: center; line-height: 1.15; overflow-wrap: anywhere; padding: 6px; scroll-snap-align: start; text-align: center; }
        .occasional-payment-category-icon { align-items: center; color: var(--primary-strong); display: inline-flex; font-size: 1.3rem; height: 22px; justify-content: center; }
        .occasional-payment-category-icon svg { height: 20px; width: 20px; }
        .occasional-payment-category.is-selected { background: var(--primary-strong); border-color: var(--primary-strong); color: #fff; }
        .occasional-payment-category.is-selected .occasional-payment-category-icon { color: #fff; }
        .occasional-payment-carousel button:focus-visible { outline: 2px solid var(--primary); outline-offset: 2px; }
        .occasional-payment-grid { display: grid; gap: 14px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .occasional-payment-dialog [hidden] { display: none !important; }
        .fuel-allocation-panel { background: #f4f9fc; border: 1px solid var(--line); border-radius: 6px; margin: 0; min-width: 0; padding: 12px; }
        .fuel-allocation-heading { align-items: flex-start; display: flex; gap: 12px; justify-content: space-between; margin-bottom: 10px; }
        .fuel-allocation-heading h3, .fuel-allocation-review h3 { font-size: .98rem; margin: 0; }
        .fuel-allocation-heading p, .fuel-allocation-note, .fuel-allocation-review > p { color: var(--muted); font-size: .78rem; margin: 3px 0 0; }
        .fuel-allocation-heading button { flex: 0 0 auto; }
        .fuel-allocation-table-wrap { border: 1px solid var(--line); border-radius: 5px; overflow-x: auto; }
        .fuel-allocation-table { border-collapse: collapse; min-width: 570px; table-layout: fixed; width: 100%; }
        .fuel-allocation-table th, .fuel-allocation-table td { border-bottom: 1px solid var(--line); font-size: .76rem; padding: 5px 7px; text-align: left; }
        .fuel-allocation-table th { background: #e8f3f8; font-size: .7rem; }
        .fuel-allocation-table tr:last-child td { border-bottom: 0; }
        .fuel-allocation-table th:nth-child(1) { width: 36%; }
        .fuel-allocation-table th:nth-child(2) { width: 28%; }
        .fuel-allocation-table th:nth-child(3) { width: 29%; }
        .fuel-allocation-table th:nth-child(4) { width: 7%; }
        .fuel-allocation-table select, .fuel-allocation-table input { font-size: .77rem !important; min-height: 32px !important; padding: 4px 6px !important; }
        .fuel-allocation-remove { align-items: center; background: transparent; border: 0; color: var(--text); cursor: pointer; display: inline-flex; font-size: 1.4rem; height: 30px; justify-content: center; width: 30px; }
        .fuel-allocation-visually-hidden { clip: rect(0, 0, 0, 0); clip-path: inset(50%); height: 1px; overflow: hidden; position: absolute; white-space: nowrap; width: 1px; }
        .fuel-allocation-totals { background: #fff; border: 1px solid var(--line); border-radius: 5px; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); margin-top: 8px; padding: 8px 0; }
        .fuel-allocation-totals > div { display: grid; gap: 2px; padding: 0 8px; text-align: center; }
        .fuel-allocation-totals > div + div { border-left: 1px solid var(--line); }
        .fuel-allocation-totals span { color: var(--muted); font-size: .68rem; }
        .fuel-allocation-totals strong { font-size: .85rem; }
        .fuel-allocation-feedback { border-radius: 4px; font-size: .76rem; margin: 6px 0 0; padding: 8px 10px; }
        .fuel-allocation-feedback[data-state="error"] { background: #fff0f0; color: #a91e28; }
        .fuel-allocation-feedback[data-state="success"] { background: #e8f8ee; color: #17673b; }
        .fuel-allocation-feedback[data-state="neutral"] { background: #e8f3f8; color: var(--primary-strong); }
        .fuel-allocation-note { margin-top: 7px; }
        .fuel-allocation-review { overflow-y: auto; padding: 20px; }
        .fuel-allocation-review dl { border: 1px solid var(--line); border-radius: 5px; margin: 12px 0; }
        .fuel-allocation-review dl div { display: flex; gap: 12px; justify-content: space-between; padding: 9px 12px; }
        .fuel-allocation-review dl div + div { border-top: 1px solid var(--line); }
        .fuel-allocation-review dt { color: var(--muted); }
        .fuel-allocation-review dd { font-weight: 800; margin: 0; text-align: right; }
        .fuel-allocation-review h4 { font-size: .85rem; margin: 14px 0 6px; }
        .fuel-allocation-review ul { list-style: none; margin: 0; padding: 0; }
        .fuel-allocation-review li { border-bottom: 1px solid var(--line); display: flex; gap: 12px; justify-content: space-between; padding: 8px 0; }
        .fuel-allocation-review li span { overflow-wrap: anywhere; }
        .fuel-allocation-review li strong { flex: 0 0 auto; }
        .occasional-payment-dialog .service-payment-dialog-footer { gap: 8px; }
        .occasional-payment-dialog button:disabled { cursor: not-allowed; opacity: .55; }
        @media (max-width: 560px) { .occasional-payment-grid { grid-template-columns: 1fr; } .occasional-payment-dialog .service-payment-dialog-footer, .fuel-allocation-heading { flex-wrap: wrap; } .fuel-allocation-totals strong { font-size: .75rem; } }
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
            const paymentCarousel = occasionalPaymentDialog?.querySelector('[data-payment-carousel]');
            const paymentCarouselTrack = paymentCarousel?.querySelector('[data-payment-carousel-track]');
            const paymentEntrySelect = occasionalPaymentDialog?.querySelector('#occasional-payment-entry');
            if (paymentCarousel && paymentCarouselTrack && paymentEntrySelect) {
                const categoryButtons = [...paymentCarousel.querySelectorAll('[data-payment-category]')];
                const entryOptions = [...paymentEntrySelect.querySelectorAll('[data-payment-entry-category]')];
                const placeholderOption = paymentEntrySelect.options[0];
                const selectCategory = (categoryId) => {
                    const currentValue = paymentEntrySelect.value;
                    const matchingOptions = entryOptions.filter((option) => !categoryId || option.dataset.paymentEntryCategory === categoryId);
                    placeholderOption.textContent = matchingOptions.length ? 'Seleccionar servicio' : 'No hay servicios registrados en esta categoría';
                    paymentEntrySelect.replaceChildren(placeholderOption, ...matchingOptions);
                    paymentEntrySelect.value = matchingOptions.some((option) => option.value === currentValue) ? currentValue : '';
                    categoryButtons.forEach((button) => {
                        const selected = button.dataset.paymentCategory === categoryId;
                        button.classList.toggle('is-selected', selected);
                        button.setAttribute('aria-pressed', String(selected));
                    });
                };
                categoryButtons.forEach((button) => button.addEventListener('click', () => {
                    selectCategory(button.dataset.paymentCategory);
                    syncFuelAllocation();
                }));

                const carouselArrows = [...paymentCarousel.querySelectorAll('[data-payment-carousel-direction]')];
                const syncCarouselArrows = () => {
                    const maxScroll = paymentCarouselTrack.scrollWidth - paymentCarouselTrack.clientWidth;
                    carouselArrows.forEach((arrow) => {
                        arrow.disabled = arrow.dataset.paymentCarouselDirection === 'previous'
                            ? paymentCarouselTrack.scrollLeft <= 1
                            : paymentCarouselTrack.scrollLeft >= maxScroll - 1;
                    });
                };
                carouselArrows.forEach((arrow) => arrow.addEventListener('click', () => {
                    paymentCarouselTrack.scrollBy({ left: (arrow.dataset.paymentCarouselDirection === 'previous' ? -1 : 1) * paymentCarouselTrack.clientWidth * .75, behavior: 'smooth' });
                }));
                paymentCarouselTrack.addEventListener('scroll', syncCarouselArrows, { passive: true });
                window.addEventListener('resize', syncCarouselArrows);
                occasionalPaymentDialog.addEventListener('toggle', syncCarouselArrows);
                syncCarouselArrows();
            }
            const paymentForm = occasionalPaymentDialog?.querySelector('form');
            const paymentFields = occasionalPaymentDialog?.querySelector('.occasional-payment-fields');
            const newCategorySelect = occasionalPaymentDialog?.querySelector('#occasional-payment-category');
            const amountInput = occasionalPaymentDialog?.querySelector('#occasional-payment-amount');
            const allocationPanel = occasionalPaymentDialog?.querySelector('[data-fuel-allocation]');
            const allocationRows = allocationPanel?.querySelector('[data-fuel-allocation-rows]');
            const allocationTemplate = occasionalPaymentDialog?.querySelector('[data-fuel-allocation-template]');
            const reviewPanel = occasionalPaymentDialog?.querySelector('[data-fuel-review]');
            const reviewNext = occasionalPaymentDialog?.querySelector('[data-fuel-review-next]');
            const reviewBack = occasionalPaymentDialog?.querySelector('[data-fuel-review-back]');
            const paymentSubmit = occasionalPaymentDialog?.querySelector('[data-payment-submit]');
            const fuelCategoryId = occasionalPaymentDialog?.dataset.fuelCategoryId;
            const savedAllocations = {{ \Illuminate\Support\Js::from(old('allocations', [])) }};
            const currency = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });
            let allocationIndex = 0;
            let wasFuelPayment = false;
            let isReviewingFuel = false;

            const cents = (value) => {
                const text = String(value ?? '');
                if (!/^\d+(?:\.\d{1,2})?$/.test(text)) return null;
                const [whole, fraction = ''] = text.split('.');
                return Number(whole) * 100 + Number(fraction.padEnd(2, '0'));
            };
            const formatCents = (value) => currency.format(value / 100);
            const isFuelPayment = () => {
                if (!fuelCategoryId) return false;
                const isNew = occasionalPaymentDialog?.querySelector('input[name="mode"]:checked')?.value === 'new';
                const categoryId = isNew
                    ? newCategorySelect?.value
                    : (paymentEntrySelect?.selectedOptions[0]?.dataset.paymentEntryCategory
                        || paymentCarousel?.querySelector('[data-payment-category][aria-pressed="true"]')?.dataset.paymentCategory);
                return categoryId === fuelCategoryId;
            };
            const updateRecipient = (row) => {
                const option = row.querySelector('[data-fuel-recipient]').selectedOptions[0];
                row.querySelector('[data-fuel-recipient-name]').textContent = option?.dataset.recipientName || '—';
            };
            const addAllocation = (entryId = '', amount = '') => {
                const row = allocationTemplate.content.firstElementChild.cloneNode(true);
                row.querySelector('[data-fuel-recipient]').name = `allocations[${allocationIndex}][entry_id]`;
                row.querySelector('[data-fuel-recipient-amount]').name = `allocations[${allocationIndex}][amount]`;
                allocationIndex++;
                allocationRows.appendChild(row);
                row.querySelector('[data-fuel-recipient]').value = String(entryId ?? '');
                row.querySelector('[data-fuel-recipient-amount]').value = amount ?? '';
                updateRecipient(row);
            };
            const syncFuelAllocation = () => {
                if (!allocationPanel) return;
                const fuel = isFuelPayment();
                if (fuel && !wasFuelPayment && !allocationRows.children.length) addAllocation();
                wasFuelPayment = fuel;
                allocationPanel.hidden = !fuel;
                allocationPanel.disabled = !fuel;
                occasionalPaymentDialog.querySelector('[data-payment-entry-label]').textContent = fuel ? 'Cuenta concentradora / proveedor' : 'Servicio ocasional';
                occasionalPaymentDialog.querySelector('[data-payment-new-name-label]').textContent = fuel ? 'Proveedor / cuenta concentradora' : 'Nombre del servicio/Titular';

                const accountId = occasionalPaymentDialog.querySelector('input[name="mode"]:checked')?.value === 'existing'
                    ? paymentEntrySelect?.value : '';
                const rows = [...allocationRows.querySelectorAll('tr')];
                rows.forEach((row) => {
                    const select = row.querySelector('[data-fuel-recipient]');
                    [...select.options].forEach((option) => { option.disabled = Boolean(accountId && option.value === accountId); });
                    if (select.value === accountId) select.value = '';
                    updateRecipient(row);
                });

                if (!fuel) {
                    reviewNext.hidden = true;
                    reviewBack.hidden = true;
                    paymentSubmit.hidden = false;
                    return;
                }

                const paymentCents = cents(amountInput.value);
                const amounts = rows.map((row) => cents(row.querySelector('[data-fuel-recipient-amount]').value));
                const assignedCents = amounts.reduce((sum, value) => sum + (value ?? 0), 0);
                const selectedIds = rows.map((row) => row.querySelector('[data-fuel-recipient]').value);
                const validRows = rows.length > 0 && amounts.every((value) => value !== null && value > 0)
                    && selectedIds.every(Boolean) && new Set(selectedIds).size === selectedIds.length;
                const difference = paymentCents === null ? 0 : paymentCents - assignedCents;
                const balanced = paymentCents !== null && paymentCents > 0 && validRows && difference === 0;
                allocationPanel.querySelector('[data-fuel-payment-total]').textContent = formatCents(paymentCents ?? 0);
                allocationPanel.querySelector('[data-fuel-assigned-total]').textContent = formatCents(assignedCents);
                allocationPanel.querySelector('[data-fuel-difference]').textContent = formatCents(Math.abs(difference));
                allocationPanel.querySelector('[data-fuel-difference-label]').textContent = difference < 0 ? 'Excedente' : (difference > 0 ? 'Por asignar' : 'Diferencia');

                const feedback = allocationPanel.querySelector('[data-fuel-allocation-feedback]');
                const availableCards = [...allocationTemplate.content.querySelectorAll('[data-fuel-recipient] option')]
                    .filter((option) => option.value && option.value !== accountId).length;
                if (!availableCards) {
                    feedback.textContent = 'Registra primero las tarjetas o referencias de Vale de Gasolina en el catálogo de Servicios.';
                } else if (paymentCents === null || paymentCents <= 0) {
                    feedback.textContent = 'Ingresa el monto que se transferirá a la cuenta concentradora.';
                } else if (new Set(selectedIds.filter(Boolean)).size !== selectedIds.filter(Boolean).length) {
                    feedback.textContent = 'Cada tarjeta o referencia solo puede recibir una asignación por pago.';
                } else if (!validRows) {
                    feedback.textContent = 'Selecciona cada tarjeta e ingresa un monto mayor que cero.';
                } else if (difference !== 0) {
                    feedback.textContent = difference < 0
                        ? `El total asignado excede el pago por ${formatCents(-difference)}.`
                        : `Faltan ${formatCents(difference)} por asignar.`;
                } else {
                    feedback.textContent = 'El total asignado coincide con el monto del pago.';
                }
                feedback.dataset.state = balanced ? 'success' : (difference < 0 ? 'error' : 'neutral');
                reviewNext.disabled = !balanced || !paymentForm.checkValidity();
                reviewNext.hidden = isReviewingFuel;
                reviewBack.hidden = !isReviewingFuel;
                paymentSubmit.hidden = !isReviewingFuel;
            };

            if (allocationPanel) {
                if (Array.isArray(savedAllocations)) savedAllocations.forEach((row) => addAllocation(row.entry_id, row.amount));
                allocationPanel.querySelector('[data-add-fuel-allocation]').addEventListener('click', () => {
                    addAllocation();
                    syncFuelAllocation();
                });
                allocationRows.addEventListener('click', (event) => {
                    const remove = event.target.closest('[data-remove-fuel-allocation]');
                    if (!remove) return;
                    remove.closest('tr').remove();
                    syncFuelAllocation();
                });
                paymentForm.addEventListener('input', syncFuelAllocation);
                paymentForm.addEventListener('change', syncFuelAllocation);
                reviewNext.addEventListener('click', () => {
                    syncFuelAllocation();
                    if (reviewNext.disabled) return;
                    const isNew = occasionalPaymentDialog.querySelector('input[name="mode"]:checked')?.value === 'new';
                    reviewPanel.querySelector('[data-fuel-review-account]').textContent = isNew
                        ? `${occasionalPaymentDialog.querySelector('#occasional-payment-name').value} - ${occasionalPaymentDialog.querySelector('#occasional-payment-reference').value}`
                        : paymentEntrySelect.selectedOptions[0].textContent;
                    reviewPanel.querySelector('[data-fuel-review-amount]').textContent = formatCents(cents(amountInput.value));
                    reviewPanel.querySelector('[data-fuel-review-date]').textContent = occasionalPaymentDialog.querySelector('#occasional-payment-date').value.split('-').reverse().join('/');
                    const list = reviewPanel.querySelector('[data-fuel-review-list]');
                    list.replaceChildren();
                    [...allocationRows.querySelectorAll('tr')].forEach((row) => {
                        const option = row.querySelector('[data-fuel-recipient]').selectedOptions[0];
                        const item = document.createElement('li');
                        const recipient = document.createElement('span');
                        const amount = document.createElement('strong');
                        recipient.textContent = `${option.dataset.recipientName} - ${option.dataset.referenceNumber}`;
                        amount.textContent = formatCents(cents(row.querySelector('[data-fuel-recipient-amount]').value));
                        item.append(recipient, amount);
                        list.append(item);
                    });
                    isReviewingFuel = true;
                    paymentFields.hidden = true;
                    reviewPanel.hidden = false;
                    syncFuelAllocation();
                });
                reviewBack.addEventListener('click', () => {
                    isReviewingFuel = false;
                    paymentFields.hidden = false;
                    reviewPanel.hidden = true;
                    syncFuelAllocation();
                });
                paymentForm.addEventListener('submit', (event) => {
                    syncFuelAllocation();
                    if (isFuelPayment() && (!isReviewingFuel || !paymentForm.checkValidity())) event.preventDefault();
                });
                syncFuelAllocation();
            }
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
