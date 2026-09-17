<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationRule extends Model
{
    protected $table = 'marketing_automation_rules';

    protected $fillable = [
        'company_id',
        'campaign_id',
        'name',
        'trigger',
        'action',
        'payload',
        'active',
    ];

    protected $casts = [
        'payload' => 'array',
        'active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
