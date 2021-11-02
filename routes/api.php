<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('sign-in', [\App\Http\Controllers\UserController::class, 'signIn'])
    ->name('sign-in');
