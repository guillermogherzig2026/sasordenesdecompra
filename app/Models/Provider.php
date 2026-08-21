<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = [
        'buyer_id',
        'business_name',
        'rfc',
        'contact_name',
        'phone',
        'address',
        'business_line',
        'provider_business_line_id',
        'provider_business_subcategory_id',
        'provider_business_subcategory',
        'bank',
        'account_number',
        'clabe',
        'reference',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function businessLine()
    {
        return $this->belongsTo(ProviderBusinessLine::class, 'provider_business_line_id');
    }

    public function businessSubcategory()
    {
        return $this->belongsTo(ProviderBusinessSubcategory::class, 'provider_business_subcategory_id');
    }
}
