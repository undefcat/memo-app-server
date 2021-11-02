<?php

namespace Tests\Feature\Memo;

use App\Models\Memo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class MemoUpdateTest extends TestCase
{
    use RefreshDatabase;

    private const MEMO_UPDATE_ROUTE_NAME = 'memo.update';

    public function test_메모_수정_204_성공(): void
    {
        $user = User::factory()->create();
        $memo = Memo::factory()
            ->for($user)
            ->create();

        $formData = [
            'title' => 'other title',
            'content' => 'other content',
        ];

        $response = $this
            ->actingAs($user)
            ->putJson($this->memoUrl($memo), $formData);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(fn (AssertableJson $json) =>
            $json
                ->whereType('id', 'string')
                ->etc()
        );

        $updatedMemo = Memo::find($memo->id);

        $this->assertEquals($formData['title'], $updatedMemo->title);
        $this->assertEquals($formData['content'], $updatedMemo->content);
    }

    public function test_메모_수정_작성자_아니면_403_실패(): void
    {
        $user = User::factory()->create();
        $memo = Memo::factory()
            ->create();

        $formData = [
            'title' => 'other title',
            'content' => 'other content',
        ];

        $response = $this
            ->actingAs($user)
            ->putJson($this->memoUrl($memo), $formData);

        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $updatedMemo = Memo::find($memo->id);

        $this->assertNotEquals($formData['title'], $updatedMemo->title);
        $this->assertNotEquals($formData['content'], $updatedMemo->content);
    }

    private function memoUrl(?Memo $memo = null): string
    {
        if ($memo === null) {
            return route(self::MEMO_UPDATE_ROUTE_NAME);
        }

        return route(self::MEMO_UPDATE_ROUTE_NAME, ['mid' => $memo->id]);
    }
}
