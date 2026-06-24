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

class VerifyPaymentMail extends Mailable
{
    use Queueable, SerializesModels;

    public readonly string $verificationPayload;
    public readonly string $qrCode;

    public function __construct(public readonly Attendant $attendant)
    {
        $this->verificationPayload = Crypt::encryptString(json_encode([
            'userId' => $this->attendant->id,
            'conferenceId' => $this->attendant->conference_id,
            'paymentId' => $this->attendant->payment_id,
        ], JSON_UNESCAPED_UNICODE));

        $this->qrCode = base64_encode(
            QrCode::format('png')
                ->size(250)
                ->generate($this->verificationPayload)
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
                'verificationPayload' => $this->verificationPayload,
                'attendant' => $this->attendant,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
