<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Provider;
use App\Models\PurchaseOrder;
use App\Models\RecurringService;
use App\Models\SupplyOrder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private const EXCEL_TEXT_HEADERS = [
        'Numero Servicio',
        'No. Servicio',
    ];

    public function index()
    {
        return view('reports.index');
    }

    public function download(string $type): StreamedResponse
    {
        $user = Auth::user();
        $constructionContext = $user?->role === 'superadmin' && request('context') === 'construction';

        $rows = match ($type) {
            'finance-active' => $this->orderRows(PurchaseOrder::with(['buyer', 'company', 'provider', 'payment', 'items.receiptItems', 'receipts'])->general()->whereIn('status', ['sent', 'approved'])->get()),
            'finance-active-excel' => $this->orderRows(PurchaseOrder::with(['buyer', 'company', 'provider', 'payment', 'items.receiptItems', 'receipts'])->general()->whereIn('status', ['sent', 'approved'])->get()),
            'finance-history' => $this->orderRows($this->sortHistoryOrders(PurchaseOrder::with(['buyer', 'company', 'provider', 'payment', 'items.receiptItems', 'receipts', 'auditLogs'])->general()->whereIn('status', ['paid', 'rejected', 'canceled'])->get())),
            'finance-history-items-excel' => $this->orderItemRows($this->sortHistoryOrders(PurchaseOrder::with(['buyer', 'company', 'provider', 'payment', 'items.receiptItems', 'receipts', 'auditLogs'])->general()->whereIn('status', ['paid', 'rejected', 'canceled'])->get())),
            'buyer-active' => $this->orderRows(PurchaseOrder::with(['buyer', 'company', 'provider', 'payment', 'items.receiptItems', 'receipts'])->general()->where('buyer_id', $user->id)->whereNotIn('status', ['rejected', 'canceled'])->get()),
            'buyer-items-excel' => $this->orderItemRows(PurchaseOrder::with(['buyer', 'company', 'provider', 'payment', 'items.receiptItems', 'receipts', 'auditLogs'])
                ->when($constructionContext, fn ($query) => $query->forConstruction(), fn ($query) => $query->general())
                ->when($user?->role !== 'superadmin', fn ($query) => $query->where('buyer_id', $user->id))
                ->latest()
                ->get()),
            'buyer-history' => $this->orderRows(PurchaseOrder::with(['buyer', 'company', 'provider', 'payment', 'items.receiptItems', 'receipts'])->general()->where('buyer_id', $user->id)->whereIn('status', ['paid', 'rejected', 'canceled'])->get()),
            'inventory-paid' => $this->orderRows(PurchaseOrder::with(['buyer', 'company', 'provider', 'payment', 'items.receiptItems', 'receipts'])->general()->where('status', 'paid')->where('receipt_status', '!=', 'completed')->get()),
            'inventory-history' => $this->orderRows(PurchaseOrder::with(['buyer', 'company', 'provider', 'payment', 'items.receiptItems', 'receipts'])->general()->where('status', 'paid')->where('receipt_status', 'completed')->get()),
            'audit' => $this->auditRows(),
            'providers' => $this->providerRows(),
            'providers-excel' => $this->providerRows(),
            'companies' => $this->companyRows(),
            'supply-orders' => $this->supplyOrderRows($this->supplyOrdersForReport()),
            'supply-orders-excel' => $this->supplyOrderRows($this->supplyOrdersForReport()),
            'services-catalog' => $this->serviceRows(),
            'services-payments' => $this->servicePaymentRows(),
            'services-months-excel' => $this->serviceMonthRows(request('month')),
            default => abort(404),
        };

        if (str_ends_with($type, '-excel')) {
            return $this->excel("{$type}.xls", $rows);
        }

        return $this->csv("{$type}.csv", $rows);
    }

    private function orderRows($orders): array
    {
        return $orders->map(function (PurchaseOrder $order) {
            $ordered = $order->items->sum('quantity');
            $received = $order->items->sum(fn ($item) => $item->receiptItems->sum('received_quantity'));
            $lastReceipt = $order->receipts->sortByDesc('received_on')->first();

            return [
                'OC' => $order->folio,
                'Fecha Envio' => ($order->created_on ?? $order->created_at)?->format('d/m/Y'),
                'Comprador' => $order->buyer?->name,
                'Empresa' => $order->company?->name,
                'Proveedor' => $order->provider?->business_name,
                'Giro Proveedor' => $order->provider?->business_line,
                'Referencia' => $order->reference ?: $order->provider?->reference,
                'Concepto Pago' => $order->payment_concept,
                'Monto' => $order->total,
                'Credito' => $order->is_credit ? 'Si' : 'No',
                'Dias Credito' => $order->is_credit ? $order->credit_days : '',
                'Estado Finanzas' => $order->status,
                'Estado Inventarios' => $order->receipt_status,
                'Cantidad OC' => $ordered,
                'Cantidad Recibida' => $received,
                'Fecha Vencimiento' => $order->due_date?->format('d/m/Y'),
                'Fecha Pago' => $order->payment?->paid_on?->format('d/m/Y'),
                'Fecha Evento Historial' => $this->historyEventDate($order)?->format('d/m/Y H:i'),
                'Archivo Pago' => $order->payment?->original_name,
                'Factura' => $lastReceipt?->invoice_number,
                'Fecha Recepcion' => $lastReceipt?->received_on?->format('d/m/Y'),
                'Documento Recepcion' => $lastReceipt?->original_name,
            ];
        })->all();
    }

    private function orderItemRows($orders): array
    {
        return $orders->flatMap(function (PurchaseOrder $order) {
            $lastReceipt = $order->receipts->sortByDesc('received_on')->first();

            return $order->items->map(function ($item, int $index) use ($order, $lastReceipt) {
                $received = $item->receiptItems->sum('received_quantity');

                return [
                    'OC' => $order->folio,
                    'Partida' => $index + 1,
                    'Fecha Envio' => ($order->created_on ?? $order->created_at)?->format('d/m/Y'),
                    'Comprador' => $order->buyer?->name,
                    'Empresa' => $order->company?->name,
                    'Proveedor' => $order->provider?->business_name,
                    'Giro Proveedor' => $order->provider?->business_line,
                    'Referencia' => $order->reference ?: $order->provider?->reference,
                    'Concepto Pago' => $order->payment_concept,
                    'Articulo' => $item->article,
                    'Cantidad OC' => $item->quantity,
                    'Precio Unitario' => $item->unit_price,
                    'Importe Partida' => $item->line_total,
                    'Credito' => $order->is_credit ? 'Si' : 'No',
                    'Dias Credito' => $order->is_credit ? $order->credit_days : '',
                    'Cantidad Recibida' => $received,
                    'Estado Finanzas' => $order->status,
                    'Estado Inventarios' => $order->receipt_status,
                    'Fecha Vencimiento' => $order->due_date?->format('d/m/Y'),
                    'Fecha Pago' => $order->payment?->paid_on?->format('d/m/Y'),
                    'Fecha Evento Historial' => $this->historyEventDate($order)?->format('d/m/Y H:i'),
                    'Archivo Pago' => $order->payment?->original_name,
                    'Factura' => $lastReceipt?->invoice_number,
                    'Fecha Recepcion' => $lastReceipt?->received_on?->format('d/m/Y'),
                    'Documento Recepcion' => $lastReceipt?->original_name,
                ];
            });
        })->values()->all();
    }

    private function sortHistoryOrders($orders)
    {
        return $orders
            ->sortByDesc(fn (PurchaseOrder $order) => $this->historyEventDate($order)?->timestamp ?? 0)
            ->values();
    }

    private function historyEventDate(PurchaseOrder $order)
    {
        return match ($order->status) {
            'paid' => $order->payment?->paid_on,
            'rejected' => $order->auditLogs
                ->where('action', 'rejected')
                ->sortByDesc('created_at')
                ->first()
                ?->created_at,
            'canceled' => $order->auditLogs
                ->where('action', 'canceled')
                ->sortByDesc('created_at')
                ->first()
                ?->created_at ?? $order->updated_at,
            default => $order->updated_at,
        };
    }

    private function auditRows(): array
    {
        return AuditLog::with('user')->latest()->limit(500)->get()->map(fn (AuditLog $log) => [
            'Fecha' => $log->created_at->format('d/m/Y H:i'),
            'Usuario' => $log->user?->name,
            'Accion' => $log->action,
            'Descripcion' => $log->description,
        ])->all();
    }

    private function providerRows(): array
    {
        return Provider::with(['buyer', 'businessSubcategory'])->orderBy('business_name')->get()->map(fn (Provider $provider) => [
            'Comprador' => $provider->buyer?->name,
            'Razon Social' => $provider->business_name,
            'RFC' => $provider->rfc,
            'Contacto' => $provider->contact_name,
            'Telefono' => $provider->phone,
            'Direccion' => $provider->address,
            'Giro' => $provider->business_line,
            'Subcategoria' => $provider->businessSubcategory?->name ?? $provider->provider_business_subcategory,
            'Banco' => $provider->bank,
            'Cuenta' => $provider->account_number,
            'CLABE' => $provider->clabe,
            'Referencia' => $provider->reference,
            'Fecha Alta' => $provider->created_at->format('d/m/Y'),
        ])->all();
    }

    private function companyRows(): array
    {
        return Company::orderBy('name')->get()->map(fn (Company $company) => [
            'Razon Social' => $company->name,
            'RFC' => $company->rfc,
            'Direccion' => $company->address,
            'Observaciones OC' => $company->purchase_order_notes,
            'Logotipo' => $company->logo_path ? 'Cargado' : 'Sin logotipo',
            'Fecha Alta' => $company->created_at->format('d/m/Y'),
        ])->all();
    }

    private function serviceRows(): array
    {
        return RecurringService::with('receipts')->orderBy('folio')->get()->map(fn (RecurringService $service) => [
            'ID' => $service->folio,
            'Titular' => $service->holder,
            'Empresa' => $service->company_name,
            'Banco' => $service->bank,
            'Cuenta Pagadora' => $service->payer_account,
            'Servicio' => $service->service_name,
            'Proveedor' => $service->provider,
            'Numero Servicio' => $service->service_number,
            'Categoria' => $service->category,
            'Costo' => $service->cost,
            'Vigencia' => $service->validity,
            'Lapso Pago' => $service->is_domiciled ? 'Domiciliado' : $service->payment_interval_days.' dias',
            'Dia de corte' => $service->cutoff_day ?? 'N/A',
            'Fecha Inicio' => $service->start_date?->format('d/m/Y'),
            'Referencia' => $service->reference,
            'Estado' => $service->status,
            'Recibos Cargados' => $service->receipts->whereNotNull('support_file_path')->count(),
            'Pagos Comprobados' => $service->receipts->whereNotNull('payment_file_path')->count(),
        ])->all();
    }

    private function supplyOrderRows($orders): array
    {
        return $orders->flatMap(function (SupplyOrder $order) {
            $items = $order->items->isNotEmpty() ? $order->items->values() : collect([null]);

            return $items->map(function ($item, int $index) use ($order) {
                return [
                    'ID Consecutivo OS' => $order->supply_consecutive,
                    'OS' => $order->folio,
                    'Partida' => $item ? $index + 1 : '',
                    'Fecha Envio' => $order->created_on?->format('d/m/Y'),
                    'Usuario' => $order->requester?->name,
                    'Empresa' => $order->company?->name,
                    'Almacen Origen' => $order->warehouse_from,
                    'Almacen Destino' => $order->warehouse_to,
                    'Descripcion' => $item?->article,
                    'SKU' => $item?->catalogItem?->sku,
                    'Cantidad' => $item?->quantity,
                    'Unidad' => $item?->catalogItem?->unit,
                    'Precio Unitario' => $item?->unit_cost,
                    'Precio Total' => $item?->line_total,
                    'Estado' => $order->status,
                    'Remision' => $order->formatted_delivery_remission_number ?: 'Pendiente',
                    'Fecha Salida' => $order->delivered_on?->format('d/m/Y'),
                    'Fecha Recepcion' => $order->received_on?->format('d/m/Y'),
                    'Recibio' => $order->received_by_name,
                ];
            });
        })->values()->all();
    }

    private function supplyOrdersForReport()
    {
        $user = Auth::user();
        $canSeeAll = $user?->role === 'superadmin'
            || $user?->canAccessRole('finance')
            || $user?->canAccessRole('inventory');

        return SupplyOrder::with(['requester', 'company', 'items.catalogItem'])
            ->when(! $canSeeAll, fn ($builder) => $builder->where('requester_id', $user?->id ?? 0))
            ->orderBy('id')
            ->get();
    }

    private function servicePaymentRows(): array
    {
        return RecurringService::with('receipts')->orderBy('folio')->get()->flatMap(function (RecurringService $service) {
            return $service->receipts->map(fn ($receipt) => [
                'ID' => $service->folio,
                'Servicio' => $service->service_name,
                'Proveedor' => $service->provider,
                'Periodo' => optional($receipt->period_start)->format('d/m/Y').' al '.optional($receipt->due_date)->format('d/m/Y'),
                'Monto' => $receipt->amount ?? $service->cost,
                'Estado' => $receipt->status,
                'Recibo / Factura' => $receipt->support_original_name,
                'Fecha Recibo / Factura' => $receipt->support_on?->format('d/m/Y'),
                'Comprobante Pago' => $receipt->payment_original_name,
                'Fecha Pago Finanzas' => $receipt->payment_paid_on?->format('d/m/Y'),
            ]);
        })->all();
    }

    private function serviceMonthRows(?string $monthKey = null): array
    {
        $months = $monthKey
            ? collect([Carbon::createFromFormat('Y-m', $monthKey)->startOfMonth()])
            : collect(range(0, 12))->map(fn (int $offset) => now()->startOfMonth()->addMonths($offset));

        $services = RecurringService::with('receipts')
            ->where('status', '!=', 'inactive')
            ->orderBy('folio')
            ->get();

        return $months
            ->flatMap(function (Carbon $month) use ($services) {
                return $services
                    ->flatMap(fn (RecurringService $service) => $this->serviceOccurrencesForMonth($service, $month))
                    ->sortBy(fn (array $item) => $item['due_date'])
                    ->values()
                    ->map(function (array $item) use ($month) {
                        $service = $item['service'];
                        $receipt = $item['receipt'];
                        $isDomiciled = (bool) $service->is_domiciled;
                        $paid = filled($receipt?->payment_file_path);
                        $hasSupport = filled($receipt?->support_file_path);
                        $paused = $service->status === 'paused';
                        $status = $paused ? 'Pausado' : ($isDomiciled ? 'DOM' : ($paid ? 'Pagado' : ($hasSupport ? 'Listo para pago' : 'Pendiente')));

                        return [
                            'Mes' => $this->serviceMonthLabel($month),
                            'ID' => $service->folio,
                            'Titular' => $service->holder ?: $service->company_name,
                            'Sucursal' => $service->display_branch,
                            'Banco' => $service->bank,
                            'Cuenta Pagadora' => $service->payer_account,
                            'Servicio' => $service->service_name,
                            'Proveedor' => $service->provider,
                            'Numero Servicio' => $service->service_number,
                            'Periodo' => Carbon::parse($item['period_start'])->format('d/m/Y').' al '.Carbon::parse($item['due_date'])->format('d/m/Y'),
                            'Vencimiento' => Carbon::parse($item['payment_due_date'] ?? $item['due_date'])->format('d/m/Y'),
                            'Monto' => $receipt?->amount ?? $service->cost,
                            'Referencia' => $service->reference,
                            'Estado' => $status,
                            'Factura' => $receipt?->support_original_name ?: 'Pendiente',
                            'Comprobante Pago' => $isDomiciled ? 'DOM' : ($receipt?->payment_original_name ?: 'Pendiente'),
                        ];
                    });
            })
            ->values()
            ->all();
    }

    private function serviceOccurrencesForMonth(RecurringService $service, Carbon $month)
    {
        if (! $service->start_date) {
            return collect();
        }

        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $interval = max((int) $service->payment_interval_days, 1);

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
                'receipt' => $service->receipts->firstWhere('due_date', $dueDate),
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
                'receipt' => $service->receipts->firstWhere('due_date', $dueDate),
            ]);
            $due->addDays($interval);
            $guard++;
        }

        return $items;
    }

    private function serviceMonthLabel(Carbon $month): string
    {
        $months = [
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

        return ($months[(int) $month->month] ?? strtolower($month->format('F'))).' de '.$month->year;
    }

    private function csv(string $filename, array $rows): StreamedResponse
    {
        $headers = array_keys($rows[0] ?? ['Mensaje' => 'Sin datos']);

        return response()->streamDownload(function () use ($rows, $headers) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headers);
            foreach ($rows as $row) {
                fputcsv($out, array_map(fn ($header) => $row[$header] ?? '', $headers));
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function excel(string $filename, array $rows): StreamedResponse
    {
        $headers = array_keys($rows[0] ?? ['Mensaje' => 'Sin datos']);

        return response()->streamDownload(function () use ($rows, $headers) {
            echo '<table border="1"><thead><tr>';
            foreach ($headers as $header) {
                echo '<th>'.htmlspecialchars($header, ENT_QUOTES, 'UTF-8').'</th>';
            }
            echo '</tr></thead><tbody>';

            foreach ($rows as $row) {
                echo '<tr>';
                foreach ($headers as $header) {
                    $attributes = in_array($header, self::EXCEL_TEXT_HEADERS, true)
                        ? ' style="mso-number-format:\'\\@\';"'
                        : '';

                    echo '<td'.$attributes.'>'.htmlspecialchars((string) ($row[$header] ?? ''), ENT_QUOTES, 'UTF-8').'</td>';
                }
                echo '</tr>';
            }

            echo '</tbody></table>';
        }, $filename, ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8']);
    }
}
