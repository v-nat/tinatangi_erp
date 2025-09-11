<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get("/login", function () {
    return view("auth.login");
})->name("login");

Route::post('/login', [AuthController::class, 'adminLogin'])->name('admin.login');
Route::post('/logout-account', [AuthController::class, 'logout'])->name('logout');
