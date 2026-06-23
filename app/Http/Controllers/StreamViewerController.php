<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use Illuminate\Http\Request;

class StreamViewerController extends Controller
{
    public function show(int $id)
    {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response('Conferencia no encontrada', 404);
        }

        return view('conferences.stream', [
            'conference' => $conference,
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
}
