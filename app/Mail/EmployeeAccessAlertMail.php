<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeAccessAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employeeName;
    public $accessList;
    public $ownerName;

    public function __construct($employeeName, $accessList, $ownerName)
    {
        $this->employeeName = $employeeName;
        $this->accessList = $accessList;
        $this->ownerName = $ownerName;
    }

    public function build()
    {
        return $this->subject("Access Alert: {$this->employeeName}")
            ->view('emails.employee_access_alert');
    }
}
