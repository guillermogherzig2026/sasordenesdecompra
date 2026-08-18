<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('construction_unit_prices', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('chapter_code', 4)->index();
            $table->string('chapter_name');
            $table->text('description');
            $table->string('unit', 40);
            $table->decimal('labor_unit_price', 16, 2)->nullable();
            $table->decimal('material_unit_price', 16, 2)->nullable();
            $table->decimal('total_unit_price', 16, 2);
            $table->unsignedSmallInteger('source_page')->nullable();
            $table->timestamps();
        });

        $this->importCatalog();
    }

    public function down(): void
    {
        Schema::dropIfExists('construction_unit_prices');
    }

    private function importCatalog(): void
    {
        $path = database_path('data/cdmx_unit_prices_january_2026.csv');
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException("No se pudo abrir el catalogo de precios unitarios: {$path}");
        }

        try {
            $header = fgetcsv($handle, 0, ',', '"', '');

            if ($header === false) {
                throw new RuntimeException('El catalogo de precios unitarios no contiene encabezados.');
            }

            $now = now();
            $batch = [];

            while (($values = fgetcsv($handle, 0, ',', '"', '')) !== false) {
                if (count($values) !== count($header)) {
                    throw new RuntimeException('Se encontro un renglon invalido en el catalogo de precios unitarios.');
                }

                $row = array_combine($header, $values);
                $batch[] = [
                    'code' => $row['code'],
                    'chapter_code' => $row['chapter_code'],
                    'chapter_name' => $row['chapter_name'],
                    'description' => $row['description'],
                    'unit' => $row['unit'],
                    'labor_unit_price' => $row['labor_unit_price'] !== '' ? $row['labor_unit_price'] : null,
                    'material_unit_price' => $row['material_unit_price'] !== '' ? $row['material_unit_price'] : null,
                    'total_unit_price' => $row['total_unit_price'],
                    'source_page' => $row['source_page'] !== '' ? (int) $row['source_page'] : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                if (count($batch) === 400) {
                    DB::table('construction_unit_prices')->insert($batch);
                    $batch = [];
                }
            }

            if ($batch !== []) {
                DB::table('construction_unit_prices')->insert($batch);
            }
        } finally {
            fclose($handle);
        }
    }
};
