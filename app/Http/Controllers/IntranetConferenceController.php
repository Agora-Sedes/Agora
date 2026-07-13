<?php

namespace App\Http\Controllers;

use App\Models\Attendant;
use App\Models\Conference;
use App\Models\Talk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class IntranetConferenceController extends Controller
{
    public function list()
    {
        $conferences = Conference::withCount('talks')->orderByDesc('starts_at')->get();

        return view('intranet.conferences.list', [
            'conferences' => $conferences,
        ]);
    }

    public function new()
    {
        $conference = new Conference();
        $conference->price = 12000;

        return view('intranet.conferences.new', [
            'conference' => $conference,
            'talks' => collect([new Talk()]),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateConferencePayload($request);

        $conference = DB::transaction(function () use ($data) {
            $conference = Conference::create($data['conference']);

            foreach ($data['talks'] as $talkData) {
                $conference->talks()->create($talkData);
            }

            return $conference;
        });

        return redirect()->route('intranet.conferences.dashboard', ['id' => $conference->id]);
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

    public function edit(int $id)
    {
        $conference = Conference::with('talks')->find($id);

        if (is_null($conference)) {
            return response('Conferencia no encontrada', 404);
        }

        return view('intranet.conferences.new', [
            'conference' => $conference,
            'talks' => $conference->talks->sortBy('starts_at')->values(),
            'mode' => 'edit',
        ]);
    }

    public function update(int $id, Request $request)
    {
        $conference = Conference::with('talks')->find($id);

        if (is_null($conference)) {
            return response('Conferencia no encontrada', 404);
        }

        $data = $this->validateConferencePayload($request);

        DB::transaction(function () use ($conference, $data) {
            $conference->update($data['conference']);

            $conference->talks()->delete();

            foreach ($data['talks'] as $talkData) {
                $conference->talks()->create($talkData);
            }
        });

        return redirect()->route('intranet.conferences.dashboard', ['id' => $conference->id]);
    }

    public function delete(int $id)
    {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response('Conferencia no encontrada', 404);
        }

        DB::transaction(function () use ($conference) {
            $conference->talks()->delete();
            $conference->attendants()->delete();
            $conference->delete();
        });

        return redirect()->route('intranet.conferences.list');
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

    private function validateConferencePayload(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after_or_equal:starts_at',
            'price' => 'required|numeric|min:0',
            'talks' => 'nullable|array',
            'talks.*.title' => 'required_with:talks|string|max:255',
            'talks.*.description' => 'required_with:talks|string',
            'talks.*.starts_at' => 'required_with:talks|date',
            'talks.*.ends_at' => 'required_with:talks|date',
            'talks.*.speaker' => 'required_with:talks|string|max:255',
            'talks.*.speaker_background' => 'required_with:talks|string',
        ]);

        $conference = [
            'title' => $data['title'],
            'description' => $data['description'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'price' => $data['price'],
        ];

        $talks = collect($data['talks'] ?? [])
            ->filter(fn (array $talk) => !empty(trim($talk['title'] ?? '')))
            ->values()
            ->map(fn (array $talk) => [
                'title' => $talk['title'],
                'description' => $talk['description'],
                'starts_at' => $talk['starts_at'],
                'ends_at' => $talk['ends_at'],
                'speaker' => $talk['speaker'],
                'speaker_background' => $talk['speaker_background'],
            ])
            ->all();

        return [
            'conference' => $conference,
            'talks' => $talks,
        ];
    }
}
