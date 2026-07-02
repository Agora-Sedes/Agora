<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\Talk;
use Illuminate\Http\Request;
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
        return view('intranet.conferences.new', [
            'conference' => new Conference(),
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
        // TODO: Database integration
        return view('intranet.conferences.qr-scan');
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
