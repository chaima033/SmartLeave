<?php

namespace Tests\Feature;

use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResumeWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_can_create_resume(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);

        $response = $this->actingAs($user)->post(route('resumes.store'), [
            'title' => 'CV Laravel',
            'summary' => 'Developpeur full stack',
            'experience' => '4 ans',
            'education' => 'Licence',
            'skills' => 'Laravel, PHP, MySQL',
            'projects' => 'Portail RH',
            'certifications' => 'AWS',
            'languages' => 'FR, EN',
            'is_primary' => 1,
        ]);

        $response->assertRedirect(route('resumes.index'));
        $this->assertDatabaseHas('resumes', [
            'user_id' => $user->id,
            'title' => 'CV Laravel',
            'is_primary' => 1,
        ]);
    }

    public function test_recruiter_can_view_candidate_resumes(): void
    {
        $recruiter = User::factory()->create(['role' => 'recruiter']);
        $candidate = User::factory()->create(['role' => 'candidate']);

        Resume::create([
            'user_id' => $candidate->id,
            'title' => 'CV principal',
            'skills' => ['Laravel'],
            'is_primary' => true,
        ]);

        $response = $this->actingAs($recruiter)->get(route('recruiter.resumes.show', $candidate));

        $response->assertOk();
    }
}
