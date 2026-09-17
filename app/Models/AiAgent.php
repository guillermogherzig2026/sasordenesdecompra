<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiAgent extends Model
{
    protected $fillable = [
        'name',
        'description',
        'instructions',
        'is_active',
        'created_by',
    ];

    protected $attributes = [
        'is_active' => false,
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
