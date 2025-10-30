<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HR\PayrollController;
use App\Http\Controllers\Admin\Finance\FinanceController;
use App\Http\Controllers\Admin\Finance\InvoiceController;
use App\Http\Controllers\Admin\Procurement\PurchaseOrderController;

Route::get('/finance', [FinanceController::class, 'finance'])->middleware('auth', 'isEmployee')->name('finance');

Route::prefix('/finance')->middleware(['auth' , 'isEmployee'])->group(function () {

    /////////////////////////////////////////////////////////// PAYROLL //////////////////////////////////////////////////////
    Route::get('/payroll', [PayrollController::class, 'indexOnFinance'])->name('finance.payroll');
    Route::get('/payroll/list', [PayrollController::class, 'getPayrollList']);
    Route::get('/payroll/view/{id}', [PayrollController::class, 'getPayrollview']);
    Route::put('/payroll/process/{id}/{status}', [PayrollController::class, 'putOnProcess']);

    /////////////////////////////////////////////////////////// BUDGETS //////////////////////////////////////////////////////
    Route::get('/budgets', [FinanceController::class, 'budgetsIndex'])->name('finance.budgets');
    Route::get('/budgets/requests', [FinanceController::class, 'getPendingRequests']);
    Route::get('/budgets/history', [FinanceController::class, 'getRequestsHistory']);
    Route::put('/budgets/requests/approve/{id}/{req_id}', [FinanceController::class,'approveRequest']);
    Route::put('/budgets/requests/reject/{id}', [FinanceController::class,'rejectRequest']);

    /////////////////////////////////////////////////////////// PR / PO //////////////////////////////////////////////////////
    Route::get('/purchases', [FinanceController::class, 'purchasesIndex'])->name('finance.purchases');
    Route::get('/purchases/get-details/{id}', [FinanceController::class, 'getDetailsForViewing']);
    Route::get('/purchases/get-invoice/{id}', [InvoiceController::class, 'getInvoiceForViewing']);
    Route::get('/purchases/get-requests', [FinanceController::class, 'purchaseRequests']);
    Route::put('/purchases/process/{id}/{status}', [PurchaseOrderController::class, 'putOnProcess']);
});
