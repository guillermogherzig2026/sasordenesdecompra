@if ($payments->isNotEmpty())
    @php
        $fuelBreakdowns = $payments->mapWithKeys(fn ($payment) => [$payment->id => [
            'provider' => $payment->entry->name,
            'total' => $payment->amount,
            'allocations' => $payment->allocations->map(fn ($allocation) => [
                'reference' => $allocation->reference_number,
                'recipient' => $allocation->recipient_name,
                'amount' => $allocation->amount,
            ])->all(),
        ]])->all();
    @endphp
    <dialog class="fuel-breakdown-dialog" data-fuel-breakdown-dialog aria-labelledby="fuel-breakdown-title">
        <header class="fuel-breakdown-header">
            <div>
                <h2 id="fuel-breakdown-title">Asignación de vales de gasolina</h2>
                <p data-fuel-breakdown-provider></p>
            </div>
            <button class="fuel-breakdown-close" type="button" data-fuel-breakdown-close aria-label="Cerrar" title="Cerrar">&times;</button>
        </header>
        <div class="fuel-breakdown-body">
            <table>
                <thead><tr><th>Referencia / tarjeta</th><th>Usuario</th><th>Monto asignado (MXN)</th></tr></thead>
                <tbody data-fuel-breakdown-rows></tbody>
                <tfoot><tr><th colspan="2">Total transferido</th><td data-fuel-breakdown-total></td></tr></tfoot>
            </table>
        </div>
    </dialog>
    <style>
        .fuel-breakdown-dialog { border: 1px solid var(--line); border-radius: 8px; box-shadow: 0 24px 64px rgba(16, 43, 58, .24); color: var(--text); max-height: calc(100dvh - 32px); padding: 0; width: min(700px, calc(100vw - 32px)); }
        .fuel-breakdown-dialog::backdrop { background: rgba(16, 43, 58, .55); }
        .fuel-breakdown-header { align-items: flex-start; border-bottom: 1px solid var(--line); display: flex; gap: 16px; justify-content: space-between; padding: 16px 20px; }
        .fuel-breakdown-header h2 { font-size: 1.1rem; margin: 0; }
        .fuel-breakdown-header p { color: var(--muted); margin: 4px 0 0; }
        .fuel-breakdown-close { align-items: center; background: #fff; border: 1px solid var(--line); border-radius: 6px; color: var(--text); cursor: pointer; display: inline-flex; flex: 0 0 34px; font-size: 1.3rem; height: 34px; justify-content: center; padding: 0; }
        .fuel-breakdown-body { overflow: auto; padding: 16px 20px 20px; }
        .fuel-breakdown-body table { border-collapse: collapse; min-width: 490px; width: 100%; }
        .fuel-breakdown-body th, .fuel-breakdown-body td { border-bottom: 1px solid var(--line); padding: 10px 8px; text-align: left; }
        .fuel-breakdown-body th { background: #e8f3f8; font-size: .76rem; }
        .fuel-breakdown-body td { font-size: .85rem; }
        .fuel-breakdown-body th:last-child, .fuel-breakdown-body td:last-child { text-align: right; }
        .fuel-breakdown-body tfoot th, .fuel-breakdown-body tfoot td { background: #f4f9fc; font-weight: 800; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const breakdowns = {{ \Illuminate\Support\Js::from($fuelBreakdowns) }};
            const dialog = document.querySelector('[data-fuel-breakdown-dialog]');
            const formatter = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });
            document.querySelectorAll('[data-fuel-breakdown]').forEach((button) => {
                button.addEventListener('click', () => {
                    const payment = breakdowns[button.dataset.fuelBreakdown];
                    if (!payment || !dialog) return;
                    dialog.querySelector('[data-fuel-breakdown-provider]').textContent = payment.provider;
                    dialog.querySelector('[data-fuel-breakdown-total]').textContent = formatter.format(Number(payment.total));
                    const tbody = dialog.querySelector('[data-fuel-breakdown-rows]');
                    tbody.replaceChildren();
                    payment.allocations.forEach((allocation) => {
                        const row = document.createElement('tr');
                        [allocation.reference, allocation.recipient, formatter.format(Number(allocation.amount))].forEach((value) => {
                            const cell = document.createElement('td');
                            cell.textContent = value;
                            row.append(cell);
                        });
                        tbody.append(row);
                    });
                    dialog.showModal();
                });
            });
            dialog?.querySelector('[data-fuel-breakdown-close]')?.addEventListener('click', () => dialog.close());
            dialog?.addEventListener('click', (event) => {
                if (event.target === dialog) dialog.close();
            });
        });
    </script>
@endif
