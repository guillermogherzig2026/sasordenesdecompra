<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\PurchaseOrderReceiptItem;
use App\Models\SupplyOrder;
use App\Models\SupplyOrderItem;
use App\Models\WarehouseCatalogItem;
use App\Models\WarehouseInventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InventorySupplyOrderController extends Controller
{
    public function catalog()
    {
        $this->ensureCatalogView();

        return redirect()->route('inventory.warehouses.catalog', 'central');
    }

    public function warehouseCatalog(Request $request, string $warehouseKey)
    {
        $this->ensureCatalogView();

        $warehouse = $this->warehouseRows()->firstWhere('key', $warehouseKey);
        abort_unless($warehouse, 404);

        $catalogSearchQuery = trim((string) $request->query('q'));

        if (! $warehouse['is_central']) {
            $companyCatalogRows = $this->companyWarehouseCatalogRows($warehouse);

            return view('inventory.catalog.index', [
                'warehouse' => $warehouse,
                'items' => collect(),
                'companyCatalogRows' => $this->filterCatalogRows($companyCatalogRows, $catalogSearchQuery),
                'canManageCatalog' => false,
                'isSupplyCatalog' => false,
                'catalogSearchQuery' => $catalogSearchQuery,
                'catalogSearchSuggestions' => $this->catalogSuggestionsFromRows($companyCatalogRows),
                ...$this->catalogOptionLists(),
            ]);
        }

        $inventoryWarehouse = $this->inventoryWarehouseName($warehouse);
        $items = WarehouseCatalogItem::with([
            'inventories' => fn ($query) => $query->where('warehouse', $inventoryWarehouse),
        ])->orderBy('name')->get();

        return view('inventory.catalog.index', [
            'warehouse' => $warehouse,
            'items' => $this->filterCatalogItems($items, $catalogSearchQuery),
            'companyCatalogRows' => collect(),
            'canManageCatalog' => $this->canManageCentralCatalog(),
            'isSupplyCatalog' => true,
            'catalogSearchQuery' => $catalogSearchQuery,
            'catalogSearchSuggestions' => $this->catalogSuggestionsFromItems($items),
            ...$this->catalogOptionLists(),
        ]);
    }

    public function storeCatalog(Request $request, string $warehouseKey = 'central')
    {
        $this->ensureCatalogManage();

        $warehouse = $this->warehouseRows()->firstWhere('key', $warehouseKey);
        abort_unless($warehouse && $warehouse['is_central'], 404);

        $inventoryWarehouse = $this->inventoryWarehouseName($warehouse);

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:120'],
            'subcategory' => ['required', 'string', 'max:120'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'authorized' => ['nullable', 'boolean'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'minimum_quantity' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $inventoryWarehouse) {
            $item = WarehouseCatalogItem::create([
                'sku' => $this->nextCatalogSku(),
                'category' => $validated['category'],
                'subcategory' => $validated['subcategory'],
                'name' => $validated['name'],
                'unit' => $validated['unit'],
                'unit_cost' => $validated['unit_cost'] ?? 0,
                'description' => $validated['description'] ?? null,
                'authorized' => (bool) ($validated['authorized'] ?? true),
            ]);

            WarehouseInventoryItem::create([
                'warehouse_catalog_item_id' => $item->id,
                'warehouse' => $inventoryWarehouse,
                'quantity' => $validated['quantity'] ?? 0,
                'minimum_quantity' => $validated['minimum_quantity'] ?? 0,
            ]);
        });

        return redirect()->route('inventory.warehouses.catalog', $warehouse['key'])->with('status', 'Insumo agregado al catalogo del almacen.');
    }

    public function editCatalog(string $warehouseKey, WarehouseCatalogItem $warehouseCatalogItem)
    {
        $this->ensureCatalogManage();

        $warehouse = $this->warehouseRows()->firstWhere('key', $warehouseKey);
        abort_unless($warehouse && $warehouse['is_central'], 404);

        return view('inventory.catalog.edit', [
            'warehouse' => $warehouse,
            'item' => $warehouseCatalogItem->load([
                'inventories' => fn ($query) => $query->where('warehouse', $this->inventoryWarehouseName($warehouse)),
            ]),
            ...$this->catalogOptionLists(),
        ]);
    }

    public function editCatalogLegacy(WarehouseCatalogItem $warehouseCatalogItem)
    {
        return redirect()->route('inventory.warehouses.catalog.edit', [
            'warehouseKey' => 'central',
            'warehouseCatalogItem' => $warehouseCatalogItem,
        ]);
    }

    public function updateCatalogLegacy(Request $request, WarehouseCatalogItem $warehouseCatalogItem)
    {
        return $this->updateCatalog($request, 'central', $warehouseCatalogItem);
    }

    public function updateCatalog(Request $request, string $warehouseKey, WarehouseCatalogItem $warehouseCatalogItem)
    {
        $this->ensureCatalogManage();

        $warehouse = $this->warehouseRows()->firstWhere('key', $warehouseKey);
        abort_unless($warehouse && $warehouse['is_central'], 404);

        $inventoryWarehouse = $this->inventoryWarehouseName($warehouse);

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:120'],
            'subcategory' => ['required', 'string', 'max:120'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'unit_cost' => ['nullable', 'numeric', 'min:0'],
            'authorized' => ['nullable', 'boolean'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'minimum_quantity' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($warehouseCatalogItem, $validated, $inventoryWarehouse) {
            $warehouseCatalogItem->update([
                'category' => $validated['category'],
                'subcategory' => $validated['subcategory'],
                'name' => $validated['name'],
                'unit' => $validated['unit'],
                'unit_cost' => $validated['unit_cost'] ?? 0,
                'description' => $validated['description'] ?? null,
                'authorized' => (bool) ($validated['authorized'] ?? false),
            ]);

            WarehouseInventoryItem::updateOrCreate(
                [
                    'warehouse_catalog_item_id' => $warehouseCatalogItem->id,
                    'warehouse' => $inventoryWarehouse,
                ],
                [
                    'quantity' => $validated['quantity'],
                    'minimum_quantity' => $validated['minimum_quantity'] ?? 0,
                ],
            );
        });

        return redirect()->route('inventory.warehouses.catalog', $warehouse['key'])->with('status', 'Producto del catalogo actualizado.');
    }

    public function inventory(Request $request)
    {
        $this->ensureInventory();

        $query = trim((string) $request->query('q'));
        $warehouseFilter = trim((string) $request->query('warehouse'));

        return view('inventory.stock.index', [
            'items' => WarehouseInventoryItem::with('catalogItem')
                ->when($warehouseFilter !== '', fn ($builder) => $builder->where('warehouse', $warehouseFilter))
                ->when($query, fn ($builder) => $builder->whereHas('catalogItem', fn ($item) => $item->where('name', 'like', "%{$query}%")->orWhere('sku', 'like', "%{$query}%")))
                ->orderBy('warehouse')
                ->get()
                ->sortBy(fn (WarehouseInventoryItem $item) => $item->catalogItem?->name)
                ->values(),
            'catalogItems' => WarehouseCatalogItem::where('authorized', true)->orderBy('name')->get(),
            'query' => $query,
            'warehouseFilter' => $warehouseFilter,
        ]);
    }

    public function warehouses(Request $request)
    {
        $this->ensureInventory();

        $query = trim((string) $request->query('q'));
        $warehouses = $this->warehouseRows();

        if ($query !== '') {
            $needle = mb_strtolower($query);
            $warehouses = $warehouses
                ->filter(fn (array $warehouse) => str_contains(mb_strtolower(implode(' ', $warehouse)), $needle))
                ->values();
        }

        return view('inventory.warehouses.index', [
            'warehouses' => $warehouses,
            'query' => $query,
        ]);
    }

    public function warehouseMovements(Request $request, string $warehouseKey)
    {
        $this->ensureInventory();

        $warehouse = $this->warehouseRows()->firstWhere('key', $warehouseKey);
        abort_unless($warehouse, 404);

        $movements = $warehouse['is_central']
            ? $this->centralWarehouseMovements($warehouse)
            : $this->companyWarehouseMovements($warehouse);

        $filters = [
            'type' => trim((string) $request->query('type')),
            'date_from' => trim((string) $request->query('date_from')),
            'date_to' => trim((string) $request->query('date_to')),
            'order' => trim((string) $request->query('order')),
            'q' => trim((string) $request->query('q')),
        ];

        $types = $movements->pluck('type')->unique()->sort()->values();

        $movements = $movements
            ->filter(function (array $movement) use ($filters) {
                if ($filters['type'] !== '' && $movement['type'] !== $filters['type']) {
                    return false;
                }

                if ($filters['date_from'] !== '' && ($movement['date_value'] === '' || $movement['date_value'] < $filters['date_from'])) {
                    return false;
                }

                if ($filters['date_to'] !== '' && ($movement['date_value'] === '' || $movement['date_value'] > $filters['date_to'])) {
                    return false;
                }

                if ($filters['order'] !== '' && ! str_contains(mb_strtolower($movement['order']), mb_strtolower($filters['order']))) {
                    return false;
                }

                if ($filters['q'] !== '' && ! str_contains(mb_strtolower($movement['search']), mb_strtolower($filters['q']))) {
                    return false;
                }

                return true;
            })
            ->sortByDesc(fn (array $movement) => $movement['date_value'].' '.$movement['order'])
            ->values();

        return view('inventory.warehouses.movements', [
            'warehouse' => $warehouse,
            'movements' => $movements,
            'types' => $types,
            'filters' => $filters,
        ]);
    }

    public function editWarehouse(string $warehouseKey)
    {
        $this->ensureInventory();

        $warehouse = $this->warehouseRows()->firstWhere('key', $warehouseKey);
        abort_unless($warehouse, 404);

        $selectedCompanyIds = [];
        if ($warehouse['is_central'] && Schema::hasTable('supply_warehouses') && Schema::hasTable('supply_warehouse_companies')) {
            $supplyWarehouseId = DB::table('supply_warehouses')->where('key', $warehouseKey)->value('id');
            $selectedCompanyIds = $supplyWarehouseId
                ? DB::table('supply_warehouse_companies')->where('supply_warehouse_id', $supplyWarehouseId)->pluck('company_id')->map(fn ($id) => (int) $id)->all()
                : [];
        }

        return view('inventory.warehouses.edit', [
            'warehouse' => $warehouse,
            'companies' => Company::orderBy('name')->get(),
            'selectedCompanyIds' => $selectedCompanyIds,
        ]);
    }

    public function createSupplyWarehouse()
    {
        $this->ensureInventory();

        return view('inventory.warehouses.create-supply', [
            'companies' => Company::orderBy('name')->get(),
        ]);
    }

    public function storeSupplyWarehouse(Request $request)
    {
        $this->ensureInventory();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:80'],
            'address' => ['nullable', 'string', 'max:500'],
            'companies' => ['array'],
            'companies.*' => ['integer', 'exists:companies,id'],
        ]);

        DB::transaction(function () use ($validated) {
            $supplyWarehouseId = DB::table('supply_warehouses')->insertGetId([
                'key' => Str::slug($validated['name']).'-'.Str::random(8),
                'name' => $validated['name'],
                'short_name' => $validated['short_name'] ?? null,
                'address' => $validated['address'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            collect($validated['companies'] ?? [])
                ->unique()
                ->values()
                ->each(function ($companyId) use ($supplyWarehouseId) {
                    DB::table('supply_warehouse_companies')->insert([
                        'supply_warehouse_id' => $supplyWarehouseId,
                        'company_id' => $companyId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                });
        });

        return redirect()->route('inventory.warehouses.index')->with('status', 'Almacen de suministros creado.');
    }

    public function updateWarehouse(Request $request, string $warehouseKey)
    {
        $this->ensureInventory();

        $warehouse = $this->warehouseRows()->firstWhere('key', $warehouseKey);
        abort_unless($warehouse, 404);

        if ($warehouse['is_central']) {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'short_name' => ['nullable', 'string', 'max:80'],
                'address' => ['nullable', 'string', 'max:500'],
                'companies' => ['array'],
                'companies.*' => ['integer', 'exists:companies,id'],
            ]);

            DB::transaction(function () use ($validated, $warehouse) {
                $supplyWarehouseId = DB::table('supply_warehouses')->where('key', $warehouse['key'])->value('id');
                $payload = [
                    'name' => $validated['name'],
                    'short_name' => $validated['short_name'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'updated_at' => now(),
                ];

                if ($supplyWarehouseId) {
                    DB::table('supply_warehouses')->where('id', $supplyWarehouseId)->update($payload);
                } else {
                    $payload['key'] = $warehouse['key'];
                    $payload['created_at'] = now();
                    $supplyWarehouseId = DB::table('supply_warehouses')->insertGetId($payload);
                }

                DB::table('supply_warehouse_companies')->where('supply_warehouse_id', $supplyWarehouseId)->delete();

                collect($validated['companies'] ?? [])
                    ->unique()
                    ->values()
                    ->each(function ($companyId) use ($supplyWarehouseId) {
                        DB::table('supply_warehouse_companies')->insert([
                            'supply_warehouse_id' => $supplyWarehouseId,
                            'company_id' => $companyId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    });
            });

            return redirect()->route('inventory.warehouses.close')->with('status', 'Almacen de suministros actualizado.');
        }

        $validated = $request->validate([
            'warehouse' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:80'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $company = Company::findOrFail($warehouse['company_id']);
        $updated = false;
        $warehouses = collect($company->warehouseObjects())
            ->map(function (array $item) use ($warehouse, $validated, &$updated) {
                if (! $updated && $item['name'] === $warehouse['real_warehouse']) {
                    $updated = true;

                    return [
                        'name' => $validated['warehouse'],
                        'short_name' => $validated['short_name'] ?? '',
                    ];
                }

                return $item;
            })
            ->values()
            ->all();

        abort_unless($updated, 404);

        $company->update([
            'warehouses' => $warehouses,
            'address' => $validated['address'] ?? null,
        ]);

        return redirect()->route('inventory.warehouses.close')->with('status', 'Almacen actualizado.');
    }

    public function closeWarehouseWindow()
    {
        $this->ensureInventory();

        return view('inventory.warehouses.close');
    }

    public function storeInventory(Request $request)
    {
        $this->ensureInventory();

        $validated = $request->validate([
            'warehouse_catalog_item_id' => ['required', 'integer', 'exists:warehouse_catalog_items,id'],
            'warehouse' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0'],
            'minimum_quantity' => ['nullable', 'numeric', 'min:0'],
        ]);

        WarehouseInventoryItem::updateOrCreate(
            [
                'warehouse_catalog_item_id' => $validated['warehouse_catalog_item_id'],
                'warehouse' => $validated['warehouse'],
            ],
            [
                'quantity' => $validated['quantity'],
                'minimum_quantity' => $validated['minimum_quantity'] ?? 0,
            ],
        );

        return redirect()->route('inventory.stock.index')->with('status', 'Inventario actualizado.');
    }

    public function active(Request $request)
    {
        $this->ensureInventory();

        $query = trim((string) $request->query('q'));

        return view('inventory.supply-orders.active', [
            'orders' => SupplyOrder::with(['requester', 'company', 'items.catalogItem'])
                ->whereIn('status', ['approved', 'remitted'])
                ->when($query, function ($builder) use ($query) {
                    $builder->where(function ($inner) use ($query) {
                        $inner->where('folio', 'like', "%{$query}%")
                            ->orWhereHas('requester', fn ($user) => $user->where('name', 'like', "%{$query}%"))
                            ->orWhereHas('items', fn ($items) => $items->where('article', 'like', "%{$query}%"));
                    });
                })
                ->orderBy('delivery_date')
                ->get(),
            'query' => $query,
        ]);
    }

    public function deliver(Request $request, SupplyOrder $supplyOrder)
    {
        $this->ensureInventory();
        abort_unless($supplyOrder->status === 'approved', 403);

        $validated = $request->validate([
            'delivered_on' => ['required', 'date'],
            'receiving_pin' => ['nullable', 'digits:4'],
        ]);

        DB::transaction(function () use ($supplyOrder, $validated) {
            $supplyOrder->load('items.catalogItem');

            foreach ($supplyOrder->items as $item) {
                $inventory = WarehouseInventoryItem::where('warehouse', WarehouseInventoryItem::CENTRAL_WAREHOUSE)
                    ->where('warehouse_catalog_item_id', $item->warehouse_catalog_item_id)
                    ->first();

                if (! $inventory || (float) $inventory->quantity < (float) $item->quantity) {
                    throw ValidationException::withMessages([
                        'order' => "Inventario insuficiente para {$item->article}.",
                    ]);
                }

                $inventory->update([
                    'quantity' => (float) $inventory->quantity - (float) $item->quantity,
                ]);
            }

            $supplyOrder->update([
                'status' => 'remitted',
                'delivery_remission_number' => $this->nextRemissionNumber(),
                'remission_token' => $supplyOrder->remission_token ?: Str::random(48),
                'delivered_on' => $validated['delivered_on'],
                'delivered_by' => Auth::id(),
                'receiving_pin' => ($validated['receiving_pin'] ?? null) ?: env('WAREHOUSE_RECEIVING_PIN', '1234'),
            ]);

            $this->audit($supplyOrder, 'remitted', 'Remision de entrega generada y descontada del inventario central.');
        });

        return redirect()->route('inventory.supply-orders.active')->with('status', "{$supplyOrder->folio} tiene remision generada y queda pendiente de recepcion.");
    }

    public function remission(SupplyOrder $supplyOrder)
    {
        $this->ensureInventory();
        abort_unless(in_array($supplyOrder->status, ['remitted', 'delivered'], true), 404);

        return view('inventory.supply-orders.remission', [
            'order' => $supplyOrder->load(['requester', 'company', 'items.catalogItem', 'deliveredBy']),
        ]);
    }

    private function nextRemissionNumber(): string
    {
        $latest = SupplyOrder::query()
            ->whereNotNull('delivery_remission_number')
            ->pluck('delivery_remission_number')
            ->map(function (string $number) {
                return preg_match('/^REM-(\d+)$/', $number, $matches) ? (int) $matches[1] : 0;
            })
            ->max();

        return 'REM-'.str_pad((string) (($latest ?: 0) + 1), 6, '0', STR_PAD_LEFT);
    }

    private function nextCatalogSku(): string
    {
        $latest = WarehouseCatalogItem::query()
            ->whereNotNull('sku')
            ->pluck('sku')
            ->map(function (string $sku) {
                return preg_match('/^SKU-(\d+)$/', $sku, $matches) ? (int) $matches[1] : 0;
            })
            ->max();

        return 'SKU-'.str_pad((string) (($latest ?: 0) + 1), 6, '0', STR_PAD_LEFT);
    }

    private function warehouseRows()
    {
        return $this->supplyWarehouseRows()->merge(
            Company::orderBy('name')->get()->flatMap(function (Company $company) {
                return collect($company->warehouseObjects())->map(fn (array $warehouse) => [
                    'key' => $this->warehouseKey($company->id, $warehouse['name']),
                    'is_central' => false,
                    'type' => 'Empresa',
                    'company_id' => $company->id,
                    'company' => $company->name,
                    'rfc' => $company->rfc ?: '—',
                    'warehouse' => $warehouse['name'],
                    'real_warehouse' => $warehouse['name'],
                    'short_name' => $warehouse['short_name'] ?: '—',
                    'address' => $company->address ?: 'Sin direccion capturada',
                ]);
            })
        )->values();
    }

    private function supplyWarehouseRows()
    {
        if (! Schema::hasTable('supply_warehouses')) {
            return collect([$this->fallbackCentralWarehouseRow()]);
        }

        return collect(DB::table('supply_warehouses')->orderByRaw('CASE WHEN `key` = ? THEN 0 ELSE 1 END', ['central'])->orderBy('name')->get())
            ->map(fn ($warehouse) => $this->supplyWarehouseRow($warehouse));
    }

    private function supplyWarehouseRow($warehouse): array
    {
        $companies = collect();
        if (Schema::hasTable('supply_warehouse_companies')) {
            $companies = Company::query()
                ->whereIn('id', DB::table('supply_warehouse_companies')->where('supply_warehouse_id', $warehouse->id)->pluck('company_id'))
                ->orderBy('name')
                ->get();
        }

        return [
            'key' => $warehouse->key,
            'is_central' => true,
            'type' => 'Almacen de suministros',
            'company_id' => null,
            'company' => $companies->pluck('name')->implode(', ') ?: 'Sin empresas asignadas',
            'rfc' => $companies->pluck('rfc')->filter()->implode(', ') ?: '—',
            'warehouse' => $warehouse->name,
            'real_warehouse' => $warehouse->address ?: $warehouse->name,
            'short_name' => $warehouse->short_name ?: '—',
            'address' => $warehouse->address ?: 'Sin direccion capturada',
        ];
    }

    private function fallbackCentralWarehouseRow(): array
    {
        return [
            'key' => 'central',
            'is_central' => true,
            'type' => 'Almacen de suministros',
            'company_id' => null,
            'company' => 'Prodifem',
            'rfc' => '—',
            'warehouse' => 'Almacen central',
            'real_warehouse' => WarehouseInventoryItem::CENTRAL_WAREHOUSE,
            'short_name' => 'Central',
            'address' => WarehouseInventoryItem::CENTRAL_WAREHOUSE,
        ];
    }

    private function warehouseKey(int $companyId, string $warehouse): string
    {
        $payload = json_encode(['company_id' => $companyId, 'warehouse' => $warehouse]);

        return rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
    }

    private function inventoryWarehouseName(array $warehouse): string
    {
        return $warehouse['real_warehouse'] ?: $warehouse['warehouse'];
    }

    private function catalogOptionLists(): array
    {
        return [
            'categoryOptions' => WarehouseCatalogItem::query()
                ->whereNotNull('category')
                ->pluck('category')
                ->filter()
                ->unique()
                ->sort()
                ->values(),
            'subcategoryOptions' => WarehouseCatalogItem::query()
                ->whereNotNull('subcategory')
                ->pluck('subcategory')
                ->filter()
                ->unique()
                ->sort()
                ->values(),
        ];
    }

    private function filterCatalogItems($items, string $query)
    {
        if ($query === '') {
            return $items;
        }

        $needle = $this->normalizeCatalogSearchText($query);

        return $items
            ->filter(fn (WarehouseCatalogItem $item) => str_contains(
                $this->normalizeCatalogSearchText(implode(' ', [
                    $item->sku,
                    $item->category,
                    $item->subcategory,
                    $item->name,
                    $item->description,
                    $item->unit,
                ])),
                $needle
            ))
            ->values();
    }

    private function filterCatalogRows($rows, string $query)
    {
        if ($query === '') {
            return $rows;
        }

        $needle = $this->normalizeCatalogSearchText($query);

        return $rows
            ->filter(fn (array $row) => str_contains($this->normalizeCatalogSearchText(implode(' ', $row)), $needle))
            ->values();
    }

    private function catalogSuggestionsFromItems($items)
    {
        return $items
            ->pluck('name')
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }

    private function catalogSuggestionsFromRows($rows)
    {
        return $rows
            ->pluck('name')
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }

    private function normalizeCatalogSearchText(string $value): string
    {
        return strtr(mb_strtolower($value), [
            'á' => 'a',
            'à' => 'a',
            'ä' => 'a',
            'â' => 'a',
            'é' => 'e',
            'è' => 'e',
            'ë' => 'e',
            'ê' => 'e',
            'í' => 'i',
            'ì' => 'i',
            'ï' => 'i',
            'î' => 'i',
            'ó' => 'o',
            'ò' => 'o',
            'ö' => 'o',
            'ô' => 'o',
            'ú' => 'u',
            'ù' => 'u',
            'ü' => 'u',
            'û' => 'u',
            'ñ' => 'n',
        ]);
    }

    private function companyWarehouseCatalogRows(array $warehouse)
    {
        return PurchaseOrderReceiptItem::with([
            'receipt.purchaseOrder.company',
            'receipt.purchaseOrder.provider',
            'purchaseOrderItem',
        ])
            ->whereHas('receipt.purchaseOrder', function ($builder) use ($warehouse) {
                $builder->where('company_id', $warehouse['company_id'])
                    ->where('warehouse', $warehouse['real_warehouse']);
            })
            ->get()
            ->groupBy(function (PurchaseOrderReceiptItem $receiptItem) {
                $item = $receiptItem->purchaseOrderItem;

                return mb_strtolower(trim($item?->article ?: 'Producto no capturado'));
            })
            ->map(function ($receiptItems) {
                $first = $receiptItems->first();
                $item = $first?->purchaseOrderItem;
                $quantity = $receiptItems->sum(fn (PurchaseOrderReceiptItem $receiptItem) => (float) $receiptItem->received_quantity);
                $latestReceipt = $receiptItems
                    ->pluck('receipt')
                    ->filter()
                    ->sortByDesc(fn ($receipt) => $receipt->received_on?->toDateString() ?? '')
                    ->first();

                return [
                    'sku' => '—',
                    'category' => 'Ordenes de compra',
                    'subcategory' => 'Actas de recepcion',
                    'name' => $item?->article ?: 'Producto no capturado',
                    'description' => 'Proveedor: '.($latestReceipt?->purchaseOrder?->provider?->business_name ?: 'No capturado'),
                    'unit' => 'unidad',
                    'unit_cost' => (float) ($item?->unit_price ?? 0),
                    'quantity' => $quantity,
                    'minimum_quantity' => null,
                    'status' => 'Recibido',
                    'updated_at' => $latestReceipt?->received_on?->format('d/m/Y') ?: 'Sin fecha',
                ];
            })
            ->sortBy('name')
            ->values();
    }

    private function companyWarehouseMovements(array $warehouse)
    {
        return PurchaseOrderReceiptItem::with([
            'receipt.receiver',
            'receipt.purchaseOrder.company',
            'receipt.purchaseOrder.provider',
            'purchaseOrderItem',
        ])
            ->whereHas('receipt.purchaseOrder', function ($builder) use ($warehouse) {
                $builder->where('company_id', $warehouse['company_id'])
                    ->where('warehouse', $warehouse['real_warehouse']);
            })
            ->get()
            ->map(function (PurchaseOrderReceiptItem $receiptItem) use ($warehouse) {
                $receipt = $receiptItem->receipt;
                $order = $receipt?->purchaseOrder;
                $item = $receiptItem->purchaseOrderItem;
                $quantity = (float) $receiptItem->received_quantity;
                $unitPrice = (float) ($item?->unit_price ?? 0);
                $amount = $quantity * $unitPrice;
                $dateValue = $receipt?->received_on?->toDateString() ?? '';

                return $this->movementRow([
                    'type' => 'Entrada / Acta de recepcion',
                    'date' => $receipt?->received_on?->format('d/m/Y') ?: 'Sin fecha',
                    'date_value' => $dateValue,
                    'order' => $order?->folio ?: '—',
                    'document' => $receipt?->invoice_number ?: $receipt?->original_name ?: 'Acta de recepcion',
                    'company' => $order?->company?->name ?: $warehouse['company'],
                    'warehouse' => $warehouse['warehouse'],
                    'related' => $order?->provider?->business_name ?: 'Proveedor no capturado',
                    'product' => $item?->article ?: 'Producto no capturado',
                    'quantity' => number_format($quantity, 2),
                    'unit' => 'unidad',
                    'unit_price' => '$'.number_format($unitPrice, 2),
                    'amount' => '$'.number_format($amount, 2),
                    'stock' => '—',
                    'status' => 'Recibida',
                    'details' => trim('Acta: '.($receipt?->invoice_number ?: '—').' · Documento: '.($receipt?->original_name ?: '—').' · Recibio: '.($receipt?->receiver?->name ?: 'Inventarios')),
                ]);
            })
            ->values();
    }

    private function centralWarehouseMovements(?array $warehouse = null)
    {
        $centralWarehouse = $warehouse
            ?? $this->warehouseRows()->firstWhere('key', 'central')
            ?? $this->fallbackCentralWarehouseRow();
        $inventoryWarehouse = $this->inventoryWarehouseName($centralWarehouse);

        $entries = WarehouseInventoryItem::with('catalogItem')
            ->where('warehouse', $inventoryWarehouse)
            ->get()
            ->flatMap(function (WarehouseInventoryItem $inventory) use ($centralWarehouse) {
                $item = $inventory->catalogItem;
                $quantity = (float) $inventory->quantity;
                $unitCost = (float) ($item?->unit_cost ?? 0);
                $dateValue = $inventory->created_at?->toDateString() ?? $inventory->updated_at?->toDateString() ?? '';

                return [
                    $this->movementRow([
                        'type' => 'Entrada',
                        'date' => $inventory->created_at?->format('d/m/Y') ?: 'Sin fecha',
                        'date_value' => $dateValue,
                        'order' => 'Inventario',
                        'document' => 'Alta / actualizacion',
                        'company' => $centralWarehouse['company'],
                        'warehouse' => $centralWarehouse['warehouse'],
                        'related' => 'Inventarios',
                        'product' => $item?->name ?: 'Insumo no capturado',
                        'quantity' => number_format($quantity, 2),
                        'unit' => $item?->unit ?: 'unidad',
                        'unit_price' => '$'.number_format($unitCost, 2),
                        'amount' => '$'.number_format($quantity * $unitCost, 2),
                        'stock' => number_format($quantity, 2),
                        'status' => 'Registrada',
                        'details' => 'Entrada registrada en el catalogo autorizado del almacen central.',
                    ]),
                    $this->movementRow([
                        'type' => 'Existencia',
                        'date' => $inventory->updated_at?->format('d/m/Y') ?: 'Sin fecha',
                        'date_value' => $inventory->updated_at?->toDateString() ?? $dateValue,
                        'order' => 'Existencia actual',
                        'document' => $item?->sku ?: 'Sin SKU',
                        'company' => $centralWarehouse['company'],
                        'warehouse' => $centralWarehouse['warehouse'],
                        'related' => 'Stock actual',
                        'product' => $item?->name ?: 'Insumo no capturado',
                        'quantity' => number_format($quantity, 2),
                        'unit' => $item?->unit ?: 'unidad',
                        'unit_price' => '$'.number_format($unitCost, 2),
                        'amount' => '$'.number_format($quantity * $unitCost, 2),
                        'stock' => number_format($quantity, 2),
                        'status' => $quantity > 0 ? 'Disponible' : 'Sin existencia',
                        'details' => 'Existencia actual del almacen central.',
                    ]),
                ];
            });

        $exits = SupplyOrderItem::with(['supplyOrder.requester', 'supplyOrder.company', 'catalogItem'])
            ->whereHas('supplyOrder', function ($builder) use ($inventoryWarehouse) {
                $builder->where('warehouse_from', $inventoryWarehouse)
                    ->whereIn('status', ['remitted', 'delivered']);
            })
            ->get()
            ->map(function (SupplyOrderItem $item) use ($centralWarehouse) {
                $order = $item->supplyOrder;
                $quantity = (float) $item->quantity;
                $unitCost = (float) $item->unit_cost;
                $dateValue = $order?->delivered_on?->toDateString() ?? '';

                return $this->movementRow([
                    'type' => 'Salida',
                    'date' => $order?->delivered_on?->format('d/m/Y') ?: 'Sin fecha',
                    'date_value' => $dateValue,
                    'order' => $order?->folio ?: '—',
                    'document' => $order?->delivery_remission_number ?: 'Remision pendiente',
                    'company' => $order?->company?->name ?: 'Empresa no capturada',
                    'warehouse' => $centralWarehouse['warehouse'],
                    'related' => 'Destino: '.($order?->warehouse_to ?: 'Sin destino').' · Solicitante: '.($order?->requester?->name ?: 'Usuario'),
                    'product' => $item->article,
                    'quantity' => number_format($quantity, 2),
                    'unit' => $item->catalogItem?->unit ?: 'unidad',
                    'unit_price' => '$'.number_format($unitCost, 2),
                    'amount' => '$'.number_format((float) $item->line_total, 2),
                    'stock' => '—',
                    'status' => $order?->status === 'delivered' ? 'Recibida' : 'Pendiente de recepcion',
                    'details' => 'Salida por orden de suministro hacia '.($order?->warehouse_to ?: 'almacen destino no capturado').'.',
                ]);
            });

        return $entries->merge($exits)->values();
    }

    private function movementRow(array $row): array
    {
        $row['search'] = mb_strtolower(implode(' ', $row));

        return $row;
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

    private function ensureInventory(): void
    {
        abort_unless(Auth::user()?->canAccessRole('inventory'), 403);
    }

    private function ensureCatalogView(): void
    {
        abort_unless(Auth::user()?->canAccessRole('inventory') || $this->canManageCentralCatalog(), 403);
    }

    private function ensureCatalogManage(): void
    {
        abort_unless($this->canManageCentralCatalog(), 403);
    }

    private function canManageCentralCatalog(): bool
    {
        $user = Auth::user();

        return (bool) ($user?->active && in_array($user->role, ['superadmin', 'finance'], true));
    }
}
