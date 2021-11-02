<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemoRequest;
use App\Models\File;
use App\Models\Memo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MemoController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $count = 20;

        $user = Auth::user();
        $paginator = $user
            ->memos()
            ->paginate($count);

        return response()->json([
            'memos' => $paginator->items(),
            'page' => [
                'current' => $paginator->currentPage(),
                'has_next' => $paginator->hasMorePages(),
            ],
        ]);
    }

    public function store(MemoRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();

        $memo = new Memo();

        $memo->user_id = Auth::id();
        $memo->title = $data['title'];
        $memo->content = $data['content'];

        $memo->save();

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $attachment) {
                $path = Storage::disk('public')->put('memo', $attachment);

                [$type, $subType] = explode('/', $attachment->getMimeType());

                $file = new File();

                $file->size = $attachment->getSize();
                $file->tag = 'file';
                $file->mime_type = $type;
                $file->mime_subtype = $subType;
                $file->original_name = $attachment->getClientOriginalName();
                $file->path = $path;

                $memo->files()->save($file);
            }
        }

        return response()->json([
            'error' => false,
            'id' => (string)$memo->id,
        ], Response::HTTP_CREATED);
    }

    public function update(MemoRequest $request, string $mid)
    {
        $data = $request->validated();

        $memo = Memo::findOrFail($mid);

        $user = Auth::user();
        if ($user->cannot('update', $memo)) {
            return response()->json(null, Response::HTTP_FORBIDDEN);
        }

        $memo->title = $data['title'];
        $memo->content = $data['content'];

        $memo->save();

        return response()->json([
            'error' => false,
            'id' => $mid,
        ], Response::HTTP_OK);
    }

    public function destroy(Request $request, string $mid): \Illuminate\Http\JsonResponse
    {
        $memo = Memo::findOrFail($mid);

        $user = Auth::user();
        if ($user->cannot('destroy', $memo)) {
            return response()->json(null, Response::HTTP_FORBIDDEN);
        }

        $memo->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
