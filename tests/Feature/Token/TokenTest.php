<?php

namespace Tests\Feature\Token;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TokenTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN_CHECK_ROUTE_NAME = 'token.check';

    public function test_토큰_유효함_204_성공(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this
            ->getJson($this->url());

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_토큰_유효하지_않음_401_실패(): void
    {
        $response = $this->getJson($this->url());

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    private function url(): string
    {
        return route(self::TOKEN_CHECK_ROUTE_NAME);
    }
}
