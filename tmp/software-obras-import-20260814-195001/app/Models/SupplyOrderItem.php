<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplyOrderItem extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'requested_quantity' => 'decimal:4',
            'authorized_quantity' => 'decimal:4',
            'sent_quantity' => 'decimal:4',
            'received_quantity' => 'decimal:4',
            'rejected_quantity' => 'decimal:4',
            'unit_price' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    public function supplyOrder(): BelongsTo
    {
        return $this->belongsTo(SupplyOrder::class);
    }
}
