<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VerifyPaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    public readonly string $verificationUrl;
    public readonly string $qrCode;

    public function __construct(public readonly string $inscriptionId)
    {
        $this->verificationUrl =
            env('AGORA_URL') . "/verify-payment/irl/{$this->inscriptionId}";

        $this->qrCode = base64_encode(
            QrCode::format('png')
                ->size(250)
                ->generate($this->verificationUrl)
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Verificá tu pago - Jornada Ágora');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verify-payment',
            with: [
                'qrCode' => $this->qrCode,
                'verificationUrl' => $this->verificationUrl,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
