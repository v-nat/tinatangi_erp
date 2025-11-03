<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SalaryUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $employeeName;
    public $positionName;
    public $newBaseSalary;

    public function __construct($employeeName, $positionName, $newBaseSalary)
    {
        $this->employeeName = $employeeName;
        $this->positionName = $positionName;
        $this->newBaseSalary = $newBaseSalary;
    }

    public function build()
    {
        return $this->subject('Update Regarding Your Salary')
                    ->markdown('emails.salary-updated');
    }
}
