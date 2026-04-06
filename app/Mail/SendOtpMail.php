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
    public $type; // 'register' atau 'reset'

    /**
     * @param int    $otp
     * @param string $type  'register' | 'reset'
     */
    public function __construct($otp, string $type = 'reset')
    {
        $this->otp  = $otp;
        $this->type = $type;
    }

    /**
     * Subject email dinamis sesuai tipe.
     */
    public function envelope(): Envelope
    {
        $subject = $this->type === 'register'
            ? 'Kode OTP Verifikasi Email - Perpustakaan Sekolah'
            : 'Kode OTP Reset Password - Perpustakaan Sekolah';

        return new Envelope(subject: $subject);
    }

    /**
     * Tampilkan view email dengan data tipe.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: [
                'otp'  => $this->otp,
                'type' => $this->type,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}