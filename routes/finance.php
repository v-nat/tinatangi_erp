<?php

use App\Http\Controllers\Admin\Finance\FinanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HR\PayrollController;

Route::middleware(['auth'])->group(function () {
    Route::get('/finance/payroll', [PayrollController::class, 'indexOnFinance'])->name('finance.payroll');
    Route::put('/finance/payroll/process/{id}/{status}', [PayrollController::class, 'putOnProcess']);


    Route::get('/finance/budgets', [FinanceController::class, 'budgetsIndex'])->name('finance.budgets');
    Route::get('/finance/budgets/requests', [FinanceController::class, 'getPendingRequests']);
    Route::put('/finance/budgets/requests/approve/{id}/{req_id}', [FinanceController::class,'approveRequest']);
    Route::put('/finance/budgets/requests/reject/{id}', [FinanceController::class,'rejectRequest']);

    Route::get('/finance/purchases', [FinanceController::class, 'purchasesIndex'])->name('finance.purchases');
});