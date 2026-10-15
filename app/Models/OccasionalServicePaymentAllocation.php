<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OccasionalServicePaymentAllocation extends Model
{
    protected $fillable = [
        'occasional_service_entry_id',
        'recipient_name',
        'reference_number',
        'amount',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(OccasionalServicePayment::class, 'occasional_service_payment_id');
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(OccasionalServiceEntry::class, 'occasional_service_entry_id');
    }
}
