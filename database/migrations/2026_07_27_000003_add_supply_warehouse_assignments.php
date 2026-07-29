<?php

use App\Models\Company;
use App\Models\WarehouseInventoryItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('supply_warehouses')) {
            Schema::create('supply_warehouses', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->string('name');
                $table->string('short_name')->nullable();
                $table->string('address')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('supply_warehouse_companies')) {
            Schema::create('supply_warehouse_companies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('supply_warehouse_id')->constrained()->cascadeOnDelete();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['supply_warehouse_id', 'company_id']);
            });
        }

        $warehouseId = DB::table('supply_warehouses')->where('key', 'central')->value('id');

        if (! $warehouseId) {
            $warehouseId = DB::table('supply_warehouses')->insertGetId([
                'key' => 'central',
                'name' => 'Almacen central',
                'short_name' => 'Central',
                'address' => WarehouseInventoryItem::CENTRAL_WAREHOUSE,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $prodifemId = Company::query()
            ->where('name', 'like', '%Prodifem%')
            ->value('id');

        if ($prodifemId && ! DB::table('supply_warehouse_companies')->where('supply_warehouse_id', $warehouseId)->where('company_id', $prodifemId)->exists()) {
            DB::table('supply_warehouse_companies')->insert([
                'supply_warehouse_id' => $warehouseId,
                'company_id' => $prodifemId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('supply_warehouse_companies');
        Schema::dropIfExists('supply_warehouses');
    }
};
