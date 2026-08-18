<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Provider;
use App\Models\ProviderBusinessLine;
use App\Models\ProviderBusinessSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SuperAdminProviderBusinessLineController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureSuperAdmin();

        $query = trim((string) $request->query('q'));

        return view('superadmin.provider-business-lines', [
            'providers' => Provider::with(['buyer', 'businessLine', 'businessSubcategory'])
                ->orderBy('business_name')
                ->get(),
            'lines' => ProviderBusinessLine::withCount('providers')
                ->with([
                    'providers' => fn ($providers) => $providers
                        ->with(['buyer', 'businessSubcategory'])
                        ->orderBy('business_name'),
                    'subcategories' => fn ($subcategories) => $subcategories
                        ->withCount('providers')
                        ->with(['providers' => fn ($providers) => $providers
                            ->with('buyer')
                            ->orderBy('business_name')])
                        ->orderBy('name'),
                ])
                ->when($query, fn ($builder) => $builder->where('name', 'like', "%{$query}%"))
                ->orderBy('name')
                ->get(),
            'query' => $query,
        ]);
    }

    public function manage()
    {
        $this->ensureSuperAdmin();

        return view('superadmin.provider-business-line-management', [
            'lines' => ProviderBusinessLine::query()
                ->withCount(['providers', 'subcategories'])
                ->with(['subcategories' => fn ($subcategories) => $subcategories
                    ->withCount('providers')
                    ->orderBy('name')])
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureSuperAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:provider_business_lines,name'],
        ]);

        $line = ProviderBusinessLine::create([
            'name' => trim($validated['name']),
            'active' => true,
        ]);

        $this->audit($line, 'provider_business_line_created', "Categoria {$line->name} creada por Super Administrador.");

        return $this->redirectAfterMutation($request, 'Categoria creada.');
    }

    public function update(Request $request, ProviderBusinessLine $providerLine)
    {
        $this->ensureSuperAdmin();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80', Rule::unique('provider_business_lines', 'name')->ignore($providerLine->id)],
            'active' => ['nullable', 'boolean'],
        ]);

        $oldName = $providerLine->name;
        $providerLine->update([
            'name' => trim($validated['name']),
            'active' => $request->boolean('active'),
        ]);

        if ($oldName !== $providerLine->name) {
            $providerLine->providers()->update(['business_line' => $providerLine->name]);
        }

        $this->audit($providerLine, 'provider_business_line_updated', "Categoria {$providerLine->name} actualizada por Super Administrador.");

        return $this->redirectAfterMutation($request, 'Categoria actualizada.');
    }

    public function destroy(Request $request, ProviderBusinessLine $providerLine)
    {
        $this->ensureSuperAdmin();

        if ($providerLine->providers()->exists()) {
            return back()->with('status', "No se puede eliminar {$providerLine->name} porque tiene proveedores relacionados.");
        }

        $name = $providerLine->name;
        $this->audit($providerLine, 'provider_business_line_deleted', "Categoria {$name} eliminada por Super Administrador.");
        $providerLine->delete();

        return $this->redirectAfterMutation($request, "Categoria {$name} eliminada.");
    }

    public function storeSubcategory(Request $request, ProviderBusinessLine $providerLine)
    {
        $this->ensureSuperAdmin();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('provider_business_subcategories', 'name')
                    ->where(fn ($query) => $query->where('provider_business_line_id', $providerLine->id)),
            ],
        ]);

        $subcategory = $providerLine->subcategories()->create([
            'name' => trim($validated['name']),
            'active' => true,
        ]);

        $this->audit($subcategory, 'provider_business_subcategory_created', "Subcategoria {$subcategory->name} creada en {$providerLine->name}.");

        return $this->redirectAfterMutation($request, 'Subcategoria creada.');
    }

    public function updateSubcategory(Request $request, ProviderBusinessSubcategory $subcategory)
    {
        $this->ensureSuperAdmin();

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('provider_business_subcategories', 'name')
                    ->where(fn ($query) => $query->where('provider_business_line_id', $subcategory->provider_business_line_id))
                    ->ignore($subcategory->id),
            ],
            'active' => ['nullable', 'boolean'],
        ]);

        $oldName = $subcategory->name;
        $subcategory->update([
            'name' => trim($validated['name']),
            'active' => $request->boolean('active'),
        ]);

        if ($oldName !== $subcategory->name) {
            $subcategory->providers()->update(['provider_business_subcategory' => $subcategory->name]);
        }

        $this->audit($subcategory, 'provider_business_subcategory_updated', "Subcategoria {$subcategory->name} actualizada.");

        return $this->redirectAfterMutation($request, 'Subcategoria actualizada.');
    }

    public function destroySubcategory(Request $request, ProviderBusinessSubcategory $subcategory)
    {
        $this->ensureSuperAdmin();

        if ($subcategory->providers()->exists()) {
            return back()->with('status', "No se puede eliminar {$subcategory->name} porque tiene proveedores relacionados.");
        }

        $name = $subcategory->name;
        $this->audit($subcategory, 'provider_business_subcategory_deleted', "Subcategoria {$name} eliminada.");
        $subcategory->delete();

        return $this->redirectAfterMutation($request, "Subcategoria {$name} eliminada.");
    }

    private function redirectAfterMutation(Request $request, string $status)
    {
        $route = $request->input('return_to') === 'management'
            ? 'superadmin.provider-lines.manage'
            : 'superadmin.provider-lines.index';

        return redirect()->route($route)->with('status', $status);
    }

    private function audit($model, string $action, string $description): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => $model::class,
            'auditable_id' => $model->id,
            'action' => $action,
            'description' => $description,
        ]);
    }

    private function ensureSuperAdmin(): void
    {
        abort_unless(Auth::user()?->canAccessRole('superadmin'), 403);
    }
}
