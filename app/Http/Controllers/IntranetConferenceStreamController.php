<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
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

        return view('intranet.conferences.manage', [
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
            ->route('intranet.conferences.manage', ['id' => $conference->id])
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
            ->route('intranet.conferences.manage', ['id' => $conference->id])
            ->with('status', 'Transmisión detenida.');
    }

    // ---- Public: store question (viewer) ----

    public function storeQuestion(Request $request, int $id): JsonResponse
    {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response()->json(['error' => 'Conferencia no encontrada'], 404);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'min:3', 'max:280'],
        ]);

        $question = Question::create([
            'conference_id' => $id,
            'body' => trim($validated['body']),
            'status' => 'pending',
        ]);

        return response()->json($question->only(['id', 'body', 'status', 'created_at']), 201);
    }

    // ---- Admin: question management ----

    public function adminQuestions(int $id): JsonResponse
    {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response()->json(['error' => 'Conferencia no encontrada'], 404);
        }

        $questions = Question::query()
            ->where('conference_id', $id)
            ->ordered()
            ->get(['id', 'body', 'status', 'pinned', 'position', 'created_at']);

        return response()->json($questions);
    }

    public function updateQuestion(Request $request, int $id, int $qid): JsonResponse
    {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response()->json(['error' => 'Conferencia no encontrada'], 404);
        }

        $question = Question::where('conference_id', $id)->find($qid);

        if (is_null($question)) {
            return response()->json(['error' => 'Pregunta no encontrada'], 404);
        }

        $validated = $request->validate([
            'status' => ['nullable', 'in:pending,answered'],
            'body' => ['nullable', 'string', 'min:1', 'max:280'],
            'pinned' => ['nullable', 'boolean'],
        ]);

        if (isset($validated['status'])) {
            $question->status = $validated['status'];
        }
        if (isset($validated['body'])) {
            $question->body = trim($validated['body']);
        }
        if (isset($validated['pinned'])) {
            $question->pinned = $validated['pinned'];
        }

        $question->save();

        return response()->json($question->only(['id', 'body', 'status', 'pinned', 'position', 'created_at']));
    }

    public function destroyQuestion(int $id, int $qid): JsonResponse
    {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response()->json(['error' => 'Conferencia no encontrada'], 404);
        }

        $question = Question::where('conference_id', $id)->find($qid);

        if (is_null($question)) {
            return response()->json(['error' => 'Pregunta no encontrada'], 404);
        }

        $question->delete();

        return response()->json([], 204);
    }

    public function reorderQuestions(Request $request, int $id): JsonResponse
    {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response()->json(['error' => 'Conferencia no encontrada'], 404);
        }

        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:questions,id'],
        ]);

        foreach ($validated['order'] as $index => $qId) {
            Question::where('conference_id', $id)->where('id', $qId)->update(['position' => $index]);
        }

        return response()->json([], 204);
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
