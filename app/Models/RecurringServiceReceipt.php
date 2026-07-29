<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecurringServiceReceipt extends Model
{
    protected $fillable = [
        'recurring_service_id',
        'due_date',
        'period_start',
        'amount',
        'support_file_path',
        'support_original_name',
        'support_on',
        'payment_file_path',
        'payment_original_name',
        'payment_paid_on',
        'paid_by',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'period_start' => 'date',
            'amount' => 'decimal:2',
            'support_on' => 'date',
            'payment_paid_on' => 'date',
        ];
    }

    public function recurringService()
    {
        return $this->belongsTo(RecurringService::class);
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function isPaid(): bool
    {
        return filled($this->payment_file_path);
    }
}
