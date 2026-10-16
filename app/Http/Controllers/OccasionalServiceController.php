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
use Illuminate\Validation\ValidationException;

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
            'bank' => ['nullable', 'string', 'max:120'],
            'payer_account' => ['nullable', 'string', 'max:80'],
            'amount' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:9999999999.99'],
            'due_date' => ['required', 'date', 'after_or_equal:today', 'before_or_equal:'.now()->addMonthsNoOverflow(12)->endOfMonth()->toDateString()],
        ]);

        $existingEntry = $newEntry ? null : OccasionalServiceEntry::with('category')->findOrFail($validated['occasional_service_entry_id']);
        $category = $newEntry ? OccasionalService::findOrFail($validated['category_id']) : $existingEntry->category;
        $isFuelPayment = $category->name === 'Vale de Gasolina';
        $allocationRows = [];

        if ($isFuelPayment) {
            $fuelProvider = $category->entries()
                ->where('reference_number', OccasionalServiceEntry::FUEL_PROVIDER_REFERENCE)
                ->firstOrFail();
            if ($newEntry || $existingEntry->id !== $fuelProvider->id) {
                throw ValidationException::withMessages([
                    'occasional_service_entry_id' => 'Selecciona Efectivale como cuenta concentradora para Vale de Gasolina.',
                ]);
            }

            $allocationRows = $request->validate([
                'allocations' => ['required', 'array', 'min:1', 'max:500'],
                'allocations.*.entry_id' => [
                    'required', 'integer', 'distinct',
                    Rule::exists('occasional_service_entries', 'id')->where('occasional_service_id', $category->id),
                    Rule::notIn([$fuelProvider->id]),
                ],
                'allocations.*.amount' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:9999999999.99'],
            ], [
                'allocations.required' => 'Asigna el pago a al menos una tarjeta o referencia.',
                'allocations.*.entry_id.distinct' => 'Cada tarjeta o referencia solo puede aparecer una vez.',
                'allocations.*.entry_id.exists' => 'Selecciona una referencia de Vale de Gasolina registrada.',
                'allocations.*.entry_id.not_in' => 'La cuenta concentradora no puede recibir una asignación.',
            ])['allocations'];

            $assignedCents = array_sum(array_map(fn (array $row) => $this->cents($row['amount']), $allocationRows));
            if ($assignedCents !== $this->cents($validated['amount'])) {
                throw ValidationException::withMessages([
                    'allocations' => 'El total asignado a las tarjetas debe ser igual al monto del pago.',
                ]);
            }
        }

        $payment = DB::transaction(function () use ($validated, $newEntry, $existingEntry, $category, $allocationRows) {
            $entry = $newEntry
                ? $category->entries()->create([
                    'name' => $validated['name'],
                    'reference_number' => $validated['reference_number'],
                    'bank' => $validated['bank'] ?? null,
                    'payer_account' => $validated['payer_account'] ?? null,
                    'created_by' => Auth::id(),
                ])
                : $existingEntry;

            $payment = OccasionalServicePayment::create([
                'occasional_service_entry_id' => $entry->id,
                'amount' => $validated['amount'],
                'due_date' => $validated['due_date'],
                'created_by' => Auth::id(),
            ]);

            if ($allocationRows) {
                $recipients = OccasionalServiceEntry::whereKey(array_column($allocationRows, 'entry_id'))->get()->keyBy('id');
                $payment->allocations()->createMany(array_map(fn (array $row) => [
                    'occasional_service_entry_id' => $row['entry_id'],
                    'recipient_name' => $recipients[$row['entry_id']]->name,
                    'reference_number' => $recipients[$row['entry_id']]->reference_number,
                    'amount' => $row['amount'],
                ], $allocationRows));
            }

            return $payment;
        });

        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => OccasionalServicePayment::class,
            'auditable_id' => $payment->id,
            'action' => 'occasional_service_payment_scheduled',
            'description' => "Pago ocasional {$payment->entry->reference_number} programado para {$payment->due_date->toDateString()}".
                ($allocationRows ? ' con '.count($allocationRows).' tarjetas asignadas.' : '.'),
        ]);

        return redirect()->route('services.months', ['month' => $payment->due_date->format('Y-m')])
            ->with('status', 'Pago ocasional agregado a Servicios Mensuales.');
    }

    private function cents(string $amount): int
    {
        [$whole, $fraction] = array_pad(explode('.', $amount, 2), 2, '');

        return ((int) $whole * 100) + (int) str_pad($fraction, 2, '0');
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
            'bank' => ['nullable', 'string', 'max:120'],
            'payer_account' => ['nullable', 'string', 'max:80'],
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
                'bank' => $validated['bank'] ?? null,
                'payer_account' => $validated['payer_account'] ?? null,
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

    public function update(Request $request, OccasionalServiceEntry $entry)
    {
        $this->ensureServices($request);
        abort_if($entry->reference_number === OccasionalServiceEntry::FUEL_PROVIDER_REFERENCE
            && $entry->category->name === 'Vale de Gasolina', 403);

        $validated = $request->validate([
            'category_id' => ['required', 'integer', Rule::exists('occasional_services', 'id')],
            'name' => ['required', 'string', 'max:100'],
            'reference_number' => [
                'required', 'string', 'max:100',
                Rule::unique('occasional_service_entries', 'reference_number')
                    ->where('occasional_service_id', $request->input('category_id'))
                    ->ignore($entry->id),
            ],
            'bank' => ['nullable', 'string', 'max:120'],
            'payer_account' => ['nullable', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $entry->occasional_service_id = $validated['category_id'];
        $entry->fill([
            'name' => $validated['name'],
            'reference_number' => $validated['reference_number'],
            'bank' => $validated['bank'] ?? null,
            'payer_account' => $validated['payer_account'] ?? null,
            'description' => $validated['description'] ?? null,
        ])->save();

        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => OccasionalServiceEntry::class,
            'auditable_id' => $entry->id,
            'action' => 'occasional_service_updated',
            'description' => "Servicio ocasional {$entry->name} ({$entry->reference_number}) actualizado.",
        ]);

        return redirect()->route('services.catalog', ['occasional' => $entry->occasional_service_id])
            ->with('status', 'Información del servicio ocasional actualizada.');
    }

    public function export(Request $request)
    {
        $this->ensureServices($request);
        $fuelCategoryId = OccasionalService::where('name', 'Vale de Gasolina')->value('id');

        return response()->streamDownload(function () use ($fuelCategoryId) {
            $output = fopen('php://output', 'wb');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Categoría', 'Servicio ocasional', 'Número de referencia', 'Banco', 'Cuenta pagadora', 'Descripción'], ',', '"', '');

            OccasionalServiceEntry::query()
                ->when($fuelCategoryId, fn ($builder) => $builder->where(fn ($inner) => $inner
                    ->where('occasional_service_id', '!=', $fuelCategoryId)
                    ->orWhere('reference_number', '!=', OccasionalServiceEntry::FUEL_PROVIDER_REFERENCE)))
                ->with('category')->lazyById()->each(function (OccasionalServiceEntry $entry) use ($output) {
                fputcsv($output, [
                    $this->csvCell($entry->category->name),
                    $this->csvCell($entry->name),
                    $this->csvCell($entry->reference_number),
                    $this->csvCell($entry->bank ?? ''),
                    $this->csvCell($entry->payer_account ?? ''),
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
