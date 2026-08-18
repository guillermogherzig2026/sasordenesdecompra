<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ConstructionUnitPrice extends Model
{
    protected $fillable = [
        'code',
        'chapter_code',
        'chapter_name',
        'description',
        'unit',
        'labor_unit_price',
        'material_unit_price',
        'total_unit_price',
        'source_page',
    ];

    protected function casts(): array
    {
        return [
            'labor_unit_price' => 'decimal:2',
            'material_unit_price' => 'decimal:2',
            'total_unit_price' => 'decimal:2',
            'source_page' => 'integer',
        ];
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $inner) use ($term): void {
            $inner->where('code', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('unit', 'like', "%{$term}%");
        });
    }
}
