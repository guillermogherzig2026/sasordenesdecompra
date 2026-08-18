<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderBusinessLine extends Model
{
    protected $fillable = [
        'name',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function providers()
    {
        return $this->hasMany(Provider::class);
    }

    public function subcategories()
    {
        return $this->hasMany(ProviderBusinessSubcategory::class, 'provider_business_line_id');
    }
}
