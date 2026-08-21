<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $needsContractorKey = ! Schema::hasColumn('construction_schedule_items', 'contractor_key');
        $needsContractorSequence = ! Schema::hasColumn('construction_schedule_items', 'contractor_sequence');

        if ($needsContractorKey || $needsContractorSequence) {
            Schema::table('construction_schedule_items', function (Blueprint $table) use ($needsContractorKey, $needsContractorSequence) {
                if ($needsContractorKey) {
                    $table->string('contractor_key', 160)->nullable()->after('contractor');
                }

                if ($needsContractorSequence) {
                    $table->unsignedInteger('contractor_sequence')->nullable()->after('contractor_key');
                }
            });
        }

        if (! Schema::hasTable('construction_schedule_contractor_counters')) {
            Schema::create('construction_schedule_contractor_counters', function (Blueprint $table) {
                $table->id();
                $table->foreignId('construction_project_id');
                $table->string('contractor_key', 160);
                $table->string('contractor', 160);
                $table->unsignedInteger('last_sequence')->default(0);
                $table->timestamps();

                $table->unique(
                    ['construction_project_id', 'contractor_key'],
                    'construction_schedule_counter_contractor_unique'
                );
                $table->foreign('construction_project_id', 'construction_schedule_counter_project_fk')
                    ->references('id')
                    ->on('construction_projects')
                    ->cascadeOnDelete();
            });
        } else {
            if (! Schema::hasIndex('construction_schedule_contractor_counters', 'construction_schedule_counter_contractor_unique')) {
                Schema::table('construction_schedule_contractor_counters', function (Blueprint $table) {
                    $table->unique(
                        ['construction_project_id', 'contractor_key'],
                        'construction_schedule_counter_contractor_unique'
                    );
                });
            }

            $hasProjectForeignKey = collect(Schema::getForeignKeys('construction_schedule_contractor_counters'))
                ->contains(fn (array $foreignKey): bool => in_array('construction_project_id', $foreignKey['columns'], true));

            if (! $hasProjectForeignKey) {
                Schema::table('construction_schedule_contractor_counters', function (Blueprint $table) {
                    $table->foreign('construction_project_id', 'construction_schedule_counter_project_fk')
                        ->references('id')
                        ->on('construction_projects')
                        ->cascadeOnDelete();
                });
            }
        }

        $counters = [];
        $now = now();
        $items = DB::table('construction_schedule_items')
            ->orderBy('construction_project_id')
            ->orderBy('id')
            ->get(['id', 'construction_project_id', 'contractor']);

        foreach ($items as $item) {
            $contractor = $this->normalizeContractor((string) $item->contractor);
            $contractorKey = Str::lower($contractor);
            $counterKey = $item->construction_project_id.'|'.$contractorKey;
            $nextSequence = ($counters[$counterKey]['last_sequence'] ?? 0) + 1;

            DB::table('construction_schedule_items')
                ->where('id', $item->id)
                ->update([
                    'contractor' => $contractor,
                    'contractor_key' => $contractorKey,
                    'contractor_sequence' => $nextSequence,
                ]);

            $counters[$counterKey] = [
                'construction_project_id' => $item->construction_project_id,
                'contractor_key' => $contractorKey,
                'contractor' => $contractor,
                'last_sequence' => $nextSequence,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach ($counters as $counter) {
            DB::table('construction_schedule_contractor_counters')->updateOrInsert(
                [
                    'construction_project_id' => $counter['construction_project_id'],
                    'contractor_key' => $counter['contractor_key'],
                ],
                $counter,
            );
        }

        if (! Schema::hasIndex('construction_schedule_items', 'construction_schedule_item_contractor_sequence_unique')) {
            Schema::table('construction_schedule_items', function (Blueprint $table) {
                $table->unique(
                    ['construction_project_id', 'contractor_key', 'contractor_sequence'],
                    'construction_schedule_item_contractor_sequence_unique'
                );
            });
        }
    }

    public function down(): void
    {
        Schema::table('construction_schedule_items', function (Blueprint $table) {
            $table->dropUnique('construction_schedule_item_contractor_sequence_unique');
            $table->dropColumn(['contractor_key', 'contractor_sequence']);
        });

        Schema::dropIfExists('construction_schedule_contractor_counters');
    }

    private function normalizeContractor(string $contractor): string
    {
        return preg_replace('/\s+/u', ' ', trim($contractor)) ?: trim($contractor);
    }
};
