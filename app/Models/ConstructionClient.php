<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConstructionClient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'rfc',
        'contact_name',
        'phone',
        'email',
        'status',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(ConstructionProject::class, 'client_id');
    }
}
