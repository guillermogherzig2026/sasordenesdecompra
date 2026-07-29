<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplyOrderItem extends Model
{
    protected $fillable = [
        'supply_order_id',
        'warehouse_catalog_item_id',
        'article',
        'quantity',
        'unit_cost',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    public function supplyOrder()
    {
        return $this->belongsTo(SupplyOrder::class);
    }

    public function catalogItem()
    {
        return $this->belongsTo(WarehouseCatalogItem::class, 'warehouse_catalog_item_id');
    }
}
