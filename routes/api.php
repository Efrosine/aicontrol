<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/upload', function (Request $request) {
    \Log::info('Upload endpoint called', ['request' => $request->all()]);
    return response()->json(['message' => 'Upload logged']);
});