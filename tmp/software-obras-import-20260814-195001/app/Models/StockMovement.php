<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'movement_date' => 'date',
            'quantity_in' => 'decimal:4',
            'quantity_out' => 'decimal:4',
            'balance' => 'decimal:4',
            'unit_cost' => 'decimal:2',
            'movement_value' => 'decimal:2',
            'accumulated_value' => 'decimal:2',
        ];
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(MaterialCatalog::class, 'material_catalog_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
