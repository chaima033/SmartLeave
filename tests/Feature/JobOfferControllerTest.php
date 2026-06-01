<?php

namespace Tests\Feature;

use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobOfferControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_recruiter_can_create_update_and_delete_job_offer(): void
    {
        $recruiter = User::factory()->create(['role' => 'recruiter']);

        $response = $this->actingAs($recruiter)->post(route('recruiter.jobs.store'), [
            'title' => 'Offre Laravel',
            'description' => 'Une offre pour tester',
            'location' => 'Paris',
            'skills' => 'Laravel, PHP',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('recruiter.jobs.index'));

        $job = JobOffer::first();
        $this->assertNotNull($job);
        $this->assertStringContainsString('offre-laravel', $job->slug);
        $this->assertEquals('published', $job->status);
        $this->assertNotNull($job->published_at);
        $this->assertEquals(['Laravel', 'PHP'], $job->skills);

        $update = $this->actingAs($recruiter)->put(route('recruiter.jobs.update', $job), [
            'title' => 'Offre Laravel mise a jour',
            'description' => 'Modification',
            'location' => 'Paris',
            'skills' => 'Laravel, PHP, Vue',
            'status' => 'published',
        ]);

        $update->assertRedirect(route('recruiter.jobs.index'));
        $job->refresh();

        $this->assertSame('Offre Laravel mise a jour', $job->title);
        $this->assertSame(['Laravel', 'PHP', 'Vue'], $job->skills);

        $delete = $this->actingAs($recruiter)->delete(route('recruiter.jobs.destroy', $job));

        $delete->assertRedirect();
        $this->assertDatabaseMissing('job_offers', ['id' => $job->id]);
    }

    public function test_jobs_index_search_and_show_work_for_public_jobs(): void
    {
        $recruiter = User::factory()->create(['role' => 'recruiter']);

        $job = JobOffer::create([
            'recruiter_id' => $recruiter->id,
            'title' => 'Testeur QA',
            'slug' => 'testeur-qa',
            'description' => 'Test de recherche',
            'status' => 'published',
            'skills' => ['QA'],
        ]);

        $user = User::factory()->create(['role' => 'candidate']);

        $index = $this->actingAs($user)->get(route('jobs.index', ['search' => 'Testeur']));
        $show = $this->actingAs($user)->get(route('jobs.show', $job));

        $index->assertOk();
        $show->assertOk();
    }
}
