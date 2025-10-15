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
    Route::get('/operations/kds/check-new-orders', [KitchenDisplayController::class, 'checkNewOrders']);
    Route::get('/operations/kds/get-today-orders', [KitchenDisplayController::class, 'fetchTodayOrders']);
    Route::post('/operations/kds/update-status', [KitchenDisplayController::class, 'updateStatus']);
    Route::post('/operations/pos/void-order/{order}', [POSController::class, 'voidOrder'])->name('pos.voidOrder');
});
