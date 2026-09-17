<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $table = 'marketing_campaigns';

    public const STATUSES = [
        'active' => 'Activa',
        'paused' => 'Pausada',
        'ended' => 'Finalizada',
    ];

    protected $fillable = [
        'company_id',
        'responsible_user_id',
        'name',
        'status',
        'starts_at',
        'ends_at',
        'budget',
        'spent',
        'product',
        'source',
        'social_networks',
        'description',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'budget' => 'decimal:2',
        'spent' => 'decimal:2',
        'social_networks' => 'array',
    ];

    public function scopeVisibleTo(Builder $query, \App\Models\User $user): Builder
    {
        return $query;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'marketing_campaign_user', 'campaign_id', 'user_id')->withTimestamps();
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
}
