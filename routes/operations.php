<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth' , 'isEmployee'])->group(function () {
    ////////////////////////////////// DASHBOARD ////////////////////////////////////////////////
    Route::get('/inventory', [OperationsController::class, 'index'])->name('op.dashboard');
});