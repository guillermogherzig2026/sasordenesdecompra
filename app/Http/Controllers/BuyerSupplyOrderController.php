<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\SupplyOrder;
use App\Models\SupplyOrderItem;
use App\Models\WarehouseCatalogItem;
use App\Models\WarehouseInventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BuyerSupplyOrderController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureSupplies();

        $panel = $request->query('panel', 'pending');
        $query = trim((string) $request->query('q'));

        $orders = SupplyOrder::with(['company', 'items.catalogItem'])
            ->with('requester')
            ->when(! $this->isSuperAdmin(), fn ($builder) => $builder->where('requester_id', Auth::id()))
            ->when(
                $panel === 'history',
                fn ($builder) => $builder->whereIn('status', ['delivered', 'rejected', 'canceled']),
                fn ($builder) => $builder->whereIn('status', ['sent', 'approved', 'remitted'])
            )
            ->when($query, function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('folio', 'like', "%{$query}%")
                        ->orWhereHas('company', fn ($company) => $company->where('name', 'like', "%{$query}%"))
                        ->orWhereHas('items', fn ($items) => $items->where('article', 'like', "%{$query}%"));
                });
            })
            ->orderByDesc('created_on')
            ->get();

        return view('buyer.supply-orders.index', [
            'orders' => $orders,
            'panel' => $panel,
            'query' => $query,
        ]);
    }

    public function create()
    {
        $this->ensureSupplies();

        return view('buyer.supply-orders.form', [
            'companies' => $this->allowedCompanies(),
            'catalogItems' => WarehouseCatalogItem::where('authorized', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureSupplies();

        $validated = $request->validate([
            'company_id' => ['required', 'integer'],
            'warehouse_to' => ['nullable', 'string', 'max:255'],
            'delivery_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.warehouse_catalog_item_id' => ['nullable', 'integer'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0.01'],
        ]);

        $company = $this->allowedCompanies()->firstWhere('id', (int) $validated['company_id']);
        abort_unless($company, 403);

        $order = DB::transaction(function () use ($validated, $company) {
            $catalogItemsById = WarehouseCatalogItem::query()
                ->where('authorized', true)
                ->whereIn('id', collect($validated['items'])->pluck('warehouse_catalog_item_id')->map(fn ($id) => (int) $id)->all())
                ->get()
                ->keyBy('id');

            $rows = collect($validated['items'])
                ->filter(fn (array $row) => filled($row['warehouse_catalog_item_id'] ?? null) && filled($row['quantity'] ?? null))
                ->map(function (array $row) use ($catalogItemsById) {
                    $catalogItem = $catalogItemsById->get((int) $row['warehouse_catalog_item_id']);
                    abort_unless($catalogItem, 422);

                    $quantity = (float) $row['quantity'];

                    return [
                        'item' => $catalogItem,
                        'quantity' => $quantity,
                    ];
                })
                ->filter(fn (array $row) => $row['quantity'] > 0)
                ->values();

            abort_if($rows->isEmpty(), 422, 'Agrega al menos un insumo.');

            $order = SupplyOrder::create([
                'folio' => $this->nextFolio(),
                'requester_id' => Auth::id(),
                'company_id' => $company->id,
                'warehouse_from' => WarehouseInventoryItem::CENTRAL_WAREHOUSE,
                'warehouse_to' => $validated['warehouse_to'] ?? null,
                'created_on' => now()->toDateString(),
                'delivery_date' => $validated['delivery_date'] ?? null,
                'due_date' => $validated['due_date'] ?? null,
                'status' => 'sent',
                'notes' => $validated['notes'] ?? null,
                'total' => 0,
            ]);

            foreach ($rows as $row) {
                $item = $row['item'];
                SupplyOrderItem::create([
                    'supply_order_id' => $order->id,
                    'warehouse_catalog_item_id' => $item->id,
                    'article' => $item->name,
                    'quantity' => $row['quantity'],
                    'unit_cost' => $item->unit_cost ?? 0,
                    'line_total' => round($row['quantity'] * (float) ($item->unit_cost ?? 0), 2),
                ]);
            }

            $order->update([
                'total' => $order->items()->sum('line_total'),
            ]);

            $this->audit($order, 'sent', 'OS enviada a Finanzas para autorizacion.');

            return $order;
        });

        return redirect()->route('buyer.supply-orders.index')->with('status', "{$order->folio} enviada.");
    }

    public function remission(SupplyOrder $supplyOrder)
    {
        $this->ensureSupplies();
        abort_unless($this->isSuperAdmin() || (int) $supplyOrder->requester_id === Auth::id(), 403);
        abort_unless(in_array($supplyOrder->status, ['remitted', 'delivered'], true), 404);

        return view('inventory.supply-orders.remission', [
            'order' => $supplyOrder->load(['requester', 'company', 'items.catalogItem', 'deliveredBy']),
        ]);
    }

    private function nextFolio(): string
    {
        $latest = SupplyOrder::query()
            ->where('folio', 'like', 'OS-%')
            ->get()
            ->map(fn (SupplyOrder $order) => (int) str_replace('OS-', '', $order->folio))
            ->max();

        return 'OS-'.(($latest ?: 1000) + 1);
    }

    private function allowedCompanies()
    {
        if ($this->isSuperAdmin()) {
            return Company::orderBy('name')->get();
        }

        $names = Auth::user()?->authorizedCompanyNames() ?? [];

        return Company::query()
            ->when(count($names), fn ($builder) => $builder->whereIn('name', $names))
            ->when(! count($names), fn ($builder) => $builder->whereRaw('1 = 0'))
            ->orderBy('name')
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

    private function isSuperAdmin(): bool
    {
        return Auth::user()?->role === 'superadmin';
    }

    private function ensureSupplies(): void
    {
        abort_unless(Auth::user()?->canAccessBuyerSubrole('supplies'), 403);
    }
}
