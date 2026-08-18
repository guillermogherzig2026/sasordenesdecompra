<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Provider;
use App\Models\ProviderBusinessLine;
use App\Models\ProviderBusinessSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BuyerProviderController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureBuyer();
        $constructionContext = $this->isConstructionContext($request);

        $query = trim((string) $request->query('q'));
        $providers = Provider::with(['buyer', 'businessSubcategory'])
            ->when(Auth::user()?->role !== 'superadmin', fn ($builder) => $builder->where('buyer_id', Auth::id()))
            ->when($query, function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('business_name', 'like', "%{$query}%")
                        ->orWhere('rfc', 'like', "%{$query}%")
                        ->orWhere('business_line', 'like', "%{$query}%")
                        ->orWhere('bank', 'like', "%{$query}%");
                });
            })
            ->latest()
            ->get();

        return view('buyer.providers.index', [
            'providers' => $providers,
            'businessLines' => ProviderBusinessLine::with(['subcategories' => fn ($subcategories) => $subcategories
                ->where('active', true)
                ->orderBy('name')])
                ->where('active', true)
                ->orderBy('name')
                ->get(),
            'query' => $query,
            'constructionContext' => $constructionContext,
            'providerRoutePrefix' => $constructionContext ? 'construction.providers' : 'buyer.providers',
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureBuyer();
        $constructionContext = $this->isConstructionContext($request);

        $validated = $this->providerPayload($request);

        $provider = Provider::create([
            ...$validated,
            'buyer_id' => Auth::id(),
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => Provider::class,
            'auditable_id' => $provider->id,
            'action' => 'provider_created',
            'description' => $constructionContext
                ? "Proveedor {$provider->business_name} dado de alta para Administracion de obra."
                : "Proveedor {$provider->business_name} dado de alta por comprador.",
        ]);

        return redirect()
            ->route($constructionContext ? 'construction.providers.index' : 'buyer.providers.index')
            ->with('status', 'Proveedor registrado.');
    }

    public function update(Request $request, Provider $provider)
    {
        $this->ensureOwner($provider);
        $constructionContext = $this->isConstructionContext($request);

        $provider->update($this->providerPayload($request));

        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => Provider::class,
            'auditable_id' => $provider->id,
            'action' => 'provider_updated',
            'description' => $constructionContext
                ? "Proveedor {$provider->business_name} actualizado desde Administracion de obra."
                : "Proveedor {$provider->business_name} actualizado por comprador.",
        ]);

        return redirect()
            ->route($constructionContext ? 'construction.providers.index' : 'buyer.providers.index')
            ->with('status', 'Proveedor actualizado.');
    }

    private function providerPayload(Request $request): array
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'rfc' => ['required', 'string', 'max:20'],
            'business_line_id' => ['required', 'integer', Rule::exists('provider_business_lines', 'id')->where('active', true)],
            'business_subcategory_id' => ['nullable', 'integer', Rule::exists('provider_business_subcategories', 'id')->where('active', true)],
            'bank' => ['required', 'string', 'max:120'],
            'account_number' => ['required', 'string', 'max:40'],
            'clabe' => ['required', 'string', 'size:18'],
            'reference' => ['nullable', 'string', 'max:255'],
        ]);

        $line = ProviderBusinessLine::findOrFail($validated['business_line_id']);
        $subcategory = null;

        if (! empty($validated['business_subcategory_id'])) {
            $subcategory = ProviderBusinessSubcategory::query()
                ->where('provider_business_line_id', $line->id)
                ->where('active', true)
                ->findOrFail($validated['business_subcategory_id']);
        }

        return [
            'business_name' => $validated['business_name'],
            'rfc' => $validated['rfc'],
            'business_line' => $line->name,
            'provider_business_line_id' => $line->id,
            'provider_business_subcategory_id' => $subcategory?->id,
            'provider_business_subcategory' => $subcategory?->name,
            'bank' => $validated['bank'],
            'account_number' => $validated['account_number'],
            'clabe' => $validated['clabe'],
            'reference' => $validated['reference'] ?? null,
        ];
    }

    private function ensureBuyer(): void
    {
        abort_unless(Auth::user()?->canAccessBuyerSubrole('purchases'), 403);
    }

    private function ensureOwner(Provider $provider): void
    {
        $this->ensureBuyer();

        abort_unless(Auth::user()?->role === 'superadmin' || (int) $provider->buyer_id === Auth::id(), 403);
    }

    private function isConstructionContext(Request $request): bool
    {
        return $request->routeIs('construction.providers.*');
    }
}
