<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemoRequest;
use App\Models\Memo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MemoController extends Controller
{
    public function store(MemoRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validated();

        $memo = new Memo();

        $memo->user_id = Auth::id();
        $memo->title = $data['title'];
        $memo->content = $data['content'];

        $memo->save();

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
