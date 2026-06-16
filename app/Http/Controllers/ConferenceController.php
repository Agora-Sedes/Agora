<?php

namespace App\Http\Controllers;

use App\Models\Conference;

class ConferenceController extends Controller
{
    public function list()
    {
        $conferences = Conference::all()->sortByDesc('starts_at');
        return view('conferences.list', [
            'conferences' => $conferences,
        ]);
    }

    public function show(int $id)
    {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response('Conferencia no encontrada', 404);
        }

        $talks = $conference->talks->sortBy('starts_at');

        return view('conferences.show', [
            'conference' => $conference,
            'talks' => $talks,
        ]);
    }
}
