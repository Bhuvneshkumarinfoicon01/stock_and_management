<?php

use App\Http\Controllers\admin\AccountModuleController;
use App\Http\Controllers\admin\BalanceSheetController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\ClerkDashboardController;
use App\Http\Controllers\admin\CommunicationController;
use App\Http\Controllers\admin\CustomerController;
use App\Http\Controllers\admin\DailyOperationsController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\GeneralLedgerController;
use App\Http\Controllers\admin\GlAccountController;
use App\Http\Controllers\admin\HistoryController;
use App\Http\Controllers\admin\IncomingStockController;
use App\Http\Controllers\admin\InventoryDashboardController;
use App\Http\Controllers\admin\InventoryFlowController;
use App\Http\Controllers\admin\InventoryReportController;
use App\Http\Controllers\admin\InventoryValuationController;
use App\Http\Controllers\admin\InvoiceController;
use App\Http\Controllers\admin\JournalEntryController;
use App\Http\Controllers\admin\TransactionAccountMappingController;
use App\Http\Controllers\admin\LedgerController;
use App\Http\Controllers\admin\NotificationController;
use App\Http\Controllers\admin\OrganizationInfoController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\admin\OutgoingStockController;
use App\Http\Controllers\admin\ProductStoreMarkupController;
use App\Http\Controllers\admin\PaymentTermController;
use App\Http\Controllers\admin\PerformingProductController;
use App\Http\Controllers\admin\PermissionController;
use App\Http\Controllers\admin\ProfitLossController;
use App\Http\Controllers\admin\PurchaseDocumentController;
use App\Http\Controllers\admin\RealTimeUpdatesController;
use App\Http\Controllers\admin\ReportSectionController;
use App\Http\Controllers\admin\SalesAnalysisSuperAdminController;
use App\Http\Controllers\admin\SalesPerformanceController;
use App\Http\Controllers\admin\SalesTrendController;
use App\Http\Controllers\admin\SettingController;
use App\Http\Controllers\admin\SidebarLinksController;
use App\Http\Controllers\admin\StockAdjustmentController;
use App\Http\Controllers\admin\StockController;
use App\Http\Controllers\admin\StockTransferController;
use App\Http\Controllers\admin\StoreController;
use App\Http\Controllers\admin\SupplierController;
use App\Http\Controllers\admin\SupplierDueReportController;
use App\Http\Controllers\admin\SupplierLedgerController;
use App\Http\Controllers\admin\SupplierPaymentController;
use App\Http\Controllers\admin\StoreManagerDashboardController;
use App\Http\Controllers\admin\SuperAdminDashboardController;
use App\Http\Controllers\admin\UnitController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ResetPasswordController;
use App\Mail\StockTransferNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('login');
// });

Route::get('/send-mail', function () {
    $recipientEmail = 'dev12@infoiconsoftware.com';
    $subject = 'Test Email from Laravel 12';
    $messageBody = 'This is a test email sent from Laravel 12';

    try {
        Mail::raw($messageBody, function ($message) use ($recipientEmail, $subject) {
            $message
                ->to($recipientEmail)
                ->subject($subject);
        });

        return response('Email sent successfully!', 200);
    } catch (\Exception $e) {
        return response('Error sending email: ' . $e->getMessage(), 500);
    }
});
Route::get('/send-stock-transfer-mail', function () {
    $transfer = (object) [
        'store_from' => 'Store A',
        'store_to' => 'Store B',
        'reference_no' => 'REF123456',
        'arrival_date' => now()->format('Y-m-d'),
        'notes' => 'Test stock transfer'
    ];

    $items = [
        (object) [
            'product_name' => 'Product X',
            'qty' => 10
        ]
    ];

    try {
        Mail::to('dev12@infoiconsoftware.com')->send(new StockTransferNotification($transfer, $items));
        return 'Stock transfer mail sent successfully.';
    } catch (\Exception $e) {
        return 'Mail send failed: ' . $e->getMessage();
    }
});
Route::get('/', [LoginController::class, 'login_view'])->name('login_view');
// Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/', [LoginController::class, 'login'])->name('login');
// Forgot Password
Route::get('/forgot-password', [ForgotPasswordController::class, 'index'])->name('forgot-password');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::group(['middleware' => ['auth']], function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/users', [UserController::class, 'getuser'])->name('admin.users');
    Route::post('/users/{id}/reset-password', [UserController::class, 'resetUserPassword'])->name('admin.reset-user-password');
    Route::post('/users/add', [UserController::class, 'add'])->name('add-employee');

    // Route::get('/json-data', [UserController::class, 'getjsondata'])->name('json-data');
    Route::get('/view-employee', [UserController::class, 'viewEmployee'])->name('view-employee');
    Route::get('/edit-employee/{id}', [UserController::class, 'edit'])->name('edit-employee');
    Route::post('/update-employee', [UserController::class, 'update'])->name('update-employee');

    Route::delete('/delete-employee/{id}', [UserController::class, 'destroy'])->name('delete-employee');

    Route::get('/profile', [UserController::class, 'getData'])->name('profile');
    Route::post('/profile', [UserController::class, 'getProfileInfo'])->name('postProfile');
    Route::get('/change-password', [UserController::class, 'changePassword'])->name('change-password');
    Route::post('/change-password', [UserController::class, 'updatePassword'])->name('update-password');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    Route::get('/superadmin-dashboard', [SuperAdminDashboardController::class, 'superadminDashboard'])->name('superadmin_dashboard');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/storemanager_dashboard', [StoreManagerDashboardController::class, 'storemanagerDashboard'])->name('storemanager_dashboard');
    Route::get('/clerk_dashboard', [ClerkDashboardController::class, 'ClerkDashboard'])->name('clerk_dashboard');

    Route::group(['prefix' => 'product'], function () {
        Route::get('create', [StockController::class, 'create'])->name('stock.create');
    });

    Route::group(['prefix' => 'stock'], function () {
        Route::get('list', [StockController::class, 'index'])->name('stock.list');
        Route::get('by-product', [StockController::class, 'stockByProduct'])->name('stock.by-product');
        Route::get('inventory-valuation', [InventoryValuationController::class, 'index'])->name('inventory-valuation');
        //Route::get('create', [StockController::class, 'create'])->name('stock.create');
        Route::get('edit/{stock}', [StockController::class, 'edit'])->name('stock.edit');
        Route::post('store', [StockController::class, 'store'])->name('stock.store');
        // Route::put('update/{id}', [StockController::class, 'update'])->name('stock.update');
        Route::put('update/{stock}', [StockController::class, 'update'])->name('stock.update');
        Route::delete('delete/{id}', [StockController::class, 'destroy'])->name('stock.delete');
        // EXPORTS
        Route::get('exports', [StockController::class, 'export'])->name('stocks-export');
        Route::get('list-storeMangaer', [StockController::class, 'indexStoreManager'])->name('stock.list.storemanager');
        // IMPORTING CSV FILE
        Route::post('stocks-import', [StockController::class, 'import'])->name('stocks-import');
        Route::post('upload-images', [StockController::class, 'uploadImages'])->name('stocks-upload-images');

        Route::get('admin/stocks/download-dummy', [StockController::class, 'downloadDummy'])->name('stocks-dummy-download');
        Route::get('store-markups', [ProductStoreMarkupController::class, 'index'])->name('stock.store-markups');
        Route::post('store-markups', [ProductStoreMarkupController::class, 'update'])->name('stock.store-markups.update');
    });
    Route::group(['prefix' => 'store'], function () {
        Route::post('/store', [StoreController::class, 'store'])->name('stores_store');
        Route::get('/all', [StoreController::class, 'allStores'])->name('stores_all');

        Route::get('/daily-operations', [DailyOperationsController::class, 'dailyoperations'])->name('daily-operations-overview-admin');
        Route::get('/daily-sales-overview', [DailyOperationsController::class, 'dailySalesOverview'])->name('daily-sales-overview-admin');
    });
    /*Route::group(['prefix' => 'category'], function () {
        Route::get('list', [CategoryController::class,'index'])->name('category.list');
        Route::get('create', [CategoryController::class,'create'])->name('category.create');
        Route::get('edit/{id}', [CategoryController::class,'edit'])->name('category.edit');
        Route::post('store', [CategoryController::class,'storeUpdate'])->name('category.store');
    });*/
    Route::get('/storeCommunication', [CommunicationController::class, 'communications'])->name('stores_communication');
    Route::get('/superAdmin-stor2storecomm', [CommunicationController::class, 'communicationSuperAdmin'])->name('stor2storecomm-superadmin');
    Route::get('/storeManage-stor2storecomm', [CommunicationController::class, 'communicationStoreManager'])->name('stor2storecomm-storemanager');
    Route::get('/fetch-messages', [CommunicationController::class, 'fetchMessages'])->name('fetchMessages');
    Route::post('/store', [CommunicationController::class, 'storeMessage'])->name('messages.store');
    Route::get('/fetch-unread-messages', [CommunicationController::class, 'fetchUnreadMessages'])->name('fetchUnreadMessages');
    Route::post('/mark-messages-as-read', [CommunicationController::class, 'markMessagesAsRead'])->name('markMessagesAsRead');
    Route::post('/send-notification', [NotificationController::class, 'sendNotification']);
    Route::get('/notifications/fetch', [NotificationController::class, 'fetchUserNotifications'])->name('notifications.fetch');
    Route::delete('/notifications/clear', [NotificationController::class, 'clear'])->name('notifications.clear');

    Route::resource('categories', CategoryController::class);

    // Route::resource('orders', OrderController::class);
    Route::resource('stocktransfers', StockTransferController::class);
    Route::resource('units', UnitController::class);

    Route::get('pos-terminal', [OrderController::class, 'index'])->name('pos-terminal');
    Route::get('create', [OrderController::class, 'create'])->name('category.create');
    Route::get('edit/{id}', [OrderController::class, 'edit'])->name('category.edit');
    // Route::post('store', [OrderController::class,'storeUpdate'])->name('category.store');
    Route::post('/add-to-cart', [OrderController::class, 'addToCart'])->name('add.to.cart');
    Route::get('/get-cart', [OrderController::class, 'getCart'])->name('get.cart');
    Route::post('remove-from-cart', [OrderController::class, 'removeFromCart'])->name('remove.from.cart');
    Route::post('clear-cart', [OrderController::class, 'clearCart'])->name('clear.cart');
    Route::post('place-order', [OrderController::class, 'placeOrder'])->name('place-order');
    Route::post('/create-customer', [CustomerController::class, 'store'])->name('create-customer');  // CREATING NEW CUSTOMER

    Route::get('stock-transfer', [StockTransferController::class, 'index'])->name('stock-transfer');
    Route::get('stock-transfer/show/{referenceNo}', [StockTransferController::class, 'show'])->name('stock-transfer.show');
    Route::post('stock-transfer', [StockTransferController::class, 'storeTransfer'])->name('stock-transfer.store');
    Route::get('get-stores', [StockTransferController::class, 'getStores'])->name('get-stores');  // orderController new function
    Route::get('get-products', [StockTransferController::class, 'getProductsByStore'])->name('get-products');  // orderController new function
    Route::get('stock-transfer/edit/{id}', [StockTransferController::class, 'edit'])->name('stock-transfer.edit');
    Route::post('stock-transfer/update', [StockTransferController::class, 'updateTransfer'])->name('stock-transfer.update');
    Route::delete('/delete-transfer', [StockTransferController::class, 'destroy'])->name('stock-transfer-delete');

    Route::get('stock-adjustment', [StockAdjustmentController::class, 'showAdjustmentForm'])->name('stock-adjustment');
    Route::get('stock-adjustment/unit-cost', [StockAdjustmentController::class, 'unitCost'])->name('stock-adjustment.unit-cost');
    Route::post('stock-adjustment', [stockAdjustmentController::class, 'createAdjustment'])->name('create-adjustment');
    Route::put('update/{id}', [StockAdjustmentController::class, 'update'])->name('update-adjustment');
    // Route::post('update/{id}', [StockAdjustmentController::class, 'update'])->name('update-adjustment');
    Route::delete('adjustment/{id}/delete', [StockAdjustmentController::class, 'destroy'])->name('delete-adjustment');
    Route::get('/get-adjustment-data/{id}', [StockAdjustmentController::class, 'getAdjustmentData'])->name('get-adjustment-data');

    Route::get('profit-loss', [ProfitLossController::class, 'showProfitLossStatement'])->name('profit-loss-statement');
    Route::get('/profit-loss/sales', [ProfitLossController::class, 'getMonthlySales'])->name('profit-loss.sales');  // for getting json data for profit loss staement
    Route::get('balance-sheet', [BalanceSheetController::class, 'showBalanceSheet'])->name('balance-sheet');
    Route::get('/balance-sheet/data', [BalanceSheetController::class, 'getBalanceSheetData'])->name('balance-sheet-data');

    // //////////////////////////////////////////////////////////////////////////////////////////////////////////// INOVICE
    Route::get('invoice', [InvoiceController::class, 'index'])->name('invoice');
    Route::get('credit-invoices/data/{scope}', [InvoiceController::class, 'creditInvoices'])->name('credit-invoices.data');
    Route::get('credit-invoices', [InvoiceController::class, 'creditInvoices'])->defaults('scope', 'all')->name('credit-invoices.all');
    Route::get('credit-invoices/paid', [InvoiceController::class, 'creditInvoices'])->defaults('scope', 'paid')->name('credit-invoices.paid');
    Route::get('credit-invoices/unpaid', [InvoiceController::class, 'creditInvoices'])->defaults('scope', 'unpaid')->name('credit-invoices.unpaid');
    Route::get('credit-invoices/partial', [InvoiceController::class, 'creditInvoices'])->defaults('scope', 'partial')->name('credit-invoices.partial');
    Route::get('credit-invoices/overdue', [InvoiceController::class, 'creditInvoices'])->defaults('scope', 'overdue')->name('credit-invoices.overdue');
    Route::get('credit-invoices/estimates', [InvoiceController::class, 'creditInvoices'])->defaults('scope', 'estimates')->name('credit-invoices.estimates');
    Route::get('credit-invoices/cash-sales', [InvoiceController::class, 'creditInvoices'])->defaults('scope', 'cash-sales')->name('credit-invoices.cash-sales');
    Route::get('sales-invoices/data/{scope}', [InvoiceController::class, 'allSalesInvoices'])->name('sales-invoices.data');
    Route::get('sales-invoices/{order}', [InvoiceController::class, 'salesInvoicesShow'])->name('sales-invoices.show');
    Route::get('sales-invoices', [InvoiceController::class, 'allSalesInvoices'])->defaults('scope', 'sales-all')->name('sales-invoices.index');
    Route::post('credit-invoices/{order}/convert-estimate', [InvoiceController::class, 'convertEstimateInvoice'])->name('credit-invoices.convert-estimate');
    Route::get('generate-invoice', [InvoiceController::class, 'generateInvoice'])->name('generate-invoice');
    Route::get('account-module', [AccountModuleController::class, 'index'])->name('account-module');

    // Suppliers & purchases (payables / procurement)
    Route::resource('suppliers', SupplierController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::get('supplier-ledger', [SupplierLedgerController::class, 'index'])->name('supplier-ledger.index');
    Route::get('supplier-payments', [SupplierPaymentController::class, 'index'])->name('supplier-payments.index');
    Route::get('supplier-payments/create', [SupplierPaymentController::class, 'create'])->name('supplier-payments.create');
    Route::post('supplier-payments', [SupplierPaymentController::class, 'store'])->name('supplier-payments.store');
    Route::get('supplier-due-report', [SupplierDueReportController::class, 'index'])->name('supplier-due-report.index');

    Route::get('purchase-orders', [PurchaseDocumentController::class, 'purchaseOrdersIndex'])->name('purchase-orders.index');
    Route::get('purchase-orders/create', [PurchaseDocumentController::class, 'purchaseOrdersCreate'])->name('purchase-orders.create');
    Route::post('purchase-orders', [PurchaseDocumentController::class, 'purchaseOrdersStore'])->name('purchase-orders.store');
    Route::get('purchase-orders/{purchaseOrder}/items', [PurchaseDocumentController::class, 'purchaseOrderItems'])->name('purchase-orders.items');
    Route::get('purchase-orders/{purchaseOrder}/grn-context', [PurchaseDocumentController::class, 'purchaseOrderGrnContext'])->name('purchase-orders.grn-context');
    Route::get('purchase-orders/{purchaseOrder}', [PurchaseDocumentController::class, 'purchaseOrdersShow'])->name('purchase-orders.show');

    Route::get('store-purchases', [PurchaseDocumentController::class, 'storePurchasesIndex'])->name('store-purchases.index');
    Route::get('store-purchases/create', [PurchaseDocumentController::class, 'storePurchasesCreate'])->name('store-purchases.create');
    Route::post('store-purchases', [PurchaseDocumentController::class, 'storePurchasesStore'])->name('store-purchases.store');
    Route::get('store-purchases/{storePurchase}', [PurchaseDocumentController::class, 'storePurchasesShow'])->name('store-purchases.show');

    Route::get('purchase-returns', [PurchaseDocumentController::class, 'purchaseReturnsIndex'])->name('purchase-returns.index');
    Route::get('purchase-returns/create', [PurchaseDocumentController::class, 'purchaseReturnsCreate'])->name('purchase-returns.create');
    Route::post('purchase-returns', [PurchaseDocumentController::class, 'purchaseReturnsStore'])->name('purchase-returns.store');

    Route::get('goods-receipt-notes', [PurchaseDocumentController::class, 'grnIndex'])->name('goods-receipt-notes.index');
    Route::get('goods-receipt-notes/create', [PurchaseDocumentController::class, 'grnCreate'])->name('goods-receipt-notes.create');
    Route::post('goods-receipt-notes', [PurchaseDocumentController::class, 'grnStore'])->name('goods-receipt-notes.store');
    Route::get('goods-receipt-notes/{grn}', [PurchaseDocumentController::class, 'grnShow'])->name('goods-receipt-notes.show');

    Route::get('supplier-purchase-invoices', [PurchaseDocumentController::class, 'purchaseInvoicesIndex'])->name('supplier-purchase-invoices.index');
    Route::get('supplier-purchase-invoices/create', [PurchaseDocumentController::class, 'purchaseInvoicesCreate'])->name('supplier-purchase-invoices.create');
    Route::post('supplier-purchase-invoices', [PurchaseDocumentController::class, 'purchaseInvoicesStore'])->name('supplier-purchase-invoices.store');
    Route::get('supplier-purchase-invoices/{invoice}/items', [PurchaseDocumentController::class, 'purchaseInvoiceItems'])->name('supplier-purchase-invoices.items');
    Route::get('supplier-purchase-invoices/{invoice}/print-data', [PurchaseDocumentController::class, 'purchaseInvoicePrintData'])->name('supplier-purchase-invoices.print-data');
    Route::get('supplier-purchase-invoices/{invoice}', [PurchaseDocumentController::class, 'purchaseInvoicesShow'])->name('supplier-purchase-invoices.show');
    Route::put('supplier-purchase-invoices/{invoice}', [PurchaseDocumentController::class, 'purchaseInvoicesUpdate'])->name('supplier-purchase-invoices.update');
    Route::post('check-supplier-credit-limit', [PurchaseDocumentController::class, 'checkSupplierCreditLimit'])->name('check-supplier-credit-limit');
    Route::get('purchase-invoices-on-hold', [PurchaseDocumentController::class, 'purchaseInvoicesOnHold'])->name('purchase-invoices-on-hold');
    Route::post('purchase-invoices-on-hold/{invoiceId}/resume', [PurchaseDocumentController::class, 'resumePurchaseInvoice'])->name('purchase-invoices.resume');
    Route::post('purchase-invoices-on-hold/{invoiceId}/cancel', [PurchaseDocumentController::class, 'cancelPurchaseInvoice'])->name('purchase-invoices.cancel');

    Route::get('batch-purchasing', [PurchaseDocumentController::class, 'batchPurchasesIndex'])->name('batch-purchases.index');
    Route::get('batch-purchasing/create', [PurchaseDocumentController::class, 'batchPurchasesCreate'])->name('batch-purchases.create');
    Route::get('batch-purchasing/search-invoices', [PurchaseDocumentController::class, 'searchBatchPurchaseInvoices'])->name('batch-purchases.search-invoices');
    Route::post('batch-purchasing/preview', [PurchaseDocumentController::class, 'previewBatchPurchase'])->name('batch-purchases.preview');
    Route::get('batch-purchasing-on-hold', [PurchaseDocumentController::class, 'batchPurchasesOnHold'])->name('batch-purchases-on-hold');
    Route::post('batch-purchasing', [PurchaseDocumentController::class, 'batchPurchasesStore'])->name('batch-purchases.store');
    Route::get('batch-purchasing/{batch}', [PurchaseDocumentController::class, 'batchPurchasesShow'])->name('batch-purchases.show');
    Route::put('batch-purchasing/{batch}', [PurchaseDocumentController::class, 'batchPurchasesUpdate'])->name('batch-purchases.update');

    Route::get('sales-invoices-on-hold', [OrderController::class, 'salesInvoicesOnHold'])->name('sales-invoices-on-hold');
    Route::post('sales-invoices-on-hold/{orderId}/resume', [OrderController::class, 'resumeSaleInvoice'])->name('sales-invoices.resume');
    Route::post('sales-invoices-on-hold/{orderId}/cancel', [OrderController::class, 'cancelSaleInvoice'])->name('sales-invoices.cancel');

    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::get('chart-of-accounts', [GlAccountController::class, 'index'])->name('chart-of-accounts');
        Route::post('chart-of-accounts', [GlAccountController::class, 'store'])->name('chart-of-accounts.store');
        Route::put('chart-of-accounts/{glAccount}', [GlAccountController::class, 'update'])->name('chart-of-accounts.update');
        Route::post('chart-of-accounts/{glAccount}/toggle', [GlAccountController::class, 'toggle'])->name('chart-of-accounts.toggle');

        Route::get('journals', [JournalEntryController::class, 'index'])->name('journals.index');
        Route::get('journals/create', [JournalEntryController::class, 'create'])->name('journals.create');
        Route::post('journals', [JournalEntryController::class, 'store'])->name('journals.store');
        Route::get('journals/{journalEntry}', [JournalEntryController::class, 'show'])->name('journals.show');
        Route::post('journals/{journalEntry}/post', [JournalEntryController::class, 'post'])->name('journals.post');
        Route::delete('journals/{journalEntry}', [JournalEntryController::class, 'destroy'])->name('journals.destroy');

        Route::get('general-ledger', [GeneralLedgerController::class, 'index'])->name('general-ledger');
        Route::get('trial-balance', [GeneralLedgerController::class, 'trialBalance'])->name('trial-balance');

        Route::get('transaction-mappings', [TransactionAccountMappingController::class, 'index'])->name('transaction-mappings');
        Route::post('transaction-mappings', [TransactionAccountMappingController::class, 'update'])->name('transaction-mappings.update');
    });
    Route::get('/view-invoice', [InvoiceController::class, 'viewInvoice'])->name('view-invoice');
    Route::get('invoice/{order}/payment-info', [InvoiceController::class, 'invoicePaymentInfo'])->name('invoice-payment-info');
    Route::get('invoice-filter', [InvoiceController::class, 'filter'])->name('invoice.filter');
    Route::get('payment-terms', [PaymentTermController::class, 'index'])->name('payment-terms.index');
    Route::post('payment-terms', [PaymentTermController::class, 'store'])->name('payment-terms.store');
    Route::post('payment-terms/{paymentTerm}/update', [PaymentTermController::class, 'update'])->name('payment-terms.update');
    Route::post('payment-terms/{paymentTerm}/toggle-status', [PaymentTermController::class, 'toggleStatus'])->name('payment-terms.toggle-status');
    Route::delete('payment-terms/{paymentTerm}', [PaymentTermController::class, 'destroy'])->name('payment-terms.destroy');

    // //////////////////////////////////////////////////////////////////////////////////////////////////////////// LEDGER
    // Route::view('ledger-view', 'admin.view-ledger')->name('view-ledger');
    Route::group(['prefix' => 'ledger'], function () {
        Route::get('/', [LedgerController::class, 'index'])->name('ledger');
        Route::post('store-ledger', [LedgerController::class, 'storeLedger'])->name('store-ledger');  // STORES LEDGER TO DB
        Route::get('view-ledger/{id}', [LedgerController::class, 'viewLedgerEntries'])->name('ledgerEntries');
        Route::get('{id}/entries', [LedgerController::class, 'getLedgerEntries'])->name('get-ledger-entries');
        Route::post('{id}/update', [LedgerController::class, 'updateLedger'])->name('update-ledger');
        Route::delete('{id}', [LedgerController::class, 'destroy'])->name('delete-ledger');
    });

    // ////////////////////////////////////////////////////////////////////////////////////////////////////////////

    Route::get('transaction-mgnt', [OrderController::class, 'manageTransactions'])->name('transaction-manage');  // TRANSACTION_MANAGE
    Route::get('order-items/{order_id}', [OrderController::class, 'getOrderItems'])->name('get-orderItems');  // GET-ORDER-ITEMS
    Route::view('pos-sale-insight', 'admin.pos-sale-insight')->name('pos-sale-insight');

    Route::get('inventory-flow-admin', [InventoryFlowController::class, 'index'])->name('inventory-flow-admin');
    Route::get('inventory-flow-superAdmin', [InventoryFlowController::class, 'indexSuperAdmin'])->name('inventory-flow-superAdmin');
    Route::get('top-performing-product', [PerformingProductController::class, 'index'])->name('top-performing-product');
    Route::get('/top-products-data', [PerformingProductController::class, 'getTopProductsData'])->name('top-products-data');
    Route::get('/under-products-data', [PerformingProductController::class, 'getUnderProductsData'])->name('under-products-data');
    Route::get('/top-products-data/view-all', [PerformingProductController::class, 'topViewAll'])->name('top-view-all');
    Route::get('/under-products-data/view-all', [PerformingProductController::class, 'underViewAll'])->name('under-view-all');
    // ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // STORE- MANAGER ROUTES//

    Route::get('sales', [InvoiceController::class, 'sales'])->name('sales');
    Route::get('/view-details', [InvoiceController::class, 'showSalesDetail'])->name('sales_details');
    Route::get('storemanager/sales-trend', [SalesTrendController::class, 'storemgrSalesReport'])->name('salesTrend');

    Route::get('customer-report', [InvoiceController::class, 'customerReportIndex'])->name('customer-report');
    Route::get('customer-report-sold-products', [InvoiceController::class, 'soldProductReportIndex'])->name('customer-report-sold-products');
    Route::get('incoming-stock', [IncomingStockController::class, 'index'])->name('incoming-stock');
    Route::post('/update-transfer-status/{id}', [IncomingStockController::class, 'updateTransferStatus'])->name('update-transfer-status');

    Route::get('outgoing-stock', [OutgoingStockController::class, 'index'])->name('outgoing-stock');
    Route::get('real-time-stock', [RealTimeUpdatesController::class, 'index'])->name('real-time-stock');
    Route::get('sales-performance', [SalesPerformanceController::class, 'index'])->name('sales-performance');
    // Route::get('/monthly-data', [SalesPerformanceController::class, 'getMonthlySalesData'])->name('monthly-data');

    Route::get('inventory-report', [InventoryReportController::class, 'index'])->name('inventory-report');
    Route::get('inventory-report/outOfStock-view', [InventoryReportController::class, 'getOutOfStock'])->name('outOfStock-view');
    Route::get('inventory-report/lowOnStock-view', [InventoryReportController::class, 'getLowStock'])->name('lowOnStock-view');

    // Route::view('stor2storecomm', 'storemanager.stor2storecomm')->name('stor2storecomm');
    Route::get('employee-list', [StoreController::class, 'index'])->name('employee-list');
    // Route::view('User', 'storemanager.User')->name('User');
    Route::get('setting', [SettingController::class, 'index'])->name('setting');
    Route::post('seetings-store', [SettingController::class, 'storeSetting'])->name('setting-store');
    Route::post('settings/purge-data', [SettingController::class, 'purgeUserGeneratedData'])->name('setting-purge-data');
    Route::post('settings/backup/full', [SettingController::class, 'createFullDatabaseBackup'])->name('setting-backup-full');
    Route::post('settings/backup/table', [SettingController::class, 'createTableBackup'])->name('setting-backup-table');
    Route::get('settings/backup/download/{filename}', [SettingController::class, 'downloadDatabaseBackup'])->name('setting-backup-download');
    Route::delete('settings/backup/{filename}', [SettingController::class, 'deleteDatabaseBackup'])->name('setting-backup-delete');

    Route::post('/select-store', [StoreController::class, 'selectStore'])->name('select.store');
    Route::post('add', [StoreController::class, 'addEmployee'])->name('add-store-staff');
    Route::get('/view-staff', [StoreController::class, 'viewEmployee'])->name('view-staff');
    Route::get('/edit-staff', [StoreController::class, 'editEmployee'])->name('edit-staff');
    Route::post('/update-staff', [StoreController::class, 'updateEmployee'])->name('update-staff');
    Route::delete('/delete-staff', [StoreController::class, 'deleteEmployee'])->name('delete-staff');

    Route::get('customer', [CustomerController::class, 'index'])->name('customer');
    Route::get('customer/{customer}/orders', [CustomerController::class, 'customerOrders'])->name('customer-orders');
    Route::get('customer/{customer}/orders/data', [CustomerController::class, 'customerOrdersData'])->name('customer-orders.data');
    Route::post('customer/{customer}/orders/{order}/payments', [CustomerController::class, 'storeOrderPayment'])->name('customer-order-payment.store');
    Route::get('customer/{customer}/ledger/data', [CustomerController::class, 'customerLedgerData'])->name('customer-ledger.data');
    Route::get('/view-customer', [CustomerController::class, 'viewCustomer'])->name('view-customer');
    Route::get('/edit-customer', [CustomerController::class, 'editCustomer'])->name('edit-customer');
    Route::post('/update-customer', [CustomerController::class, 'updateCustomer'])->name('update-customer');
    Route::delete('/delete-customer', [CustomerController::class, 'deleteCustomer'])->name('delete-customer');
    Route::post('/change-status-customer', [CustomerController::class, 'changeStatusCustomer'])->name('change-status-customer');

    Route::get('payment-trend-data', [DashboardController::class, 'getPaymentTrendData'])->name('payment.trend.data');  // PAYMENT METHOD CHART IN SALES INSIGHT
    Route::get('dashboard/pos-sales-data', [DashboardController::class, 'getPosSalesData']);  // POS SALES CHART IN SALES INSIGHT

    Route::get('sales-trend', [SalesTrendController::class, 'index'])->name('sales-trend');
    Route::get('/sales-trend-report/fetch', [SalesTrendController::class, 'fetchSalesData']);
    Route::get('/filter', [SalesTrendController::class, 'filter'])->name('sales-trend-filter');

    // STOREMGR DASBOARD SUB VIEWS
    // Route::view('sales-summary', 'storemanager.index')->name('sales-summary');
    Route::get('report-section', [ReportSectionController::class, 'index'])->name('report-section');
    Route::get('customer-insights', [ReportSectionController::class, 'customerInsightsView'])->name('customer-insights');

    // Route::view('sales-summary', 'storemanager.index')->name('sales-summary');

    Route::get('inventory-summary', [InventoryDashboardController::class, 'index'])->name('inventory-summary');
    Route::get('/low-stock/filter', [InventoryDashboardController::class, 'filterLowStock'])->name('low-stock.filter');
    Route::get('/out-of-stock/filter', [InventoryDashboardController::class, 'filterOutOfStock'])->name('out-of-stock.filter');

    // /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // SUPER ADMIN ROUTES SIDEBAR
    Route::view('inventory-dashboard', 'SuperAdmin.inventory')->name('inventory-dashboard');
    Route::view('finance-dashboard', 'SuperAdmin.finance')->name('finance-dashboard');

    Route::get('daily-operations-overview', [DailyOperationsController::class, 'index'])->name('daily-operations-overview-superadmin');
    Route::view('manage-stock', 'SuperAdmin.manage-stocks')->name('manage-stocks-superadmin');

    Route::view('Invoice', 'SuperAdmin.store-manager')->name('store-manager-superadmin');

    Route::get('sales-analysis', [SalesAnalysisSuperAdminController::class, 'index'])->name('sales-analysis-superadmin');

    Route::resource('organization-info', OrganizationInfoController::class)->except(['show']);

    Route::get('store-list', [StoreController::class, 'superAdminStoreList'])->name('store-list-superadmin');
    Route::get('store-list/edit/{id}', [StoreController::class, 'editStore'])->name('edit-store-superadmin');
    Route::post('store-list/update/{id}', [StoreController::class, 'updateStore'])->name('update-store-superadmin');
    Route::delete('store-list/delete/{id}', [StoreController::class, 'destroy'])->name('delete-store-superadmin');

    Route::get('superAdmin/low-stock-products', [SuperAdminDashboardController::class, 'lowStockProducts'])->name('view-all-low-stock-products');


    //Route::get('/test-superadmin-store-list', [StoreController::class, 'superadmminstorelist_test'])->name('store-list-superadmin');


    // ROUTES FOR STORE EMPLOYEE IN SUPER-ADMIN AND ADMIN SIDEBAR
    Route::get('store-employee', [StoreController::class, 'indexStoreEmployee'])->name('store-employee');
    Route::post('/add-satff-admin', [StoreController::class, 'addEmployeeAdmin'])->name('add-store-staff-admin');
    Route::get('/view-staff-admin', [StoreController::class, 'viewEmployeeAdmin'])->name('view-staff-admin');
    Route::get('/edit-staff-admin', [StoreController::class, 'editEmployeeAdmin'])->name('edit-staff-admin');
    Route::post('/update-staff-admin', [StoreController::class, 'updateEmployeeAdmin'])->name('update-staff-admin');
    Route::delete('/delete-staff-admin', [StoreController::class, 'deleteEmployeeAdmin'])->name('delete-staff-admin');

    // EXPORTS
    Route::get('stock-transfers/export', [StockTransferController::class, 'exportStockTransferData'])->name('stock-transfers.export');
    Route::get('stock-adjustments/export', [StockAdjustmentController::class, 'exportStockAdjustmentData'])->name('stock-adjustments.export');
    Route::get('export-profit-loss', [ProfitLossController::class, 'exportMonthlyProfitLoss'])->name('export.profit-loss');
    Route::get('balance-sheet-export', [BalanceSheetController::class, 'exportBalanceSheet'])->name('balance-sheet.export');

    Route::get('invoices-export', [InvoiceController::class, 'exportCsv'])->name('invoices.export');

    Route::post('change-status', [UserController::class, 'changeStatus'])->name('change-status');
    Route::post('change-status-storeEmployee', [StoreController::class, 'changeStatusStoreEmployee'])->name('change-status-storeEmployee');

    // INVOICE GENERATION
    Route::post('generate-invoice-order', [OrderController::class, 'invoiceOrderCreate'])->name('invoice-order');
    Route::post('check-customer-credit-limit', [OrderController::class, 'checkCustomerCreditLimit'])->name('check-customer-credit-limit');
    Route::get('get-products-invoice', [InvoiceController::class, 'getProductsByStore'])->name('get-products-invoice');
    Route::get('edit-invoice', [InvoiceController::class, 'editInvoice'])->name('invoice-edit');
    Route::post('update-invoice', [OrderController::class, 'invoiceOrderUpdate'])->name('invoice-update');
    // Update unit price for invoice items (without recreating order)
    Route::post('invoice-item-price/update', [OrderController::class, 'invoiceItemPriceUpdate'])->name('invoice-item-price.update');

    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/data', [HistoryController::class, 'data'])->name('history.data');  // DataTables feed
    Route::get('/show/{history}', [HistoryController::class, 'show'])->name('history.show');  // modal payload

    Route::post('/users/save-permissions', [PermissionController::class, 'savePermissions'])->name('save-permission');
    Route::get('/users/{id}/permissions', [PermissionController::class, 'getPermissions']);

    Route::post('/sidebar-links/save', [SidebarLinksController::class, 'store'])->name('sidebar-links.store');
    Route::get('/sidebar-links/fetch', [SidebarLinksController::class, 'fetch'])->name('sidebar-links.fetch');
});


Route::get('/optimize-clear', function () {
    // Run the optimize:clear command
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    //Artisan::call('config:cache');
    //Artisan::call('route:cache');
    //Artisan::call('view:cache');
    //Artisan::call('optimize:clear');
    return 'Application cache cleared and optimized successfully!';
    //return 'Cache cleared successfully!';
});







Route::get('/test', function () {
    return view('testdel');
});



/*
php artisan optimize:clear
php artisan route:cache
php artisan route:clear

URL: http://eps.infoicon.co.in:201/ 
// Super admin detail username: Superadmin@gmail.com Password: Gd6Y2fiTcU1VD1i 
// Admin detail username: admin@gmail.com Password: K3RWbpPeaO7TAFE 
// Store manager detail username: storemanager@gmail.com Password: KRNrCQvwpjx1h6v 
// Clerk Details username: clerk@gmail.com Password: Admin@123


ssh -i ~/.ssh/id_rsa_guest500 guest500@comfortsystemsja.ddns.net
The server and login information remain the same from the last time it was sent to you.  Nevertheless, please see below:
Server Info	LEMP (Ubuntu 24.04 | Nginx | MariaDB | Php 8.3)
URL	comfortsystemsja.ddns.net
Port	22
Access Type	SSH
Username	guest500
Password	LaHy6Mghum8R
Authentication Type	Publickey
Passphrase	Isaiah1:18
Database Username	guest500
Database Password	LaHy6Mghum8R
Application URL	


http://comfortsystemsja.ddns.net

info@info-H510M-H:~/Desktop$ scp -i ~/.ssh/id_rsa_guest500 /var/www/html/liveserver/11_03_2026_stock_mgnt.zips guest500@comfortsystemsja.ddns.net:home/var/www/html/
info@info-H510M-H:~/Desktop$ scp -i ~/.ssh/id_rsa_guest500 /var/www/html/11_03_2026_stock_mgnt.zips guest500@comfortsystemsja.ddns.net:home/var/www/html/

scp: stat local "/var/www/html/liveserver/11_03_2026_stock_mgnt.zips": No such file or directory

//==================================================================================
//php artisan migrate --path=database/migrations/2026_04_14_120000_add_receivable_columns_to_orders_table.php
//php artisan migrate --path=database/migrations/2026_04_14_120100_create_customer_payments_table.php
//php artisan migrate --path=database/migrations/2026_04_14_120200_create_customer_ledger_entries_table.php
//php artisan migrate --path=database/migrations/2026_04_14_130000_create_payment_terms_table.php
//php artisan migrate --path=database/migrations/2026_05_13_120000_create_double_entry_accounting_tables.php
//php artisan migrate --path=database/migrations/2026_05_14_100000_create_suppliers_and_purchases_tables.php
//php artisan migrate --path=database/migrations/2026_05_26_064500_add_product_account_fields_to_stocks_table.php
//php artisan migrate --path=database/migrations/2026_05_26_072500_add_product_type_to_stocks_table.php
//php artisan migrate --path=database/migrations/2026_06_02_120000_add_invoice_status_and_number_to_orders_table.php
//php artisan migrate --path=database/migrations/2026_06_02_120100_add_invoice_prefix_to_stores_table.php
//php artisan migrate --path=database/migrations/2026_06_02_120200_add_credit_limit_to_customers_table.php  
//php artisan migrate --path=database/migrations/2026_06_03_123152_add_credit_limit_to_suppliers_table.php
//php artisan migrate --path=database/migrations/2026_06_03_124108_add_current_sale_invoice_sequence_to_stores_table.php
//php artisan migrate --path=database/migrations/2026_06_03_140000_add_invoice_status_to_supplier_purchase_invoices_table.php
//php artisan migrate --path=database/migrations/2026_06_03_150000_add_draft_reference_and_credit_limit_flags.php
//php artisan migrate --path=database/migrations/2026_06_03_150100_add_credit_limit_override_users_setting.php
//php artisan migrate --path=database/migrations/2026_04_01_000001_add_status_to_customers_table.php
//php artisan migrate --path=database/migrations/2026_06_05_160000_add_purchase_doc_sequences_to_stores_table.php
//php artisan migrate --path=database/migrations/2026_06_05_170000_add_internal_invoice_number_to_supplier_purchase_invoices.php
//php artisan migrate --path=database/migrations/2026_06_05_171000_create_transaction_account_mappings_table.php
//php artisan migrate --path=database/migrations/2026_06_05_180000_add_per_line_tax_fields.php
//php artisan migrate --path=database/migrations/2026_06_10_120000_add_per_line_tax_to_purchase_invoices.php
//php artisan migrate --path=database/migrations/2026_06_10_130000_add_per_line_tax_to_purchase_documents.php
//php artisan migrate --path=database/migrations/2026_06_11_120000_add_taxation_payable_gl_account.php


//php artisan migrate --path=database/migrations/2026_06_11_100000_link_purchase_orders_invoices_grn.php
//php artisan migrate --path=database/migrations/2026_06_12_100000_create_batch_purchases_tables.php
//php artisan migrate --path=database/migrations/2026_06_13_100000_add_inventory_valuation_settings.php
//php artisan migrate --path=database/migrations/2026_06_13_120000_add_product_cost_and_store_markups.php

//php artisan migrate --path=database/migrations/2026_06_26_064948_create_agent_conversations_table.php

//==================================================================================
    
Ability to change the unit price when generating an invoice  --> done
Clerks don't need to see Financial Oversight (Profit & Loss | Balance Sheet) -->done
Search Field in the Store List only searching on the Name Column, needs to search on all (or more) columns. --> done
Clerk should not be able to delete transactions.  Only SuperAdmins should have that permission. -->already not showing delete opotion, where it showing this option please explain more
Daily Operation Overview - For the different data points, give the option to search by date range, not just default to the current day.  --> explain more where we have to add date etc.
Invoice Report - Please add customer name to the columns shown --> done
Please add the ability to reset passwords, since the SuperAdmin cannot reset passwords for other users -->done
All users should be able to see stock in all locations and be able to filter by location as well --> done only can see cann't modify

The Add Stock section seems to be where you create individual products, and as such, we would not need Supplier Name, or Purchase Date there (at least, not during the initial creating of the product).  As such, rename this from Add Stock to Create Product   --> done we changed Add Stock to Create Product
There needs to be a new section to Add Stock in bulk, using a layout similar to the Invoice Management > Generate Invoice, where multiple products purchased from a supplier can be added.  You can name this section Add Stock --> we added Add stock in sidebar,explain more what fields are required, need same fields as in add products
Show totals in reports for numeric columns   -->done
Give the ability to print reports.  It should not be an on-screen option only. --> we have given print option in custmized report->sales trend, more explain, where we have to give print option 
More reports are needed, than what are currently available. -->explain more, which type reports need to add 
The SuperAdmin is the ultimate user with all permissions granted. As such, every  option, menu, screen, report etc, that is available to all other users, must be available to the SuperAdmin. --> super admin always one, if you want we can give more permissions to admin, 
In trying to establish the stores, we were unable to create the store, as at the end of the process, the system rejected all emails to create the store, saying that the email has already been used.  As such, we were unable to go beyond that point, however, we noticed that the stores are listed in he drop-down store-selector at the top of the page, but are shown nowhere else; this is mystifying. --> done, email should be unique, its should be different per store 
Concerning the adding of stock to the system, we encountered further difficulties, as we couldn't enter any new categories.  All attempts gave an "unable to add new category" message.  We were unable to verify the performance of a number of fields, as again, the product could not be saved, as it gave a system error, and crashed the system. -->done

source /var/www/nginx/inteacosmetics/prestashop9.sql
//==================================================================================
//==================================================================================
Fix search on store section.
Fix serach on stock list section.
add customer name on invoice report section.
added super admin can reset password for other users.
clerk cant see financial oversight section.
//==================================================================================

Step 1️⃣ — Rename current live project
sudo mv /var/www/html/stockmgnt /var/www/html/old_stockmgnt

This preserves the old project.

Nothing changes for the new upload yet.

Step 2️⃣ — Rename new project when ready to go live

Once you verify the new project works (you can test by temporarily linking it or checking URLs):

sudo mv /var/www/html/stock_mgnt /var/www/html/stockmgnt
Step 3️⃣ — Set permissions for the new live folder
sudo chown -R www-data:www-data /var/www/html/stockmgnt
sudo chmod -R 755 /var/www/html/stockmgnt
Step 4️⃣ — Optional: Remove old project after confirming

Once the new project works fine:

sudo rm -rf /var/www/html/old_stockmgnt

✅ This method is safe because the old project stays intact until you are sure the new one works.

If you want, I can also write a one-line command sequence that:

Renames current live → backup

Renames new upload → live

Sets permissions

…so you can deploy the new project safely with just one command.

Do you want me to do that?

sudo rm /var/www/html/11_03_2026_stock_mgnt.zip
//==================================================================================

✅ How to fix

You have two main options:

Option 1 — Upload to your home folder, then move with sudo on the server

1️⃣ Upload to your home directory:

scp -i ~/.ssh/id_rsa_guest500 ~/Desktop/11_03_2026_stock_mgnt.zip guest500@comfortsystemsja.ddns.net:~/

This works because guest500 always has write access to their home folder.

2️⃣ SSH into the server:

ssh -i ~/.ssh/id_rsa_guest500 guest500@comfortsystemsja.ddns.net

3️⃣ Move the ZIP to /var/www/html/ with sudo:

sudo mv ~/11_03_2026_stock_mgnt.zip /var/www/html/
cd /var/www/html/
sudo unzip 11_03_2026_stock_mgnt.zip
sudo chown -R www-data:www-data stock_mgmt
sudo chmod -R 755 stock_mgmt

Now the project is in /var/www/html/stock_mgmt with correct permissions for the web server.

Option 2 — Give guest500 temporary write permission (less recommended)
sudo chmod o+w /var/www/html/
scp -i ~/.ssh/id_rsa_guest500 ~/Desktop/11_03_2026_stock_mgnt.zip guest500@comfortsystemsja.ddns.net:/var/www/html/
sudo chmod o-w /var/www/html/

This is riskier because you open /var/www/html/ for writing.

✅ Recommended

Option 1 is safer and standard for deploying projects.

If you want, I can write a single one-line workflow that:

Uploads your ZIP to the server

Moves it to /var/www/html/

Unzips it

Sets correct permissions

…all with your existing SSH key, so you just run one command from your local terminal.

Do you want me to do that?
 
//===============================================================================
Yes 👍 You can upload a ZIP file from the terminal using SFTP or SCP since your server supports SSH.

1️⃣ Using SFTP (Recommended)

Connect first:

sftp -i ~/.ssh/id_rsa_guest500 guest500@comfortsystemsja.ddns.net

Enter your passphrase when prompted.

Then upload the ZIP file:

put file.zip

Example:

put project.zip

Upload to a specific folder:

put project.zip /home/guest500/

Useful commands inside SFTP:

ls        # list remote files
lcd       # change local folder
cd        # change server folder
put       # upload file
exit      # disconnect
2️⃣ Using SCP (Fastest way)

You can upload directly in one command:

scp -i ~/.ssh/id_rsa_guest500 project.zip guest500@comfortsystemsja.ddns.net:/home/guest500/

It will ask for the passphrase and upload immediately.

3️⃣ Upload Entire Folder as ZIP

If you want to zip and upload in one flow:

zip -r project.zip project/
scp -i ~/.ssh/id_rsa_guest500 project.zip guest500@comfortsystemsja.ddns.net:/home/guest500/
4️⃣ Unzip on the server

After uploading, SSH into the server:

ssh -i ~/.ssh/id_rsa_guest500 guest500@comfortsystemsja.ddns.net

Then run:

unzip project.zip

✅ Pro tip (very useful):
If you're uploading large files, use:

rsync -avz -e "ssh -i ~/.ssh/id_rsa_guest500" project.zip guest500@comfortsystemsja.ddns.net:/home/guest500/

It is faster and resumable.

If you want, I can also show a 1-command trick DevOps engineers use to upload a folder without creating a zip first (very powerful). 🚀
///////====================================================================
Since your project is in:

/var/www/html/stock_mgmt

you can zip it and transfer it to another server using SSH (SCP). Here is the simple workflow.

1️⃣ Create the ZIP file

Go to the web root:

cd /var/www/html

Create the zip:

zip -r stock_mgmt.zip stock_mgmt

This will create:

/var/www/html/stock_mgmt.zip
2️⃣ Transfer the ZIP to the remote server

Use SCP over SSH:

scp -i ~/.ssh/id_rsa_guest500 /var/www/html/stock_mgmt.zip guest500@comfortsystemsja.ddns.net:/home/guest500/

You will be asked for the passphrase of the key.

After this, the file will be uploaded to:

/home/guest500/stock_mgmt.zip

on the server.

3️⃣ Login to the server
ssh -i ~/.ssh/id_rsa_guest500 guest500@comfortsystemsja.ddns.net
4️⃣ Move it to web directory

Example:

sudo mv /home/guest500/stock_mgmt.zip /var/www/html/
5️⃣ Unzip on the server
cd /var/www/html
sudo unzip stock_mgmt.zip

Now your project will be available again as:

/var/www/html/stock_mgmt

✅ Even faster (no zip needed):

You can transfer the whole folder directly:

scp -i ~/.ssh/id_rsa_guest500 -r /var/www/html/stock_mgmt guest500@comfortsystemsja.ddns.net:/var/www/html/

💡 If you want, I can also show a very powerful command using rsync that DevOps engineers use to deploy projects to servers in one command (much faster than zip + scp).

On demo server, when we changed in file its effect the code, but on live server we changed in file it not reflect.
As on screenshot image home page showing on demo server (attached file 01_localserver.png).
We changed in index file (find attached file 02_localserver_indexfile.png)
After changed in index file we can see the info page on  home screen (find the attached file 03_localserver_homepage.png)

Same changs we make on live server index file (find the attached file 04_liverserverindexfile.png)
But still showing home page not showing info (find the attaced file 05_liveserverhomepage.png)

We check on private window file permission given 777 


On demo server its uploading the data (find the attached file 06_demoserver.png )
on live server its not uploading the (find the attached file 07_liverserver.png)
wile data in database on live server (find the attached file 08_liveserverdb.png)

//////////////////////////////////////////

Yes! ✅ With Ollama + Continue in VS Code, you can start a new project just by giving a prompt, and the model will generate the project structure, files, and code for you.

Here’s how it works:

🧩 1️⃣ New Project via Prompt
Example prompt:
Create a new Python Flask project with:
- main.py for API routes
- utils.py for helper functions
- requirements.txt with Flask and requests

What happens:

Continue creates the suggested files in your open workspace
Populates each file with starter code
Inline suggestions can expand or fix code as you type
🧩 2️⃣ Workflow in VS Code
Open a new empty folder in VS Code
Open the Continue sidebar or just type in a new file
Give your prompt in a comment or chat panel, e.g.:
# Generate a Python project for a REST API with authentication
Continue will:
Suggest file contents for app.py, auth.py, etc.
Let you accept inline or insert automatically
🧩 3️⃣ Auto-create multiple files

You can do it interactively:

Prompt for the project structure
Accept suggestions for each file
Use Ctrl + I to refine or expand functions
🧩 4️⃣ Optional: Fully automated with Python agent

You can combine Ollama + a mini Python agent like I showed before:

Give a single prompt:
Create a Python project for a blog website with Flask and SQLAlchemy
Agent automatically:
Creates files (app.py, models.py, routes.py)
Writes starter code
Saves files in your folder

⚡ Works fully locally — no internet required for code generation

💡 Tips
Keep one model running to avoid lag
Use quantized model for smooth performance (codellama:13b-q4 or deepseek-coder:6.7b)
VS Code inline suggestions + agent combo = closest to “Copilot but fully local”

If you want, I can write a ready-to-use Python agent that lets you create a full project from a single prompt automatically — optimized for 16GB RAM and VS Code.

Do you want me to do that?

========================================================
name: Local Config
version: 1.0.0
schema: v1
models:
  - name: deepseek-coder
    version: 6.7b-instruct
    path: deepseek-coder:6.7b-instruct
    settings:
      max_threads: 4           # Use 4 CPU threads
      max_context_tokens: 2048 # Maximum context for prompt understanding
      low_memory_mode: false   # Full performance mode
      batch_size: 1            # Send 1 prompt at a time
      gpu_acceleration: false  # Set true if you have a GPU
      prompt_behavior:          # Custom settings for prompt execution
        create_file: true      # Model can suggest creating new files
        edit_file: true        # Model can suggest edits to existing files
        read_file: true        # Model can read file content when prompted
        auto_confirm: false    # Avoid auto-executing destructive changes
        default_file_path: ./stock_mgnt  # Base path for file operations
        logging: true          # Save all prompt results for reference
        code_formatting: true  # Auto-format code suggestions

🏆 Final ranking (coding use)
🔥 Best to worst for development:
🥇 Qwen2.5-Coder → BEST for coding + agent use
🥈 DeepSeek-Coder-V2 → best for heavy projects
🥉 Llama 3.1 → best for reasoning/design
⚙️ DeepSeek-Coder 6.7B → lightweight fallback
💡 What you should use
If you want VS Code AI like Cursor:

👉 Use Qwen2.5-Coder

If you want powerful backend coding:

👉 DeepSeek-Coder-V2

If you want lightweight local setup:

👉 DeepSeek-Coder 6.7B

🚀 Recommended setup (best combo)

Use multiple models:

Qwen2.5-Coder → coding + editing
Llama 3.1 → planning + architecture
DeepSeek-Coder-V2 → heavy reasoning fallback

If you want, I can:
✔ give you exact Ollama install commands
✔ or configure Continue so it automatically uses the best model for each task
✔ or fix your “agent mode not working” issue completely

//=========================
🧠 Short answer

👉 Yes, it will work on 16GB RAM
👉 But:

Small/medium Qwen coder models = smooth
Large models = slow or may swap memory (lag)
⚙️ What actually matters

When you say “Qwen2.5-Coder”, there are different sizes:

🟢 Good on 16GB RAM (recommended)
7B / 7B-Instruct / 7B-Coder
Smooth performance
Usable in VS Code / Continue
Minor delay only

👉 This is what you should use

🟡 Usable but slower
14B models
Will run on 16GB only with:
CPU offloading
or quantized (Q4/Q5)
Noticeable lag
🔴 Not recommended on 16GB
32B / 70B models
Will be very slow or crash
Needs 24GB–64GB+ RAM or GPU
⚡ Real-world performance (16GB RAM)

Using Ollama:

Qwen2.5-Coder 7B

✔ 1–3 sec response delay
✔ usable coding assistant
✔ good for VS Code Continue
✔ no major lag if system is clean

Qwen2.5-Coder 14B

⚠ 5–15 sec responses
⚠ RAM pressure
⚠ possible freezing during large prompts

🚀 Best setup for YOU (16GB RAM)
👉 Recommended model:

Qwen2.5-Coder 7B Instruct

👉 Run with:
Ollama
Continue in VS Code
Context limit: 4096–8192
🔥 Pro tips to avoid lag

✔ Close Chrome tabs (RAM matters a lot)
✔ Don’t use 32k context unless needed
✔ Keep VS Code workspace small
✔ Use quantized models (if available)

🧠 Final verdict

👉 16GB RAM is perfect for Qwen2.5-Coder 7B
👉 It becomes slow only if you go above 7B–14B range

If you want, I can recommend:
✔ exact best Qwen model tag for Ollama
✔ or fastest coding setup for VS Code (no lag)
✔ or compare Qwen vs DeepSeek-Coder-V2 on your machine specifically

Hi,
We removed the rating numbers and icon
Price text made in center
On search seaction sorting base on price, fixed

Hi,

In invoice management section link:- http://eps.infoicon.co.in:201/admin/invoice, when you click generate invoice.

We have given option three type invoice, credit, cash and estimate (Find the attached file stock_01.png).

When you select credit invoice, we have added payment terms where invoice days option given (Find the attached file stock_02.png).

Fill all the details (Find the attached file stock_03.png).

After submit the form you will get success message (Find the attached file stock_04.png). 

On invoice list added columns Due date, overdue and status. due date and status will show on credit invoice (Find the attached file stock_05.png).

When we create estimate invoice, in estimate invoice product quantity will not be decrease from the total quantity, quantity will be same.

Given option to change estimate invoice to cash or credit invoice, after changed the invoice, quantity will be decrease from total quantity (Find the attached file stock_08.png).

Form for changing invoice form ((Find the attached files stock_06.png stock_07.png).

In Receivables section we have given options All Customers, credit invoices->all credit invoices, credit invoices->all paid credit invoices, credit invoices->all unpaid credit invoices, Estimates invoices, Cash sales invoices (Find the attached file stock_09.png)

Click on All customers, here you can see all customer list (Find the attached file stock_10.png).

Click on view order section (Find the attached file stock_11.png). 

Cllick on order section, you will see all generated invoices to customer (Find the attached file stock_12.png).

On action if invocie type is Credit, then there fill be option receive payment (Find the attached file stock_12.png).  

On action you will also see if change invoice type,  estimate invoice transfer to credit or cash invoice (Find the attached file stock_13.png) 

On click received payment there will be option to add the payment against the invoice (Find the attached file stock_14.png).

After received payment, status will be partial, other wise unpaid (Find the attached file stock_15.png).

In all credit invoices, you will see all credit invoices (Find the attached file stock_16.png).

In all paid credit invoices, you will see all paid credit invoices (Find the attached file stock_17.png).

In all unpaid credit invoices, you will see all unpaid credit invoices (Find the attached file stock_18.png).

In Estimate invoices, you will see all estimate invoices (Find the attached file stock_19.png).

In Cash invoices, you will see all cash invoices .

If due date expire it will show the overdue (Find the attached file stock_20.png).



Hi,
We add supplier and purchase section (find the attaced file stock_01_15052026.png).
    IN supplier section 
        --> Supplier list, you can see all suplier registered here (find the attached file stock_02_15052026.png).

        --> Supplier ledger, you can see all supplier payment credit and debit (find the attached file stock_03_15052026.png).

        --> Supplier payment, You can see all payment given to suppllier(find the attached file stock_05_15052026.png).

        --> Supplier payment->Record Payment, You can add here payment given to supplier(find the attached file stock_04_15052026.png).

        --> Supplier due, you can see all dues to against supplier(find the attached file stock_06_15052026.png ).

    In Purchase Section
        --> Purchase Order, In purchase order section you can see all created purchase order(find the attached file stock_07_15052026.png)

        --> Purchase Order->Purchase order, here you can creat purchase order(find the attached file stock_08_15052026.png)

        --> Purchase return, in purchase return section you can see all returns and crate new returns(find the attached file stock_09_15052026.png)

        --> GRN(goods receipt notes), here you can receipt notes or create here(find the attached file stock_10_15052026.png)
        
        --> Purchase Invoice, here you can create purchase invoice(find the attaced file stock_11_15052026.png)

If any change in supplier and purchase section, Please let us know.


//feedbacke 26-05-2026
1. The section for account module, is currently accessed under Receivables as you now have it.  This needs to change and have The Account Module section be it's own section in the side panel. -->done
2. What you now have as Ledgers (except the Organization Info), should now come under the umbrella of the Account Module, and call it Reports -->done
3. What you now have as Receivables, should be called Sales  -->done
4. What you now have as Stock, should be called Purchases  -->done
5. The Create Product that is currently under Stock, needs some further tweaking. 
6. Give it accounting functionality by adding two new fields, Credit Account & Debit Account.  These two fields will be dropdown fields, and pull from the Chart of Accounts.  Once these two fields are configured, then whenever any transaction is done with the product, it will automatically create a Journal with Debit and Credit postings.  These two fields are mandatory and must be populated before the product can be saved.  -->working
7. Add a new field, Product Type, which tells us what kind of product we're creating.  Options should be, Service, Inventory Stock, Non-Inventory Stock  -->done
8. Remove the Quantity field from the Product.  Quantities of any product should be computed from the sum of all purchases of the product, less all the sales of the product.  -->done
9. Place Organization Info under Settings -->done
10. The reports need work, as well as other stuff, but we will get into that one later.




//feedback 02-06-2026
Here are few more things that we've seen that need addressing.  Please be advised, that this is not ALL that is outstanding:

1.  Create a menu item to be able to generate an invoice from within the Sales menu

2.  Give the option to save sale or purchase invoices in a draft or hold (whatever you want to call it) state, that can be resumed in the future

3.  Give the option to view all invoices on hold, under both Purchases and Sales and the option to resume

4.  Create a field called Credit Limit for both Suppliers and Customers.

5.  Before a credit sale or purchase is made, a check will be made against the customer's or supplier's limit, if one has been set, and if the current sale or purchase will cause that limit to be exceeded, then let the system give a warning, and that invoice can only be saved as draft or hold.

6.  Under settings, create a setting for Users Allowed To Override Credit Limits, and this setting should give us the option to enter all the users, that have the permission to override credit limits

7.  Any of the users in 6. can then be able to resume and save any invoice that was saved as draft because of being over the limit.  Or cancel it if they wish.

8.  Sale or Purchase invoices saved as draft, should not get an invoice number.  You can give them a separate draft number if that's a necessity, but not an official invoice sequential number

9.  When creating a store, create a field for sale invoice number sequence.  It is this sequence or format that will be used for the invoices generated for that particular store, as opposed to the default INV## that is currently in place now.  So if we enter AB######, then all invoices will be AB000001, AB000002, AB000003 etc.

10. Create another field when creating a store, for Next sale invoice number in sequence.  This will work in tandem with 9. so if we enter a sequence of AB###### and a Next Sale Invoice Number in Sequence of 2348, then the next numbers in the sequence will be AB002348, AB002349, AB002350 etc.  You will obviously have to ensure that once the next number in sequence has been generated, then the system doesn't check that field anymore, until of course, it's changed again.  I'll leave it up to you to decide on how you want to implement that.

11. Duplicate 9. and 10. to accommodate, Purchase Invoices, Purchase Orders, Purchase Return, GRN, Credit Note, Debit Note, Receipts

12. On the product page, give the option to select whether the product is taxable or non-taxable.  Create another field that is accessible only if the product is taxable, to enter the tax rate

13. When generating invoices, change the way taxes are computed.  Currently they are global taxes, meaning, that they affect all items on the invoice.  This is not always the case.  So to deal with such a scenario, we put the taxable status on the line item, so when an item is selected, the tax status is populated by default, with the tax rate.  Taxes will then be calculated per line, and the total shown at the bottom.

14. When generating an invoice give the option to make the entire invoice non-taxable, that will override the individual line item taxable status, as there are some customers, that have tax exempt status and don't pay taxes.

15. There should be two invoice number fields on a purchase invoice.  1.  The sequential internal number for purchase invoices, that is autogenerated, and 2.  The supplier's invoice number, which can be anything

16. Give the option to set the Debit and Credit Chart of Account entries for all the main source generators, such as Sale Invoices, Purchase Invoices, Credit Notes, Debit Notes etc.

17. Sale Invoices will generate two journal entries, one for 16. that records the sale value of the transaction, and the other for the debit and credit accounts set on the product, that records the cost of the transaction.

18. Create a set of instructions for the proper installation of the Accounting System on any given server.  Please state clearly all pre-requisites that need to be installed before the server is ready for the Accounting System, and what hardware specifications are required for the server that will host the Accounting System.

Regards


for point 11 change file name and location as follows:
app/Http/Controllers/admin/PurchaseDocumentController.php
app/Http/Controllers/admin/CustomerController.php
app/Http/Controllers/admin/SupplierController.php
app/Http/Controllers/admin/StoreController.php
app/Http/Controllers/admin/SupplierPaymentController.php

app/Services/StoreSequentialNumberService.php

app/models/Store.php

app/database/migrations/2026_06_05_160000_add_purchase_doc_sequences_to_stores_table.php

views/admin/partials/store-document-sequences-fields.blade.php
views/admin/purchases/purchase-invoices-create.blade.php
views/admin/partials/admin/model.blade.php
view/superadmin/store-list.blade.php



*/
