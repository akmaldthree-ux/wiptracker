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
use App\Http\Controllers\CuttingPlanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'executive'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'executive'])->name('dashboard.executive');
    Route::get('/dashboard/operasional', [DashboardController::class, 'operational'])->name('dashboard.operational');
    Route::get('/dashboard/wip-monitor', [DashboardController::class, 'wipMonitor'])->name('dashboard.wip-monitor');

    Route::resource('orders', ProductionOrderController::class);
    Route::patch('orders/{order}/status', [ProductionOrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('orders/{order}/send-to-cutting', [ProductionOrderController::class, 'sendToCutting'])->name('orders.send-to-cutting');

    Route::get('/wip', [WipController::class, 'index'])->name('wip.index');
    Route::get('/wip/input', [WipController::class, 'create'])->name('wip.create');
    Route::post('/wip', [WipController::class, 'store'])->name('wip.store');
    Route::get('/wip/{order}', [WipController::class, 'show'])->name('wip.show');

    Route::resource('handover', HandoverController::class);
    Route::post('handover/{handover}/confirm', [HandoverController::class, 'confirm'])->name('handover.confirm');
    Route::post('handover/{handover}/approve', [HandoverController::class, 'approve'])->name('handover.approve');

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

    Route::resource('cutting', CuttingPlanController::class);
    Route::patch('cutting/{cutting}/status', [CuttingPlanController::class, 'updateStatus'])->name('cutting.status');
    Route::post('cutting/{cutting}/bundle', [CuttingPlanController::class, 'storeBundle'])->name('cutting.bundle.store');
    Route::patch('cutting/bundle/{bundle}/status', [CuttingPlanController::class, 'updateBundle'])->name('cutting.bundle.status');

    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifikasi/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifikasi/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    Route::prefix('master')->name('master.')->group(function () {
        Route::resource('produk', ProductController::class);
        Route::resource('series', SeriesController::class);
        Route::resource('warna', ColorController::class);
        Route::resource('ukuran', SizeController::class);
        Route::resource('stasiun', StationController::class);
        Route::resource('sewing-location', SewingLocationController::class);
    });

    Route::resource('users', UserController::class);
    Route::get('/profil', [UserController::class, 'profile'])->name('profile');
    Route::put('/profil', [UserController::class, 'updateProfile'])->name('profile.update');
});

// API routes for dynamic selects
Route::middleware('auth')->prefix('api')->group(function() {
    Route::get('series-by-product/{product}', fn(\App\Models\Product $product) => response()->json($product->series()->where('is_active',true)->get(['id','name','code'])));
    Route::get('skus-by-series/{series}', fn(\App\Models\Series $series) => response()->json($series->skus()->where('is_active',true)->with(['color','size'])->get()));
    Route::get('order-skus/{order}', fn(\App\Models\ProductionOrder $order) => response()->json($order->items()->with(['sku.color','sku.size'])->get()));
});
