<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/collections/{slug}', [PageController::class, 'collection'])->name('collections.show');
Route::get('/collections/{collection}/products/{slug}', [PageController::class, 'product'])->name('products.show');
Route::get('/search', [PageController::class, 'search'])->name('search');
Route::get('/wishlist', [PageController::class, 'wishlist'])->name('wishlist');
Route::get('/cart', [PageController::class, 'cart'])->name('cart');
Route::get('/checkout', [PageController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'place'])->name('checkout.place');
Route::get('/checkout/thanks/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/track-order', [PageController::class, 'trackOrder'])->name('orders.track');
Route::get('/api/products', [PageController::class, 'productsApi'])->name('api.products');
Route::get('/policy', [PageController::class, 'policy'])->name('policy');
Route::get('/lookbook', [PageController::class, 'lookbook'])->name('lookbook');

Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::middleware('admin.session')->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])->name('admin.orders.update');
    Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('admin.orders.destroy');
    Route::post('/sync', [AdminController::class, 'sync'])->name('admin.sync');
    Route::post('/popular', [AdminController::class, 'updatePopular'])->name('admin.popular');
    Route::post('/settings/currency', [AdminSettingsController::class, 'updateCurrency'])->name('admin.settings.currency');
    Route::post('/settings/currency/live', [AdminSettingsController::class, 'fetchLiveRate'])->name('admin.settings.currency.live');
    Route::get('/settings/currency/live-rate', [AdminSettingsController::class, 'liveRatePreview'])->name('admin.settings.currency.live_rate');
    Route::post('/settings/bank', [AdminSettingsController::class, 'updateBankDetails'])->name('admin.settings.bank');
    Route::get('/products', [AdminProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{product}', [AdminProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::post('/products/{product}/variants', [AdminProductController::class, 'storeVariant'])->name('admin.products.variants.store');
    Route::put('/variants/{variant}', [AdminProductController::class, 'updateVariant'])->name('admin.variants.update');
    Route::delete('/variants/{variant}', [AdminProductController::class, 'destroyVariant'])->name('admin.variants.destroy');
});

Route::redirect('/index.html', '/');
Route::redirect('/winter-25.html', '/collections/winter25');
Route::redirect('/policy.html', '/policy');
Route::redirect('/lookbook.html', '/lookbook');
Route::redirect('/shop', '/collections/men-all');
