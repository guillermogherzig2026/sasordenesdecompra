<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityIncident extends Model
{
    protected $fillable = [
        'security_company_id',
        'security_branch_id',
        'security_camera_id',
        'created_by',
        'title',
        'description',
        'priority',
        'status',
        'occurred_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function securityCompany(): BelongsTo
    {
        return $this->belongsTo(SecurityCompany::class);
    }

    public function securityBranch(): BelongsTo
    {
        return $this->belongsTo(SecurityBranch::class);
    }

    public function securityCamera(): BelongsTo
    {
        return $this->belongsTo(SecurityCamera::class);
    }
}
