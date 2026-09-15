<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendInviteEventMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name, $subject, $email,$custom_message;
    public $eventDetail = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $subject, $email,$custom_message,$eventDetail)
    {
        $this->name = $name;
        $this->subject = $subject;
        $this->email = $email;
        $this->custom_message = $custom_message;
        $this->eventDetail = $eventDetail;
    }

    public function build()
    {
        return $this->markdown('emails.send-invite-event-volunteer-mail', [
                'name'  => $this->name,
                'email' => $this->email,
                'custom_message' => $this->custom_message,
                'eventDetail' => $this->eventDetail,
            ])->subject($this->subject);
    }
}
