<?php

namespace Tests\Unit;

use App\Services\GeminiService;
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
}
