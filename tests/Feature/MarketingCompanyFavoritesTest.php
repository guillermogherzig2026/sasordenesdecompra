<?php

namespace Tests\Feature;

use App\Models\Marketing\Campaign;
use App\Models\Marketing\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingCompanyFavoritesTest extends TestCase
{
    use RefreshDatabase;

    public function test_carousel_only_contains_favorites_while_catalog_still_selects_any_company(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $favorite = Company::create(['name' => 'Favorita', 'active' => true, 'is_favorite' => true]);
        $other = Company::create(['name' => 'Otra empresa', 'active' => true]);

        $this->actingAs($user)->get(route('marketing.companies.index', ['company_id' => 'all']))
            ->assertOk()
            ->assertSee('data-marketing-favorite-id="'.$favorite->id.'"', false)
            ->assertDontSee('data-marketing-favorite-id="'.$other->id.'"', false)
            ->assertSee('Otra empresa')
            ->assertSee(route('marketing.companies.index', ['company_id' => $other->id]), false)
            ->assertSee('Seleccionar');

        $this->get(route('marketing.companies.index', [
            'company_id' => $other->id,
            'search' => 'Otra',
            'status' => 'active',
        ]))
            ->assertOk()
            ->assertSessionHas('marketing_company_id', $other->id)
            ->assertSee('marketing-company-selected-label', false)
            ->assertSee('data-marketing-favorite-id="'.$favorite->id.'"', false)
            ->assertDontSee('data-marketing-favorite-id="'.$other->id.'"', false);
    }

    public function test_favorite_toggle_updates_carousel_without_changing_active_status(): void
    {
        $user = User::factory()->create([
            'role' => 'buyer', 'active' => true, 'menu_permissions' => ['marketing.companies'],
        ]);
        $company = Company::create(['name' => 'Negocio', 'active' => false]);

        $this->actingAs($user)->patch(route('marketing.companies.favorite', $company), ['favorite' => true])
            ->assertRedirect();
        $this->assertTrue($company->fresh()->is_favorite);
        $this->assertFalse($company->fresh()->active);
        $this->get(route('marketing.companies.index', ['company_id' => 'all']))
            ->assertSee('data-marketing-favorite-id="'.$company->id.'"', false);

        $this->patch(route('marketing.companies.favorite', $company), ['favorite' => false])
            ->assertRedirect();
        $this->assertFalse($company->fresh()->is_favorite);
        $this->get(route('marketing.companies.index', ['company_id' => 'all']))
            ->assertDontSee('data-marketing-favorite-id="'.$company->id.'"', false)
            ->assertSee('Negocio');
    }

    public function test_new_company_can_be_favorited_and_is_selected_after_creation(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);

        $response = $this->actingAs($user)->post(route('marketing.companies.store'), [
            'name' => 'Nueva empresa',
            'active' => '1',
            'is_favorite' => '1',
        ]);

        $company = Company::query()->where('name', 'Nueva empresa')->sole();
        $this->assertTrue($company->is_favorite);
        $response->assertRedirect(route('marketing.companies.index', ['company_id' => $company->id]));
    }

    public function test_marking_favorites_requires_marketing_companies_permission(): void
    {
        $user = User::factory()->create([
            'role' => 'buyer', 'active' => true, 'menu_permissions' => ['marketing.dashboard'],
        ]);
        $company = Company::create(['name' => 'Restringida', 'active' => true]);

        $this->actingAs($user)->patch(route('marketing.companies.favorite', $company), ['favorite' => true])
            ->assertForbidden();
        $this->assertFalse($company->fresh()->is_favorite);
    }

    public function test_the_five_marketing_screens_share_favorites_and_the_full_company_picker(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $favorite = Company::create(['name' => 'Favorita', 'active' => true, 'is_favorite' => true]);
        $other = Company::create(['name' => 'Otra empresa', 'active' => true]);

        $this->actingAs($user);
        foreach (['marketing.index', 'marketing.companies.index', 'marketing.campaigns.index', 'marketing.leads.index', 'marketing.conversations.index'] as $route) {
            $this->get(route($route, ['company_id' => $other->id]))
                ->assertOk()
                ->assertViewHas('company', fn (?Company $company) => $company?->is($other))
                ->assertSee('data-marketing-favorite-id="'.$favorite->id.'"', false)
                ->assertDontSee('data-marketing-favorite-id="'.$other->id.'"', false)
                ->assertSee('id="marketing-company-select"', false)
                ->assertSee('<option value="'.$other->id.'" selected', false);
        }
    }

    public function test_all_companies_scope_survives_menu_navigation_and_includes_all_campaigns(): void
    {
        $user = User::factory()->create(['role' => 'superadmin', 'active' => true]);
        $first = Company::create(['name' => 'Primera', 'active' => true, 'is_favorite' => true]);
        $second = Company::create(['name' => 'Segunda', 'active' => true]);
        Campaign::create(['company_id' => $first->id, 'name' => 'Campaña A', 'status' => 'active']);
        Campaign::create(['company_id' => $second->id, 'name' => 'Campaña B', 'status' => 'active']);

        $this->actingAs($user)->get(route('marketing.companies.index', ['company_id' => $second->id]))
            ->assertSessionHas('marketing_company_id', $second->id);

        $this->get(route('marketing.campaigns.index', ['company_id' => 'all']))
            ->assertOk()
            ->assertViewHas('company', null)
            ->assertViewHas('campaigns', fn ($campaigns) => $campaigns->count() === 2)
            ->assertSessionHas('marketing_company_scope', 'all');

        foreach (['marketing.index', 'marketing.leads.index', 'marketing.conversations.index', 'marketing.companies.index'] as $route) {
            $this->get(route($route))
                ->assertOk()
                ->assertViewHas('company', null)
                ->assertSee('Todas las empresas');
        }

        $this->get(route('marketing.leads.create'))
            ->assertOk()
            ->assertSee('Selecciona una empresa');

        $this->get(route('marketing.index', ['company_id' => $first->id]))
            ->assertSessionMissing('marketing_company_scope')
            ->assertSessionHas('marketing_company_id', $first->id);
    }
}
