<?php

namespace App\Models\Marketing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tag extends Model
{
    protected $table = 'marketing_tags';

    protected $fillable = [
        'company_id',
        'name',
        'color',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
