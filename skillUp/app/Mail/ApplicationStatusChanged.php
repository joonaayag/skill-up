<?php
namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $customMessage;

    public function __construct(Application $application, string $customMessage)
    {
        $this->application = $application;
        $this->customMessage = $customMessage;
    }

    public function build()
    {
        return $this->subject('Estado de tu candidatura actualizado')
            ->view('emails.application_status_changed');
    }
}

