<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VerifyAssistanceMail extends Mailable
{
    use Queueable, SerializesModels;

    public readonly string $verificationUrl;
    public readonly string $qrCode;

    public function __construct(
        public readonly string $token,
        public readonly float $unitPrice = 12000,
        public readonly int $peopleCount = 1,
    ) {
        $this->verificationUrl =
            env('APP_URL') . "/verify-attendance/{$this->token}";

        $this->qrCode = base64_encode(
            QrCode::format('png')
                ->size(250)
                ->generate($this->verificationUrl)
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verificá tu asistencia - Jornada Ágora'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verify-assistance',
            with: [
                'qrCode' => $this->qrCode,
                'verificationUrl' => $this->verificationUrl,
                'moneyPaid' => $this->unitPrice * $this->peopleCount,
                'peopleAmount' => $this->peopleCount,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
