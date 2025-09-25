<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ItemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\WareHousesController;
use App\Http\Controllers\LaporanTransaksiController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PengaturanController;
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
    Route::name('fe.')->group(function () {
        Route::controller(App\Http\Controllers\FrontendController::class)->group(function () {
            // Gantilah route ini ke /beranda atau yang lain jika tidak ingin konflik dengan '/'
            Route::get('/beranda', 'index')->name('index'); 
            Route::get('/list', 'list')->name('list');

            Route::middleware(['auth'])->group(function () {
                Route::get('/category/{id}', 'category')->name('category');
                Route::get('/review', 'review')->name('review');

                Route::controller(App\Http\Controllers\AddressController::class)->group(function () {
                    Route::get('/alamat', 'alamat')->name('alamat');
                    Route::post('/post_alamat', 'post_alamat')->name('post_alamat');
                    Route::put('/update_alamat', 'update_alamat')->name('update_alamat');
                    Route::delete('/delete_alamat', 'delete_alamat')->name('delete_alamat');
                    Route::put('/set_alamat', 'set_alamat')->name('set_alamat');
                    Route::put('/update_address/{id}', 'update_address')->name('update_address');
                });

                Route::controller(App\Http\Controllers\CateringController::class)->group(function () {
                    Route::get('/catering', 'catering')->name('catering');
                    Route::get('/resto-catering', 'resto_catering')->name('resto_catering');
                    Route::get('/cart-catering', 'cart_catering')->name('cart_catering');
                    Route::post('/post_catering', 'post_catering')->name('post_catering');
                    Route::put('/update_catering', 'update_catering')->name('update_catering');
                });

                Route::controller(App\Http\Controllers\InstantController::class)->group(function () {
                    Route::get('/instan', 'instan')->name('instan');
                    Route::get('/resto-instan', 'resto_instan')->name('resto_instan');
                    Route::get('/cart-instan', 'cart_instan')->name('cart_instan');
                });

                Route::middleware(['alamat'])->group(function () {
                    Route::get('/akun', 'akun')->name('akun');
                    Route::put('/akun', 'update_akun')->name('update_akun');
                    Route::get('/password', 'password')->name('password');
                    Route::put('/password', 'update_password')->name('update_password');
                    Route::get('/riwayat', 'riwayat')->name('riwayat');
                    Route::get('/bantuan', 'bantuan')->name('bantuan');

                    Route::get('mdlRiwayat/{id?}', 'mdlRiwayat')->name('mdlRiwayat');
                    Route::post('upload', 'upload')->name('upload');
                    Route::get('mdlCartInstan/{id?}', 'mdlCartInstan')->name('mdlCartInstan');
                    Route::post('uploadInstan', 'uploadInstan')->name('uploadInstan');

                    Route::post('/cart', 'post_cart')->name('post_cart');
                    Route::put('/minus/{id}', 'minus')->name('minus');
                    Route::put('/plus/{id}', 'plus')->name('plus');
                    Route::post('/check', 'check')->name('check');
                    Route::put('/cart/{id}', 'update_cart')->name('update_cart');
                    Route::delete('/cart/delete/{id}', 'delete_cart')->name('delete_cart');
                    Route::put('/update_note/{id}', 'update_note')->name('update_note');
                    Route::put('/pay/{id}', 'pay')->name('pay');
                    Route::get('/invoice/{id}', 'invoice')->name('invoice');
                    Route::get('/invoices', 'invoices')->name('invoices');

                    Route::resource('orders', App\Http\Controllers\OrderController::class)->only(['index', 'show']);
                });
            });
        });
    });

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

    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        // Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        // Route::controller(App\Http\Controllers\ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        //     Route::get('/', 'edit')->name('edit');
        //     Route::put('/', 'update')->name('update');
        //     Route::post('upload', 'upload')->name('upload');
        //     Route::put('password', 'password')->name('password');
        // });

        // Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
        // Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
        // Route::resource('transactions', App\Http\Controllers\Admin\TransactionController::class);
        // Route::resource('order_products', App\Http\Controllers\Admin\OrderProductController::class);
        // Route::resource('transactions_instant', App\Http\Controllers\Admin\InstantController::class);
        // Route::resource('transactions_catering', App\Http\Controllers\Admin\CateringController::class);
        // Route::resource('riwayat', App\Http\Controllers\Admin\RiwayatController::class);

        Route::controller(App\Http\Controllers\Admin\TransactionController::class)->name('transactions.')->group(function () {
            Route::get('/status/{id}', 'status')->name('status');
            Route::put('/status/{id}', 'status_update')->name('status_update');
        });

        Route::resource('roles', App\Http\Controllers\Admin\RoleController::class);
        Route::resource('users', App\Http\Controllers\Admin\UserController::class);

        Route::controller(App\Http\Controllers\Admin\SettingWebsiteController::class)->prefix('setting')->name('setting.')->group(function () {
            Route::get('website', 'index')->name('index');
            Route::put('website/{admin_website}', 'update')->name('update');
        });

        // Route::get('/clear-cache', function () {
        //     Artisan::call('cache:clear');
        //     Artisan::call('config:clear');
        //     Artisan::call('route:clear');
        //     Artisan::call('view:clear');
        //     Artisan::call('optimize:clear');
        //     return back()->with('success', 'Cache, config, route, dan view berhasil dibersihkan!');
        // })->name('admin.clearcache');

        Route::group(['prefix' => 'category/', 'as' => 'category.'], function () {
            Route::get('index', [CategoryController::class, 'index'])->name('index');
            Route::get('create', [CategoryController::class, 'create'])->name('create');
            Route::post('store', [CategoryController::class, 'store'])->name('store');
            Route::get('edit/{id}', [CategoryController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [CategoryController::class, 'update'])->name('update');
            Route::delete('destroy/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        });
        Route::group(['prefix' => 'items/', 'as' => 'items.'], function () {
            Route::get('index', [ItemController::class, 'index'])->name('index');
            Route::get('create', [ItemController::class, 'create'])->name('create');
            Route::post('store', [ItemController::class, 'store'])->name('store');
            Route::get('edit/{id}', [ItemController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [ItemController::class, 'update'])->name('update');
            Route::delete('destroy/{id}', [ItemController::class, 'destroy'])->name('destroy');
            Route::get('import', [ItemController::class, 'import'])->name('import');
            Route::post('importData', [ItemController::class, 'importData'])->name('importData');
        });
        Route::group(['prefix' => 'warehouse/', 'as' => 'warehouse.'], function () {
            Route::get('index', [WareHousesController::class, 'index'])->name('index');
            Route::get('create', [WareHousesController::class, 'create'])->name('create');
            Route::post('store', [WareHousesController::class, 'store'])->name('store');
            Route::get('edit/{id}', [WareHousesController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [WareHousesController::class, 'update'])->name('update');
            Route::delete('destroy/{id}', [WareHousesController::class, 'destroy'])->name('destroy');
            Route::post('check-name', [WareHousesController::class, 'checkName'])->name('checkName');
        });
        Route::group(['prefix' => 'menu/', 'as' => 'menu.'], function () {
            Route::get('index', [MenuController::class, 'index'])->name('index');
            Route::get('create', [MenuController::class, 'create'])->name('create');
            Route::post('store', [MenuController::class, 'store'])->name('store');
            Route::get('edit/{id}', [MenuController::class, 'edit'])->name('edit');
            Route::post('update/{id}', [MenuController::class, 'update'])->name('update');
            Route::delete('destroy/{id}', [MenuController::class, 'destroy'])->name('destroy');
        });

        Route::group(['prefix' => 'stock/', 'as' => 'stock.'], function () {
            Route::get('index', [StockController::class, 'index'])->name('index');
            Route::get('create', [StockController::class, 'create'])->name('create');
            Route::post('store', [StockController::class, 'store'])->name('store');
            Route::get('cek-stok-akhir', [StockController::class, 'cekStokAkhir'])->name('cekStokAkhir');
        });

        Route::group(['prefix' => 'live_stock/', 'as' => 'live_stock.'], function () {
            Route::get('index', [StockController::class, 'live_stock'])->name('index');
            Route::get('export_excel', [StockController::class, 'export_excel'])->name('export_excel');
            Route::get('export_pdf', [StockController::class, 'export_pdf'])->name('export_pdf');
        });

        Route::group(['prefix' => 'in_stock/', 'as' => 'in_stock.'], function () {
            Route::get('index', [StokInController::class, 'index'])->name('index');
            Route::get('create', [StokInController::class, 'create'])->name('create');
            Route::post('store', [StokInController::class, 'store'])->name('store');
            Route::get('get_harga_satuan', [StokInController::class, 'getHargaSatuan'])->name('getHargaSatuan');
            Route::get('get_warehouse', [StokInController::class, 'getWarehouse'])->name('getWarehouse');
        });

        Route::group(['prefix' => 'out_stock/', 'as' => 'out_stock.'], function () {
            Route::get('index', [StockOutController::class, 'index'])->name('index');
            Route::get('create', [StockOutController::class, 'create'])->name('create');
            Route::post('store', [StockOutController::class, 'store'])->name('store');
            Route::get('get_harga_satuan', [StockOutController::class, 'getHargaSatuan'])->name('getHargaSatuan');
            Route::get('get_warehouse', [StokInController::class, 'getWarehouse'])->name('getWarehouse');
            Route::get('cek_live_stok', [StockOutController::class, 'cekLiveStok'])->name('cekLiveStok');
            Route::get('get_menu', [StockOutController::class, 'getMenu'])->name('getMenu');
            Route::post('update_menu', [StockOutController::class, 'updateMenu'])->name('updateMenu');
        });

        Route::group(['prefix' => 'adjustment_stock/', 'as' => 'adjustment_stock.'], function () {
            Route::get('index', [StockAdjustmentController::class, 'index'])->name('index');
            Route::get('create', [StockAdjustmentController::class, 'create'])->name('create');
            Route::post('store', [StockAdjustmentController::class, 'store'])->name('store');
            Route::get('cek_jumlah_terakhir', [StockAdjustmentController::class, 'cekJumlahTerakhir'])->name('cekJumlahTerakhir');
            Route::get('get_warehouse', [StockAdjustmentController::class, 'getWarehouse'])->name('getWarehouse');
            Route::post('verifikasi', [StockAdjustmentController::class, 'verifikasi'])->name('verifikasi');
        });

        Route::group(['prefix' => 'laporan_transaksi/', 'as' => 'laporan_transaksi.'], function () {
            Route::get('index', [LaporanTransaksiController::class, 'index'])->name('index');
            Route::get('create', [LaporanTransaksiController::class, 'create'])->name('create');
            Route::get('download/{id}', [LaporanTransaksiController::class, 'download'])->name('download');
            Route::post('preview', [LaporanTransaksiController::class, 'preview'])->name('preview');
        });

        Route::group(['prefix' => 'pengaturan/', 'as' => 'pengaturan.'], function () {
            Route::get('index', [PengaturanController::class, 'index'])->name('index');
            Route::post('update_password', [PengaturanController::class, 'updatePassword'])->name('updatePassword');
        });
    });

});
