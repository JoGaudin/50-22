<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn () => [
    'message' => 'ok',
    'app' => config('app.name'),
]);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
