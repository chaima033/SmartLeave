<?php

namespace Tests\Feature;

use App\Models\AiMessage;
use App\Models\CandidateProfile;
use App\Models\CompanyProfile;
use App\Models\User;
use App\Services\GeminiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class AiAssistantControllerAdditionalTest extends TestCase
{
    use RefreshDatabase;

    public function test_assistant_ask_route_with_recruiter_context_uses_company_profile(): void
    {
        $recruiter = User::factory()->create(['role' => 'recruiter', 'name' => 'Marie']);

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
                    'Conseils recrutement',
                    Mockery::on(
                        fn($context) => is_array($context)
                            && $context['user']['name'] === 'Marie'
                            && $context['user']['role'] === 'recruiter'
                            && isset($context['profile']['company_name'])
                    ),
                    [],
                    'recruiter'
                )
                ->andReturn('Réponse recrutement');
        }));

        $response = $this->actingAs($recruiter)->post('/assistant', [
            'message' => 'Conseils recrutement',
            'mode' => 'recruiter',
        ]);

        $response->assertOk()->assertJson(['reply' => 'Réponse recrutement']);

        $this->assertDatabaseHas('ai_messages', [
            'user_id' => $recruiter->id,
            'mode' => 'recruiter',
            'prompt' => 'Conseils recrutement',
            'response' => 'Réponse recrutement',
        ]);
    }

    public function test_assistant_ask_route_includes_previous_history_for_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => 'candidate', 'name' => 'Aline']);

        CandidateProfile::create([
            'user_id' => $user->id,
            'headline' => 'Developpeuse',
            'location' => 'Lyon',
        ]);

        AiMessage::create([
            'user_id' => $user->id,
            'mode' => 'candidate',
            'prompt' => 'Premier message',
            'response' => 'Première réponse',
            'context' => ['user' => ['name' => 'Aline']],
        ]);

        AiMessage::create([
            'user_id' => $user->id,
            'mode' => 'candidate',
            'prompt' => 'Second message',
            'response' => 'Seconde réponse',
            'context' => ['user' => ['name' => 'Aline']],
        ]);

        $this->instance(GeminiService::class, Mockery::mock(GeminiService::class, function ($mock)
        {
            $mock->shouldReceive('generate')
                ->once()
                ->andReturn('Réponse avec historique');
        }));

        $response = $this->actingAs($user)->post('/assistant', [
            'message' => 'Nouvelle question',
            'mode' => 'candidate',
        ]);

        $response->assertOk()->assertJson(['reply' => 'Réponse avec historique']);
    }
}
