<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Operations\OperationsController;
use App\Http\Controllers\Admin\Operations\POSController;

Route::middleware(['auth' , 'isEmployee'])->group(function () {
    ////////////////////////////////// DASHBOARD ////////////////////////////////////////////////
    Route::get('/operations', [OperationsController::class, 'index'])->name('op.dashboard');
    Route::get('/operations/pos', [OperationsController::class, 'pos'])->name('op.pos');

    ////////////////////////////////////////////// POS /////////////////////////////////////////
    Route::get('/operations/pos/get-all-products', [POSController::class, 'getAllProducts']);
});
