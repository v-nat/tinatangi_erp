<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CRM\CrmController;

Route::get('/customer-service', [CrmController::class, 'index'])->middleware('auth', 'isEmployee')->name('crm');

Route::prefix('/customer-service')->middleware(['auth', 'isEmployee'])->group(function () {
});
