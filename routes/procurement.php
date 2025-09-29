<?php

use App\Http\Controllers\Admin\Procurement\ProcurementController;
use App\Http\Controllers\Admin\Procurement\PurchaseOrderController;
use App\Http\Controllers\Admin\Procurement\SupplierController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    //////////////////////////////////////////  to views /////////////////////////////////////
    Route::get('/procurement', [ProcurementController::class, 'index'])->name('procurement.index');
    Route::get('/procurement/create-purchase-request', [ProcurementController::class, 'createPR'])->name('procurement.createPR');
    Route::get('/procurement/purchase-orders', [ProcurementController::class, 'purchaseOrders'])->name('procurement.purchaseOrders');
    Route::get('/procurement/supplier', [ProcurementController::class, 'supplier'])->name('procurement.supplier');


    /////////////////////////////////////////// SUPPLIER //////////////////////////////////////////
    Route::post('/procurement/supplier/add-supplier', [SupplierController::class,'storeSupplier']);
    Route::get('/procurement/supplier/get-supplier', [SupplierController::class,'getSupplier']);

    /////////////////////////////////////////////// CREATE PR /////////////////////////////////////////////////////
    Route::get('/procurement/generateOrderID', [ProcurementController::class,'generatePurchaseOrderID']);
    Route::get('/procurement/create-purchase-request/get-active-supplier', [SupplierController::class,'getActiveSupplier']);
    Route::get('/procurement/create-purchase-request/get-categories', [PurchaseOrderController::class,'getCategories']);
    Route::get('/procurement/create-purchase-request/get-items', [PurchaseOrderController::class,'getItems']);
    Route::post('/procurement/create-purchase-request/submit-request', [PurchaseOrderController::class,'store']);
});