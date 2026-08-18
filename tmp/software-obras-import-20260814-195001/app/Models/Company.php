<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $guarded = [];

    public function auditModule(): string
    {
        return 'Empresas';
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
