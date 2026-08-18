<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderReceipt;
use App\Models\PurchaseOrderReceiptItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InventoryReceiptController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureInventory();

        $query = trim((string) $request->query('q'));
        $orders = PurchaseOrder::with(['buyer', 'company', 'provider', 'payment', 'items.receiptItems', 'receipts'])
            ->general()
            ->where('status', 'paid')
            ->where('receipt_status', '!=', 'completed')
            ->when(Auth::user()?->role !== 'superadmin', fn ($builder) => $this->restrictToAuthorizedCompanies($builder))
            ->when($query, fn ($builder) => $this->search($builder, $query))
            ->orderByDesc('updated_at')
            ->get();

        return view('inventory.orders.index', [
            'orders' => $orders,
            'query' => $query,
        ]);
    }

    public function history(Request $request)
    {
        $this->ensureInventory();

        $query = trim((string) $request->query('q'));
        $orders = PurchaseOrder::with(['buyer', 'company', 'provider', 'payment', 'receipts.items.purchaseOrderItem'])
            ->general()
            ->where('status', 'paid')
            ->where('receipt_status', 'completed')
            ->when(Auth::user()?->role !== 'superadmin', fn ($builder) => $this->restrictToAuthorizedCompanies($builder))
            ->when($query, fn ($builder) => $this->search($builder, $query))
            ->orderByDesc('updated_at')
            ->get();

        return view('inventory.orders.history', [
            'orders' => $orders,
            'query' => $query,
        ]);
    }

    public function create(PurchaseOrder $purchaseOrder)
    {
        $this->ensureInventory();
        $this->ensureGeneralOrder($purchaseOrder);
        abort_unless($purchaseOrder->isOpenForInventory(), 403);

        return view('inventory.orders.receipt', [
            'order' => $purchaseOrder->load(['buyer', 'company', 'provider', 'items.receiptItems.receipt', 'receipts.items.purchaseOrderItem']),
            'progress' => $this->progress($purchaseOrder),
        ]);
    }

    public function print(PurchaseOrder $purchaseOrder)
    {
        $this->ensureInventory();
        $this->ensureGeneralOrder($purchaseOrder);
        abort_unless($purchaseOrder->status === 'paid', 403);

        return view('finance.orders.print', [
            'order' => $purchaseOrder->load(['buyer', 'company', 'provider', 'items']),
        ]);
    }

    public function store(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->ensureInventory();
        $this->ensureGeneralOrder($purchaseOrder);
        abort_unless($purchaseOrder->isOpenForInventory(), 403);

        $validated = $request->validate([
            'receipt_file' => ['required', 'file', 'max:10240'],
            'invoice_number' => ['required', 'string', 'max:80'],
            'received_on' => ['required', 'date'],
            'items' => ['required', 'array'],
            'items.*.purchase_order_item_id' => ['required', 'integer'],
            'items.*.received_quantity' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($request, $purchaseOrder, $validated) {
            $purchaseOrder->load('items.receiptItems');
            $itemsById = $purchaseOrder->items->keyBy('id');
            $rows = collect($validated['items'])->map(function (array $row) use ($itemsById) {
                $item = $itemsById->get((int) $row['purchase_order_item_id']);
                abort_unless($item, 422);

                $alreadyReceived = (float) $item->receiptItems->sum('received_quantity');
                $remaining = max((float) $item->quantity - $alreadyReceived, 0);
                $received = (float) $row['received_quantity'];

                abort_if($received > $remaining, 422, 'La cantidad recibida no puede superar el restante.');

                return [
                    'item' => $item,
                    'received' => $received,
                    'remaining' => $remaining,
                ];
            })->filter(fn (array $row) => $row['received'] > 0)->values();

            abort_if($rows->isEmpty(), 422, 'Registra al menos una cantidad recibida.');

            $path = $request->file('receipt_file')->store('receipts');
            $receipt = PurchaseOrderReceipt::create([
                'purchase_order_id' => $purchaseOrder->id,
                'received_by' => Auth::id(),
                'file_path' => $path,
                'original_name' => $request->file('receipt_file')->getClientOriginalName(),
                'invoice_number' => $validated['invoice_number'],
                'received_on' => $validated['received_on'],
            ]);

            foreach ($rows as $row) {
                PurchaseOrderReceiptItem::create([
                    'purchase_order_receipt_id' => $receipt->id,
                    'purchase_order_item_id' => $row['item']->id,
                    'received_quantity' => $row['received'],
                ]);
            }

            $purchaseOrder->load('items.receiptItems');
            $completed = $purchaseOrder->items->every(function ($item) {
                return (float) $item->receiptItems->sum('received_quantity') >= (float) $item->quantity;
            });

            $purchaseOrder->update([
                'receipt_status' => $completed ? 'completed' : 'partial',
            ]);

            $this->audit(
                $purchaseOrder,
                $completed ? 'receipt_completed' : 'receipt_partial',
                $completed
                    ? 'Recepcion completada: cantidades recibidas coinciden con la OC.'
                    : 'Recepcion parcial registrada: la orden permanece en OC pagadas.',
            );
        });

        return redirect()->route('inventory.orders.index')
            ->with('status', "{$purchaseOrder->folio} registrada como {$purchaseOrder->fresh()->receipt_status}.");
    }

    private function restrictToAuthorizedCompanies($builder): void
    {
        $user = Auth::user();
        $assignments = $user?->normalizedCompanyAssignments() ?? [];

        if (! count($assignments)) {
            $builder->whereRaw('1 = 0');

            return;
        }

        $filterByWarehouse = Schema::hasColumn('purchase_orders', 'warehouse');
        $builder->where(function ($outer) use ($assignments, $filterByWarehouse) {
            foreach ($assignments as $assignment) {
                $companyName = $assignment['name'] ?? null;
                if (! $companyName) {
                    continue;
                }

                $outer->orWhere(function ($inner) use ($companyName, $assignment, $filterByWarehouse) {
                    $inner->whereHas('company', fn ($company) => $company->where('name', $companyName));

                    if ($filterByWarehouse && ! empty($assignment['warehouses'])) {
                        $inner->whereIn('warehouse', $assignment['warehouses']);
                    }
                });
            }
        });
    }

    private function search($builder, string $query): void
    {
        $builder->where(function ($inner) use ($query) {
            $inner->where('folio', 'like', "%{$query}%")
                ->orWhereHas('buyer', fn ($buyer) => $buyer->where('name', 'like', "%{$query}%"))
                ->orWhereHas('company', fn ($company) => $company->where('name', 'like', "%{$query}%"))
                ->orWhereHas('provider', fn ($provider) => $provider->where('business_name', 'like', "%{$query}%"));
        });
    }

    private function progress(PurchaseOrder $order): array
    {
        return $order->items->map(function ($item) {
            $received = (float) $item->receiptItems->sum('received_quantity');
            $ordered = (float) $item->quantity;

            return [
                'item' => $item,
                'ordered' => $ordered,
                'received' => $received,
                'remaining' => max($ordered - $received, 0),
            ];
        })->all();
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

    private function ensureInventory(): void
    {
        abort_unless(Auth::user()?->canAccessRole('inventory'), 403);
    }

    private function ensureGeneralOrder(PurchaseOrder $order): void
    {
        abort_unless($order->construction_project_id === null, 404);
    }
}
