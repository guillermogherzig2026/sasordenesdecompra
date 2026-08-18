<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderPayment;
use App\Support\StoredFileResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FinancePurchaseOrderController extends Controller
{
    public function active(Request $request)
    {
        $this->ensureFinance();

        $query = trim((string) $request->query('q'));
        $orders = PurchaseOrder::with(['buyer', 'company', 'provider', 'payment', 'auditLogs'])
            ->general()
            ->whereIn('status', ['sent', 'approved'])
            ->when($query, function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('folio', 'like', "%{$query}%")
                        ->orWhereHas('buyer', fn ($buyer) => $buyer->where('name', 'like', "%{$query}%"))
                        ->orWhereHas('company', fn ($company) => $company->where('name', 'like', "%{$query}%"))
                        ->orWhereHas('provider', fn ($provider) => $provider->where('business_name', 'like', "%{$query}%"));
                });
            })
            ->orderBy('due_date')
            ->get();

        return view('finance.orders.active', [
            'orders' => $orders,
            'query' => $query,
        ]);
    }

    public function history(Request $request)
    {
        $this->ensureFinance();

        $query = trim((string) $request->query('q'));
        $orders = PurchaseOrder::with(['buyer', 'company', 'provider', 'payment', 'auditLogs'])
            ->general()
            ->whereIn('status', ['paid', 'rejected', 'canceled'])
            ->when($query, function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('folio', 'like', "%{$query}%")
                        ->orWhereHas('buyer', fn ($buyer) => $buyer->where('name', 'like', "%{$query}%"))
                        ->orWhereHas('company', fn ($company) => $company->where('name', 'like', "%{$query}%"))
                        ->orWhereHas('provider', fn ($provider) => $provider->where('business_name', 'like', "%{$query}%"));
                });
            })
            ->get()
            ->each(function (PurchaseOrder $order) {
                $order->history_event_date = $this->historyEventDate($order);
                $order->history_event_label = match ($order->status) {
                    'paid' => 'Pago',
                    'rejected' => 'Rechazo',
                    'canceled' => 'Cancelacion',
                    default => 'Movimiento',
                };
                $order->rejection_reason = $this->rejectionReason($order);
            })
            ->sortByDesc(fn (PurchaseOrder $order) => $order->history_event_date?->timestamp ?? 0)
            ->values();

        return view('finance.orders.history', [
            'orders' => $orders,
            'query' => $query,
        ]);
    }

    public function approve(PurchaseOrder $purchaseOrder)
    {
        $this->ensureFinance();
        $this->ensureGeneralOrder($purchaseOrder);
        abort_unless($purchaseOrder->status === 'sent', 403);

        $purchaseOrder->update(['status' => 'approved']);
        $this->audit($purchaseOrder, 'approved', 'OC aprobada por Finanzas. Notificacion enviada al comprador.');

        return redirect()->route('finance.orders.active')->with('status', "{$purchaseOrder->folio} aprobada.");
    }

    public function reject(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->ensureFinance();
        $this->ensureGeneralOrder($purchaseOrder);
        abort_unless(in_array($purchaseOrder->status, ['sent', 'approved'], true), 403);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $purchaseOrder->update(['status' => 'rejected']);
        $this->audit($purchaseOrder, 'rejected', "OC rechazada: {$validated['reason']}.");

        return redirect()->route('finance.orders.history')->with('status', "{$purchaseOrder->folio} rechazada y enviada a Historial.");
    }

    public function paymentForm(PurchaseOrder $purchaseOrder)
    {
        $this->ensureFinance();
        $this->ensureGeneralOrder($purchaseOrder);
        abort_unless($purchaseOrder->status === 'approved', 403);

        return view('finance.orders.payment', [
            'order' => $purchaseOrder->load(['buyer', 'company', 'provider', 'items']),
        ]);
    }

    public function storePayment(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->ensureFinance();
        $this->ensureGeneralOrder($purchaseOrder);
        abort_unless($purchaseOrder->status === 'approved', 403);

        $validated = $request->validate([
            'payment_file' => ['required', 'file', 'max:10240'],
            'paid_on' => ['required', 'date'],
        ]);

        DB::transaction(function () use ($request, $purchaseOrder, $validated) {
            $path = $request->file('payment_file')->store('payments');

            PurchaseOrderPayment::updateOrCreate(
                ['purchase_order_id' => $purchaseOrder->id],
                [
                    'paid_by' => Auth::id(),
                    'file_path' => $path,
                    'original_name' => $request->file('payment_file')->getClientOriginalName(),
                    'paid_on' => $validated['paid_on'],
                ],
            );

            $purchaseOrder->update(['status' => 'paid']);
            $this->audit($purchaseOrder, 'paid', 'Pago registrado con archivo adjunto.');
        });

        return redirect()->route('finance.orders.active')->with('status', "{$purchaseOrder->folio} pagada.");
    }

    public function replacePaymentReceipt(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->ensureFinance();
        $this->ensureGeneralOrder($purchaseOrder);
        abort_unless($purchaseOrder->status === 'paid', 403);

        $validated = $request->validate([
            'payment_file' => ['required', 'file', 'max:10240'],
        ]);

        $payment = $purchaseOrder->payment;
        abort_unless($payment, 404);

        DB::transaction(function () use ($request, $purchaseOrder, $payment) {
            if ($payment->file_path && Storage::exists($payment->file_path)) {
                Storage::delete($payment->file_path);
            }

            $file = $request->file('payment_file');
            $payment->update([
                'paid_by' => Auth::id(),
                'file_path' => $file->store('payments'),
                'original_name' => $file->getClientOriginalName(),
            ]);

            $this->audit($purchaseOrder, 'payment_replaced', 'Comprobante de pago reemplazado por Finanzas.');
        });

        return redirect()->route('finance.orders.history')->with('status', "Comprobante de {$purchaseOrder->folio} actualizado.");
    }

    public function print(PurchaseOrder $purchaseOrder)
    {
        $this->ensureFinance();
        $this->ensureGeneralOrder($purchaseOrder);

        return view('finance.orders.print', [
            'order' => $purchaseOrder->load(['buyer', 'company', 'provider', 'items', 'payment']),
        ]);
    }

    public function paymentReceipt(PurchaseOrder $purchaseOrder)
    {
        $this->ensureFinance();
        $this->ensureGeneralOrder($purchaseOrder);

        $payment = $purchaseOrder->payment;

        abort_unless($payment, 404);

        return StoredFileResponse::download($payment->file_path, $payment->original_name);
    }

    public function quoteSupport(PurchaseOrder $purchaseOrder)
    {
        $this->ensureFinance();
        $this->ensureGeneralOrder($purchaseOrder);

        return StoredFileResponse::download($purchaseOrder->quote_file_path, $purchaseOrder->quote_original_name ?: $purchaseOrder->folio.'-cotizacion');
    }

    private function audit(PurchaseOrder $order, string $action, string $description): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => PurchaseOrder::class,
            'auditable_id' => $order->id,
            'action' => $action,
            'description' => $description,
        ]);
    }

    private function rejectionReason(PurchaseOrder $order): ?string
    {
        if ($order->status !== 'rejected') {
            return null;
        }

        $description = $order->auditLogs
            ->where('action', 'rejected')
            ->sortByDesc('created_at')
            ->first()
            ?->description;

        if (! $description) {
            return null;
        }

        return trim(str_replace(['OC rechazada:', 'OC rechazada'], '', $description), " .\t\n\r\0\x0B");
    }

    private function historyEventDate(PurchaseOrder $order)
    {
        return match ($order->status) {
            'paid' => $order->payment?->paid_on,
            'rejected' => $order->auditLogs
                ->where('action', 'rejected')
                ->sortByDesc('created_at')
                ->first()
                ?->created_at,
            'canceled' => $order->auditLogs
                ->where('action', 'canceled')
                ->sortByDesc('created_at')
                ->first()
                ?->created_at ?? $order->updated_at,
            default => $order->updated_at,
        };
    }

    private function ensureFinance(): void
    {
        abort_unless(Auth::user()?->canAccessRole('finance'), 403);
    }

    private function ensureGeneralOrder(PurchaseOrder $order): void
    {
        abort_unless($order->construction_project_id === null, 404);
    }
}
