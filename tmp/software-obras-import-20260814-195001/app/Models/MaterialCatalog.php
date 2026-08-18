<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialCatalog extends Model
{
    use SoftDeletes;

    protected $table = 'material_catalog';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'minimum_stock' => 'decimal:4',
            'standard_cost' => 'decimal:2',
        ];
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
