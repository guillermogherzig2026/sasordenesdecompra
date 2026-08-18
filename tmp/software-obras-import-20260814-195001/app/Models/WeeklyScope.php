<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeeklyScope extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'actual_date' => 'date',
            'evidence' => 'array',
            'programmed_quantity' => 'decimal:4',
            'executed_quantity' => 'decimal:4',
            'fulfillment_percent' => 'decimal:2',
            'weekly_budget' => 'decimal:2',
            'actual_cost' => 'decimal:2',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function crew(): BelongsTo
    {
        return $this->belongsTo(Crew::class);
    }
}
