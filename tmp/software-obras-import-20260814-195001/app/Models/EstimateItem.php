<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstimateItem extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'previous_quantity' => 'decimal:4',
            'period_quantity' => 'decimal:4',
            'cumulative_quantity' => 'decimal:4',
            'programmed_percent' => 'decimal:2',
            'actual_percent' => 'decimal:2',
            'amount' => 'decimal:2',
            'real_amount' => 'decimal:2',
        ];
    }

    public function estimate(): BelongsTo
    {
        return $this->belongsTo(Estimate::class);
    }

    public function workItem(): BelongsTo
    {
        return $this->belongsTo(WorkItem::class);
    }
}
