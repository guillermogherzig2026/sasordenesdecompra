<?php

namespace App\Models;

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
