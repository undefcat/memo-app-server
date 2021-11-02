<?php

namespace App\Http\Controllers;

use App\Models\User;
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

    public function signUp(Request $request): \Illuminate\Http\JsonResponse
    {
        $rules = [
            'account' => ['required', 'between:2,60'],
            'password' => ['required', 'confirmed', 'between:4,20'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'messages' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();

        $isExist = User::where('account', '=', $data['account'])
            ->count() > 0;

        if ($isExist) {
            return response()->json([
                'error' => true,
                'messages' => [
                    'account' => ['이미 존재하는 계정입니다.'],
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = new User();

        $user->account = $data['account'];
        $user->password = bcrypt($data['password']);

        $user->save();

        return response()->json(null, Response::HTTP_CREATED);
    }

    public function signOut(): \Illuminate\Http\JsonResponse
    {
        Auth::logout();

        session()->flush();
        session()->regenerate(true);

        return response()
            ->json(null, Response::HTTP_NO_CONTENT);
    }
}
