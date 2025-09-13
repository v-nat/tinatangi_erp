<?php

use App\Http\Controllers\Admin\Hr\HR_Controller;
use App\Http\Controllers\Admin\Hr\EmployeeController;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth'])->group(function () {
    Route::get('/humanresources', [HR_Controller::class, 'index'])->name('hr.dashboard');
    Route::get('/humanresources/employees', [HR_Controller::class, 'employees'])->name('hr.employees');
    Route::get('/humanresources/employees/get', [HR_Controller::class, 'getEmployees']);
    Route::post('/humanresources/store-employee', [EmployeeController::class, 'storeEmployee']);
    Route::get('/humanresources/edit-employee/{id}', [EmployeeController::class, 'editEmployee'])->name('edit.employee');
    Route::put('/humanresources/update-employee/{id}', [EmployeeController::class, 'updateEmployee'])->name('update.employee');

    Route::get('/humanresources/manage', [EmployeeController::class, 'manage'])->name('hr.manage');
    Route::get('/supervisors-by-department', [EmployeeController::class, 'getSupervisors']);
    Route::get('/ceo', [EmployeeController::class, 'getCEO']);
    // Route::any('/humanresources/manage/store-employee', function () {
    //     dd('Fallback route hit');
    // });
    Route::get('/overtimes', [HR_Controller::class, 'otApp'])->name('hr.ot-app');
    Route::get('/leaves', [HR_Controller::class, 'leaveApp'])->name('hr.leave-app');
// });