<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserSignInTest extends TestCase
{
    use RefreshDatabase;

    private const LOGIN_ROUTE_NAME = 'sign-in';

    public function test_회원_로그인_204_성공(): void
    {
        $user = User::factory()->create();

        $credential = [
            'account' => $user->account,
            'password' => 'password',
        ];

        $this->postJsonAssertStatus($credential, Response::HTTP_NO_CONTENT);
    }

    public function test_회원_로그인_존재하지_않는_계정_401_실패(): void
    {
        $credential = [
            'account' => 'nope',
            'password' => 'nope',
        ];

        $this->postJsonAssertStatus($credential, Response::HTTP_UNAUTHORIZED);
    }

    public function test_회원_로그인_아이디_필수값_422_실패(): void
    {
        $credential = [
            'account' => '',
            'password' => 'password',
        ];

        $this->postJsonAssertStatus($credential, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_회원_로그인_비밀번호_필수값_422_실패(): void
    {
        $credential = [
            'account' => 'account',
            'password' => '',
        ];

        $this->postJsonAssertStatus($credential, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function postJsonAssertStatus(array $credential, int $status): \Illuminate\Testing\TestResponse
    {
        $response = $this->postJson($this->signInUrl(), $credential);

        $response->assertStatus($status);

        return $response;
    }

    private function signInUrl(): string
    {
        return route(self::LOGIN_ROUTE_NAME);
    }
}
