<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Finance\FinanceController;
use App\Http\Controllers\Admin\Finance\InvoiceController;
use App\Http\Controllers\Admin\Inventory\InventoryController;

Route::middleware(['auth' , 'isEmployee'])->group(function () {
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::get('/inventory/all-items', [InventoryController::class, 'all'])->name('inventory.all-items');
    Route::get('/inventory/data-to-display', [InventoryController::class, 'fetchDataToDisplay']);
    Route::get('/inventory/get-to-receive', [InventoryController::class, 'getToReceive']);
    Route::get('/inventory/get-to-restock', [InventoryController::class, 'getToRestock']);
    Route::get('/inventory/items-to-receive/get-invoice/{id}', [InvoiceController::class, 'getInvoiceForViewing']);
    Route::post('/inventory/items-to-receive/receive-inventory/{id}', [InventoryController::class, 'receiveInventory']);
    Route::get('/inventory/purchases/get-details/{id}', [FinanceController::class, 'getDetailsForViewing']);
    Route::get('/inventory/recent-items', [InventoryController::class, 'getRecentItems']);
    Route::get('/inventory/get-all-items', [InventoryController::class, 'getAllItems']);
    Route::post('/inventory/send-restock-request', [InventoryController::class, 'restockRequest']);

});
