<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\SupplierController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/debug-db', function () {
    $db = config('database.default');

    return response()->json([
        'default_connection' => $db,
        'database_file' => config("database.connections.$db.database"),
        'nativephp_connection' => config('database.connections.nativephp.database'),
        'products_count' => Product::count(),
        'first_product' => Product::first()?->only(['id', 'name', 'expiry_date']),
    ]);
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('customers', CustomerController::class);

    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::post('/sales/{sale}/refund', [SaleController::class, 'refund'])->name('sales.refund');
    Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');

    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::get('/search', [PosController::class, 'search'])->name('search');
        Route::post('/checkout', [PosController::class, 'checkout'])->name('checkout');
        Route::get('/receipt/{sale}', [PosController::class, 'receipt'])->name('receipt');
    });

    Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups', [BackupController::class, 'create'])->name('backups.create');
    Route::get('/backups/{name}/download', [BackupController::class, 'download'])->name('backups.download');
    Route::post('/backups/{name}/restore', [BackupController::class, 'restore'])->name('backups.restore');
    Route::delete('/backups/{name}', [BackupController::class, 'destroy'])->name('backups.destroy');
    Route::post('/backups/google-drive', [BackupController::class, 'saveGoogleDrivePath'])->name('backups.google-drive');

    Route::resource('suppliers', SupplierController::class);

    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('/purchases/products/search', [PurchaseController::class, 'searchProducts'])->name('purchases.products');
    Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::resource('expenses', ExpenseController::class);

    Route::get('/stock', [StockAdjustmentController::class, 'index'])->name('stock.index');
    Route::get('/stock/create', [StockAdjustmentController::class, 'create'])->name('stock.create');
    Route::post('/stock', [StockAdjustmentController::class, 'store'])->name('stock.store');
    Route::get('/stock/products/search', [StockAdjustmentController::class, 'searchProducts'])->name('stock.products');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/profit', [ReportController::class, 'profit'])->name('reports.profit');
    Route::get('/reports/products', [ReportController::class, 'products'])->name('reports.products');
    Route::get('/reports/stock', [ReportController::class, 'stock'])->name('reports.stock');

    Route::get('/barcodes', [BarcodeController::class, 'index'])->name('barcodes.index');
    Route::post('/barcodes/print', [BarcodeController::class, 'print'])->name('barcodes.print');
    Route::get('/barcodes/{product}/single', [BarcodeController::class, 'single'])->name('barcodes.single');

    Route::get('/products/generate-barcode', [ProductController::class, 'generateBarcode'])->name('products.generate-barcode');
    Route::post('/products/bulk-generate-barcodes', [ProductController::class, 'bulkGenerateBarcodes'])->name('products.bulk-generate-barcodes');

    Route::get('/customers/{customer}/statement', [CustomerController::class, 'statement'])->name('customers.statement');
    Route::resource('customers', CustomerController::class);

    Route::get('/payments', [CustomerPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [CustomerPaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [CustomerPaymentController::class, 'store'])->name('payments.store');
    Route::delete('/payments/{payment}', [CustomerPaymentController::class, 'destroy'])->name('payments.destroy');

    Route::get('/customers/{customer}/statement', [CustomerController::class, 'statement'])->name('customers.statement');
    Route::resource('customers', CustomerController::class);

    Route::get('/payments', [CustomerPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [CustomerPaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [CustomerPaymentController::class, 'store'])->name('payments.store');
    Route::delete('/payments/{payment}', [CustomerPaymentController::class, 'destroy'])->name('payments.destroy');

    Route::get('/customers/{customer}/statement', [CustomerController::class, 'statement'])->name('customers.statement');
    Route::resource('customers', CustomerController::class);

    Route::get('/payments', [CustomerPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [CustomerPaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [CustomerPaymentController::class, 'store'])->name('payments.store');
    Route::delete('/payments/{payment}', [CustomerPaymentController::class, 'destroy'])->name('payments.destroy');


    Route::get('/trash', [\App\Http\Controllers\TrashController::class, 'index'])->name('trash.index');
    Route::get('/trash/{type}', [\App\Http\Controllers\TrashController::class, 'show'])->name('trash.show');
    Route::post('/trash/{type}/{id}/restore', [\App\Http\Controllers\TrashController::class, 'restore'])->name('trash.restore');
    Route::delete('/trash/{type}/{id}', [\App\Http\Controllers\TrashController::class, 'force-delete'])->name('trash.force-delete');
    Route::delete('/trash/{type}', [\App\Http\Controllers\TrashController::class, 'empty'])->name('trash.empty');

    Route::put('/settings/login', [SettingController::class, 'updateLogin'])->name('settings.login');
    
});
