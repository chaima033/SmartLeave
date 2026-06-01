<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_can_update_profile_and_candidate_profile_created(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Candidate Updated',
            'phone' => '0123456789',
            'headline' => 'Developpeur',
            'location' => 'Paris',
            'bio' => 'Mon bio',
            'desired_role' => 'Full stack',
            'skills' => 'Laravel, Vue',
            'salary_expectation' => '50000',
            'portfolio_url' => 'https://exemple.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Candidate Updated',
        ]);
        $this->assertDatabaseHas('candidate_profiles', [
            'user_id' => $user->id,
            'headline' => 'Developpeur',
            'location' => 'Paris',
        ]);
    }

    public function test_recruiter_can_update_profile_and_company_profile_created(): void
    {
        $user = User::factory()->create(['role' => 'recruiter']);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Recruteur Updated',
            'phone' => '0987654321',
            'location' => 'Lyon',
            'company_name' => 'Smart Company',
            'company_industry' => 'Tech',
            'company_website' => 'https://smart.com',
            'company_size' => '50-100',
            'company_description' => 'Entreprise test',
            'bio' => 'Description',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Recruteur Updated',
            'company_name' => 'Smart Company',
        ]);
        $this->assertDatabaseHas('company_profiles', [
            'user_id' => $user->id,
            'company_name' => 'Smart Company',
            'industry' => 'Tech',
        ]);
    }
}
