<?php

use App\Livewire\Storefront\Account\Addresses;
use App\Livewire\Storefront\Account\OrderShow;
use App\Livewire\Storefront\Account\Orders;
use App\Livewire\Storefront\Account\Profile;
use App\Livewire\Storefront\Account\SetPassword;
use App\Livewire\Storefront\Auth\ForgotPassword;
use App\Livewire\Storefront\Auth\Login;
use App\Livewire\Storefront\Auth\Register;
use App\Livewire\Storefront\Auth\ResetPassword;
use App\Livewire\Storefront\Cart;
use App\Livewire\Storefront\Checkout;
use App\Livewire\Storefront\HomePage;
use App\Livewire\Storefront\ProductDetails;
use App\Livewire\Storefront\ProductListing;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

// Public storefront
Route::get('/', HomePage::class)->name('home');
Route::get('/shop', ProductListing::class)->name('shop');
Route::get('/categories/{slug}', ProductListing::class)->name('categories.show');
Route::get('/products/{slug}', ProductDetails::class)->name('products.show');
Route::get('/cart', Cart::class)->name('cart');
Route::get('/checkout', Checkout::class)->name('checkout');

Route::get('/order/{order:order_number}/success', function (Order $order) {
    return view('storefront.order-success', compact('order'));
})->name('checkout.success');

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('/account/password/set', SetPassword::class)->name('account.password.set');

    Route::middleware('password.set')->group(function () {
        Route::get('/account/profile', Profile::class)->name('account.profile');
        Route::get('/account/orders', Orders::class)->name('account.orders');
        Route::get('/account/orders/{orderNumber}', OrderShow::class)->name('account.orders.show');
        Route::get('/account/addresses', Addresses::class)->name('account.addresses');
    });
});
