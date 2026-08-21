<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\ConstructionProject;
use App\Models\Provider;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderPayment;
use App\Support\StoredFileResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BuyerPurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureBuyer();

        $panel = $request->query('panel', 'all');
        $query = trim((string) $request->query('q'));

        $constructionContext = $this->isConstructionContext($request);
        $orders = $this->buyerOrders($request)
            ->with(['company', 'provider', 'payment', 'constructionProject'])
            ->when($panel === 'paid', fn ($builder) => $builder->where('status', 'paid'))
            ->when($panel === 'pending-payment', fn ($builder) => $builder->whereIn('status', ['sent', 'approved']))
            ->when($panel === 'rejected', fn ($builder) => $builder->where('status', 'rejected'))
            ->when($panel === 'all', fn ($builder) => $builder->where('status', '!=', 'rejected'))
            ->when($query, function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('folio', 'like', "%{$query}%")
                        ->orWhereHas('company', fn ($company) => $company->where('name', 'like', "%{$query}%"))
                        ->orWhereHas('provider', fn ($provider) => $provider->where('business_name', 'like', "%{$query}%"));
                });
            })
            ->when(
                $panel === 'paid',
                fn ($builder) => $builder->orderByDesc(
                    PurchaseOrderPayment::select('paid_on')
                        ->whereColumn('purchase_order_payments.purchase_order_id', 'purchase_orders.id')
                        ->limit(1)
                ),
                fn ($builder) => $builder->when(
                    $panel === 'all',
                    fn ($inner) => $inner->orderByDesc('created_at')->orderByDesc('id'),
                    fn ($inner) => $inner->orderBy('due_date')->orderByDesc('id')
                )
            )
            ->get();

        return view('buyer.orders.index', [
            'orders' => $orders,
            'panel' => $panel,
            'query' => $query,
            'constructionContext' => $constructionContext,
        ]);
    }

    public function create(Request $request)
    {
        $this->ensureBuyer();
        $constructionContext = $this->isConstructionContext($request);

        return view('buyer.orders.form', [
            'order' => null,
            'companies' => $this->allowedCompanies(),
            'providers' => $this->providersForCurrentUser(),
            'constructionContext' => $constructionContext,
            'constructionProjects' => $this->constructionProjects($request, $constructionContext),
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureBuyer();

        $constructionContext = $this->isConstructionContext($request);
        $validated = $this->validatedOrder($request, $constructionContext);
        $buyer = Auth::user();
        $company = $this->allowedCompanies()->firstWhere('id', (int) $validated['company_id']);
        $provider = $this->providersForCurrentUser()->firstWhere('id', (int) $validated['provider_id']);

        abort_unless($company && $provider, 403);

        $order = DB::transaction(function () use ($request, $validated, $buyer, $company, $provider, $constructionContext) {
            $total = $this->itemsTotal($validated['items']);
            $warehouse = $this->validatedWarehouse($validated['warehouse'] ?? null, $company);
            $orderPayload = [
                'folio' => $this->nextFolio(),
                'buyer_id' => $buyer->id,
                'construction_project_id' => $constructionContext ? $validated['construction_project_id'] : null,
                'company_id' => $company->id,
                'provider_id' => $provider->id,
                'created_on' => now()->toDateString(),
                'due_date' => $validated['due_date'],
                'is_credit' => $validated['is_credit'],
                'credit_days' => $validated['credit_days'],
                'reference' => $validated['reference'] ?: $provider->reference,
                'payment_concept' => $validated['payment_concept'],
                'observations' => $validated['observations'],
                'delivery_date' => $validated['delivery_date'],
                'status' => 'sent',
                'receipt_status' => 'pending',
                'total' => $total,
            ];

            if (Schema::hasColumn('purchase_orders', 'warehouse')) {
                $orderPayload['warehouse'] = $warehouse;
            }

            $orderPayload = array_merge($orderPayload, $this->quotePayload($request));
            $order = PurchaseOrder::create($orderPayload);

            $this->syncItems($order, $validated['items']);
            $this->audit($order, 'sent', 'OC enviada a Finanzas para revision.');

            return $order;
        });

        return redirect()->route('buyer.orders.index', $this->contextRouteParameters($request))->with('status', "Orden {$order->folio} creada.");
    }

    public function edit(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->ensureOwner($purchaseOrder);
        $this->ensureOrderContext($request, $purchaseOrder);
        abort_unless($purchaseOrder->isEditableByBuyer(), 403);
        $constructionContext = $this->isConstructionContext($request);

        return view('buyer.orders.form', [
            'order' => $purchaseOrder->load('items'),
            'companies' => $this->allowedCompanies(),
            'providers' => $this->providersForCurrentUser(),
            'constructionContext' => $constructionContext,
            'constructionProjects' => $this->constructionProjects($request, $constructionContext),
        ]);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->ensureOwner($purchaseOrder);
        $this->ensureOrderContext($request, $purchaseOrder);
        abort_unless($purchaseOrder->isEditableByBuyer(), 403);

        $constructionContext = $this->isConstructionContext($request);
        $validated = $this->validatedOrder($request, $constructionContext);
        $company = $this->allowedCompanies()->firstWhere('id', (int) $validated['company_id']);
        $provider = $this->providersForCurrentUser()->firstWhere('id', (int) $validated['provider_id']);

        abort_unless($company && $provider, 403);

        DB::transaction(function () use ($request, $purchaseOrder, $validated, $company, $provider, $constructionContext) {
            $warehouse = $this->validatedWarehouse($validated['warehouse'] ?? null, $company);
            $orderPayload = [
                'construction_project_id' => $constructionContext ? $validated['construction_project_id'] : null,
                'company_id' => $company->id,
                'provider_id' => $provider->id,
                'due_date' => $validated['due_date'],
                'is_credit' => $validated['is_credit'],
                'credit_days' => $validated['credit_days'],
                'reference' => $validated['reference'],
                'payment_concept' => $validated['payment_concept'],
                'observations' => $validated['observations'],
                'delivery_date' => $validated['delivery_date'],
                'total' => $this->itemsTotal($validated['items']),
            ];

            if (Schema::hasColumn('purchase_orders', 'warehouse')) {
                $orderPayload['warehouse'] = $warehouse;
            }

            $quotePayload = $this->quotePayload($request, $purchaseOrder);
            $purchaseOrder->update(array_merge($orderPayload, $quotePayload));

            $purchaseOrder->items()->delete();
            $this->syncItems($purchaseOrder, $validated['items']);
            $this->audit($purchaseOrder, 'updated', 'OC actualizada por el comprador antes de aprobacion.');
        });

        return redirect()->route('buyer.orders.index', $this->contextRouteParameters($request))->with('status', "Orden {$purchaseOrder->folio} actualizada.");
    }

    public function cancel(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->ensureOwner($purchaseOrder);
        $this->ensureOrderContext($request, $purchaseOrder);
        abort_unless($purchaseOrder->isEditableByBuyer(), 403);

        $purchaseOrder->update(['status' => 'canceled']);
        $this->audit($purchaseOrder, 'canceled', 'OC cancelada por el comprador antes de aprobacion.');

        return redirect()->route('buyer.orders.index', $this->contextRouteParameters($request))->with('status', "Orden {$purchaseOrder->folio} cancelada.");
    }

    public function print(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->ensureOwner($purchaseOrder);
        $this->ensureOrderContext($request, $purchaseOrder);

        return view('finance.orders.print', [
            'order' => $purchaseOrder->load(['buyer', 'company', 'provider', 'items']),
        ]);
    }

    public function paymentReceipt(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->ensureOwner($purchaseOrder);
        $this->ensureOrderContext($request, $purchaseOrder);

        $payment = $purchaseOrder->payment;

        abort_unless($payment, 404);

        return StoredFileResponse::download($payment->file_path, $payment->original_name);
    }

    public function quoteSupport(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->ensureOwner($purchaseOrder);
        $this->ensureOrderContext($request, $purchaseOrder);

        return StoredFileResponse::download($purchaseOrder->quote_file_path, $purchaseOrder->quote_original_name ?: $purchaseOrder->folio.'-cotizacion');
    }

    private function validatedOrder(Request $request, bool $constructionContext): array
    {
        $validated = $request->validate([
            'construction_project_id' => [$constructionContext ? 'required' : 'nullable', 'integer', 'exists:construction_projects,id'],
            'company_id' => ['required', 'integer'],
            'provider_id' => ['required', 'integer'],
            'warehouse' => ['required', 'string', 'max:255'],
            'delivery_date' => ['required', 'date'],
            'due_date' => ['required', 'date'],
            'is_credit' => ['nullable', 'boolean'],
            'credit_days' => ['nullable', 'integer', 'min:1', 'max:366'],
            'reference' => ['nullable', 'string', 'max:255'],
            'payment_concept' => ['nullable', 'string', 'max:255'],
            'observations' => ['nullable', 'string', 'max:2000'],
            'quote_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.article' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ], [
            'warehouse.required' => 'Selecciona un almacen para enviar la orden de compra.',
        ]);

        $validated['is_credit'] = $request->boolean('is_credit');
        $validated['credit_days'] = $validated['is_credit'] ? (int) ($validated['credit_days'] ?? 0) : null;

        if ($validated['is_credit'] && ! $validated['credit_days']) {
            throw ValidationException::withMessages([
                'credit_days' => 'Indica los dias de credito.',
            ]);
        }

        if ($constructionContext) {
            $projectIsVisible = ConstructionProject::query()
                ->visibleTo($request->user())
                ->whereKey((int) $validated['construction_project_id'])
                ->exists();

            if (! $projectIsVisible) {
                throw ValidationException::withMessages([
                    'construction_project_id' => 'Selecciona una obra disponible.',
                ]);
            }
        }

        $validated['items'] = collect($validated['items'])
            ->filter(fn (array $item) => trim((string) $item['article']) !== '')
            ->values()
            ->all();

        abort_unless(count($validated['items']) > 0, 422);

        return $validated;
    }

    private function validatedWarehouse(?string $warehouse, Company $company): string
    {
        $warehouse = trim((string) $warehouse);
        $user = Auth::user();
        $availableWarehouses = $user?->role === 'superadmin'
            ? $company->warehouseList()
            : ($user?->authorizedWarehousesFor($company->name) ?: $company->warehouseList());

        if (! count($availableWarehouses)) {
            throw ValidationException::withMessages([
                'warehouse' => 'La empresa seleccionada no tiene almacenes disponibles. Registra un almacen antes de enviar la orden de compra.',
            ]);
        }

        if ($warehouse === '' || ! in_array($warehouse, $availableWarehouses, true)) {
            throw ValidationException::withMessages([
                'warehouse' => 'Selecciona un almacen autorizado para enviar la orden de compra.',
            ]);
        }

        return $warehouse;
    }

    private function quotePayload(Request $request, ?PurchaseOrder $order = null): array
    {
        if (! Schema::hasColumn('purchase_orders', 'quote_file_path')) {
            return [];
        }

        if (! $request->hasFile('quote_file')) {
            return [];
        }

        if ($order?->quote_file_path && Storage::exists($order->quote_file_path)) {
            Storage::delete($order->quote_file_path);
        }

        $file = $request->file('quote_file');

        return [
            'quote_file_path' => $file->store('purchase-order-quotes'),
            'quote_original_name' => $file->getClientOriginalName(),
        ];
    }

    private function syncItems(PurchaseOrder $order, array $items): void
    {
        foreach ($items as $item) {
            PurchaseOrderItem::create([
                'purchase_order_id' => $order->id,
                'article' => $item['article'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'line_total' => $item['quantity'] * $item['unit_price'],
            ]);
        }
    }

    private function nextFolio(): string
    {
        $latest = PurchaseOrder::query()
            ->where('folio', 'like', 'OC-%')
            ->get()
            ->map(fn (PurchaseOrder $order) => (int) str_replace('OC-', '', $order->folio))
            ->max();

        return 'OC-'.(($latest ?: 400) + 1);
    }

    private function itemsTotal(array $items): float
    {
        return collect($items)->sum(fn (array $item) => (float) $item['quantity'] * (float) $item['unit_price']);
    }

    private function buyerOrders(Request $request)
    {
        $orders = $this->isSuperAdmin()
            ? PurchaseOrder::query()
            : PurchaseOrder::where('buyer_id', Auth::id());

        return $this->isConstructionContext($request)
            ? $orders->forConstruction()
            : $orders->general();
    }

    private function constructionProjects(Request $request, bool $constructionContext)
    {
        if (! $constructionContext) {
            return collect();
        }

        return ConstructionProject::query()
            ->visibleTo($request->user())
            ->orderBy('project_key')
            ->get();
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

    private function providersForCurrentUser()
    {
        return Provider::query()
            ->when(! $this->isSuperAdmin(), fn ($builder) => $builder->where('buyer_id', Auth::id()))
            ->orderBy('business_name')
            ->get();
    }

    private function isSuperAdmin(): bool
    {
        return Auth::user()?->role === 'superadmin';
    }

    private function isConstructionContext(Request $request): bool
    {
        return $this->isSuperAdmin() && $request->query('context') === 'construction';
    }

    private function contextRouteParameters(Request $request): array
    {
        return $this->isConstructionContext($request) ? ['context' => 'construction'] : [];
    }

    private function ensureOrderContext(Request $request, PurchaseOrder $order): void
    {
        $matchesContext = $this->isConstructionContext($request)
            ? $order->construction_project_id !== null
            : $order->construction_project_id === null;

        abort_unless($matchesContext, 404);
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

    private function ensureBuyer(): void
    {
        abort_unless(Auth::user()?->canAccessBuyerSubrole('purchases'), 403);
    }

    private function ensureOwner(PurchaseOrder $order): void
    {
        $this->ensureBuyer();
        abort_unless($this->isSuperAdmin() || (int) $order->buyer_id === Auth::id(), 403);
    }
}
