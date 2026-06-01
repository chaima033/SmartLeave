<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiService
{
    public function buildPayload(string $prompt, array $context = [], array $history = [], string $mode = 'candidate'): array
    {
        $systemPrompt = $mode === 'recruiter'
            ? 'Tu es un assistant de recrutement. Donne des conseils clairs sur la sélection, les entretiens, les offres et les profils.'
            : 'Tu es un assistant carrière. Aide le candidat sur son CV, ses candidatures, sa recherche d emploi et ses réponses aux offres.';

        $contents = [];

        $contents[] = [
            'role' => 'user',
            'parts' => [[
                'text' => trim($systemPrompt . "\n\nContexte métier: " . json_encode($context, JSON_UNESCAPED_UNICODE)),
            ]],
        ];

        foreach ($history as $item)
        {
            $contents[] = [
                'role' => $item['role'] ?? 'user',
                'parts' => [[
                    'text' => $item['content'] ?? '',
                ]],
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [[
                'text' => 'Question utilisateur: ' . $prompt,
            ]],
        ];

        return [
            'contents' => $contents,
        ];
    }

    public function generate(string $prompt, array $context = [], array $history = [], string $mode = 'candidate'): string
    {
        $apiKey = config('services.gemini.key');
        $model = config('services.gemini.model', 'gemini-flash-latest');

        try
        {
            if (! $apiKey)
            {
                throw new RuntimeException('Gemini API key is missing.');
            }

            $payload = $this->buildPayload($prompt, $context, $history, $mode);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-goog-api-key' => $apiKey,
            ])->timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", $payload);

            if (! $response->successful())
            {
                throw new RuntimeException('Gemini request failed: ' . $response->body());
            }

            $answer = data_get($response->json(), 'candidates.0.content.parts.0.text');

            if (filled($answer))
            {
                return $answer;
            }
        }
        catch (\Throwable $throwable)
        {
            // Fallback local to keep the chatbot functional even when the external API fails.
        }

        return $this->fallbackResponse($prompt, $context, $mode);
    }

    private function fallbackResponse(string $prompt, array $context, string $mode): string
    {
        $roleLabel = $mode === 'recruiter' ? 'recruteur' : 'candidat';
        $profile = $context['profile'] ?? [];
        $user = $context['user'] ?? [];
        $name = $user['name'] ?? 'Utilisateur';

        $summaryParts = [];

        if (is_array($profile))
        {
            foreach (['headline', 'location', 'company_name', 'company_industry', 'company_description'] as $key)
            {
                if (! empty($profile[$key]))
                {
                    $summaryParts[] = $profile[$key];
                }
            }
        }

        $summary = $summaryParts ? implode(' | ', array_unique($summaryParts)) : 'aucune donnée profil supplémentaire';

        return "Bonjour {$name}, je n'ai pas pu joindre Gemini pour le moment, mais je peux déjà vous aider en tant que {$roleLabel}. Contexte disponible: {$summary}. Votre demande: {$prompt}. Si vous voulez, je peux vous orienter sur les CV, offres, candidatures ou le profil entreprise à partir des données enregistrées dans l'application.";
    }
}
