<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ConstructionPayroll extends Model
{
    public const STATUSES = [
        'Borrador',
        'Programada',
        'En revision',
        'Aprobada',
        'Pausada',
        'Cancelada',
        'Concluida',
        'Pagada',
    ];

    public const CATALOG_STATUSES = [
        'Borrador',
        'Programada',
        'Cancelada',
        'Concluida',
    ];

    protected $fillable = [
        'construction_project_id',
        'code',
        'contractor',
        'description',
        'area',
        'periodicity',
        'period_start',
        'period_end',
        'payment_due_date',
        'progress',
        'amount',
        'status',
        'payment_date',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'payment_due_date' => 'date',
            'payment_date' => 'date',
            'progress' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(ConstructionProject::class, 'construction_project_id');
    }

    public function paymentOrder(): HasOne
    {
        return $this->hasOne(ConstructionPaymentOrder::class, 'construction_payroll_id');
    }

    public function paymentOrders(): HasMany
    {
        return $this->hasMany(ConstructionPaymentOrder::class, 'construction_payroll_id');
    }
}
