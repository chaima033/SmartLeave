<?php

namespace Tests\Unit;

use App\Services\GeminiService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiServiceTest extends TestCase
{
    public function test_build_payload_contains_context_history_and_question(): void
    {
        $service = new GeminiService();

        $payload = $service->buildPayload(
            'Quels CV sont disponibles ?',
            ['user' => ['name' => 'Karim', 'role' => 'candidate']],
            [
                ['role' => 'user', 'content' => 'Bonjour'],
                ['role' => 'assistant', 'content' => 'Salut'],
            ],
            'candidate'
        );

        $joinedText = '';

        foreach ($payload['contents'] as $content)
        {
            foreach ($content['parts'] as $part)
            {
                $joinedText .= ' ' . ($part['text'] ?? '');
            }
        }

        $this->assertStringContainsString('Contexte métier', $joinedText);
        $this->assertStringContainsString('Quels CV sont disponibles ?', $joinedText);
        $this->assertStringContainsString('Bonjour', $joinedText);
        $this->assertStringContainsString('Salut', $joinedText);
    }

    public function test_generate_returns_answer_when_api_successful(): void
    {
        config(['services.gemini.key' => 'test-api-key']);
        config(['services.gemini.model' => 'gemini-flash-latest']);

        Http::fake([
            '*' => Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [[
                            'text' => 'Voici une réponse générée par Gemini.',
                        ]],
                    ],
                ]],
            ], 200),
        ]);

        $service = new GeminiService();

        $answer = $service->generate(
            'Parlez-moi des CV disponibles.',
            ['user' => ['name' => 'Karim', 'role' => 'candidate']],
            [],
            'candidate'
        );

        $this->assertSame('Voici une réponse générée par Gemini.', $answer);
    }

    public function test_build_payload_uses_recruiter_system_prompt(): void
    {
        $service = new GeminiService();

        $payload = $service->buildPayload(
            'Que pensez-vous de ce candidat ?',
            ['user' => ['name' => 'Sophie', 'role' => 'recruiter']],
            [],
            'recruiter'
        );

        $this->assertStringContainsString('assistant de recrutement', $payload['contents'][0]['parts'][0]['text']);
        $this->assertStringContainsString('Question utilisateur: Que pensez-vous de ce candidat ?', $payload['contents'][1]['parts'][0]['text']);
    }

    public function test_generate_returns_fallback_response_when_gemini_is_unavailable(): void
    {
        config(['services.gemini.key' => null]);

        $service = new GeminiService();

        $answer = $service->generate(
            'Quels sont les prochains entretiens ?',
            [
                'user' => ['name' => 'Claire', 'role' => 'recruiter'],
                'profile' => [
                    'company_name' => 'SmartLeave SA',
                    'company_industry' => 'Ressources Humaines',
                    'company_description' => 'Plateforme de recrutement intelligente',
                ],
            ],
            [],
            'recruiter'
        );

        $this->assertStringContainsString('Bonjour Claire', $answer);
        $this->assertStringContainsString('recruteur', $answer);
        $this->assertStringContainsString('SmartLeave SA', $answer);
        $this->assertStringContainsString('Ressources Humaines', $answer);
        $this->assertStringContainsString('Plateforme de recrutement intelligente', $answer);
        $this->assertStringContainsString('Quels sont les prochains entretiens ?', $answer);
    }

    public function test_generate_returns_fallback_response_when_api_fails(): void
    {
        config(['services.gemini.key' => 'test-api-key']);
        config(['services.gemini.model' => 'gemini-flash-latest']);

        Http::fake([
            '*' => Http::response([], 500),
        ]);

        $service = new GeminiService();

        $answer = $service->generate(
            'Donnez-moi un résumé rapide.',
            ['user' => ['name' => 'Olivier', 'role' => 'candidate']],
            [],
            'candidate'
        );

        $this->assertStringContainsString('Bonjour Olivier', $answer);
        $this->assertStringContainsString('candidat', $answer);
        $this->assertStringContainsString('Donnez-moi un résumé rapide.', $answer);
    }
}
