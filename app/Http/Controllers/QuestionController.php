<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(int $id): JsonResponse
    {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response()->json(['error' => 'Conferencia no encontrada'], 404);
        }

        $questions = Question::query()
            ->where('conference_id', $id)
            ->where('status', 'pending')
            ->latest()
            ->get(['id', 'body', 'status', 'created_at']);

        return response()->json($questions);
    }

    public function store(Request $request, int $id): JsonResponse
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

    public function adminIndex(int $id): JsonResponse
    {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response()->json(['error' => 'Conferencia no encontrada'], 404);
        }

        $questions = Question::query()
            ->where('conference_id', $id)
            ->latest()
            ->get(['id', 'body', 'status', 'created_at']);

        return response()->json($questions);
    }

    public function update(Request $request, int $id, int $qid): JsonResponse
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
            'status' => ['nullable', 'in:pending,answered,hidden'],
            'body' => ['nullable', 'string', 'min:1', 'max:280'],
        ]);

        if (isset($validated['status'])) {
            $question->status = $validated['status'];
        }
        if (isset($validated['body'])) {
            $question->body = trim($validated['body']);
        }

        $question->save();

        return response()->json($question->only(['id', 'body', 'status', 'created_at']));
    }

    public function destroy(int $id, int $qid): JsonResponse
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

    public function manage(int $id)
    {
        $conference = Conference::find($id);

        if (is_null($conference)) {
            return response('Conferencia no encontrada', 404);
        }

        return view('intranet.conferences.questions', [
            'conference' => $conference,
        ]);
    }
}
