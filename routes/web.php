<?php

use App\Http\Controllers\BuyerProviderController;
use App\Http\Controllers\BuyerPurchaseOrderController;
use App\Http\Controllers\BuyerReimbursementOrderController;
use App\Http\Controllers\BuyerSupplyOrderController;
use App\Http\Controllers\CompanyAssetController;
use App\Http\Controllers\FinanceAdminController;
use App\Http\Controllers\FinancePurchaseOrderController;
use App\Http\Controllers\FinanceReimbursementOrderController;
use App\Http\Controllers\FinanceServicePaymentController;
use App\Http\Controllers\FinanceSupplyOrderController;
use App\Http\Controllers\InventoryReceiptController;
use App\Http\Controllers\InventorySupplyOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SuperAdminProviderBusinessLineController;
use App\Http\Controllers\SuperAdminUserController;
use App\Http\Controllers\SupplyOrderDigitalController;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\PurchaseOrder;
use App\Models\RecurringService;
use App\Models\RecurringServiceReceipt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $remember = $request->boolean('remember');

    if (! Auth::attempt([...$credentials, 'active' => true], $remember)) {
        throw ValidationException::withMessages([
            'email' => 'Las credenciales no coinciden con un usuario activo.',
        ]);
    }

    $request->session()->regenerate();

    return redirect()->intended(route('dashboard'));
})->name('login.store');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

Route::get('/remisiones/os/{token}', [SupplyOrderDigitalController::class, 'show'])->name('supply-orders.digital.show');
Route::post('/remisiones/os/{token}/recibir', [SupplyOrderDigitalController::class, 'receive'])->name('supply-orders.digital.receive');

Route::get('/dashboard', function () {
    $user = Auth::user();
    $approvedFinanceOrders = PurchaseOrder::where('status', 'approved');
    $paidThisMonth = PurchaseOrder::where('status', 'paid')
        ->whereHas('payment', fn ($payment) => $payment
            ->whereYear('paid_on', now()->year)
            ->whereMonth('paid_on', now()->month));
    $buyerPaidThisMonth = PurchaseOrder::where('buyer_id', $user->id)
        ->where('status', 'paid')
        ->whereHas('payment', fn ($payment) => $payment
            ->whereYear('paid_on', now()->year)
            ->whereMonth('paid_on', now()->month));
    $services = RecurringService::with('receipts')->where('status', 'active')->get();
    $currentServiceOccurrences = $services->flatMap(function (RecurringService $service) {
        $start = now()->copy()->startOfMonth();
        $end = now()->copy()->endOfMonth();
        $interval = max($service->payment_interval_days, 1);
        $due = $service->start_date->copy();
        $guard = 0;
        $items = collect();

        while ($due->lt($start) && $guard < 500) {
            $due->addDays($interval);
            $guard++;
        }

        while ($due->lte($end) && $guard < 600) {
            $dueDate = $due->toDateString();
            $receipt = $service->receipts->firstWhere('due_date', $dueDate);
            $items->push([
                'service' => $service,
                'receipt' => $receipt,
            ]);
            $due->addDays($interval);
            $guard++;
        }

        return $items;
    });
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

    return view('dashboard', [
        'user' => $user,
        'ordersCount' => PurchaseOrder::count(),
        'pendingFinanceCount' => PurchaseOrder::whereIn('status', ['sent', 'approved'])->count(),
        'financeSentCount' => PurchaseOrder::where('status', 'sent')->count(),
        'financeApprovedCount' => PurchaseOrder::where('status', 'approved')->count(),
        'financePendingAmount' => (clone $approvedFinanceOrders)->sum('total'),
        'financeCurrentMonthTotal' => (clone $paidThisMonth)->sum('total'),
        'currentMonthLabel' => ($monthNames[(int) now()->month] ?? now()->format('F')).' de '.now()->year,
        'myOrdersCount' => PurchaseOrder::where('buyer_id', $user->id)->count(),
        'buyerSentCount' => PurchaseOrder::where('buyer_id', $user->id)->where('status', 'sent')->count(),
        'buyerApprovedCount' => PurchaseOrder::where('buyer_id', $user->id)->where('status', 'approved')->count(),
        'buyerPaidCount' => PurchaseOrder::where('buyer_id', $user->id)->where('status', 'paid')->count(),
        'buyerCurrentMonthTotal' => (clone $buyerPaidThisMonth)->sum('total'),
        'inventoryOpenCount' => PurchaseOrder::where('status', 'paid')->where('receipt_status', '!=', 'completed')->count(),
        'inventoryPendingCount' => PurchaseOrder::where('status', 'paid')->where('receipt_status', 'pending')->count(),
        'inventoryPartialCount' => PurchaseOrder::where('status', 'paid')->where('receipt_status', 'partial')->count(),
        'inventoryCompletedCount' => PurchaseOrder::where('status', 'paid')->where('receipt_status', 'completed')->count(),
        'inventoryCompletedAmount' => PurchaseOrder::where('status', 'paid')->where('receipt_status', 'completed')->sum('total'),
        'servicesCount' => $services->count(),
        'servicesDueThisMonthCount' => $currentServiceOccurrences->filter(fn (array $item) => ! $item['service']->is_domiciled && ! $item['receipt']?->isPaid())->count(),
        'servicesMonthAmount' => $currentServiceOccurrences->sum(fn (array $item) => (float) $item['service']->cost),
        'servicesReceiptsLoadedCount' => RecurringServiceReceipt::whereNotNull('support_file_path')->count(),
        'usersCount' => User::count(),
        'activeUsersCount' => User::where('active', true)->count(),
        'rolesCount' => User::distinct('role')->count('role'),
        'companiesCount' => Company::count(),
        'auditLogs' => AuditLog::with('user')->latest()->limit(8)->get(),
        'financeAuditLogs' => AuditLog::with(['user', 'auditable'])
            ->where('auditable_type', PurchaseOrder::class)
            ->latest()
            ->limit(6)
            ->get(),
        'orderAuditLogs' => AuditLog::with(['user', 'auditable'])
            ->where('auditable_type', PurchaseOrder::class)
            ->latest()
            ->limit(6)
            ->get(),
    ]);
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/reportes', fn () => redirect()->route('dashboard'))->name('reports.index');
    Route::get('/reportes/{type}', [ReportController::class, 'download'])->name('reports.download');
    Route::get('/empresas/{company}/logo', [CompanyAssetController::class, 'logo'])->name('companies.logo');
});

Route::middleware('auth')->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/usuarios', [SuperAdminUserController::class, 'index'])->name('users.index');
    Route::post('/usuarios', [SuperAdminUserController::class, 'store'])->name('users.store');
    Route::put('/usuarios/{user}', [SuperAdminUserController::class, 'update'])->name('users.update');
    Route::patch('/usuarios/{user}/estado', [SuperAdminUserController::class, 'toggle'])->name('users.toggle');
    Route::get('/giros-proveeduria', [SuperAdminProviderBusinessLineController::class, 'index'])->name('provider-lines.index');
    Route::post('/giros-proveeduria', [SuperAdminProviderBusinessLineController::class, 'store'])->name('provider-lines.store');
    Route::put('/giros-proveeduria/{providerLine}', [SuperAdminProviderBusinessLineController::class, 'update'])->name('provider-lines.update');
    Route::delete('/giros-proveeduria/{providerLine}', [SuperAdminProviderBusinessLineController::class, 'destroy'])->name('provider-lines.destroy');
});

Route::middleware('auth')->prefix('comprador')->name('buyer.')->group(function () {
    Route::get('/ordenes', [BuyerPurchaseOrderController::class, 'index'])->name('orders.index');
    Route::get('/ordenes/nueva', [BuyerPurchaseOrderController::class, 'create'])->name('orders.create');
    Route::post('/ordenes', [BuyerPurchaseOrderController::class, 'store'])->name('orders.store');
    Route::get('/ordenes/{purchaseOrder}/pdf', [BuyerPurchaseOrderController::class, 'print'])->name('orders.print');
    Route::get('/ordenes/{purchaseOrder}/comprobante-pago', [BuyerPurchaseOrderController::class, 'paymentReceipt'])->name('orders.payment-receipt');
    Route::get('/ordenes/{purchaseOrder}/cotizacion', [BuyerPurchaseOrderController::class, 'quoteSupport'])->name('orders.quote');
    Route::get('/ordenes/{purchaseOrder}/editar', [BuyerPurchaseOrderController::class, 'edit'])->name('orders.edit');
    Route::put('/ordenes/{purchaseOrder}', [BuyerPurchaseOrderController::class, 'update'])->name('orders.update');
    Route::patch('/ordenes/{purchaseOrder}/cancelar', [BuyerPurchaseOrderController::class, 'cancel'])->name('orders.cancel');

    Route::get('/proveedores', [BuyerProviderController::class, 'index'])->name('providers.index');
    Route::post('/proveedores', [BuyerProviderController::class, 'store'])->name('providers.store');
    Route::put('/proveedores/{provider}', [BuyerProviderController::class, 'update'])->name('providers.update');

    Route::get('/suministros', [BuyerSupplyOrderController::class, 'index'])->name('supply-orders.index');
    Route::get('/suministros/nueva', [BuyerSupplyOrderController::class, 'create'])->name('supply-orders.create');
    Route::post('/suministros', [BuyerSupplyOrderController::class, 'store'])->name('supply-orders.store');
    Route::get('/suministros/{supplyOrder}/remision', [BuyerSupplyOrderController::class, 'remission'])->name('supply-orders.remission');

    Route::get('/reembolsos', [BuyerReimbursementOrderController::class, 'index'])->name('reimbursement-orders.index');
    Route::get('/reembolsos/nueva', [BuyerReimbursementOrderController::class, 'create'])->name('reimbursement-orders.create');
    Route::post('/reembolsos', [BuyerReimbursementOrderController::class, 'store'])->name('reimbursement-orders.store');
    Route::post('/reembolsos/{reimbursementOrder}/soporte', [BuyerReimbursementOrderController::class, 'uploadSupport'])->name('reimbursement-orders.support.store');
    Route::get('/reembolsos/{reimbursementOrder}/cotizacion', [BuyerReimbursementOrderController::class, 'quote'])->name('reimbursement-orders.quote');
    Route::get('/reembolsos/{reimbursementOrder}/soporte', [BuyerReimbursementOrderController::class, 'support'])->name('reimbursement-orders.support');
    Route::get('/reembolsos/{reimbursementOrder}/pago', [BuyerReimbursementOrderController::class, 'payment'])->name('reimbursement-orders.payment');
});

Route::middleware('auth')->prefix('finanzas')->name('finance.')->group(function () {
    Route::get('/ordenes-vigentes', [FinancePurchaseOrderController::class, 'active'])->name('orders.active');
    Route::patch('/ordenes/{purchaseOrder}/aprobar', [FinancePurchaseOrderController::class, 'approve'])->name('orders.approve');
    Route::patch('/ordenes/{purchaseOrder}/rechazar', [FinancePurchaseOrderController::class, 'reject'])->name('orders.reject');
    Route::get('/ordenes/{purchaseOrder}/pago', [FinancePurchaseOrderController::class, 'paymentForm'])->name('orders.payment');
    Route::post('/ordenes/{purchaseOrder}/pago', [FinancePurchaseOrderController::class, 'storePayment'])->name('orders.payment.store');
    Route::post('/ordenes/{purchaseOrder}/comprobante-pago', [FinancePurchaseOrderController::class, 'replacePaymentReceipt'])->name('orders.payment-receipt.replace');
    Route::get('/ordenes/{purchaseOrder}/comprobante-pago', [FinancePurchaseOrderController::class, 'paymentReceipt'])->name('orders.payment-receipt');
    Route::get('/ordenes/{purchaseOrder}/cotizacion', [FinancePurchaseOrderController::class, 'quoteSupport'])->name('orders.quote');
    Route::get('/ordenes/{purchaseOrder}/pdf', [FinancePurchaseOrderController::class, 'print'])->name('orders.print');
    Route::get('/historial', [FinancePurchaseOrderController::class, 'history'])->name('orders.history');
    Route::get('/pago-servicios', [FinanceServicePaymentController::class, 'index'])->name('services.index');
    Route::get('/pago-servicios/{service}/{dueDate}', [FinanceServicePaymentController::class, 'paymentForm'])->name('services.payment');
    Route::post('/pago-servicios/{service}/{dueDate}', [FinanceServicePaymentController::class, 'storePayment'])->name('services.payment.store');
    Route::get('/pago-servicios/recibos/{receipt}/factura', [FinanceServicePaymentController::class, 'supportFile'])->name('services.support-file');
    Route::get('/pago-servicios/recibos/{receipt}/comprobante', [FinanceServicePaymentController::class, 'paymentFile'])->name('services.payment-file');
    Route::patch('/pago-servicios/{service}/estado/{status}', [FinanceServicePaymentController::class, 'updateStatus'])->name('services.status');

    Route::get('/os-vigentes', [FinanceSupplyOrderController::class, 'active'])->name('supply-orders.active');
    Route::get('/os-historial', [FinanceSupplyOrderController::class, 'history'])->name('supply-orders.history');
    Route::get('/os/{supplyOrder}/remision', [FinanceSupplyOrderController::class, 'remission'])->name('supply-orders.remission');
    Route::patch('/os/{supplyOrder}/aprobar', [FinanceSupplyOrderController::class, 'approve'])->name('supply-orders.approve');
    Route::patch('/os/{supplyOrder}/rechazar', [FinanceSupplyOrderController::class, 'reject'])->name('supply-orders.reject');

    Route::get('/or-vigentes', [FinanceReimbursementOrderController::class, 'active'])->name('reimbursement-orders.active');
    Route::get('/or-historial', [FinanceReimbursementOrderController::class, 'history'])->name('reimbursement-orders.history');
    Route::patch('/or/{reimbursementOrder}/aprobar', [FinanceReimbursementOrderController::class, 'approve'])->name('reimbursement-orders.approve');
    Route::patch('/or/{reimbursementOrder}/rechazar', [FinanceReimbursementOrderController::class, 'reject'])->name('reimbursement-orders.reject');
    Route::post('/or/{reimbursementOrder}/pago', [FinanceReimbursementOrderController::class, 'storePayment'])->name('reimbursement-orders.payment.store');
    Route::get('/or/{reimbursementOrder}/cotizacion', [FinanceReimbursementOrderController::class, 'quote'])->name('reimbursement-orders.quote');
    Route::get('/or/{reimbursementOrder}/soporte', [FinanceReimbursementOrderController::class, 'support'])->name('reimbursement-orders.support');
    Route::get('/or/{reimbursementOrder}/pago', [FinanceReimbursementOrderController::class, 'payment'])->name('reimbursement-orders.payment');

    Route::get('/autorizaciones', [FinanceAdminController::class, 'users'])->name('admin.users');
    Route::post('/autorizaciones', [FinanceAdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/autorizaciones/{user}', [FinanceAdminController::class, 'updateUser'])->name('admin.users.update');
    Route::patch('/autorizaciones/{user}/estado', [FinanceAdminController::class, 'toggleUser'])->name('admin.users.toggle');
    Route::get('/proveedores', [FinanceAdminController::class, 'providers'])->name('admin.providers');
    Route::get('/proveedores/{provider}/editar', [FinanceAdminController::class, 'editProvider'])->name('admin.providers.edit');
    Route::put('/proveedores/{provider}', [FinanceAdminController::class, 'updateProvider'])->name('admin.providers.update');
    Route::get('/empresas', [FinanceAdminController::class, 'companies'])->name('admin.companies');
    Route::post('/empresas', [FinanceAdminController::class, 'storeCompany'])->name('admin.companies.store');
    Route::get('/empresas/{company}/editar', [FinanceAdminController::class, 'editCompany'])->name('admin.companies.edit');
    Route::put('/empresas/{company}', [FinanceAdminController::class, 'updateCompany'])->name('admin.companies.update');
    Route::delete('/empresas/{company}', [FinanceAdminController::class, 'destroyCompany'])->name('admin.companies.destroy');
});

Route::middleware('auth')->prefix('inventarios')->name('inventory.')->group(function () {
    Route::get('/ordenes-pagadas', [InventoryReceiptController::class, 'index'])->name('orders.index');
    Route::get('/ordenes/{purchaseOrder}/pdf', [InventoryReceiptController::class, 'print'])->name('orders.print');
    Route::get('/ordenes/{purchaseOrder}/comprobante-pago', [FinancePurchaseOrderController::class, 'paymentReceipt'])->name('orders.payment-receipt');
    Route::get('/ordenes/{purchaseOrder}/recepcion', [InventoryReceiptController::class, 'create'])->name('orders.receipt');
    Route::post('/ordenes/{purchaseOrder}/recepcion', [InventoryReceiptController::class, 'store'])->name('orders.receipt.store');
    Route::get('/historial', [InventoryReceiptController::class, 'history'])->name('orders.history');

    Route::get('/catalogos-autorizados', [InventorySupplyOrderController::class, 'catalog'])->name('catalog.index');
    Route::post('/catalogos-autorizados', [InventorySupplyOrderController::class, 'storeCatalog'])->name('catalog.store');
    Route::get('/catalogos-autorizados/{warehouseCatalogItem}/editar', [InventorySupplyOrderController::class, 'editCatalogLegacy'])->name('catalog.edit');
    Route::put('/catalogos-autorizados/{warehouseCatalogItem}', [InventorySupplyOrderController::class, 'updateCatalogLegacy'])->name('catalog.update');
    Route::get('/inventarios', [InventorySupplyOrderController::class, 'inventory'])->name('stock.index');
    Route::post('/inventarios', [InventorySupplyOrderController::class, 'storeInventory'])->name('stock.store');
    Route::get('/almacenes', [InventorySupplyOrderController::class, 'warehouses'])->name('warehouses.index');
    Route::get('/almacenes/cerrar', [InventorySupplyOrderController::class, 'closeWarehouseWindow'])->name('warehouses.close');
    Route::get('/almacenes/suministros/nuevo', [InventorySupplyOrderController::class, 'createSupplyWarehouse'])->name('warehouses.supply.create');
    Route::post('/almacenes/suministros', [InventorySupplyOrderController::class, 'storeSupplyWarehouse'])->name('warehouses.supply.store');
    Route::get('/almacenes/{warehouseKey}/catalogo', [InventorySupplyOrderController::class, 'warehouseCatalog'])->name('warehouses.catalog');
    Route::post('/almacenes/{warehouseKey}/catalogo', [InventorySupplyOrderController::class, 'storeCatalog'])->name('warehouses.catalog.store');
    Route::get('/almacenes/{warehouseKey}/catalogo/{warehouseCatalogItem}/editar', [InventorySupplyOrderController::class, 'editCatalog'])->name('warehouses.catalog.edit');
    Route::put('/almacenes/{warehouseKey}/catalogo/{warehouseCatalogItem}', [InventorySupplyOrderController::class, 'updateCatalog'])->name('warehouses.catalog.update');
    Route::get('/almacenes/{warehouseKey}/editar', [InventorySupplyOrderController::class, 'editWarehouse'])->name('warehouses.edit');
    Route::put('/almacenes/{warehouseKey}', [InventorySupplyOrderController::class, 'updateWarehouse'])->name('warehouses.update');
    Route::get('/almacenes/{warehouseKey}/movimientos', [InventorySupplyOrderController::class, 'warehouseMovements'])->name('warehouses.movements');
    Route::get('/os-vigentes', [InventorySupplyOrderController::class, 'active'])->name('supply-orders.active');
    Route::post('/os/{supplyOrder}/remision', [InventorySupplyOrderController::class, 'deliver'])->name('supply-orders.deliver');
    Route::get('/os/{supplyOrder}/remision', [InventorySupplyOrderController::class, 'remission'])->name('supply-orders.remission');
});

Route::middleware('auth')->prefix('servicios')->name('services.')->group(function () {
    Route::get('/alta', [ServiceController::class, 'create'])->name('create');
    Route::post('/alta', [ServiceController::class, 'store'])->name('store');
    Route::get('/catalogo', [ServiceController::class, 'catalog'])->name('catalog');
    Route::get('/meses', [ServiceController::class, 'months'])->name('months');
    Route::get('/{service}/editar', [ServiceController::class, 'edit'])->name('edit');
    Route::put('/{service}', [ServiceController::class, 'update'])->name('update');
    Route::get('/{service}/{dueDate}/recibo', [ServiceController::class, 'receiptForm'])->name('receipt');
    Route::post('/{service}/{dueDate}/recibo', [ServiceController::class, 'storeReceipt'])->name('receipt.store');
    Route::get('/recibos/{receipt}/factura', [ServiceController::class, 'supportFile'])->name('support-file');
    Route::get('/recibos/{receipt}/comprobante', [ServiceController::class, 'paymentFile'])->name('payment-file');
    Route::patch('/{service}/{dueDate}/monto', [ServiceController::class, 'updateAmount'])->name('amount.update');
    Route::patch('/{service}/estado/{status}', [ServiceController::class, 'updateStatus'])->name('status');
});
