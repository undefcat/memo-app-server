<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('sign-in', [\App\Http\Controllers\UserController::class, 'signIn'])
    ->name('sign-in');

Route::post('sign-up', [\App\Http\Controllers\UserController::class, 'signUp'])
    ->name('sign-up');
