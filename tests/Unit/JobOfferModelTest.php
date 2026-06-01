<?php

namespace Tests\Unit;

use App\Models\JobOffer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobOfferModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_slug_is_generated_and_published_at_set_when_job_offer_is_saved(): void
    {
        $recruiter = User::factory()->create(['role' => 'recruiter']);

        $job = JobOffer::create([
            'recruiter_id' => $recruiter->id,
            'title' => 'Offre sans slug',
            'description' => 'Test auto slug',
            'status' => 'published',
            'skills' => ['Laravel'],
        ]);

        $this->assertNotNull($job->slug);
        $this->assertStringContainsString('offre-sans-slug', $job->slug);
        $this->assertNotNull($job->published_at);
        $this->assertEquals('published', $job->status);
    }
}
