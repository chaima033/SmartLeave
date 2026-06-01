<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\JobOffer;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_dashboard_shows_candidate_mode(): void
    {
        $candidate = User::factory()->create(['role' => 'candidate']);

        Resume::create([
            'user_id' => $candidate->id,
            'title' => 'CV demo',
            'skills' => ['PHP'],
            'is_primary' => true,
        ]);

        JobOffer::create([
            'recruiter_id' => User::factory()->create(['role' => 'recruiter'])->id,
            'title' => 'Offre publique',
            'slug' => 'offre-publique',
            'description' => 'Publication',
            'status' => 'published',
            'skills' => ['PHP'],
        ]);

        Application::create([
            'job_offer_id' => 1,
            'candidate_id' => $candidate->id,
            'resume_id' => 1,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($candidate)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('mode', 'candidate');
        $response->assertViewHas('resumesCount', 1);
        $response->assertViewHas('jobsCount', 1);
    }

    public function test_recruiter_dashboard_shows_recruiter_mode(): void
    {
        $recruiter = User::factory()->create(['role' => 'recruiter']);

        $job = JobOffer::create([
            'recruiter_id' => $recruiter->id,
            'title' => 'Offre recruteur',
            'slug' => 'offre-recruteur',
            'description' => 'Poste',
            'status' => 'published',
            'skills' => ['PHP'],
        ]);

        $resume = Resume::create([
            'user_id' => User::factory()->create(['role' => 'candidate'])->id,
            'title' => 'CV application',
            'skills' => ['PHP'],
            'is_primary' => true,
        ]);

        Application::create([
            'job_offer_id' => $job->id,
            'candidate_id' => $resume->user_id,
            'resume_id' => $resume->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($recruiter)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('mode', 'recruiter');
        $response->assertViewHas('offersCount', 1);
        $response->assertViewHas('applicationsCount', 1);
    }
}
