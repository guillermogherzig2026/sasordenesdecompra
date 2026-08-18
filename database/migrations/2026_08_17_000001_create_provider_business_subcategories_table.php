<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('provider_business_subcategories')) {
            Schema::create('provider_business_subcategories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('provider_business_line_id');
                $table->string('name');
                $table->boolean('active')->default(true);
                $table->timestamps();

                $table->foreign('provider_business_line_id', 'provider_subcategories_line_fk')
                    ->references('id')
                    ->on('provider_business_lines')
                    ->cascadeOnDelete();
                $table->unique(['provider_business_line_id', 'name'], 'provider_subcategories_line_name_unique');
            });
        }

        if (! Schema::hasColumn('providers', 'provider_business_subcategory_id')) {
            Schema::table('providers', function (Blueprint $table) {
                $table->foreignId('provider_business_subcategory_id')
                    ->nullable()
                    ->after('provider_business_line_id')
                    ->constrained('provider_business_subcategories')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('providers', 'provider_business_subcategory')) {
            Schema::table('providers', function (Blueprint $table) {
                $table->string('provider_business_subcategory')->nullable()->after('provider_business_subcategory_id');
            });
        }

        $line = DB::table('provider_business_lines')
            ->where('name', 'Farmaceuticos (Medicamentos e Insumos)')
            ->orWhere('name', 'like', 'Farmaceuticos%')
            ->first();

        if ($line) {
            collect(['Medicamentos', 'Material de curacion', 'Dispositivos medicos'])
                ->each(function (string $name) use ($line): void {
                    DB::table('provider_business_subcategories')->updateOrInsert(
                        [
                            'provider_business_line_id' => $line->id,
                            'name' => $name,
                        ],
                        [
                            'active' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    );
                });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('providers', 'provider_business_subcategory')) {
            Schema::table('providers', function (Blueprint $table) {
                $table->dropColumn('provider_business_subcategory');
            });
        }

        if (Schema::hasColumn('providers', 'provider_business_subcategory_id')) {
            Schema::table('providers', function (Blueprint $table) {
                $table->dropConstrainedForeignId('provider_business_subcategory_id');
            });
        }

        Schema::dropIfExists('provider_business_subcategories');
    }
};
