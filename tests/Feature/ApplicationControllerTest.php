<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\JobOffer;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_can_apply_with_latest_resume_when_resume_id_is_missing(): void
    {
        $candidate = User::factory()->create(['role' => 'candidate']);
        $recruiter = User::factory()->create(['role' => 'recruiter']);

        Resume::create([
            'user_id' => $candidate->id,
            'title' => 'CV ancien',
            'skills' => ['PHP'],
            'is_primary' => false,
            'created_at' => now()->subMinute(),
            'updated_at' => now()->subMinute(),
        ]);

        $latest = Resume::create([
            'user_id' => $candidate->id,
            'title' => 'CV récent',
            'skills' => ['Laravel'],
            'is_primary' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $job = JobOffer::create([
            'recruiter_id' => $recruiter->id,
            'title' => 'Offre test',
            'slug' => 'offre-test',
            'description' => 'Poste test',
            'status' => 'published',
            'skills' => ['PHP'],
        ]);

        $response = $this->actingAs($candidate)->post(route('jobs.apply', $job), [
            'cover_letter' => 'Je postule',
            'candidate_notes' => 'Disponible',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('applications', [
            'candidate_id' => $candidate->id,
            'job_offer_id' => $job->id,
            'status' => 'submitted',
        ]);

        $application = Application::where('job_offer_id', $job->id)
            ->where('candidate_id', $candidate->id)
            ->first();

        $this->assertNotNull($application);
        $this->assertNotNull($application->resume_id);
    }

    public function test_recruiter_can_view_application_index_and_update_application_status(): void
    {
        $candidate = User::factory()->create(['role' => 'candidate']);
        $recruiter = User::factory()->create(['role' => 'recruiter']);

        $job = JobOffer::create([
            'recruiter_id' => $recruiter->id,
            'title' => 'Offre suivie',
            'slug' => 'offre-suivie',
            'description' => 'Poste',
            'status' => 'published',
            'skills' => ['PHP'],
        ]);

        $resume = Resume::create([
            'user_id' => $candidate->id,
            'title' => 'CV de suivi',
            'skills' => ['PHP'],
            'is_primary' => true,
        ]);

        $application = Application::create([
            'job_offer_id' => $job->id,
            'candidate_id' => $candidate->id,
            'resume_id' => $resume->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($recruiter)->get(route('recruiter.applications.index'));
        $response->assertOk();

        $update = $this->actingAs($recruiter)->put(route('recruiter.applications.update', $application), [
            'status' => 'hired',
            'recruiter_feedback' => 'Bonne candidature',
        ]);

        $update->assertRedirect();
        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'hired',
            'recruiter_feedback' => 'Bonne candidature',
        ]);
    }
}
