<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminUsernameTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_a_user_with_a_login_username(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin', 'active' => true]);

        $this->actingAs($admin)->post(route('superadmin.users.store'), [
            'name' => 'Carlo Her',
            'username' => 'carloher',
            'email' => 'carloher@example.test',
            'password' => 'UniquePassword2026',
            'role' => 'superadmin',
        ])->assertRedirect(route('superadmin.users.index'));

        $user = User::where('username', 'carloher')->firstOrFail();

        $this->assertSame('superadmin', $user->role);
        $this->assertTrue(Hash::check('UniquePassword2026', $user->password));
        Auth::logout();
        $this->assertTrue(Auth::attempt([
            'username' => 'carloher',
            'password' => 'UniquePassword2026',
            'active' => true,
        ]));
    }

    public function test_super_admin_cannot_reuse_an_existing_login_username(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        User::factory()->create(['username' => 'carloher']);

        $this->actingAs($admin)->post(route('superadmin.users.store'), [
            'name' => 'Another Carlo',
            'username' => 'carloher',
            'email' => 'another-carlo@example.test',
            'password' => 'UniquePassword2026',
            'role' => 'superadmin',
        ])->assertSessionHasErrors('username');

        $this->assertDatabaseMissing('users', ['email' => 'another-carlo@example.test']);
    }
}
