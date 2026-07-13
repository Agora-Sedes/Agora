<?php

namespace App\Http\Controllers;

use App\Models\Attendant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class IntranetConferenceController extends Controller
{
    public function list()
    {
        // TODO: Database integration
        return view('intranet.conferences.list');
    }

    public function dashboard(int $id)
    {
        // TODO: Database integration
        return view('intranet.conferences.dashboard');
    }

    public function qrScan(int $id)
    {
        // TODO: Database integration
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

        return response()->json([
            'found' => true,
            'belongs_to_conference' => $belongsToConference,
            'attendant' => [
                'id' => $attendant->id,
                'full_name' => $attendant->full_name,
                'government_id' => $attendant->government_id,
                'email' => $attendant->email,
                'phone_number' => $attendant->phone_number,
                'conference_id' => $attendant->conference_id,
                'payment_id' => $attendant->payment_id,
                'was_present' => (bool) $attendant->was_present,
            ],
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
