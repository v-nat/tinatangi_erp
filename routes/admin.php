<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;

Route::get('/admin', [AdminController::class, 'index'])->middleware('auth', 'isEmployee')->name('admin');

Route::prefix('/admin')->middleware(['auth', 'isEmployee'])->group(function () {
});
