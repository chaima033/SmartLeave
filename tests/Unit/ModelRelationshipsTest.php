<?php

namespace Tests\Unit;

use App\Http\Requests\Chatbot\ChatbotRequest;
use App\Http\Requests\JobOffer\UpdateJobOfferRequest;
use App\Http\Requests\Resume\UpdateResumeRequest;
use App\Models\AiMessage;
use App\Models\Application;
use App\Models\CandidateProfile;
use App\Models\CompanyProfile;
use App\Models\JobOffer;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_candidate_relationships(): void
    {
        $candidate = User::factory()->create(['role' => 'candidate']);

        $candidateProfile = CandidateProfile::create([
            'user_id' => $candidate->id,
            'headline' => 'Testeur',
            'location' => 'Paris',
        ]);

        $resume = Resume::create([
            'user_id' => $candidate->id,
            'title' => 'CV candidate',
            'skills' => ['PHP'],
            'is_primary' => true,
        ]);

        $recruiter = User::factory()->create(['role' => 'recruiter']);

        $jobOffer = JobOffer::create([
            'recruiter_id' => $recruiter->id,
            'title' => 'Offre relation',
            'slug' => 'offre-relation',
            'description' => 'Description',
            'status' => 'published',
            'skills' => ['PHP'],
        ]);

        $application = Application::create([
            'job_offer_id' => $jobOffer->id,
            'candidate_id' => $candidate->id,
            'resume_id' => $resume->id,
            'status' => 'submitted',
        ]);

        $aiMessage = AiMessage::create([
            'user_id' => $candidate->id,
            'mode' => 'candidate',
            'prompt' => 'Test',
            'response' => 'Response',
            'context' => ['user' => ['name' => $candidate->name]],
        ]);

        $this->assertTrue($candidate->candidateProfile->is($candidateProfile));
        $this->assertTrue($candidate->resumes->first()->is($resume));
        $this->assertTrue($candidate->applications->first()->is($application));
        $this->assertTrue($candidate->aiMessages->first()->is($aiMessage));
    }

    public function test_candidate_profile_belongs_to_user(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);

        $candidateProfile = CandidateProfile::create([
            'user_id' => $user->id,
            'headline' => 'Testeur',
            'location' => 'Lyon',
        ]);

        $this->assertTrue($candidateProfile->user->is($user));
    }

    public function test_candidate_profile_casts_method_returns_array(): void
    {
        $candidateProfile = new CandidateProfile();

        $method = new \ReflectionMethod($candidateProfile, 'casts');
        $method->setAccessible(true);

        $this->assertSame(['skills' => 'array'], $method->invoke($candidateProfile));
    }

    public function test_user_recruiter_relationships(): void
    {
        $recruiter = User::factory()->create(['role' => 'recruiter']);

        $companyProfile = CompanyProfile::create([
            'user_id' => $recruiter->id,
            'company_name' => 'Test Co',
            'industry' => 'Tech',
            'website' => 'https://example.com',
            'location' => 'Paris',
        ]);

        $jobOffer = JobOffer::create([
            'recruiter_id' => $recruiter->id,
            'title' => 'Offre test',
            'slug' => 'offre-test',
            'description' => 'Description',
            'status' => 'published',
            'skills' => ['PHP'],
        ]);

        $this->assertTrue($recruiter->companyProfile->is($companyProfile));
        $this->assertTrue($recruiter->jobOffers->first()->is($jobOffer));
    }

    public function test_company_profile_belongs_to_user(): void
    {
        $user = User::factory()->create(['role' => 'recruiter']);

        $companyProfile = CompanyProfile::create([
            'user_id' => $user->id,
            'company_name' => 'Company Test',
            'industry' => 'Service',
            'website' => 'https://example.com',
            'location' => 'Paris',
        ]);

        $this->assertTrue($companyProfile->user->is($user));
    }

    public function test_company_profile_user_relationship_from_model(): void
    {
        $user = User::factory()->create(['role' => 'recruiter']);

        $companyProfile = CompanyProfile::create([
            'user_id' => $user->id,
            'company_name' => 'Company Test',
            'industry' => 'Service',
            'website' => 'https://example.com',
            'location' => 'Paris',
        ]);

        $this->assertTrue($user->companyProfile->is($companyProfile));
    }

    public function test_update_resume_request_inherits_store_rules(): void
    {
        $request = new UpdateResumeRequest();

        $this->assertArrayHasKey('title', $request->rules());
        $this->assertArrayHasKey('summary', $request->rules());
    }

    public function test_update_job_offer_request_inherits_store_rules(): void
    {
        $request = new UpdateJobOfferRequest();

        $this->assertArrayHasKey('title', $request->rules());
        $this->assertArrayHasKey('description', $request->rules());
    }

    public function test_resume_belongs_to_user(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);

        $resume = Resume::create([
            'user_id' => $user->id,
            'title' => 'CV lien',
            'skills' => ['PHP'],
            'is_primary' => true,
        ]);

        $this->assertTrue($resume->user->is($user));
    }

    public function test_ai_message_belongs_to_user(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);

        $message = AiMessage::create([
            'user_id' => $user->id,
            'mode' => 'candidate',
            'prompt' => 'Test',
            'response' => 'OK',
            'context' => ['foo' => 'bar'],
        ]);

        $this->assertTrue($message->user->is($user));
    }
}
