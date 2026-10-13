<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OccasionalServicePayment extends Model
{
    protected $fillable = [
        'occasional_service_entry_id',
        'amount',
        'due_date',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
        ];
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(OccasionalServiceEntry::class, 'occasional_service_entry_id');
    }
}
