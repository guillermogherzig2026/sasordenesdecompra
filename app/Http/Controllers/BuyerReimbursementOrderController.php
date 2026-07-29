<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\ReimbursementOrder;
use App\Support\StoredFileResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BuyerReimbursementOrderController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureReimbursements();

        $panel = $request->query('panel', 'pending');
        $query = trim((string) $request->query('q'));

        $orders = ReimbursementOrder::with('company')
            ->when(! $this->isSuperAdmin(), fn ($builder) => $builder->where('requester_id', Auth::id()))
            ->when(
                $panel === 'history',
                fn ($builder) => $builder->whereIn('status', ['paid', 'rejected', 'canceled']),
                fn ($builder) => $builder->whereIn('status', ['sent', 'approved'])
            )
            ->when($query, function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $inner->where('folio', 'like', "%{$query}%")
                        ->orWhere('provider', 'like', "%{$query}%")
                        ->orWhereHas('company', fn ($company) => $company->where('name', 'like', "%{$query}%"));
                });
            })
            ->orderByDesc('created_on')
            ->get();

        return view('buyer.reimbursement-orders.index', [
            'orders' => $orders,
            'panel' => $panel,
            'query' => $query,
        ]);
    }

    public function create()
    {
        $this->ensureReimbursements();

        return view('buyer.reimbursement-orders.form', [
            'companies' => $this->allowedCompanies(),
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureReimbursements();

        $validated = $request->validate([
            'company_id' => ['required', 'integer'],
            'provider' => ['required', 'string', 'max:255'],
            'concept' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'quote_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'support_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $company = $this->allowedCompanies()->firstWhere('id', (int) $validated['company_id']);
        abort_unless($company, 403);

        $order = DB::transaction(function () use ($request, $validated, $company) {
            $quote = $request->file('quote_file');
            $support = $request->file('support_file');

            $order = ReimbursementOrder::create([
                'folio' => $this->nextFolio(),
                'requester_id' => Auth::id(),
                'company_id' => $company->id,
                'provider' => $validated['provider'],
                'concept' => $validated['concept'] ?? null,
                'created_on' => now()->toDateString(),
                'amount' => $validated['amount'],
                'status' => 'sent',
                'quote_file_path' => $quote->store('reimbursement-quotes'),
                'quote_original_name' => $quote->getClientOriginalName(),
                'support_file_path' => $support?->store('reimbursement-supports'),
                'support_original_name' => $support?->getClientOriginalName(),
                'support_on' => $support ? now()->toDateString() : null,
                'notes' => $validated['notes'] ?? null,
            ]);

            $this->audit($order, 'sent', 'OR enviada a Finanzas para autorizacion.');

            return $order;
        });

        return redirect()->route('buyer.reimbursement-orders.index')->with('status', "{$order->folio} enviada.");
    }

    public function uploadSupport(Request $request, ReimbursementOrder $reimbursementOrder)
    {
        $this->ensureReimbursements();
        abort_unless($this->isSuperAdmin() || (int) $reimbursementOrder->requester_id === Auth::id(), 403);
        abort_unless(! in_array($reimbursementOrder->status, ['paid', 'rejected', 'canceled'], true), 403);

        $validated = $request->validate([
            'support_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $file = $request->file('support_file');
        $reimbursementOrder->update([
            'support_file_path' => $file->store('reimbursement-supports'),
            'support_original_name' => $file->getClientOriginalName(),
            'support_on' => now()->toDateString(),
        ]);

        $this->audit($reimbursementOrder, 'support_uploaded', 'Soporte de bien o servicio cargado por el usuario.');

        return back()->with('status', 'Soporte cargado.');
    }

    public function quote(ReimbursementOrder $reimbursementOrder)
    {
        $this->ensureOwner($reimbursementOrder);

        return StoredFileResponse::download($reimbursementOrder->quote_file_path, $reimbursementOrder->quote_original_name ?: $reimbursementOrder->folio.'-cotizacion');
    }

    public function support(ReimbursementOrder $reimbursementOrder)
    {
        $this->ensureOwner($reimbursementOrder);

        return StoredFileResponse::download($reimbursementOrder->support_file_path, $reimbursementOrder->support_original_name ?: $reimbursementOrder->folio.'-soporte');
    }

    public function payment(ReimbursementOrder $reimbursementOrder)
    {
        $this->ensureOwner($reimbursementOrder);

        return StoredFileResponse::download($reimbursementOrder->payment_file_path, $reimbursementOrder->payment_original_name ?: $reimbursementOrder->folio.'-pago');
    }

    private function nextFolio(): string
    {
        $latest = ReimbursementOrder::query()
            ->where('folio', 'like', 'OR-%')
            ->get()
            ->map(fn (ReimbursementOrder $order) => (int) str_replace('OR-', '', $order->folio))
            ->max();

        return 'OR-'.(($latest ?: 1000) + 1);
    }

    private function allowedCompanies()
    {
        if ($this->isSuperAdmin()) {
            return Company::orderBy('name')->get();
        }

        $names = Auth::user()?->authorizedCompanyNames() ?? [];

        return Company::query()
            ->when(count($names), fn ($builder) => $builder->whereIn('name', $names))
            ->when(! count($names), fn ($builder) => $builder->whereRaw('1 = 0'))
            ->orderBy('name')
            ->get();
    }

    private function audit(ReimbursementOrder $order, string $action, string $description): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => ReimbursementOrder::class,
            'auditable_id' => $order->id,
            'action' => $action,
            'description' => $description,
        ]);
    }

    private function ensureOwner(ReimbursementOrder $order): void
    {
        $this->ensureReimbursements();
        abort_unless($this->isSuperAdmin() || (int) $order->requester_id === Auth::id(), 403);
    }

    private function isSuperAdmin(): bool
    {
        return Auth::user()?->role === 'superadmin';
    }

    private function ensureReimbursements(): void
    {
        abort_unless(Auth::user()?->canAccessBuyerSubrole('reimbursements'), 403);
    }
}
