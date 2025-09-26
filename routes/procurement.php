<?php

use App\Http\Controllers\Admin\Procurement\ProcurementController;
use App\Http\Controllers\Admin\Procurement\PurchaseOrderController;
use App\Http\Controllers\Admin\Procurement\SupplierController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    //////////////////////////////////////////  to views /////////////////////////////////////
    Route::get('/procurement', [ProcurementController::class, 'index'])->name('procurement.index');
    Route::get('/procurement/create-purchase-order', [ProcurementController::class, 'createPO'])->name('procurement.createPO');
    Route::get('/procurement/purchase-orders', [ProcurementController::class, 'purchaseOrders'])->name('procurement.purchaseOrders');
    Route::get('/procurement/supplier', [ProcurementController::class, 'supplier'])->name('procurement.supplier');


    /////////////////////////////////////////// SUPPLIER //////////////////////////////////////////
    Route::post('/procurement/supplier/add-supplier', [SupplierController::class,'storeSupplier']);
    Route::get('/procurement/supplier/get-supplier', [SupplierController::class,'getSupplier']);

    /////////////////////////////////////////////// CREATE PO /////////////////////////////////////////////////////
    Route::get('/procurement/create-purchase-order/get-active-supplier', [SupplierController::class,'getActiveSupplier']);
    Route::get('/procurement/create-purchase-order/get-categories', [PurchaseOrderController::class,'getCategories']);
    Route::get('/procurement/create-purchase-order/get-items', [PurchaseOrderController::class,'getItems']);
});