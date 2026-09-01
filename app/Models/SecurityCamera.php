<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityCamera extends Model
{
    protected $fillable = [
        'security_branch_id',
        'name',
        'stream_url',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'stream_url' => 'encrypted',
        ];
    }

    public function securityBranch(): BelongsTo
    {
        return $this->belongsTo(SecurityBranch::class);
    }
}
