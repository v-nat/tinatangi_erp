<?php

use App\Http\Controllers\Admin\Procurement\PurchaseOrderController;
use App\Http\Controllers\Admin\Procurement\SupplierController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/procurement', [PurchaseOrderController::class, 'index'])->name('procurement.index');
    Route::get('/procurement/create-purchase-order', [PurchaseOrderController::class, 'createPO'])->name('procurement.createPO');
    Route::get('/procurement/supplier', [PurchaseOrderController::class, 'supplier'])->name('procurement.supplier');
    Route::get('/procurement/purchase-orders', [PurchaseOrderController::class, 'purchaseOrders'])->name('procurement.purchaseOrders');

    Route::post('/procurement/supplier/add-supplier', [SupplierController::class,'storeSupplier']);
    Route::get('/procurement/supplier/get-supplier', [SupplierController::class,'getSupplier']);
    Route::get('/procurement/supplier/get-active-supplier', [SupplierController::class,'getActiveSupplier']);
});