<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPassCodeMail extends Mailable implements ShouldQueue 
{
    use Queueable, SerializesModels;

    
    public $name, $subject, $passcode;
  
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject,$name,$passcode)
    {
        $this->subject  = $subject;
        $this->name     = $name;
        $this->passcode = $passcode;

    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.send-passcode-mail', [
                'name'  => $this->name,
                'passcode' => $this->passcode
            ])->subject($this->subject);
    }
}
