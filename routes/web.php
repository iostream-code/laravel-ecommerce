<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ---------- Storefront (publik) ----------
Route::get('/', [StorefrontController::class, 'landing'])->name('landing');
Route::get('/katalog', [StorefrontController::class, 'catalog'])->name('products');
Route::get('/produk/{product:slug}', [StorefrontController::class, 'show'])->name('product');

Auth::routes();

// Setelah login, arahkan sesuai role
Route::get('/home', function () {
    return auth()->user()?->is_admin
        ? redirect()->route('admin.dashboard')
        : redirect()->route('products');
})->name('home');

// ---------- Pembeli (login) ----------
Route::middleware('auth')->group(function () {
    Route::post('/cart/{product:slug}', [CartController::class, 'addToCart'])->name('add_to_cart');
    Route::get('/cart', [CartController::class, 'showCart'])->name('cart');
    Route::patch('/cart/{cart}', [CartController::class, 'updateCart'])->name('update_cart');
    Route::delete('/cart/{cart}', [CartController::class, 'deleteCart'])->name('delete_cart');

    Route::get('/checkout', [OrderController::class, 'checkoutForm'])->name('checkout_form');
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::get('/orders', [OrderController::class, 'orders'])->name('orders');
    Route::get('/order/{order}', [OrderController::class, 'detailOrder'])->name('detail_order');
    Route::post('/order/{order}/pay', [OrderController::class, 'submitPayment'])->name('submit_payment');

    Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile');
    Route::get('/profile/{user}/edit', [ProfileController::class, 'editProfile'])->name('edit_profile');
});

// ---------- Webhook Midtrans (tanpa auth & CSRF) ----------
Route::post('/midtrans/callback', [OrderController::class, 'midtransCallback'])->name('midtrans_callback');

// ---------- Admin ----------
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/produk', [Admin\ProductController::class, 'index'])->name('products');
    Route::get('/produk/tambah', [Admin\ProductController::class, 'create'])->name('products.create');
    Route::post('/produk', [Admin\ProductController::class, 'store'])->name('products.store');
    Route::get('/produk/{product:slug}/edit', [Admin\ProductController::class, 'edit'])->name('products.edit');
    Route::patch('/produk/{product:slug}', [Admin\ProductController::class, 'update'])->name('products.update');
    Route::delete('/produk/{product:slug}', [Admin\ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/pesanan', [Admin\OrderController::class, 'index'])->name('orders');
    Route::get('/pesanan/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/pesanan/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');
});
