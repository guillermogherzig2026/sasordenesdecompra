<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderReceipt extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'received_by',
        'file_path',
        'original_name',
        'invoice_number',
        'received_on',
    ];

    protected function casts(): array
    {
        return [
            'received_on' => 'date',
        ];
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderReceiptItem::class);
    }
}
