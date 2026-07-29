<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReimbursementOrder extends Model
{
    protected $fillable = [
        'folio',
        'requester_id',
        'company_id',
        'provider',
        'concept',
        'created_on',
        'amount',
        'status',
        'quote_file_path',
        'quote_original_name',
        'support_file_path',
        'support_original_name',
        'support_on',
        'payment_file_path',
        'payment_original_name',
        'paid_on',
        'paid_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'created_on' => 'date',
            'amount' => 'decimal:2',
            'support_on' => 'date',
            'paid_on' => 'date',
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

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    public function getRouteKeyName(): string
    {
        return 'folio';
    }
}
