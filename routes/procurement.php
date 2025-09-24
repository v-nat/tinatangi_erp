<?php

use App\Http\Controllers\Admin\Procurement\PurchaseOrderController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/procurement', [PurchaseOrderController::class, 'index'])->name('procurement.index');
});