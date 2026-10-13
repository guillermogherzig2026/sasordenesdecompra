<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OccasionalService extends Model
{
    protected $fillable = [
        'name',
        'description',
        'icon',
        'color',
        'created_by',
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(OccasionalServiceEntry::class);
    }
}
