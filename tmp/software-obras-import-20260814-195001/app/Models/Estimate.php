<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estimate extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'cutoff_date' => 'date',
            'prepared_at' => 'date',
            'authorized_at' => 'date',
            'scheduled_payment_date' => 'date',
            'attachments' => 'array',
            'previous_progress' => 'decimal:2',
            'period_progress' => 'decimal:2',
            'cumulative_progress' => 'decimal:2',
            'gross_amount' => 'decimal:2',
            'retention' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance' => 'decimal:2',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(EstimateItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(EstimatePayment::class);
    }
}
