<?php

namespace App\Http\Controllers\Admin\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    // VARIABLES 

    const SSS = 600;
    const PHILHEALTH = 450;
    const PAGIBIG = 100;    

    const WORKING_DAYS_PER_MONTH = 26;
    const WORKING_HOURS_PER_DAY = 8;
    const OVERTIME_RATE_MULTIPLIER = 1.25;

    














    // Functions

    public function indexOnHr(){
        return view("pages.admin.human_resources.payroll");
    }
    //
}
