<?php

namespace App\Http\Controllers;

use App\Models\Attendant;
use App\Models\Conference;
use App\Models\StreamSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StreamViewerController extends Controller
{
    /** Duración de la cookie del dispositivo, en minutos (1 día). */
    private const DEVICE_COOKIE_MINUTES = 60 * 24;

    public function show(int $id, string $token, Request $request)
    {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response('Conferencia no encontrada', 404);
        }

        $attendant = $this->findAttendant($id, $token);

        if (is_null($attendant)) {
            return response('No encontramos tu inscripción para esta transmisión.', 403);
        }

        // Sin pago confirmado (is_draft) no puede acceder a la transmisión.
        if ($attendant->is_draft) {
            return response('Tu pago todavía está pendiente. Una vez confirmado vas a poder acceder a la transmisión.', 403);
        }

        $deviceToken = $request->cookie($this->deviceCookieName($id));
        $newToken = Str::random(40);

        // Tomamos la sesión de forma atómica: si no existía la creamos con nuestro
        // token; si ya existía, firstOrCreate la devuelve sin pisarla. Así dos
        // pestañas simultáneas en el primer ingreso no violan el unique(attendant_id).
        $session = StreamSession::firstOrCreate(
            ['attendant_id' => $attendant->id],
            ['conference_id' => $conference->id, 'session_token' => $newToken],
        );

        // La sesión es nuestra: la acabamos de crear, o es el mismo dispositivo.
        if ($session->wasRecentlyCreated) {
            return $this->streamResponse($conference, $token, 'active', $newToken);
        }

        if ($deviceToken && hash_equals($session->session_token, $deviceToken)) {
            return $this->streamResponse($conference, $token, 'active', $deviceToken);
        }

        // Hay una sesión activa en otro dispositivo: pedimos confirmación para reemplazarla.
        return $this->streamResponse($conference, $token, 'conflict', null);
    }

    public function replaceSession(int $id, string $token)
    {
        $attendant = $this->findAttendant($id, $token);

        if (is_null($attendant)) {
            return response('No encontramos tu inscripción para esta transmisión.', 403);
        }

        // Sin pago confirmado (is_draft) no puede tomar la sesión de la transmisión.
        if ($attendant->is_draft) {
            return response('Tu pago todavía está pendiente. Una vez confirmado vas a poder acceder a la transmisión.', 403);
        }

        StreamSession::where('attendant_id', $attendant->id)->delete();
        $deviceToken = $this->openSession($attendant, $id);

        return redirect()
            ->route('conferences.stream', ['id' => $id, 'token' => $token])
            ->withCookie(cookie($this->deviceCookieName($id), $deviceToken, self::DEVICE_COOKIE_MINUTES));
    }

    public function checkSession(int $id, string $token, Request $request)
    {
        $attendant = $this->findAttendant($id, $token);

        if (is_null($attendant) || $attendant->is_draft) {
            return response()->json(['active' => false], 403);
        }

        $deviceToken = $request->cookie($this->deviceCookieName($id));

        $isActive = StreamSession::where('attendant_id', $attendant->id)
            ->where('session_token', $deviceToken)
            ->exists();

        return response()->json(['active' => $isActive]);
    }

    /**
     * Previsualización del stream para el admin (intranet). Renderiza la misma
     * vista pero sin gating de sesión única ni polling, para no tocar las
     * sesiones de los asistentes.
     */
    public function preview(int $id)
    {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response('Conferencia no encontrada', 404);
        }

        return view('conferences.stream', [
            'conference' => $conference,
            'streamToken' => null,
            'sessionState' => 'preview',
        ]);
    }

    public function apiVideoId(int $id)
    {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response()->json([
                'error' => 'Conferencia no encontrada',
            ], 404);
        }

        return response()->json([
            'youtube_id' => $conference->youtube_id,
        ]);
    }

    /**
     * Busca al asistente de la conferencia cuyo md5(government_id) coincide con el token.
     * Si hay varios con el mismo DNI (p. ej. una inscripción vieja en borrador y otra
     * confirmada), preferimos la confirmada (is_draft = false primero).
     */
    private function findAttendant(int $conferenceId, string $token): ?Attendant
    {
        return Attendant::where('conference_id', $conferenceId)
            ->whereRaw('MD5(government_id) = ?', [$token])
            ->orderBy('is_draft')
            ->first();
    }

    /** Crea la sesión activa para el asistente y devuelve el token de dispositivo generado. */
    private function openSession(Attendant $attendant, int $conferenceId): string
    {
        $deviceToken = Str::random(40);

        StreamSession::create([
            'attendant_id' => $attendant->id,
            'conference_id' => $conferenceId,
            'session_token' => $deviceToken,
        ]);

        return $deviceToken;
    }

    /** Nombre de la cookie del dispositivo, propio de cada conferencia. */
    private function deviceCookieName(int $conferenceId): string
    {
        return 'stream_session_token_' . $conferenceId;
    }

    /**
     * Renderiza la vista del stream. Cuando recibimos un device token nuevo
     * (sesión recién abierta) lo adjuntamos como cookie en la respuesta.
     */
    private function streamResponse(Conference $conference, string $token, string $sessionState, ?string $deviceToken)
    {
        $response = response()->view('conferences.stream', [
            'conference' => $conference,
            'streamToken' => $token,
            'sessionState' => $sessionState,
        ]);

        if (! is_null($deviceToken)) {
            $response->withCookie(cookie($this->deviceCookieName($conference->id), $deviceToken, self::DEVICE_COOKIE_MINUTES));
        }

        return $response;
    }
}
