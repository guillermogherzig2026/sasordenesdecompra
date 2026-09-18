<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Company extends Model
{
    protected $table = 'marketing_companies';

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'industry',
        'logo_initials',
        'brand_color',
        'active',
        'status',
        'api_token',
        'settings',
    ];

    protected $casts = [
        'active' => 'boolean',
        'settings' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Company $company): void {
            $company->slug = $company->slug ?: Str::slug($company->name).'-'.Str::lower(Str::random(4));
            $company->logo_initials = $company->logo_initials ?: Str::of($company->name)->substr(0, 2)->upper();
            $company->api_token = $company->api_token ?: Str::random(64);
            $company->status = $company->active ? 'active' : 'inactive';
        });

        static::saving(function (Company $company): void {
            $company->status = $company->active ? 'active' : 'inactive';
        });
    }

    public function scopeVisibleTo(Builder $query, \App\Models\User $user): Builder
    {
        return $query;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function whatsappNumbers(): HasMany
    {
        return $this->hasMany(WhatsAppNumber::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    public function automationRules(): HasMany
    {
        return $this->hasMany(AutomationRule::class);
    }
}
