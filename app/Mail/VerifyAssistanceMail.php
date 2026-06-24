<?php

namespace App\Mail;

use App\Models\Attendant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class VerifyAssistanceMail extends Mailable
{
    use Queueable, SerializesModels;

    public readonly string $verificationPayload;
    public readonly string $qrCode;

    public function __construct(public readonly Attendant $attendant)
    {
        $this->verificationPayload = Crypt::encryptString(json_encode([
            'attendant_id' => $this->attendant->id,
            'payment_id' => $this->attendant->payment_id,
            'conference_id' => $this->attendant->conference_id,
        ], JSON_UNESCAPED_UNICODE));

        $this->qrCode = base64_encode(
            QrCode::format('png')
                ->size(250)
                ->generate($this->verificationPayload)
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
                'verificationPayload' => $this->verificationPayload,
                'moneyPaid' => 12000,
                'peopleAmount' => 250,
                'attendant' => $this->attendant,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
