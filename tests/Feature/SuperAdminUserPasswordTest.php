<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminUserPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_update_a_users_password_inline(): void
    {
        $admin = $this->superAdmin();
        $user = User::factory()->create([
            'role' => 'finance',
            'active' => true,
            'password' => 'OldPassword2026',
            'plain_password' => 'OldPassword2026',
        ]);

        $response = $this->actingAs($admin)->put(route('superadmin.users.update', $user), [
            'password_only' => '1',
            'password_user_id' => $user->id,
            'password' => 'NuevaClave2026',
        ]);

        $response
            ->assertRedirect(route('superadmin.users.index'))
            ->assertSessionHas('status', 'Contrasena actualizada.');

        $user->refresh();

        $this->assertSame('NuevaClave2026', $user->plain_password);
        $this->assertTrue(Hash::check('NuevaClave2026', $user->password));
    }

    public function test_users_page_shows_the_password_and_inline_controls(): void
    {
        $admin = $this->superAdmin();

        User::factory()->create([
            'role' => 'finance',
            'active' => true,
            'password' => 'Visible2026',
            'plain_password' => 'Visible2026',
        ]);

        $this->actingAs($admin)
            ->get(route('superadmin.users.index'))
            ->assertOk()
            ->assertSee('Visible2026')
            ->assertSee('data-password-editor', false)
            ->assertSee('data-password-edit', false)
            ->assertSee('Guardar')
            ->assertSee('Cancelar');
    }

    public function test_inline_password_update_requires_at_least_six_characters(): void
    {
        $admin = $this->superAdmin();
        $user = User::factory()->create([
            'role' => 'finance',
            'active' => true,
            'password' => 'OldPassword2026',
            'plain_password' => 'OldPassword2026',
        ]);

        $response = $this->actingAs($admin)
            ->from(route('superadmin.users.index'))
            ->put(route('superadmin.users.update', $user), [
                'password_only' => '1',
                'password_user_id' => $user->id,
                'password' => '123',
            ]);

        $response
            ->assertRedirect(route('superadmin.users.index'))
            ->assertSessionHasErrors('password');

        $user->refresh();

        $this->assertSame('OldPassword2026', $user->plain_password);
        $this->assertTrue(Hash::check('OldPassword2026', $user->password));
    }

    public function test_a_legacy_user_without_a_visible_password_can_receive_one_inline(): void
    {
        $admin = $this->superAdmin();
        $user = User::factory()->create([
            'role' => 'finance',
            'active' => true,
            'password' => 'UnrecoverablePassword2026',
            'plain_password' => null,
        ]);

        $this->actingAs($admin)
            ->get(route('superadmin.users.index'))
            ->assertOk()
            ->assertSee('No disponible')
            ->assertSee('Editar');

        $this->actingAs($admin)->put(route('superadmin.users.update', $user), [
            'password_only' => '1',
            'password_user_id' => $user->id,
            'password' => 'ClaveAsignada2026',
        ])->assertRedirect(route('superadmin.users.index'));

        $user->refresh();

        $this->assertSame('ClaveAsignada2026', $user->plain_password);
        $this->assertTrue(Hash::check('ClaveAsignada2026', $user->password));
    }

    private function superAdmin(): User
    {
        return User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
            'password' => 'AdminPassword2026',
            'plain_password' => 'AdminPassword2026',
        ]);
    }
}
