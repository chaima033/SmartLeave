<?php

namespace Tests\Feature;

use App\Models\CandidateProfile;
use App\Models\User;
use App\Services\GeminiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class AiAssistantControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_chatbot_with_guest_input_does_not_persist_message(): void
    {
        $this->instance(GeminiService::class, Mockery::mock(GeminiService::class, function ($mock)
        {
            $mock->shouldReceive('generate')
                ->once()
                ->with('Bonjour', ['user' => ['name' => null, 'role' => 'candidate'], 'profile' => null], [], 'candidate')
                ->andReturn('Salut utilisateur');
        }));

        $response = $this->postJson('/api/chatbot', [
            'message' => 'Bonjour',
            'mode' => 'candidate',
        ]);

        $response->assertOk()->assertJson(['reply' => 'Salut utilisateur']);
        $this->assertDatabaseCount('ai_messages', 0);
    }

    public function test_api_chatbot_with_user_persists_ai_message(): void
    {
        $user = User::factory()->create(['role' => 'candidate', 'name' => 'Aline']);

        CandidateProfile::create([
            'user_id' => $user->id,
            'headline' => 'Developpeuse',
            'location' => 'Lyon',
        ]);

        $this->instance(GeminiService::class, Mockery::mock(GeminiService::class, function ($mock)
        {
            $mock->shouldReceive('generate')
                ->once()
                ->with(
                    'Questions sur les CV',
                    Mockery::on(fn($context) => is_array($context) && $context['user']['name'] === 'Aline'),
                    [],
                    'candidate'
                )
                ->andReturn('Reponse Aline');
        }));

        $response = $this->postJson('/api/chatbot', [
            'message' => 'Questions sur les CV',
            'mode' => 'candidate',
            'user_id' => $user->id,
        ]);

        $response->assertOk()->assertJson(['reply' => 'Reponse Aline']);
        $this->assertDatabaseHas('ai_messages', [
            'user_id' => $user->id,
            'prompt' => 'Questions sur les CV',
            'response' => 'Reponse Aline',
        ]);
    }

    public function test_assistant_ask_route_saves_message_for_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => 'candidate', 'name' => 'Sophie']);

        CandidateProfile::create([
            'user_id' => $user->id,
            'headline' => 'Consultante',
            'location' => 'Bordeaux',
        ]);

        $this->instance(GeminiService::class, Mockery::mock(GeminiService::class, function ($mock)
        {
            $mock->shouldReceive('generate')
                ->once()
                ->andReturn('Reponse de Sophie');
        }));

        $response = $this->actingAs($user)->post('/assistant', [
            'message' => 'Bonjour IA',
            'mode' => 'candidate',
        ]);

        $response->assertOk()->assertJson(['reply' => 'Reponse de Sophie']);
        $this->assertDatabaseHas('ai_messages', [
            'user_id' => $user->id,
            'prompt' => 'Bonjour IA',
            'response' => 'Reponse de Sophie',
        ]);
    }
}
