<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'signed_at' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
            'total_value' => 'decimal:2',
            'retention_percentage' => 'decimal:2',
        ];
    }

    public function auditModule(): string
    {
        return 'Contratos';
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
