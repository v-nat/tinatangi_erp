<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;

Route::middleware(['auth', 'isEmployee'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
});
