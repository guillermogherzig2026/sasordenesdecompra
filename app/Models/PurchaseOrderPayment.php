<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderPayment extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'paid_by',
        'file_path',
        'original_name',
        'paid_on',
    ];

    protected function casts(): array
    {
        return [
            'paid_on' => 'date',
        ];
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
