<?php

namespace Tests\Feature;

use App\Models\AiAgent;
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
            ->assertSee('Crear nuevo')
            ->assertSee('Todos')
            ->assertSee('href="'.route('superadmin.agents.index').'"', false)
            ->assertSeeInOrder([
                'Usuarios y Roles',
                'Proveedores',
                'Centro de agentes',
            ]);
    }

    public function test_superadmin_can_open_the_agent_center_from_finance(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $financeAgentCenterUrl = route('finance.agents.index');

        $this->actingAs($superAdmin)
            ->get($financeAgentCenterUrl)
            ->assertOk()
            ->assertSee('Centro de agentes')
            ->assertSee('href="'.$financeAgentCenterUrl.'"', false)
            ->assertSeeInOrder([
                'OR Historial',
                'Proveedores',
                'Empresas y Negocios',
                'Servicios',
                'Autorizaciones',
                'Centro de agentes',
            ]);
    }

    public function test_superadmin_can_open_the_agent_center_from_plaza_administration(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $plazaAgentCenterUrl = route('superadmin.plazas.agents.index');

        $this->actingAs($superAdmin)
            ->get($plazaAgentCenterUrl)
            ->assertOk()
            ->assertSee('Centro de agentes')
            ->assertSee('href="'.$plazaAgentCenterUrl.'"', false)
            ->assertSeeInOrder([
                'Marketing local',
                'Centro de agentes',
                'Alta de Usuarios',
            ]);
    }

    public function test_superadmin_can_create_update_and_toggle_an_agent_from_each_center(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $createResponse = $this->actingAs($superAdmin)->post(route('superadmin.agents.store'), [
            'context' => 'finanzas',
            'agent_form' => '1',
            'name' => 'Control de pagos',
            'description' => 'Revisa pagos pendientes.',
            'instructions' => 'Prioriza documentos con fecha limite cercana.',
        ]);

        $agent = AiAgent::query()->firstOrFail();

        $createResponse->assertRedirect(route('finance.agents.index', ['selected' => $agent->id]));
        $this->assertDatabaseHas('ai_agents', [
            'id' => $agent->id,
            'name' => 'Control de pagos',
            'is_active' => false,
            'created_by' => $superAdmin->id,
        ]);

        $this->actingAs($superAdmin)
            ->put(route('superadmin.agents.update', $agent), [
                'context' => 'plazas',
                'agent_form' => '1',
                'editing_agent_id' => $agent->id,
                'name' => 'Control de cobranza',
                'description' => 'Revisa la cobranza de las plazas.',
                'instructions' => 'Separa saldos vigentes y vencidos.',
            ])
            ->assertRedirect(route('superadmin.plazas.agents.index', ['selected' => $agent->id]));

        $this->assertDatabaseHas('ai_agents', [
            'id' => $agent->id,
            'name' => 'Control de cobranza',
            'description' => 'Revisa la cobranza de las plazas.',
        ]);

        $this->actingAs($superAdmin)
            ->patch(route('superadmin.agents.toggle', $agent), [
                'context' => 'superadmin',
                'selected' => (string) $agent->id,
                'is_active' => '1',
            ])
            ->assertRedirect(route('superadmin.agents.index', ['selected' => $agent->id]));

        $this->assertTrue($agent->fresh()->is_active);
    }

    public function test_agent_names_must_be_unique(): void
    {
        $superAdmin = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        AiAgent::create(['name' => 'Agente existente']);

        $this->actingAs($superAdmin)
            ->from(route('superadmin.agents.index'))
            ->post(route('superadmin.agents.store'), [
                'context' => 'superadmin',
                'agent_form' => '1',
                'name' => 'Agente existente',
            ])
            ->assertRedirect(route('superadmin.agents.index'))
            ->assertSessionHasErrors('name');

        $this->assertSame(1, AiAgent::query()->count());
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

        $this->actingAs($financeUser)
            ->get(route('finance.agents.index'))
            ->assertForbidden();

        $this->actingAs($financeUser)
            ->get(route('superadmin.plazas.agents.index'))
            ->assertForbidden();
    }
}
