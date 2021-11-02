<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserSignUpTest extends TestCase
{
    use RefreshDatabase;

    private const SIGN_UP_ROUTE_NAME = 'sign-up';

    public function test_회원가입_201_성공(): void
    {
        $formData = [
            'account' => 'account',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $this->postJsonAssertStatus($formData, Response::HTTP_CREATED);
    }

    public function test_회원가입_중복_아이디_422_실패(): void
    {
        $user = User::factory()->create();

        $formData = [
            'account' => $user->account,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $this->postJsonAssertStatus($formData, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_회원가입_비밀번호_확인_다름_422_실패(): void
    {
        $formData = [
            'account' => 'account',
            'password' => 'password',
            'password_confirmation' => 'password2',
        ];

        $this->postJsonAssertStatus($formData, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function postJsonAssertStatus(array $data, int $status): \Illuminate\Testing\TestResponse
    {
        $response = $this->postJson($this->signUpUrl(), $data);

        $response->assertStatus($status);

        return $response;
    }

    private function signUpUrl(): string
    {
        return route(self::SIGN_UP_ROUTE_NAME);
    }
}
