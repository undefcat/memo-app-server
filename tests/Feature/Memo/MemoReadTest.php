<?php

namespace Tests\Feature\Memo;

use App\Models\File;
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

    private const MEMO_SHOW_ROUTE_NAME = 'memo.show';

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

    public function test_메모_가져오기_200_성공(): void
    {
        $user = User::factory()->create();
        $memo = Memo::factory()
            ->for($user)
            ->has(File::factory()->count(3), 'files')
            ->create();

        $response = $this
            ->actingAs($user)
            ->getJson($this->showUrl($memo));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(fn (AssertableJson $json) =>
            $json
                ->has('memo', fn (AssertableJson $memoJson) =>
                    $memoJson
                        ->has('id')
                        ->has('title')
                        ->has('content')
                        ->has('date')
                )
                ->has('files', 3)
                ->has('files.0', fn (AssertableJson $filesJson) =>
                    $filesJson
                        ->whereType('name', ['string'])
                        ->whereType('size', ['integer'])
                        ->whereType('mime_type', ['string'])
                        ->whereType('mime_subtype', ['string'])
                        ->whereType('url', ['string'])
                )
        );
    }

    public function test_메모_목록_비회원_401_실패(): void
    {
        $response = $this->getJson($this->indexUrl());

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    private function getJsonPage(int $count, int $page): \Illuminate\Testing\TestResponse
    {
        $fileCount = 3;

        $memos = Memo::factory()
            ->has(File::factory()->count($fileCount), 'files')
            ->count($count);

        $user = User::factory()
            ->has($memos)
            ->create();

        $response = $this
            ->actingAs($user)
            ->getJson($this->indexUrl($page));

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

    private function indexUrl(int $page = 1): string
    {
        return route(self::MEMO_INDEX_ROUTE_NAME, ['page' => $page]);
    }

    private function showUrl(Memo $memo): string
    {
        return route(self::MEMO_SHOW_ROUTE_NAME, ['mid' => $memo->id]);
    }
}
