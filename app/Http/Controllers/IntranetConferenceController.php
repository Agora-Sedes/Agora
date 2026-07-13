<?php

namespace App\Http\Controllers;

use App\Models\Attendant;
use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class IntranetConferenceController extends Controller
{
    public function list()
    {
        $conferences = Conference::all()->sortByDesc('starts_at');

        return view('intranet.conferences.list', [
            'conferences' => $conferences,
        ]);
    }

    public function dashboard(int $id)
    {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response('Conferencia no encontrada', 404);
        }

        return view('intranet.conferences.dashboard', [
            'conference' => $conference,
        ]);
    }

    public function qrScan(int $id)
    {
        return view('intranet.conferences.qr-scan', [
            'conferenceId' => $id,
        ]);
    }

    public function qrScanLookup(Request $request, int $id)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $payload = $this->decryptQrCode((string) $request->input('code'));
        if (! $payload) {
            return response()->json([
                'found' => false,
                'message' => 'No se pudo leer el QR.',
            ], 422);
        }

        $userId = $payload['userId'] ?? null;
        $conferenceId = $payload['conferenceId'] ?? null;
        $paymentId = $payload['paymentId'] ?? null;

        if (! $userId || ! $conferenceId || ! $paymentId) {
            return response()->json([
                'found' => false,
                'message' => 'El QR no contiene los datos esperados.',
            ], 422);
        }

        $attendant = Attendant::query()->find($userId);
        if (! $attendant) {
            return response()->json([
                'found' => false,
                'message' => 'No se encontró un inscripto con ese código.',
            ], 404);
        }

        $belongsToConference = $attendant->conference_id === $id;

        $conference = Conference::find($attendant->conference_id);
        $now = now();

        return response()->json([
            'found' => true,
            'belongs_to_conference' => $belongsToConference,
            'is_paid' => ! (bool) $attendant->is_draft,
            'is_event_day' => $conference && $now->between($conference->starts_at, $conference->ends_at),
            'attendant' => [
                'id' => $attendant->id,
                'full_name' => $attendant->full_name,
                'government_id' => $attendant->government_id,
                'email' => $attendant->email,
                'phone_number' => $attendant->phone_number,
                'conference_id' => $attendant->conference_id,
                'payment_id' => $attendant->payment_id,
                'is_draft' => (bool) $attendant->is_draft,
                'was_present' => (bool) $attendant->was_present,
            ],
            'conference' => $conference ? [
                'id' => $conference->id,
                'title' => $conference->title,
                'starts_at' => $conference->starts_at?->toISOString(),
                'ends_at' => $conference->ends_at?->toISOString(),
            ] : null,
        ]);
    }

    public function qrScanMarkPaid(Request $request, int $id)
    {
        $validated = $request->validate([
            'attendant_id' => ['required', 'integer'],
        ]);

        $attendant = Attendant::query()
            ->where('conference_id', $id)
            ->findOrFail($validated['attendant_id']);

        $attendant->is_draft = false;
        $attendant->save();

        return response()->json([
            'ok' => true,
            'message' => 'Pago confirmado.',
        ]);
    }

    public function qrScanConfirm(Request $request, int $id)
    {
        $validated = $request->validate([
            'attendant_id' => ['required', 'integer'],
        ]);

        $attendant = Attendant::query()
            ->where('conference_id', $id)
            ->findOrFail($validated['attendant_id']);

        $attendant->was_present = true;
        $attendant->save();

        return response()->json([
            'ok' => true,
            'message' => 'Asistencia confirmada.',
        ]);
    }

    private function decryptQrCode(string $code): ?array
    {
        try {
            $json = Crypt::decryptString(trim($code));
            $payload = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

            return is_array($payload) ? $payload : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
