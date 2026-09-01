@extends('layouts.app')

@section('body')
    <x-app-shell title="Historial">
        <section class="panel finance-history-panel">
            <div class="panel-header">
                <div>
                    <h2>Historial</h2>
                    <p class="fine-print">Finanzas conserva aqui las ordenes pagadas, rechazadas o canceladas.</p>
                </div>
                <div class="item-actions">
                    <a class="button ghost" href="{{ route('reports.download', 'finance-history-items-excel') }}">Detalle por partida</a>

                </div>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                                                <tr>
                            <th># OC</th>
                            <th>Fecha de pago</th>
                            <th>Comprador</th>
                            <th>Proveedor</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Pago</th>
                            <th>Fecha de pago</th>
                            <th>Recepcion</th>
                            <th>Almacen receptor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            @php
                                $lastReceipt = $order->receipts->sortByDesc('received_on')->first();
                                $ordered = $order->items->sum('quantity');
                                $received = $order->items->sum(fn ($item) => $item->receiptItems->sum('received_quantity'));
                                $receiptText = \App\Support\UiStatus::receipt($order->receipt_status, 'finance');
                                $receiptDetail = $lastReceipt
                                    ? "{$lastReceipt->invoice_number} - {$lastReceipt->received_on?->format('d/m/Y')} - Recibido " . number_format((float) $received, 0) . ' de ' . number_format((float) $ordered, 0)
                                    : null;
                            @endphp
                            <tr>
                                <td>
                                    <strong>
                                        <a href="{{ route('finance.orders.print', $order) }}" target="_blank">{{ $order->folio }}</a>
                                    </strong>
                                </td>
                                                                <td>{{ ($order->created_on ?? $order->created_at)?->format('d/m/Y') ?? 'Sin fecha' }}</td>
                                <td>{{ $order->buyer->name }}</td>
                                <td>{{ $order->provider->business_name }}</td>
                                <td>${{ number_format((float) $order->total, 0) }}</td>
                                <td>
                                    @if ($order->status === 'paid' && $order->payment)
                                        <a class="status {{ \App\Support\UiStatus::purchaseOrderClass($order->status, 'finance') }}" href="{{ route('finance.orders.payment-receipt', $order) }}" target="_blank" title="Descargar comprobante de pago">
                                            {{ \App\Support\UiStatus::purchaseOrder($order->status, 'finance') }}
                                        </a>
                                    @elseif ($order->status === 'rejected')
                                        <details class="rejection-popover">
                                            <summary class="status {{ \App\Support\UiStatus::purchaseOrderClass($order->status, 'finance') }}">
                                                {{ \App\Support\UiStatus::purchaseOrder($order->status, 'finance') }}
                                            </summary>
                                            <div class="rejection-popover-panel">
                                                <button class="rejection-popover-close" type="button" aria-label="Cerrar motivo">x</button>
                                                <strong>Motivo del rechazo</strong>
                                                <p>{{ $order->rejection_reason ?: 'Sin motivo registrado.' }}</p>
                                            </div>
                                        </details>
                                    @else
                                        <span class="status {{ \App\Support\UiStatus::purchaseOrderClass($order->status, 'finance') }}">
                                            {{ \App\Support\UiStatus::purchaseOrder($order->status, 'finance') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if ($order->payment)
                                        <a class="button ghost small" href="{{ route('finance.orders.payment-receipt', $order) }}" target="_blank" rel="noopener" aria-label="Ver comprobante de pago">Ver</a>
                                        @if ($order->status === 'paid')
                                            <form class="replace-payment-form" method="POST" action="{{ route('finance.orders.payment-receipt.replace', $order) }}" enctype="multipart/form-data" data-replace-payment-form>
                                                @csrf
                                                <input id="payment-file-{{ $order->id }}" name="payment_file" type="file" accept=".pdf,.jpg,.jpeg,.png,.webp" required hidden data-replace-payment-input>
                                                <button class="button ghost small" type="button" data-replace-payment-trigger>Cambiar comprobante</button>
                                            </form>
                                        @endif
                                    @else
                                        Sin pago
                                    @endif
                                </td>
                                <td>
                                    {{ $order->history_event_date?->format('d/m/Y') ?? 'Sin fecha' }}
                                    <small class="fine-print">{{ $order->history_event_label }}</small>
                                </td>
                                <td>
                                    {{ $receiptText }}
                                    @if ($receiptDetail)
                                        <small class="fine-print">{{ $receiptDetail }}</small>
                                    @endif
                                </td>
                                <td>{{ $order->warehouse ?: 'Sin almacen asignado' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10">No hay historial para mostrar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <dialog class="confirm-dialog" id="replace-payment-confirmation" aria-labelledby="replace-payment-title" data-replace-payment-dialog>
            <div class="confirm-card">
                <h3 id="replace-payment-title">Sustituir comprobante</h3>
                <p>Se va a sustituir el archivo, ¿estás seguro?</p>
                <div class="form-actions">
                    <button class="button ghost" type="button" data-replace-payment-cancel>No</button>
                    <button class="button primary" type="button" data-replace-payment-confirm>Sí</button>
                </div>
            </div>
        </dialog>
    </x-app-shell>
    <script>
        document.body.dataset.generalExportReady = 'true';
    </script>
    <style>
        .finance-history-panel th:nth-child(2),
        .finance-history-panel td:nth-child(2) {
            min-width: 130px;
            white-space: nowrap;
        }

        .finance-history-panel th:nth-child(3),
        .finance-history-panel td:nth-child(3) {
            min-width: 180px;
        }
    </style>

    <style>
        .replace-payment-form { margin-top: 8px; }
        .replace-payment-form .button { justify-self: start; }
        .rejection-popover { position: relative; display: inline-block; }
        .rejection-popover summary { cursor: pointer; list-style: none; }
        .rejection-popover summary::-webkit-details-marker { display: none; }
        .rejection-popover summary::after { content: 'v'; margin-left: 4px; opacity: .7; }
        .rejection-popover[open] summary::after { content: '^'; }
        .rejection-popover-panel { position: absolute; z-index: 60; top: calc(100% + 8px); left: 0; width: 260px; padding: 12px 34px 12px 12px; border: 1px solid #f1b8b4; border-radius: 8px; background: #fff; box-shadow: 0 14px 32px rgba(35, 48, 73, .22); color: #233049; }
        .rejection-popover-panel strong { display: block; margin-bottom: 6px; color: #b42318; font-size: .82rem; }
        .rejection-popover-panel p { margin: 0; font-size: .86rem; line-height: 1.35; white-space: normal; }
        .rejection-popover-close { position: absolute; top: 8px; right: 8px; width: 22px; height: 22px; border: 1px solid #f1b8b4; border-radius: 999px; background: #fdecec; color: #b42318; font-weight: 900; cursor: pointer; line-height: 1; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const replacementDialog = document.querySelector('[data-replace-payment-dialog]');
            const replacementCancel = replacementDialog?.querySelector('[data-replace-payment-cancel]');
            const replacementConfirm = replacementDialog?.querySelector('[data-replace-payment-confirm]');
            let pendingReplacementForm = null;

            const cancelReplacement = () => {
                const selectedInput = pendingReplacementForm?.querySelector('[data-replace-payment-input]');
                if (selectedInput) {
                    selectedInput.value = '';
                }
                pendingReplacementForm = null;

                if (replacementDialog?.open) {
                    replacementDialog.close();
                }
            };

            document.querySelectorAll('[data-replace-payment-form]').forEach((form) => {
                const input = form.querySelector('[data-replace-payment-input]');
                const trigger = form.querySelector('[data-replace-payment-trigger]');

                trigger?.addEventListener('click', () => {
                    input.value = '';
                    input.click();
                });

                input?.addEventListener('change', () => {
                    if (!input.files?.length || !replacementDialog) {
                        return;
                    }

                    pendingReplacementForm = form;
                    replacementDialog.showModal();
                });
            });

            replacementCancel?.addEventListener('click', cancelReplacement);
            replacementDialog?.addEventListener('cancel', (event) => {
                event.preventDefault();
                cancelReplacement();
            });

            replacementConfirm?.addEventListener('click', () => {
                if (!pendingReplacementForm) {
                    return;
                }

                const form = pendingReplacementForm;
                pendingReplacementForm = null;
                replacementDialog.close();
                form.requestSubmit();
            });

            document.querySelectorAll('.rejection-popover-close').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    button.closest('.rejection-popover')?.removeAttribute('open');
                });
            });

            document.addEventListener('click', (event) => {
                document.querySelectorAll('.rejection-popover[open]').forEach((popover) => {
                    if (!popover.contains(event.target)) {
                        popover.removeAttribute('open');
                    }
                });
            });
        });
    </script>
@endsection
