<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lead extends Model
{
    protected $table = 'marketing_leads';

    public const STATUSES = [
        'nuevo' => 'Nuevo',
        'interesado' => 'Interesado',
        'calificado' => 'Calificado',
        'seguimiento' => 'Seguimiento',
        'convertido' => 'Convertido',
        'descartado' => 'Descartado',
    ];

    protected $fillable = [
        'company_id',
        'campaign_id',
        'advisor_id',
        'name',
        'phone',
        'email',
        'product',
        'status',
        'estimated_value',
        'last_contact_at',
        'next_follow_up_at',
        'source',
        'position',
        'tags',
        'notes',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'last_contact_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
        'tags' => 'array',
    ];

    public function scopeVisibleTo(Builder $query, \App\Models\User $user): Builder
    {
        return $query;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_id');
    }

    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
}
