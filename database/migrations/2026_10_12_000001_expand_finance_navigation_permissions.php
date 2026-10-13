<?php

use App\Support\NavigationPermissionCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $granted = NavigationPermissionCatalog::financeRolePermissions();

        DB::table('users')
            ->where('role', 'finance')
            ->whereNotNull('menu_permissions')
            ->orderBy('id')
            ->chunkById(100, function ($users) use ($granted): void {
                foreach ($users as $user) {
                    $current = json_decode($user->menu_permissions, true);
                    $permissions = NavigationPermissionCatalog::normalize(array_merge(
                        is_array($current) ? $current : [],
                        $granted
                    ));

                    DB::table('users')->where('id', $user->id)->update([
                        'menu_permissions' => json_encode($permissions),
                    ]);
                }
            });
    }

    public function down(): void
    {
        // Existing user permissions cannot be distinguished from those added here.
    }
};
