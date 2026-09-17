<?php

namespace App\Http\Controllers;

use App\Models\AiAgent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AgentCenterController extends Controller
{
    private const CONTEXT_ROUTES = [
        'superadmin' => 'superadmin.agents.index',
        'finanzas' => 'finance.agents.index',
        'plazas' => 'superadmin.plazas.agents.index',
    ];

    public function index(Request $request): View|RedirectResponse
    {
        $this->authorizeAccess();

        if ($request->routeIs('superadmin.agents.index') && $request->query('area') === 'finanzas') {
            return redirect()->route('finance.agents.index');
        }

        if ($request->routeIs('superadmin.agents.index') && $request->query('area') === 'plazas') {
            return redirect()->route('superadmin.plazas.agents.index');
        }

        $agents = AiAgent::query()->orderBy('name')->get();
        $selected = (string) $request->query('selected', '');

        if ($selected !== 'all' && (! ctype_digit($selected) || ! $agents->contains('id', (int) $selected))) {
            $selected = '';
        }

        return view('superadmin.agent-center', [
            'agents' => $agents,
            'context' => $this->contextForRequest($request),
            'selected' => $selected,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAccess();
        $validated = $this->validateAgent($request);

        $agent = AiAgent::create([
            'name' => trim($validated['name']),
            'description' => $this->nullableTrimmed($validated['description'] ?? null),
            'instructions' => $this->nullableTrimmed($validated['instructions'] ?? null),
            'created_by' => Auth::id(),
        ]);

        return $this->redirectToContext($validated['context'], (string) $agent->id)
            ->with('status', 'Agente creado correctamente.');
    }

    public function update(Request $request, AiAgent $aiAgent): RedirectResponse
    {
        $this->authorizeAccess();
        $validated = $this->validateAgent($request, $aiAgent);

        $aiAgent->update([
            'name' => trim($validated['name']),
            'description' => $this->nullableTrimmed($validated['description'] ?? null),
            'instructions' => $this->nullableTrimmed($validated['instructions'] ?? null),
        ]);

        return $this->redirectToContext($validated['context'], (string) $aiAgent->id)
            ->with('status', 'Agente actualizado correctamente.');
    }

    public function toggle(Request $request, AiAgent $aiAgent): RedirectResponse
    {
        $this->authorizeAccess();
        $validated = $request->validate([
            'context' => ['required', Rule::in(array_keys(self::CONTEXT_ROUTES))],
            'is_active' => ['required', 'boolean'],
            'selected' => ['nullable', 'string', 'max:20'],
        ]);

        $aiAgent->update(['is_active' => (bool) $validated['is_active']]);

        $selected = (string) ($validated['selected'] ?? $aiAgent->id);

        return $this->redirectToContext($validated['context'], $selected);
    }

    private function validateAgent(Request $request, ?AiAgent $agent = null): array
    {
        return $request->validate([
            'context' => ['required', Rule::in(array_keys(self::CONTEXT_ROUTES))],
            'agent_form' => ['required', 'in:1'],
            'editing_agent_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:120', Rule::unique('ai_agents', 'name')->ignore($agent?->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'instructions' => ['nullable', 'string', 'max:20000'],
        ], [
            'name.required' => 'Escribe el nombre del agente.',
            'name.max' => 'El nombre no debe superar los 120 caracteres.',
            'name.unique' => 'Ya existe un agente con este nombre.',
            'description.max' => 'La descripcion no debe superar los 1000 caracteres.',
            'instructions.max' => 'Las instrucciones no deben superar los 20000 caracteres.',
        ]);
    }

    private function contextForRequest(Request $request): string
    {
        return match (true) {
            $request->routeIs('finance.agents.*') => 'finanzas',
            $request->routeIs('superadmin.plazas.agents.*') => 'plazas',
            default => 'superadmin',
        };
    }

    private function redirectToContext(string $context, string $selected = ''): RedirectResponse
    {
        $parameters = $selected !== '' ? ['selected' => $selected] : [];

        return redirect()->route(self::CONTEXT_ROUTES[$context], $parameters);
    }

    private function nullableTrimmed(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function authorizeAccess(): void
    {
        abort_unless(Auth::user()?->canAccessRole('superadmin'), 403);
    }
}
