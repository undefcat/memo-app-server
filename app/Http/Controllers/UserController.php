<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function signIn(Request $request): \Illuminate\Http\JsonResponse
    {
        $rules = [
            'account' => ['required'],
            'password' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'messages' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $credential = $validator->validated();

        if (!Auth::attempt($credential)) {
            return response()->json([
                'error' => true,
                'messages' => [
                    'login' => ['아이디 혹은 비밀번호가 올바르지 않아요.'],
                ],
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
