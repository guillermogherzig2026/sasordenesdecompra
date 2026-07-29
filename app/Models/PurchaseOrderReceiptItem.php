<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderReceiptItem extends Model
{
    protected $fillable = [
        'purchase_order_receipt_id',
        'purchase_order_item_id',
        'received_quantity',
    ];

    protected function casts(): array
    {
        return [
            'received_quantity' => 'decimal:2',
        ];
    }

    public function receipt()
    {
        return $this->belongsTo(PurchaseOrderReceipt::class, 'purchase_order_receipt_id');
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }
}
