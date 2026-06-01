<?php

namespace Tests\Feature;

use App\Models\AiMessage;
use App\Models\CandidateProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatbotApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_chatbot_api_returns_reply_and_persists_message(): void
    {
        $user = User::factory()->create([
            'role' => 'candidate',
            'name' => 'Test Candidate',
        ]);

        CandidateProfile::create([
            'user_id' => $user->id,
            'headline' => 'Developpeur Laravel',
            'location' => 'Paris',
        ]);

        Http::fake([
            '*' => Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [[
                            'text' => 'Oui, vous avez 2 CV disponibles.',
                        ]],
                    ],
                ]],
            ], 200),
        ]);

        $response = $this->postJson('/api/chatbot', [
            'message' => 'Quels CV sont disponibles ?',
            'mode' => 'candidate',
            'user_id' => $user->id,
        ]);

        $response->assertOk()->assertJsonStructure(['reply']);

        $this->assertDatabaseHas('ai_messages', [
            'user_id' => $user->id,
            'prompt' => 'Quels CV sont disponibles ?',
        ]);

        $this->assertDatabaseCount('ai_messages', 1);
        $this->assertEquals('Oui, vous avez 2 CV disponibles.', AiMessage::first()->response);
    }

    public function test_chatbot_api_validates_required_message(): void
    {
        $response = $this->postJson('/api/chatbot', [
            'mode' => 'candidate',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['prompt']);
    }

    public function test_chatbot_api_falls_back_when_gemini_is_unavailable(): void
    {
        config(['services.gemini.key' => null]);

        $user = User::factory()->create([
            'role' => 'recruiter',
            'name' => 'Recruiter Test',
        ]);

        $response = $this->postJson('/api/chatbot', [
            'message' => 'Quels CV sont disponibles ?',
            'mode' => 'recruiter',
            'user_id' => $user->id,
        ]);

        $response->assertOk()->assertJsonStructure(['reply']);
        $this->assertStringContainsString('je n\'ai pas pu joindre Gemini', $response->json('reply'));
        $this->assertDatabaseHas('ai_messages', [
            'user_id' => $user->id,
            'prompt' => 'Quels CV sont disponibles ?',
        ]);
    }
}
