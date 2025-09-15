<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Hr\AttendanceController;
use App\Http\Controllers\Admin\Hr\HR_Controller;
use App\Http\Controllers\Admin\Hr\EmployeeController;
use App\Http\Controllers\Admin\HR\OvertimeController;
use App\Http\Controllers\Admin\HR\LeaveController;

Route::middleware(['auth'])->group(function () {
    Route::get('/attendance/list', [AttendanceController::class, 'attendanceList']);
    Route::get('/attendance/this-day', [AttendanceController::class, 'thisDay']);
    Route::post('/attendance/time-in', [AttendanceController::class, 'timeIn']);
    Route::post('/attendance/time-out', [AttendanceController::class, 'timeOut']);

    Route::get('/humanresources', [HR_Controller::class, 'index'])->name('hr.dashboard');
    Route::get('/humanresources/employees', [HR_Controller::class, 'employees'])->name('hr.employees');
    Route::get('/humanresources/employees/get', [HR_Controller::class, 'getEmployees']);
    Route::post('/humanresources/store-employee', [EmployeeController::class, 'storeEmployee']);
    Route::get('/humanresources/edit-employee/{id}', [EmployeeController::class, 'editEmployee'])->name('edit.employee');
    Route::put('/humanresources/update-employee/{id}', [EmployeeController::class, 'updateEmployee'])->name('update.employee');

    Route::get('/humanresources/manage', [EmployeeController::class, 'manage'])->name('hr.manage');
    Route::get('/supervisors-by-department', [EmployeeController::class, 'getSupervisors']);
    Route::get('/ceo', [EmployeeController::class, 'getCEO']);

    Route::get('/humanresources/overtimes', [HR_Controller::class, 'otApp'])->name('hr.ot-app');
    Route::get('/humanresources/overtimes/get', [OvertimeController::class, 'index']);
    Route::post('/humanresources/overtime/approve/{overtime_id}', [OvertimeController::class,'approve']);
    Route::post('/humanresources/overtime/reject/{overtime_id}', [OvertimeController::class,'reject']);

    Route::get('/humanresources/leaves', [HR_Controller::class, 'leaveApp'])->name('hr.leave-app');
    Route::get('/humanresources/leaves/get', [LeaveController::class,'index']);
    Route::post('/humanresources/leave/approve/{leave_id}', [LeaveController::class,'approve']);
    Route::post('/humanresources/leave/reject/{leave_id}', [LeaveController::class,'reject']);

});
