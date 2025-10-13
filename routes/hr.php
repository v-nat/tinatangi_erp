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
    Route::get('/human-resources/attendance/list', [AttendanceController::class, 'attendanceList']);

    ////////////////////////////////// EMPLOYEE MANAGEMENT //////////////////////////
    Route::get('/human-resources', [HR_Controller::class, 'index'])->name('hr.dashboard');
    Route::get('/human-resources/employees', [HR_Controller::class, 'employees'])->name('hr.employees');
    Route::get('/human-resources/employees/get', [HR_Controller::class, 'getEmployees']);
    Route::post('/human-resources/store-employee', [EmployeeController::class, 'storeEmployee']);
    Route::get('/human-resources/edit-employee/{id}', [EmployeeController::class, 'editEmployee'])->name('edit.employee');
    Route::put('/human-resources/update-employee/{id}', [EmployeeController::class, 'updateEmployee'])->name('update.employee');

    Route::get('/human-resources/manage', [EmployeeController::class, 'manage'])->name('hr.manage');
    Route::get('/human-resources/supervisors-by-department-and-position', [EmployeeController::class, 'getSupervisorForPosition']);
    Route::get('/human-resources/positions-by-department', [EmployeeController::class, 'getPositions']);
    Route::get('/ceo', [EmployeeController::class, 'getCEO']);

    //////////////////////////////// OVERTIME ///////////////////////////////////////////
    Route::get('/human-resources/overtimes', [HR_Controller::class, 'otMngmnt'])->name('hr.ot-app');
    Route::get('/employee/overtimes/{id}', [HR_Controller::class, 'otApplication'])->name('hr.ot-application');
    Route::get('/employee/overtimes/requests/list/{id}', [OvertimeController::class, 'getUserReq']);
    Route::post('/employee/overtimes/request/submit', [OvertimeController::class, 'submitReq']);
    Route::get('/human-resources/overtimes/get', [OvertimeController::class, 'index']);
    Route::post('/human-resources/overtime/approve/{overtime_id}', [OvertimeController::class, 'approve']);
    Route::post('/human-resources/overtime/reject/{overtime_id}', [OvertimeController::class, 'reject']);

    ///////////////////////////////// LEAVE ////////////////////////////////////////////
    Route::get('/human-resources/leaves', [HR_Controller::class, 'leaveMngmnt'])->name('hr.leave-app');
    Route::get('/employee/leaves/{id}', [HR_Controller::class, 'leaveApplication'])->name('hr.leave-application');
    Route::get('/employee/leaves/requests/list/{id}', [LeaveController::class, 'getUserReq']);
    Route::post('/employee/leaves/request/submit', [LeaveController::class, 'submitReq']);
    Route::get('/human-resources/leaves/get', [LeaveController::class, 'index']);
    Route::post('/human-resources/leave/approve/{leave_id}', [LeaveController::class, 'approve']);
    Route::post('/human-resources/leave/reject/{leave_id}', [LeaveController::class, 'reject']);

    ///////////////////////////////// PAYROLL ///////////////////////////////////////////////
    Route::get('/human-resources/payroll', [PayrollController::class, 'indexOnHr'])->name('hr.payroll');
    Route::get('/human-resources/payroll/list', [PayrollController::class, 'getPayrollList']);
    Route::get('/human-resources/payroll/view/{id}', [PayrollController::class, 'getPayrollview']);
    Route::put('/human-resources/payroll/release/{id}', [PayrollController::class, 'releasePayroll']);
    Route::post('/human-resources/payroll/generate', [PayrollController::class, 'generatePayroll'])->name('hr.payroll.generate');
});
