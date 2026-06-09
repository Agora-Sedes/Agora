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
        public readonly string $token
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
        //TODO: fetch money paid and people amount from database once DB issue is done
        return new Content(
            view: 'emails.verify-assistance',
            with: [
                'qrCode' => $this->qrCode,
                'verificationUrl' => $this->verificationUrl,
                'moneyPaid' => 12000,
                'peopleAmount' => 250
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
