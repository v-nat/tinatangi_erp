<?php

use App\Http\Controllers\Admin\Finance\FinanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HR\PayrollController;
use App\Http\Controllers\Admin\Procurement\ProcurementController;
use App\Http\Controllers\Admin\Procurement\PurchaseOrderController;

Route::middleware(['auth'])->group(function () {
    Route::get('/finance/payroll', [PayrollController::class, 'indexOnFinance'])->name('finance.payroll');
    Route::put('/finance/payroll/process/{id}/{status}', [PayrollController::class, 'putOnProcess']);

    Route::get('/finance/budgets', [FinanceController::class, 'budgetsIndex'])->name('finance.budgets');
    Route::get('/finance/budgets/requests', [FinanceController::class, 'getPendingRequests']);
    Route::get('/finance/budgets/history', [FinanceController::class, 'getRequestsHistory']);
    Route::put('/finance/budgets/requests/approve/{id}/{req_id}', [FinanceController::class,'approveRequest']);
    Route::put('/finance/budgets/requests/reject/{id}', [FinanceController::class,'rejectRequest']);

    Route::get('/finance/purchases', [FinanceController::class, 'purchasesIndex'])->name('finance.purchases');
    Route::get('/finance/purchases/get-details/{id}', [FinanceController::class, 'getDetailsForViewing']);
    Route::get('/finance/purchases/get-requests', [FinanceController::class, 'purchaseRequests']);
    Route::put('/finance/purchases/process/{id}/{status}', [PurchaseOrderController::class, 'putOnProcess']);
});