@php
    $financeContext = $financeContext ?? false;
    $allowPaymentUpload = $allowPaymentUpload ?? false;
    $allowDiscard = $allowDiscard ?? false;
    $emptyMessage = $emptyMessage ?? 'No hay pagos registrados.';
    $money = fn ($value) => '$'.number_format((float) $value, 2);
@endphp

<div class="table-scroll construction-payment-table-scroll">
    <table class="construction-payment-table">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Codigo</th>
                <th>Descripcion</th>
                <th>Contratista</th>
                <th>Periodo</th>
                <th>Fecha limite de pago</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Factura</th>
                <th>Pago</th>
                <th>Fecha de Pago</th>
                @if ($allowDiscard)
                    <th data-no-filter data-no-sort>Descartar</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($paymentOrders as $paymentOrder)
                @php
                    $invoiceUrl = filled($paymentOrder->invoice_file_path)
                        ? ($financeContext
                            ? route('finance.construction-payment-orders.invoice', $paymentOrder)
                            : route('construction.payment-orders.invoice', $paymentOrder))
                        : null;
                    $paymentUrl = filled($paymentOrder->payment_file_path)
                        ? ($financeContext
                            ? route('finance.construction-payment-orders.payment', $paymentOrder)
                            : route('construction.payment-orders.payment', $paymentOrder))
                        : null;
                @endphp
                <tr data-construction-payment-order="{{ $paymentOrder->id }}" data-payment-code="{{ $paymentOrder->code }}">
                    <td><span class="labor-type-badge {{ strtolower($paymentOrder->type) }}">{{ $paymentOrder->type }}</span></td>
                    <td><strong>{{ $paymentOrder->code }}</strong></td>
                    <td>{{ $paymentOrder->description }}</td>
                    <td>{{ $paymentOrder->contractor ?: '-' }}</td>
                    <td>{{ $paymentOrder->periodLabel() }}</td>
                    <td>{{ $paymentOrder->payment_due_date?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $money($paymentOrder->amount) }}</td>
                    <td><span class="status {{ $paymentOrder->statusClass() }}">{{ $paymentOrder->displayStatus() }}</span></td>
                    <td>
                        <div class="labor-file-actions">
                            @if ($invoiceUrl)
                                <a class="button ghost small labor-view-button" href="{{ $invoiceUrl }}" target="_blank" rel="noopener">Ver</a>
                            @else
                                <button class="button ghost small labor-view-button" type="button" disabled aria-disabled="true" title="Sin archivo adjunto">Ver</button>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="labor-file-actions">
                            @if ($allowPaymentUpload && blank($paymentOrder->payment_file_path))
                                <form method="POST" action="{{ route('finance.construction-payment-orders.payment.store', $paymentOrder) }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="paid_on" value="{{ now()->toDateString() }}">
                                    <label class="button primary small" title="Subir comprobante de pago">
                                        Subir
                                        <input class="file-upload-input" type="file" name="payment_file" accept=".pdf,.jpg,.jpeg,.png,.webp" data-auto-file-submit required>
                                    </label>
                                </form>
                            @endif
                            @if ($paymentUrl)
                                <a class="button ghost small labor-view-button" href="{{ $paymentUrl }}" target="_blank" rel="noopener">Ver</a>
                            @else
                                <button class="button ghost small labor-view-button" type="button" disabled aria-disabled="true" title="Sin archivo adjunto">Ver</button>
                            @endif
                        </div>
                    </td>
                    <td>{{ $paymentOrder->paid_on?->format('d/m/Y') ?? '-' }}</td>
                    @if ($allowDiscard)
                        <td>
                            <form method="POST" action="{{ route('finance.construction-payment-orders.discard', $paymentOrder) }}">
                                @csrf
                                @method('PATCH')
                                <button class="button danger small" type="submit">Descartar</button>
                            </form>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td class="empty-state" colspan="{{ $allowDiscard ? 12 : 11 }}">{{ $emptyMessage }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($allowPaymentUpload)
    <script>
        (() => {
            document.querySelectorAll('[data-auto-file-submit]').forEach((input) => {
                if (input.dataset.autoSubmitBound) return;
                input.dataset.autoSubmitBound = 'true';
                input.addEventListener('change', () => {
                    if (input.files?.length) input.form?.submit();
                });
            });
        })();
    </script>
@endif
