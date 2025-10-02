<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Supplier\SupplierController;
use App\Http\Controllers\Admin\Procurement\PurchaseOrderController;

Route::middleware(['isSupplier'])->group(function () {
    Route::get('/supplier/approve-purchase', [SupplierController::class, 'approveRequest'])->name('supplier.approve');
    Route::get('/supplier/orders/get-list', [SupplierController::class, 'purchaseOrdersList']);
    Route::put('/supplier/orders/process/{id}/{status}', [PurchaseOrderController::class, 'processPurchaseOrders']);
});
