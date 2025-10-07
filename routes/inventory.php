<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Finance\InvoiceController;
use App\Http\Controllers\Admin\Inventory\InventoryController;

Route::middleware(['auth' , 'isEmployee'])->group(function () {
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::get('/inventory/all-items', [InventoryController::class, 'all'])->name('inventory.all-items');
    Route::get('/inventory/get-to-receive', [InventoryController::class, 'getToReceive']);
    Route::get('/inventory/item-to-receive/get-invoice/{id}', [InvoiceController::class, 'getInvoiceForViewing']);

});
