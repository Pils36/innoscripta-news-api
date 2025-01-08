<?php

use App\Http\Middleware\JsonResponse;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;

Route::prefix('news')->middleware(JsonResponse::class)->group(function () {
    Route::get('/', [NewsController::class, 'index']);
    Route::get('/search', [NewsController::class, 'search']);
});


