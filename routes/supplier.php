<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Supplier\SupplierController;
use App\Http\Controllers\Admin\Finance\FinanceController;
use App\Http\Controllers\Admin\Finance\InvoiceController;
use App\Http\Controllers\Admin\Procurement\PurchaseOrderController;

Route::get('/supplier', [SupplierController::class, 'index'])->middleware('auth', 'isSupplier')->name('supplier');

Route::prefix('/supplier')->middleware(['auth', 'isSupplier'])->group(function () {
    Route::get('/approve-purchase', [SupplierController::class, 'approveRequest'])->name('supplier.approve');
    Route::get('/orders/get-list', [SupplierController::class, 'purchaseOrdersList']);
    Route::get('/purchases/get-details/{id}', [FinanceController::class, 'getDetailsForViewing']);
    Route::get('/purchases/get-invoice/{id}', [InvoiceController::class, 'getInvoiceForViewing']);
    Route::put('/orders/process/{id}/{status}', [PurchaseOrderController::class, 'processPurchaseOrders']);
});
