<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\OccasionalService;
use App\Models\OccasionalServiceEntry;
use App\Models\OccasionalServicePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OccasionalServiceController extends Controller
{
    public function storePayment(Request $request)
    {
        $this->ensureServices($request, true);
        abort_unless($request->user()->canNavigateTo('services.months'), 403);

        $newEntry = $request->input('mode') === 'new';
        $validated = $request->validate([
            'mode' => ['required', Rule::in(['existing', 'new'])],
            'occasional_service_entry_id' => [$newEntry ? 'nullable' : 'required', 'integer', Rule::exists('occasional_service_entries', 'id')],
            'category_id' => [$newEntry ? 'required' : 'nullable', 'integer', Rule::exists('occasional_services', 'id')],
            'name' => [$newEntry ? 'required' : 'nullable', 'string', 'max:100'],
            'reference_number' => [
                $newEntry ? 'required' : 'nullable', 'string', 'max:100',
                ...($newEntry ? [Rule::unique('occasional_service_entries', 'reference_number')
                    ->where('occasional_service_id', $request->input('category_id'))] : []),
            ],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'due_date' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:'.now()->addMonthsNoOverflow(12)->endOfMonth()->toDateString()],
        ]);

        $payment = DB::transaction(function () use ($validated, $newEntry) {
            $entry = $newEntry
                ? OccasionalService::findOrFail($validated['category_id'])->entries()->create([
                    'name' => $validated['name'],
                    'reference_number' => $validated['reference_number'],
                    'created_by' => Auth::id(),
                ])
                : OccasionalServiceEntry::findOrFail($validated['occasional_service_entry_id']);

            return OccasionalServicePayment::create([
                'occasional_service_entry_id' => $entry->id,
                'amount' => $validated['amount'],
                'due_date' => $validated['due_date'],
                'created_by' => Auth::id(),
            ]);
        });

        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => OccasionalServicePayment::class,
            'auditable_id' => $payment->id,
            'action' => 'occasional_service_payment_scheduled',
            'description' => "Pago ocasional {$payment->entry->reference_number} programado para {$payment->due_date->toDateString()}.",
        ]);

        return redirect()->route('services.months', ['month' => $payment->due_date->format('Y-m')])
            ->with('status', 'Pago ocasional agregado a Servicios Mensuales.');
    }

    public function store(Request $request)
    {
        $this->ensureServices($request, true);

        $newCategory = blank($request->input('category_id'));
        $validated = $request->validate([
            'category_id' => ['nullable', 'integer', Rule::exists('occasional_services', 'id')],
            'name' => ['required', 'string', 'max:100', ...($newCategory ? [Rule::unique('occasional_services', 'name')] : [])],
            'reference_number' => [
                'required', 'string', 'max:100',
                ...($newCategory ? [] : [Rule::unique('occasional_service_entries', 'reference_number')
                    ->where('occasional_service_id', $request->input('category_id'))]),
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => [$newCategory ? 'required' : 'nullable', Rule::in(['parking', 'car', 'ticket', 'briefcase', 'wrench', 'star'])],
            'color' => [$newCategory ? 'required' : 'nullable', Rule::in(['cyan', 'blue', 'amber', 'rose', 'purple'])],
        ]);

        [$category, $entry] = DB::transaction(function () use ($validated, $newCategory) {
            $category = $newCategory ? OccasionalService::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'icon' => $validated['icon'],
                'color' => $validated['color'],
                'created_by' => Auth::id(),
            ]) : OccasionalService::findOrFail($validated['category_id']);

            $entry = $category->entries()->create([
                'name' => $validated['name'],
                'reference_number' => $validated['reference_number'],
                'description' => $validated['description'] ?? null,
                'created_by' => Auth::id(),
            ]);

            return [$category, $entry];
        });

        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => OccasionalServiceEntry::class,
            'auditable_id' => $entry->id,
            'action' => 'occasional_service_created',
            'description' => "Servicio ocasional {$entry->name} ({$entry->reference_number}) dado de alta.",
        ]);

        return redirect()->route('services.catalog', ['occasional' => $category->id])
            ->with('status', 'Servicio ocasional registrado.');
    }

    public function export(Request $request)
    {
        $this->ensureServices($request);

        return response()->streamDownload(function () {
            $output = fopen('php://output', 'wb');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Categoría', 'Servicio ocasional', 'Número de referencia', 'Descripción'], ',', '"', '');

            OccasionalServiceEntry::query()->with('category')->lazyById()->each(function (OccasionalServiceEntry $entry) use ($output) {
                fputcsv($output, [
                    $this->csvCell($entry->category->name),
                    $this->csvCell($entry->name),
                    $this->csvCell($entry->reference_number),
                    $this->csvCell($entry->description ?? ''),
                ], ',', '"', '');
            });

            fclose($output);
        }, 'servicios-ocasionales.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function ensureServices(Request $request, bool $create = false): void
    {
        $user = $request->user();
        abort_unless($user?->canAccessRole('services') || $user?->canAccessRole('finance'), 403);

        $permissions = $create
            ? ['services.create', 'finance.services.create']
            : ['services.catalog', 'finance.services.catalog'];
        abort_unless(collect($permissions)->contains(fn ($permission) => $user->canNavigateTo($permission)), 403);
    }

    private function csvCell(string $value): string
    {
        return preg_match('/^\s*[=+\-@]/u', $value) ? "'{$value}" : $value;
    }
}
