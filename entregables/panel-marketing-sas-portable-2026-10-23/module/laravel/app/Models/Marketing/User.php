<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $table = 'marketing_users';

    public const ROLES = [
        'superadministrador' => 'Superadministrador',
        'administrador' => 'Administrador de empresa',
        'supervisor' => 'Supervisor',
        'asesor' => 'Asesor',
    ];

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'role',
        'phone',
        'timezone',
        'active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'active' => 'boolean',
        'password' => 'hashed',
    ];

    protected function initials(): Attribute
    {
        return Attribute::make(
            get: fn (): string => collect(explode(' ', $this->name))
                ->filter()
                ->take(2)
                ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
                ->implode('')
        );
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadministrador';
    }

    public function canManageCompany(?Company $company): bool
    {
        if (! $company) {
            return $this->isSuperAdmin();
        }

        return $this->isSuperAdmin() || (int) $this->company_id === (int) $company->id;
    }

    public function canEditAdminData(): bool
    {
        return in_array($this->role, ['superadministrador', 'administrador', 'supervisor'], true);
    }

    public function scopeVisibleTo(Builder $query, \App\Models\User $user): Builder
    {
        return $query;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'marketing_campaign_user', 'user_id', 'campaign_id')->withTimestamps();
    }

    public function assignedLeads(): HasMany
    {
        return $this->hasMany(Lead::class, 'advisor_id');
    }

    public function assignedConversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'assigned_user_id');
    }
}
