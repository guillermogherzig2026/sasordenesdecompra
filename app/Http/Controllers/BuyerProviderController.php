<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Provider;
use App\Models\ProviderBusinessLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BuyerProviderController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureBuyer();

        $query = trim((string) $request->query('q'));
        $providers = Provider::with('buyer')
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
            'businessLines' => ProviderBusinessLine::where('active', true)->orderBy('name')->get(),
            'query' => $query,
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureBuyer();

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
            'description' => "Proveedor {$provider->business_name} dado de alta por comprador.",
        ]);

        return redirect()->route('buyer.providers.index')->with('status', 'Proveedor registrado.');
    }

    public function update(Request $request, Provider $provider)
    {
        $this->ensureOwner($provider);

        $provider->update($this->providerPayload($request, $provider));

        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => Provider::class,
            'auditable_id' => $provider->id,
            'action' => 'provider_updated',
            'description' => "Proveedor {$provider->business_name} actualizado por comprador.",
        ]);

        return redirect()->route('buyer.providers.index')->with('status', 'Proveedor actualizado.');
    }

    private function providerPayload(Request $request, ?Provider $provider = null): array
    {
        $rfcRule = Rule::unique('providers', 'rfc')
            ->where(fn ($query) => $query->where('buyer_id', Auth::id()));

        if ($provider) {
            $rfcRule->ignore($provider->id);
        }

        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'rfc' => ['required', 'string', 'max:20', $rfcRule],
            'business_line_id' => ['required', 'integer', Rule::exists('provider_business_lines', 'id')->where('active', true)],
            'bank' => ['required', 'string', 'max:120'],
            'account_number' => ['required', 'string', 'max:40'],
            'clabe' => ['required', 'string', 'size:18'],
            'reference' => ['nullable', 'string', 'max:255'],
        ]);

        $line = ProviderBusinessLine::findOrFail($validated['business_line_id']);

        return [
            'business_name' => $validated['business_name'],
            'rfc' => strtoupper(trim($validated['rfc'])),
            'business_line' => $line->name,
            'provider_business_line_id' => $line->id,
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
}
