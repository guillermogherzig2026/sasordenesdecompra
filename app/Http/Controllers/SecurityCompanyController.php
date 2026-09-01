<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\SecurityBranch;
use App\Models\SecurityCompany;
use App\Support\SecurityAnalyticsCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SecurityCompanyController extends Controller
{
    public function index(Request $request): View
    {
        $availableSections = [
            'companies',
            'branches',
            'cameras',
            'visualization',
            'analytics',
            'alerts',
            'users',
            'reports',
            'configuration',
        ];
        $requestedSection = (string) $request->query('section', 'companies');
        $section = in_array($requestedSection, $availableSections, true) ? $requestedSection : 'companies';
        $securityCompanies = collect();
        $securityBranches = collect();
        $selectedSecurityCompany = null;
        $selectedSectionCompany = null;
        $selectedCameraBranch = null;
        $selectedAnalyticsCompany = null;
        $selectedAnalyticsBranch = null;
        $financeCompanies = collect();
        $analyticsParameters = [];
        $analyticsRequested = false;
        $analyticsDate = now()->toDateString();
        $companyDashboardDateFrom = now()->toDateString();
        $companyDashboardDateTo = now()->toDateString();
        $companyDashboardComparison = 'previous_day';

        if ($section === 'companies') {
            $securityCompanies = SecurityCompany::query()
                ->with('financeCompany')
                ->withCount('branches')
                ->orderBy('name')
                ->get();

            $selectedSecurityCompany = $securityCompanies
                ->firstWhere('id', $request->integer('company'));

            if ($selectedSecurityCompany) {
                $request->session()->put('security_company_id', $selectedSecurityCompany->id);
            }

            $companyDashboardDateFrom = $this->normalizeDate(
                (string) $request->query('from_date', ''),
                $companyDashboardDateFrom,
            );
            $companyDashboardDateTo = $this->normalizeDate(
                (string) $request->query('to_date', ''),
                $companyDashboardDateTo,
            );

            if ($companyDashboardDateFrom > $companyDashboardDateTo) {
                [$companyDashboardDateFrom, $companyDashboardDateTo] = [$companyDashboardDateTo, $companyDashboardDateFrom];
            }

            $requestedComparison = (string) $request->query('compare', 'previous_day');
            if (in_array($requestedComparison, ['previous_day', 'previous_week', 'previous_period'], true)) {
                $companyDashboardComparison = $requestedComparison;
            }

            $financeCompanies = Company::query()
                ->orderBy('name')
                ->get(['id', 'name', 'rfc']);
        } elseif ($section === 'branches') {
            $securityCompanies = SecurityCompany::query()
                ->withCount('branches')
                ->orderBy('name')
                ->get();

            $selectedSectionCompany = $this->resolveSectionCompany($request, $securityCompanies);

            $securityBranches = SecurityBranch::query()
                ->with([
                    'securityCompany',
                    'cameras' => fn ($query) => $query->orderBy('sort_order'),
                ])
                ->when(
                    $selectedSectionCompany,
                    fn ($query) => $query->where('security_company_id', $selectedSectionCompany->id),
                )
                ->orderBy('name')
                ->get();

            $selectedCameraBranch = $securityBranches
                ->firstWhere('id', $request->integer('branch_id'))
                ?? $securityBranches->first();
        } elseif ($section === 'analytics') {
            $securityCompanies = SecurityCompany::query()
                ->with(['branches' => fn ($query) => $query->orderBy('name')])
                ->withCount('branches')
                ->orderBy('name')
                ->get();

            $securityBranches = $securityCompanies
                ->flatMap(fn (SecurityCompany $company) => $company->branches)
                ->values();

            $selectedSectionCompany = $this->resolveSectionCompany($request, $securityCompanies);

            $selectedAnalyticsCompany = $selectedSectionCompany;

            $selectedAnalyticsBranch = $selectedAnalyticsCompany?->branches
                ->firstWhere('id', $request->integer('branch_id'));

            $analyticsDate = $this->normalizeDate(
                (string) $request->query('analytics_date', ''),
                $analyticsDate,
            );

            $analyticsRequested = (string) $request->query('show_analytics') === '1';
            $analyticsParameters = SecurityAnalyticsCatalog::parameters();
        } else {
            $securityCompanies = SecurityCompany::query()
                ->withCount('branches')
                ->orderBy('name')
                ->get();

            $selectedSectionCompany = $this->resolveSectionCompany($request, $securityCompanies);
        }

        return view('security.index', [
            'securitySectionKey' => $section,
            'securityCompanies' => $securityCompanies,
            'securityBranches' => $securityBranches,
            'selectedSecurityCompany' => $selectedSecurityCompany,
            'selectedSectionCompany' => $selectedSectionCompany,
            'selectedCameraBranch' => $selectedCameraBranch,
            'selectedAnalyticsCompany' => $selectedAnalyticsCompany,
            'selectedAnalyticsBranch' => $selectedAnalyticsBranch,
            'financeCompanies' => $financeCompanies,
            'analyticsParameters' => $analyticsParameters,
            'analyticsRequested' => $analyticsRequested,
            'analyticsDate' => $analyticsDate,
            'companyDashboardDateFrom' => $companyDashboardDateFrom,
            'companyDashboardDateTo' => $companyDashboardDateTo,
            'companyDashboardComparison' => $companyDashboardComparison,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:security_companies,name'],
            'entity_type' => ['required', 'in:company,business'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'rfc' => ['nullable', 'string', 'max:20', 'unique:security_companies,rfc'],
            'address' => ['nullable', 'string', 'max:1000'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'finance_company_id' => ['nullable', 'integer', 'exists:companies,id'],
        ], [], [
            'name' => 'nombre comercial',
            'entity_type' => 'tipo de registro',
            'legal_name' => 'razón social',
            'rfc' => 'RFC',
            'address' => 'dirección',
            'contact_name' => 'responsable de vigilancia',
            'contact_phone' => 'teléfono',
            'contact_email' => 'correo electrónico',
            'finance_company_id' => 'empresa relacionada en Finanzas',
        ]);

        $validated['rfc'] = filled($validated['rfc'] ?? null)
            ? strtoupper(trim($validated['rfc']))
            : null;

        $securityCompany = SecurityCompany::create($validated);

        return redirect()
            ->route('security.index', ['company' => $securityCompany->id])
            ->with('status', 'Empresa o negocio de Vigilancia guardado.');
    }

    public function storeBranch(Request $request): RedirectResponse
    {
        $securityCompanyId = $request->integer('security_company_id');
        $validated = $request->validate([
            'security_company_id' => ['required', 'integer', 'exists:security_companies,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('security_branches', 'name')->where('security_company_id', $securityCompanyId),
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('security_branches', 'code')->where('security_company_id', $securityCompanyId),
            ],
            'description' => ['nullable', 'string', 'max:1500'],
            'address' => ['required', 'string', 'max:1000'],
            'country' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:255'],
            'timezone' => ['required', Rule::in([
                'America/Mexico_City',
                'America/Cancun',
                'America/Chihuahua',
                'America/Tijuana',
            ])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'analytics_enabled' => ['nullable', 'boolean'],
            'alerts_enabled' => ['nullable', 'boolean'],
            'camera_urls' => ['nullable', 'array', 'max:24'],
            'camera_urls.*' => ['array:name,url'],
            'camera_urls.*.name' => ['nullable', 'string', 'max:120'],
            'camera_urls.*.url' => ['nullable', 'string', 'max:2048', 'url:http,https,rtsp,rtsps'],
        ], [], [
            'security_company_id' => 'empresa o negocio',
            'name' => 'nombre de la sucursal',
            'code' => 'clave de la sucursal',
            'description' => 'descripción',
            'address' => 'dirección',
            'country' => 'país',
            'state' => 'estado o provincia',
            'city' => 'ciudad',
            'postal_code' => 'código postal',
            'phone' => 'teléfono',
            'email' => 'correo electrónico',
            'timezone' => 'zona horaria',
            'status' => 'estatus',
            'camera_urls.*.name' => 'nombre de la cámara',
            'camera_urls.*.url' => 'URL de la cámara',
        ]);

        $validated['name'] = trim($validated['name']);
        $validated['code'] = filled($validated['code'] ?? null)
            ? strtoupper(trim($validated['code']))
            : null;
        $validated['analytics_enabled'] = $request->boolean('analytics_enabled');
        $validated['alerts_enabled'] = $request->boolean('alerts_enabled');
        $cameraUrls = $this->normalizeCameraUrls($validated['camera_urls'] ?? []);
        unset($validated['camera_urls']);

        $securityBranch = DB::transaction(function () use ($validated, $cameraUrls): SecurityBranch {
            $securityBranch = SecurityBranch::create($validated);
            $securityBranch->cameras()->createMany($cameraUrls);

            return $securityBranch;
        });

        return redirect()
            ->route('security.index', [
                'section' => 'branches',
                'company_id' => $validated['security_company_id'],
                'branch_id' => $securityBranch->id,
            ]);
    }

    public function updateBranch(Request $request, SecurityBranch $securityBranch): RedirectResponse
    {
        $securityCompanyId = (int) $securityBranch->security_company_id;
        $request->merge(['security_company_id' => $securityCompanyId]);

        $validated = $request->validate([
            'security_company_id' => ['required', 'integer', 'exists:security_companies,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('security_branches', 'name')
                    ->where('security_company_id', $securityCompanyId)
                    ->ignore($securityBranch->id),
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('security_branches', 'code')
                    ->where('security_company_id', $securityCompanyId)
                    ->ignore($securityBranch->id),
            ],
            'description' => ['nullable', 'string', 'max:1500'],
            'address' => ['required', 'string', 'max:1000'],
            'country' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:255'],
            'timezone' => ['required', Rule::in([
                'America/Mexico_City',
                'America/Cancun',
                'America/Chihuahua',
                'America/Tijuana',
            ])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'analytics_enabled' => ['nullable', 'boolean'],
            'alerts_enabled' => ['nullable', 'boolean'],
            'camera_urls' => ['nullable', 'array', 'max:24'],
            'camera_urls.*' => ['array:name,url'],
            'camera_urls.*.name' => ['nullable', 'string', 'max:120'],
            'camera_urls.*.url' => ['nullable', 'string', 'max:2048', 'url:http,https,rtsp,rtsps'],
        ]);

        $validated['name'] = trim($validated['name']);
        $validated['code'] = filled($validated['code'] ?? null)
            ? strtoupper(trim($validated['code']))
            : null;
        $validated['analytics_enabled'] = $request->boolean('analytics_enabled');
        $validated['alerts_enabled'] = $request->boolean('alerts_enabled');
        $cameraUrls = $this->normalizeCameraUrls($validated['camera_urls'] ?? []);
        unset($validated['camera_urls']);

        DB::transaction(function () use ($securityBranch, $validated, $cameraUrls): void {
            $securityBranch->update($validated);
            $securityBranch->cameras()->delete();
            $securityBranch->cameras()->createMany($cameraUrls);
        });

        return redirect()
            ->route('security.index', [
                'section' => 'branches',
                'company_id' => $securityCompanyId,
                'branch_id' => $securityBranch->id,
            ])
            ->with('security_branch_updated', true);
    }

    private function normalizeCameraUrls(array $cameraUrls): array
    {
        return collect($cameraUrls)
            ->filter(fn (array $camera) => filled($camera['url'] ?? null))
            ->values()
            ->map(function (array $camera, int $index): array {
                return [
                    'name' => filled($camera['name'] ?? null)
                        ? trim((string) $camera['name'])
                        : 'Cámara '.str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                    'stream_url' => trim((string) $camera['url']),
                    'sort_order' => $index,
                ];
            })
            ->all();
    }

    private function normalizeDate(string $date, string $fallback): string
    {
        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $parts)) {
            return $fallback;
        }

        return checkdate((int) $parts[2], (int) $parts[3], (int) $parts[1])
            ? $date
            : $fallback;
    }

    private function resolveSectionCompany(Request $request, Collection $securityCompanies): ?SecurityCompany
    {
        $selectedCompany = $securityCompanies
            ->firstWhere('id', $request->integer('company_id'))
            ?? $securityCompanies->firstWhere('id', (int) $request->session()->get('security_company_id'))
            ?? $securityCompanies->first();

        if ($selectedCompany) {
            $request->session()->put('security_company_id', $selectedCompany->id);
        }

        return $selectedCompany;
    }
}
