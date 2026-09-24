<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SecurityBranch extends Model
{
    protected $fillable = [
        'security_company_id',
        'name',
        'code',
        'description',
        'address',
        'country',
        'state',
        'city',
        'postal_code',
        'contact_name',
        'phone',
        'email',
        'timezone',
        'status',
        'is_primary',
        'analytics_enabled',
        'alerts_enabled',
    ];

    protected function casts(): array
    {
        return [
            'analytics_enabled' => 'boolean',
            'alerts_enabled' => 'boolean',
            'is_primary' => 'boolean',
        ];
    }

    public function securityCompany(): BelongsTo
    {
        return $this->belongsTo(SecurityCompany::class);
    }

    public function cameras(): HasMany
    {
        return $this->hasMany(SecurityCamera::class)->orderBy('sort_order');
    }
}
