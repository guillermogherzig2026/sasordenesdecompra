<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollItem extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'days_worked' => 'decimal:2',
            'normal_hours' => 'decimal:2',
            'extra_hours' => 'decimal:2',
            'jornales' => 'decimal:2',
            'piecework_amount' => 'decimal:2',
            'bonus' => 'decimal:2',
            'discount' => 'decimal:2',
            'loan' => 'decimal:2',
            'gross_amount' => 'decimal:2',
            'deductions' => 'decimal:2',
            'net_amount' => 'decimal:2',
        ];
    }

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
