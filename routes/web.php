<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/home', [App\Http\Controllers\HomeController::class, 'chatbot'])->name('chatbot');



// Admin Routes (protected by auth middleware)
// Route::middleware('auth')->group(function() {
//     Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    
    
//     // API Key Management
//     Route::get('/api-keys', [App\Http\Controllers\Admin\ApiKeyController::class, 'index'])->name('admin.api-keys');
//     Route::post('/api-keys', [App\Http\Controllers\Admin\ApiKeyController::class, 'store']);
//     Route::delete('/api-keys/{id}', [App\Http\Controllers\Admin\ApiKeyController::class, 'destroy']);
    
//     // Question Management
//     Route::get('/questions', [App\Http\Controllers\Admin\QuestionController::class, 'index'])->name('admin.questions.index');
//     Route::get('/questions/create', [App\Http\Controllers\Admin\QuestionController::class, 'create']);
//     Route::post('/questions', [App\Http\Controllers\Admin\QuestionController::class, 'store']);
//     Route::get('/questions/{id}/edit', [App\Http\Controllers\Admin\QuestionController::class, 'edit']);
//     Route::put('/questions/{id}', [App\Http\Controllers\Admin\QuestionController::class, 'update']);
//     Route::delete('/questions/{id}', [App\Http\Controllers\Admin\QuestionController::class, 'destroy']);
    
//     // CSV Import
//     Route::get('/questions/import', [App\Http\Controllers\Admin\QuestionController::class, 'importForm']);
//     Route::post('/questions/import', [App\Http\Controllers\Admin\QuestionController::class, 'import']);
    
//     // Statistics
//     Route::get('/statistics', [App\Http\Controllers\Admin\StatisticController::class, 'import']);
// });

Route::middleware('auth')->group(function() {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    // API Key Management
    Route::get('/api-keys', [App\Http\Controllers\Admin\ApiKeyController::class, 'index'])->name('admin.api-keys');
    Route::post('/api-keys', [App\Http\Controllers\Admin\ApiKeyController::class, 'store'])->name('admin.api-keys.store');
    Route::delete('/api-keys/{id}', [App\Http\Controllers\Admin\ApiKeyController::class, 'destroy'])->name('admin.api-keys.destroy');

    // Question Management
    Route::get('/questions', [App\Http\Controllers\Admin\QuestionController::class, 'index'])->name('admin.questions.index');
    Route::get('/questions/create', [App\Http\Controllers\Admin\QuestionController::class, 'create'])->name('admin.questions.create');
    Route::post('/questions', [App\Http\Controllers\Admin\QuestionController::class, 'store'])->name('admin.questions.store');
    Route::get('/questions/{id}/edit', [App\Http\Controllers\Admin\QuestionController::class, 'edit'])->name('admin.questions.edit');
    Route::put('/questions/{id}', [App\Http\Controllers\Admin\QuestionController::class, 'update'])->name('admin.questions.update');
    Route::delete('/questions/{id}', [App\Http\Controllers\Admin\QuestionController::class, 'destroy'])->name('admin.questions.destroy');

    // CSV Import
    Route::get('/questions/import', [App\Http\Controllers\Admin\QuestionController::class, 'importForm'])->name('admin.questions.import-form');
    Route::post('/questions/import', [App\Http\Controllers\Admin\QuestionController::class, 'import'])->name('admin.questions.import');

    // Statistics
    // Route::get('/statistics', [App\Http\Controllers\Admin\StatisticController::class, 'import'])->name('admin.statistics.import');
});


Route::prefix('admin')->middleware('auth')->group(function() {
    Route::get('questions/{question}/children', [App\Http\Controllers\Admin\QuestionController::class, 'children'])
        ->name('admin.questions.children');
    Route::resource('questions', App\Http\Controllers\Admin\QuestionController::class);
});
