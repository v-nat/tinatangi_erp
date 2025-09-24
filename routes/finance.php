<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HR\PayrollController;

Route::middleware(['auth'])->group(function () {
    Route::get('/finance/payroll', [PayrollController::class, 'indexOnFinance'])->name('finance.payroll');
});