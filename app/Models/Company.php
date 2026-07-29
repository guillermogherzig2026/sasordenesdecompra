<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'rfc',
        'address',
        'purchase_order_notes',
        'logo_path',
        'warehouses',
    ];

    protected function casts(): array
    {
        return [
            'warehouses' => 'array',
        ];
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function supplyOrders()
    {
        return $this->hasMany(SupplyOrder::class);
    }

    public function reimbursementOrders()
    {
        return $this->hasMany(ReimbursementOrder::class);
    }

    public function initials(): string
    {
        return collect(explode(' ', $this->name))
            ->filter()
            ->map(fn (string $word) => mb_substr($word, 0, 1))
            ->take(2)
            ->implode('');
    }

    public function warehouseList(): array
    {
        return collect($this->warehouses ?: [])
            ->map(fn ($warehouse) => trim(is_array($warehouse) ? ($warehouse['name'] ?? '') : (string) $warehouse))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function warehouseObjects(): array
    {
        return collect($this->warehouses ?: [])
            ->map(fn ($warehouse) => is_array($warehouse)
                ? ['name' => trim($warehouse['name'] ?? ''), 'short_name' => trim($warehouse['short_name'] ?? '')]
                : ['name' => trim((string) $warehouse), 'short_name' => '']
            )
            ->filter(fn ($warehouse) => filled($warehouse['name']))
            ->values()
            ->all();
    }

    public function warehouseShortNameFor(string $name): string
    {
        $match = collect($this->warehouseObjects())->first(fn (array $w) => $w['name'] === $name);

        return $match['short_name'] ?? '';
    }
}
