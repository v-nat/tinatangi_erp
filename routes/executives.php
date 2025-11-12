<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Executives\ExecutivesController;

Route::get('/executives', [ExecutivesController::class, 'index'])->middleware('auth', 'isEmployee')->name('executives');

Route::prefix('/executives')->middleware(['auth', 'isEmployee'])->group(function () {
    Route::get('/analytics', [ExecutivesController::class, 'analytics'])->name('executives.analytics');
});
