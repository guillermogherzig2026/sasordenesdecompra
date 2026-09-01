<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminGovernmentContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_open_government_contracts_from_the_menu(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('superadmin.government-contracts.index'));

        $response->assertOk();
        $response->assertSee('Contratos Gobierno');
        $response->assertSee('<summary>Contratos Gobierno</summary>', false);
        $response->assertSee('href="'.route('superadmin.government-contracts.index').'"', false);
        $response->assertSee('src="'.route('superadmin.government-contracts.panel').'"', false);

        foreach (\App\Support\GovernmentContractNavigation::items() as $section => $item) {
            $response->assertSee($item['label']);
            if ($section !== \App\Support\GovernmentContractNavigation::defaultSection()) {
                $response->assertSee(
                    'href="'.route('superadmin.government-contracts.index', ['section' => $section]).'"',
                    false,
                );
            }
        }

        $panelResponse = $this->actingAs($user)->get(route('superadmin.government-contracts.panel'));

        $panelResponse->assertOk();
        $panelResponse->assertSee('id="root"', false);
        $panelResponse->assertSee('government-contracts-parent-navigation');
        $panelResponse->assertSee('data-selected-module="Contratos"', false);
        $panelResponse->assertSee('modules/contratos-gobierno/app.js', false);
        $panelResponse->assertSee('modules/contratos-gobierno/contract-carousel.css', false);
        $panelResponse->assertSee('modules/contratos-gobierno/contract-carousel.js', false);
    }

    public function test_government_contract_submenu_opens_the_selected_embedded_module(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);
        $section = 'supply-billing';

        $response = $this->actingAs($user)->get(route('superadmin.government-contracts.index', [
            'section' => $section,
        ]));

        $response->assertOk();
        $response->assertSee(
            'src="'.route('superadmin.government-contracts.panel', ['section' => $section]).'"',
            false,
        );

        $this->actingAs($user)
            ->get(route('superadmin.government-contracts.panel', ['section' => $section]))
            ->assertOk()
            ->assertSee('data-selected-module="OS Facturacion"', false);
    }

    public function test_user_can_only_open_the_authorized_government_contract_submenu(): void
    {
        $user = User::factory()->create([
            'role' => 'administrative_assistant',
            'active' => true,
            'menu_permissions' => ['government_contracts.supply_billing'],
        ]);

        $response = $this->actingAs($user)->get(route('superadmin.government-contracts.index', [
            'section' => 'supply-billing',
        ]));

        $response->assertOk();
        $response->assertSee('<summary>Contratos Gobierno</summary>', false);
        $response->assertSee('OS Facturacion');
        $response->assertDontSee(
            'href="'.route('superadmin.government-contracts.index', ['section' => 'supply-pending']).'"',
            false,
        );

        $this->actingAs($user)
            ->get(route('superadmin.government-contracts.index'))
            ->assertForbidden();
    }

    public function test_government_contracts_rejects_non_superadmin_users(): void
    {
        $user = User::factory()->create([
            'role' => 'finance',
            'active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('superadmin.government-contracts.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('superadmin.government-contracts.panel'))
            ->assertForbidden();
    }

    public function test_government_contracts_requires_authentication(): void
    {
        $this->get(route('superadmin.government-contracts.index'))
            ->assertRedirect(route('login'));
    }
}
