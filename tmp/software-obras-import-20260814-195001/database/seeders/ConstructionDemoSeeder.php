<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use App\Models\ChangeOrder;
use App\Models\Client;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Crew;
use App\Models\DailyLog;
use App\Models\Document;
use App\Models\Employee;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\EstimatePayment;
use App\Models\Incident;
use App\Models\Invoice;
use App\Models\MaterialCatalog;
use App\Models\MaterialRequest;
use App\Models\MaterialRequestItem;
use App\Models\Payment;
use App\Models\Payroll;
use App\Models\PayrollItem;
use App\Models\Permission;
use App\Models\Photo;
use App\Models\ProgressRecord;
use App\Models\Project;
use App\Models\ProjectEvent;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Retention;
use App\Models\Role;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\SupplierEvaluation;
use App\Models\SupplyOrder;
use App\Models\SupplyOrderItem;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WeeklyScope;
use App\Models\WorkItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ConstructionDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $roles = $this->createRolesAndPermissions();
            [$company, $clients, $users] = $this->createCoreCatalogs($roles);
            $projects = $this->createProjects($company, $clients, $users);

            foreach ($projects as $project) {
                $this->assignProjectUsers($project, $users);
                $this->createBudgetCatalog($project, $users);
                $this->createFollowUpRecords($project, $users);
            }

            $this->createPriceContractData($projects['los_pinos'], $users);
            $this->createPriceContractData($projects['bodega'], $users, 3);
            $this->createAdministrationData($projects['oficinas'], $users);
            $this->createAdministrationData($projects['plaza'], $users, 25);
            $this->createMaterialsAndPurchases($projects, $company, $users);
        });
    }

    private function createRolesAndPermissions(): array
    {
        $roleData = [
            ['Superadministrador', 'superadministrador'],
            ['Administrador de obra', 'administrador-obra'],
            ['Residente de obra', 'residente-obra'],
            ['Supervisor', 'supervisor'],
            ['Almacen', 'almacen'],
            ['Compras', 'compras'],
            ['Nomina', 'nomina'],
            ['Contabilidad', 'contabilidad'],
            ['Consulta', 'consulta'],
        ];

        $roles = collect($roleData)->mapWithKeys(function (array $role): array {
            return [$role[1] => Role::firstOrCreate(
                ['slug' => $role[1]],
                ['name' => $role[0], 'description' => "Rol {$role[0]}"]
            )];
        });

        $permissions = collect([
            ['Obras', 'Ver obras', 'projects.view'],
            ['Obras', 'Crear obras', 'projects.create'],
            ['Obras', 'Editar obras', 'projects.edit'],
            ['Contratos', 'Ver contratos', 'contracts.view'],
            ['Presupuestos', 'Gestionar presupuestos', 'budgets.manage'],
            ['Materiales', 'Gestionar materiales', 'materials.manage'],
            ['Nomina', 'Gestionar nomina', 'payroll.manage'],
            ['Compras', 'Gestionar compras', 'purchases.manage'],
            ['Pagos', 'Gestionar pagos', 'payments.manage'],
            ['Reportes', 'Ver reportes', 'reports.view'],
            ['Usuarios', 'Gestionar usuarios', 'users.manage'],
            ['Bitacora', 'Ver bitacora', 'audit.view'],
        ])->mapWithKeys(function (array $permission): array {
            return [$permission[2] => Permission::firstOrCreate(
                ['slug' => $permission[2]],
                ['module' => $permission[0], 'name' => $permission[1]]
            )];
        });

        $roles['superadministrador']->permissions()->sync($permissions->pluck('id'));
        $roles['administrador-obra']->permissions()->sync($permissions->except(['users.manage'])->pluck('id'));
        $roles['residente-obra']->permissions()->sync($permissions->only(['projects.view', 'projects.edit', 'budgets.manage', 'materials.manage', 'reports.view'])->pluck('id'));
        $roles['supervisor']->permissions()->sync($permissions->only(['projects.view', 'reports.view', 'audit.view'])->pluck('id'));
        $roles['almacen']->permissions()->sync($permissions->only(['projects.view', 'materials.manage'])->pluck('id'));
        $roles['compras']->permissions()->sync($permissions->only(['projects.view', 'materials.manage', 'purchases.manage'])->pluck('id'));
        $roles['nomina']->permissions()->sync($permissions->only(['projects.view', 'payroll.manage'])->pluck('id'));
        $roles['contabilidad']->permissions()->sync($permissions->only(['projects.view', 'payments.manage', 'reports.view'])->pluck('id'));
        $roles['consulta']->permissions()->sync($permissions->only(['projects.view', 'reports.view'])->pluck('id'));

        return $roles->all();
    }

    private function createCoreCatalogs(array $roles): array
    {
        $company = Company::firstOrCreate(
            ['rfc' => 'CDM260101AB1'],
            [
                'name' => 'Constructora Demo del Centro',
                'phone' => '55 1000 2400',
                'email' => 'contacto@constructorademo.local',
                'address' => 'Av. Reforma 120, Ciudad de Mexico',
            ]
        );

        $clients = [
            'romero' => Client::firstOrCreate(['name' => 'Familia Romero'], ['company_id' => $company->id, 'contact_name' => 'Laura Romero', 'phone' => '55 2345 1234']),
            'axia' => Client::firstOrCreate(['name' => 'Inmobiliaria Axia'], ['company_id' => $company->id, 'contact_name' => 'Mario Lopez', 'phone' => '55 8000 2190']),
            'bahia' => Client::firstOrCreate(['name' => 'Logistica Bahia'], ['company_id' => $company->id, 'contact_name' => 'Carlos Ruiz', 'phone' => '81 2200 7788']),
            'sur' => Client::firstOrCreate(['name' => 'Grupo Comercial Sur'], ['company_id' => $company->id, 'contact_name' => 'Patricia Vega', 'phone' => '55 6500 9010']),
        ];

        $password = Hash::make('password');
        $users = [
            'super' => User::firstOrCreate(['email' => 'super@obras.local'], ['company_id' => $company->id, 'role_id' => $roles['superadministrador']->id, 'name' => 'Sofia Administradora', 'password' => $password, 'position' => 'Direccion']),
            'admin' => User::firstOrCreate(['email' => 'admin@obras.local'], ['company_id' => $company->id, 'role_id' => $roles['administrador-obra']->id, 'name' => 'Adrian Torres', 'password' => $password, 'position' => 'Administrador de obra']),
            'residente' => User::firstOrCreate(['email' => 'residente@obras.local'], ['company_id' => $company->id, 'role_id' => $roles['residente-obra']->id, 'name' => 'Valeria Medina', 'password' => $password, 'position' => 'Residente']),
            'supervisor' => User::firstOrCreate(['email' => 'supervisor@obras.local'], ['company_id' => $company->id, 'role_id' => $roles['supervisor']->id, 'name' => 'Hector Salas', 'password' => $password, 'position' => 'Supervisor']),
            'almacen' => User::firstOrCreate(['email' => 'almacen@obras.local'], ['company_id' => $company->id, 'role_id' => $roles['almacen']->id, 'name' => 'Rosa Montiel', 'password' => $password, 'position' => 'Almacen']),
            'compras' => User::firstOrCreate(['email' => 'compras@obras.local'], ['company_id' => $company->id, 'role_id' => $roles['compras']->id, 'name' => 'Omar Beltran', 'password' => $password, 'position' => 'Compras']),
            'nomina' => User::firstOrCreate(['email' => 'nomina@obras.local'], ['company_id' => $company->id, 'role_id' => $roles['nomina']->id, 'name' => 'Daniela Prado', 'password' => $password, 'position' => 'Nomina']),
            'contabilidad' => User::firstOrCreate(['email' => 'contabilidad@obras.local'], ['company_id' => $company->id, 'role_id' => $roles['contabilidad']->id, 'name' => 'Elena Cortes', 'password' => $password, 'position' => 'Contabilidad']),
            'consulta' => User::firstOrCreate(['email' => 'consulta@obras.local'], ['company_id' => $company->id, 'role_id' => $roles['consulta']->id, 'name' => 'Invitado Consulta', 'password' => $password, 'position' => 'Consulta']),
        ];

        return [$company, $clients, $users];
    }

    private function createProjects(Company $company, array $clients, array $users): array
    {
        $projectRows = [
            'los_pinos' => [
                'project_key' => 'OBR-001',
                'name' => 'Residencial Los Pinos',
                'client_id' => $clients['romero']->id,
                'location' => 'Cuernavaca, Morelos',
                'project_type' => 'Casa habitacion',
                'modality' => 'Precio alzado',
                'responsible_user_id' => $users['residente']->id,
                'start_date' => '2026-01-06',
                'estimated_end_date' => '2026-11-30',
                'status' => 'En Proceso',
                'physical_progress' => 65.4,
                'financial_progress' => 58.3,
                'contracted_value' => 24500000,
                'estimated_amount' => 16005250,
                'paid_amount' => 14200000,
                'retention_amount' => 780263,
                'photo_path' => '/images/projects/residencial-los-pinos.png',
            ],
            'oficinas' => [
                'project_key' => 'OBR-002',
                'name' => 'Oficinas Corporativas',
                'client_id' => $clients['axia']->id,
                'location' => 'Monterrey, Nuevo Leon',
                'project_type' => 'Edificio de oficinas',
                'modality' => 'Administracion',
                'responsible_user_id' => $users['admin']->id,
                'start_date' => '2026-02-03',
                'estimated_end_date' => '2026-10-16',
                'status' => 'En Proceso',
                'physical_progress' => 40.0,
                'financial_progress' => 36.7,
                'contracted_value' => 17800000,
                'estimated_amount' => 7200000,
                'paid_amount' => 6535000,
                'retention_amount' => 0,
                'photo_path' => '/images/projects/oficinas-corporativas.png',
            ],
            'bodega' => [
                'project_key' => 'OBR-003',
                'name' => 'Bodega Industrial',
                'client_id' => $clients['bahia']->id,
                'location' => 'Apodaca, Nuevo Leon',
                'project_type' => 'Nave industrial',
                'modality' => 'Precio alzado',
                'responsible_user_id' => $users['supervisor']->id,
                'start_date' => '2026-03-10',
                'estimated_end_date' => '2026-09-20',
                'status' => 'En Proceso',
                'physical_progress' => 90.0,
                'financial_progress' => 82.5,
                'contracted_value' => 39500000,
                'estimated_amount' => 35550000,
                'paid_amount' => 32587500,
                'retention_amount' => 1777500,
                'photo_path' => '/images/projects/bodega-industrial.png',
            ],
            'plaza' => [
                'project_key' => 'OBR-004',
                'name' => 'Plaza Comercial Sur',
                'client_id' => $clients['sur']->id,
                'location' => 'Tlalpan, Ciudad de Mexico',
                'project_type' => 'Centro comercial',
                'modality' => 'Administracion',
                'responsible_user_id' => $users['residente']->id,
                'start_date' => '2026-04-01',
                'estimated_end_date' => '2027-02-15',
                'status' => 'En Proceso',
                'physical_progress' => 25.0,
                'financial_progress' => 21.0,
                'contracted_value' => 51200000,
                'estimated_amount' => 12800000,
                'paid_amount' => 10752000,
                'retention_amount' => 0,
                'photo_path' => '/images/projects/plaza-comercial-sur.png',
            ],
        ];

        return collect($projectRows)->mapWithKeys(function (array $row, string $key) use ($company): array {
            $project = Project::updateOrCreate(
                ['project_key' => $row['project_key']],
                $row + ['company_id' => $company->id]
            );

            Contract::updateOrCreate(
                ['contract_number' => 'CT-'.$row['project_key'].'-2026'],
                [
                    'project_id' => $project->id,
                    'signed_at' => Carbon::parse($row['start_date'])->subDays(12),
                    'start_date' => $row['start_date'],
                    'end_date' => $row['estimated_end_date'],
                    'total_value' => $row['contracted_value'],
                    'retention_percentage' => $row['modality'] === 'Precio alzado' ? 5 : 0,
                    'status' => 'Vigente',
                ]
            );

            return [$key => $project];
        })->all();
    }

    private function assignProjectUsers(Project $project, array $users): void
    {
        $project->users()->syncWithoutDetaching([
            $users['admin']->id => ['can_view' => true, 'can_edit' => true],
            $users['residente']->id => ['can_view' => true, 'can_edit' => true],
            $users['supervisor']->id => ['can_view' => true, 'can_edit' => false],
            $users['almacen']->id => ['can_view' => true, 'can_edit' => false],
            $users['compras']->id => ['can_view' => true, 'can_edit' => false],
            $users['nomina']->id => ['can_view' => true, 'can_edit' => false],
            $users['contabilidad']->id => ['can_view' => true, 'can_edit' => false],
            $users['consulta']->id => ['can_view' => true, 'can_edit' => false],
        ]);
    }

    private function createBudgetCatalog(Project $project, array $users): void
    {
        $budget = Budget::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'Presupuesto base'],
            ['version' => '1.0', 'planned_total' => $project->contracted_value, 'approved_at' => $project->start_date, 'status' => 'Activo']
        );

        $categories = [
            ['01', 'Preliminares', 7],
            ['02', 'Cimentacion', 13],
            ['03', 'Estructura', 26],
            ['04', 'Albanileria', 16],
            ['05', 'Instalaciones', 14],
            ['06', 'Acabados', 16],
            ['07', 'Exteriores', 5],
            ['08', 'Equipamiento', 3],
        ];

        foreach ($categories as [$code, $name, $weight]) {
            $category = Category::updateOrCreate(
                ['project_id' => $project->id, 'code' => $code],
                ['budget_id' => $budget->id, 'name' => $name, 'level' => 'Categoria', 'sort_order' => (int) $code, 'status' => 'Activo']
            );

            $amount = round(((float) $project->contracted_value * $weight) / 100, 2);
            $realProgress = max(min((float) $project->physical_progress + (($weight % 3) * 4) - 6, 100), 0);
            $scheduledProgress = max(min($realProgress + (($weight % 2) ? 3 : -2), 100), 0);

            WorkItem::updateOrCreate(
                ['project_id' => $project->id, 'code' => $code.'.01'],
                [
                    'budget_id' => $budget->id,
                    'category_id' => $category->id,
                    'responsible_user_id' => $project->responsible_user_id,
                    'description' => $name.' - paquete principal',
                    'unit' => 'gbl',
                    'contracted_quantity' => 100,
                    'unit_price' => $amount / 100,
                    'amount' => $amount,
                    'executed_quantity' => $realProgress,
                    'progress_percent' => $realProgress,
                    'scheduled_percent' => $scheduledProgress,
                    'estimated_amount' => round($amount * ($realProgress / 100), 2),
                    'paid_amount' => round($amount * ((float) $project->financial_progress / 100), 2),
                    'estimated_end_date' => Carbon::parse($project->estimated_end_date)->subDays(8 - (int) $code),
                    'status' => $realProgress >= 100 ? 'Completa' : ($realProgress > 0 ? 'En proceso' : 'Sin iniciar'),
                ]
            );
        }
    }

    private function createPriceContractData(Project $project, array $users, int $estimateCount = 6): void
    {
        $workItems = $project->workItems()->take(4)->get();
        $periodStart = Carbon::parse($project->start_date)->addWeeks(14);

        for ($index = 1; $index <= $estimateCount; $index++) {
            $gross = round(((float) $project->contracted_value * (6 + ($index % 2))) / 100, 2);
            $retention = round($gross * 0.05, 2);
            $net = $gross - $retention;
            $paid = $index <= 2 ? $net : ($index === 3 ? round($net * 0.72, 2) : 0);

            $estimate = Estimate::updateOrCreate(
                ['project_id' => $project->id, 'estimate_number' => $index],
                [
                    'period_start' => $periodStart->copy()->addWeeks($index - 1),
                    'period_end' => $periodStart->copy()->addWeeks($index - 1)->addDays(6),
                    'cutoff_date' => $periodStart->copy()->addWeeks($index - 1)->addDays(6),
                    'prepared_at' => $periodStart->copy()->addWeeks($index - 1)->addDays(7),
                    'authorized_at' => $index <= 3 ? $periodStart->copy()->addWeeks($index - 1)->addDays(8) : null,
                    'scheduled_payment_date' => $periodStart->copy()->addWeeks($index - 1)->addDays(15),
                    'previous_progress' => max(($index - 1) * 6.5, 0),
                    'period_progress' => 6.5,
                    'cumulative_progress' => $index * 6.5,
                    'gross_amount' => $gross,
                    'retention' => $retention,
                    'net_amount' => $net,
                    'paid_amount' => $paid,
                    'balance' => $net - $paid,
                    'status' => $paid >= $net ? 'Pagada' : ($paid > 0 ? 'Pago parcial' : ($index <= 3 ? 'Autorizada' : 'Pendiente')),
                    'attachments' => ['generador' => 'Generador estimacion '.$index],
                ]
            );

            foreach ($workItems as $itemIndex => $workItem) {
                EstimateItem::updateOrCreate(
                    ['estimate_id' => $estimate->id, 'work_item_id' => $workItem->id],
                    [
                        'previous_quantity' => max(($index - 1) * 8, 0),
                        'period_quantity' => 8 + $itemIndex,
                        'cumulative_quantity' => ($index * 8) + $itemIndex,
                        'programmed_percent' => min($index * 9, 100),
                        'actual_percent' => min(($index * 8) + $itemIndex, 100),
                        'amount' => round($gross / max($workItems->count(), 1), 2),
                        'real_amount' => round(($gross / max($workItems->count(), 1)) * 0.92, 2),
                        'status' => $index <= 2 ? 'Completada' : 'En proceso',
                    ]
                );
            }

            if ($paid > 0) {
                EstimatePayment::updateOrCreate(
                    ['estimate_id' => $estimate->id, 'reference' => 'TR-'.$project->project_key.'-'.$index],
                    ['paid_at' => $estimate->scheduled_payment_date, 'amount' => $paid, 'payment_method' => 'Transferencia', 'status' => 'Aplicado']
                );
            }

            Retention::updateOrCreate(
                ['project_id' => $project->id, 'estimate_id' => $estimate->id],
                [
                    'percentage' => 5,
                    'amount' => $retention,
                    'released_amount' => $index <= 2 ? round($retention * 0.8, 2) : 0,
                    'released_at' => $index <= 2 ? $estimate->scheduled_payment_date?->copy()->addWeeks(3) : null,
                    'document_path' => $index <= 2 ? 'Acta de liberacion '.$index.'.pdf' : null,
                    'status' => $index <= 2 ? 'Liberado' : 'Por liberar',
                ]
            );
        }
    }

    private function createAdministrationData(Project $project, array $users, int $week = 24): void
    {
        $crew = Crew::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'Cuadrilla base '.$project->project_key],
            ['leader_user_id' => $users['residente']->id, 'specialty' => 'Obra civil', 'status' => 'Activa']
        );

        $categories = ['Albaniles', 'Ayudantes', 'Fierreros', 'Carpinteros', 'Oficiales', 'Otros'];
        foreach ($categories as $index => $category) {
            Employee::updateOrCreate(
                ['project_id' => $project->id, 'name' => $category.' '.$project->project_key],
                [
                    'crew_id' => $crew->id,
                    'position' => $category,
                    'specialty' => $category,
                    'daily_salary' => 420 + ($index * 80),
                    'weekly_payment' => (420 + ($index * 80)) * 6,
                    'hiring_type' => 'Semanal',
                    'bank_account' => '000'.$index.'-'.$project->project_key,
                    'clabe' => str_pad((string) (100000000000000000 + $index), 18, '0', STR_PAD_LEFT),
                    'status' => 'Activo',
                ]
            );
        }

        $payroll = Payroll::updateOrCreate(
            ['project_id' => $project->id, 'week_number' => $week],
            [
                'period_start' => Carbon::now()->startOfWeek()->subWeeks(1),
                'period_end' => Carbon::now()->startOfWeek()->subWeeks(1)->addDays(6),
                'gross_amount' => 245800,
                'deductions' => 0,
                'net_amount' => 245800,
                'paid_at' => Carbon::now()->startOfWeek()->addDays(1),
                'status' => 'Pagada',
            ]
        );

        foreach ($crew->employees()->get() as $employee) {
            PayrollItem::updateOrCreate(
                ['payroll_id' => $payroll->id, 'employee_id' => $employee->id],
                [
                    'worker_category' => $employee->position,
                    'days_worked' => 6,
                    'normal_hours' => 48,
                    'extra_hours' => 4,
                    'jornales' => 6,
                    'gross_amount' => $employee->weekly_payment,
                    'deductions' => 0,
                    'net_amount' => $employee->weekly_payment,
                    'status' => 'Pagada',
                ]
            );
        }

        $workItem = $project->workItems()->first();
        WeeklyScope::updateOrCreate(
            ['project_id' => $project->id, 'week_number' => $week, 'year' => 2026, 'activity' => 'Frentes prioritarios de obra civil'],
            [
                'work_item_id' => $workItem?->id,
                'responsible_user_id' => $project->responsible_user_id,
                'crew_id' => $crew->id,
                'unit' => 'gbl',
                'programmed_quantity' => 100,
                'executed_quantity' => 78,
                'fulfillment_percent' => 78,
                'weekly_budget' => 420000,
                'actual_cost' => 398500,
                'scheduled_date' => Carbon::now()->startOfWeek(),
                'actual_date' => Carbon::now()->startOfWeek()->addDays(5),
                'observations' => 'Semana con avance dentro de rango.',
                'status' => 'En proceso',
            ]
        );
    }

    private function createMaterialsAndPurchases(array $projects, Company $company, array $users): void
    {
        $materials = collect([
            ['CEM-50', 'Cemento CPC 30R', 'sacos', 100, 210],
            ['ARE-M3', 'Arena', 'm3', 8, 420],
            ['GRA-M3', 'Grava', 'm3', 8, 460],
            ['VAR-38', 'Varilla 3/8"', 'kg', 100, 22],
            ['CAB-12', 'Cable THW 12', 'm', 100, 18],
            ['TUB-12', 'Tubo conduit 1/2"', 'pzas', 50, 34],
        ])->mapWithKeys(function (array $row): array {
            return [$row[0] => MaterialCatalog::updateOrCreate(
                ['code' => $row[0]],
                ['name' => $row[1], 'unit' => $row[2], 'category' => 'Construccion', 'minimum_stock' => $row[3], 'standard_cost' => $row[4], 'status' => 'Activo']
            )];
        });

        $supplier = Supplier::updateOrCreate(
            ['business_name' => 'Materiales La Oruga SA de CV'],
            ['rfc' => 'MOR260101K90', 'contact_name' => 'Laura Garcia', 'phone' => '463 556 7758', 'email' => 'ventas@laoruga.local', 'specialty' => 'Materiales', 'materials_supplied' => 'Cemento, acero, instalaciones', 'payment_terms' => '15 dias', 'status' => 'Activo']
        );

        SupplierEvaluation::updateOrCreate(
            ['supplier_id' => $supplier->id, 'project_id' => $projects['los_pinos']->id],
            ['price_score' => 9, 'delivery_score' => 8.5, 'quality_score' => 9.2, 'warranty_score' => 8, 'comments' => 'Proveedor confiable para suministros semanales.', 'evaluated_at' => now()]
        );

        foreach ($projects as $project) {
            $warehouse = Warehouse::updateOrCreate(
                ['project_id' => $project->id, 'name' => 'Almacen de obra '.$project->project_key],
                ['company_id' => $company->id, 'responsible_user_id' => $users['almacen']->id, 'type' => 'Obra', 'location' => $project->location, 'status' => 'Activo']
            );

            foreach ($materials->take(3) as $material) {
                StockMovement::updateOrCreate(
                    ['warehouse_id' => $warehouse->id, 'material_catalog_id' => $material->id, 'folio' => 'KDX-'.$project->project_key.'-'.$material->code],
                    [
                        'project_id' => $project->id,
                        'user_id' => $users['almacen']->id,
                        'movement_date' => now()->subDays(5),
                        'movement_type' => 'Entrada',
                        'quantity_in' => 120,
                        'quantity_out' => 0,
                        'balance' => 120,
                        'unit_cost' => $material->standard_cost,
                        'movement_value' => 120 * (float) $material->standard_cost,
                        'accumulated_value' => 120 * (float) $material->standard_cost,
                        'reference' => 'Carga inicial demo',
                    ]
                );
            }

            $request = MaterialRequest::updateOrCreate(
                ['folio' => 'REQ-'.$project->project_key.'-001'],
                [
                    'project_id' => $project->id,
                    'requester_user_id' => $users['residente']->id,
                    'warehouse_id' => $warehouse->id,
                    'area' => 'Obra',
                    'requested_at' => now()->subDays(4),
                    'required_at' => now()->addDays(3),
                    'priority' => 'Alta',
                    'related_activity' => 'Cimentacion',
                    'observations' => 'Surtir antes del colado programado.',
                    'status' => 'Autorizado',
                ]
            );

            foreach ($materials->take(2) as $material) {
                MaterialRequestItem::updateOrCreate(
                    ['material_request_id' => $request->id, 'material_catalog_id' => $material->id],
                    [
                        'material_name' => $material->name,
                        'unit' => $material->unit,
                        'requested_quantity' => 80,
                        'available_stock' => 120,
                        'authorized_quantity' => 80,
                        'supplied_quantity' => 40,
                        'pending_quantity' => 40,
                        'usage_destination' => 'Frente principal',
                        'status' => 'Parcialmente surtido',
                    ]
                );
            }

            $supply = SupplyOrder::updateOrCreate(
                ['folio' => 'OS-'.$project->project_key.'-001'],
                [
                    'material_request_id' => $request->id,
                    'project_id' => $project->id,
                    'destination_warehouse_id' => $warehouse->id,
                    'supplier_id' => $supplier->id,
                    'shipping_user_id' => $users['compras']->id,
                    'receiving_user_id' => $users['almacen']->id,
                    'ordered_at' => now()->subDays(2),
                    'commitment_date' => now()->addDays(2),
                    'status' => 'En transito',
                    'observations' => 'Entrega parcial programada.',
                ]
            );

            foreach ($materials->take(2) as $material) {
                SupplyOrderItem::updateOrCreate(
                    ['supply_order_id' => $supply->id, 'material_catalog_id' => $material->id],
                    [
                        'material_name' => $material->name,
                        'unit' => $material->unit,
                        'requested_quantity' => 80,
                        'authorized_quantity' => 80,
                        'sent_quantity' => 80,
                        'received_quantity' => 40,
                        'rejected_quantity' => 0,
                        'unit_price' => $material->standard_cost,
                        'amount' => 80 * (float) $material->standard_cost,
                        'lot' => 'L-'.$project->project_key,
                    ]
                );
            }

            $purchase = PurchaseOrder::updateOrCreate(
                ['folio' => 'OC-'.$project->project_key.'-001'],
                [
                    'project_id' => $project->id,
                    'supplier_id' => $supplier->id,
                    'material_request_id' => $request->id,
                    'supply_order_id' => $supply->id,
                    'ordered_at' => now()->subDays(3),
                    'expected_delivery_at' => now()->addDays(2),
                    'subtotal' => 92800,
                    'taxes' => 14848,
                    'total' => 107648,
                    'status' => 'Autorizada',
                ]
            );

            PurchaseOrderItem::updateOrCreate(
                ['purchase_order_id' => $purchase->id, 'description' => 'Materiales principales '.$project->project_key],
                ['unit' => 'lote', 'quantity' => 1, 'unit_price' => 92800, 'amount' => 92800]
            );

            $invoice = Invoice::updateOrCreate(
                ['folio' => 'FAC-'.$project->project_key.'-001'],
                ['project_id' => $project->id, 'supplier_id' => $supplier->id, 'purchase_order_id' => $purchase->id, 'issued_at' => now()->subDays(1), 'subtotal' => 92800, 'taxes' => 14848, 'total' => 107648, 'status' => 'Recibida']
            );

            Payment::updateOrCreate(
                ['project_id' => $project->id, 'invoice_id' => $invoice->id, 'concept' => 'Pago proveedor materiales '.$project->project_key],
                ['payment_type' => 'Proveedor', 'beneficiary' => $supplier->business_name, 'requested_at' => now(), 'scheduled_at' => now()->addDays(7), 'amount' => 107648, 'payment_method' => 'Transferencia', 'status' => 'Programado']
            );
        }
    }

    private function createFollowUpRecords(Project $project, array $users): void
    {
        ProgressRecord::updateOrCreate(
            ['project_id' => $project->id, 'record_date' => now()->toDateString()],
            [
                'work_item_id' => $project->workItems()->first()?->id,
                'user_id' => $project->responsible_user_id,
                'week_number' => now()->weekOfYear,
                'programmed_percent' => min((float) $project->physical_progress + 5, 100),
                'actual_percent' => $project->physical_progress,
                'quantity_executed' => $project->physical_progress,
                'observations' => 'Registro inicial de avance fisico.',
                'status' => 'Registrado',
            ]
        );

        DailyLog::updateOrCreate(
            ['project_id' => $project->id, 'log_date' => now()->subDay()->toDateString()],
            [
                'responsible_user_id' => $project->responsible_user_id,
                'weather' => 'Soleado',
                'personnel' => 'Cuadrilla completa en sitio.',
                'machinery' => 'Retroexcavadora, compactador y camioneta.',
                'activities' => 'Avance en frente principal y revision de suministro.',
                'received_material' => 'Cemento y agregados.',
                'progress' => 'Sin desviaciones mayores.',
                'problems' => 'Pendiente confirmacion de una entrega.',
                'instructions' => 'Mantener evidencia fotografica diaria.',
                'observations' => 'Bitacora demo.',
                'photos' => [$project->photo_path],
            ]
        );

        Incident::updateOrCreate(
            ['folio' => 'INC-'.$project->project_key.'-001'],
            [
                'project_id' => $project->id,
                'responsible_user_id' => $users['supervisor']->id,
                'incident_date' => now()->subDays(3),
                'category' => 'Suministro',
                'description' => 'Retraso menor en entrega de material programado.',
                'priority' => 'Media',
                'commitment_date' => now()->addDays(2),
                'solution' => 'Proveedor confirmo entrega parcial.',
                'status' => 'En seguimiento',
            ]
        );

        ChangeOrder::updateOrCreate(
            ['folio' => 'OCAM-'.$project->project_key.'-001'],
            [
                'project_id' => $project->id,
                'requester_user_id' => $users['admin']->id,
                'requested_at' => now()->subDays(10),
                'description' => 'Ajuste de alcance por adecuacion solicitada por cliente.',
                'reason' => 'Cambio de especificacion.',
                'affected_items' => ['Instalaciones', 'Acabados'],
                'cost_impact' => round((float) $project->contracted_value * 0.015, 2),
                'schedule_impact_days' => 5,
                'new_contract_amount' => round((float) $project->contracted_value * 1.015, 2),
                'new_end_date' => Carbon::parse($project->estimated_end_date)->addDays(5),
                'authorizations' => ['Cliente', 'Administrador de obra'],
                'status' => 'Aprobada',
            ]
        );

        Document::updateOrCreate(
            ['project_id' => $project->id, 'name' => 'Contrato principal '.$project->project_key],
            ['user_id' => $users['admin']->id, 'document_type' => 'PDF', 'category' => 'Contratos', 'document_date' => $project->start_date, 'version' => '1.0', 'file_path' => 'contratos/'.$project->project_key.'.pdf', 'status' => 'Vigente']
        );

        Photo::updateOrCreate(
            ['project_id' => $project->id, 'title' => 'Vista general '.$project->project_key],
            ['user_id' => $project->responsible_user_id, 'category' => 'Avance', 'taken_at' => now()->subDays(2), 'file_path' => $project->photo_path, 'description' => 'Fotografia principal de la obra.']
        );

        ProjectEvent::updateOrCreate(
            ['project_id' => $project->id, 'title' => 'Revision semanal '.$project->project_key],
            ['event_type' => 'Reunion', 'starts_at' => now()->addDays(2)->setTime(9, 0), 'ends_at' => now()->addDays(2)->setTime(10, 0), 'status' => 'Programado', 'notes' => 'Revision de avance y pagos.']
        );
    }
}
