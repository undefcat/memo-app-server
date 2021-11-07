<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenController extends Controller
{
    public function check(): \Illuminate\Http\JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
