<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HR\AttendanceController;
use App\Http\Controllers\Admin\HR\HR_Controller;
use App\Http\Controllers\Admin\HR\EmployeeController;
use App\Http\Controllers\Admin\HR\OvertimeController;
use App\Http\Controllers\Admin\HR\LeaveController;
use App\Http\Controllers\Admin\HR\PayrollController;

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
    Route::get('/supervisors-by-department-and-position', [EmployeeController::class, 'getSupervisorForPosition']);
    Route::get('/positions-by-department', [EmployeeController::class, 'getPositions']);
    Route::get('/ceo', [EmployeeController::class, 'getCEO']);

    Route::get('/humanresources/overtimes', [HR_Controller::class, 'otApp'])->name('hr.ot-app');
    Route::get('/employee/overtimes/{id}', [HR_Controller::class, 'otApplication'])->name('hr.ot-application');
    Route::get('/employee/overtimes/requests/list/{id}', [OvertimeController::class, 'getUserReq']);
    Route::post('/employee/overtimes/request/submit', [OvertimeController::class, 'submitReq']);
    Route::get('/humanresources/overtimes/get', [OvertimeController::class, 'index']);
    Route::post('/humanresources/overtime/approve/{overtime_id}', [OvertimeController::class,'approve']);
    Route::post('/humanresources/overtime/reject/{overtime_id}', [OvertimeController::class,'reject']);

    Route::get('/humanresources/leaves', [HR_Controller::class, 'leaveApp'])->name('hr.leave-app');
    Route::get('/employee/leaves/{id}', [HR_Controller::class, 'leaveApplication'])->name('hr.leave-application');
    Route::get('/employee/leaves/requests/list/{id}', [LeaveController::class,'getUserReq']);
    Route::post('/employee/leaves/request/submit', [LeaveController::class,'submitReq']);
    Route::get('/humanresources/leaves/get', [LeaveController::class,'index']);
    Route::post('/humanresources/leave/approve/{leave_id}', [LeaveController::class,'approve']);
    Route::post('/humanresources/leave/reject/{leave_id}', [LeaveController::class,'reject']);

    Route::get('/humanresources/payroll',[PayrollController::class, 'indexOnHr'])->name('hr.payroll');
    Route::get('/humanresources/payroll/list', [PayrollController::class,'getPayrollList']);
    Route::post('/humanresources/payroll/generate',[PayrollController::class, 'generatePayroll'])->name('hr.payroll.generate');
});
