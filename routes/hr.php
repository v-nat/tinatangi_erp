<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HR\HR_Controller;
use App\Http\Controllers\Admin\HR\LeaveController;
use App\Http\Controllers\Admin\HR\PayrollController;
use App\Http\Controllers\Admin\HR\EmployeeController;
use App\Http\Controllers\Admin\HR\OvertimeController;
use App\Http\Controllers\Admin\HR\AttendanceController;
use App\Http\Controllers\Admin\HR\DepartmentController;
use App\Http\Controllers\Admin\HR\ScheduleController;
use App\Http\Controllers\Admin\HR\PayrollSettingsController;

/////////////////////////////////// EMPLOYEE ATTENDANCE /////////////////////////////////////////////
Route::prefix('/attendance')->middleware(['auth'])->group(function () {
    Route::get('/this-day', [AttendanceController::class, 'thisDay']);
    Route::get('/isOnLeave', [AttendanceController::class, 'isOnLeave']);
    Route::post('/time-in', [AttendanceController::class, 'timeIn']);
    Route::post('/time-out', [AttendanceController::class, 'timeOut']);
    Route::get('/employee-attendance-list/{id}', [AttendanceController::class, 'employeeAttendanceList'])->name('employee.attendance.list');
    Route::get('/get-employee-attendance-list/{id}', [AttendanceController::class, 'getEmployeeAttendanceList']);
});

////////////////////////////////// GENERAL EMPLOYEE ///////////////////////////////////////////////////
Route::prefix('/employee')->middleware(['auth', 'isEmployee'])->group(function () {
    ////////////////////////////////// OVERTIME ////////////////////////////////////
    Route::get('/overtimes/{id}', [HR_Controller::class, 'otApplication'])->name('hr.ot-application');
    Route::get('/overtimes/requests/list/{id}', [OvertimeController::class, 'getUserReq']);
    Route::post('/overtimes/request/submit', [OvertimeController::class, 'submitReq']);

    ////////////////////////////////// LEAVE ///////////////////////////////////////////
    Route::get('/leaves/{id}', [HR_Controller::class, 'leaveApplication'])->name('hr.leave-application');
    Route::get('/leaves/requests/list/{id}', [LeaveController::class, 'getUserReq']);
    Route::post('/leaves/request/submit', [LeaveController::class, 'submitReq']);

    ///////////////////////////////// PAYSLIP ///////////////////////////////////////
    Route::get('/payslip/{id}', [HR_Controller::class, 'employeePayslip'])->name('hr.payslip');
    Route::get('/payslip/list/{id}', [PayrollController::class, 'getPayslipList']);
    Route::get('/payslip/data/{id}', [PayrollController::class, 'getPayrollview']);

    ///////////////////////////////// SCHEDULE ///////////////////////////////////////
    Route::get('/schedule/{id}', [ScheduleController::class, 'viewEmployeeSchedule'])->name('hr.employee-schedule');
    Route::get('/schedule/events/{id}', [ScheduleController::class, 'getEmployeeScheduleEvents'])->name('hr.employee-schedule.events');
});


//////////////////////////////////////// HR ///////////////////////////////////////
Route::get('/human-resources', [HR_Controller::class, 'index'])->middleware('auth', 'isEmployee')->name('hr.dashboard');

Route::prefix('/human-resources')->middleware(['auth', 'isEmployee'])->group(function () {
    /////////////////////////// ATTENDANCE ///////////////////////////////////////
    Route::get('/attendance/list', [AttendanceController::class, 'attendanceList']);

    ///////////////////////////////////// DASHBOARD ////////////////////////////////////////////////
    Route::get('/schedules', [HR_Controller::class, 'getSchedules'])->name('hr.schedules');

    ////////////////////////////////// EMPLOYEE MANAGEMENT //////////////////////////
    Route::get('/employees', [HR_Controller::class, 'employees'])->name('hr.employees');
    Route::get('/employees/get', [HR_Controller::class, 'getEmployees']);
    Route::post('/store-employee', [EmployeeController::class, 'storeEmployee']);
    Route::get('/edit-employee/{id}', [EmployeeController::class, 'editEmployee'])->name('edit.employee');
    Route::put('/update-employee/{id}', [EmployeeController::class, 'updateEmployee'])->name('update.employee');

    Route::get('/schedules/manage', [ScheduleController::class, 'manage'])->name('hr.schedules.manage');
    Route::get('/schedules/manage/employees', [ScheduleController::class, 'employeesWithSchedules']);
    Route::get('/schedules/manage/employees/{employee}', [ScheduleController::class, 'showEmployeeSchedule']);
    Route::post('/schedules/manage/employees/{employee}', [ScheduleController::class, 'upsertEmployeeSchedule']);
    Route::delete('/schedules/manage/schedule/{schedule}', [ScheduleController::class, 'destroySchedule'])->whereNumber('schedule');

    Route::get('/manage', [EmployeeController::class, 'manage'])->name('hr.manage');
    Route::get('/supervisors-by-department-and-position', [EmployeeController::class, 'getSupervisorForPosition']);
    Route::get('/positions-by-department', [EmployeeController::class, 'getPositions']);

    ////////////////////////////////// PAYROLL SETTINGS AND POSITIONS SALARIES //////////////////////////
    Route::get('/get-payroll-settings', [EmployeeController::class, 'getPayrollSettings']);
    Route::get('/get-salary-by-position', [EmployeeController::class, 'getSalaryByPosition']);
    Route::get('/get-salary-settings', [EmployeeController::class, 'getSalarySettings']);
    Route::post('/update-payroll-settings', [PayrollSettingsController::class, 'updatePayrollSettings']);
    Route::get('/get-salary-setting/{id}', [PayrollSettingsController::class, 'getSingleSalarySetting']);
    Route::put('/update-salary-setting/{id}', [PayrollSettingsController::class, 'updateSalarySetting']);
    Route::get('/departments/list', [DepartmentController::class, 'getDepartmentList']);
    Route::post('/store-position-and-salary', [PayrollSettingsController::class, 'storePositionAndSalary']);
    Route::get('/ceo', [EmployeeController::class, 'getCEO']);

    //////////////////////////////// OVERTIME ///////////////////////////////////////////
    Route::get('/overtimes', [HR_Controller::class, 'otMngmnt'])->name('hr.ot-app');
    Route::get('/overtimes/get', [OvertimeController::class, 'index']);
    Route::post('/overtime/approve/{overtime_id}', [OvertimeController::class, 'approve']);
    Route::post('/overtime/reject/{overtime_id}', [OvertimeController::class, 'reject']);

    ///////////////////////////////// LEAVE ////////////////////////////////////////////
    Route::get('/leaves', [HR_Controller::class, 'leaveMngmnt'])->name('hr.leave-app');
    Route::get('/leaves/get', [LeaveController::class, 'index']);
    Route::post('/leave/approve/{leave_id}', [LeaveController::class, 'approve']);
    Route::post('/leave/reject/{leave_id}', [LeaveController::class, 'reject']);

    ///////////////////////////////// PAYROLL ///////////////////////////////////////////////
    Route::get('/payroll', [PayrollController::class, 'indexOnHr'])->name('hr.payroll');
    Route::get('/payroll/list', [PayrollController::class, 'getPayrollList']);
    Route::get('/payroll/view/{id}', [PayrollController::class, 'getPayrollview']);
    Route::put('/payroll/release/{id}', [PayrollController::class, 'releasePayroll']);
    Route::post('/payroll/generate', [PayrollController::class, 'generatePayroll'])->name('hr.payroll.generate');
    Route::post('/payroll/batch-generate', [PayrollController::class, 'generateBatchPayroll']);
});
