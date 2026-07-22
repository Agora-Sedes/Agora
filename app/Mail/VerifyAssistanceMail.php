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
    public readonly bool $isOnline;
    public readonly string $conferenceUrl;

    public function __construct(
        public readonly Attendant $attendant
    ) {
        $token = md5($attendant->government_id);

        $this->verificationPayload = Crypt::encryptString(json_encode([
            'userId' => $attendant->id,
            'conferenceId' => $attendant->conference_id,
            'paymentId' => $attendant->payment_id,
        ], JSON_UNESCAPED_UNICODE));

        $this->qrCode = base64_encode(
            QrCode::format('png')
                ->size(250)
                ->generate($this->verificationPayload)
        );

        // Si el asistente se inscribió como virtual, incluimos el link a la conferencia.
        $this->isOnline = $attendant->mode === 'online';
        $this->conferenceUrl = route('conferences.stream', [
            'id' => $attendant->conference_id,
            'token' => $token,
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verificá tu asistencia - Ágora'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verify-assistance',
            with: [
                'qrCode' => $this->qrCode,
                'isOnline' => $this->isOnline,
                'conferenceUrl' => $this->conferenceUrl,
                'attendant' => $this->attendant,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
