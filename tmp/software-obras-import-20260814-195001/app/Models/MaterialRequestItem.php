<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialRequestItem extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'requested_quantity' => 'decimal:4',
            'available_stock' => 'decimal:4',
            'authorized_quantity' => 'decimal:4',
            'supplied_quantity' => 'decimal:4',
            'pending_quantity' => 'decimal:4',
        ];
    }

    public function materialRequest(): BelongsTo
    {
        return $this->belongsTo(MaterialRequest::class);
    }
}
