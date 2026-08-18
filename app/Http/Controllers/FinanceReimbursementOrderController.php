<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ReimbursementOrder;
use App\Support\StoredFileResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceReimbursementOrderController extends Controller
{
    public function active(Request $request)
    {
        $this->ensureFinance();

        return view('finance.reimbursement-orders.active', [
            'orders' => $this->orders($request, ['sent', 'approved']),
            'query' => trim((string) $request->query('q')),
        ]);
    }

    public function history(Request $request)
    {
        $this->ensureFinance();

        return view('finance.reimbursement-orders.history', [
            'orders' => $this->orders($request, ['paid', 'rejected', 'canceled']),
            'query' => trim((string) $request->query('q')),
        ]);
    }

    public function approve(ReimbursementOrder $reimbursementOrder)
    {
        $this->ensureFinance();
        abort_unless($reimbursementOrder->status === 'sent', 403);

        $reimbursementOrder->update(['status' => 'approved']);
        $this->audit($reimbursementOrder, 'approved', 'OR autorizada por Finanzas.');

        return redirect()->route('finance.reimbursement-orders.active')->with('status', "{$reimbursementOrder->folio} autorizada.");
    }

    public function reject(Request $request, ReimbursementOrder $reimbursementOrder)
    {
        $this->ensureFinance();
        abort_unless(in_array($reimbursementOrder->status, ['sent', 'approved'], true), 403);

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $reason = ($validated['reason'] ?? null) ?: 'No cumple criterios de autorizacion';
        $reimbursementOrder->update(['status' => 'rejected']);
        $this->audit($reimbursementOrder, 'rejected', "OR rechazada: {$reason}.");

        return redirect()->route('finance.reimbursement-orders.history')->with('status', "{$reimbursementOrder->folio} rechazada.");
    }

    public function storePayment(Request $request, ReimbursementOrder $reimbursementOrder)
    {
        $this->ensureFinance();
        abort_unless($reimbursementOrder->status === 'approved', 403);

        $validated = $request->validate([
            'payment_file' => ['required', 'file', 'max:10240'],
            'paid_on' => ['required', 'date'],
        ]);

        $file = $request->file('payment_file');
        $reimbursementOrder->update([
            'status' => 'paid',
            'payment_file_path' => $file->store('reimbursement-payments'),
            'payment_original_name' => $file->getClientOriginalName(),
            'paid_on' => $validated['paid_on'],
            'paid_by' => Auth::id(),
        ]);
        $this->audit($reimbursementOrder, 'paid', 'Pago de OR registrado con archivo adjunto.');

        return redirect()->route('finance.reimbursement-orders.history')->with('status', "{$reimbursementOrder->folio} pagada.");
    }

    public function quote(ReimbursementOrder $reimbursementOrder)
    {
        $this->ensureFinance();

        return StoredFileResponse::download($reimbursementOrder->quote_file_path, $reimbursementOrder->quote_original_name ?: $reimbursementOrder->folio.'-cotizacion');
    }

    public function support(ReimbursementOrder $reimbursementOrder)
    {
        $this->ensureFinance();

        return StoredFileResponse::download($reimbursementOrder->support_file_path, $reimbursementOrder->support_original_name ?: $reimbursementOrder->folio.'-soporte');
    }

    public function payment(ReimbursementOrder $reimbursementOrder)
    {
        $this->ensureFinance();

        return StoredFileResponse::download($reimbursementOrder->payment_file_path, $reimbursementOrder->payment_original_name ?: $reimbursementOrder->folio.'-pago');
    }

    private function orders(Request $request, array $statuses)
    {
        $query = trim((string) $request->query('q'));

        return ReimbursementOrder::with(['requester', 'company', 'paidBy'])
            ->whereIn('status', $statuses)
            ->when($query, function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('folio', 'like', "%{$query}%")
                        ->orWhere('provider', 'like', "%{$query}%")
                        ->orWhereHas('requester', fn ($user) => $user->where('name', 'like', "%{$query}%"))
                        ->orWhereHas('company', fn ($company) => $company->where('name', 'like', "%{$query}%"));
                });
            })
            ->orderByDesc('created_on')
            ->get();
    }

    private function audit(ReimbursementOrder $order, string $action, string $description): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => ReimbursementOrder::class,
            'auditable_id' => $order->id,
            'action' => $action,
            'description' => $description,
        ]);
    }

    private function ensureFinance(): void
    {
        abort_unless(Auth::user()?->canAccessRole('finance'), 403);
    }
}
