<?php

namespace Tests\Feature;

use App\Models\CandidateProfile;
use App\Models\CompanyProfile;
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

    public function test_assistant_index_returns_view_for_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);

        $response = $this->actingAs($user)->get('/assistant');

        $response->assertOk();
        $response->assertViewHas('history');
    }

    public function test_assistant_ask_route_returns_service_failure_message(): void
    {
        $user = User::factory()->create(['role' => 'candidate', 'name' => 'Sophie']);

        $this->instance(GeminiService::class, Mockery::mock(GeminiService::class, function ($mock)
        {
            $mock->shouldReceive('generate')
                ->once()
                ->andThrow(new \RuntimeException('Service failed'));
        }));

        $response = $this->actingAs($user)->post('/assistant', [
            'message' => 'Bonjour IA',
            'mode' => 'candidate',
        ]);

        $response->assertStatus(503);
        $this->assertSame('Le service IA est indisponible pour le moment.', $response->json('reply'));
    }

    public function test_assistant_ask_route_returns_json_service_failure(): void
    {
        $user = User::factory()->create(['role' => 'candidate', 'name' => 'Noémie']);

        $this->instance(GeminiService::class, Mockery::mock(GeminiService::class, function ($mock)
        {
            $mock->shouldReceive('generate')
                ->once()
                ->andThrow(new \RuntimeException('Service failure'));
        }));

        $response = $this->actingAs($user)->postJson('/assistant', [
            'message' => 'Bonjour IA',
            'mode' => 'candidate',
        ]);

        $response->assertStatus(503);
        $this->assertSame('Le service IA est indisponible pour le moment.', $response->json('reply'));
    }

    public function test_api_chatbot_falls_back_when_gemini_throws(): void
    {
        $user = User::factory()->create(['role' => 'candidate', 'name' => 'Dev']);

        CandidateProfile::create([
            'user_id' => $user->id,
            'headline' => 'Développeur',
            'location' => 'Nantes',
        ]);

        $this->instance(GeminiService::class, Mockery::mock(GeminiService::class, function ($mock)
        {
            $mock->shouldReceive('generate')
                ->once()
                ->andThrow(new \RuntimeException('Service down'));
        }));

        $response = $this->postJson('/api/chatbot', [
            'message' => 'Comment améliorer mon CV ?',
            'mode' => 'candidate',
            'user_id' => $user->id,
        ]);

        $response->assertStatus(503);
        $this->assertSame('Le service IA est indisponible pour le moment.', $response->json('reply'));
    }

    public function test_api_chatbot_with_recruiter_context_includes_company_profile(): void
    {
        $recruiter = User::factory()->create(['role' => 'recruiter', 'name' => 'Julien']);

        CompanyProfile::create([
            'user_id' => $recruiter->id,
            'company_name' => 'Agence Test',
            'industry' => 'Tech',
            'website' => 'https://agence.test',
            'location' => 'Lyon',
        ]);

        $this->instance(GeminiService::class, Mockery::mock(GeminiService::class, function ($mock)
        {
            $mock->shouldReceive('generate')
                ->once()
                ->with(
                    'Recruter un profil ?',
                    Mockery::on(
                        fn($context) => is_array($context)
                            && $context['user']['role'] === 'recruiter'
                            && isset($context['profile']['company_name'])
                    ),
                    [],
                    'recruiter'
                )
                ->andReturn('Réponse recruteur');
        }));

        $response = $this->postJson('/api/chatbot', [
            'prompt' => 'Recruter un profil ?',
            'mode' => 'recruiter',
            'user_id' => $recruiter->id,
        ]);

        $response->assertOk()->assertJson(['reply' => 'Réponse recruteur']);
    }
}
