<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'plain_password',
        'role',
        'buyer_subrole',
        'companies',
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

    public function canAccessRole(string $role): bool
    {
        return $this->active && ($this->role === 'superadmin' || $this->role === $role);
    }

    public function canAccessBuyerSubrole(string $subrole): bool
    {
        if (! $this->active) {
            return false;
        }

        if ($this->role === 'superadmin') {
            return true;
        }

        return $this->role === 'buyer' && in_array($subrole, $this->buyerSubroles(), true);
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
