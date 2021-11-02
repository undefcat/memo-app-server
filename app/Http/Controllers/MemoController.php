<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class MemoController extends Controller
{
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $rules = [
            'title' => ['required', 'between:1,250'],
            'content' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'messages' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();

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
