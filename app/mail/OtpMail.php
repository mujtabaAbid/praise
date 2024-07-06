<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class OtpMail extends Mailable
{

    public $details;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // dd($this->details);
        return $this->from(config('mail.mailers.smtp2.username'), config('mail.from.name'))->subject('Your OTP Code : '.$this->details)
        ->view('mail_templates.otp_mail')->with('details', $this->details);
    }
}