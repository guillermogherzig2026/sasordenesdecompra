<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class RecurringService extends Model
{
    protected $fillable = [
        'folio',
        'holder',
        'company_name',
        'bank',
        'payer_account',
        'branch',
        'service_name',
        'provider',
        'service_number',
        'category',
        'cost',
        'validity',
        'payment_interval_days',
        'due_days_after_cutoff',
        'is_domiciled',
        'start_date',
        'cutoff_day',
        'cutoff_month',
        'cutoff_year',
        'reference',
        'service_location',
        'notes',
        'status',
        'created_by',
    ];

    public function getDisplayBranchAttribute(): string
    {
        return $this->branch ?: 'Sin sucursal';
    }

    public function getDisplayLocationAttribute(): string
    {
        if (filled($this->service_location)) {
            return $this->service_location;
        }

        static $companyAddresses = [];
        $companyName = (string) ($this->company_name ?: $this->holder);

        if ($companyName === '') {
            return 'Sin ubicacion';
        }

        if (! array_key_exists($companyName, $companyAddresses)) {
            $companyAddresses[$companyName] = Company::where('name', $companyName)->value('address');
        }

        return $companyAddresses[$companyName] ?: 'Sin ubicacion';
    }

    public function nextScheduledPaymentDate(?Carbon $from = null): ?Carbon
    {
        $start = $this->start_date?->copy()->startOfDay();

        if (! $start) {
            return null;
        }

        $interval = max((int) $this->payment_interval_days, 1);

        if ($this->cutoff_day) {
            $month = (int) ($this->cutoff_month ?: $start->month);
            $year = (int) ($this->cutoff_year ?: $start->year);

            if (! checkdate($month, (int) $this->cutoff_day, $year)) {
                return null;
            }

            $due = Carbon::create($year, $month, (int) $this->cutoff_day)->startOfDay();
            if ($due->lt($start)) {
                $due->addYear();
            }
            $due->addDays($interval);
        } else {
            $due = $start->copy()->addDays(max((int) $this->due_days_after_cutoff, 0));
        }

        $today = ($from ?? now())->copy()->startOfDay();
        while ($due->lt($today)) {
            $due->addDays($interval);
        }

        return $due;
    }

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'payment_interval_days' => 'integer',
            'due_days_after_cutoff' => 'integer',
            'cutoff_day' => 'integer',
            'cutoff_month' => 'integer',
            'cutoff_year' => 'integer',
            'is_domiciled' => 'boolean',
            'start_date' => 'date',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function receipts()
    {
        return $this->hasMany(RecurringServiceReceipt::class);
    }
}
