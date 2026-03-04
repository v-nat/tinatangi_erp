<?php

use App\Http\Controllers\Admin\Operations\KitchenDisplayController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Operations\OperationsController;
use App\Http\Controllers\Admin\Operations\POSController;
use App\Http\Controllers\Admin\Operations\SalesReportController;

Route::get('/operations', [OperationsController::class, 'index'])->middleware('auth', 'isEmployee')->name('op.dashboard');

Route::prefix('/operations')->middleware(['auth', 'isEmployee'])->group(function () {
    Route::get('/dashboard-analytics', [OperationsController::class, 'getDashboardAnalytics']);

    ////////////////////////////////// NAV ROUTES //////////////////////////////////////////////
    Route::get('/pos', [OperationsController::class, 'pos'])->name('op.pos');
    Route::get('/kds', [OperationsController::class, 'kds'])->name('op.kds');

    ////////////////////////////////////////////// POS /////////////////////////////////////////
    Route::get('/pos/get-all-products', [POSController::class, 'getAllProducts']);
    Route::get('/pos/get-pastries-products', [POSController::class, 'getPastriesProducts']);
    Route::get('/pos/get-beverages-products', [POSController::class, 'getBeveragesProducts']);
    Route::get('/pos/get-meals-products', [POSController::class, 'getMealsProducts']);
    Route::get('/pos/get-snacks-sides-products', [POSController::class, 'getSnacksAndSidesProducts']);
    Route::get('/pos/categories', [POSController::class, 'getProductCategories']);
    Route::get('/pos/products', [POSController::class, 'getProducts']);
    Route::get('/pos/monthly-best-sellers', [POSController::class, 'monthlyBestSellers']);
    Route::get('/pos/active-discounts', [POSController::class, 'getActiveDiscounts']);
    Route::post('/pos/submit-order', [POSController::class, 'submitOrder']);
    Route::get('/pos/recent-orders', [POSController::class, 'recentOrders']);

    //////////////////////////////////////////// KDS ///////////////////////////////////////////
    Route::get('/kds/check-new-orders', [KitchenDisplayController::class, 'checkNewOrders']);
    Route::get('/kds/get-today-orders', [KitchenDisplayController::class, 'fetchTodayOrders']);
    Route::post('/kds/update-status', [KitchenDisplayController::class, 'updateStatus']);
    Route::post('/pos/void-order/{order}', [POSController::class, 'voidOrder'])->name('pos.voidOrder');

    //////////////////////////////////////////// SALES REPORTING ///////////////////////////////////////////
    Route::get('/sales/reporting', [SalesReportController::class, 'index'])->name('op.sales.reporting');
    Route::get('/sales/reporting/orders', [SalesReportController::class, 'listEligibleOrders']);
    Route::get('/sales/reporting/submissions', [SalesReportController::class, 'listSubmittedReports']);
    Route::post('/sales/reporting/submit', [SalesReportController::class, 'store']);
});
