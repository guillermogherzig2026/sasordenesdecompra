<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('provider_business_lines')) {
            Schema::create('provider_business_lines', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('providers', 'provider_business_line_id')) {
            Schema::table('providers', function (Blueprint $table) {
                $table->foreignId('provider_business_line_id')
                    ->nullable()
                    ->after('business_line')
                    ->constrained('provider_business_lines')
                    ->nullOnDelete();
            });
        }

        $defaultLines = collect([
            'Medicamentos',
            'Servicios farmaceuticos',
            'Materiales de obra',
            'Otros',
        ]);

        $existingLines = DB::table('providers')
            ->select('business_line')
            ->whereNotNull('business_line')
            ->where('business_line', '!=', '')
            ->distinct()
            ->pluck('business_line');

        $defaultLines
            ->merge($existingLines)
            ->map(fn ($line) => trim((string) $line))
            ->filter()
            ->unique()
            ->values()
            ->each(function (string $line) {
                DB::table('provider_business_lines')->updateOrInsert(
                    ['name' => $line],
                    [
                        'active' => true,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ],
                );
            });

        DB::table('providers')
            ->whereNull('provider_business_line_id')
            ->orderBy('id')
            ->get(['id', 'business_line'])
            ->each(function ($provider) {
                $line = DB::table('provider_business_lines')
                    ->where('name', $provider->business_line)
                    ->first();

                if ($line) {
                    DB::table('providers')
                        ->where('id', $provider->id)
                        ->update(['provider_business_line_id' => $line->id]);
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('providers', 'provider_business_line_id')) {
            Schema::table('providers', function (Blueprint $table) {
                $table->dropConstrainedForeignId('provider_business_line_id');
            });
        }

        Schema::dropIfExists('provider_business_lines');
    }
};
