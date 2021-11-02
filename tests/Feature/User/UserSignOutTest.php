<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserSignOutTest extends TestCase
{
    use RefreshDatabase;

    private const SIGN_OUT_ROUTE_NAME = 'sign-out';

    public function test_회원_로그아웃_204_성공(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->withSession(['test' => true])
            ->getJson(route(self::SIGN_OUT_ROUTE_NAME));

        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $response->assertSessionMissing('test');
    }
}
