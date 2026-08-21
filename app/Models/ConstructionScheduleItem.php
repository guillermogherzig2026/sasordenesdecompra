<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConstructionScheduleItem extends Model
{
    public const STATUSES = ['Programado', 'En proceso', 'Concluido'];

    protected $fillable = [
        'construction_project_id',
        'created_by_user_id',
        'title',
        'contractor',
        'contractor_key',
        'contractor_sequence',
        'description',
        'start_date',
        'end_date',
        'progress',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'contractor_sequence' => 'integer',
            'progress' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(ConstructionProject::class, 'construction_project_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
