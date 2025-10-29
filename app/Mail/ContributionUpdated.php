<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContributionUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $contributions;

    public function __construct(array $contributions)
    {
        $this->contributions = $contributions;
    }

    public function build()
    {
        return $this->subject('Update on Mandatory Contributions')
                    ->markdown('emails.contribution-updated');
    }
}
