<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductionOrderController;
use App\Http\Controllers\WipController;
use App\Http\Controllers\HandoverController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\MasterData\ProductController;
use App\Http\Controllers\MasterData\SeriesController;
use App\Http\Controllers\MasterData\ColorController;
use App\Http\Controllers\MasterData\SizeController;
use App\Http\Controllers\MasterData\StationController;
use App\Http\Controllers\MasterData\SewingLocationController;
use App\Http\Controllers\MasterData\SupplierController;
use App\Http\Controllers\CuttingPlanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RejectController;
use App\Http\Controllers\QcInspectionController;
use App\Http\Controllers\BomController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'executive'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'executive'])->name('dashboard.executive');
    Route::get('/dashboard/operasional', [DashboardController::class, 'operational'])->name('dashboard.operational');
    Route::get('/dashboard/wip-monitor', [DashboardController::class, 'wipMonitor'])->name('dashboard.wip-monitor');
    Route::get('/dashboard/reject', [RejectController::class, 'dashboard'])->name('dashboard.reject');
    Route::patch('/rework/{handover}/result', [RejectController::class, 'updateRework'])->name('rework.update');
    Route::patch('/second-stock/{secondStock}/status', [RejectController::class, 'updateSecondStock'])->name('second-stock.update');
    Route::get('/dashboard/station-deadlines', [DashboardController::class, 'stationDeadlines'])->name('dashboard.station-deadlines');

    Route::resource('orders', ProductionOrderController::class);
    Route::patch('orders/{order}/status', [ProductionOrderController::class, 'updateStatus'])->name('orders.status');

    Route::get('/wip', [WipController::class, 'index'])->name('wip.index');
    Route::get('/wip/input', [WipController::class, 'create'])->name('wip.create');
    Route::post('/wip', [WipController::class, 'store'])->name('wip.store');
    Route::get('/wip/{order}', [WipController::class, 'show'])->name('wip.show');

    Route::resource('handover', HandoverController::class);
    Route::post('handover/{handover}/confirm', [HandoverController::class, 'confirm'])->name('handover.confirm');
    Route::post('handover/{handover}/approve', [HandoverController::class, 'approve'])->name('handover.approve');
    Route::post('handover/{handover}/complete-order', [HandoverController::class, 'completeOrder'])->name('handover.complete-order');
    Route::post('handover/{handover}/forward-rework', [HandoverController::class, 'forwardRework'])->name('handover.forward-rework');
    Route::post('orders/{order}/send-to-cutting', [HandoverController::class, 'sendFromOrder'])->name('orders.send-to-cutting');

    Route::get('/procurement', [ProcurementController::class, 'index'])->name('procurement.index');
    Route::get('/procurement/{order}', [ProcurementController::class, 'show'])->name('procurement.show');
    Route::post('/procurement/{order}/approve', [ProcurementController::class, 'approve'])->name('procurement.approve');
    Route::post('/procurement/{order}/revoke', [ProcurementController::class, 'revoke'])->name('procurement.revoke');
    Route::post('/procurement/{order}/regenerate', [ProcurementController::class, 'regenerate'])->name('procurement.regenerate');

    Route::resource('bahan-baku', RawMaterialController::class);
    Route::get('bahan-baku/{rawMaterial}/receipt', [RawMaterialController::class, 'receiptForm'])->name('bahan-baku.receipt');
    Route::post('bahan-baku/{rawMaterial}/receipt', [RawMaterialController::class, 'storeReceipt'])->name('bahan-baku.receipt.store');

    Route::get('/budget', [BudgetController::class, 'index'])->name('budget.index');
    Route::get('/budget/{order}', [BudgetController::class, 'show'])->name('budget.show');
    Route::get('/budget/{order}/edit', [BudgetController::class, 'edit'])->name('budget.edit');
    Route::put('/budget/{order}', [BudgetController::class, 'update'])->name('budget.update');
    Route::post('/budget/{order}/cost', [BudgetController::class, 'storeCost'])->name('budget.cost.store');

    Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/produksi', [ReportController::class, 'production'])->name('laporan.produksi');
    Route::get('/laporan/handover', [ReportController::class, 'handover'])->name('laporan.handover');
    Route::get('/laporan/bahan-baku', [ReportController::class, 'material'])->name('laporan.bahan-baku');
    Route::get('/laporan/budget', [ReportController::class, 'budget'])->name('laporan.budget');

    // Export routes
    Route::get('/laporan/produksi/export-excel', [ReportController::class, 'exportProductionExcel'])->name('laporan.produksi.excel');
    Route::get('/laporan/produksi/export-pdf', [ReportController::class, 'exportProductionPdf'])->name('laporan.produksi.pdf');
    Route::get('/laporan/handover/export-excel', [ReportController::class, 'exportHandoverExcel'])->name('laporan.handover.excel');
    Route::get('/laporan/handover/export-pdf', [ReportController::class, 'exportHandoverPdf'])->name('laporan.handover.pdf');
    Route::get('/laporan/reject/export-excel', [ReportController::class, 'exportRejectExcel'])->name('laporan.reject.excel');
    Route::get('/laporan/reject/export-pdf', [ReportController::class, 'exportRejectPdf'])->name('laporan.reject.pdf');

    Route::resource('cutting', CuttingPlanController::class);
    Route::patch('cutting/{cutting}/status', [CuttingPlanController::class, 'updateStatus'])->name('cutting.status');
    Route::post('cutting/{cutting}/bundle', [CuttingPlanController::class, 'storeBundle'])->name('cutting.bundle.store');
    Route::patch('cutting/bundle/{bundle}/status', [CuttingPlanController::class, 'updateBundle'])->name('cutting.bundle.status');

    Route::get('/bom', [BomController::class, 'index'])->name('bom.index');
    Route::post('/bom', [BomController::class, 'store'])->name('bom.store');
    Route::delete('/bom/{bom}', [BomController::class, 'destroy'])->name('bom.destroy');

    Route::resource('purchase-order', PurchaseOrderController::class);
    Route::post('purchase-order/{purchaseOrder}/send', [PurchaseOrderController::class, 'send'])->name('purchase-order.send');
    Route::post('purchase-order/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-order.receive');
    Route::post('purchase-order/{purchaseOrder}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-order.cancel');

    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifikasi/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifikasi/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    // QC Inspection routes
    Route::get('/qc', [QcInspectionController::class, 'index'])->name('qc.index');
    Route::get('/qc/create', [QcInspectionController::class, 'create'])->name('qc.create');
    Route::post('/qc', [QcInspectionController::class, 'store'])->name('qc.store');
    Route::get('/qc/{qc}', [QcInspectionController::class, 'show'])->name('qc.show');

    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('produk', ProductController::class);
        Route::resource('series', SeriesController::class);
        Route::resource('warna', ColorController::class);
        Route::resource('ukuran', SizeController::class);
        Route::resource('stasiun', StationController::class);
        Route::resource('sewing-location', SewingLocationController::class);
        Route::resource('supplier', SupplierController::class);
        // Clear all per-module
        Route::delete('produk/clear-all',          [ImportController::class, 'clearProducts'])->name('produk.clear-all');
        Route::delete('series/clear-all',           [ImportController::class, 'clearSeries'])->name('series.clear-all');
        Route::delete('warna/clear-all',            [ImportController::class, 'clearColors'])->name('warna.clear-all');
        Route::delete('ukuran/clear-all',           [ImportController::class, 'clearSizes'])->name('ukuran.clear-all');
        Route::delete('sewing-location/clear-all',  [ImportController::class, 'clearSewingLocations'])->name('sewing-location.clear-all');
        Route::delete('supplier/clear-all',         [ImportController::class, 'clearSuppliers'])->name('supplier.clear-all');
        Route::delete('bahan-baku/clear-all',       [ImportController::class, 'clearRawMaterials'])->name('bahan-baku.clear-all');
    });

    // Import routes
    Route::prefix('import')->name('import.')->group(function () {
        // Templates
        Route::get('template/produk',       [ImportController::class, 'templateProduct'])->name('template.produk');
        Route::get('template/warna',        [ImportController::class, 'templateColor'])->name('template.warna');
        Route::get('template/ukuran',       [ImportController::class, 'templateSize'])->name('template.ukuran');
        Route::get('template/supplier',     [ImportController::class, 'templateSupplier'])->name('template.supplier');
        Route::get('template/bahan-baku',   [ImportController::class, 'templateRawMaterial'])->name('template.bahan-baku');
        Route::get('template/bom',          [ImportController::class, 'templateBom'])->name('template.bom');
        Route::get('template/wip',          [ImportController::class, 'templateWip'])->name('template.wip');
        Route::get('template/order/{order}/item', [ImportController::class, 'templateOrderItem'])->name('template.order-item');
        Route::get('template/tempat-sewing',     [ImportController::class, 'templateSewingLocation'])->name('template.tempat-sewing');
        Route::get('template/series',            [ImportController::class, 'templateSeries'])->name('template.series');
        // Imports
        Route::post('produk',       [ImportController::class, 'importProduct'])->name('produk');
        Route::post('warna',        [ImportController::class, 'importColor'])->name('warna');
        Route::post('ukuran',       [ImportController::class, 'importSize'])->name('ukuran');
        Route::post('supplier',     [ImportController::class, 'importSupplier'])->name('supplier');
        Route::post('bahan-baku',   [ImportController::class, 'importRawMaterial'])->name('bahan-baku');
        Route::post('bom',          [ImportController::class, 'importBom'])->name('bom');
        Route::post('wip',          [ImportController::class, 'importWip'])->name('wip');
        Route::post('order/{order}/item', [ImportController::class, 'importOrderItem'])->name('order-item');
        Route::post('tempat-sewing',      [ImportController::class, 'importSewingLocation'])->name('tempat-sewing');
        Route::post('series',             [ImportController::class, 'importSeries'])->name('series');
    });

    Route::resource('users', UserController::class);
    Route::get('/profil', [UserController::class, 'profile'])->name('profile');
    Route::put('/profil', [UserController::class, 'updateProfile'])->name('profile.update');
});

// API routes for dynamic selects
Route::middleware('auth')->prefix('api')->group(function() {
    Route::get('bom/calculate', [BomController::class, 'calculate'])->name('api.bom.calculate');
    Route::get('series-by-product/{product}', fn(\App\Models\Product $product) => response()->json($product->series()->where('is_active',true)->get(['id','name','code'])));
    Route::get('skus-by-series/{series}', fn(\App\Models\Series $series) => response()->json($series->skus()->where('is_active',true)->with(['color','size'])->get()));
    Route::get('order-skus/{order}', fn(\App\Models\ProductionOrder $order) => response()->json($order->items()->with(['sku.color','sku.size'])->get()));
    Route::get('wip-available/{order}/{station}', function(\App\Models\ProductionOrder $order, \App\Models\Station $station) {
        $entries = \App\Models\WipEntry::where('production_order_id', $order->id)
            ->where('station_id', $station->id)
            ->selectRaw('sku_id, SUM(qty_in) as total_in, SUM(qty_out) as total_out, SUM(qty_reject) as total_reject')
            ->groupBy('sku_id')->get();
        $result = [];
        foreach ($entries as $e) {
            $result[$e->sku_id] = max(0, $e->total_in - $e->total_out - $e->total_reject);
        }
        return response()->json($result);
    });
});

