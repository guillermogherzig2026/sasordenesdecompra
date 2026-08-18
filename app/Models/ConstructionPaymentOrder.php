<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConstructionPaymentOrder extends Model
{
    protected $fillable = [
        'construction_project_id',
        'construction_payroll_id',
        'type',
        'code',
        'description',
        'contractor',
        'area',
        'periodicity',
        'period_start',
        'period_end',
        'period_reference',
        'payment_due_date',
        'progress',
        'amount',
        'status',
        'invoice_file_path',
        'invoice_original_name',
        'payment_file_path',
        'payment_original_name',
        'paid_on',
        'paid_by',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'payment_due_date' => 'date',
            'paid_on' => 'date',
            'progress' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(ConstructionProject::class, 'construction_project_id');
    }

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(ConstructionPayroll::class, 'construction_payroll_id');
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query
            ->whereNull('payment_file_path')
            ->whereNotIn('status', ['Pagada', 'Pagado', 'Cancelada', 'Cancelado']);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->whereNotNull('payment_file_path');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $inner) use ($term): void {
            $inner->where('type', 'like', "%{$term}%")
                ->orWhere('code', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('contractor', 'like', "%{$term}%")
                ->orWhereHas('project', fn (Builder $project) => $project
                    ->where('project_key', 'like', "%{$term}%")
                    ->orWhere('name', 'like', "%{$term}%"));
        });
    }

    public function periodLabel(): string
    {
        if ($this->period_start && $this->period_end) {
            return $this->period_start->format('d/m').' - '.$this->period_end->format('d/m/Y');
        }

        return $this->period_reference ?: 'Pendiente';
    }

    public function statusClass(): string
    {
        return match ($this->status) {
            'Aprobada', 'Aprobado' => 'approved',
            'Pagada', 'Pagado' => 'paid',
            'En revision' => 'warning',
            'Cancelada', 'Cancelado' => 'canceled',
            'En ejecucion' => 'primary',
            default => 'pending',
        };
    }
}
