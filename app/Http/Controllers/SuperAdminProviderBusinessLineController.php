<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ProviderBusinessLine;
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
            'lines' => ProviderBusinessLine::withCount('providers')
                ->when($query, fn ($builder) => $builder->where('name', 'like', "%{$query}%"))
                ->orderBy('name')
                ->get(),
            'query' => $query,
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

        $this->audit($line, 'provider_business_line_created', "Giro {$line->name} creado por Super Administrador.");

        return redirect()->route('superadmin.provider-lines.index')->with('status', 'Giro de proveeduria creado.');
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

        $this->audit($providerLine, 'provider_business_line_updated', "Giro {$providerLine->name} actualizado por Super Administrador.");

        return redirect()->route('superadmin.provider-lines.index')->with('status', 'Giro de proveeduria actualizado.');
    }

    public function destroy(ProviderBusinessLine $providerLine)
    {
        $this->ensureSuperAdmin();

        if ($providerLine->providers()->exists()) {
            return back()->with('status', "No se puede eliminar {$providerLine->name} porque tiene proveedores relacionados.");
        }

        $name = $providerLine->name;
        $this->audit($providerLine, 'provider_business_line_deleted', "Giro {$name} eliminado por Super Administrador.");
        $providerLine->delete();

        return redirect()->route('superadmin.provider-lines.index')->with('status', "Giro {$name} eliminado.");
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
