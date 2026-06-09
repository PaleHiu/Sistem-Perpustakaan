<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public readonly string $otp,
        public readonly string $recipientEmail,
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode OTP Reset Password - SIPUS',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: $this->buildHtml(),
        );
    }

    /**
     * Build the HTML email body.
     */
    private function buildHtml(): string
    {
        return '
            <div style="font-family:Arial,sans-serif;max-width:500px;margin:0 auto;padding:30px;">
                <div style="background:#1f3c45;padding:20px;border-radius:10px 10px 0 0;text-align:center;">
                    <h2 style="color:#1fcf8e;margin:0;letter-spacing:2px;">SIPUS</h2>
                    <p style="color:white;margin:5px 0 0;font-size:12px;">Library Management System</p>
                </div>
                <div style="background:white;padding:30px;border:1px solid #edf2f7;border-radius:0 0 10px 10px;">
                    <h3 style="color:#2d3748;margin-bottom:10px;">Reset Password</h3>
                    <p style="color:#718096;font-size:14px;">Gunakan kode OTP berikut untuk mereset password kamu:</p>
                    <div style="background:#f0fff4;border:2px solid #1fcf8e;border-radius:10px;padding:20px;text-align:center;margin:20px 0;">
                        <h1 style="color:#1fcf8e;font-size:42px;letter-spacing:10px;margin:0;font-family:monospace;">' . e($this->otp) . '</h1>
                    </div>
                    <p style="color:#e53e3e;font-size:13px;text-align:center;">&#9888; Kode berlaku selama <strong>10 menit</strong></p>
                    <p style="color:#a0aec0;font-size:12px;margin-top:20px;">Jika kamu tidak meminta reset password, abaikan email ini.</p>
                </div>
            </div>
        ';
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
