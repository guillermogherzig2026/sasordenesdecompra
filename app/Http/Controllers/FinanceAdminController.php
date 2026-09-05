<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Provider;
use App\Models\ProviderBusinessLine;
use App\Models\ProviderBusinessSubcategory;
use App\Models\User;
use App\Support\NavigationPermissionCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FinanceAdminController extends Controller
{
    private const BUYER_SUBROLES = ['purchases', 'supplies', 'reimbursements'];

    public function users()
    {
        $this->ensureFinance();

        return view('finance.admin.users', [
            'users' => User::whereIn('role', ['buyer', 'inventory', 'administrative_assistant'])->orderBy('name')->get(),
            'companies' => Company::orderBy('name')->get(),
            'supplyWarehouses' => $this->supplyWarehouseAuthorizationRows(),
            'navigationCatalog' => NavigationPermissionCatalog::categories(),
            'existingUsernames' => User::whereNotNull('username')->pluck('username')->values(),
        ]);
    }

    public function storeUser(Request $request)
    {
        $this->ensureFinance();

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255', 'required_without:first_name'],
            'first_name' => ['nullable', 'string', 'max:80', 'required_without:name'],
            'paternal_last_name' => ['nullable', 'string', 'max:80'],
            'maternal_last_name' => ['nullable', 'string', 'max:80'],
            'username' => ['nullable', 'string', 'max:80', 'regex:/^[a-z0-9]+$/', Rule::unique('users', 'username')],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:6'],
            'companies' => ['array'],
            'companies.*' => ['string'],
            'supply_warehouses' => ['array'],
            'supply_warehouses.*' => ['string', 'max:255'],
            'warehouses' => ['array'],
            'warehouses.*' => ['array'],
            'warehouses.*.*' => ['string', 'max:255'],
            'menu_permissions_configured' => ['nullable', 'boolean'],
            'menu_permissions' => ['nullable', 'array'],
            'menu_permissions.*' => ['string', Rule::in(NavigationPermissionCatalog::allKeys())],
        ]);

        $menuPermissions = $this->menuPermissionsPayload($request, $validated);
        $role = $this->roleForMenuPermissions($menuPermissions);
        $buyerSubroles = $this->buyerSubrolesForMenuPermissions($menuPermissions);
        $companies = $this->companyAssignments($validated['companies'] ?? [], $validated['warehouses'] ?? [], $validated['supply_warehouses'] ?? []);
        $personalName = $this->personalNamePayload($validated);
        $username = filled($validated['username'] ?? null)
            ? $validated['username']
            : $this->availableUsername($this->suggestedUsername($personalName));
        $password = $validated['password'] ?? $username.random_int(2024, 2026);

        $user = User::create([
            ...$personalName,
            'username' => $username,
            'email' => strtolower($validated['email']),
            'password' => $password,
            'plain_password' => $password,
            'role' => $role,
            'buyer_subrole' => $role === 'buyer' ? $this->buyerSubrolesPayload($buyerSubroles) : null,
            'companies' => $companies,
            'menu_permissions' => $menuPermissions,
            'active' => true,
        ]);

        $this->audit($user, 'user_created', "Usuario {$user->email} creado por Finanzas.");

        return redirect()->route('finance.admin.users')->with('status', 'Usuario creado correctamente.');
    }

    public function updateUser(Request $request, User $user)
    {
        $this->ensureFinance();
        abort_if(! in_array($user->role, ['buyer', 'inventory', 'administrative_assistant'], true), 403);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255', 'required_without:first_name'],
            'first_name' => ['nullable', 'string', 'max:80', 'required_without:name'],
            'paternal_last_name' => ['nullable', 'string', 'max:80'],
            'maternal_last_name' => ['nullable', 'string', 'max:80'],
            'username' => ['nullable', 'string', 'max:80', 'regex:/^[a-z0-9]+$/', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'companies' => ['array'],
            'companies.*' => ['string'],
            'supply_warehouses' => ['array'],
            'supply_warehouses.*' => ['string', 'max:255'],
            'warehouses' => ['array'],
            'warehouses.*' => ['array'],
            'warehouses.*.*' => ['string', 'max:255'],
            'menu_permissions_configured' => ['nullable', 'boolean'],
            'menu_permissions' => ['nullable', 'array'],
            'menu_permissions.*' => ['string', Rule::in(NavigationPermissionCatalog::allKeys())],
        ]);

        $menuPermissions = $this->menuPermissionsPayload($request, $validated, $user->role, $user->buyerSubroles());
        $role = $this->roleForMenuPermissions($menuPermissions);
        $buyerSubroles = $this->buyerSubrolesForMenuPermissions($menuPermissions);
        $personalName = $this->personalNamePayload($validated);

        $payload = [
            ...$personalName,
            'username' => $validated['username'] ?? $user->username,
            'email' => strtolower($validated['email']),
            'role' => $role,
            'buyer_subrole' => $role === 'buyer' ? $this->buyerSubrolesPayload($buyerSubroles) : null,
            'companies' => $this->companyAssignments($validated['companies'] ?? [], $validated['warehouses'] ?? [], $validated['supply_warehouses'] ?? []),
            'menu_permissions' => $menuPermissions,
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = $validated['password'];
            $payload['plain_password'] = $validated['password'];
        }

        $user->update($payload);
        $this->audit($user, 'user_updated', "Usuario {$user->email} actualizado por Finanzas.");

        return redirect()
            ->route('finance.admin.users', ['view' => 'users'])
            ->with('status', 'Usuario actualizado correctamente.');
    }

    public function toggleUser(User $user)
    {
        $this->ensureFinance();
        abort_if($user->role === 'finance', 403);

        $user->update(['active' => ! $user->active]);
        $this->audit($user, 'user_status_updated', "Usuario {$user->email} ".($user->active ? 'activado.' : 'desactivado.'));

        return back()->with('status', 'Estado del usuario actualizado.');
    }

    public function providers(Request $request)
    {
        $this->ensureFinance();

        $query = trim((string) $request->query('q'));
        $providers = Provider::with(['buyer', 'businessSubcategory'])
            ->when($query, fn ($builder) => $builder->where(function ($inner) use ($query) {
                $inner->where('business_name', 'like', "%{$query}%")
                    ->orWhere('rfc', 'like', "%{$query}%")
                    ->orWhere('business_line', 'like', "%{$query}%")
                    ->orWhere('provider_business_subcategory', 'like', "%{$query}%")
                    ->orWhere('contact_name', 'like', "%{$query}%")
                    ->orWhere('phone', 'like', "%{$query}%")
                    ->orWhere('bank', 'like', "%{$query}%")
                    ->orWhereHas('buyer', fn ($buyer) => $buyer->where('name', 'like', "%{$query}%"));
            }))
            ->orderBy('business_name')
            ->get();

        return view('finance.admin.providers', [
            'providers' => $providers,
            'businessLines' => $this->providerBusinessLines(),
            'buyers' => User::where('role', 'buyer')->where('active', true)->orderBy('name')->get(),
            'query' => $query,
        ]);
    }

    public function storeProvider(Request $request)
    {
        $this->ensureFinance();

        $validated = $request->validate([
            'buyer_id' => ['required', 'integer', Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'buyer')->where('active', true))],
            'business_name' => ['required', 'string', 'max:255'],
            'rfc' => ['required', 'string', 'max:20', Rule::unique('providers', 'rfc')->where(fn ($query) => $query->where('buyer_id', $request->integer('buyer_id')))],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'business_line_id' => ['required', 'integer', Rule::exists('provider_business_lines', 'id')->where('active', true)],
            'business_subcategory_id' => ['nullable', 'integer', Rule::exists('provider_business_subcategories', 'id')->where('active', true)],
            'bank' => ['required', 'string', 'max:120'],
            'account_number' => ['required', 'string', 'max:40'],
            'clabe' => ['required', 'string', 'size:18'],
            'reference' => ['nullable', 'string', 'max:255'],
        ]);

        $line = ProviderBusinessLine::findOrFail($validated['business_line_id']);
        $subcategory = $this->providerSubcategoryForLine($validated['business_subcategory_id'] ?? null, $line);

        $provider = Provider::create([
            'buyer_id' => $validated['buyer_id'],
            'business_name' => $validated['business_name'],
            'rfc' => $validated['rfc'],
            'contact_name' => $validated['contact_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'business_line' => $line->name,
            'provider_business_line_id' => $line->id,
            'provider_business_subcategory_id' => $subcategory?->id,
            'provider_business_subcategory' => $subcategory?->name,
            'bank' => $validated['bank'],
            'account_number' => $validated['account_number'],
            'clabe' => $validated['clabe'],
            'reference' => $validated['reference'] ?? null,
        ]);

        $this->audit($provider, 'provider_created', "Proveedor {$provider->business_name} creado por Finanzas.");

        return redirect()->route('finance.admin.providers')->with('status', 'Proveedor registrado.');
    }

    public function updateProvider(Request $request, Provider $provider)
    {
        $this->ensureFinance();

        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'rfc' => ['required', 'string', 'max:20'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:1000'],
            'business_line_id' => ['required', 'integer', Rule::exists('provider_business_lines', 'id')->where('active', true)],
            'business_subcategory_id' => ['nullable', 'integer', Rule::exists('provider_business_subcategories', 'id')->where('active', true)],
            'bank' => ['required', 'string', 'max:120'],
            'account_number' => ['required', 'string', 'max:40'],
            'clabe' => ['required', 'string', 'size:18'],
            'reference' => ['nullable', 'string', 'max:255'],
        ]);

        $line = ProviderBusinessLine::findOrFail($validated['business_line_id']);
        $subcategory = $this->providerSubcategoryForLine($validated['business_subcategory_id'] ?? null, $line);

        $provider->update([
            'business_name' => $validated['business_name'],
            'rfc' => $validated['rfc'],
            'contact_name' => $validated['contact_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'business_line' => $line->name,
            'provider_business_line_id' => $line->id,
            'provider_business_subcategory_id' => $subcategory?->id,
            'provider_business_subcategory' => $subcategory?->name,
            'bank' => $validated['bank'],
            'account_number' => $validated['account_number'],
            'clabe' => $validated['clabe'],
            'reference' => $validated['reference'] ?? null,
        ]);
        $this->audit($provider, 'provider_updated', "Proveedor {$provider->business_name} actualizado por Finanzas.");

        return back()->with('status', 'Proveedor actualizado.');
    }

    public function editProvider(Provider $provider)
    {
        $this->ensureFinance();

        return view('finance.admin.provider-edit', [
            'provider' => $provider->load(['buyer', 'businessSubcategory']),
            'businessLines' => $this->providerBusinessLines(),
        ]);
    }

    public function companies(Request $request)
    {
        $this->ensureFinance();

        $query = trim((string) $request->query('q'));
        $companies = Company::query()
            ->when($query, fn ($builder) => $builder->where(function ($inner) use ($query) {
                $inner->where('name', 'like', "%{$query}%")
                    ->orWhere('rfc', 'like', "%{$query}%")
                    ->orWhere('address', 'like', "%{$query}%");
            }))
            ->orderBy('name')
            ->get();

        return view('finance.admin.companies', [
            'companies' => $companies,
            'query' => $query,
            'buyers' => User::where('role', 'buyer')->where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function storeCompany(Request $request)
    {
        $this->ensureFinance();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:companies,name'],
            'rfc' => ['required', 'string', 'max:20', 'unique:companies,rfc'],
            'address' => ['required', 'string', 'max:1000'],
            'purchase_order_notes' => ['nullable', 'string', 'max:3000'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'buyer_ids' => ['array'],
            'buyer_ids.*' => ['integer'],
            'warehouses' => ['nullable', 'array'],
            'warehouses.*.name' => ['required_with:warehouses', 'string', 'max:255'],
            'warehouses.*.short_name' => ['nullable', 'string', 'max:50'],
            'warehouses.*.address' => ['nullable', 'string', 'max:1000'],
        ]);

        $companyPayload = [
            'name' => $validated['name'],
            'rfc' => strtoupper($validated['rfc']),
            'address' => $validated['address'],
            'purchase_order_notes' => $validated['purchase_order_notes'] ?? null,
            'logo_path' => $request->file('logo')?->store('company-logos'),
        ];

        if (Schema::hasColumn('companies', 'warehouses')) {
            $companyPayload['warehouses'] = $this->parseWarehouses(
                $validated['warehouses'] ?? [],
                $validated['address']
            );
        }

        $company = Company::create($companyPayload);

        $this->assignCompanyToBuyers($company, $validated['buyer_ids'] ?? []);
        $this->audit($company, 'company_created', "Empresa {$company->name} creada por Finanzas.");

        return redirect()->route('finance.admin.companies')->with('status', 'Empresa guardada.');
    }

    public function updateCompany(Request $request, Company $company)
    {
        $this->ensureFinance();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rfc' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:1000'],
            'purchase_order_notes' => ['nullable', 'string', 'max:3000'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'buyer_ids' => ['array'],
            'buyer_ids.*' => ['integer'],
            'warehouses' => ['nullable', 'array'],
            'warehouses.*.name' => ['required_with:warehouses', 'string', 'max:255'],
            'warehouses.*.short_name' => ['nullable', 'string', 'max:50'],
            'warehouses.*.address' => ['nullable', 'string', 'max:1000'],
        ]);

        $oldName = $company->name;
        $oldLogoPath = $company->logo_path;
        $newLogoPath = $request->hasFile('logo')
            ? $request->file('logo')->store('company-logos')
            : $company->logo_path;

        $companyPayload = [
            'name' => $validated['name'],
            'rfc' => strtoupper($validated['rfc']),
            'address' => $validated['address'],
            'purchase_order_notes' => $validated['purchase_order_notes'] ?? null,
            'logo_path' => $newLogoPath,
        ];

        if (Schema::hasColumn('companies', 'warehouses')) {
            $companyPayload['warehouses'] = $this->parseWarehouses(
                $validated['warehouses'] ?? [],
                $validated['address']
            );
        }

        $company->update($companyPayload);

        if ($request->hasFile('logo') && $oldLogoPath && $oldLogoPath !== $newLogoPath) {
            Storage::delete($oldLogoPath);
        }

        $this->syncCompanyBuyers($company, $validated['buyer_ids'] ?? [], $oldName);
        $this->audit($company, 'company_updated', "Empresa {$company->name} actualizada por Finanzas.");

        return back()->with('status', 'Empresa actualizada.');
    }

    public function editCompany(Company $company)
    {
        $this->ensureFinance();

        return view('finance.admin.company-edit', [
            'company' => $company,
            'buyers' => User::where('role', 'buyer')->where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function destroyCompany(Company $company)
    {
        $this->ensureFinance();

        if ($company->purchaseOrders()->exists()) {
            return back()->with('status', "No se puede eliminar {$company->name} porque ya tiene ordenes de compra relacionadas.");
        }

        $companyName = $company->name;

        User::whereIn('role', ['buyer', 'inventory'])->get()->each(function (User $user) use ($companyName) {
            $companies = collect($user->normalizedCompanyAssignments())
                ->reject(fn (array $assignment) => $assignment['name'] === $companyName)
                ->values()
                ->all();

            $user->update(['companies' => $companies]);
        });

        $this->audit($company, 'company_deleted', "Empresa {$companyName} eliminada por Finanzas.");
        $company->delete();

        return redirect()->route('finance.admin.companies')->with('status', "Empresa {$companyName} eliminada.");
    }

    private function assignCompanyToBuyers(Company $company, array $buyerIds): void
    {
        User::whereIn('id', $buyerIds)->where('role', 'buyer')->get()->each(function (User $buyer) use ($company) {
            $assignments = collect($buyer->normalizedCompanyAssignments())
                ->reject(fn (array $assignment) => $assignment['name'] === $company->name)
                ->push(['name' => $company->name, 'warehouses' => []])
                ->values()
                ->all();

            $buyer->update(['companies' => $assignments]);
        });
    }

    private function syncCompanyBuyers(Company $company, array $buyerIds, ?string $oldName = null): void
    {
        $selectedBuyerIds = collect($buyerIds)->map(fn ($id) => (int) $id)->all();

        User::where('role', 'buyer')->get()->each(function (User $buyer) use ($company, $oldName, $selectedBuyerIds) {
            $assignments = collect($buyer->normalizedCompanyAssignments())
                ->reject(fn (array $assignment) => in_array($assignment['name'], array_filter([$oldName, $company->name]), true))
                ->values();

            if (in_array((int) $buyer->id, $selectedBuyerIds, true)) {
                $assignments->push(['name' => $company->name, 'warehouses' => $buyer->authorizedWarehousesFor($oldName ?: $company->name)]);
            }

            $buyer->update(['companies' => $assignments->values()->all()]);
        });
    }

    private function companyAssignments(array $companyKeys, array $warehousesByCompany, array $supplyWarehouseKeys = []): array
    {
        $companies = Company::orderBy('name')->get();
        $companiesById = $companies->keyBy(fn (Company $company) => (string) $company->id);
        $companiesByName = $companies->keyBy('name');

        $assignments = collect($companyKeys)
            ->map(function ($companyKey) use ($warehousesByCompany, $companiesById, $companiesByName) {
                $company = $companiesById->get((string) $companyKey) ?: $companiesByName->get((string) $companyKey);

                if (! $company) {
                    return null;
                }

                $warehouses = collect($warehousesByCompany[(string) $company->id] ?? $warehousesByCompany[$company->name] ?? [])
                    ->map(fn ($warehouse) => trim((string) $warehouse))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                return ['name' => $company->name, 'warehouses' => $warehouses];
            })
            ->filter()
            ->unique('name');

        return $this->mergeCompanyAssignments(
            $assignments->values()->all(),
            $this->supplyWarehouseAssignments($supplyWarehouseKeys)
        );
    }

    private function mergeCompanyAssignments(array $baseAssignments, array $extraAssignments): array
    {
        return collect($baseAssignments)
            ->concat($extraAssignments)
            ->filter(fn (array $assignment) => filled($assignment['name'] ?? null))
            ->groupBy('name')
            ->map(fn ($assignments, $name) => [
                'name' => (string) $name,
                'warehouses' => $assignments
                    ->flatMap(fn (array $assignment) => $assignment['warehouses'] ?? [])
                    ->map(fn ($warehouse) => trim((string) $warehouse))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    private function supplyWarehouseAssignments(array $selectedKeys): array
    {
        $selectedKeys = collect($selectedKeys)->map(fn ($key) => (string) $key)->filter()->unique()->values();
        $selectedCompaniesByWarehouse = [];
        $selectedWholeWarehouses = [];

        $selectedKeys->each(function (string $selectedKey) use (&$selectedCompaniesByWarehouse, &$selectedWholeWarehouses) {
            [$warehouseKey, $companyId] = array_pad(explode('|', $selectedKey, 2), 2, null);
            $warehouseKey = trim((string) $warehouseKey);

            if ($warehouseKey === '') {
                return;
            }

            if (filled($companyId)) {
                $selectedCompaniesByWarehouse[$warehouseKey][] = (int) $companyId;
                return;
            }

            $selectedWholeWarehouses[] = $warehouseKey;
        });

        $warehouseKeys = collect(array_merge(array_keys($selectedCompaniesByWarehouse), $selectedWholeWarehouses))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (! count($warehouseKeys)) {
            return [];
        }

        return $this->supplyWarehouseAuthorizationRows()
            ->whereIn('key', $warehouseKeys)
            ->flatMap(function (array $warehouse) use ($selectedCompaniesByWarehouse, $selectedWholeWarehouses) {
                $selectedCompanyIds = collect($selectedCompaniesByWarehouse[$warehouse['key']] ?? [])
                    ->map(fn ($companyId) => (int) $companyId)
                    ->unique()
                    ->all();
                $allCompaniesSelected = in_array($warehouse['key'], $selectedWholeWarehouses, true);

                return collect($warehouse['companies'])
                    ->filter(fn (array $company) => $allCompaniesSelected || in_array((int) $company['id'], $selectedCompanyIds, true))
                    ->map(fn (array $company) => [
                        'name' => $company['name'],
                        'warehouses' => [$warehouse['label']],
                    ]);
            })
            ->values()
            ->all();
    }

    private function supplyWarehouseAuthorizationRows()
    {
        if (! Schema::hasTable('supply_warehouses')) {
            return collect();
        }

        return collect(DB::table('supply_warehouses')->orderByRaw('CASE WHEN `key` = ? THEN 0 ELSE 1 END', ['central'])->orderBy('name')->get())
            ->map(function ($warehouse) {
                $companies = Schema::hasTable('supply_warehouse_companies')
                    ? Company::query()
                        ->whereIn('id', DB::table('supply_warehouse_companies')->where('supply_warehouse_id', $warehouse->id)->pluck('company_id'))
                        ->orderBy('name')
                        ->get(['id', 'name'])
                    : collect();

                return [
                    'key' => $warehouse->key,
                    'label' => $this->centralWarehouseLabel((string) $warehouse->name, (string) ($warehouse->short_name ?? '')),
                    'warehouse' => $warehouse->name,
                    'short_name' => $warehouse->short_name,
                    'address' => $warehouse->address,
                    'companies' => $companies->map(fn (Company $company) => [
                        'id' => (int) $company->id,
                        'name' => $company->name,
                    ])->values()->all(),
                ];
            })
            ->values();
    }

    private function centralWarehouseLabel(string $name, string $shortName = ''): string
    {
        $name = trim($name);
        $shortName = trim($shortName);

        if (preg_match('/^almacen central$/i', $name) && $shortName !== '' && ! preg_match('/^central$/i', $shortName)) {
            return "Almacen Central {$shortName}";
        }

        if (preg_match('/^almacen central/i', $name)) {
            return $name;
        }

        return "Almacen Central {$name}";
    }

    private function parseWarehouses(array $warehouses, string $companyAddress): array
    {
        $parsed = collect($warehouses)
            ->map(fn ($warehouse) => [
                'name' => trim($warehouse['name'] ?? ''),
                'short_name' => trim($warehouse['short_name'] ?? ''),
                'address' => trim($warehouse['address'] ?? '') ?: trim($companyAddress),
            ])
            ->filter(fn ($warehouse) => filled($warehouse['name']))
            ->unique('name')
            ->values()
            ->all();

        return $parsed ?: [[
            'name' => 'Almacen principal',
            'short_name' => 'Principal',
            'address' => trim($companyAddress),
        ]];
    }

    private function buyerSubrolesPayload(array $buyerSubroles): string
    {
        $subroles = collect($buyerSubroles)
            ->map(fn ($subrole) => trim((string) $subrole))
            ->filter(fn ($subrole) => in_array($subrole, self::BUYER_SUBROLES, true))
            ->unique()
            ->values();

        return $subroles->isNotEmpty() ? $subroles->implode(',') : 'purchases';
    }

    private function roleForMenuPermissions(array $permissions): string
    {
        $permissions = NavigationPermissionCatalog::normalize($permissions);

        if (array_intersect($permissions, NavigationPermissionCatalog::categoryKeys('procurement')) !== []) {
            return 'buyer';
        }

        if (array_intersect($permissions, NavigationPermissionCatalog::categoryKeys('inventory')) !== []) {
            return 'inventory';
        }

        return 'administrative_assistant';
    }

    private function buyerSubrolesForMenuPermissions(array $permissions): array
    {
        $permissions = NavigationPermissionCatalog::normalize($permissions);
        $groups = NavigationPermissionCatalog::categories()['procurement']['groups'] ?? [];
        $subroleGroups = [
            'purchases' => ['purchase_orders', 'providers'],
            'supplies' => ['supply_orders'],
            'reimbursements' => ['reimbursement_orders'],
        ];

        return collect($subroleGroups)
            ->filter(function (array $groupKeys) use ($groups, $permissions) {
                $groupPermissions = collect($groupKeys)
                    ->flatMap(fn (string $groupKey) => $groups[$groupKey]['items'] ?? [])
                    ->all();

                return array_intersect($permissions, $groupPermissions) !== [];
            })
            ->keys()
            ->values()
            ->all();
    }

    private function menuPermissionsPayload(
        Request $request,
        array $validated,
        string $fallbackRole = 'administrative_assistant',
        array $fallbackBuyerSubroles = ['purchases']
    ): array
    {
        if ($request->boolean('menu_permissions_configured')) {
            return NavigationPermissionCatalog::normalize($validated['menu_permissions'] ?? []);
        }

        return NavigationPermissionCatalog::defaultsForRole(
            $fallbackRole,
            $fallbackBuyerSubroles
        );
    }

    private function personalNamePayload(array $validated): array
    {
        $firstName = trim((string) ($validated['first_name'] ?? $validated['name'] ?? ''));
        $paternalLastName = trim((string) ($validated['paternal_last_name'] ?? ''));
        $maternalLastName = trim((string) ($validated['maternal_last_name'] ?? ''));

        return [
            'name' => collect([$firstName, $paternalLastName, $maternalLastName])->filter()->implode(' '),
            'first_name' => $firstName,
            'paternal_last_name' => $paternalLastName ?: null,
            'maternal_last_name' => $maternalLastName ?: null,
        ];
    }

    private function suggestedUsername(array $personalName): string
    {
        $givenNames = preg_split('/\s+/', trim((string) $personalName['first_name']), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $initials = collect($givenNames)
            ->map(fn (string $name) => substr($this->normalizeUsername($name), 0, 1))
            ->implode('');
        $paternalLastName = $this->normalizeUsername((string) $personalName['paternal_last_name']);
        $maternalInitial = substr($this->normalizeUsername((string) $personalName['maternal_last_name']), 0, 1);

        return substr($initials.$paternalLastName.$maternalInitial, 0, 80) ?: 'usuario';
    }

    private function availableUsername(string $base): string
    {
        $candidate = $base;
        $suffix = 2;

        while (User::where('username', $candidate)->exists()) {
            $suffixText = (string) $suffix++;
            $candidate = substr($base, 0, 80 - strlen($suffixText)).$suffixText;
        }

        return $candidate;
    }

    private function normalizeUsername(string $value): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower(Str::ascii($value))) ?: '';
    }

    private function audit($model, string $action, string $description): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => $model::class,
            'auditable_id' => $model->id,
            'action' => $action,
            'description' => $description,
        ]);
    }

    private function providerBusinessLines()
    {
        return ProviderBusinessLine::with(['subcategories' => fn ($subcategories) => $subcategories
            ->where('active', true)
            ->orderBy('name')])
            ->where('active', true)
            ->orderBy('name')
            ->get();
    }

    private function providerSubcategoryForLine($subcategoryId, ProviderBusinessLine $line): ?ProviderBusinessSubcategory
    {
        if (empty($subcategoryId)) {
            return null;
        }

        return ProviderBusinessSubcategory::query()
            ->where('provider_business_line_id', $line->id)
            ->where('active', true)
            ->findOrFail($subcategoryId);
    }

    private function ensureFinance(): void
    {
        abort_unless(Auth::user()?->canAccessRole('finance'), 403);
    }
}
