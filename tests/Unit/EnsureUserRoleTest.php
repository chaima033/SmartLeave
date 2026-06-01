<?php

namespace Tests\Unit;

use App\Http\Middleware\EnsureUserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class EnsureUserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_denied_access(): void
    {
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => null);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        (new EnsureUserRole())->handle($request, fn() => response('ok'), 'candidate');
    }

    public function test_users_with_matching_role_are_allowed(): void
    {
        $user = User::factory()->create(['role' => 'candidate']);
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $user);

        $response = (new EnsureUserRole())->handle($request, fn() => response('ok'), 'candidate');

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_users_with_wrong_role_are_denied(): void
    {
        $user = User::factory()->create(['role' => 'recruiter']);
        $request = Request::create('/test', 'GET');
        $request->setUserResolver(fn() => $user);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        (new EnsureUserRole())->handle($request, fn() => response('ok'), 'candidate');
    }
}
