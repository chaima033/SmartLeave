<?php

namespace Tests\Feature;

use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResumeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_can_update_own_resume(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);

        $resume = Resume::create([
            'user_id' => $user->id,
            'title' => 'CV initial',
            'skills' => ['PHP'],
            'is_primary' => false,
        ]);

        $response = $this->actingAs($user)->put(route('resumes.update', $resume), [
            'title' => 'CV mis a jour',
            'summary' => 'Mise a jour',
            'skills' => 'Laravel, Vue, PHP',
            'is_primary' => 1,
        ]);

        $response->assertRedirect(route('resumes.index'));

        $this->assertDatabaseHas('resumes', [
            'id' => $resume->id,
            'title' => 'CV mis a jour',
            'is_primary' => 1,
        ]);
    }

    public function test_candidate_cannot_edit_another_users_resume(): void
    {
        $owner = User::factory()->create(['role' => 'candidate']);
        $other = User::factory()->create(['role' => 'candidate']);

        $resume = Resume::create([
            'user_id' => $owner->id,
            'title' => 'CV secret',
            'skills' => ['PHP'],
            'is_primary' => true,
        ]);

        $response = $this->actingAs($other)->get(route('resumes.edit', $resume));

        $response->assertStatus(403);
    }

    public function test_candidate_can_edit_own_resume(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);

        $resume = Resume::create([
            'user_id' => $user->id,
            'title' => 'CV à éditer',
            'skills' => ['PHP'],
            'is_primary' => false,
        ]);

        $response = $this->actingAs($user)->get(route('resumes.edit', $resume));

        $response->assertOk();
        $response->assertViewHas('resume', $resume);
    }

    public function test_candidate_can_delete_resume(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);

        $resume = Resume::create([
            'user_id' => $user->id,
            'title' => 'CV a supprimer',
            'skills' => ['PHP'],
            'is_primary' => false,
        ]);

        $response = $this->actingAs($user)->delete(route('resumes.destroy', $resume));

        $response->assertRedirect();
        $this->assertDatabaseMissing('resumes', ['id' => $resume->id]);
    }

    public function test_recruiter_can_access_recruiter_resume_index_and_show(): void
    {
        $recruiter = User::factory()->create(['role' => 'recruiter']);
        $candidate = User::factory()->create(['role' => 'candidate']);

        $resume = Resume::create([
            'user_id' => $candidate->id,
            'title' => 'CV public',
            'skills' => ['PHP'],
            'is_primary' => true,
        ]);

        $index = $this->actingAs($recruiter)->get(route('recruiter.resumes.index'));
        $show = $this->actingAs($recruiter)->get(route('recruiter.resumes.show', $candidate));

        $index->assertOk();
        $show->assertOk();
    }

    public function test_candidate_can_view_resume_index(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);

        Resume::create([
            'user_id' => $user->id,
            'title' => 'CV pour index',
            'skills' => ['PHP'],
            'is_primary' => true,
        ]);

        $response = $this->actingAs($user)->get(route('resumes.index'));

        $response->assertOk();
        $response->assertViewHas('resumes');
    }

    public function test_candidate_can_view_resume_create_form(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);

        $response = $this->actingAs($user)->get(route('resumes.create'));

        $response->assertOk();
    }

    public function test_candidate_can_store_resume(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);

        $response = $this->actingAs($user)->post(route('resumes.store'), [
            'title' => 'Nouveau CV',
            'summary' => 'Resume creation',
            'skills' => 'PHP, Laravel',
            'is_primary' => 1,
        ]);

        $response->assertRedirect(route('resumes.index'));
        $this->assertDatabaseHas('resumes', [
            'user_id' => $user->id,
            'title' => 'Nouveau CV',
            'is_primary' => 1,
        ]);
    }

    public function test_candidate_can_store_resume_without_skills(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);

        $response = $this->actingAs($user)->post(route('resumes.store'), [
            'title' => 'CV sans skills',
            'summary' => 'Sans competences listées',
            'is_primary' => 0,
        ]);

        $response->assertRedirect(route('resumes.index'));
        $this->assertDatabaseHas('resumes', [
            'user_id' => $user->id,
            'title' => 'CV sans skills',
            'is_primary' => 0,
        ]);
    }

    public function test_parse_skills_private_method_splits_csv_string(): void
    {
        $controller = app(\App\Http\Controllers\ResumeController::class);
        $method = new \ReflectionMethod($controller, 'parseSkills');
        $method->setAccessible(true);

        $result = $method->invoke($controller, 'PHP, Laravel,  Vue  ,');

        $this->assertSame(['PHP', 'Laravel', 'Vue'], $result);
    }
}
