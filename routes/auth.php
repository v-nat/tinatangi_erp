<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get("/login", function () {
    return view("auth.login");
})->name("login");

Route::post('/login-account', [AuthController::class, 'adminLogin']);
Route::post('/logout-account', [AuthController::class, 'logout'])->name('logout');
