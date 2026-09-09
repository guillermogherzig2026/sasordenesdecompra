<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminAgentCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_open_the_agent_center_from_the_sidebar(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $this->actingAs($superAdmin)
            ->get(route('superadmin.agents.index'))
            ->assertOk()
            ->assertSee('Centro de agentes')
            ->assertSee('href="'.route('superadmin.agents.index').'"', false)
            ->assertSeeInOrder([
                'Usuarios y Roles',
                'Proveedores',
                'Centro de agentes',
            ]);
    }

    public function test_non_superadmin_cannot_open_the_agent_center(): void
    {
        $financeUser = User::factory()->create([
            'role' => 'finance',
            'active' => true,
        ]);

        $this->actingAs($financeUser)
            ->get(route('superadmin.agents.index'))
            ->assertForbidden();
    }
}
