<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConstructionPaymentProgressPhoto extends Model
{
    protected $fillable = [
        'construction_payment_order_id',
        'file_path',
        'original_name',
        'description',
        'uploaded_by',
    ];

    public function paymentOrder(): BelongsTo
    {
        return $this->belongsTo(ConstructionPaymentOrder::class, 'construction_payment_order_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
