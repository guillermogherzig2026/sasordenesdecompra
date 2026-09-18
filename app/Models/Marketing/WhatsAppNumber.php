<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppNumber extends Model
{
    protected $table = 'marketing_whatsapp_numbers';

    protected $fillable = [
        'company_id',
        'display_name',
        'phone_number',
        'provider_phone_id',
        'status',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
