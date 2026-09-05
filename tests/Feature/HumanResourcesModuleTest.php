<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HumanResourcesModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_open_the_human_resources_module_and_all_menu_sections(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $sections = [
            'inicio' => 'Inicio',
            'candidatos' => 'Registro de candidatos',
            'empleados' => 'Empleados',
            'contratos' => 'Contratos',
            'aprobaciones' => 'Pendientes de Aprobación',
            'nomina' => 'Nómina',
            'horas-extra' => 'Horas extras',
            'reportes' => 'Reportes',
            'configuracion' => 'Configuración',
            'gerentes' => 'Gerentes y Sucursales',
        ];

        $response = $this->actingAs($user)->get(route('human-resources.show', 'inicio'));

        $response->assertOk();
        $response->assertSee('<!doctype html>', false);
        $response->assertSee('Recursos Humanos');
        $response->assertSee('class="human-resources-frame"', false);
        $response->assertSee('data-human-resources-frame', false);
        $response->assertSee('src="'.route('human-resources.embed', 'inicio').'"', false);

        foreach ($sections as $section => $label) {
            $response->assertSee($label);
            $response->assertSee('href="'.route('human-resources.show', $section).'"', false);
            $this->actingAs($user)->get(route('human-resources.show', $section))->assertOk();
        }
    }

    public function test_embedded_module_starts_on_the_requested_screen_and_loads_its_assets(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('human-resources.embed', 'empleados'));

        $response->assertOk();
        $response->assertSee('class="hr-suite-embedded"', false);
        $response->assertSee('body.hr-suite-embedded .content', false);
        $response->assertSee('"embedded":true', false);
        $response->assertSee('"route":"employees"', false);
        $response->assertSee(asset('hr-suite/assets/css/styles.css'), false);
        $response->assertSee(asset('hr-suite/assets/js/app.js'), false);
        $this->assertFileExists(public_path('hr-suite/assets/js/data.js'));
        $this->assertFileExists(public_path('hr-suite/assets/docs/Contrato por Tiempo Indeterminado.docx'));

        $applicationScript = file_get_contents(public_path('hr-suite/assets/js/app.js'));
        $this->assertStringNotContainsString('data-tooltip="Filtrar ${safe(column.label)}"', $applicationScript);
        $this->assertStringContainsString('aria-label="Filtrar ${safe(column.label)}"', $applicationScript);
        $this->assertStringContainsString('data-action="toggle-vacation-status"', $applicationScript);
        $this->assertStringContainsString('data-action="accept-vacation"', $applicationScript);
        $this->assertStringContainsString('data-action="reject-vacation"', $applicationScript);
        $this->assertStringContainsString('["Aprobada", "Rechazada"].includes(row.status)', $applicationScript);
        $this->assertStringNotContainsString('columns.push({ key: "actions", label: "Acciones", sortable: false, render: (row) => `<button class="icon-btn" data-action="approve-vacation"', $applicationScript);
        $this->assertStringContainsString('const employeesById = new Map(state.data.employees.map((employee) => [Number(employee.id), employee]))', $applicationScript);
        $this->assertStringContainsString('class="vacation-event-person"', $applicationScript);
        $this->assertStringContainsString('class="vacation-event-context"', $applicationScript);
        $this->assertStringContainsString('<span>${safe(event.branch)}</span>', $applicationScript);
        $this->assertStringContainsString('<span>${safe(event.company)}</span>', $applicationScript);
        $this->assertStringContainsString('data-action="open-candidate-form"', $applicationScript);
        $this->assertStringContainsString('class="card candidate-inline-form"', $applicationScript);
        $this->assertStringContainsString('data-action="close-candidate-form"', $applicationScript);
        $this->assertStringContainsString('state.ui.candidateFormOpen = false', $applicationScript);
        $this->assertStringNotContainsString('modal.type === "candidate"', $applicationScript);
        $this->assertStringContainsString('Gestionar Vacaciones <span class="selector-count">', $applicationScript);
        $this->assertStringNotContainsString('Vacaciones pendientes <span class="selector-count">', $applicationScript);
        $this->assertMatchesRegularExpression('/data-action="portal-vacation"[^>]*>.*?Nueva solicitud<\/button>\s*<button[^>]*data-route="vacations"[^>]*>.*?Gestionar Vacaciones/s', $applicationScript);
        $this->assertStringContainsString('id="employee-form" class="form-card employee-form-compact"', $applicationScript);
        $this->assertStringContainsString('return `EMP-${String(highest + 1).padStart(5, "0")}`;', $applicationScript);
        $this->assertStringContainsString('pattern="EMP-[0-9]{5}" maxlength="9"', $applicationScript);
        $this->assertStringContainsString('if (!/^EMP-\d{5}$/.test(employeeNumber))', $applicationScript);
        $this->assertStringContainsString('contractPersonType: "candidate"', $applicationScript);
        $this->assertStringContainsString('data-action="set-contract-person-type"', $applicationScript);
        $this->assertStringContainsString('data-action="draft-candidate"', $applicationScript);
        $this->assertStringContainsString('contractCandidateEmployees', $applicationScript);
        $this->assertStringContainsString('function candidateIsInPostInterview(candidate)', $applicationScript);
        $this->assertStringContainsString('function candidateIsEligibleForContract(candidate)', $applicationScript);
        $this->assertStringContainsString('.filter(candidateIsEligibleForContract)', $applicationScript);
        $this->assertStringContainsString('No hay candidatos con las 7 rondas concluidas en Post entrevista y negociación.', $applicationScript);
        $this->assertStringContainsString('class="actions contract-wizard-actions"', $applicationScript);
        $this->assertStringContainsString('${icon("arrow-left", "btn-icon")}Anterior', $applicationScript);
        $this->assertStringContainsString('function moveCandidateToEmployees(candidateId, employee, contract)', $applicationScript);
        $this->assertStringContainsString('state.data.employees.unshift(employee);', $applicationScript);
        $this->assertStringContainsString('state.data.candidates = (state.data.candidates || [])', $applicationScript);
        $this->assertStringContainsString('moveCandidateToEmployees(contract.sourceCandidateId || employee.sourceCandidateId, employee, contract);', $applicationScript);
        $this->assertStringContainsString('function normalizeConvertedCandidates(data)', $applicationScript);
        $this->assertStringContainsString('normalizeConvertedCandidates(data);', $applicationScript);
        $this->assertStringContainsString('const interviewRounds = [', $applicationScript);
        $this->assertStringContainsString('function normalizeCandidateInterviewRounds(data)', $applicationScript);
        $this->assertStringContainsString('function normalizeInterviewRoundCatalog(data)', $applicationScript);
        $this->assertStringContainsString('label: "Ronda"', $applicationScript);
        $this->assertStringContainsString('interviewRoundCell(row)', $applicationScript);
        $this->assertStringContainsString('label: "Próxima entrevista"', $applicationScript);
        $this->assertStringContainsString('nextInterviewCell(row)', $applicationScript);
        $this->assertStringContainsString('Ronda ${nextRound} de ${rounds.length}', $applicationScript);
        $this->assertStringContainsString('data-action="open-interview-rounds"', $applicationScript);
        $this->assertStringContainsString('showToolbar = true', $applicationScript);
        $this->assertSame(3, substr_count($applicationScript, 'showToolbar: false'));
        $this->assertStringContainsString('modal.type === "interview-rounds"', $applicationScript);
        $this->assertStringContainsString('data-action="edit-interview-round"', $applicationScript);
        $this->assertStringContainsString('id="interview-round-form"', $applicationScript);
        $this->assertStringContainsString('Entrevista con Ing. Franco Guerrero', $applicationScript);
        $this->assertStringContainsString('Estudio Socioeconómico', $applicationScript);
        $this->assertStringNotContainsString('<h3>Contratos con empleados</h3>', $applicationScript);

        $styles = file_get_contents(public_path('hr-suite/assets/css/styles.css'));
        $this->assertStringContainsString('.candidate-inline-form', $styles);
        $this->assertStringContainsString('.employee-form-compact .form-grid', $styles);
        $this->assertStringContainsString('grid-template-columns: repeat(4, minmax(180px, 1fr));', $styles);
        $this->assertStringContainsString('.contract-person-carousel', $styles);
        $this->assertStringContainsString('.contract-person-option', $styles);
        $this->assertStringContainsString('.contract-wizard-actions', $styles);
        $this->assertStringContainsString('.interview-round-progress', $styles);
        $this->assertStringContainsString('.next-interview-cell', $styles);
        $this->assertStringContainsString('.interview-rounds-modal', $styles);
        $this->assertStringContainsString('.interview-round-item', $styles);
    }

    public function test_candidate_form_uses_the_registered_company_catalog(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        Company::create([
            'name' => 'Empresa Zeta',
            'rfc' => 'EZT260820AA1',
        ]);
        Company::create([
            'name' => 'Empresa Alfa',
            'rfc' => 'EAL260820AA2',
        ]);

        $response = $this->actingAs($user)->get(route('human-resources.embed', 'candidatos'));

        $response->assertOk();
        $response->assertSee('"registeredCompanies":["Empresa Alfa","Empresa Zeta"]', false);

        $applicationScript = file_get_contents(public_path('hr-suite/assets/js/app.js'));
        $this->assertStringContainsString('suiteConfig.registeredCompanies', $applicationScript);
        $this->assertStringContainsString('registeredCandidateCompanies(candidate?.company)', $applicationScript);
        $this->assertStringContainsString('const companyOptions = registeredCandidateCompanies(candidate?.company);', $applicationScript);
    }

    public function test_submenu_keeps_the_general_sidebar_open_and_loads_the_selected_screen_on_the_right(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $response = $this->actingAs($user)->get(route('human-resources.show', 'empleados'));

        $response->assertOk();
        $response->assertSeeInOrder([
            '<aside class="sidebar">',
            '<details class="nav-group" open>',
            '<summary>Recursos Humanos</summary>',
            'class="button nav-button sub-nav-button active" href="'.route('human-resources.show', 'empleados').'"',
            '</aside>',
            '<main class="content-shell">',
            'src="'.route('human-resources.embed', 'empleados').'"',
        ], false);
    }

    public function test_human_resources_module_requires_authentication_and_superadmin_access(): void
    {
        $this->get(route('human-resources.show', 'inicio'))
            ->assertRedirect(route('login'));

        $user = User::factory()->create([
            'role' => 'buyer',
            'active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('human-resources.show', 'inicio'))
            ->assertForbidden();
    }

    public function test_unknown_human_resources_section_returns_not_found(): void
    {
        $user = User::factory()->create([
            'role' => 'superadmin',
            'active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('human-resources.show', 'inexistente'))
            ->assertNotFound();
    }
}
