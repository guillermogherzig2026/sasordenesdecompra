<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'plain_password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('plain_password')->nullable()->after('password');
            });
        }

        DB::table('users')
            ->where('email', 'gherzig@sasordenesdecompra.com')
            ->whereNull('plain_password')
            ->update(['plain_password' => 'ghfarma2026']);

        DB::table('users')
            ->whereNull('plain_password')
            ->update([
                'password' => Hash::make('ghfarma2026'),
                'plain_password' => 'ghfarma2026',
            ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'plain_password')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('plain_password');
            });
        }
    }
};
