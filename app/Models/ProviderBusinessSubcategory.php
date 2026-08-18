<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderBusinessSubcategory extends Model
{
    protected $fillable = [
        'provider_business_line_id',
        'name',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function businessLine()
    {
        return $this->belongsTo(ProviderBusinessLine::class, 'provider_business_line_id');
    }

    public function providers()
    {
        return $this->hasMany(Provider::class, 'provider_business_subcategory_id');
    }
}
