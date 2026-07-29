<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_orders', 'is_credit')) {
                $table->boolean('is_credit')->default(false)->after('due_date');
            }

            if (! Schema::hasColumn('purchase_orders', 'credit_days')) {
                $table->unsignedSmallInteger('credit_days')->nullable()->after('is_credit');
            }
        });
    }

    public function down(): void
    {
        $columns = collect(['is_credit', 'credit_days'])
            ->filter(fn (string $column) => Schema::hasColumn('purchase_orders', $column))
            ->values()
            ->all();

        if ($columns === []) {
            return;
        }

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn($columns);
        });
    }
};
