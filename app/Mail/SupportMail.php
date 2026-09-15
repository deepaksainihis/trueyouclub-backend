<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userRequestDetails,$subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userRequestDetails,$subject)
    {
        $this->userRequestDetails = $userRequestDetails;
        $this->subject = $subject;
    }

   /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.auth.support-mail', [
            'logoUrl'=>asset(config('constants.default.email_logo')),
            'name' => $this->userRequestDetails['name'],
            'email' => $this->userRequestDetails['email'],
            'phone_number' => $this->userRequestDetails['phone_number'],
            'contact_preferance' => $this->userRequestDetails['contact_preferance'],
            'message' => $this->userRequestDetails['message'],
        ])->subject($this->subject);
    }
}
