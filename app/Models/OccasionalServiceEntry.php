<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OccasionalServiceEntry extends Model
{
    public const FUEL_PROVIDER_REFERENCE = 'EFECTIVALE-CONCENTRADORA';

    protected $fillable = [
        'name',
        'reference_number',
        'bank',
        'payer_account',
        'description',
        'created_by',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(OccasionalService::class, 'occasional_service_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OccasionalServicePayment::class, 'occasional_service_entry_id');
    }
}
