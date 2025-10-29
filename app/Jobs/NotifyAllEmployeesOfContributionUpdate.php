<?php
namespace App\Jobs;

use App\Mail\ContributionUpdated;
use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyAllEmployeesOfContributionUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $contributions;

    public function __construct(array $contributions)
    {
        $this->contributions = $contributions;
    }

    public function handle()
    {
        // Chunk through employees to avoid memory issues with large numbers
        Employee::with('user:id,email')->chunk(100, function ($employees) {
            foreach ($employees as $employee) {
                if ($employee->user && $employee->user->email) {
                    Mail::to($employee->user->email)->send(new ContributionUpdated($this->contributions));
                }
            }
        });
    }
}
