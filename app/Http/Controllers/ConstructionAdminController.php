<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ConstructionAuditLog;
use App\Models\ConstructionClient;
use App\Models\ConstructionPaymentOrder;
use App\Models\ConstructionPayroll;
use App\Models\ConstructionProject;
use App\Models\ConstructionUnitPrice;
use App\Models\User;
use App\Support\StoredFileResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ConstructionAdminController extends Controller
{
    public function dashboard(Request $request): View
    {
        $projects = ConstructionProject::query()
            ->visibleTo($request->user())
            ->with(['client', 'responsible'])
            ->orderBy('project_key')
            ->get();

        $summary = [
            'projects' => $projects->count(),
            'active' => $projects->where('status', 'En ejecucion')->count(),
            'contracted' => $projects->sum('contracted_value'),
            'paid' => $projects->sum('paid_amount'),
            'pending' => $projects->sum(fn (ConstructionProject $project): float => $project->balance_to_pay),
            'physical' => round($projects->avg('physical_progress') ?? 0, 2),
            'financial' => round($projects->avg('financial_progress') ?? 0, 2),
        ];

        $editableProjectIds = $this->canManage($request->user())
            ? $projects->pluck('id')->all()
            : DB::table('construction_project_user')
                ->where('user_id', $request->user()->id)
                ->where('can_edit', true)
                ->whereIn('construction_project_id', $projects->pluck('id'))
                ->pluck('construction_project_id')
                ->map(fn ($projectId): int => (int) $projectId)
                ->all();

        return view('construction.dashboard', [
            'projects' => $projects,
            'summary' => $summary,
            'editableProjectIds' => $editableProjectIds,
            'auditLogs' => ConstructionAuditLog::with(['user', 'project'])->latest('occurred_at')->limit(6)->get(),
        ]);
    }

    public function projects(Request $request): View
    {
        $baseQuery = ConstructionProject::query()->visibleTo($request->user());

        $projects = (clone $baseQuery)
            ->with(['company', 'client', 'responsible'])
            ->when($request->filled('company_id'), fn (Builder $query) => $query->where('company_id', $request->integer('company_id')))
            ->when($request->filled('client_id'), fn (Builder $query) => $query->where('client_id', $request->integer('client_id')))
            ->when($request->filled('responsible_user_id'), fn (Builder $query) => $query->where('responsible_user_id', $request->integer('responsible_user_id')))
            ->when($request->filled('modality'), fn (Builder $query) => $query->where('modality', $request->input('modality')))
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->input('status')))
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $search = '%'.$request->input('search').'%';
                $query->where(function (Builder $builder) use ($search): void {
                    $builder
                        ->where('project_key', 'like', $search)
                        ->orWhere('name', 'like', $search)
                        ->orWhere('location', 'like', $search)
                        ->orWhereHas('client', fn (Builder $client) => $client->where('name', 'like', $search));
                });
            })
            ->orderByDesc('start_date')
            ->get();

        $counts = [
            'Todas' => (clone $baseQuery)->count(),
            'Por iniciar' => (clone $baseQuery)->where('status', 'Por iniciar')->count(),
            'En ejecucion' => (clone $baseQuery)->where('status', 'En ejecucion')->count(),
            'Terminada' => (clone $baseQuery)->where('status', 'Terminada')->count(),
        ];

        return view('construction.projects.index', [
            'projects' => $projects,
            'counts' => $counts,
            'filterOptions' => $this->filterOptions(),
            'canCreate' => $this->canManage($request->user()),
        ]);
    }

    public function create(Request $request): View
    {
        abort_unless($this->canManage($request->user()), 403);

        return view('construction.projects.form', [
            'project' => new ConstructionProject([
                'project_key' => $this->nextProjectKey(),
                'status' => 'Por iniciar',
                'modality' => 'Precio alzado',
                'physical_progress' => 0,
                'financial_progress' => 0,
                'constructed_area' => 0,
                'sellable_rentable_area' => 0,
                'parking_area' => 0,
                'levels_count' => 0,
            ]),
            'companies' => Company::orderBy('name')->get(),
            'clients' => ConstructionClient::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($this->canManage($request->user()), 403);

        $data = $this->projectData($request);
        $data['project_key'] = $this->nextProjectKey();

        $project = ConstructionProject::create($data);
        $project->users()->syncWithoutDetaching([
            $request->user()->id => ['can_view' => true, 'can_edit' => true],
        ]);

        if ($project->responsible_user_id) {
            $project->users()->syncWithoutDetaching([
                $project->responsible_user_id => ['can_view' => true, 'can_edit' => true],
            ]);
        }

        $this->recordAudit($request, $project, 'Obra creada', "Se creo la obra {$project->project_key}.");

        return redirect(route('construction.dashboard').'#project-row-'.$project->id)
            ->with('status', 'Obra creada correctamente.');
    }

    public function show(Request $request, ConstructionProject $project): RedirectResponse
    {
        $this->authorizeProject($request, $project);

        return redirect(route('construction.dashboard').'#project-row-'.$project->id);
    }

    public function edit(Request $request, ConstructionProject $project): View
    {
        abort_unless($this->canEditProject($request->user(), $project), 403);

        return view('construction.projects.form', [
            'project' => $project,
            'companies' => Company::orderBy('name')->get(),
            'clients' => ConstructionClient::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, ConstructionProject $project): RedirectResponse
    {
        abort_unless($this->canEditProject($request->user(), $project), 403);

        $oldValues = $project->only([
            'name',
            'status',
            'physical_progress',
            'financial_progress',
            'paid_amount',
            'constructed_area',
            'sellable_rentable_area',
            'parking_area',
            'levels_count',
        ]);
        $project->update($this->projectData($request, $project));

        $this->recordAudit($request, $project, 'Obra actualizada', "Se actualizo la obra {$project->project_key}.", $oldValues, $project->only(array_keys($oldValues)));

        return redirect(route('construction.dashboard').'#project-row-'.$project->id)
            ->with('status', 'Obra actualizada correctamente.');
    }

    public function destroy(Request $request, ConstructionProject $project): RedirectResponse
    {
        abort_unless($this->canManage($request->user()), 403);

        $this->recordAudit($request, $project, 'Obra eliminada', "Se envio {$project->project_key} a borrado logico.");
        $project->delete();

        return redirect()
            ->route('construction.projects.index')
            ->with('status', 'Obra enviada a borrado logico.');
    }

    public function audit(): View
    {
        return view('construction.audit', [
            'logs' => ConstructionAuditLog::with(['user', 'project'])->latest('occurred_at')->limit(600)->get(),
        ]);
    }

    public function usersAccess(): View
    {
        $projects = ConstructionProject::with('client')->orderBy('project_key')->get();
        $users = User::orderBy('name')->get();
        $accessByUser = DB::table('construction_project_user')
            ->get()
            ->groupBy('user_id')
            ->map(fn ($rows) => $rows->keyBy('construction_project_id'));

        return view('construction.users-access', compact('projects', 'users', 'accessByUser'));
    }

    public function updateUserAccess(Request $request, User $user): RedirectResponse
    {
        abort_unless($this->canManage($request->user()), 403);

        $data = $request->validate([
            'projects' => ['nullable', 'array'],
            'projects.*.can_view' => ['nullable', 'boolean'],
            'projects.*.can_edit' => ['nullable', 'boolean'],
        ]);

        $oldAccess = DB::table('construction_project_user')
            ->where('user_id', $user->id)
            ->get()
            ->mapWithKeys(fn ($row) => [$row->construction_project_id => ['can_view' => (bool) $row->can_view, 'can_edit' => (bool) $row->can_edit]])
            ->all();

        $sync = collect($data['projects'] ?? [])
            ->filter(fn (array $access): bool => isset($access['can_view']))
            ->mapWithKeys(fn (array $access, string $projectId): array => [
                (int) $projectId => ['can_view' => true, 'can_edit' => isset($access['can_edit'])],
            ])
            ->all();

        $user->belongsToMany(ConstructionProject::class, 'construction_project_user', 'user_id', 'construction_project_id')
            ->withPivot(['can_view', 'can_edit'])
            ->sync($sync);

        $this->recordAudit($request, null, 'Permisos actualizados', "Se actualizaron permisos de obra para {$user->email}.", $oldAccess, $sync);

        return back()->with('status', 'Permisos de obra actualizados.');
    }

    public function storePayroll(Request $request): RedirectResponse
    {
        $data = $this->payrollData($request);
        $project = ConstructionProject::findOrFail($data['construction_project_id']);

        abort_unless($this->canEditProject($request->user(), $project), 403);

        $payroll = ConstructionPayroll::create($data);
        $this->syncPayrollPaymentOrder($payroll);
        $this->recordAudit(
            $request,
            $project,
            'Nomina creada',
            "Se creo la nomina periodica {$payroll->code}.",
            null,
            $payroll->toArray()
        );

        return redirect()
            ->route('construction.placeholder', [
                'section' => 'mano-obra',
                'project' => $project->id,
                'open_payroll' => 1,
            ])
            ->with('status', "Nomina {$payroll->code} creada correctamente.");
    }

    public function storeEstimate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'construction_project_id' => ['required', 'integer', 'exists:construction_projects,id'],
            'code' => [
                'required',
                'string',
                'max:40',
                Rule::unique('construction_payment_orders', 'code'),
                Rule::unique('construction_payrolls', 'code'),
            ],
            'contractor' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:120'],
            'periodicity' => ['required', Rule::in(['Semanal', 'Quincenal', 'Mensual'])],
            'period_reference' => ['required', 'string', 'max:120'],
            'payment_due_date' => ['required', 'date'],
            'progress' => ['required', 'numeric', 'min:0', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['Sin asignar', 'Programado', 'En ejecucion', 'En revision', 'Aprobado'])],
        ]);
        $project = ConstructionProject::findOrFail($data['construction_project_id']);

        abort_unless($this->canEditProject($request->user(), $project), 403);

        $estimate = ConstructionPaymentOrder::create([
            ...$data,
            'type' => 'Estimacion',
        ]);
        $this->recordAudit(
            $request,
            $project,
            'Estimacion creada',
            "Se creo el paquete de estimaciones {$estimate->code}.",
            null,
            $estimate->toArray()
        );

        return redirect()
            ->route('construction.placeholder', [
                'section' => 'mano-obra',
                'project' => $project->id,
                'open_estimates' => 1,
            ])
            ->with('status', "Paquete de estimaciones {$estimate->code} creado correctamente.");
    }

    public function editPayroll(Request $request, ConstructionPayroll $payroll): View
    {
        $payroll->load('project');
        abort_unless($this->canEditProject($request->user(), $payroll->project), 403);

        $projects = ConstructionProject::query()
            ->visibleTo($request->user())
            ->orderBy('project_key')
            ->get()
            ->filter(fn (ConstructionProject $project): bool => $this->canEditProject($request->user(), $project))
            ->values();

        return view('construction.payrolls.edit', [
            'payroll' => $payroll,
            'projects' => $projects,
            'payrollPeriodicityOptions' => ['Semanal', 'Quincenal', 'Mensual'],
            'payrollStatusOptions' => ['Borrador', 'En revision', 'Aprobada', 'Pagada', 'Cancelada'],
        ]);
    }

    public function updatePayroll(Request $request, ConstructionPayroll $payroll): RedirectResponse
    {
        abort_unless($this->canEditProject($request->user(), $payroll->project), 403);

        $data = $this->payrollData($request, $payroll);
        $project = ConstructionProject::findOrFail($data['construction_project_id']);

        abort_unless($this->canEditProject($request->user(), $project), 403);

        $oldValues = $payroll->toArray();
        $payroll->update($data);
        $this->syncPayrollPaymentOrder($payroll->fresh());
        $this->recordAudit(
            $request,
            $project,
            'Nomina actualizada',
            "Se actualizo la nomina periodica {$payroll->code}.",
            $oldValues,
            $payroll->fresh()->toArray()
        );

        return redirect()
            ->route('construction.placeholder', [
                'section' => 'mano-obra',
                'project' => $project->id,
                'open_payroll' => 1,
            ])
            ->with('status', "Nomina {$payroll->code} actualizada correctamente.");
    }

    public function destroyPayroll(Request $request, ConstructionPayroll $payroll): RedirectResponse
    {
        $payroll->loadMissing('project');
        $project = $payroll->project;

        abort_unless($project !== null, 404);
        abort_unless($this->canEditProject($request->user(), $project), 403);

        $oldValues = $payroll->toArray();
        $code = $payroll->code;

        $payroll->delete();

        $this->recordAudit(
            $request,
            $project,
            'Nomina eliminada',
            "Se elimino la nomina periodica {$code}.",
            $oldValues
        );

        return redirect()
            ->route('construction.placeholder', [
                'section' => 'mano-obra',
                'project' => $project->id,
            ])
            ->with('status', "Nomina {$code} eliminada correctamente.");
    }

    public function storePaymentInvoice(Request $request, ConstructionPaymentOrder $paymentOrder): RedirectResponse
    {
        $paymentOrder->loadMissing('project');
        abort_unless($this->canEditProject($request->user(), $paymentOrder->project), 403);

        $request->validate([
            'invoice_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ]);

        if ($paymentOrder->invoice_file_path && Storage::exists($paymentOrder->invoice_file_path)) {
            Storage::delete($paymentOrder->invoice_file_path);
        }

        $file = $request->file('invoice_file');
        $paymentOrder->update([
            'invoice_file_path' => $file->store('construction-payment-invoices'),
            'invoice_original_name' => $file->getClientOriginalName(),
        ]);

        $this->recordAudit(
            $request,
            $paymentOrder->project,
            'Factura de pago cargada',
            "Se cargo la factura de {$paymentOrder->code}."
        );

        return back()->with('status', "Factura de {$paymentOrder->code} cargada correctamente.");
    }

    public function paymentInvoice(Request $request, ConstructionPaymentOrder $paymentOrder)
    {
        $paymentOrder->loadMissing('project');
        $this->authorizeProject($request, $paymentOrder->project);
        abort_unless(filled($paymentOrder->invoice_file_path), 404);

        return StoredFileResponse::inline(
            $paymentOrder->invoice_file_path,
            $paymentOrder->invoice_original_name ?: $paymentOrder->code.'-factura'
        );
    }

    public function paymentReceipt(Request $request, ConstructionPaymentOrder $paymentOrder)
    {
        $paymentOrder->loadMissing('project');
        $this->authorizeProject($request, $paymentOrder->project);
        abort_unless(filled($paymentOrder->payment_file_path), 404);

        return StoredFileResponse::inline(
            $paymentOrder->payment_file_path,
            $paymentOrder->payment_original_name ?: $paymentOrder->code.'-pago'
        );
    }

    public function destroyPaymentOrder(Request $request, ConstructionPaymentOrder $paymentOrder): RedirectResponse
    {
        $paymentOrder->loadMissing('project');
        abort_unless($this->canEditProject($request->user(), $paymentOrder->project), 403);
        abort_unless($paymentOrder->construction_payroll_id === null, 403);

        $oldValues = $paymentOrder->toArray();
        $project = $paymentOrder->project;
        $code = $paymentOrder->code;

        foreach ([$paymentOrder->invoice_file_path, $paymentOrder->payment_file_path] as $path) {
            if ($path && Storage::exists($path)) {
                Storage::delete($path);
            }
        }

        $paymentOrder->delete();
        $this->recordAudit(
            $request,
            $project,
            'Estimacion eliminada',
            "Se elimino la estimacion {$code}.",
            $oldValues
        );

        return redirect()
            ->route('construction.placeholder', ['section' => 'mano-obra', 'project' => $project->id])
            ->with('status', "Estimacion {$code} eliminada correctamente.");
    }

    public function placeholder(Request $request, string $section): View|RedirectResponse
    {
        if ($section === 'compras') {
            return redirect()->route('buyer.orders.index', ['context' => 'construction']);
        }

        if ($section === 'proveedores') {
            return redirect()->route('construction.providers.index');
        }

        if ($section === 'mano-obra') {
            $projects = ConstructionProject::query()
                ->visibleTo($request->user())
                ->with(['client', 'responsible'])
                ->orderBy('project_key')
                ->get();

            $activeProjects = $projects->where('status', 'En ejecucion')->values();

            if ($activeProjects->isEmpty()) {
                $activeProjects = $projects->values();
            }

            $requestedProjectId = $request->integer('project');
            $selectedProjectId = $activeProjects->contains('id', $requestedProjectId)
                ? $requestedProjectId
                : $activeProjects->first()?->id;
            $paymentOrders = ConstructionPaymentOrder::query()
                ->whereIn('construction_project_id', $activeProjects->pluck('id'))
                ->with(['project', 'payroll'])
                ->orderBy('code')
                ->get();

            return view('construction.labor-tracking', [
                'projects' => $projects,
                'selectedProjectId' => $selectedProjectId,
                'catalogRows' => $this->laborTrackingRows($paymentOrders),
                'laborRows' => $this->laborTrackingRows($paymentOrders->filter(fn (ConstructionPaymentOrder $order): bool =>
                    blank($order->payment_file_path)
                    && ! in_array($order->status, ['Pagada', 'Pagado', 'Cancelada', 'Cancelado'], true)
                )),
                'payrollPeriodicityOptions' => ['Semanal', 'Quincenal', 'Mensual'],
                'payrollStatusOptions' => ['Borrador', 'En revision', 'Aprobada', 'Pagada', 'Cancelada'],
            ]);
        }

        if ($section === 'pagos') {
            $projects = ConstructionProject::query()
                ->visibleTo($request->user())
                ->with(['client', 'responsible'])
                ->orderBy('project_key')
                ->get();
            $activeProjects = $projects->where('status', 'En ejecucion')->values();

            if ($activeProjects->isEmpty()) {
                $activeProjects = $projects->values();
            }

            $requestedProjectId = $request->integer('project');
            $selectedProjectId = $activeProjects->contains('id', $requestedProjectId)
                ? $requestedProjectId
                : $activeProjects->first()?->id;
            $paymentOrders = ConstructionPaymentOrder::query()
                ->paid()
                ->where('construction_project_id', $selectedProjectId)
                ->with(['project', 'paidBy'])
                ->orderByDesc('paid_on')
                ->orderBy('code')
                ->get();

            return view('construction.payment-history', [
                'projects' => $projects,
                'selectedProjectId' => $selectedProjectId,
                'paymentOrders' => $paymentOrders,
            ]);
        }

        if ($section === 'tabulador-precios-unitarios') {
            return $this->unitPriceCatalog($request);
        }

        $labels = $this->placeholderLabels();
        $showProjectCarousel = in_array($section, [
            'generadores-obra',
            'calendario',
            'pagos',
            'materiales-insumos',
            'ordenes-suministro',
            'almacenes',
        ], true);
        $showGeneratorPanel = $section === 'generadores-obra';
        $showMaterialsCatalog = $section === 'materiales-insumos';

        return view('construction.placeholder', [
            'label' => $labels[$section] ?? str($section)->replace('-', ' ')->title(),
            'projects' => $showProjectCarousel
                ? ConstructionProject::query()
                    ->visibleTo($request->user())
                    ->with(['client', 'responsible'])
                    ->orderBy('project_key')
                    ->get()
                : collect(),
            'showProjectCarousel' => $showProjectCarousel,
            'showMaterialsCatalogButton' => $showMaterialsCatalog,
            'materialsCatalog' => $showMaterialsCatalog ? $this->materialsExplosionCatalogData() : [],
            'showGeneratorPanel' => $showGeneratorPanel,
            'generatorPanel' => $showGeneratorPanel ? $this->generatorPanelData() : [],
        ]);
    }

    private function unitPriceCatalog(Request $request): View
    {
        $search = trim((string) $request->input('q', ''));
        $chapter = trim((string) $request->input('chapter', ''));

        $unitPrices = ConstructionUnitPrice::query()
            ->search($search)
            ->when($chapter !== '', fn (Builder $query) => $query->where('chapter_code', $chapter))
            ->orderBy('code')
            ->paginate(500)
            ->withQueryString();

        $chapters = ConstructionUnitPrice::query()
            ->select(['chapter_code', 'chapter_name'])
            ->distinct()
            ->orderBy('chapter_code')
            ->get();

        return view('construction.unit-prices.index', [
            'unitPrices' => $unitPrices,
            'chapters' => $chapters,
            'search' => $search,
            'selectedChapter' => $chapter,
        ]);
    }

    private function generatorPanelData(): array
    {
        return [
            'selected_level' => 'nivel-02',
            'levels' => [
                ['key' => 'total', 'short' => 'TOT', 'name' => 'Total', 'area' => 1248.50],
                ['key' => 'sotano', 'short' => 'SOT', 'name' => 'Sotano', 'area' => 0],
                ['key' => 'planta-baja', 'short' => 'PB', 'name' => 'Planta baja', 'area' => 620.10],
                ['key' => 'nivel-01', 'short' => 'N01', 'name' => 'Nivel 01', 'area' => 910.30],
                ['key' => 'nivel-02', 'short' => 'N02', 'name' => 'Nivel 02', 'area' => 286.40],
                ['key' => 'nivel-03', 'short' => 'N03', 'name' => 'Nivel 03', 'area' => 0],
                ['key' => 'azotea', 'short' => 'AZ', 'name' => 'Azotea', 'area' => 0],
            ],
            'categories' => [
                ['key' => 'preliminares', 'name' => 'Preliminares', 'expanded' => false, 'rows' => []],
                ['key' => 'cimentacion', 'name' => 'Cimentacion', 'expanded' => false, 'rows' => []],
                ['key' => 'estructura', 'name' => 'Estructura', 'expanded' => false, 'rows' => []],
                [
                    'key' => 'albanileria',
                    'name' => 'Albanileria',
                    'expanded' => true,
                    'rows' => [
                        ['concept' => 'Muros de block', 'zone' => 'Ejes A-B / 1-3', 'length' => 6.20, 'height' => 2.90, 'pieces' => 3, 'voids' => 1.38, 'unit' => 'm2', 'evidence' => 'A-B_1-3.pdf'],
                        ['concept' => 'Castillos y cadenas', 'zone' => 'Ejes B-C / 1-3', 'length' => 4.10, 'height' => 2.90, 'pieces' => 8, 'voids' => 2.64, 'unit' => 'm2', 'evidence' => 'Castillos.pdf'],
                        ['concept' => 'Aplanados', 'zone' => 'Ejes A-D / 1-3', 'length' => 12.80, 'height' => 2.20, 'pieces' => 3, 'voids' => 0.64, 'unit' => 'm2', 'evidence' => 'Aplanados.pdf'],
                        ['concept' => 'Pretiles', 'zone' => 'Azotea / 1-2', 'length' => 9.50, 'height' => 1.22, 'pieces' => 5, 'voids' => 0.43, 'unit' => 'm2', 'evidence' => 'Pretiles.pdf'],
                    ],
                ],
                ['key' => 'instalaciones', 'name' => 'Instalaciones', 'expanded' => false, 'rows' => []],
            ],
            'history' => [
                ['action' => 'Cuantificacion actualizada', 'detail' => 'Nivel 02 - Albanileria', 'date' => 'Hoy, 09:30'],
                ['action' => 'Plano asociado', 'detail' => 'Muros de block - A-B_1-3.pdf', 'date' => 'Ayer, 16:45'],
                ['action' => 'Nivel creado', 'detail' => 'Nivel 02', 'date' => '12/08/2026, 11:20'],
            ],
        ];
    }

    private function materialsExplosionCatalogData(): array
    {
        $supply = static fn (
            string $name,
            float $dosage,
            string $dosageUnit,
            float $waste,
            string $purchaseUnit,
            float $unitCost,
        ): array => [
            'name' => $name,
            'dosage' => $dosage,
            'dosage_unit' => $dosageUnit,
            'waste' => $waste,
            'purchase_unit' => $purchaseUnit,
            'unit_cost' => $unitCost,
        ];

        $concreteMix = static function (
            string $key,
            string $name,
            float $cement,
            float $sand,
            float $gravel,
            bool $expanded = false,
        ) use ($supply): array {
            return [
                'key' => $key,
                'name' => $name,
                'quantity' => 1,
                'unit' => 'm3',
                'expanded' => $expanded,
                'supplies' => [
                    $supply('Cemento CPC 30R', $cement / 50, 'bultos/m3', 2, 'bulto de 50 kg', 210),
                    $supply('Arena', $sand, 'm3/m3', 3, 'bultos', 280),
                    $supply('Grava 3/4', $gravel, 'm3/m3', 3, 'bultos', 260),
                ],
            ];
        };

        return [
            'categories' => [
                [
                    'key' => 'pintura',
                    'name' => 'Pintura',
                    'quantity' => 2480,
                    'unit' => 'm2',
                    'expanded' => false,
                    'concepts' => [
                        [
                            'key' => 'pintura-vinilica',
                            'name' => 'Pintura vinil acrilica en muros',
                            'quantity' => 1,
                            'unit' => 'm2',
                            'expanded' => false,
                            'supplies' => [
                                $supply('Pintura vinil acrilica', 0.12, 'l/m2', 5, 'l', 78),
                                $supply('Sellador acrilico', 0.08, 'l/m2', 3, 'l', 52),
                                $supply('Rodillo profesional', 0.01, 'pza/m2', 2, 'pza', 85),
                            ],
                        ],
                        [
                            'key' => 'esmalte-estructura',
                            'name' => 'Esmalte en estructura metalica',
                            'quantity' => 1,
                            'unit' => 'm2',
                            'expanded' => false,
                            'supplies' => [
                                $supply('Esmalte alquidalico', 0.10, 'l/m2', 5, 'l', 96),
                                $supply('Primario anticorrosivo', 0.08, 'l/m2', 4, 'l', 88),
                                $supply('Thinner estandar', 0.03, 'l/m2', 3, 'l', 42),
                            ],
                        ],
                    ],
                ],
                [
                    'key' => 'pasta',
                    'name' => 'Pasta',
                    'quantity' => 1850,
                    'unit' => 'm2',
                    'expanded' => false,
                    'concepts' => [
                        [
                            'key' => 'pasta-base',
                            'name' => 'Pasta base en muro interior',
                            'quantity' => 1,
                            'unit' => 'm2',
                            'expanded' => false,
                            'supplies' => [
                                $supply('Pasta base', 1.40, 'kg/m2', 4, 'kg', 15.50),
                                $supply('Sellador para pasta', 0.08, 'l/m2', 3, 'l', 48),
                            ],
                        ],
                        [
                            'key' => 'pasta-fina',
                            'name' => 'Pasta fina texturizada',
                            'quantity' => 1,
                            'unit' => 'm2',
                            'expanded' => false,
                            'supplies' => [
                                $supply('Pasta fina', 1.10, 'kg/m2', 4, 'kg', 19.80),
                                $supply('Pigmento mineral', 0.03, 'kg/m2', 2, 'kg', 62),
                            ],
                        ],
                    ],
                ],
                [
                    'key' => 'concreto',
                    'name' => 'Concreto',
                    'quantity' => 420,
                    'unit' => 'm3',
                    'note' => 'Dosificacion base por resistencia: 1.00 m3',
                    'expanded' => true,
                    'concepts' => [
                        $concreteMix('concreto-150', "Concreto f'c 150 kg/cm2", 250, 0.56, 0.70),
                        $concreteMix('concreto-200', "Concreto f'c 200 kg/cm2", 300, 0.54, 0.68),
                        $concreteMix('concreto-250', "Concreto f'c 250 kg/cm2", 350, 0.52, 0.66, true),
                        $concreteMix('concreto-300', "Concreto f'c 300 kg/cm2", 400, 0.50, 0.64),
                    ],
                ],
                [
                    'key' => 'muro-block',
                    'name' => 'Muro de block',
                    'quantity' => 1200,
                    'unit' => 'm2',
                    'expanded' => false,
                    'concepts' => [
                        [
                            'key' => 'block-12',
                            'name' => 'Muro de block 12 x 20 x 40 cm',
                            'quantity' => 1,
                            'unit' => 'm2',
                            'expanded' => false,
                            'supplies' => [
                                $supply('Block hueco 12 cm', 12.50, 'pza/m2', 5, 'pza', 18.50),
                                $supply('Mortero cemento-arena', 0.02, 'm3/m2', 4, 'm3', 1850),
                            ],
                        ],
                        [
                            'key' => 'block-12-12-20',
                            'name' => 'Muro de block 12 x 12 x 20 cm',
                            'quantity' => 1,
                            'unit' => 'm2',
                            'expanded' => false,
                            'supplies' => [
                                $supply('Block hueco 12 x 12 x 20 cm', 41.67, 'pza/m2', 5, 'pza', 10.50),
                                $supply('Mortero cemento-arena', 0.03, 'm3/m2', 4, 'm3', 1850),
                            ],
                        ],
                        [
                            'key' => 'block-15',
                            'name' => 'Muro de block 15 x 20 x 40 cm',
                            'quantity' => 1,
                            'unit' => 'm2',
                            'expanded' => false,
                            'supplies' => [
                                $supply('Block hueco 15 cm', 12.50, 'pza/m2', 5, 'pza', 22),
                                $supply('Mortero cemento-arena', 0.02, 'm3/m2', 4, 'm3', 1850),
                            ],
                        ],
                        [
                            'key' => 'tabique-rojo',
                            'name' => 'Muro de tabique rojo recocido',
                            'quantity' => 1,
                            'unit' => 'm2',
                            'expanded' => false,
                            'supplies' => [
                                $supply('Tabique rojo recocido', 42, 'pza/m2', 6, 'pza', 5.80),
                                $supply('Mortero cemento-arena', 0.03, 'm3/m2', 4, 'm3', 1850),
                            ],
                        ],
                        [
                            'key' => 'celosia',
                            'name' => 'Muro de celosia de concreto',
                            'quantity' => 1,
                            'unit' => 'm2',
                            'expanded' => false,
                            'supplies' => [
                                $supply('Celosia prefabricada', 16, 'pza/m2', 5, 'pza', 32),
                                $supply('Mortero adhesivo', 4.50, 'kg/m2', 4, 'kg', 12.50),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function nextProjectKey(): string
    {
        $lastSequence = ConstructionProject::withTrashed()
            ->where('project_key', 'like', 'OBR-%')
            ->pluck('project_key')
            ->map(function (string $projectKey): int {
                return preg_match('/^OBR-(\d+)$/', $projectKey, $matches)
                    ? (int) $matches[1]
                    : 0;
            })
            ->max() ?? 0;

        return sprintf('OBR-%03d', $lastSequence + 1);
    }

    private function projectData(Request $request, ?ConstructionProject $project = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'client_id' => ['nullable', 'integer', 'exists:construction_clients,id'],
            'responsible_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'project_type' => ['nullable', 'string', 'max:120'],
            'modality' => ['required', 'string', 'max:120'],
            'status' => ['required', 'string', 'max:120'],
            'start_date' => ['nullable', 'date'],
            'estimated_end_date' => ['nullable', 'date'],
            'contracted_value' => ['nullable', 'numeric', 'min:0'],
            'constructed_area' => ['nullable', 'numeric', 'min:0'],
            'sellable_rentable_area' => ['nullable', 'numeric', 'min:0'],
            'parking_area' => ['nullable', 'numeric', 'min:0'],
            'levels_count' => ['nullable', 'integer', 'min:0', 'max:999'],
            'photo_path' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];

        if ($project) {
            $rules['estimated_amount'] = ['nullable', 'numeric', 'min:0'];
            $rules['paid_amount'] = ['nullable', 'numeric', 'min:0'];
            $rules['retention_amount'] = ['nullable', 'numeric', 'min:0'];
            $rules['physical_progress'] = ['nullable', 'numeric', 'min:0', 'max:100'];
            $rules['financial_progress'] = ['nullable', 'numeric', 'min:0', 'max:100'];
        }

        $data = $request->validate($rules);

        if (! $project) {
            $data['estimated_amount'] = 0;
            $data['paid_amount'] = 0;
            $data['retention_amount'] = 0;
            $data['physical_progress'] = 0;
            $data['financial_progress'] = 0;
        }

        foreach (['company_id', 'client_id', 'responsible_user_id'] as $field) {
            $data[$field] = ($data[$field] ?? null) ?: null;
        }

        foreach (['contracted_value', 'estimated_amount', 'paid_amount', 'retention_amount', 'physical_progress', 'financial_progress', 'constructed_area', 'sellable_rentable_area', 'parking_area'] as $field) {
            $data[$field] = $data[$field] ?? 0;
        }
        $data['levels_count'] = $data['levels_count'] ?? 0;

        return $data;
    }

    private function payrollData(Request $request, ?ConstructionPayroll $payroll = null): array
    {
        return $request->validate([
            'construction_project_id' => ['required', 'integer', 'exists:construction_projects,id'],
            'code' => [
                'required',
                'string',
                'max:40',
                Rule::unique('construction_payrolls', 'code')->ignore($payroll?->id),
                Rule::unique('construction_payment_orders', 'code')->ignore($payroll?->paymentOrder?->id),
            ],
            'contractor' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:120'],
            'periodicity' => ['required', Rule::in(['Semanal', 'Quincenal', 'Mensual'])],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'payment_due_date' => ['required', 'date', 'after_or_equal:period_end'],
            'progress' => ['required', 'numeric', 'min:0', 'max:100'],
            'amount' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['Borrador', 'En revision', 'Aprobada', 'Pagada', 'Cancelada'])],
        ]);
    }

    private function filterOptions(): array
    {
        return [
            'companies' => Company::orderBy('name')->get(),
            'clients' => ConstructionClient::orderBy('name')->get(),
            'responsibles' => User::orderBy('name')->get(),
            'modalities' => ['Precio alzado', 'Administracion', 'Hibrida'],
            'statuses' => ['Por iniciar', 'En ejecucion', 'Terminada', 'Suspendida'],
        ];
    }

    private function canManage(User $user): bool
    {
        return $user->role === 'superadmin';
    }

    private function authorizeProject(Request $request, ConstructionProject $project): void
    {
        $user = $request->user();

        if ($this->canManage($user)) {
            return;
        }

        abort_unless($project->users()->where('users.id', $user->id)->where('construction_project_user.can_view', true)->exists(), 403);
    }

    private function canEditProject(User $user, ConstructionProject $project): bool
    {
        if ($this->canManage($user)) {
            return true;
        }

        return $project->users()->where('users.id', $user->id)->where('construction_project_user.can_edit', true)->exists();
    }

    private function recordAudit(Request $request, ?ConstructionProject $project, string $action, string $description, ?array $oldValues = null, ?array $newValues = null): void
    {
        ConstructionAuditLog::create([
            'user_id' => $request->user()?->id,
            'construction_project_id' => $project?->id,
            'occurred_at' => now(),
            'module' => 'Administracion de obra',
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    private function placeholderLabels(): array
    {
        return [
            'generadores-obra' => 'Generadores de obra',
            'contratos' => 'Contratos',
            'presupuestos' => 'Presupuestos',
            'partidas-conceptos' => 'Partidas y conceptos',
            'estimaciones' => 'Estimaciones',
            'avances' => 'Avances',
            'alcances-semanales' => 'Alcances semanales',
            'mano-obra' => 'Mano de obra',
            'nomina' => 'Nomina',
            'calendario' => 'Calendario',
            'fotografias' => 'Fotografias',
            'incidencias' => 'Incidencias',
            'cambios' => 'Cambios',
            'materiales-insumos' => 'Materiales e insumos',
            'requerimientos' => 'Requerimientos',
            'ordenes-suministro' => 'Ordenes de suministro',
            'almacenes' => 'Almacenes',
            'compras' => 'Compras',
            'tabulador-precios-unitarios' => 'Tabulador de precios unitarios',
            'proveedores' => 'Alta de proveedor',
            'pagos' => 'Historial de pagos',
            'flujo-efectivo' => 'Flujo de efectivo',
            'facturas' => 'Facturas',
            'retenciones' => 'Retenciones',
            'reportes' => 'Reportes',
            'documentos' => 'Documentos',
            'configuracion' => 'Configuracion',
        ];
    }

    private function syncPayrollPaymentOrder(ConstructionPayroll $payroll): void
    {
        $paymentOrder = ConstructionPaymentOrder::firstOrNew([
            'construction_payroll_id' => $payroll->id,
        ]);
        $wasPaid = filled($paymentOrder->payment_file_path);

        $paymentOrder->fill([
            'construction_project_id' => $payroll->construction_project_id,
            'type' => 'Nomina',
            'code' => $payroll->code,
            'description' => $payroll->description,
            'contractor' => $payroll->contractor,
            'area' => $payroll->area,
            'periodicity' => $payroll->periodicity,
            'period_start' => $payroll->period_start,
            'period_end' => $payroll->period_end,
            'period_reference' => null,
            'payment_due_date' => $payroll->payment_due_date,
            'progress' => $payroll->progress,
            'amount' => $payroll->amount,
            'status' => $wasPaid ? 'Pagado' : $payroll->status,
        ]);
        $paymentOrder->save();
    }

    private function laborTrackingRows(Collection $paymentOrders): array
    {
        return $paymentOrders->map(fn (ConstructionPaymentOrder $order): array => [
            'id' => $order->construction_payroll_id,
            'payment_order_id' => $order->id,
            'project_id' => $order->construction_project_id,
            'type' => $order->type,
            'code' => $order->code,
            'description' => $order->description,
            'area' => $order->area ?: ($order->type === 'Nomina' ? 'Mano de obra' : 'Sin categoria'),
            'responsible' => $order->contractor ?: '-',
            'periodicity' => $order->periodicity ?: '-',
            'period' => $order->periodLabel(),
            'payment_due_date' => $order->payment_due_date?->format('d/m/Y') ?? '-',
            'progress' => (float) $order->progress,
            'amount' => (float) $order->amount,
            'disbursed_amount' => filled($order->payment_file_path) ? (float) $order->amount : 0.0,
            'status' => $order->status,
            'status_class' => $order->statusClass(),
            'payment_date' => $order->paid_on?->format('d/m/Y') ?? '-',
            'invoice_file_url' => filled($order->invoice_file_path)
                ? route('construction.payment-orders.invoice', $order)
                : null,
            'payment_file_url' => filled($order->payment_file_path)
                ? route('construction.payment-orders.payment', $order)
                : null,
            'invoice_upload_url' => route('construction.payment-orders.invoice.store', $order),
            'payment_upload_url' => route('finance.construction-payment-orders.payment.store', $order),
            'delete_url' => $order->construction_payroll_id
                ? route('construction.payrolls.destroy', $order->construction_payroll_id)
                : route('construction.payment-orders.destroy', $order),
        ])->values()->all();
    }

    private function payrollStatusClass(string $status): string
    {
        return match ($status) {
            'Aprobada' => 'approved',
            'Pagada', 'Pagado' => 'paid',
            'En revision' => 'warning',
            'Cancelada' => 'canceled',
            default => 'pending',
        };
    }
}
