<?php

namespace App\Http\Controllers;

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
        return view('intranet.conferences.qr-scan');
    }
}
