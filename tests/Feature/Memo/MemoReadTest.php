<?php

namespace Tests\Feature\Memo;

use App\Models\Memo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class MemoReadTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 컨트롤러에서 한 페이지당 가져오는 메모의 갯수 값을 설정해야한다.
     */
    private const PER_PAGE = 20;

    private const MEMO_INDEX_ROUTE_NAME = 'memo.index';

    public function test_메모_1페이지_가져오기_200_성공(): void
    {
        $this->getJsonPage(50, 1);
    }

    public function test_메모_목록_2페이지_가져오기_200_성공(): void
    {
        $this->getJsonPage(50, 2);
    }

    public function test_메모_목록_마지막_페이지_가져오기_200_성공(): void
    {
        $this->getJsonPage(50, 3);
    }

    public function test_메모_목록_비회원_401_실패(): void
    {
        $response = $this->getJson($this->memoUrl());

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    private function getJsonPage(int $count, int $page): \Illuminate\Testing\TestResponse
    {
        $user = User::factory()
            ->has(Memo::factory()->count($count))
            ->create();

        $response = $this
            ->actingAs($user)
            ->getJson($this->memoUrl($page));

        $hasNext = ceil($count / self::PER_PAGE) > $page;

        $currentPageSize = $hasNext
            ? self::PER_PAGE
            : ($count % (self::PER_PAGE));

        if ($currentPageSize === 0) {
            $currentPageSize = self::PER_PAGE;
        }

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(fn (AssertableJson $json) =>
        $json
            ->has('memos', $currentPageSize)
            ->where('page.current', $page)
            ->where('page.has_next', $hasNext)
        );

        return $response;
    }

    private function memoUrl(int $page = 1): string
    {
        return route(self::MEMO_INDEX_ROUTE_NAME, ['page' => $page]);
    }
}
