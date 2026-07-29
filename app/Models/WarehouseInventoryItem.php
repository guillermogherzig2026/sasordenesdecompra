<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseInventoryItem extends Model
{
    public const CENTRAL_WAREHOUSE = 'San Francisco 516';

    protected $fillable = [
        'warehouse_catalog_item_id',
        'warehouse',
        'quantity',
        'minimum_quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'minimum_quantity' => 'decimal:2',
        ];
    }

    public function catalogItem()
    {
        return $this->belongsTo(WarehouseCatalogItem::class, 'warehouse_catalog_item_id');
    }
}
