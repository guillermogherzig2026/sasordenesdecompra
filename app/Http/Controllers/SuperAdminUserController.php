<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class SuperAdminUserController extends Controller
{
    private const ROLES = ['superadmin', 'finance', 'buyer', 'inventory', 'services', 'administrative_assistant'];
    private const BUYER_SUBROLES = ['purchases', 'supplies', 'reimbursements'];

    public function index(Request $request)
    {
        $this->ensureSuperAdmin();

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'role' => ['nullable', Rule::in(self::ROLES)],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        $query = User::query();

        if (filled($filters['q'] ?? null)) {
            $search = trim($filters['q']);
            $query->where(function ($inner) use ($search) {
                $inner
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (filled($filters['role'] ?? null)) {
            $query->where('role', $filters['role']);
        }

        if (($filters['status'] ?? null) === 'active') {
            $query->where('active', true);
        }

        if (($filters['status'] ?? null) === 'inactive') {
            $query->where('active', false);
        }

        return view('superadmin.users', [
            'users' => $query->orderBy('role')->orderBy('name')->paginate(12)->withQueryString(),
            'companies' => Company::orderBy('name')->get(),
            'supplyWarehouses' => $this->supplyWarehouseAuthorizationRows(),
            'roles' => self::ROLES,
            'filters' => $filters,
            'totalUsers' => User::count(),
            'activeUsers' => User::where('active', true)->count(),
            'inactiveUsers' => User::where('active', false)->count(),
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureSuperAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in(self::ROLES)],
            'buyer_subroles' => ['required_if:role,buyer', 'array', 'min:1'],
            'buyer_subroles.*' => ['required', Rule::in(self::BUYER_SUBROLES)],
            'companies' => ['array'],
            'companies.*' => ['string'],
            'supply_warehouses' => ['array'],
            'supply_warehouses.*' => ['string', 'max:255'],
            'warehouses' => ['array'],
            'warehouses.*' => ['array'],
            'warehouses.*.*' => ['string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => $validated['password'],
            'plain_password' => $validated['password'],
            'role' => $validated['role'],
            'buyer_subrole' => $validated['role'] === 'buyer' ? $this->buyerSubrolesPayload($validated['buyer_subroles'] ?? []) : null,
            'companies' => $this->companiesForRole($validated['role'], $validated['companies'] ?? [], $validated['warehouses'] ?? [], $validated['supply_warehouses'] ?? []),
            'active' => true,
        ]);

        $this->audit($user, 'superadmin_user_created', "Usuario {$user->email} creado por Super Administrador.");

        return redirect()->route('superadmin.users.index')->with('status', 'Usuario creado correctamente.');
    }

    public function update(Request $request, User $user)
    {
        $this->ensureSuperAdmin();

        if ($request->boolean('password_only')) {
            return $this->updatePassword($request, $user);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(self::ROLES)],
            'buyer_subroles' => ['required_if:role,buyer', 'array', 'min:1'],
            'buyer_subroles.*' => ['required', Rule::in(self::BUYER_SUBROLES)],
            'companies' => ['array'],
            'companies.*' => ['string'],
            'supply_warehouses' => ['array'],
            'supply_warehouses.*' => ['string', 'max:255'],
            'warehouses' => ['array'],
            'warehouses.*' => ['array'],
            'warehouses.*.*' => ['string', 'max:255'],
        ]);

        abort_if(
            $user->id === Auth::id() && $validated['role'] !== 'superadmin',
            403,
            'No puedes quitarte el rol de Super Administrador.'
        );

        $payload = [
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'role' => $validated['role'],
            'buyer_subrole' => $validated['role'] === 'buyer' ? $this->buyerSubrolesPayload($validated['buyer_subroles'] ?? []) : null,
            'companies' => $this->companiesForRole($validated['role'], $validated['companies'] ?? [], $validated['warehouses'] ?? [], $validated['supply_warehouses'] ?? []),
        ];

        $user->update($payload);
        $this->audit($user, 'superadmin_user_updated', "Usuario {$user->email} actualizado por Super Administrador.");

        return redirect()->route('superadmin.users.index')->with('status', 'Usuario actualizado.');
    }

    private function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required', 'string', 'min:6', 'max:255'],
            'password_user_id' => ['required', 'integer'],
        ]);

        abort_unless((int) $validated['password_user_id'] === $user->id, 404);

        $user->update([
            'password' => $validated['password'],
            'plain_password' => $validated['password'],
        ]);

        $this->audit(
            $user,
            'superadmin_user_password_updated',
            "Contrasena de {$user->email} actualizada por Super Administrador."
        );

        return redirect()
            ->route('superadmin.users.index')
            ->with('status', 'Contrasena actualizada.');
    }

    public function toggle(User $user)
    {
        $this->ensureSuperAdmin();
        abort_if($user->id === Auth::id(), 403, 'No puedes desactivar tu propia cuenta.');

        $user->update(['active' => ! $user->active]);
        $this->audit($user, 'superadmin_user_status_updated', "Usuario {$user->email} ".($user->active ? 'activado.' : 'desactivado.'));

        return redirect()->route('superadmin.users.index')->with('status', 'Estado del usuario actualizado.');
    }

    private function companiesForRole(string $role, array $companyKeys, array $warehousesByCompany = [], array $supplyWarehouseKeys = []): array
    {
        if (! in_array($role, ['buyer', 'inventory'], true)) {
            return Company::orderBy('name')->get()
                ->map(fn (Company $company) => [
                    'name' => $company->name,
                    'warehouses' => $company->warehouseList(),
                ])
                ->values()
                ->all();
        }

        $companies = Company::orderBy('name')->get();
        $companiesById = $companies->keyBy(fn (Company $company) => (string) $company->id);
        $companiesByName = $companies->keyBy('name');

        $assignments = collect($companyKeys)
            ->map(function ($companyKey) use ($warehousesByCompany, $companiesById, $companiesByName) {
                $company = $companiesById->get((string) $companyKey) ?: $companiesByName->get((string) $companyKey);

                if (! $company) {
                    return null;
                }

                return [
                    'name' => $company->name,
                    'warehouses' => collect($warehousesByCompany[(string) $company->id] ?? $warehousesByCompany[$company->name] ?? [])
                        ->map(fn ($warehouse) => trim((string) $warehouse))
                        ->filter()
                        ->unique()
                        ->values()
                        ->all(),
                ];
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
        $selectedKeys = collect($selectedKeys)->map(fn ($key) => (string) $key)->filter()->unique()->all();

        if (! count($selectedKeys)) {
            return [];
        }

        return $this->supplyWarehouseAuthorizationRows()
            ->whereIn('key', $selectedKeys)
            ->flatMap(fn (array $warehouse) => collect($warehouse['companies'])->map(fn (array $company) => [
                'name' => $company['name'],
                'warehouses' => [$warehouse['label']],
            ]))
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

    private function buyerSubrolesPayload(array $buyerSubroles): string
    {
        $subroles = collect($buyerSubroles)
            ->map(fn ($subrole) => trim((string) $subrole))
            ->filter(fn ($subrole) => in_array($subrole, self::BUYER_SUBROLES, true))
            ->unique()
            ->values();

        return $subroles->isNotEmpty() ? $subroles->implode(',') : 'purchases';
    }

    private function audit(User $user, string $action, string $description): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
            'action' => $action,
            'description' => $description,
        ]);
    }

    private function ensureSuperAdmin(): void
    {
        abort_unless(Auth::user()?->canAccessRole('superadmin'), 403);
    }
}
