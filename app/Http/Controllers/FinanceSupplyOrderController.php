<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SupplyOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceSupplyOrderController extends Controller
{
    public function active(Request $request)
    {
        $this->ensureFinance();

        return view('finance.supply-orders.active', [
            'orders' => $this->orders($request, ['sent', 'approved', 'remitted']),
            'query' => trim((string) $request->query('q')),
        ]);
    }

    public function history(Request $request)
    {
        $this->ensureFinance();

        return view('finance.supply-orders.history', [
            'orders' => $this->orders($request, ['delivered', 'rejected', 'canceled']),
            'query' => trim((string) $request->query('q')),
        ]);
    }

    public function approve(SupplyOrder $supplyOrder)
    {
        $this->ensureFinance();
        abort_unless($supplyOrder->status === 'sent', 403);

        $supplyOrder->update(['status' => 'approved']);
        $this->audit($supplyOrder, 'approved', 'OS autorizada por Finanzas.');

        return redirect()->route('finance.supply-orders.active')->with('status', "{$supplyOrder->folio} autorizada.");
    }

    public function reject(Request $request, SupplyOrder $supplyOrder)
    {
        $this->ensureFinance();
        abort_unless(in_array($supplyOrder->status, ['sent', 'approved'], true), 403);

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $reason = $validated['reason'] ?: 'No cumple criterios de autorizacion';
        $supplyOrder->update(['status' => 'rejected']);
        $this->audit($supplyOrder, 'rejected', "OS rechazada: {$reason}.");

        return redirect()->route('finance.supply-orders.history')->with('status', "{$supplyOrder->folio} rechazada.");
    }

    public function remission(SupplyOrder $supplyOrder)
    {
        $this->ensureFinance();
        abort_unless(in_array($supplyOrder->status, ['remitted', 'delivered'], true), 404);

        return view('inventory.supply-orders.remission', [
            'order' => $supplyOrder->load(['requester', 'company', 'items.catalogItem', 'deliveredBy']),
        ]);
    }

    private function orders(Request $request, array $statuses)
    {
        $query = trim((string) $request->query('q'));

        return SupplyOrder::with(['requester', 'company', 'items.catalogItem', 'deliveredBy'])
            ->whereIn('status', $statuses)
            ->when($query, function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('folio', 'like', "%{$query}%")
                        ->orWhereHas('requester', fn ($user) => $user->where('name', 'like', "%{$query}%"))
                        ->orWhereHas('company', fn ($company) => $company->where('name', 'like', "%{$query}%"))
                        ->orWhereHas('items', fn ($items) => $items->where('article', 'like', "%{$query}%"));
                });
            })
            ->orderByDesc('created_on')
            ->get();
    }

    private function audit(SupplyOrder $order, string $action, string $description): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => SupplyOrder::class,
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
