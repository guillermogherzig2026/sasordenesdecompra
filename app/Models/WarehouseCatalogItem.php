<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseCatalogItem extends Model
{
    protected $fillable = [
        'sku',
        'category',
        'subcategory',
        'name',
        'unit',
        'unit_cost',
        'description',
        'authorized',
    ];

    protected function casts(): array
    {
        return [
            'authorized' => 'boolean',
            'unit_cost' => 'decimal:2',
        ];
    }

    public function inventories()
    {
        return $this->hasMany(WarehouseInventoryItem::class);
    }

    public function centralInventory()
    {
        return $this->hasOne(WarehouseInventoryItem::class)->where('warehouse', WarehouseInventoryItem::CENTRAL_WAREHOUSE);
    }
}
