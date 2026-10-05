<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCustomEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $sendEmailDataArr;

    // Constructor to pass data to the mailable
    public function __construct($sendEmailDataArr)
    {
        $this->sendEmailDataArr = $sendEmailDataArr;
    }

    // Build the email
    public function build()
    {
        $email = $this->subject($this->sendEmailDataArr['emailSubject'])
            ->view('emails.custom')
            ->with([
                    'emailContent' => $this->sendEmailDataArr['emailContent'],
                    'emailSubject' => $this->sendEmailDataArr['emailSubject']
                ]);

        if (!blank($this->sendEmailDataArr['cc'])) {
            $email->cc((array)$this->sendEmailDataArr['cc']);
        }
        if (!blank($this->sendEmailDataArr['bcc'])) {
            $email->bcc((array)$this->sendEmailDataArr['bcc']);
        }
        if (!blank($this->sendEmailDataArr['files'])) {
            foreach ((array)$this->sendEmailDataArr['files'] as $file) {
                $email->attach($file);
            }
        }
        return $email;
    }
}
