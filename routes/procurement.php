<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Finance\FinanceController;
use App\Http\Controllers\Admin\Finance\InvoiceController;
use App\Http\Controllers\Admin\Procurement\SupplierController;
use App\Http\Controllers\Admin\Procurement\ProcurementController;
use App\Http\Controllers\Admin\Procurement\PurchaseOrderController;
use App\Http\Controllers\GenerateIdController;

Route::get('/procurement', [ProcurementController::class, 'index'])->middleware('auth', 'isEmployee')->name('procurement.index');

Route::prefix('/procurement')->middleware(['auth', 'isEmployee'])->group(function () {
    Route::get('/dashboard-analytics', [ProcurementController::class, 'getDashboardAnalytics']);
    Route::get('/spend-forecast', [ProcurementController::class, 'getSpendForecast']);
    //////////////////////////////////////////  to views /////////////////////////////////////
    Route::get('/create-purchase-request', [ProcurementController::class, 'createPR'])->name('procurement.createPR');
    Route::get('/purchase-orders', [ProcurementController::class, 'purchaseOrders'])->name('procurement.purchaseOrders');
    Route::get('/supplier', [ProcurementController::class, 'supplier'])->name('procurement.supplier');

    /////////////////////////////////////////// SUPPLIER //////////////////////////////////////////
    Route::post('/supplier/add-supplier', [SupplierController::class, 'storeSupplier']);
    Route::get('/supplier/get-supplier', [SupplierController::class, 'getSupplier']);
    Route::get('/supplier/{supplier}', [SupplierController::class, 'show']);
    Route::put('/supplier/{supplier}', [SupplierController::class, 'update']);

    /////////////////////////////////////////////// CREATE PR /////////////////////////////////////////////////////
    Route::get('/generateOrderID/{type}', [GenerateIdController::class, 'generateID']);
    Route::get('/create-purchase-request/get-active-supplier', [SupplierController::class, 'getActiveSupplier']);
    Route::get('/create-purchase-request/get-categories', [PurchaseOrderController::class, 'getCategories']);
    Route::get('/create-purchase-request/get-items', [PurchaseOrderController::class, 'getItems']);
    Route::post('/create-purchase-request/submit-request', [PurchaseOrderController::class, 'store']);
    Route::post('/complete-purchase-request/submit-request', [PurchaseOrderController::class, 'sendReq']);

    ///////////////////////////////////////// PURCHASE ORDER //////////////////////////////////////////////////
    Route::get('/purchases/get-list', [ProcurementController::class, 'purchaseOrdersList']);
    Route::get('/purchases/get-requests-list', [ProcurementController::class, 'purchaseRequestsList']);
    Route::get('/purchases/get-restock-data/{id}', [ProcurementController::class, 'getRestockData']);
    Route::get('/purchases/get-details/{id}', [FinanceController::class, 'getDetailsForViewing']);
    Route::get('/purchases/get-invoice/{id}', [InvoiceController::class, 'getInvoiceForViewing']);
    Route::put('/purchases/order/{id}/{status}', [PurchaseOrderController::class, 'processPurchaseOrders']);

    // Routes for Initial Delivery
    Route::get('/purchases/get-delivery-details/{id}', [PurchaseOrderController::class, 'getDeliveryDetailsForModal']);
    Route::post('/purchases/receive-delivery', [PurchaseOrderController::class, 'receiveDelivery']);

    // NEW: Routes for Redelivery
    Route::get('/purchases/get-redelivery-details/{id}', [PurchaseOrderController::class, 'getRedeliveryDetailsForModal']);
    Route::post('/purchases/receive-redelivery', [PurchaseOrderController::class, 'receiveRedelivery']);
});
