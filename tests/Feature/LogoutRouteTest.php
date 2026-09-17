<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class LogoutRouteTest extends TestCase
{
    public function test_guest_visiting_logout_redirects_to_login(): void
    {
        $this->get('/logout')
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_visiting_logout_redirects_to_dashboard_without_logging_out(): void
    {
        $user = new User([
            'name' => 'Super Administrador',
            'email' => 'admin@example.com',
            'role' => 'superadmin',
            'active' => true,
        ]);
        $user->id = 1;
        $user->exists = true;

        $this->actingAs($user)
            ->get('/logout')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }
}
