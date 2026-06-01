<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chatbot\ChatbotRequest;
use App\Models\AiMessage;
use App\Models\User;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiAssistantController extends Controller
{
    public function index(Request $request): View
    {
        return view('assistant.index', [
            'history' => $request->user()->aiMessages()->latest()->take(12)->get(),
        ]);
    }

    public function ask(ChatbotRequest $request, GeminiService $gemini): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $context = [
            'user' => $user
                ? $user->only(['name', 'role', 'headline', 'location'])
                : [
                    'name' => null,
                    'role' => $data['mode'],
                ],
            'profile' => $this->resolveProfile($user),
        ];

        $history = $user
            ? $user->aiMessages()
                ->latest()
                ->limit(5)
                ->get(['prompt', 'response'])
                ->reverse()
                ->flatMap(fn ($item) => [
                    ['role' => 'user', 'content' => $item->prompt],
                    ['role' => 'assistant', 'content' => $item->response],
                ])
                ->values()
                ->all()
            : [];

        try {
            $response = $gemini->generate(
                $data['prompt'],
                $context,
                $history,
                $data['mode']
            );
        } catch (\Throwable $throwable) {
            $message = 'Le service IA est indisponible pour le moment.';

            return response()->json([
                'reply' => $message,
                'answer' => $message,
            ], 503);
        }

        if ($user) {
            AiMessage::create([
                'user_id' => $user->id,
                'mode' => $data['mode'],
                'prompt' => $data['prompt'],
                'response' => $response,
                'context' => $context,
            ]);
        }

        return response()->json([
            'reply' => $response,
            'answer' => $response,
        ]);
    }

    public function askApi(ChatbotRequest $request, GeminiService $gemini): JsonResponse
    {
        $user = $request->filled('user_id')
            ? User::with([
                'candidateProfile',
                'companyProfile',
                'aiMessages',
            ])->find($request->integer('user_id'))
            : null;

        $context = [
            'user' => $user
                ? $user->only(['name', 'role', 'headline', 'location'])
                : [
                    'name' => null,
                    'role' => $request->string('mode')->toString(),
                ],
            'profile' => $this->resolveProfile($user),
        ];

        $history = $user
            ? $user->aiMessages()
                ->latest()
                ->limit(5)
                ->get(['prompt', 'response'])
                ->reverse()
                ->flatMap(fn ($item) => [
                    ['role' => 'user', 'content' => $item->prompt],
                    ['role' => 'assistant', 'content' => $item->response],
                ])
                ->values()
                ->all()
            : [];

        try {
            $response = $gemini->generate(
                $request->input('prompt'),
                $context,
                $history,
                $request->input('mode')
            );
        } catch (\Throwable $throwable) {
            return response()->json([
                'reply' => 'Le service IA est indisponible pour le moment.',
            ], 503);
        }

        if ($user) {
            AiMessage::create([
                'user_id' => $user->id,
                'mode' => $request->input('mode'),
                'prompt' => $request->input('prompt'),
                'response' => $response,
                'context' => $context,
            ]);
        }

        return response()->json([
            'reply' => $response,
        ]);
    }

    private function resolveProfile(?User $user): ?array
    {
        if (! $user) {
            return null;
        }

        return $user->role === 'recruiter'
            ? $user->companyProfile?->toArray()
            : $user->candidateProfile?->toArray();
    }
}
