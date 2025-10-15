<?php

use App\Http\Controllers\Admin\Operations\KitchenDisplayController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Operations\OperationsController;
use App\Http\Controllers\Admin\Operations\POSController;

Route::middleware(['auth', 'isEmployee'])->group(function () {
    /////////////////////////////////////// DASHBOARD //////////////////////////////////////////
    Route::get('/operations', [OperationsController::class, 'index'])->name('op.dashboard');

    ////////////////////////////////// NAV ROUTES //////////////////////////////////////////////
    Route::get('/operations/pos', [OperationsController::class, 'pos'])->name('op.pos');
    Route::get('/operations/kds', [OperationsController::class, 'kds'])->name('op.kds');

    ////////////////////////////////////////////// POS /////////////////////////////////////////
    Route::get('/operations/pos/get-all-products', [POSController::class, 'getAllProducts']);
    Route::get('/operations/pos/get-pastries-products', [POSController::class, 'getPastriesProducts']);
    Route::get('/operations/pos/get-beverages-products', [POSController::class, 'getBeveragesProducts']);
    Route::get('/operations/pos/get-meals-products', [POSController::class, 'getMealsProducts']);
    Route::get('/operations/pos/get-snacks-sides-products', [POSController::class, 'getSnacksAndSidesProducts']);
    Route::post('/operations/pos/submit-order', [POSController::class, 'submitOrder']);
    Route::get('/operations/pos/recent-orders', [POSController::class, 'recentOrders']);

    //////////////////////////////////////////// KDS ///////////////////////////////////////////
    Route::get('/operations/kds/get-today-orders', [KitchenDisplayController::class, 'fetchTodayOrders']);

    // TEMPORARY TEST ROUTE - REMOVE AFTER DEBUG
    Route::get('/operations/test-pusher/{orderId}', function ($orderId) {
        try {
            $order = App\Models\Order::findOrFail($orderId);
            // Dispatch the event synchronously, bypassing the queue
            \App\Events\NewOrderCreated::dispatch($order);
            return "SUCCESS: Event dispatched for Order ID {$orderId} via sync connection.";
        } catch (\Exception $e) {
            // This will now catch any PHP or Pusher SDK exceptions immediately
            return "FAILURE: " . $e->getMessage() . " on line " . $e->getLine();
        }
    });
});
