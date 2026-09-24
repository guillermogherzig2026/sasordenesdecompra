<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeBiometricMedia extends Model
{
    protected $table = 'employee_biometric_media';

    protected $fillable = [
        'user_id',
        'employee_number',
        'original_name',
        'path',
        'mime_type',
        'media_type',
        'byte_size',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'byte_size' => 'integer',
        ];
    }
}
