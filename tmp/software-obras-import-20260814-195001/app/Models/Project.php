<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $guarded = [];

    protected $appends = [
        'balance_to_pay',
        'days_elapsed',
        'days_remaining',
        'physical_financial_difference',
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
        ];
    }

    public function auditModule(): string
    {
        return 'Obras';
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->whereHas('users', function (Builder $builder) use ($user): void {
            $builder->where('users.id', $user->id)->where('project_users.can_view', true);
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_users')
            ->withPivot(['can_view', 'can_edit'])
            ->withTimestamps();
    }

    public function contract(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function workItems(): HasMany
    {
        return $this->hasMany(WorkItem::class);
    }

    public function estimates(): HasMany
    {
        return $this->hasMany(Estimate::class);
    }

    public function progressRecords(): HasMany
    {
        return $this->hasMany(ProgressRecord::class);
    }

    public function retentions(): HasMany
    {
        return $this->hasMany(Retention::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function weeklyScopes(): HasMany
    {
        return $this->hasMany(WeeklyScope::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function materialRequests(): HasMany
    {
        return $this->hasMany(MaterialRequest::class);
    }

    public function supplyOrders(): HasMany
    {
        return $this->hasMany(SupplyOrder::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function dailyLogs(): HasMany
    {
        return $this->hasMany(DailyLog::class);
    }

    public function changeOrders(): HasMany
    {
        return $this->hasMany(ChangeOrder::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(ProjectEvent::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
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
        if (! $this->start_date) {
            return 0;
        }

        return (int) max($this->start_date->diffInDays(now(), false), 0);
    }

    public function getDaysRemainingAttribute(): int
    {
        if (! $this->estimated_end_date) {
            return 0;
        }

        return (int) max(now()->diffInDays($this->estimated_end_date, false), 0);
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'Terminada', 'Pagada', 'Activo' => 'success',
            'En Proceso', 'En ejecucion', 'En estimacion', 'En revision' => 'primary',
            'Por iniciar', 'Pausada', 'Pendiente' => 'secondary',
            'Vencida', 'Critica', 'Rechazada' => 'danger',
            default => 'primary',
        };
    }
}
