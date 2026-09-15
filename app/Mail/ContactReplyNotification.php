<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactReplyNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $name, $subject, $reply;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $subject, $reply)
    {
        $this->name = $name;
        $this->subject = $subject;
        $this->reply = $reply;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.reply-support-mail', [
            'name'  => $this->name,
            'reply' => $this->reply,
        ])->subject($this->subject);
    }
}
