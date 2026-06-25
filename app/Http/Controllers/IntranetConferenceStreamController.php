<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IntranetConferenceStreamController extends Controller
{
    public function edit(int $id)
    {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response('Conferencia no encontrada', 404);
        }

        return view('intranet.conferences.stream-edit', [
            'conference' => $conference,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response('Conferencia no encontrada', 404);
        }

        $validator = Validator::make($request->all(), [
            'youtube_link' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $videoId = $this->extractVideoId($request->input('youtube_link'));

        if (!$videoId) {
            return back()
                ->withErrors(['youtube_link' => 'Link de YouTube inválido. Pegá un link de YouTube válido o el ID del video.'])
                ->withInput();
        }

        $conference->youtube_id = $videoId;
        $conference->save();

        return redirect()
            ->route('intranet.conferences.stream.edit', ['id' => $conference->id])
            ->with('status', 'Transmisión guardada correctamente.');
    }

    public function stop(Request $request, int $id)
    {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response('Conferencia no encontrada', 404);
        }

        $conference->youtube_id = null;
        $conference->save();

        return redirect()
            ->route('intranet.conferences.stream.edit', ['id' => $conference->id])
            ->with('status', 'Transmisión detenida.');
    }

    protected function extractVideoId(string $url): ?string
    {
        if (preg_match('/youtu\.be\/(\w+)/', $url, $matches)) {
            return $matches[1];
        }
        if (preg_match('/[?&]v=([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return $matches[1];
        }
        if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return $matches[1];
        }
        if (preg_match('/youtube\.com\/live\/([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return $matches[1];
        }
        if (preg_match('/^([a-zA-Z0-9_-]{11})$/', trim($url), $matches)) {
            return $matches[1];
        }

        return null;
    }
}
