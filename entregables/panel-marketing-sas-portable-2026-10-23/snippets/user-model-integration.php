<?php

namespace App\Models;

use App\Models\Concerns\HasMarketingPermissions;
use Illuminate\Foundation\Auth\User as Authenticatable;

// Ejemplo minimo. En el proyecto destino conserva los traits y metodos existentes.
class User extends Authenticatable
{
    use HasMarketingPermissions;
}
