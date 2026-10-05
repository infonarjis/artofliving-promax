<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;

class DynamicTemplateMail extends Mailable implements ShouldQueue
{
    protected $subjectText;
    protected $content;
    protected $ccList;
    protected $bccList;
    protected $filesList;

    public function __construct(
        string $subject,
        string $content,
        array $cc = [],
        array $bcc = [],
        array $files = []
    ) {
        $this->subjectText = $subject;
        $this->content     = $content;
        $this->ccList      = $cc;
        $this->bccList     = $bcc;
        $this->filesList   = $files;
    }

    public function build()
    {
        $mail = $this->subject($this->subjectText)
            ->view('emails.dynamic')
            ->with([
                'content' => $this->content,
            ]);

        if (!empty($this->ccList)) {
            $mail->cc($this->ccList);
        }

        if (!empty($this->bccList)) {
            $mail->bcc($this->bccList);
        }

        if (!empty($this->filesList)) {
            foreach ($this->filesList as $file) {
                $mail->attach($file);
            }
        }

        return $mail;
    }
}
