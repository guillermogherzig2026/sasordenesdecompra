<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'menu_permissions')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->json('menu_permissions')->nullable()->after('companies');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'menu_permissions')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('menu_permissions');
            });
        }
    }
};
