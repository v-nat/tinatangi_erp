<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CRM\CrmController;

Route::middleware(['isEmployee'])->group(function () {
    Route::get('/customer-service', [CrmController::class, 'index'])->name('crm');
});
