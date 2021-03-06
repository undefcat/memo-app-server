<?php

namespace Tests\Feature\Memo;

use App\Models\Memo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class MemoCreateTest extends TestCase
{
    use RefreshDatabase;

    private const MEMO_STORE_ROUTE_NAME = 'memo.store';

    public function test_메모_생성_및_id값_리턴_첨부파일_없음_201_성공(): void
    {
        $formData = [
            'title' => 'title',
            'content' => 'content',
        ];

        $response = $this
            ->actingAs(User::factory()->create())
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

    public function test_메모_생성_및_id값_리턴_첨부파일_있음_201_성공(): void
    {
        Storage::fake('public');

        $files = collect([null, null])
            ->map(fn () => UploadedFile::fake()->image('image.png'));

        $formData = [
            'title' => 'title',
            'content' => 'content',
            'files' => $files->toArray(),
        ];

        $response = $this
            ->actingAs(User::factory()->create())
            ->postJson($this->memoUrl(), $formData);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson(fn (AssertableJson $json) =>
            $json
                ->whereType('id', 'string')
                ->etc()
        );

        $memo = Memo::where('title', '=', $formData['title'])
            ->first();

        $this->assertNotEmpty($memo);
        $this->assertEquals(count($files), $memo->files()->count());

        $files->each(fn ($file) =>
            Storage::disk('public')->assertExists($file->hashName('memo'))
        );
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

    private function memoUrl(): string
    {
        return route(self::MEMO_STORE_ROUTE_NAME);
    }
}
