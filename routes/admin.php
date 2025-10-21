<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['isEmployee'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
});
