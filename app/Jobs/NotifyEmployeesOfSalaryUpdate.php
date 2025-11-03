<?php
namespace App\Jobs;

use App\Mail\SalaryUpdated;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyEmployeesOfSalaryUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employeeIds;
    protected $newBaseSalary;
    protected $positionName;

    public function __construct($employeeIds, $newBaseSalary, $positionName)
    {
        $this->employeeIds = $employeeIds;
        $this->newBaseSalary = $newBaseSalary;
        $this->positionName = $positionName;
    }

    public function handle()
    {
        $employees = Employee::with('user:id,first_name,email')->whereIn('id', $this->employeeIds)->get();

        foreach ($employees as $employee) {
            if ($employee->user && $employee->user->email) {
                Mail::to($employee->user->email)->send(new SalaryUpdated(
                    $employee->user->first_name,
                    $this->positionName,
                    $this->newBaseSalary
                ));
            }
        }
    }
}
