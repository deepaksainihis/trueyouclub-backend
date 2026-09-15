<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReplySupportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject, $name,$replyMessage;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject,$name,$replyMessage)
    {
        $this->subject = $subject;
        $this->name = ucwords($name);
        $this->replyMessage = $replyMessage;
    }

   /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.reply-support-mail', [
            'logoUrl'=>asset(config('constants.default.admin_logo')),
            'name'=>$this->name,
            'replyMessage' => $this->replyMessage,
        ])->subject($this->subject);
    }
}
