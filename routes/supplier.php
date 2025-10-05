<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Supplier\SupplierController;
use App\Http\Controllers\Admin\Finance\FinanceController;
use App\Http\Controllers\Admin\Procurement\PurchaseOrderController;

Route::middleware(['isSupplier'])->group(function () {
    Route::get('/supplier/approve-purchase', [SupplierController::class, 'approveRequest'])->name('supplier.approve');
    Route::get('/supplier/orders/get-list', [SupplierController::class, 'purchaseOrdersList']);
    Route::get('/supplier/purchases/get-details/{id}', [FinanceController::class, 'getDetailsForViewing']);
    Route::put('/supplier/orders/process/{id}/{status}', [PurchaseOrderController::class, 'processPurchaseOrders']);
});
