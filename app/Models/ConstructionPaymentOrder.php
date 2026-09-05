<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConstructionPaymentOrder extends Model
{
    protected $fillable = [
        'construction_project_id',
        'construction_payroll_id',
        'scheduled_for',
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
        'invoice_xml_file_path',
        'invoice_xml_original_name',
        'fiscal_verification_file_path',
        'fiscal_verification_original_name',
        'payment_file_path',
        'payment_original_name',
        'paid_on',
        'paid_by',
        'dismissed_at',
        'discarded_at',
        'discarded_by',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_for' => 'date',
            'period_start' => 'date',
            'period_end' => 'date',
            'payment_due_date' => 'date',
            'paid_on' => 'date',
            'dismissed_at' => 'datetime',
            'discarded_at' => 'datetime',
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

    public function discardedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'discarded_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query
            ->whereNull('payment_file_path')
            ->whereNull('dismissed_at')
            ->whereNull('discarded_at')
            ->where(function (Builder $availability): void {
                $availability->whereNull('scheduled_for')
                    ->orWhereDate('scheduled_for', '<=', CarbonImmutable::now('America/Mexico_City')->toDateString());
            })
            ->where('status', '!=', 'Borrador')
            ->whereNotIn('status', ['Pagada', 'Pagado', 'Cancelada', 'Cancelado', 'Descartada']);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->whereNotNull('payment_file_path');
    }

    public function scopeHistorical(Builder $query): Builder
    {
        return $query->where(function (Builder $history): void {
            $history->whereNotNull('payment_file_path')
                ->orWhereNotNull('discarded_at')
                ->orWhereNotNull('dismissed_at');
        });
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

        if ($this->period_start && ! $this->period_end && $this->type === 'Nomina') {
            return $this->period_start->format('d/m/Y').' - Indefinido';
        }

        return $this->period_reference ?: 'Pendiente';
    }

    public function invoiceDocumentCount(): int
    {
        return (int) filled($this->invoice_file_path)
            + (int) filled($this->invoice_xml_file_path)
            + (int) filled($this->fiscal_verification_file_path);
    }

    public function invoiceDocumentStatus(): string
    {
        return match ($this->invoiceDocumentCount()) {
            0 => 'empty',
            3 => 'complete',
            default => 'partial',
        };
    }

    public function statusClass(): string
    {
        return match ($this->displayStatus()) {
            'Aprobada', 'Aprobado', 'Concluida' => 'approved',
            'Pagada', 'Pagado' => 'paid',
            'En revision', 'Pausada' => 'warning',
            'Cancelada', 'Cancelado', 'Descartada' => 'canceled',
            'En ejecucion' => 'primary',
            default => 'pending',
        };
    }

    public function displayStatus(): string
    {
        if (filled($this->dismissed_at) && blank($this->payment_file_path)) {
            return 'Descartada';
        }

        return $this->status;
    }
}
