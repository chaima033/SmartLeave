<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\JobOffer;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobApplicationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_can_apply_to_published_offer(): void
    {
        $candidate = User::factory()->create(['role' => 'candidate']);
        $recruiter = User::factory()->create(['role' => 'recruiter']);

        $resume = Resume::create([
            'user_id' => $candidate->id,
            'title' => 'CV principal',
            'skills' => ['Laravel'],
            'is_primary' => true,
        ]);

        $job = JobOffer::create([
            'recruiter_id' => $recruiter->id,
            'title' => 'Developpeur Laravel',
            'slug' => 'developpeur-laravel-test',
            'description' => 'Poste test',
            'status' => 'published',
            'skills' => ['Laravel'],
        ]);

        $response = $this->actingAs($candidate)->post(route('jobs.apply', $job), [
            'resume_id' => $resume->id,
            'cover_letter' => 'Je suis interesse.',
            'candidate_notes' => 'Disponible immediatement.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('applications', [
            'job_offer_id' => $job->id,
            'candidate_id' => $candidate->id,
            'resume_id' => $resume->id,
            'status' => 'submitted',
        ]);

        $this->assertEquals(1, Application::count());
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_candidate_cannot_create_duplicate_application(): void
    {
        $candidate = User::factory()->create(['role' => 'candidate']);
        $recruiter = User::factory()->create(['role' => 'recruiter']);

        $resume = Resume::create([
            'user_id' => $candidate->id,
            'title' => 'CV principal',
            'skills' => ['Laravel'],
            'is_primary' => true,
        ]);

        $job = JobOffer::create([
            'recruiter_id' => $recruiter->id,
            'title' => 'Developpeur Laravel Duplicate',
            'slug' => 'developpeur-laravel-duplicate-test',
            'description' => 'Poste test',
            'status' => 'published',
            'skills' => ['Laravel'],
        ]);

        Application::create([
            'job_offer_id' => $job->id,
            'candidate_id' => $candidate->id,
            'resume_id' => $resume->id,
            'status' => 'submitted',
            'cover_letter' => 'Initiale',
            'resume_snapshot' => $resume->toArray(),
            'candidate_notes' => 'Note',
        ]);

        $response = $this->actingAs($candidate)->post(route('jobs.apply', $job), [
            'resume_id' => $resume->id,
            'cover_letter' => 'Deuxieme essai',
            'candidate_notes' => 'Autre note',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Vous avez déjà postulé à cette offre.');
        $this->assertDatabaseCount('applications', 1);
    }

    public function test_get_apply_route_redirects_to_job_page(): void
    {
        $candidate = User::factory()->create(['role' => 'candidate']);
        $recruiter = User::factory()->create(['role' => 'recruiter']);

        $job = JobOffer::create([
            'recruiter_id' => $recruiter->id,
            'title' => 'Developpeur Laravel Senior',
            'slug' => 'developpeur-laravel-senior-test',
            'description' => 'Poste test',
            'status' => 'published',
            'skills' => ['Laravel'],
        ]);

        $response = $this->actingAs($candidate)->get(route('jobs.apply.form', $job));

        $response->assertRedirect(route('jobs.show', $job));
        $response->assertSessionHas('status', 'Utilisez le formulaire de candidature pour envoyer votre dossier.');
    }
}
