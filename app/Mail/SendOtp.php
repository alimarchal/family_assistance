<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOtp extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subject;
    public $description;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $subject = null, $description = null)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->description = $description;
        if (!empty($this->subject)) {
            $this->subject($subject);
        } else {
            $this->subject('One Time Password (OTP)');
        }
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.sent.otp');
    }
}
