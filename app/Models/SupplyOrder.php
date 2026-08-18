<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplyOrder extends Model
{
    protected $fillable = [
        'folio',
        'requester_id',
        'company_id',
        'warehouse_from',
        'warehouse_to',
        'created_on',
        'delivery_date',
        'due_date',
        'status',
        'total',
        'notes',
        'delivery_remission_number',
        'remission_token',
        'delivered_on',
        'delivered_by',
        'received_on',
        'received_by_name',
        'receiving_pin',
    ];

    protected function casts(): array
    {
        return [
            'created_on' => 'date',
            'delivery_date' => 'date',
            'due_date' => 'date',
            'delivered_on' => 'date',
            'received_on' => 'date',
            'total' => 'decimal:2',
        ];
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function deliveredBy()
    {
        return $this->belongsTo(User::class, 'delivered_by');
    }

    public function items()
    {
        return $this->hasMany(SupplyOrderItem::class);
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function getRouteKeyName(): string
    {
        return 'folio';
    }

    public function getSupplyConsecutiveAttribute(): string
    {
        return str_pad((string) $this->getKey(), 6, '0', STR_PAD_LEFT);
    }

    public function getFormattedDeliveryRemissionNumberAttribute(): ?string
    {
        if (! $this->delivery_remission_number) {
            return null;
        }

        if (preg_match('/^REM-(\d{4})-(\d+)$/', $this->delivery_remission_number, $matches)) {
            return 'REM-'.$matches[1].'-'.str_pad($matches[2], 6, '0', STR_PAD_LEFT);
        }

        if (preg_match('/^REM-(\d+)$/', $this->delivery_remission_number, $matches)) {
            $year = $this->delivered_on?->format('Y') ?: $this->created_on?->format('Y') ?: now()->format('Y');

            return 'REM-'.$year.'-'.str_pad($matches[1], 6, '0', STR_PAD_LEFT);
        }

        return $this->delivery_remission_number;
    }

    public function isPendingForUser(): bool
    {
        return in_array($this->status, ['sent', 'approved', 'remitted'], true);
    }
}
