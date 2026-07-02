<?php

namespace App\Mail;

use App\Models\Attendant;
use App\Models\Conference;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AttendantCertificateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pdf = null;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public readonly Conference $conference,
        public readonly Attendant $attendant,
    ) {

        $htmlContent = view('emails.attendant-certificate-mail', [

        ])->render();

        try {
            $response = Http::timeout(120)->attach(
                'file', $htmlContent, 'certificate.html'
            )->post('http://pdf-converter:8080/pdf');
        } catch (Exception $exn) {
            Log::error("Falla interna (API PDF): {$exn->getMessage()}");
            return;
        }

        if (!$response->successful() || $response->header('Content-Type') !== 'application/pdf') {
            Log::error('Falla interna (API PDF): ', ['status' => $response->status(), 'body' => $response->body()]);
            return;
        }

        $this->pdf = Attachment::fromData(
            fn () => $response->body(),
            'CertificadoAsistencia.pdf'
        )->withMime('application/pdf');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Certificado de asistencia',
            to: $this->attendant->email
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: "<p>Buenas tardes, {$this->attendant->full_name}.</p><p>Gracias por participar en la jornada <b>\"{$this->conference->title}\"</b>. Adjuntamos el certificado de asistencia.</p><p>Atentamente, Instituto de Profesorado Sedes Sapientiae</p><small>Este correo fue generado automáticamente por <a href=\"https://github.com/Agora-Sedes/Agora\">Ágora</a>, por favor, no lo respondas.</small>"
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [$this->pdf];
    }
}
