<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HR\AttendanceController;
use App\Http\Controllers\Admin\HR\HR_Controller;
use App\Http\Controllers\Admin\HR\EmployeeController;
use App\Http\Controllers\Admin\HR\OvertimeController;
use App\Http\Controllers\Admin\HR\LeaveController;
use App\Http\Controllers\Admin\HR\PayrollController;

Route::middleware(['auth'])->group(function () {
    Route::get('/attendance/this-day', [AttendanceController::class, 'thisDay']);
    Route::get('/attendance/isOnLeave', [AttendanceController::class, 'isOnLeave']);
    Route::post('/attendance/time-in', [AttendanceController::class, 'timeIn']);
    Route::post('/attendance/time-out', [AttendanceController::class, 'timeOut']);
    Route::get('/attendance/employee-attendance-list/{id}', [AttendanceController::class, 'employeeAttendanceList'])->name('employee.attendance.list');
    Route::get('/attendance/get-employee-attendance-list/{id}', [AttendanceController::class, 'getEmployeeAttendanceList']);
});

Route::middleware(['auth' , 'isEmployee'])->group(function () {
    /////////////////////////// ATTENDANCE ///////////////////////////////////////
    Route::get('/humanresources/attendance/list', [AttendanceController::class, 'attendanceList']);

    ////////////////////////////////// EMPLOYEE MANAGEMENT //////////////////////////
    Route::get('/humanresources', [HR_Controller::class, 'index'])->name('hr.dashboard');
    Route::get('/humanresources/employees', [HR_Controller::class, 'employees'])->name('hr.employees');
    Route::get('/humanresources/employees/get', [HR_Controller::class, 'getEmployees']);
    Route::post('/humanresources/store-employee', [EmployeeController::class, 'storeEmployee']);
    Route::get('/humanresources/edit-employee/{id}', [EmployeeController::class, 'editEmployee'])->name('edit.employee');
    Route::put('/humanresources/update-employee/{id}', [EmployeeController::class, 'updateEmployee'])->name('update.employee');

    Route::get('/humanresources/manage', [EmployeeController::class, 'manage'])->name('hr.manage');
    Route::get('/humanresources/supervisors-by-department-and-position', [EmployeeController::class, 'getSupervisorForPosition']);
    Route::get('/humanresources/positions-by-department', [EmployeeController::class, 'getPositions']);
    Route::get('/ceo', [EmployeeController::class, 'getCEO']);

    //////////////////////////////// OVERTIME ///////////////////////////////////////////
    Route::get('/humanresources/overtimes', [HR_Controller::class, 'otMngmnt'])->name('hr.ot-app');
    Route::get('/employee/overtimes/{id}', [HR_Controller::class, 'otApplication'])->name('hr.ot-application');
    Route::get('/employee/overtimes/requests/list/{id}', [OvertimeController::class, 'getUserReq']);
    Route::post('/employee/overtimes/request/submit', [OvertimeController::class, 'submitReq']);
    Route::get('/humanresources/overtimes/get', [OvertimeController::class, 'index']);
    Route::post('/humanresources/overtime/approve/{overtime_id}', [OvertimeController::class, 'approve']);
    Route::post('/humanresources/overtime/reject/{overtime_id}', [OvertimeController::class, 'reject']);

    ///////////////////////////////// LEAVE ////////////////////////////////////////////
    Route::get('/humanresources/leaves', [HR_Controller::class, 'leaveMngmnt'])->name('hr.leave-app');
    Route::get('/employee/leaves/{id}', [HR_Controller::class, 'leaveApplication'])->name('hr.leave-application');
    Route::get('/employee/leaves/requests/list/{id}', [LeaveController::class, 'getUserReq']);
    Route::post('/employee/leaves/request/submit', [LeaveController::class, 'submitReq']);
    Route::get('/humanresources/leaves/get', [LeaveController::class, 'index']);
    Route::post('/humanresources/leave/approve/{leave_id}', [LeaveController::class, 'approve']);
    Route::post('/humanresources/leave/reject/{leave_id}', [LeaveController::class, 'reject']);

    ///////////////////////////////// PAYROLL ///////////////////////////////////////////////
    Route::get('/humanresources/payroll', [PayrollController::class, 'indexOnHr'])->name('hr.payroll');
    Route::get('/humanresources/payroll/list', [PayrollController::class, 'getPayrollList']);
    Route::get('/humanresources/payroll/view/{id}', [PayrollController::class, 'getPayrollview']);
    Route::put('/humanresources/payroll/release/{id}', [PayrollController::class, 'releasePayroll']);
    Route::post('/humanresources/payroll/generate', [PayrollController::class, 'generatePayroll'])->name('hr.payroll.generate');
});
