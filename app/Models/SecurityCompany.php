<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class SecurityCompany extends Model
{
    protected $fillable = [
        'name',
        'entity_type',
        'legal_name',
        'rfc',
        'address',
        'contact_name',
        'contact_phone',
        'contact_email',
        'finance_company_id',
    ];

    public function financeCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'finance_company_id');
    }

    public function branches(): HasMany
    {
        return $this->hasMany(SecurityBranch::class);
    }

    public function cameras(): HasManyThrough
    {
        return $this->hasManyThrough(SecurityCamera::class, SecurityBranch::class);
    }

    public function initials(): string
    {
        return collect(preg_split('/\s+/', trim($this->name)))
            ->filter()
            ->map(fn (string $word) => mb_substr($word, 0, 1))
            ->take(2)
            ->implode('');
    }
}
