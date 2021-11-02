<?php

use Illuminate\Support\Facades\Route;

Route::post('sign-in', [\App\Http\Controllers\UserController::class, 'signIn'])
    ->name('sign-in');

Route::post('sign-up', [\App\Http\Controllers\UserController::class, 'signUp'])
    ->name('sign-up');

Route::middleware('auth')->prefix('memos')->group(function () {
    Route::get('/', [\App\Http\Controllers\MemoController::class, 'index'])
        ->name('memo.index');

    Route::get('/{mid}', [\App\Http\Controllers\MemoController::class, 'show'])
        ->where('mid', '[0-9]+')
        ->name('memo.show');

    Route::post('/', [\App\Http\Controllers\MemoController::class, 'store'])
        ->name('memo.store');

    Route::delete('/{mid}', [\App\Http\Controllers\MemoController::class, 'destroy'])
        ->where('mid', '[0-9]+')
        ->name('memo.destroy');

    Route::put('/{mid}', [\App\Http\Controllers\MemoController::class, 'update'])
        ->whereNumber('mid')
        ->name('memo.update');
});
