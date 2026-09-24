<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Company;
use App\Support\MarketingWorkspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function index(Request $request, MarketingWorkspace $workspace): View
    {
        $companyQuery = Company::query()
            ->visibleTo($request->user())
            ->withCount(['campaigns', 'leads', 'conversations', 'users'])
            ->with(['whatsappNumbers' => fn ($query) => $query->where('status', 'active')]);

        $allCompanies = (clone $companyQuery)
            ->orderByDesc('active')
            ->orderBy('name')
            ->get();
        $companies = (clone $companyQuery)
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.(string) $request->string('search').'%'))
            ->when($request->filled('status'), fn ($query) => $query->where('status', (string) $request->string('status')))
            ->orderByDesc('active')
            ->orderBy('name')
            ->get();

        $selected = $workspace->selectedCompany($request);
        $company = $selected;
        $workspaceCompanies = $allCompanies;

        return view('marketing.leadflow.companies.index', compact('companies', 'selected', 'company', 'workspaceCompanies'));
    }

    public function create(): View
    {
        return view('marketing.leadflow.companies.form', ['company' => new Company]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(4));
        $data['active'] = $request->boolean('active');
        $data['is_favorite'] = $request->boolean('is_favorite');

        $company = Company::create($data);

        return redirect()->route('marketing.companies.index', ['company_id' => $company->id])
            ->with('status', 'Empresa creada correctamente.');
    }

    public function edit(Company $company): View
    {
        return view('marketing.leadflow.companies.form', compact('company'));
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['active'] = $request->boolean('active');
        $data['is_favorite'] = $request->boolean('is_favorite');
        $company->update($data);

        return redirect()->route('marketing.companies.index', ['company_id' => $company->id])->with('status', 'Empresa actualizada.');
    }

    public function toggle(Request $request, Company $company): RedirectResponse
    {
        $company->update(['active' => ! $company->active]);

        return back()->with('status', $company->active ? 'Empresa activada.' : 'Empresa desactivada.');
    }

    public function updateFavorite(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate(['favorite' => ['required', 'boolean']]);
        $company->update(['is_favorite' => (bool) $validated['favorite']]);

        return back()->with('status', $company->is_favorite
            ? 'Empresa agregada a favoritas.'
            : 'Empresa retirada de favoritas.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'domain' => ['nullable', 'string', 'max:160'],
            'industry' => ['nullable', 'string', 'max:120'],
            'logo_initials' => ['nullable', 'string', 'max:8'],
            'brand_color' => ['nullable', 'string', 'max:20'],
        ]);
    }
}
