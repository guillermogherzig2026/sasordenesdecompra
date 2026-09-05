<?php

namespace Database\Seeders;

use App\Models\SecurityBranch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SecurityDemoCameraSeeder extends Seeder
{
    public function run(): void
    {
        SecurityBranch::query()->eachById(function (SecurityBranch $branch): void {
            DB::transaction(function () use ($branch): void {
                $branch->cameras()->delete();
                $branch->cameras()->createMany([
                    [
                        'name' => 'Entrada principal',
                        'stream_url' => "rtsp://demo.invalid/sucursales/{$branch->id}/entrada-principal",
                        'sort_order' => 0,
                    ],
                    [
                        'name' => 'Estacionamiento',
                        'stream_url' => "rtsp://demo.invalid/sucursales/{$branch->id}/estacionamiento",
                        'sort_order' => 1,
                    ],
                    [
                        'name' => 'Área de cajas',
                        'stream_url' => "rtsp://demo.invalid/sucursales/{$branch->id}/area-de-cajas",
                        'sort_order' => 2,
                    ],
                    [
                        'name' => 'Almacén',
                        'stream_url' => "rtsp://demo.invalid/sucursales/{$branch->id}/almacen",
                        'sort_order' => 3,
                    ],
                ]);
            });
        });
    }
}
