<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CRM\CrmController;

Route::middleware(['auth', 'isEmployee'])->group(function () {
    Route::get('/customer-service', [CrmController::class, 'index'])->name('crm');
});
