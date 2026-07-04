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

        $maxOrder = Question::where('conference_id', $id)->max('sort_order') ?? 0;

        $question = Question::create([
            'conference_id' => $id,
            'body' => trim($validated['body']),
            'status' => 'pending',
            'sort_order' => $maxOrder + 1,
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
            ->orderByRaw('is_pinned DESC, sort_order ASC, created_at DESC')
            ->get(['id', 'body', 'status', 'is_pinned', 'sort_order', 'created_at']);

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
            'is_pinned' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if (isset($validated['status'])) {
            $question->status = $validated['status'];
        }
        if (isset($validated['body'])) {
            $question->body = trim($validated['body']);
        }
        if (isset($validated['is_pinned'])) {
            $question->is_pinned = $validated['is_pinned'];
        }
        if (isset($validated['sort_order'])) {
            $question->sort_order = $validated['sort_order'];
        }

        $question->save();

        return response()->json($question->only(['id', 'body', 'status', 'is_pinned', 'sort_order', 'created_at']));
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
            'questions' => 'required|array',
            'questions.*.id' => 'required|integer|exists:questions,id',
            'questions.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['questions'] as $item) {
            Question::where('conference_id', $id)
                ->where('id', $item['id'])
                ->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['message' => 'Orden actualizado']);
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
