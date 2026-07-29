<?php

use App\Models\Company;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Company::whereNotNull('warehouses')->each(function (Company $company) {
            $warehouses = $company->warehouses;

            if (! is_array($warehouses) || empty($warehouses)) {
                return;
            }

            $converted = array_map(function ($warehouse) {
                if (is_string($warehouse)) {
                    return ['name' => $warehouse, 'short_name' => ''];
                }

                return $warehouse;
            }, $warehouses);

            $company->warehouses = $converted;
            $company->save();
        });
    }

    public function down(): void
    {
        Company::whereNotNull('warehouses')->each(function (Company $company) {
            $warehouses = $company->warehouses;

            if (! is_array($warehouses) || empty($warehouses)) {
                return;
            }

            $converted = array_map(function ($warehouse) {
                return is_array($warehouse) ? ($warehouse['name'] ?? '') : $warehouse;
            }, $warehouses);

            $company->warehouses = $converted;
            $company->save();
        });
    }
};
