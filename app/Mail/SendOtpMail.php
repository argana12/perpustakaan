<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    /**
     * Create a new message instance.
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Email subject
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode OTP Reset Password'
        );
    }

    /**
     * Email content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.password-otp',
            with: [
                'otp' => $this->otp
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}