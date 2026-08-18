<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierEvaluation extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'evaluated_at' => 'date',
            'price_score' => 'decimal:2',
            'delivery_score' => 'decimal:2',
            'quality_score' => 'decimal:2',
            'warranty_score' => 'decimal:2',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
