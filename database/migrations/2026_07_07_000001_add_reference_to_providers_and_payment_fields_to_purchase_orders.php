<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('providers', function (Blueprint $table) {
            if (! Schema::hasColumn('providers', 'reference')) {
                $table->string('reference')->nullable()->after('clabe');
            }
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_orders', 'reference')) {
                $table->string('reference')->nullable()->after('credit_days');
            }

            if (! Schema::hasColumn('purchase_orders', 'payment_concept')) {
                $table->string('payment_concept')->nullable()->after('reference');
            }
        });
    }

    public function down(): void
    {
        $providerColumns = collect(['reference'])
            ->filter(fn (string $column) => Schema::hasColumn('providers', $column))
            ->values()
            ->all();

        if ($providerColumns !== []) {
            Schema::table('providers', function (Blueprint $table) use ($providerColumns) {
                $table->dropColumn($providerColumns);
            });
        }

        $orderColumns = collect(['reference', 'payment_concept'])
            ->filter(fn (string $column) => Schema::hasColumn('purchase_orders', $column))
            ->values()
            ->all();

        if ($orderColumns !== []) {
            Schema::table('purchase_orders', function (Blueprint $table) use ($orderColumns) {
                $table->dropColumn($orderColumns);
            });
        }
    }
};
