<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's production baseline.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'gherzig@sasordenesdecompra.com'],
            [
                'name' => 'Super Administrador',
                'password' => 'ghfarma2026',
                'role' => 'superadmin',
                'companies' => [],
                'active' => true,
            ],
        );
    }
}
