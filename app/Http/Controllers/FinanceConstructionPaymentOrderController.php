<?php

namespace App\Http\Controllers;

use App\Models\ConstructionAuditLog;
use App\Models\ConstructionPaymentOrder;
use App\Support\StoredFileResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceConstructionPaymentOrderController extends Controller
{
    public function active(Request $request)
    {
        $this->ensureFinance();
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
                ->paid()
                ->search($query)
                ->with(['project', 'paidBy'])
                ->orderByDesc('paid_on')
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

        if ($paymentOrder->payroll) {
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
