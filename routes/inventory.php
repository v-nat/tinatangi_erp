<?php

use App\Http\Controllers\Admin\Inventory\InventoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth' , 'isEmployee'])->group(function () {
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
});