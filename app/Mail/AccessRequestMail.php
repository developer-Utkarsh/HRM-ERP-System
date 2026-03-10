<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccessRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $softwareName;
    public $softwareOwnerName;
    public $employeeName;
    public $deeplinkUrl;

    public function __construct($softwareName, $softwareOwnerName, $employeeName, $deeplinkUrl)
    {
        $this->softwareName = $softwareName;
        $this->softwareOwnerName = $softwareOwnerName;
        $this->employeeName = $employeeName;
        $this->deeplinkUrl = $deeplinkUrl;
    }

    public function build()
    {
        return $this->subject("New Access Request for {$this->softwareName}")->view('emails.create_access_request');
    }
}
