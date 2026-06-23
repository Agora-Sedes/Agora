<?php

namespace App\Http\Controllers;

use App\Models\Attendant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Conference;

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

        $code = trim((string) $request->input('code'));
        $decoded = $this->normalizeQrCode($code);

        $attendant = Attendant::query()
            ->where(function ($query) use ($decoded, $code) {
                $query->where('id', $decoded)
                    ->orWhere('government_id', $decoded)
                    ->orWhere('email', $decoded)
                    ->orWhere('id', $code)
                    ->orWhere('government_id', $code)
                    ->orWhere('email', $code);
            })
            ->first();

        if (! $attendant) {
            return response()->json([
                'found' => false,
                'message' => 'No se encontró un inscripto con ese código.',
            ], 404);
        }

        if ((int) $attendant->conference_id !== $id) {
            return response()->json([
                'found' => false,
                'wrong_conference' => true,
                'message' => 'La persona pertenece a otra conferencia.',
                'attendant' => [
                    'id' => $attendant->id,
                    'full_name' => $attendant->full_name,
                    'government_id' => $attendant->government_id,
                    'email' => $attendant->email,
                    'phone_number' => $attendant->phone_number,
                    'conference_id' => $attendant->conference_id,
                    'was_present' => (bool) $attendant->was_present,
                ],
            ], 409);
        }

        return response()->json([
            'found' => true,
            'attendant' => [
                'id' => $attendant->id,
                'full_name' => $attendant->full_name,
                'government_id' => $attendant->government_id,
                'email' => $attendant->email,
                'phone_number' => $attendant->phone_number,
                'conference_id' => $attendant->conference_id,
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

    private function normalizeQrCode(string $code): string
    {
        if (filter_var($code, FILTER_VALIDATE_URL)) {
            $path = parse_url($code, PHP_URL_PATH) ?: $code;
            $segments = array_values(array_filter(explode('/', $path)));

            return (string) (end($segments) ?: $code);
        }

        return Str::of($code)->trim()->toString();
    }
}
