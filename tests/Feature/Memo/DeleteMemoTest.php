<?php

namespace Tests\Feature\Memo;

use App\Models\Memo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DeleteMemoTest extends TestCase
{
    use RefreshDatabase;

    private const MEMO_DESTROY_ROUTE_NAME = 'memo.destroy';

    public function test_메모_삭제_204_성공(): void
    {
        $user = User::factory()->create();

        $memo = Memo::factory()
            ->for($user)
            ->create();

        $response = $this
            ->actingAs($user)
            ->deleteJson($this->memoUrl($memo));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $isDeleted = Memo::count() === 0;

        $this->assertTrue($isDeleted);
    }

    public function test_메모_삭제_작성자_아닌_경우_403_실패(): void
    {
        $memo = Memo::factory()->create();

        $response = $this
            ->actingAs(User::factory()->create())
            ->deleteJson($this->memoUrl($memo));

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $isExist = Memo::count() > 0;

        $this->assertTrue($isExist);
    }

    private function memoUrl(Memo $memo): string
    {
        return route(self::MEMO_DESTROY_ROUTE_NAME, ['mid' => $memo->id]);
    }
}
