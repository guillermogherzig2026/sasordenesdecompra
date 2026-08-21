<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('companies') || ! Schema::hasColumn('companies', 'warehouses')) {
            return;
        }

        DB::table('companies')
            ->select(['id', 'address', 'warehouses'])
            ->orderBy('id')
            ->chunkById(100, function ($companies): void {
                foreach ($companies as $company) {
                    $companyAddress = trim((string) $company->address);
                    $warehouses = collect($this->decodeWarehouses($company->warehouses))
                        ->map(function ($warehouse) use ($companyAddress): array {
                            if (is_array($warehouse)) {
                                return [
                                    'name' => trim((string) ($warehouse['name'] ?? '')),
                                    'short_name' => trim((string) ($warehouse['short_name'] ?? '')),
                                    'address' => trim((string) ($warehouse['address'] ?? '')) ?: $companyAddress,
                                ];
                            }

                            return [
                                'name' => trim((string) $warehouse),
                                'short_name' => '',
                                'address' => $companyAddress,
                            ];
                        })
                        ->filter(fn (array $warehouse): bool => filled($warehouse['name']))
                        ->unique('name')
                        ->values();

                    if ($warehouses->isEmpty()) {
                        $warehouses->push([
                            'name' => 'Almacen principal',
                            'short_name' => 'Principal',
                            'address' => $companyAddress,
                        ]);
                    }

                    DB::table('companies')
                        ->where('id', $company->id)
                        ->update([
                            'warehouses' => json_encode(
                                $warehouses->all(),
                                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                            ),
                        ]);
                }
            });
    }

    public function down(): void
    {
        // Preserve addresses added by users or normalized by this migration.
    }

    private function decodeWarehouses(mixed $warehouses): array
    {
        if (is_array($warehouses)) {
            return $warehouses;
        }

        if (! is_string($warehouses) || trim($warehouses) === '') {
            return [];
        }

        $decoded = json_decode($warehouses, true);

        return is_array($decoded) ? $decoded : [];
    }
};
