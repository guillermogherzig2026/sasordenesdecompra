<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\OccasionalService;
use App\Models\OccasionalServiceEntry;
use App\Models\OccasionalServicePayment;
use App\Models\RecurringService;
use App\Models\RecurringServiceReceipt;
use App\Support\StoredFileResponse;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ServiceController extends Controller
{
    public const CATEGORIES = [
        'Telefonia Fija', 'Telefonia Celular', 'Internet', 'Luz', 'Agua', 'Predial',
        'Recoleccion de basura', 'Mantenimiento', 'Cuota de Mantenimiento de Condominio', 'Tenencia', 'Renta', 'Otros',
    ];

    public function create()
    {
        $this->ensureServices();

        return view('services.form', [
            'categories' => $this->serviceCategoryOptions(),
            'companies' => Company::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureServices();

        $validated = $this->servicePayload($request);

        $service = RecurringService::create([
            ...$validated,
            'folio' => $this->nextFolio(),
            'status' => 'active',
            'created_by' => Auth::id(),
        ]);

        $this->audit($service, 'service_created', "Servicio {$service->folio} dado de alta.");

        return redirect()->route('services.catalog')->with('status', 'Servicio registrado.');
    }

    public function edit(RecurringService $service)
    {
        $this->ensureServices();

        return view('services.form', [
            'categories' => $this->serviceCategoryOptions(),
            'companies' => Company::orderBy('name')->get(),
            'service' => $service,
        ]);
    }

    public function update(Request $request, RecurringService $service)
    {
        $this->ensureServices();

        $service->update($this->servicePayload($request));
        $this->audit($service, 'service_updated', "Servicio {$service->folio} actualizado.");

        return redirect()->route('services.catalog')->with('status', 'Servicio actualizado.');
    }

    public function catalog(Request $request)
    {
        $this->ensureServices();

        $query = trim((string) $request->query('q'));
        $category = trim((string) $request->query('category'));
        $company = trim((string) $request->query('company'));
        $branch = trim((string) $request->query('branch'));
        $status = trim((string) $request->query('status'));
        $status = in_array($status, ['active', 'paused', 'inactive'], true) ? $status : '';
        $companies = Company::query()->orderBy('name')->get(['id', 'name']);
        $occasionalServices = OccasionalService::query()->orderBy('id')->get();
        $selectedOccasionalService = $occasionalServices->firstWhere('id', (int) $request->query('occasional'));
        $isOccasionalView = $request->query('view') === 'occasional' || $selectedOccasionalService !== null;
        if ($isOccasionalView) {
            $category = '';
        }

        $occasionalEntries = $isOccasionalView ? OccasionalServiceEntry::query()
            ->with('category')
            ->when($selectedOccasionalService, fn ($builder) => $builder->where('occasional_service_id', $selectedOccasionalService->id))
            ->latest()
            ->paginate(20)
            ->withQueryString() : null;

        $services = $isOccasionalView ? null : RecurringService::query()
            ->when($query, fn($builder) => $builder->where(function ($inner) use ($query) {
                $inner->where('folio', 'like', "%{$query}%")
                    ->orWhere('service_name', 'like', "%{$query}%")
                    ->orWhere('provider', 'like', "%{$query}%")
                    ->orWhere('company_name', 'like', "%{$query}%")
                    ->orWhere('category', 'like', "%{$query}%")
                    ->orWhere('service_number', 'like', "%{$query}%")
                    ->orWhere('branch', 'like', "%{$query}%");
            }))
            ->when($category !== '', fn ($builder) => $builder->where('category', $category))
            ->when($company !== '', fn ($builder) => $builder->where('company_name', $company))
            ->when($branch === '__none', fn ($builder) => $builder->where(function ($inner) {
                $inner->whereNull('branch')->orWhere('branch', '');
            }))
            ->when($branch !== '' && $branch !== '__none', fn ($builder) => $builder->where('branch', $branch))
            ->when($status !== '', fn ($builder) => $builder->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('services.catalog', [
            'services' => $services,
            'occasionalServices' => $occasionalServices,
            'occasionalEntries' => $occasionalEntries,
            'selectedOccasionalService' => $selectedOccasionalService,
            'isOccasionalView' => $isOccasionalView,
            'category' => $category,
            'company' => $company,
            'categories' => $this->serviceCategoryOptions(),
            'companies' => $companies,
            'canCreateService' => $request->user()->canNavigateTo('services.create')
                || $request->user()->canNavigateTo('finance.services.create'),
        ]);
    }

    private function serviceCategoryOptions(): Collection
    {
        return collect(self::CATEGORIES)
            ->merge(RecurringService::query()->whereNotNull('category')->distinct()->pluck('category'))
            ->filter()->unique()->values();
    }

    public function months(Request $request)
    {
        $this->ensureServices();

        $months = $this->monthsPayload();

        return view('services.months', [
            'months' => $months,
            'next_week_total' => $this->nextWeekTotal($months),
            'occasional_entries' => OccasionalServiceEntry::with('category')->orderBy('name')->get(),
            'occasional_categories' => OccasionalService::orderBy('name')->get(),
            'canCreateOccasionalPayment' => ($request->user()->canNavigateTo('services.create')
                || $request->user()->canNavigateTo('finance.services.create')),
        ]);
    }

    public function history(Request $request)
    {
        $this->ensureServiceHistory();
        $demoPaymentMonth = app()->environment('local') ? Carbon::create(2026, 9, 1) : null;

        return view('services.months', [
            'months' => $this->historyMonthsPayload($demoPaymentMonth),
            'next_week_total' => 0,
            'title' => 'Historial de Servicios',
            'downloadReport' => null,
            'monthSubtitle' => 'Servicios pagados durante este mes.',
            'emptyMessage' => 'No hay servicios pagados durante este mes.',
            'historyMode' => true,
            'demoPaymentMonth' => $demoPaymentMonth?->format('Y-m'),
            'metricLabels' => [
                'total' => 'Monto pagado del mes',
                'paid' => 'Monto total pagado',
                'next_week' => 'Pagos pendientes esta semana',
                'pending' => 'Monto pendiente por pagar',
            ],
        ]);
    }

    public function receiptForm(RecurringService $service, string $dueDate)
    {
        $this->ensureServices();
        abort_if($service->is_domiciled, 403);

        $occurrence = $this->occurrenceForDueDate($service, $dueDate);
        abort_unless($occurrence, 404);

        return view('services.receipt', [
            'service' => $service,
            'occurrence' => $occurrence,
            'receipt' => $this->receiptFor($service, $dueDate),
        ]);
    }

    public function storeReceipt(Request $request, RecurringService $service, string $dueDate)
    {
        $this->ensureServices();
        abort_if($service->is_domiciled, 403);

        $occurrence = $this->occurrenceForDueDate($service, $dueDate);
        abort_unless($occurrence, 404);

        $validated = $request->validate([
            'support_file' => ['required', 'file', 'max:10240'],
            'support_on' => ['required', 'date'],
        ]);

        $path = $request->file('support_file')->store('service-supports');
        RecurringServiceReceipt::updateOrCreate(
            ['recurring_service_id' => $service->id, 'due_date' => $dueDate],
            [
                'period_start' => $occurrence['period_start'],
                'support_file_path' => $path,
                'support_original_name' => $request->file('support_file')->getClientOriginalName(),
                'support_on' => $validated['support_on'],
                'status' => 'support-loaded',
            ],
        );

        $this->audit($service, 'service_support_loaded', "Recibo cargado para {$service->folio} periodo {$dueDate}.");

        return redirect()->route('services.months')->with('status', 'Factura cargada. Finanzas ya puede registrar el pago.');
    }

    public function supportFile(RecurringServiceReceipt $receipt)
    {
        $this->ensureServices();

        return StoredFileResponse::inline($receipt->support_file_path, $receipt->support_original_name);
    }

    public function downloadSupportPdf(RecurringServiceReceipt $receipt)
    {
        $this->ensureServiceHistory();
        abort_unless(filled($receipt->support_file_path), 404);

        $filename = $receipt->support_original_name ?: $receipt->support_file_path;
        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'pdf') {
            return StoredFileResponse::download($receipt->support_file_path, $receipt->support_original_name);
        }

        $service = $receipt->recurringService;
        abort_unless($service, 404);

        return $this->paymentPdfResponse([
            'type' => 'invoice',
            'demo' => false,
            'folio' => $service->folio,
            'holder' => $service->holder ?: $service->company_name,
            'branch' => $service->display_branch,
            'service' => $service->service_name,
            'provider' => $service->provider,
            'reference' => $service->reference,
            'paidOn' => $receipt->due_date?->format('d/m/Y'),
            'amount' => (float) ($receipt->amount ?? $service->cost),
            'originalName' => $receipt->support_original_name,
            'imageData' => $this->paymentImageData($receipt->support_file_path),
        ], 'factura-servicio-'.$receipt->id.'.pdf', true);
    }

    public function paymentFile(RecurringServiceReceipt $receipt)
    {
        $this->ensureServices();

        return StoredFileResponse::inline($receipt->payment_file_path, $receipt->payment_original_name);
    }

    public function downloadPaymentPdf(RecurringServiceReceipt $receipt)
    {
        $this->ensureServiceHistory();
        abort_unless($receipt->isPaid(), 404);

        $filename = $receipt->payment_original_name ?: $receipt->payment_file_path;
        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'pdf') {
            return StoredFileResponse::download($receipt->payment_file_path, $receipt->payment_original_name);
        }

        $service = $receipt->recurringService;
        abort_unless($service, 404);

        return $this->paymentPdfResponse([
            'type' => 'payment',
            'demo' => false,
            'folio' => $service->folio,
            'holder' => $service->holder ?: $service->company_name,
            'branch' => $service->display_branch,
            'service' => $service->service_name,
            'provider' => $service->provider,
            'reference' => $service->reference,
            'paidOn' => $receipt->payment_paid_on?->format('d/m/Y') ?: $receipt->due_date?->format('d/m/Y'),
            'amount' => (float) ($receipt->amount ?? $service->cost),
            'originalName' => $receipt->payment_original_name,
            'imageData' => $this->paymentImageData($receipt->payment_file_path),
        ], 'comprobante-servicio-'.$receipt->id.'.pdf', true);
    }

    public function demoPaymentFile(Request $request)
    {
        $this->ensureServiceHistory();
        abort_unless(app()->environment('local'), 404);

        return $this->paymentPdfResponse([
            'type' => 'payment',
            'demo' => true,
            'folio' => 'DEMO-SEP-2026',
            'holder' => 'Empresa de ejemplo',
            'branch' => 'Sucursal demo',
            'service' => 'Internet empresarial (demo)',
            'provider' => 'Proveedor de ejemplo',
            'reference' => 'PAGO-DEMO-SEP-2026',
            'paidOn' => '18/09/2026',
            'amount' => 1250,
            'originalName' => null,
            'imageData' => null,
        ], 'comprobante-demo-septiembre-2026.pdf', $request->boolean('download'));
    }

    public function demoInvoiceFile(Request $request)
    {
        $this->ensureServiceHistory();
        abort_unless(app()->environment('local'), 404);

        return $this->paymentPdfResponse([
            'type' => 'invoice',
            'demo' => true,
            'folio' => 'DEMO-SEP-2026',
            'holder' => 'Empresa de ejemplo',
            'branch' => 'Sucursal demo',
            'service' => 'Internet empresarial (demo)',
            'provider' => 'Proveedor de ejemplo',
            'reference' => 'DEMO-001',
            'paidOn' => '18/09/2026',
            'amount' => 1250,
            'originalName' => null,
            'imageData' => null,
        ], 'factura-demo-septiembre-2026.pdf', $request->boolean('download'));
    }

    private function paymentPdfResponse(array $payment, string $filename, bool $download)
    {
        $pdf = new Dompdf();
        $pdf->loadHtml(view('services.payment-receipt-pdf', ['payment' => $payment])->render());
        $pdf->setPaper('letter');
        $pdf->render();

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => ($download ? 'attachment' : 'inline').'; filename="'.$filename.'"',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    private function paymentImageData(?string $path): ?string
    {
        if (! filled($path) || ! Storage::disk('local')->exists($path)) {
            return null;
        }

        $bytes = Storage::disk('local')->get($path);
        $size = @getimagesizefromstring($bytes);
        if (! $size || $size[0] * $size[1] > 20_000_000) {
            return null;
        }

        $mime = $size['mime'];
        if ($mime === 'image/webp') {
            $image = @imagecreatefromstring($bytes);
            if (! $image) {
                return null;
            }
            ob_start();
            imagepng($image);
            $bytes = ob_get_clean();
            imagedestroy($image);
            $mime = 'image/png';
        }

        return in_array($mime, ['image/png', 'image/jpeg'], true)
            ? 'data:'.$mime.';base64,'.base64_encode($bytes)
            : null;
    }

    public function updateAmount(Request $request, RecurringService $service, string $dueDate)
    {
        $this->ensureServices();
        abort_if($service->is_domiciled, 403);

        $occurrence = $this->occurrenceForDueDate($service, $dueDate);
        abort_unless($occurrence, 404);

        $existingReceipt = $this->receiptFor($service, $dueDate);
        abort_if(filled($existingReceipt?->payment_file_path), 403);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        RecurringServiceReceipt::updateOrCreate(
            ['recurring_service_id' => $service->id, 'due_date' => $dueDate],
            [
                'period_start' => $occurrence['period_start'],
                'amount' => $validated['amount'],
            ],
        );

        $this->audit($service, 'service_amount_updated', "Monto actualizado para {$service->folio} periodo {$dueDate}.");

        return redirect()->route('services.months')->with('status', 'Monto del servicio actualizado.');
    }

    public function updateStatus(RecurringService $service, string $status)
    {
        $this->ensureServices();
        abort_unless(in_array($status, ['active', 'paused', 'inactive'], true), 404);

        $service->update(['status' => $status]);
        $this->audit($service, 'service_status_updated', "Estado del servicio actualizado a {$status}.");

        return back()->with('status', 'Estado del servicio actualizado.');
    }

    private function monthsPayload(): array
    {
        $start = now()->startOfMonth();
        $occasionalPayments = OccasionalServicePayment::with('entry.category')
            ->whereBetween('due_date', [$start->toDateString(), $start->copy()->addMonthsNoOverflow(12)->endOfMonth()->toDateString()])
            ->get()
            ->groupBy(fn (OccasionalServicePayment $payment) => $payment->due_date->format('Y-m'));
        $monthNames = [
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre',
        ];

        return collect(range(0, 12))->map(function (int $offset) use ($start, $monthNames, $occasionalPayments) {
            $month = $start->copy()->addMonths($offset);
            $oneTimePayments = $occasionalPayments->get($month->format('Y-m'), collect())
                ->sortBy('due_date')->values();
            $items = RecurringService::with('receipts')
                ->where('status', '!=', 'inactive')
                ->get()
                ->flatMap(fn(RecurringService $service) => $this->occurrencesForMonth($service, $month))
                ->filter(fn(array $item) => ! $this->isPaidOccurrence($item))
                ->sortBy(fn(array $item) => $item['due_date'])
                ->values();

            $totals = $this->monthTotals($items);
            $oneTimeTotal = $oneTimePayments->sum(fn (OccasionalServicePayment $payment) => (float) $payment->amount);

            return [
                'month_key' => $month->format('Y-m'),
                'label' => ($monthNames[(int) $month->month] ?? $month->format('F')) . ' de ' . $month->year,
                'items' => $items,
                'occasional_payments' => $oneTimePayments,
                'total' => $totals['total'] + $oneTimeTotal,
                'paid_total' => $totals['paid_total'],
                'pending_total' => $totals['pending_total'] + $oneTimeTotal,
            ];
        })->all();
    }

    private function historyMonthsPayload(?Carbon $demoPaymentMonth = null): array
    {
        $latestMonth = now()->startOfMonth();
        $paidReceipts = RecurringServiceReceipt::with('recurringService')
            ->whereNotNull('payment_file_path')
            ->whereNotNull('payment_paid_on')
            ->get()
            ->filter(fn (RecurringServiceReceipt $receipt) => $receipt->recurringService);
        $domiciledServices = RecurringService::with('receipts')
            ->where('status', '!=', 'inactive')
            ->where('is_domiciled', true)
            ->get();
        $earliestMonth = $this->earliestHistoryMonth($latestMonth, $paidReceipts, $domiciledServices);
        if ($demoPaymentMonth?->lte($latestMonth) && $demoPaymentMonth->lt($earliestMonth)) {
            $earliestMonth = $demoPaymentMonth->copy();
        }
        $months = [];

        for ($month = $latestMonth->copy(); $month->gte($earliestMonth); $month->subMonthNoOverflow()) {
            $items = $this->paidHistoryItemsForMonth($month, $paidReceipts, $domiciledServices);
            $months[] = [
                'month_key' => $month->format('Y-m'),
                'label' => $this->monthLabel($month),
                'items' => $items,
                ...$this->monthTotals($items),
            ];
        }

        return $months;
    }

    private function earliestHistoryMonth(
        Carbon $latestMonth,
        Collection $paidReceipts,
        Collection $domiciledServices,
    ): Carbon
    {
        $dates = $paidReceipts->pluck('payment_paid_on');

        $domiciledServices->each(function (RecurringService $service) use ($dates): void {
            $dates->push($this->firstDomiciledPaymentDate($service));
        });

        $earliestDate = $dates
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->startOfMonth())
            ->filter(fn (Carbon $date) => $date->lte($latestMonth))
            ->sortBy(fn (Carbon $date) => $date->timestamp)
            ->first();

        return $earliestDate ?? $latestMonth;
    }

    private function firstDomiciledPaymentDate(RecurringService $service): Carbon
    {
        $startDate = $service->start_date->copy()->startOfDay();

        if (! $service->cutoff_day) {
            return $startDate;
        }

        $cutoff = Carbon::create(
            $service->cutoff_year ?: $startDate->year,
            $service->cutoff_month ?: $startDate->month,
            $service->cutoff_day,
        )->startOfDay();

        if ($cutoff->lt($startDate)) {
            $cutoff->addYear();
        }

        return $cutoff->addDays(max((int) $service->payment_interval_days, 1));
    }

    private function paidHistoryItemsForMonth(
        Carbon $month,
        Collection $paidReceipts,
        Collection $domiciledServices,
    ): Collection
    {
        $monthKey = $month->format('Y-m');
        $paidItems = $paidReceipts
            ->filter(fn (RecurringServiceReceipt $receipt) => $receipt->payment_paid_on?->format('Y-m') === $monthKey)
            ->map(function (RecurringServiceReceipt $receipt) {
                $service = $receipt->recurringService;
                $dueDate = $receipt->due_date?->toDateString() ?? now()->toDateString();
                $periodStart = $receipt->period_start?->toDateString()
                    ?? Carbon::parse($dueDate)->subDays(max((int) $service->payment_interval_days, 1))->toDateString();

                return [
                    'service' => $service,
                    'due_date' => $dueDate,
                    'payment_due_date' => $dueDate,
                    'period_start' => $periodStart,
                    'receipt' => $receipt,
                ];
            });

        $domiciled = $domiciledServices
            ->flatMap(fn (RecurringService $service) => $this->occurrencesForMonth($service, $month));

        return collect($paidItems->all())
            ->merge($domiciled)
            ->unique(fn (array $item) => $item['service']->id.'|'.$item['due_date'])
            ->sortBy(fn (array $item) => $item['receipt']?->payment_paid_on?->toDateString() ?? ($item['payment_due_date'] ?? $item['due_date']))
            ->values();
    }

    private function isPaidOccurrence(array $item): bool
    {
        return (bool) $item['service']->is_domiciled || filled($item['receipt']?->payment_file_path);
    }

    private function monthLabel(Carbon $month): string
    {
        $monthNames = [
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre',
        ];

        return ($monthNames[(int) $month->month] ?? $month->format('F')).' de '.$month->year;
    }

    private function nextWeekTotal(array $months): float
    {
        $nextMonday = now()
            ->startOfDay()
            ->next(Carbon::MONDAY);

        $nextSunday = $nextMonday
            ->copy()
            ->addDays(6)
            ->endOfDay();

        $recurringTotal = collect($months)
            ->flatMap(fn(array $month) => $month['items'])
            ->filter(function (array $item) use ($nextMonday, $nextSunday) {
                $paymentDueDate = Carbon::parse(
                    $item['payment_due_date'] ?? $item['due_date']
                );

                $isPaid = filled($item['receipt']?->payment_file_path);
                $isDomiciled = (bool) $item['service']->is_domiciled;

                return $paymentDueDate->betweenIncluded(
                    $nextMonday,
                    $nextSunday
                )
                    && ! $isPaid
                    && ! $isDomiciled;
            })
            ->sum(function (array $item) {
                return (float) (
                    $item['receipt']?->amount
                    ?? $item['service']->cost
                );
            });

        $occasionalTotal = collect($months)
            ->flatMap(fn (array $month) => $month['occasional_payments'] ?? collect())
            ->filter(fn (OccasionalServicePayment $payment) => $payment->due_date->betweenIncluded($nextMonday, $nextSunday))
            ->sum(fn (OccasionalServicePayment $payment) => (float) $payment->amount);

        return $recurringTotal + $occasionalTotal;
    }

    private function monthTotals($items): array
    {
        $amountFor = fn(array $item) => (float) ($item['receipt']?->amount ?? $item['service']->cost);
        $total = $items->sum($amountFor);
        $paid = $items->filter(fn(array $item) => $item['service']->is_domiciled || filled($item['receipt']?->payment_file_path))
            ->sum($amountFor);

        return [
            'total' => $total,
            'paid_total' => $paid,
            'pending_total' => max($total - $paid, 0),
        ];
    }

    private function occurrencesForMonth(RecurringService $service, Carbon $month)
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $interval = max($service->payment_interval_days, 1);

        if ($service->cutoff_day) {
            return $this->occurrencesForMonthWithCutoff($service, $month, $start, $end, $interval);
        }

        $due = $service->start_date->copy();
        $guard = 0;

        while ($due->lt($start) && $guard < 500) {
            $due->addDays($interval);
            $guard++;
        }

        $items = collect();
        while ($due->lte($end) && $guard < 600) {
            $dueDate = $due->toDateString();
            $items->push([
                'service' => $service,
                'due_date' => $dueDate,
                'payment_due_date' => $due->copy()->addDays(max((int) ($service->due_days_after_cutoff ?? 0), 0))->toDateString(),
                'period_start' => $due->copy()->subDays($interval)->toDateString(),
                'receipt' => $this->receiptFor($service, $dueDate),
            ]);
            $due->addDays($interval);
            $guard++;
        }

        return $items;
    }

    private function occurrencesForMonthWithCutoff(RecurringService $service, Carbon $month, Carbon $start, Carbon $end, int $interval): Collection
    {
        $cutoffDay = $service->cutoff_day;
        $startDate = $service->start_date->copy()->startOfDay();
        $cutoffMonth = (int) ($service->cutoff_month ?: $startDate->month);
        $cutoff = Carbon::create($service->cutoff_year ?: $startDate->year, $cutoffMonth, $cutoffDay)->startOfDay();

        if ($cutoff->lt($startDate)) {
            $cutoff->addYear();
        }

        // El importe pertenece al mes de vencimiento: fecha de corte + lapso.
        $due = $cutoff->copy()->addDays($interval);
        $guard = 0;
        while ($due->lt($start) && $guard < 500) {
            $due->addDays($interval);
            $guard++;
        }

        $items = collect();
        while ($due->lte($end) && $guard < 600) {
            $dueDate = $due->toDateString();
            $items->push([
                'service' => $service,
                'due_date' => $dueDate,
                'payment_due_date' => $dueDate,
                'period_start' => $due->copy()->subDays($interval)->toDateString(),
                'receipt' => $this->receiptFor($service, $dueDate),
            ]);
            $due->addDays($interval);
            $guard++;
        }

        return $items;
    }

    private function occurrenceForDueDate(RecurringService $service, string $dueDate): ?array
    {
        $target = Carbon::parse($dueDate);
        $month = $target->copy()->startOfMonth();

        return $this->occurrencesForMonth($service, $month)
            ->firstWhere('due_date', $target->toDateString());
    }

    private function receiptFor(RecurringService $service, string $dueDate): ?RecurringServiceReceipt
    {
        return $service->receipts->firstWhere('due_date', $dueDate)
            ?? RecurringServiceReceipt::where('recurring_service_id', $service->id)->whereDate('due_date', $dueDate)->first();
    }

    private function nextFolio(): string
    {
        $latest = RecurringService::query()
            ->where('folio', 'like', 'SRV-%')
            ->get()
            ->map(fn(RecurringService $service) => (int) str_replace('SRV-', '', $service->folio))
            ->max();

        return 'SRV-' . str_pad(($latest ?: 0) + 1, 3, '0', STR_PAD_LEFT);
    }

    private function servicePayload(Request $request): array
    {
        $validated = $request->validate($this->serviceRules());
        $paymentLapse = $validated['payment_lapse'];
        unset($validated['payment_lapse']);

        $startDay = $validated['start_day'] ?? null;
        $startMonth = $validated['start_month'] ?? null;
        $startYear = $validated['start_year'] ?? null;
        unset($validated['start_day'], $validated['start_month'], $validated['start_year']);

        $validated['is_domiciled'] = $request->boolean('is_domiciled');
        $validated['payment_interval_days'] = (int) $paymentLapse;

        $startDate = null;

        if ($startDay && $startMonth && $startYear) {
            if (! checkdate((int) $startMonth, (int) $startDay, (int) $startYear)) {
                throw ValidationException::withMessages([
                    'start_day' => 'La fecha de inicio seleccionada no es valida.',
                ]);
            }

            $startDate = Carbon::create($startYear, $startMonth, $startDay)->startOfDay();
            $validated['start_date'] = $startDate->toDateString();
        }

        if ($request->has('cutoff_day')) {
            if (! checkdate(
                (int) $request->input('cutoff_month'),
                (int) $request->input('cutoff_day'),
                (int) $request->input('cutoff_year')
            )) {
                throw ValidationException::withMessages([
                    'cutoff_day' => 'La fecha de corte seleccionada no es valida.',
                ]);
            }

            $cutoffDay = (int) $request->input('cutoff_day');
            $cutoffMonth = (int) $request->input('cutoff_month');
            $cutoffYear = (int) $request->input('cutoff_year');
            $cutoffDate = Carbon::create($cutoffYear, $cutoffMonth, $cutoffDay)->startOfDay();

            if ($startDate && $cutoffDate->lt($startDate)) {
                throw ValidationException::withMessages([
                    'cutoff_day' => 'La fecha de corte debe ser igual o posterior a la fecha de inicio del periodo.',
                ]);
            }

            $validated['cutoff_day'] = $cutoffDay;
            $validated['cutoff_month'] = $cutoffMonth;
            $validated['cutoff_year'] = $cutoffYear;
            $validated['due_days_after_cutoff'] = $paymentLapse;
        }

        if (! Schema::hasColumn('recurring_services', 'service_location')) {
            unset($validated['service_location']);
        }

        if (! Schema::hasColumn('recurring_services', 'due_days_after_cutoff')) {
            unset($validated['due_days_after_cutoff']);
        }

        if (! Schema::hasColumn('recurring_services', 'cutoff_day')) {
            unset($validated['cutoff_day']);
        }

        if (! Schema::hasColumn('recurring_services', 'cutoff_month')) {
            unset($validated['cutoff_month']);
        }

        if (! Schema::hasColumn('recurring_services', 'cutoff_year')) {
            unset($validated['cutoff_year']);
        }

        return $validated;
    }

    private function serviceRules(): array
    {
        return [
            'holder' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'bank' => ['required', 'string', 'max:120'],
            'payer_account' => ['required', 'string', 'max:80'],
            'branch' => ['nullable', 'string', 'max:255'],
            'service_name' => ['required', 'string', 'max:255'],
            'provider' => ['required', 'string', 'max:255'],
            'service_number' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:80'],
            'cost' => ['required', 'numeric', 'min:0'],
            'validity' => ['required', 'string', 'max:120'],
            'payment_lapse' => ['required', 'in:30,60,90,180,365'],
            'due_days_after_cutoff' => ['nullable', 'integer', 'min:0', 'max:366'],
            'is_domiciled' => ['nullable', 'boolean'],
            'start_date' => ['required', 'date'],
            'start_day' => ['required', 'integer', 'min:1', 'max:31'],
            'start_month' => ['required', 'integer', 'min:1', 'max:12'],
            'start_year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'cutoff_day' => ['required', 'integer', 'min:1', 'max:31'],
            'cutoff_month' => ['required', 'integer', 'min:1', 'max:12'],
            'cutoff_year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'reference' => ['nullable', 'string', 'max:255'],
            'service_location' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    private function audit(RecurringService $service, string $action, string $description): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => RecurringService::class,
            'auditable_id' => $service->id,
            'action' => $action,
            'description' => $description,
        ]);
    }

    private function ensureServices(): void
    {
        $user = Auth::user();

        abort_unless($user?->canAccessRole('services') || $user?->canAccessRole('finance'), 403);
    }

    private function ensureServiceHistory(): void
    {
        $user = Auth::user();

        abort_unless($user?->canAccessRole('services') || $user?->canAccessRole('finance'), 403);
    }
}
