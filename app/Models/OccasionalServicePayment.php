<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OccasionalServicePayment extends Model
{
    protected $fillable = [
        'occasional_service_entry_id',
        'amount',
        'due_date',
        'created_by',
        'payment_paid_on',
        'payment_file_path',
        'payment_original_name',
        'paid_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'payment_paid_on' => 'date',
        ];
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(OccasionalServiceEntry::class, 'occasional_service_entry_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(OccasionalServicePaymentAllocation::class, 'occasional_service_payment_id');
    }
}
