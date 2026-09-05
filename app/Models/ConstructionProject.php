<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConstructionProject extends Model
{
    use SoftDeletes;

    public const STATUSES = ['Por iniciar', 'En ejecucion', 'Concluida'];
    public const CAROUSEL_STATUSES = ['En ejecucion', 'Por iniciar'];

    protected $fillable = [
        'company_id',
        'client_id',
        'responsible_user_id',
        'project_key',
        'name',
        'location',
        'project_type',
        'modality',
        'status',
        'start_date',
        'estimated_end_date',
        'contracted_value',
        'estimated_amount',
        'paid_amount',
        'retention_amount',
        'physical_progress',
        'financial_progress',
        'constructed_area',
        'sellable_rentable_area',
        'parking_area',
        'levels_count',
        'photo_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'estimated_end_date' => 'date',
            'contracted_value' => 'decimal:2',
            'estimated_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'retention_amount' => 'decimal:2',
            'physical_progress' => 'decimal:2',
            'financial_progress' => 'decimal:2',
            'constructed_area' => 'decimal:2',
            'sellable_rentable_area' => 'decimal:2',
            'parking_area' => 'decimal:2',
            'levels_count' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(ConstructionClient::class, 'client_id');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'construction_project_user')
            ->withPivot(['can_view', 'can_edit'])
            ->withTimestamps();
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(ConstructionAuditLog::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(ConstructionPayroll::class);
    }

    public function paymentOrders(): HasMany
    {
        return $this->hasMany(ConstructionPaymentOrder::class);
    }

    public function scheduleItems(): HasMany
    {
        return $this->hasMany(ConstructionScheduleItem::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->role === 'superadmin') {
            return $query;
        }

        return $query->whereHas('users', fn (Builder $users) => $users
            ->where('users.id', $user->id)
            ->where('construction_project_user.can_view', true));
    }

    public function getBalanceToPayAttribute(): float
    {
        return max((float) $this->estimated_amount - (float) $this->paid_amount, 0);
    }

    public function getPhysicalFinancialDifferenceAttribute(): float
    {
        return (float) $this->physical_progress - (float) $this->financial_progress;
    }

    public function getDaysElapsedAttribute(): int
    {
        return $this->start_date ? max($this->start_date->diffInDays(now(), false), 0) : 0;
    }

    public function getDaysRemainingAttribute(): int
    {
        return $this->estimated_end_date ? max(now()->diffInDays($this->estimated_end_date, false), 0) : 0;
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'Concluida', 'Terminada' => 'success',
            'Por iniciar' => 'warning',
            'Suspendida', 'Cancelada' => 'danger',
            default => 'primary',
        };
    }
}
