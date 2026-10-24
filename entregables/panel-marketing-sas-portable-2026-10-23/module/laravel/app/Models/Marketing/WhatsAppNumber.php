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
        'meta_phone_number_id',
        'meta_waba_id',
        'meta_access_token',
        'meta_configured_at',
        'last_webhook_at',
    ];

    protected $hidden = ['meta_access_token'];

    protected $casts = [
        'meta_access_token' => 'encrypted',
        'meta_configured_at' => 'datetime',
        'last_webhook_at' => 'datetime',
    ];

    public function hasCloudConfiguration(): bool
    {
        return filled($this->meta_phone_number_id) && filled($this->meta_waba_id) && filled($this->meta_access_token);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
