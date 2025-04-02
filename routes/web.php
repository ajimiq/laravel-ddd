<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return view('/index');
});

// Route::middleware(['auth'])->group(function () {
//     Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
//     Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
// });

Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{orderId}/receipt', [OrderController::class, 'showReceipt'])->name('orders.receipt');
Route::post('/orders/{orderId}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
Route::get('/orders/{orderId}', [OrderController::class, 'showDetail'])->name('orders.detail');
