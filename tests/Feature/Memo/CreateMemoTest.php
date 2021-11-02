<?php

namespace Tests\Feature\Memo;

use App\Models\Memo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CreateMemoTest extends TestCase
{
    use RefreshDatabase;

    private const MEMO_STORE_ROUTE_NAME = 'memo.store';

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_메모_생성_및_id값_리턴_201_성공(): void
    {
        $formData = [
            'title' => 'title',
            'content' => 'content',
        ];

        $response = $this
            ->actingAs($this->user)
            ->postJson($this->memoUrl(), $formData);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson(fn (AssertableJson $json) =>
            $json
                ->whereType('id', 'string')
                ->etc()
        );

        $isExist = Memo::count() > 0;

        $this->assertTrue($isExist);
    }

    public function test_메모_생성_비회원_401_실패(): void
    {
        $formData = [
            'title' => 'title',
            'content' => 'content',
        ];

        $response = $this
            ->postJson($this->memoUrl(), $formData);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    private function memoUrl(?Memo $memo = null): string
    {
        if ($memo === null) {
            return route(self::MEMO_STORE_ROUTE_NAME);
        }

        return route(self::MEMO_STORE_ROUTE_NAME, ['mid' => $memo->id]);
    }
}
