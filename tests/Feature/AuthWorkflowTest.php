<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_login_and_register_pages(): void
    {
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();
    }

    public function test_user_can_register_as_candidate_and_profile_is_created(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test Candidat',
            'email' => 'candidate@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => 'candidate',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'candidate@example.com', 'role' => 'candidate']);
        $this->assertDatabaseHas('candidate_profiles', ['headline' => null, 'phone' => null]);
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $login = $this->post(route('login.store'), [
            'email' => 'login@example.com',
            'password' => 'secret123',
        ]);

        $login->assertRedirect(route('dashboard'));

        $logout = $this->actingAs($user)->post(route('logout'));
        $logout->assertRedirect(route('login'));
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'login@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'login@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_user_can_register_as_recruiter_and_company_profile_is_created(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test Recruteur',
            'email' => 'recruiter@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'role' => 'recruiter',
            'company_name' => 'Test Company',
            'company_industry' => 'Tech',
            'company_website' => 'https://example.com',
            'location' => 'Paris',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'recruiter@example.com', 'role' => 'recruiter']);
        $this->assertDatabaseHas('company_profiles', ['user_id' => 1, 'company_name' => 'Test Company']);
    }
}
