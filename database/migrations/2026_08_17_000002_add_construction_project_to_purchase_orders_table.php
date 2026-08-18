<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('purchase_orders', 'construction_project_id')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->foreignId('construction_project_id')
                    ->nullable()
                    ->after('buyer_id')
                    ->constrained('construction_projects')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('purchase_orders', 'construction_project_id')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->dropConstrainedForeignId('construction_project_id');
            });
        }
    }
};
