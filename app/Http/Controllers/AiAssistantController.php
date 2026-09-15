<?php

namespace App\Http\Controllers;

use App\Models\AiChatMessage;
use App\Services\GeminiService;
use Illuminate\Http\Request;

class AiAssistantController extends Controller
{
    private const CONTEXT_MESSAGES = 20;

    public function history()
    {
        $messages = AiChatMessage::where('user_id', auth()->id())
            ->orderBy('created_at')
            ->orderBy('id')
            ->get(['role', 'content', 'created_at']);

        return response()->json(['success' => true, 'messages' => $messages]);
    }

    public function send(Request $request)
    {
        $request->validate(['message' => 'required|string|max:4000']);

        $userId = auth()->id();

        $recentHistory = AiChatMessage::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(self::CONTEXT_MESSAGES)
            ->get(['role', 'content'])
            ->reverse()
            ->map(fn ($m) => ['role' => $m->role, 'content' => $m->content])
            ->values()
            ->all();

        AiChatMessage::create([
            'user_id' => $userId,
            'role' => 'user',
            'content' => $request->message,
        ]);

        $result = (new GeminiService())->reply($recentHistory, $request->message);

        if (! $result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']]);
        }

        AiChatMessage::create([
            'user_id' => $userId,
            'role' => 'assistant',
            'content' => $result['reply'],
        ]);

        return response()->json(['success' => true, 'reply' => $result['reply']]);
    }

    public function clear()
    {
        AiChatMessage::where('user_id', auth()->id())->delete();

        return response()->json(['success' => true]);
    }
}
