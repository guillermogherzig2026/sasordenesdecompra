<?php

namespace App\Http\Controllers;

use App\Models\ConstructionAuditLog;
use App\Models\ConstructionPaymentOrder;
use App\Services\ConstructionPayrollScheduleService;
use App\Support\StoredFileResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceConstructionPaymentOrderController extends Controller
{
    public function __construct(
        private readonly ConstructionPayrollScheduleService $payrollSchedule
    ) {}

    public function active(Request $request)
    {
        $this->ensureFinance();
        $this->payrollSchedule->generateDueOccurrences();
        $query = trim((string) $request->query('q'));

        return view('finance.construction-payment-orders.active', [
            'orders' => ConstructionPaymentOrder::query()
                ->pending()
                ->search($query)
                ->with('project')
                ->orderBy('payment_due_date')
                ->orderBy('code')
                ->get(),
            'query' => $query,
        ]);
    }

    public function history(Request $request)
    {
        $this->ensureFinance();
        $query = trim((string) $request->query('q'));

        return view('finance.construction-payment-orders.history', [
            'orders' => ConstructionPaymentOrder::query()
                ->historical()
                ->search($query)
                ->with(['project', 'paidBy', 'discardedBy'])
                ->orderByDesc('updated_at')
                ->orderBy('code')
                ->get(),
            'query' => $query,
        ]);
    }

    public function storePayment(Request $request, ConstructionPaymentOrder $paymentOrder): RedirectResponse
    {
        $this->ensureFinance();
        abort_unless(blank($paymentOrder->payment_file_path), 403);

        $validated = $request->validate([
            'payment_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'paid_on' => ['required', 'date'],
        ]);

        $file = $request->file('payment_file');
        $paymentOrder->update([
            'status' => 'Pagado',
            'payment_file_path' => $file->store('construction-payment-receipts'),
            'payment_original_name' => $file->getClientOriginalName(),
            'paid_on' => $validated['paid_on'],
            'paid_by' => Auth::id(),
        ]);

        if (
            $paymentOrder->payroll
            && ! in_array(
                $paymentOrder->payroll->periodicity,
                ConstructionPayrollScheduleService::RECURRING_PERIODICITIES,
                true,
            )
        ) {
            $paymentOrder->payroll->update([
                'status' => 'Pagada',
                'payment_date' => $validated['paid_on'],
            ]);
        }

        ConstructionAuditLog::create([
            'user_id' => Auth::id(),
            'construction_project_id' => $paymentOrder->construction_project_id,
            'occurred_at' => now(),
            'module' => 'Administracion de obra',
            'action' => 'Pago de obra registrado',
            'description' => "Finanzas registro el comprobante de pago de {$paymentOrder->code}.",
            'new_values' => $paymentOrder->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()
            ->route('finance.construction-payment-orders.history')
            ->with('status', "{$paymentOrder->code} pagado y enviado al historial.");
    }

    public function discard(Request $request, ConstructionPaymentOrder $paymentOrder): RedirectResponse
    {
        $this->ensureFinance();
        abort_unless(
            blank($paymentOrder->payment_file_path)
                && blank($paymentOrder->dismissed_at)
                && blank($paymentOrder->discarded_at)
                && ! in_array($paymentOrder->status, ['Pagada', 'Pagado', 'Cancelada', 'Cancelado', 'Descartada'], true),
            422,
        );

        $oldValues = $paymentOrder->toArray();
        $paymentOrder->update([
            'status' => 'Descartada',
            'discarded_at' => now(),
            'discarded_by' => Auth::id(),
        ]);

        ConstructionAuditLog::create([
            'user_id' => Auth::id(),
            'construction_project_id' => $paymentOrder->construction_project_id,
            'occurred_at' => now(),
            'module' => 'Administracion de obra',
            'action' => 'Orden de pago descartada',
            'description' => "Finanzas descarto la orden de pago {$paymentOrder->code}.",
            'old_values' => $oldValues,
            'new_values' => $paymentOrder->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()
            ->route('finance.construction-payment-orders.history')
            ->with('status', "{$paymentOrder->code} descartada y enviada al historial.");
    }

    public function invoice(ConstructionPaymentOrder $paymentOrder)
    {
        $this->ensureFinance();
        abort_unless(filled($paymentOrder->invoice_file_path), 404);

        return StoredFileResponse::inline(
            $paymentOrder->invoice_file_path,
            $paymentOrder->invoice_original_name ?: $paymentOrder->code.'-factura'
        );
    }

    public function payment(ConstructionPaymentOrder $paymentOrder)
    {
        $this->ensureFinance();
        abort_unless(filled($paymentOrder->payment_file_path), 404);

        return StoredFileResponse::inline(
            $paymentOrder->payment_file_path,
            $paymentOrder->payment_original_name ?: $paymentOrder->code.'-pago'
        );
    }

    private function ensureFinance(): void
    {
        abort_unless(Auth::user()?->canAccessRole('finance'), 403);
    }
}
