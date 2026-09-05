<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\NavigationPermissionCatalog;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    private ?array $navigationPermissionsCache = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'paternal_last_name',
        'maternal_last_name',
        'username',
        'email',
        'password',
        'plain_password',
        'role',
        'buyer_subrole',
        'companies',
        'menu_permissions',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'companies' => 'array',
            'menu_permissions' => 'array',
            'active' => 'boolean',
        ];
    }

    public function providers()
    {
        return $this->hasMany(Provider::class, 'buyer_id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'buyer_id');
    }

    public function supplyOrders()
    {
        return $this->hasMany(SupplyOrder::class, 'requester_id');
    }

    public function reimbursementOrders()
    {
        return $this->hasMany(ReimbursementOrder::class, 'requester_id');
    }

    public function constructionProjects(): BelongsToMany
    {
        return $this->belongsToMany(ConstructionProject::class, 'construction_project_user')
            ->withPivot(['can_view', 'can_edit'])
            ->withTimestamps();
    }

    public function canAccessRole(string $role): bool
    {
        if (! $this->active) {
            return false;
        }

        if ($this->role === 'superadmin' || $this->role === $role) {
            return true;
        }

        $category = match ($role) {
            'finance' => 'finance',
            'buyer' => 'procurement',
            'inventory' => 'inventory',
            'services' => 'services',
            default => null,
        };

        return $category ? $this->hasNavigationCategory($category) : false;
    }

    public function canAccessBuyerSubrole(string $subrole): bool
    {
        if (! $this->active) {
            return false;
        }

        if ($this->role === 'superadmin') {
            return true;
        }

        if ($this->role === 'buyer' && in_array($subrole, $this->buyerSubroles(), true)) {
            return true;
        }

        $permissions = match ($subrole) {
            'purchases' => [
                'procurement.orders.create',
                'procurement.orders.paid',
                'procurement.orders.pending_payment',
                'procurement.orders.mine',
                'procurement.orders.rejected',
                'procurement.providers',
                'construction.purchases',
                'construction.providers',
            ],
            'supplies' => ['procurement.supply.create', 'procurement.supply.pending', 'procurement.supply.history'],
            'reimbursements' => ['procurement.reimbursements.create', 'procurement.reimbursements.pending', 'procurement.reimbursements.history'],
            default => [],
        };

        return collect($permissions)->contains(fn (string $permission) => $this->canNavigateTo($permission));
    }

    public function buyerSubroleOptions(): array
    {
        return [
            'purchases' => 'Compras',
            'supplies' => 'Suministros',
            'reimbursements' => 'Reembolsos',
        ];
    }

    public function buyerSubroles(): array
    {
        if ($this->role !== 'buyer') {
            return [];
        }

        $subroles = collect(explode(',', (string) ($this->buyer_subrole ?: 'purchases')))
            ->map(fn ($subrole) => trim($subrole))
            ->filter(fn ($subrole) => array_key_exists($subrole, $this->buyerSubroleOptions()))
            ->unique()
            ->values()
            ->all();

        return $subroles ?: ['purchases'];
    }

    public function effectiveBuyerSubrole(): string
    {
        return $this->buyerSubroles()[0] ?? '';
    }

    public function buyerSubroleLabel(): string
    {
        return collect($this->buyerSubroles())
            ->map(fn ($subrole) => $this->buyerSubroleOptions()[$subrole] ?? $subrole)
            ->implode(', ') ?: 'Compras';
    }

    public function navigationPermissions(): array
    {
        if ($this->navigationPermissionsCache !== null) {
            return $this->navigationPermissionsCache;
        }

        if ($this->role === 'superadmin') {
            return $this->navigationPermissionsCache = NavigationPermissionCatalog::allKeys();
        }

        if ($this->menu_permissions === null) {
            $permissions = NavigationPermissionCatalog::defaultsForRole($this->role, $this->buyerSubroles());

            if ($this->constructionProjects()->wherePivot('can_view', true)->exists()) {
                $permissions[] = 'construction.dashboard';
            }

            return $this->navigationPermissionsCache = NavigationPermissionCatalog::normalize($permissions);
        }

        return $this->navigationPermissionsCache = NavigationPermissionCatalog::normalize($this->menu_permissions);
    }

    public function canNavigateTo(string $permission): bool
    {
        return $this->active && (
            $this->role === 'superadmin'
            || in_array($permission, $this->navigationPermissions(), true)
        );
    }

    public function hasNavigationCategory(string $category): bool
    {
        $categoryKeys = NavigationPermissionCatalog::categoryKeys($category);

        return collect($categoryKeys)->contains(fn (string $permission) => $this->canNavigateTo($permission));
    }

    public function authorizedNavigationCategoryLabels(): array
    {
        return NavigationPermissionCatalog::categoryLabelsFor($this->navigationPermissions());
    }

    public function normalizedCompanyAssignments(): array
    {
        return collect($this->companies ?: [])
            ->map(function ($assignment) {
                if (is_array($assignment)) {
                    $name = trim((string) ($assignment['name'] ?? ''));
                    $warehouses = collect($assignment['warehouses'] ?? [])
                        ->map(fn ($warehouse) => trim((string) $warehouse))
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();

                    return $name ? ['name' => $name, 'warehouses' => $warehouses] : null;
                }

                $name = trim((string) $assignment);

                return $name ? ['name' => $name, 'warehouses' => []] : null;
            })
            ->filter()
            ->unique('name')
            ->values()
            ->all();
    }

    public function authorizedCompanyNames(): array
    {
        return collect($this->normalizedCompanyAssignments())
            ->pluck('name')
            ->values()
            ->all();
    }

    public function authorizedWarehousesFor(string $companyName): array
    {
        $assignment = collect($this->normalizedCompanyAssignments())
            ->first(fn (array $item) => $item['name'] === $companyName);

        return $assignment['warehouses'] ?? [];
    }
}
