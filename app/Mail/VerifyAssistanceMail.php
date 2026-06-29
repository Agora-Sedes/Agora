<?php

namespace App\Mail;

use App\Models\Attendant;
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
    public readonly bool $isVirtual;
    public readonly string $conferenceUrl;

    public function __construct(
        public readonly Attendant $attendant
    ) {
        $token = md5($attendant->government_id);
        $this->verificationUrl =
            env('APP_URL') . "/verify-attendance/{$token}";

        $this->qrCode = base64_encode(
            QrCode::format('png')
                ->size(250)
                ->generate($this->verificationUrl)
        );

        // Si el asistente se inscribió como virtual, incluimos el link a la conferencia.
        $this->isVirtual = $attendant->mode === 'online';
        $this->conferenceUrl = route('conferences.stream', ['id' => $attendant->conference_id]);
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
                'isVirtual' => $this->isVirtual,
                'conferenceUrl' => $this->conferenceUrl,
                'moneyPaid' => 12000,
                'peopleAmount' => 250,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
