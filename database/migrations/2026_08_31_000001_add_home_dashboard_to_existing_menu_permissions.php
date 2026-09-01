<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const HOME_DASHBOARD = 'home.dashboard';

    public function up(): void
    {
        if (! Schema::hasColumn('users', 'menu_permissions')) {
            return;
        }

        DB::table('users')
            ->whereNotNull('menu_permissions')
            ->orderBy('id')
            ->get(['id', 'menu_permissions'])
            ->each(function ($user): void {
                $permissions = json_decode((string) $user->menu_permissions, true);

                if (! is_array($permissions) || in_array(self::HOME_DASHBOARD, $permissions, true)) {
                    return;
                }

                $permissions[] = self::HOME_DASHBOARD;

                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['menu_permissions' => json_encode(array_values(array_unique($permissions)))]);
            });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'menu_permissions')) {
            return;
        }

        DB::table('users')
            ->whereNotNull('menu_permissions')
            ->orderBy('id')
            ->get(['id', 'menu_permissions'])
            ->each(function ($user): void {
                $permissions = json_decode((string) $user->menu_permissions, true);

                if (! is_array($permissions) || ! in_array(self::HOME_DASHBOARD, $permissions, true)) {
                    return;
                }

                $permissions = array_values(array_filter(
                    $permissions,
                    fn ($permission) => $permission !== self::HOME_DASHBOARD
                ));

                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['menu_permissions' => json_encode($permissions)]);
            });
    }
};
