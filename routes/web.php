<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ItemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\WareHousesController;
use App\Http\Controllers\LaporanTransaksiController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\Admin\RecipeController;
use App\Http\Controllers\RecipeCategoryController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\StokInController;

Route::get('/', function () {
    return Auth::check() ? redirect('/home') : redirect('/login');
});

Route::get('/clear-cache', function () {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            // Artisan::call('optimize:clear');
            return back()->with('success', 'Cache, config, route, dan view berhasil dibersihkan!');
 });

Route::middleware(['xss'])->group(function () {

    Route::post('payments/midtrans-notification', [App\Http\Controllers\PaymentCallbackController::class, 'receive']);
    Route::get('payments-finish', [App\Http\Controllers\FrontendController::class, 'payments_finish'])->name('payments_finish');

    Auth::routes([
        'login'    => true,
        'logout'   => true,
        'register' => true,
        'reset'    => false,
        'confirm'  => false,
        'verify'   => false,
    ]);

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::middleware(['auth:web,admin'])->group(function () {
        // Routes untuk semua role kecuali owner
        Route::middleware(['role:superadmin,admin,supervisor,petugas_gudang'])->group(function () {
        Route::controller(App\Http\Controllers\Admin\TransactionController::class)->name('transactions.')->prefix('transactions')->group(function () {
            Route::get('/status/{id}', 'status')->name('status');
            Route::put('/status/{id}', 'status_update')->name('status_update');
        });

        }); // End of middleware role except owner

        // Routes khusus superadmin
        Route::middleware(['role:superadmin'])->group(function () {
            Route::resource('roles', App\Http\Controllers\Admin\RoleController::class)->names('roles');
            Route::resource('users', App\Http\Controllers\Admin\UserController::class)->names('users');
        });

        Route::controller(App\Http\Controllers\Admin\SettingWebsiteController::class)->prefix('setting')->name('setting.')->group(function () {
            Route::get('website', 'index')->name('index');
            Route::put('website/{admin_website}', 'update')->name('update');
        });

        Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
            Route::get('index', [CategoryController::class, 'index'])->name('index');
            Route::get('create', [CategoryController::class, 'create'])->name('create');
            Route::post('store', [CategoryController::class, 'store'])->name('store');
            Route::get('edit/{id}', [CategoryController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [CategoryController::class, 'update'])->name('update');
            Route::delete('destroy/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'recipe_category', 'as' => 'recipe_category.'], function () {
            Route::get('index', [RecipeCategoryController::class, 'index'])->name('index');
            Route::post('store', [RecipeCategoryController::class, 'store'])->name('store');
            Route::post('update/{id}', [RecipeCategoryController::class, 'update'])->name('update');
            Route::delete('destroy/{id}', [RecipeCategoryController::class, 'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'items', 'as' => 'items.'], function () {
            Route::get('index', [ItemController::class, 'index'])->name('index');
            Route::get('create', [ItemController::class, 'create'])->name('create');
            Route::post('store', [ItemController::class, 'store'])->name('store');
            Route::get('edit/{id}', [ItemController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [ItemController::class, 'update'])->name('update');
            Route::delete('destroy/{id}', [ItemController::class, 'destroy'])->name('destroy');
            Route::get('import', [ItemController::class, 'import'])->name('import');
            Route::post('importData', [ItemController::class, 'importData'])->name('importData');
        });

        Route::group(['prefix' => 'warehouse', 'as' => 'warehouse.'], function () {
            Route::get('index', [WareHousesController::class, 'index'])->name('index');
            Route::get('create', [WareHousesController::class, 'create'])->name('create');
            Route::post('store', [WareHousesController::class, 'store'])->name('store');
            Route::get('edit/{id}', [WareHousesController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [WareHousesController::class, 'update'])->name('update');
            Route::delete('destroy/{id}', [WareHousesController::class, 'destroy'])->name('destroy');
            Route::post('check-name', [WareHousesController::class, 'checkName'])->name('checkName');
        });

        Route::group(['prefix' => 'stock', 'as' => 'stock.'], function () {
            Route::get('index', [StockController::class, 'index'])->name('index');
            Route::get('create', [StockController::class, 'create'])->name('create');
            Route::post('store', [StockController::class, 'store'])->name('store');
            Route::get('cek-stok-akhir', [StockController::class, 'cekStokAkhir'])->name('cekStokAkhir');
        });

        Route::group(['prefix' => 'live_stock', 'as' => 'live_stock.'], function () {
            Route::get('index', [StockController::class, 'live_stock'])->name('index');
            Route::get('export_excel', [StockController::class, 'export_excel'])->name('export_excel');
            Route::get('export_pdf', [StockController::class, 'export_pdf'])->name('export_pdf');
        });

        Route::group(['prefix' => 'in_stock', 'as' => 'in_stock.'], function () {
            Route::get('index', [StokInController::class, 'index'])->name('index');
            Route::get('create', [StokInController::class, 'create'])->name('create');
            Route::post('store', [StokInController::class, 'store'])->name('store');
            Route::get('get_harga_satuan', [StokInController::class, 'getHargaSatuan'])->name('getHargaSatuan');
            Route::get('get_warehouse', [StokInController::class, 'getWarehouse'])->name('getWarehouse');
        });

        Route::group(['prefix' => 'out_stock', 'as' => 'out_stock.'], function () {
            Route::get('index', [StockOutController::class, 'index'])->name('index');
            Route::get('create', [StockOutController::class, 'create'])->name('create');
            Route::post('store', [StockOutController::class, 'store'])->name('store');
            Route::get('get_harga_satuan', [StockOutController::class, 'getHargaSatuan'])->name('getHargaSatuan');
            Route::get('get_warehouse', [StokInController::class, 'getWarehouse'])->name('getWarehouse');
            Route::get('cek_live_stok', [StockOutController::class, 'cekLiveStok'])->name('cekLiveStok');
        });

        Route::group(['prefix' => 'adjustment_stock', 'as' => 'adjustment_stock.'], function () {
            Route::get('index', [StockAdjustmentController::class, 'index'])->name('index');
            Route::get('create', [StockAdjustmentController::class, 'create'])->name('create');
            Route::post('store', [StockAdjustmentController::class, 'store'])->name('store');
            Route::get('cek_jumlah_terakhir', [StockAdjustmentController::class, 'cekJumlahTerakhir'])->name('cekJumlahTerakhir');
            Route::get('get_warehouse', [StockAdjustmentController::class, 'getWarehouse'])->name('getWarehouse');
            Route::post('verifikasi', [StockAdjustmentController::class, 'verifikasi'])->name('verifikasi');
        });

        Route::group(['prefix' => 'laporan_transaksi', 'as' => 'laporan_transaksi.'], function () {
            Route::get('index', [LaporanTransaksiController::class, 'index'])->name('index');
            Route::get('create', [LaporanTransaksiController::class, 'create'])->name('create');
            Route::get('download/{id}', [LaporanTransaksiController::class, 'download'])->name('download');
            Route::post('preview', [LaporanTransaksiController::class, 'preview'])->name('preview');
        });

        Route::group(['prefix' => 'pengaturan', 'as' => 'pengaturan.'], function () {
            Route::get('index', [PengaturanController::class, 'index'])->name('index');
            Route::post('update_password', [PengaturanController::class, 'updatePassword'])->name('updatePassword');
        });

        // Recipe Routes
        Route::group(['prefix' => 'recipes', 'as' => 'recipes.'], function () {
            Route::get('/', [RecipeController::class, 'index'])->name('index');
            Route::get('/datatable', [RecipeController::class, 'getDatatable'])->name('datatable');
            Route::get('/create', [RecipeController::class, 'create'])->name('create');
            Route::post('/', [RecipeController::class, 'store'])->name('store');
            Route::get('/{id}', [RecipeController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [RecipeController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [RecipeController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [RecipeController::class, 'destroy'])->name('destroy');
            
            // Stock Out Processing
            Route::get('/{id}/stock-out', [RecipeController::class, 'createStockOut'])->name('stock-out.create');
            Route::post('/{id}/stock-out', [RecipeController::class, 'processStockOut'])->name('stock-out.process');
        });
    });
});