<?php

namespace App\Http\Controllers;

use App\Models\Attendant;
use App\Models\Conference;
use Illuminate\Http\Request;

class IntranetConferenceAttendantController extends Controller
{
    public function list(int $id) {
        $conference = Conference::with('attendants')->find($id);

        if (is_null($conference)) {
            return response('Conferencia no encontrada', 404);
        }

        $attendants = $conference->attendants->sortBy('full_name');

        return view('intranet.conferences.attendants.list', [
            'conference' => $conference,
            'attendants' => $attendants,
        ]);
    }

    public function new(int $id) {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response('Conferencia no encontrada', 404);
        }

        return view('intranet.conferences.attendants.new', [
            'conference' => $conference,
        ]);
    }

    public function store(int $id, Request $request) {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response('Conferencia no encontrada', 404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'dni' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
        ]);

        Attendant::create([
            'conference_id' => $conference->id,
            'is_draft' => false,
            'was_present' => false,
            'government_id' => $data['dni'],
            'full_name' => trim($data['name'] . ' ' . $data['lastname']),
            'email' => $data['email'],
            'phone_number' => $data['phone'],
        ]);

        return redirect()->route('intranet.conferences.attendants.list', ['id' => $conference->id]);
    }

    public function edit(int $id, int $attendantId) {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response('Conferencia no encontrada', 404);
        }

        $attendant = Attendant::where('conference_id', $conference->id)->find($attendantId);

        if (is_null($attendant)) {
            return response('Inscripto no encontrado', 404);
        }

        return view('intranet.conferences.attendants.edit', [
            'conference' => $conference,
            'attendant' => $attendant,
        ]);
    }

    public function update(int $id, int $attendantId, Request $request) {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response('Conferencia no encontrada', 404);
        }

        $attendant = Attendant::where('conference_id', $conference->id)->find($attendantId);

        if (is_null($attendant)) {
            return response('Inscripto no encontrado', 404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'dni' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'is_draft' => 'nullable|boolean',
        ]);

        $attendant->update([
            'full_name' => trim($data['name'] . ' ' . $data['lastname']),
            'government_id' => $data['dni'],
            'email' => $data['email'],
            'phone_number' => $data['phone'],
            'is_draft' => (bool) ($data['is_draft'] ?? false),
        ]);

        return redirect()->route('intranet.conferences.attendants.list', ['id' => $conference->id]);
    }

    public function destroy(int $id, int $attendantId) {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response('Conferencia no encontrada', 404);
        }

        $attendant = Attendant::where('conference_id', $conference->id)->find($attendantId);

        if (is_null($attendant)) {
            return response('Inscripto no encontrado', 404);
        }

        $attendant->delete();

        return redirect()->route('intranet.conferences.attendants.list', ['id' => $conference->id]);
    }
}
