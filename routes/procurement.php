<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Finance\FinanceController;
use App\Http\Controllers\Admin\Finance\InvoiceController;
use App\Http\Controllers\Admin\Procurement\SupplierController;
use App\Http\Controllers\Admin\Procurement\ProcurementController;
use App\Http\Controllers\Admin\Procurement\PurchaseOrderController;
use App\Http\Controllers\GenerateIdController;

Route::middleware(['auth' , 'isEmployee'])->group(function () {
    //////////////////////////////////////////  to views /////////////////////////////////////
    Route::get('/procurement', [ProcurementController::class, 'index'])->name('procurement.index');
    Route::get('/procurement/create-purchase-request', [ProcurementController::class, 'createPR'])->name('procurement.createPR');
    Route::get('/procurement/purchase-orders', [ProcurementController::class, 'purchaseOrders'])->name('procurement.purchaseOrders');
    Route::get('/procurement/supplier', [ProcurementController::class, 'supplier'])->name('procurement.supplier');

    /////////////////////////////////////////// SUPPLIER //////////////////////////////////////////
    Route::post('/procurement/supplier/add-supplier/', [SupplierController::class,'storeSupplier']);
    Route::get('/procurement/supplier/get-supplier', [SupplierController::class,'getSupplier']);

    /////////////////////////////////////////////// CREATE PR /////////////////////////////////////////////////////
    Route::get('/procurement/generateOrderID/{type}', [GenerateIdController::class,'generateID']);
    Route::get('/procurement/create-purchase-request/get-active-supplier', [SupplierController::class,'getActiveSupplier']);
    Route::get('/procurement/create-purchase-request/get-categories', [PurchaseOrderController::class,'getCategories']);
    Route::get('/procurement/create-purchase-request/get-items', [PurchaseOrderController::class,'getItems']);
    Route::post('/procurement/create-purchase-request/submit-request/', [PurchaseOrderController::class,'store']);
    Route::post('/procurement/complete-purchase-request/submit-request/', [PurchaseOrderController::class,'sendReq']);

    ///////////////////////////////////////// PURCHASE ORDER //////////////////////////////////////////////////
    Route::get('/procurement/purchases/get-list', [ProcurementController::class, 'purchaseOrdersList']);
    Route::get('/procurement/purchases/get-requests-list', [ProcurementController::class, 'purchaseRequestsList']);
    Route::get('/procurement/purchases/get-restock-data/{id}', [ProcurementController::class, 'getRestockData']);
    Route::get('/procurement/purchases/get-details/{id}', [FinanceController::class, 'getDetailsForViewing']);
    Route::get('/procurement/purchases/get-invoice/{id}', [InvoiceController::class, 'getInvoiceForViewing']);
    Route::put('/procurement/purchases/order/{id}/{status}', [PurchaseOrderController::class, 'processPurchaseOrders']);

});
