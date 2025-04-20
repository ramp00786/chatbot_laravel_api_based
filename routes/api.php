<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Api\ChatbotApiController;


Route::middleware(['api.key'])->group(function () {
    Route::prefix('chat')->group(function() {
        // Session Management
        Route::post('/sessions', [ChatbotApiController::class, 'startSession']);
        Route::post('/sessions/end', [ChatbotApiController::class, 'endSession']);
        
        // Questions
        Route::get('/questions', [ChatbotApiController::class, 'getQuestions']);
        
        // Messages
        Route::post('/messages', [ChatbotApiController::class, 'saveMessage']);
    });
});



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::get('/test', function(){
    return ['name'=>'asdf'];
});